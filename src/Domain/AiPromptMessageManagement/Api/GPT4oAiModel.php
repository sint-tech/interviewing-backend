<?php

namespace Domain\AiPromptMessageManagement\Api;

use OpenAI\Laravel\Facades\OpenAI;

class GPT4oAiModel extends AiModelClientInterface
{
    public function prompt(string $answerText): ?string
    {
        $response = OpenAI::chat()->create([
            'model' => $this->promptMessage->aiModel->name->value,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $this->systemPrompt(),
                ],
                [
                    'role' => 'user',
                    'content' => $this->contentPrompt($answerText),
                ],
            ],
        ]);

        return $response->choices[0]->message->content;
    }

    protected function replaceKeyWithValue(string &$content, string $key, string $value): string
    {
        return $content = str_replace($key, $value, $content);
    }

    protected function contentPrompt(string $answerText): string //todo rename to promptMessage
    {
        $replacers = [
            '_QUESTION_TEXT_' => $this->promptMessage->questionVariant->text,
            '_INTERVIEWEE_ANSWER_' => $answerText,
        ];

        $content = $this->promptMessage->content_prompt;

        foreach ($replacers as $replacer => $value) {
            $this->replaceKeyWithValue($content, $replacer, $value);
        }

        return $content;
    }

    private function systemPrompt(): string
    {
        $content = $this->promptMessage->system_prompt;

        $this->replaceKeyWithValue(
            $content,
            '_RESPONSE_JSON_STRUCTURE_',
            "{
            is_logical: <true|false>,
            rate: <1 to 10>,
            is_correct: <true|false>,
            answer_analysis: <analysis interviewee's about the answer>
            }");

        return $content;
    }
}
