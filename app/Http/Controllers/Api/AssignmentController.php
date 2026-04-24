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
