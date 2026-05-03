<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Submission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class SubmissionController extends Controller
{
    public function index(Request $request, Program $program): JsonResponse
    {
        $user = $request->user();

        $query = $program->submissions()->with(['assignedJudges', 'scores.details']);

        if ($user->isSubmitter()) {
            $query->where('submitter_id', $user->id);
        } elseif ($user->isJudge()) {
            $judgeId = $user->judge?->id;
            // Judges see only submissions assigned to them
            $query->whereHas('assignedJudges', fn ($q) => $q->where('judges.id', $judgeId))
                ->where('status', 'approved');
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('search')) {
            $q = $request->input('search');
            $query->where(fn ($sq) => $sq
                ->where('name', 'like', "%{$q}%")
                ->orWhere('company', 'like', "%{$q}%")
                ->orWhere('id', 'like', "%{$q}%")
            );
        }

        $submissions = $query->orderByDesc('submitted_date')->get()
            ->map(fn ($s) => $this->format($s, $program));

        return response()->json($submissions);
    }

    /**
     * @throws Throwable
     */
    public function store(Request $request, Program $program): JsonResponse
    {
        $user = $request->user();
        abort_unless($user->isSubmitter() || $user->isAdmin(), 403);
        abort_unless($program->status === 'active', 422, 'Chương trình đã kết thúc.');

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'company'     => 'required|string|max:255',
            'category_id' => 'required|string',
            'description' => 'nullable|string',
            'docs'        => 'nullable|string',
        ]);

        abort_unless(
            $program->categories()->where('id', $data['category_id'])->exists(),
            422, 'Lĩnh vực không hợp lệ.'
        );

        try {
            $submission = DB::transaction(function () use ($data, $program, $user) {
                return Submission::create([
                    'id'             => $program->generateSubmissionId(),
                    'program_id'     => $program->id,
                    'name'           => $data['name'],
                    'company'        => $data['company'],
                    'submitter_id'   => $user->id,
                    'category_id'    => $data['category_id'],
                    'description'    => $data['description'] ?? null,
                    'docs'           => $data['docs'] ?? null,
                    'submitted_date' => now()->toDateString(),
                    'status'         => 'pending',
                ]);
            });

            $submission->load(['assignedJudges', 'scores.details']);

            return response()->json(
                $this->format($submission, $program),
                201
            );
        } catch (Throwable $e) {
            Log::error($e);
            throw $e;
        }
    }

    public function update(Request $request, Program $program, Submission $submission): JsonResponse
    {
        $user = $request->user();
        abort_unless($submission->program_id === $program->id, 404);

        if ($user->isSubmitter()) {
            abort_unless((string) $submission->submitter_id === (string) $user->id, 403, 'Bạn không có quyền chỉnh sửa hồ sơ này.');
        } else {
            abort_unless($user->isAdmin(), 403);
        }

        $data = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'company'     => 'sometimes|string|max:255',
            'category_id' => 'sometimes|string',
            'description' => 'sometimes|nullable|string',
            'docs'        => 'sometimes|nullable|string',
        ]);

        if (isset($data['category_id'])) {
            abort_unless(
                $program->categories()->where('id', $data['category_id'])->exists(),
                422, 'Lĩnh vực không hợp lệ.'
            );
        }

        $submission->update($data);
        $submission->load(['assignedJudges', 'scores.details']);

        return response()->json($this->format($submission, $program));
    }

    /** Admin: approve / reject a submission */
    public function review(Request $request, Program $program, Submission $submission): JsonResponse
    {
        abort_unless($request->user()->isAdmin(), 403);
        abort_unless($submission->program_id === $program->id, 404);

        $data = $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $submission->update(['status' => $data['status']]);

        return response()->json(['status' => $submission->status]);
    }

    private function format(Submission $s, Program $program): array
    {
        $cat = $program->categories->firstWhere('id', $s->category_id);

        return [
            'id'             => $s->id,
            'program_id'     => $s->program_id,
            'name'           => $s->name,
            'company'        => $s->company,
            'submitter_id'   => $s->submitter_id,
            'category_id'    => $s->category_id,
            'category'       => $cat ? ['id' => $cat->id, 'name' => $cat->name, 'color' => $cat->color] : null,
            'description'    => $s->description,
            'submitted_date' => $s->submitted_date?->toDateString(),
            'status'         => $s->status,
            'docs'           => $s->docs ?? [],
            'assigned_judges' => $s->assignedJudges->pluck('id')->values(),
        ];
    }
}
