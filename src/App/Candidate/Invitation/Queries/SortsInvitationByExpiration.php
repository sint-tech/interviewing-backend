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

        return $query->leftJoin('vacancies', 'invitations.vacancy_id', '=', 'vacancies.id')
            ->orderByRaw(
                "CASE WHEN invitations.used_at IS NOT NULL OR invitations.expired_at <= {$nowFunction} OR vacancies.ended_at <= {$nowFunction} THEN 1 ELSE 0 END " . ($descending ? 'DESC' : 'ASC')
            );
    }
}
