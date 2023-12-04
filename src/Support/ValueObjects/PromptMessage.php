<?php

namespace Support\ValueObjects;

use Illuminate\Support\Str;

class PromptMessage
{
    protected readonly array $replacers;

    /**
     * @throws \Exception
     */
    public function __construct(protected readonly string $message, array $replacers)
    {
        $this->validReplacers();

        $this->replacers = array_fill_keys(array_keys($replacers),array_values($replacers));
    }

    /**
     * @throws \Exception
     */
    public static function make(string $message, array $replacers): static
    {
        return new static($message,$replacers);
    }

    public function message(): string
    {
        return (string) str($this->message)->replace($this->replacers(),$this->replacedValues());
    }

    public function replacedValues(): array
    {
        return array_values($this->replacers);
    }

    public function replacers(): array
    {
        return array_values($this->replacers);
    }

    /**
     * @throws \Exception
     */
    private function validReplacers(): void
    {
        foreach (array_keys($this->replacers) as $search => $replaced) {
            if (! is_string($search) && ! str($search)->startsWith('_') && ! str($search)->endsWith('_')) {
                throw new \Exception(sprintf('this replacer: %s is invalid',$search));
            }
            if (! is_string($replaced)) {
                throw new \Exception('replaced value must be string');
            }
        }
    }

    public function __toString(): string
    {
        return $this->message();
    }
}
