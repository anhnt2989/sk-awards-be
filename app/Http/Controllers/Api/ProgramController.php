<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\ProgramCategory;
use App\Models\ProgramCriterion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProgramController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Program::with(['categories', 'criteria', 'judges']);

        if ($user->isJudge()) {
            $judgeId = $user->judge?->id;
            $query->whereHas('judges', fn ($q) => $q->where('judges.id', $judgeId));
        } elseif ($user->isSubmitter()) {
            $query->where('status', 'active');
        }

        $programs = $query->orderByDesc('year')->get()->map(fn ($p) => $this->formatProgram($p));

        return response()->json($programs);
    }

    public function show(Program $program): JsonResponse
    {
        $program->load(['categories', 'criteria', 'judges']);

        return response()->json($this->formatProgram($program));
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'year'        => 'required|integer|min:2000|max:2100',
            'abbr'        => 'required|string|max:10',
            'color'       => 'nullable|string|max:20',
            'deadline'    => 'nullable|date',
            'description' => 'nullable|string',
            'categories'  => 'required|array|min:1',
            'categories.*.name'  => 'required|string',
            'categories.*.color' => 'nullable|string|max:20',
            'criteria'    => 'required|array|min:1',
            'criteria.*.name'      => 'required|string',
            'criteria.*.description' => 'nullable|string',
            'criteria.*.max_score'   => 'required|integer|min:1|max:100',
        ]);

        return DB::transaction(function () use ($data) {
            $abbr = strtoupper($data['abbr']);
            $next = Program::where('id', 'like', $abbr . '-%')
                ->pluck('id')
                ->map(fn ($id) => (int) substr(strrchr($id, '-'), 1))
                ->max() + 1;

            $program = Program::create([
                'id'          => $abbr . '-' . $next,
                'name'        => $data['name'],
                'year'        => $data['year'],
                'abbr'        => $abbr,
                'color'       => $data['color'] ?? '#1e3a5f',
                'status'      => 'active',
                'deadline'    => $data['deadline'] ?? null,
                'description' => $data['description'] ?? null,
                'next_sub_id' => 1,
            ]);

            foreach ($data['categories'] as $i => $cat) {
                ProgramCategory::create([
                    'id'         => $program->id . '-C' . ($i + 1),
                    'program_id' => $program->id,
                    'name'       => $cat['name'],
                    'color'      => $cat['color'] ?? '#3b82f6',
                    'sort_order' => $i,
                ]);
            }

            foreach ($data['criteria'] as $i => $crit) {
                ProgramCriterion::create([
                    'id'          => $program->id . '-cr' . ($i + 1),
                    'program_id'  => $program->id,
                    'name'        => $crit['name'],
                    'description' => $crit['description'] ?? null,
                    'max_score'   => $crit['max_score'],
                    'sort_order'  => $i,
                ]);
            }

            $program->load(['categories', 'criteria', 'judges']);

            return response()->json($this->formatProgram($program), 201);
        });
    }

    public function update(Request $request, Program $program): JsonResponse
    {
        $this->authorizeAdmin($request);

        $data = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'year'        => 'sometimes|integer',
            'abbr'        => 'sometimes|string|max:10',
            'color'       => 'sometimes|string|max:20',
            'status'      => 'sometimes|in:active,completed',
            'deadline'    => 'sometimes|nullable|date',
            'description' => 'sometimes|nullable|string',
        ]);

        $program->update($data);

        $program->load(['categories', 'criteria', 'judges']);

        return response()->json($this->formatProgram($program));
    }

    private function formatProgram(Program $p): array
    {
        return [
            'id'          => $p->id,
            'name'        => $p->name,
            'year'        => $p->year,
            'abbr'        => $p->abbr,
            'color'       => $p->color,
            'status'      => $p->status,
            'deadline'    => $p->deadline?->toDateString(),
            'description' => $p->description,
            'next_sub_id' => $p->next_sub_id,
            'categories'  => $p->categories->map(fn ($c) => [
                'id' => $c->id, 'name' => $c->name, 'color' => $c->color,
            ])->values(),
            'criteria' => $p->criteria->map(fn ($c) => [
                'id' => $c->id, 'name' => $c->name,
                'desc' => $c->description, 'max' => $c->max_score,
            ])->values(),
            'judge_ids' => $p->judges->pluck('id')->values(),
        ];
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()->isAdmin(), 403, 'Chỉ quản trị viên mới có quyền này.');
    }
}
