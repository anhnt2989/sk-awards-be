<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Score;
use App\Models\ScoreDetail;
use App\Models\Submission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScoreController extends Controller
{
    /** Get all scores for a submission (admin sees all, judge sees own) */
    public function index(Request $request, Program $program, Submission $submission): JsonResponse
    {
        abort_unless($submission->program_id === $program->id, 404);
        $user = $request->user();

        $query = $submission->scores()->with(['judge', 'details']);

        if ($user->isJudge()) {
            $judgeId = $user->judge?->id;
            abort_unless(
                $submission->assignedJudges()->where('judges.id', $judgeId)->exists(),
                403, 'Bạn không được phân công hồ sơ này.'
            );
            $query->where('judge_id', $judgeId);
        }

        $scores = $query->get()->map(fn ($s) => $this->format($s, $program));

        return response()->json($scores);
    }

    /** Save (draft or submit) a score — judge only */
    public function upsert(Request $request, Program $program, Submission $submission): JsonResponse
    {
        $user = $request->user();
        abort_unless($user->isJudge(), 403, 'Chỉ giám khảo mới có thể chấm điểm.');
        abort_unless($submission->program_id === $program->id, 404);
        abort_unless($submission->status === 'approved', 422, 'Hồ sơ chưa được duyệt.');

        $judgeId = $user->judge?->id;
        abort_unless($judgeId, 403, 'Tài khoản không có hồ sơ giám khảo.');
        abort_unless(
            $submission->assignedJudges()->where('judges.id', $judgeId)->exists(),
            403, 'Bạn không được phân công hồ sơ này.'
        );

        $criteriaIds = $program->criteria->pluck('id')->all();

        $data = $request->validate([
            'scores'      => 'required|array',
            'scores.*'    => 'integer|min:0|max:1000',
            'comment'     => 'nullable|string|max:2000',
            'is_submitted' => 'required|boolean',
        ]);

        // Validate criterion keys match program
        $inputKeys = array_keys($data['scores']);
        $invalid   = array_diff($inputKeys, $criteriaIds);
        abort_unless(empty($invalid), 422, 'Tiêu chí không hợp lệ: ' . implode(', ', $invalid));

        // Validate per-criterion max
        foreach ($program->criteria as $crit) {
            $val = $data['scores'][$crit->id] ?? null;
            if ($val !== null && $val > $crit->max_score) {
                abort(422, "Điểm tiêu chí '{$crit->name}' vượt quá tối đa {$crit->max_score}.");
            }
        }

        return DB::transaction(function () use ($data, $submission, $judgeId, $criteriaIds) {
            $score = Score::firstOrCreate(
                ['submission_id' => $submission->id, 'judge_id' => $judgeId],
                ['comment' => '', 'is_submitted' => false]
            );

            abort_if($score->is_submitted, 422, 'Điểm đã nộp, không thể chỉnh sửa.');

            $score->update([
                'comment'      => $data['comment'] ?? $score->comment,
                'is_submitted' => $data['is_submitted'],
            ]);

            foreach ($data['scores'] as $critId => $value) {
                ScoreDetail::updateOrCreate(
                    ['score_id' => $score->id, 'criterion_id' => $critId],
                    ['value'    => $value]
                );
            }

            $score->load(['judge', 'details']);

            return response()->json($this->format($score, $score->submission->program));
        });
    }

    private function format(Score $score, Program $program): array
    {
        $detailMap = $score->details->pluck('value', 'criterion_id')->all();

        return [
            'id'           => $score->id,
            'submission_id' => $score->submission_id,
            'judge_id'     => $score->judge_id,
            'judge_name'   => $score->judge?->name,
            'comment'      => $score->comment,
            'is_submitted' => $score->is_submitted,
            'scores'       => $detailMap,
            'total'        => array_sum($detailMap),
            'max_total'    => $program->criteria->sum('max_score'),
            'updated_at'   => $score->updated_at?->toIso8601String(),
        ];
    }
}
