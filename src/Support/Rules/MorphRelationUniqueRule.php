<?php

namespace Support\Rules;

use Closure;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MorphRelationUniqueRule extends MorphRelationValidationRule
{
    public function __construct(
        array|string $allowedModelNames,
        string $tableName,
        protected string $morphTypeColumn,
        protected ?string $morphIdColumn = 'model_id'
    ) {
        if (count(func_get_args()) == 3) {
            $this->morphTypeColumn = Str::of($morphTypeColumn)->snake()->append('_type');
            $this->morphIdColumn = Str::of($morphTypeColumn)->snake()->append('_id');
        }

        parent::__construct($allowedModelNames, $tableName, $morphIdColumn);
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        if (! $this->preValidationStepsPassed($attribute, $value, $fail)) {
            return;
        }

        if (DB::table($this->tableName)
            ->where($this->morphIdColumn, $value[$this->primaryKeyInput])
            ->where(fn (Builder $builder) => $builder
                ->where($this->morphTypeColumn, $value[$this->modelKeyInput])
                ->orWhere($this->morphTypeColumn, Relation::getMorphedModel($value[$this->modelKeyInput]))
            )
            ->whereNull('deleted_at')->limit(1)->exists()
            //todo support ignore id, support add wheres
        ) {
            $fail('the :attribute field is already exist');
        }
    }
}
