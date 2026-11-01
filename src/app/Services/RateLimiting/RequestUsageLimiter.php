<?php

namespace App\Services\RateLimiting;

use App\Models\ApiUsageCounter;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RequestUsageLimiter
{
    /**
     * @return array{allowed: bool, used: int, limit: int|null, resets_at: string}
     */
    public function attempt(string $subjectType, string $subjectId, ?int $limit): array
    {
        $today = Carbon::today('UTC')->toDateString();
        $resetsAt = Carbon::tomorrow('UTC')->startOfDay();

        return DB::transaction(function () use ($subjectType, $subjectId, $today, $limit, $resetsAt) {
            $counter = ApiUsageCounter::query()
                ->where('subject_type', $subjectType)
                ->where('subject_id', $subjectId)
                ->where('usage_date', $today)
                ->lockForUpdate()
                ->first();

            if (!$counter) {
                try {
                    $counter = ApiUsageCounter::query()->firstOrCreate(
                        ['subject_type' => $subjectType, 'subject_id' => $subjectId, 'usage_date' => $today],
                        ['request_count' => 0]
                    );
                } catch (QueryException $e) {
                    // Another concurrent request created it first; fall through to re-read below.
                }

                $counter = ApiUsageCounter::query()
                    ->where('subject_type', $subjectType)
                    ->where('subject_id', $subjectId)
                    ->where('usage_date', $today)
                    ->lockForUpdate()
                    ->firstOrFail();
            }

            $unlimited = $limit === null;
            $used = $counter->request_count;

            if (!$unlimited && $used >= $limit) {
                return [
                    'allowed' => false,
                    'used' => $used,
                    'limit' => $limit,
                    'resets_at' => $resetsAt->toIso8601String(),
                ];
            }

            $counter->increment('request_count');

            return [
                'allowed' => true,
                'used' => $used + 1,
                'limit' => $limit,
                'resets_at' => $resetsAt->toIso8601String(),
            ];
        });
    }
}
