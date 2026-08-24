<?php

namespace App\VoteSystem\Repositories;

use App\Models\Voter;
use App\VoteSystem\Domain\VoterStatistics;
use Illuminate\Support\Facades\DB;

class VoterRepository
{
    public function aggregateVoterStatistics(): VoterStatistics
    {
        /** @psalm-suppress InvalidArgument firstOrFail()'s column-array param is stubbed as
         * array<array-key,string>, but Eloquent's query builder genuinely accepts raw Expression
         * objects there at runtime - the stub is just narrower than the real implementation. */
        $stats = Voter::firstOrFail([
            DB::raw('count(*) as total'),
            DB::raw('count(used_at) AS used'),
            DB::raw('count(*) - count(used_at) as unused'),
        ])->attributesToArray();

        return VoterStatistics::fromArray($stats);
    }
}
