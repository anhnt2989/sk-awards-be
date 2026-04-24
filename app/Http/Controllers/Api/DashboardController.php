<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Score;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function show(Request $request, Program $program): JsonResponse
    {
        $user    = $request->user();
        $program->load(['categories', 'criteria', 'judges', 'submissions.scores.details', 'submissions.assignedJudges']);

        if ($user->isAdmin()) {
            return response()->json($this->adminStats($program));
        }

        if ($user->isJudge()) {
            $judgeId = $user->judge?->id;

            return response()->json($this->judgeStats($program, $judgeId));
        }

        return response()->json($this->submitterStats($program, $user->id));
    }

    private function adminStats(Program $program): array
    {
        $submissions = $program->submissions;
        $approved    = $submissions->where('status', 'approved');
        $maxTotal    = $program->criteria->sum('max_score');

        $ranked = $approved->map(function ($sub) use ($maxTotal) {
            $submittedScores = $sub->scores->where('is_submitted', true);
            if ($submittedScores->isEmpty()) {
                return ['id' => $sub->id, 'name' => $sub->name, 'company' => $sub->company, 'avg' => null];
            }
            $avg = round($submittedScores->avg(fn ($s) => $s->details->sum('value')));

            return ['id' => $sub->id, 'name' => $sub->name, 'company' => $sub->company, 'avg' => $avg];
        })->filter(fn ($s) => $s['avg'] !== null)->sortByDesc('avg')->values();

        $judgeProgress = $program->judges->map(function ($judge) use ($program) {
            $assigned = $program->submissions->where('status', 'approved')
                ->filter(fn ($s) => $s->assignedJudges->contains('id', $judge->id));
            $scored   = $assigned->filter(fn ($s) => $s->scores->where('judge_id', $judge->id)->where('is_submitted', true)->isNotEmpty());

            return [
                'judge_id'      => $judge->id,
                'judge_name'    => $judge->name,
                'assigned'      => $assigned->count(),
                'scored'        => $scored->count(),
                'pct'           => $assigned->count() ? round($scored->count() / $assigned->count() * 100) : 0,
            ];
        })->values();

        return [
            'total_submissions'  => $submissions->count(),
            'approved'           => $approved->count(),
            'pending'            => $submissions->where('status', 'pending')->count(),
            'rejected'           => $submissions->where('status', 'rejected')->count(),
            'scored_submissions' => $ranked->count(),
            'total_score_entries' => Score::whereIn('submission_id', $submissions->pluck('id'))->where('is_submitted', true)->count(),
            'judge_count'        => $program->judges->count(),
            'max_total'          => $maxTotal,
            'ranked'             => $ranked->take(10),
            'judge_progress'     => $judgeProgress,
        ];
    }

    private function judgeStats(Program $program, string $judgeId): array
    {
        $assigned  = $program->submissions
            ->where('status', 'approved')
            ->filter(fn ($s) => $s->assignedJudges->contains('id', $judgeId));

        $submitted = $assigned->filter(fn ($s) => $s->scores->where('judge_id', $judgeId)->where('is_submitted', true)->isNotEmpty());
        $draft     = $assigned->filter(fn ($s) => $s->scores->where('judge_id', $judgeId)->where('is_submitted', false)->isNotEmpty());
        $pending   = $assigned->count() - $submitted->count() - $draft->count();

        $avgScore = $submitted->isNotEmpty()
            ? round($submitted->avg(fn ($s) => $s->scores->where('judge_id', $judgeId)->first()?->details->sum('value') ?? 0))
            : 0;

        $maxTotal = $program->criteria->sum('max_score');

        return [
            'assigned'   => $assigned->count(),
            'submitted'  => $submitted->count(),
            'draft'      => $draft->count(),
            'pending'    => $pending,
            'pct'        => $assigned->count() ? round($submitted->count() / $assigned->count() * 100) : 0,
            'avg_score'  => $avgScore,
            'max_total'  => $maxTotal,
        ];
    }

    private function submitterStats(Program $program, int $userId): array
    {
        $my = $program->submissions->where('submitter_id', $userId);

        return [
            'total'    => $my->count(),
            'approved' => $my->where('status', 'approved')->count(),
            'pending'  => $my->where('status', 'pending')->count(),
            'rejected' => $my->where('status', 'rejected')->count(),
        ];
    }
}
