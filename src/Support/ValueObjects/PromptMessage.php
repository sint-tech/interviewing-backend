<?php

namespace Support\ValueObjects;

class PromptMessage
{
    protected readonly array $replacers;

    /**
     * @throws \Exception
     */
    public function __construct(protected readonly string $message, array $replacers)
    {
        $this->validReplacers($replacers);
    }

    /**
     * @throws \Exception
     */
    public static function make(string $message, array $replacers): static
    {
        return new static($message,$replacers);
    }

    public function promptMessage(): string
    {
        $str = $this->message;

        foreach ($this->replacers as $search => $replace) {
            $str = str_replace($search, $replace, $str);
        }

        return $str;
    }

    /**
     * @throws \Exception
     */
    public function replace(string $search, string $replace): self
    {
        return new self($this->promptMessage(), [$search => $replace]);
    }

    /**
     * @throws \Exception
     */
    private function validReplacers(array $replacers): void
    {
        foreach ($replacers as $search => $replaced) {
            if (! is_string($search) && ! str($search)->startsWith('_') && ! str($search)->endsWith('_')) {
                throw new \Exception(sprintf('this replacer: %s is invalid', $search));
            }
            if (! is_string($replaced)) {
                throw new \Exception('replaced value must be string');
            }
        }
        $this->replacers = $replacers;
    }

    public function toString(): string
    {
        return $this->promptMessage();
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
