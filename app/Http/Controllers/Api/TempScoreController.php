<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Submission;
use App\Models\TempScore;
use App\Models\TempScoreDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TempScoreController extends Controller
{
    public function upsert(Request $request, Program $program, Submission $submission): JsonResponse
    {
        $user = $request->user();
        abort_unless($user->isAssistant(), 403, 'Chỉ trợ lý mới có thể nhập điểm tham khảo.');
        abort_unless($submission->program_id === $program->id, 404);
        abort_unless($submission->status === 'approved', 422, 'Hồ sơ chưa được duyệt.');

        $criteriaIds = $program->criteria->pluck('id')->all();

        $data = $request->validate([
            'scores'   => 'required|array',
            'scores.*' => 'integer|min:0|max:1000',
            'comment'  => 'nullable|string|max:2000',
        ]);

        $inputKeys = array_keys($data['scores']);
        $invalid   = array_diff($inputKeys, $criteriaIds);
        abort_unless(empty($invalid), 422, 'Tiêu chí không hợp lệ: ' . implode(', ', $invalid));

        foreach ($program->criteria as $crit) {
            $val = $data['scores'][$crit->id] ?? null;
            if ($val !== null && $val > $crit->max_score) {
                abort(422, "Điểm tiêu chí '{$crit->name}' vượt quá tối đa {$crit->max_score}.");
            }
        }

        return DB::transaction(function () use ($data, $submission, $user, $program) {
            $tempScore = TempScore::firstOrCreate(
                ['submission_id' => $submission->id, 'user_id' => $user->id],
                ['comment' => '']
            );

            $tempScore->update(['comment' => $data['comment'] ?? $tempScore->comment]);

            foreach ($data['scores'] as $critId => $value) {
                TempScoreDetail::updateOrCreate(
                    ['temp_score_id' => $tempScore->id, 'criterion_id' => $critId],
                    ['value'         => $value]
                );
            }

            $tempScore->load('details');

            return response()->json($this->format($tempScore, $program));
        });
    }

    public function format(TempScore $tempScore, Program $program): array
    {
        $detailMap = $tempScore->details->pluck('value', 'criterion_id')->all();

        return [
            'id'            => $tempScore->id,
            'submission_id' => $tempScore->submission_id,
            'user_id'       => $tempScore->user_id,
            'user_name'     => $tempScore->user?->name,
            'comment'       => $tempScore->comment,
            'scores'        => $detailMap,
            'total'         => array_sum($detailMap),
            'max_total'     => $program->criteria->sum('max_score'),
            'updated_at'    => $tempScore->updated_at?->toIso8601String(),
        ];
    }
}
