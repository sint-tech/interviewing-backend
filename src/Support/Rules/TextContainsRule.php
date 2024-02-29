<?php

namespace Support\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TextContainsRule implements ValidationRule
{
    protected $substrings;

    /**
     * Create a new rule instance.
     *
     * @param  string  $substring
     * @return void
     */
    public function __construct(array $substrings)
    {
        $this->substrings = $substrings;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        foreach ($this->substrings as $substring) {
            if (strpos($value, $substring) === false) {
                $fail("The :attribute must contain the substring: {$substring}");
            }
        }
    }
}
