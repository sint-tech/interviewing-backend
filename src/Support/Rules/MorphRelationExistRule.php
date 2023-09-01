<?php

namespace Support\Rules;

use Closure;
use Illuminate\Support\Facades\DB;

class MorphRelationExistRule extends MorphRelationValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->preValidationStepsPassed($attribute, $value, $fail)) {
            return;
        }

        $table_name = $this->guessTableName(
            is_array($this->allowedModelNames) ?
                $value[$this->modelKeyInput] :
                $this->allowedModelNames::tryFrom($value[$this->modelKeyInput])->value
        );

        if (DB::table($table_name)
            ->where($this->primaryKeyColumn, $value[$this->primaryKeyInput])
            ->whereNull('deleted_at')
            ->doesntExist()
        ) {
            $fail('the :attribute field is not valid');
        }
    }
}
