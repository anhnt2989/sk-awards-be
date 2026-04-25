<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Submission;
use App\Models\UploadedFile;
use App\Services\FileUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class SubmissionController extends Controller
{
    protected FileUploadService $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    public function index(Request $request, Program $program): JsonResponse
    {
        $user = $request->user();

        $query = $program->submissions()->with(['files', 'assignedJudges', 'scores.details']);

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
            'file_ids'    => 'nullable|array|max:5',
            'file_ids.*'  => 'nullable|integer',
        ]);

        abort_unless(
            $program->categories()->where('id', $data['category_id'])->exists(),
            422, 'Lĩnh vực không hợp lệ.'
        );

        try {
            $submission = DB::transaction(function () use ($data, $program, $user, &$uploadedFiles) {
                $id = $program->generateSubmissionId();

                $submission = Submission::create([
                    'id'             => $id,
                    'program_id'     => $program->id,
                    'name'           => $data['name'],
                    'company'        => $data['company'],
                    'submitter_id'   => $user->id,
                    'category_id'    => $data['category_id'],
                    'description'    => $data['description'] ?? null,
                    'submitted_date' => now()->toDateString(),
                    'status'         => 'pending',
                ]);

                if (!empty($data['file_ids'])) {
                    $updated = UploadedFile::whereIn('id', $data['file_ids'])
                                            ->where('user_id', $user->id)
                                            ->whereNull('fileable_id')
                                            ->update([
                                                'fileable_type' => Submission::class,
                                                'fileable_id'   => $submission->id,
                                            ]);

                    if ($updated != count($data['file_ids'])) {
                        throw ValidationException::withMessages([
                            'file_ids' => 'Có file không hợp lệ trong quá trình upload, xin kiểm tra lại'
                        ]);
                    }
                }

                return $submission;
            });

            $submission->load(['files', 'assignedJudges', 'scores.details']);

            return response()->json(
                $this->format($submission, $program),
                201
            );
        } catch (Throwable $e) {
            Log::error($e);
            throw $e;
        }
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
            'docs'           => $s->files->pluck('original_name')->values(),
            'assigned_judges' => $s->assignedJudges->pluck('id')->values(),
        ];
    }
}
