<?php

namespace Support\ValueObjects;

class PromptMessage
{
    public readonly array $placeholders;

    /**
     * @throws \Exception
     */
    public function __construct(public readonly string $message, public readonly array $replacers)
    {
        $this->validReplacers($replacers);
    }

    /**
     * @throws \Exception
     */
    public static function make(string $message, array $replacers = []): static
    {
        return new static($message, $replacers);
    }

    public static function message(string $message, array $replacers = []): string
    {
        return self::make($message, $replacers);
    }

    public function promptMessage(): string
    {
        $str = $this->message;

        foreach ($this->placeholders as $search => $replace) {
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

    public function replaceMany(array $replaces): self
    {
        return new self($this->promptMessage(), $replaces);
    }

    /**
     * @throws \Exception
     */
    private function validReplacers(array $replacers): void
    {
        $result = [];

        foreach ($replacers as $search => $replaced) {
            if (!is_string($search) || !str($search)->startsWith('_') || !str($search)->endsWith('_')) {
                throw new \Exception(sprintf("this replacer: %s must be string, starts with '_' and ends with '_'", $search));
            }
            $replaced = $this->castReplacedToString($replaced);

            $result[$search] = $replaced;
        }

        $this->placeholders = $result;
    }

    public function toString(): string
    {
        return $this->promptMessage();
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    private function castReplacedToString(mixed $replaced): string
    {
        if (is_numeric($replaced)) {
            $replaced = (string) $replaced;
        }

        if (is_array($replaced)) {
            $replaced = collect($replaced)->implode(',');
        }

        return (string) $replaced;
    }
}
