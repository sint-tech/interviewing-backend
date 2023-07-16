<?php

namespace Support\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;

abstract class MorphRelationValidationRule implements ValidationRule
{
    const EnumModels = 'enum';

    const ArrayModels = 'array';

    protected string $primaryKeyInput = 'model_id';

    protected string $modelKeyInput = 'model_type';

    public function __construct
    (
        protected array|string $allowedModelNames,
        protected string|null $tableName = null,
        protected string $primaryKeyColumn = 'id'
    )
    {
        $this->allowedModelsType();
    }

    public function configMorphInputNames(string $primaryKeyInput = 'id',string $modelKeyInput = 'model'): self
    {
        $this->primaryKeyInput = $primaryKeyInput;

        $this->modelKeyInput = $modelKeyInput;

        return $this;
    }

    public function guessTableName(string $value):string
    {
        if ($this->tableName) {
            return $this->tableName;
        }

        return Str::of($value)
            ->snake()
            ->plural()
            ->toString();
    }

    protected function preValidationStepsPassed(string $attribute, mixed $value, Closure $fail):bool
    {
        if (! is_array($value)) {
            return false;
        }

        if (! array_key_exists($this->primaryKeyInput,$value) || ! array_key_exists($this->modelKeyInput,$value)) {
            $fail("the :attribute must have keys $this->primaryKeyInput and $this->modelKeyInput");
            return false;
        }

        if ($this->modelsAreArray() && ! in_array($value[$this->modelKeyInput],$this->allowedModelNames)) {
            $fail("the :attribute.$this->allowedModelNames is not valid");
            return false;
        }

        if ($this->modelsAreEnum() && enum_exists($this->allowedModelNames) && is_null($this->allowedModelNames::tryFrom($value[$this->modelKeyInput]))) {
            $fail("the :attribute.{$this->modelKeyInput} is not supported");
            return false;
        }

        return true;
    }

    protected function allowedModelsType():string
    {
        if (enum_exists($this->allowedModelNames)) {
            return self::EnumModels;
        }
        else if(is_array($this->allowedModelNames)) {
            return self::ArrayModels;
        }

        throw new \LogicException('$allowedModelNames must be enum class path or array of strings');
    }

    protected function modelsAreEnum():bool
    {
        return $this->allowedModelsType() == self::EnumModels;
    }

    protected function modelsAreArray():bool
    {
        return $this->allowedModelsType() == self::ArrayModels;
    }
}
