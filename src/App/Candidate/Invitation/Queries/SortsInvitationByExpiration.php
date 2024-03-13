<?php
namespace App\Candidate\Invitation\Queries;

use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\Sorts\Sort;
use Illuminate\Database\Eloquent\Builder;

class SortsInvitationByExpiration implements Sort
{
    public function __invoke(Builder $query, $descending, string $property): Builder
    {
        $nowFunction = DB::connection()->getDriverName() === 'sqlite' ? 'CURRENT_TIMESTAMP' : 'NOW()';

        return $query->orderByRaw(
            "CASE WHEN used_at IS NOT NULL OR expired_at <= {$nowFunction} THEN 1 ELSE 0 END " . ($descending ? 'DESC' : 'ASC')
        );
    }
}
