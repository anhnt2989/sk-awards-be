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
            'categories'                                          => 'required|array|min:1',
            'categories.*.name'                                   => 'required|string',
            'categories.*.color'                                  => 'nullable|string|max:20',
            'categories.*.sub_categories'                         => 'required|array|min:1',
            'categories.*.sub_categories.*.name'                  => 'required|string',
            'categories.*.sub_categories.*.criteria'              => 'required|array|min:1',
            'categories.*.sub_categories.*.criteria.*.name'       => 'required|string',
            'categories.*.sub_categories.*.criteria.*.description' => 'nullable|string',
            'categories.*.sub_categories.*.criteria.*.max_score'  => 'required|integer|min:1|max:100',
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

            $crIdx = 1;
            foreach ($data['categories'] as $ci => $cat) {
                $catId = $program->id . '-C' . ($ci + 1);
                ProgramCategory::create([
                    'id'         => $catId,
                    'program_id' => $program->id,
                    'parent_id'  => null,
                    'name'       => $cat['name'],
                    'color'      => $cat['color'] ?? '#3b82f6',
                    'sort_order' => $ci,
                ]);

                foreach ($cat['sub_categories'] as $si => $sc) {
                    $scId = $catId . '-SC' . ($si + 1);
                    ProgramCategory::create([
                        'id'         => $scId,
                        'program_id' => $program->id,
                        'parent_id'  => $catId,
                        'name'       => $sc['name'],
                        'color'      => $cat['color'] ?? '#3b82f6',
                        'sort_order' => $si,
                    ]);

                    foreach ($sc['criteria'] as $ki => $crit) {
                        ProgramCriterion::create([
                            'id'          => $program->id . '-cr' . $crIdx++,
                            'program_id'  => $program->id,
                            'category_id' => $scId,
                            'name'        => $crit['name'],
                            'description' => $crit['description'] ?? null,
                            'max_score'   => $crit['max_score'],
                            'sort_order'  => $ki,
                        ]);
                    }
                }
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
        $allCats    = $p->categories;
        $topCats    = $allCats->whereNull('parent_id');
        $subCatMap  = $allCats->whereNotNull('parent_id')->groupBy('parent_id');
        $critMap    = $p->criteria->groupBy('category_id');

        $categories = $topCats->map(function ($cat) use ($subCatMap, $critMap) {
            $subCats = ($subCatMap[$cat->id] ?? collect())->map(function ($sc) use ($critMap) {
                return [
                    'id'       => $sc->id,
                    'name'     => $sc->name,
                    'criteria' => ($critMap[$sc->id] ?? collect())->map(fn ($c) => [
                        'id'   => $c->id,
                        'name' => $c->name,
                        'desc' => $c->description,
                        'max'  => $c->max_score,
                    ])->values(),
                ];
            })->values();

            return [
                'id'             => $cat->id,
                'name'           => $cat->name,
                'color'          => $cat->color,
                'sub_categories' => $subCats,
            ];
        })->values();

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
            'categories'  => $categories,
            'criteria'    => $p->criteria->map(fn ($c) => [
                'id'   => $c->id,
                'name' => $c->name,
                'desc' => $c->description,
                'max'  => $c->max_score,
            ])->values(),
            'judge_ids'   => $p->judges->pluck('id')->values(),
        ];
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()->isAdmin(), 403, 'Chỉ quản trị viên mới có quyền này.');
    }
}
