<?php

namespace Domain\AiPromptMessageManagement\Traits;

use Illuminate\Support\Str;

trait ValidateJsonTrait
{
    public function validateJson($str, $keys = [])
    {
        $decoded_response = json_decode($this->cleanString($str), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }

        if (count($keys) > 0) {
            foreach ($keys as $key) {
                if (!array_key_exists($key, $decoded_response)) {
                    $not_found[] = $key;
                }
            }

            if (isset($not_found) && count($not_found) > 0) {
                return false;
            }
        }

        return true;
    }

    public function cleanString($str)
    {
        return Str::of($str)
            ->replace('\t', '')
            ->replace('\n', '')
            ->trim();
    }
}
