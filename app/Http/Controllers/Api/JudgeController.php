<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Judge;
use App\Models\Program;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JudgeController extends Controller
{
    /** List all judges (admin: all, judge: self, submitter: none) */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user->isAdmin() || $user->isJudge(), 403);

        $judges = Judge::all()->map(fn ($j) => $this->format($j));

        return response()->json($judges);
    }

    /** List judges assigned to a program */
    public function programJudges(Program $program): JsonResponse
    {
        $judges = $program->judges()->get()->map(fn ($j) => $this->format($j));

        return response()->json($judges);
    }

    /** Add judge to program */
    public function addToProgram(Request $request, Program $program): JsonResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $data = $request->validate([
            'judge_id' => 'required|string|exists:judges,id',
        ]);

        $program->judges()->syncWithoutDetaching([$data['judge_id']]);

        return response()->json(['message' => 'Đã thêm giám khảo vào chương trình.']);
    }

    /** Remove judge from program */
    public function removeFromProgram(Request $request, Program $program, Judge $judge): JsonResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $program->judges()->detach($judge->id);

        // Remove their score drafts (not submitted ones) for this program's submissions
        $submissionIds = $program->submissions()->pluck('id');
        \App\Models\Score::where('judge_id', $judge->id)
            ->whereIn('submission_id', $submissionIds)
            ->where('is_submitted', false)
            ->delete();

        return response()->json(['message' => 'Đã gỡ giám khảo khỏi chương trình.']);
    }

    private function format(Judge $j): array
    {
        return [
            'id'        => $j->id,
            'name'      => $j->name,
            'email'     => $j->email,
            'specialty' => $j->specialty,
            'group'     => $j->judge_group,
        ];
    }
}
