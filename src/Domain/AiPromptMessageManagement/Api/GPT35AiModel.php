<?php

namespace Domain\AiPromptMessageManagement\Api;

use OpenAI\Laravel\Facades\OpenAI;

class GPT35AiModel extends AiModelClientInterface
{
    public function ask(string $answerText)
    {
        $response = OpenAI::chat()->create([
            'model' => $this->promptMessage->aiModel->value,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $this->systemMessage(),
                ],
                [
                    'role' => 'user',
                    'content' => $this->questionMessage($answerText),
                ],
            ],
        ]);

        return $response->choices[0]->message->content;
    }

    protected function replaceKeyWithValue(string &$content, string $key, string $value): string
    {
        return $content = str_replace($key, $value, $content);
    }

    protected function questionMessage(string $answerText): string
    {
        $messageContent = 'interviewer: {{INTERVIEWER_QUESTION}}.\\n interviewee: {{INTERVIEWEE_ANSWER}}.';

        $this->replaceKeyWithValue($messageContent, '{{INTERVIEWER_QUESTION}}', $this->promptMessage->questionVariant->text);

        $this->replaceKeyWithValue($messageContent, '{{INTERVIEWEE_ANSWER}}', $answerText);

        return $messageContent;
    }

    private function systemMessage(): string
    {
        return "I'm conducting an interview, and I asked the interviewee some questions about the interviewee behaviors
        Please provide your maximum precise evaluation as an HR expert about this answer regarding 4 points is it logical or illogical.
        rate this answer using scale 1 to 10, as 1 means he has a bad behavior or trait, and 10 means he doesn't have any bad behavior or trait.
        is the interviewee answer correct.
        analyze in brief the answer.
        with this json shape
        {
            is_logical: <true|false>,
            rate: <1 to 10>,
            is_correct: <true|false>,
            answer_analysis: <analysis interviewee's about the answer>,
        }

        Next, I\'ll send you the question, and the answer, you reply and be precise to the max and be bold and cold-hearted, and be 100% sure about it.";
    }
}
