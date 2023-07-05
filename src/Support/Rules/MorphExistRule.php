<?php

namespace Support\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MorphExistRule implements ValidationRule
{
    protected string $primaryKeyInput = 'model_id';

    protected string $modelKeyInput = 'model_type';

    public function __construct
    (
        protected array|string $allowedModelNames,
        protected string|null $tableName = null,
        protected string $primaryKeyColumn = 'id'
    )
    {

    }

    public function configMorphNames(string $primaryKeyInput = 'id',string $modelKeyInput = 'model'): self
    {
        $this->primaryKeyInput = $primaryKeyInput;

        $this->modelKeyInput = $modelKeyInput;

        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_array($value)) {
            $fail("the :attribute field must be an array");
            return ;
        }

        if (! array_key_exists($this->primaryKeyInput,$value) || ! array_key_exists($this->modelKeyInput,$value)) {
            $fail("the :attribute must have keys $this->primaryKeyInput and $this->modelKeyInput");
            return ;
        }

        if (is_array($this->allowedModelNames) && ! in_array($value[$this->modelKeyInput],$this->allowedModelNames)) {
            $fail("the :attribute.$this->allowedModelNames is not valid");
            return ;
        }

        if (is_string($this->allowedModelNames) && enum_exists($this->allowedModelNames) && is_null($this->allowedModelNames::tryFrom($value[$this->modelKeyInput]))) {
            $fail("the :attribute.{$this->modelKeyInput} is not supported");
            return ;
        }

        $table_name = $this->guessTableName(
          is_array($this->allowedModelNames) ?
              $value[$this->modelKeyInput] :
              $this->allowedModelNames::tryFrom($value[$this->modelKeyInput])->value
        );

        if (! Schema::hasTable($table_name)) {
            $fail("the :attribute.{$this->modelKeyInput} is not supported");
            return ;
        }

        if (DB::table($table_name)->where($this->primaryKeyColumn,$value[$this->primaryKeyInput])->whereNull('deleted_at')->doesntExist()) {
            $fail("the :attribute is not valid");
        }
    }

    public function guessTableName(string $value):string
    {
        if ($this->tableName) {
            return $this->tableName;
        }

        return Str::of($value)
            ->ucfirst()
            ->snake()
            ->plural()
            ->toString();
    }
}
