<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Judge;
use App\Models\Program;
use App\Models\Score;
use App\Models\Submission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function assign(Request $request, Program $program, Submission $submission, Judge $judge): JsonResponse
    {
        abort_unless($request->user()->isAdmin(), 403);
        abort_unless($submission->program_id === $program->id, 404);
        abort_unless($submission->status === 'approved', 422, 'Chỉ phân công hồ sơ đã được duyệt.');
        abort_unless($program->judges()->where('judges.id', $judge->id)->exists(), 422, 'Giám khảo không thuộc chương trình này.');

        $submission->assignedJudges()->syncWithoutDetaching([$judge->id]);

        return response()->json(['message' => 'Đã phân công giám khảo.']);
    }

    public function bulkAssign(Request $request, Program $program): JsonResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $data = $request->validate([
            'sub_ids'    => 'required|array|min:1',
            'sub_ids.*'  => 'required|string|exists:submissions,id',
            'judge_ids'  => 'required|array|min:1',
            'judge_ids.*'=> 'required|string|exists:judges,id',
        ]);

        $programSubmissionIds = $program->submissions()->pluck('id');
        $programJudgeIds = $program->judges()->pluck('judges.id');

        $outsideSubs = collect($data['sub_ids'])->diff($programSubmissionIds);
        if ($outsideSubs->isNotEmpty()) {
            return response()->json([
                'message'     => 'Một số hồ sơ không thuộc chương trình này.',
                'invalid_ids' => $outsideSubs->values(),
            ], 422);
        }

        $outsideJudges = collect($data['judge_ids'])->diff($programJudgeIds);
        if ($outsideJudges->isNotEmpty()) {
            return response()->json([
                'message'     => 'Một số giám khảo không thuộc chương trình này.',
                'invalid_ids' => $outsideJudges->values(),
            ], 422);
        }

        $submissions = Submission::whereIn('id', $data['sub_ids'])->get();

        $notApproved = $submissions->where('status', '!=', 'approved')->pluck('id');
        if ($notApproved->isNotEmpty()) {
            return response()->json([
                'message'     => 'Một số hồ sơ chưa được duyệt.',
                'invalid_ids' => $notApproved->values(),
            ], 422);
        }

        foreach ($submissions as $submission) {
            $submission->assignedJudges()->syncWithoutDetaching($data['judge_ids']);
        }

        return response()->json([
            'message'  => 'Đã phân công ' . count($data['judge_ids']) . ' giám khảo cho ' . $submissions->count() . ' hồ sơ.',
            'assigned' => $submissions->count(),
        ]);
    }

    public function unassign(Request $request, Program $program, Submission $submission, Judge $judge): JsonResponse
    {
        abort_unless($request->user()->isAdmin(), 403);
        abort_unless($submission->program_id === $program->id, 404);

        $submission->assignedJudges()->detach($judge->id);

        // Remove draft score (keep submitted scores for audit trail)
        Score::where('submission_id', $submission->id)
            ->where('judge_id', $judge->id)
            ->where('is_submitted', false)
            ->delete();

        return response()->json(['message' => 'Đã gỡ phân công.']);
    }
}
