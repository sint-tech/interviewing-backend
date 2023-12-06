<?php

return [

    /*
    |------------------------------------------------------------------
    | generative AI models
    |--------------------------------------------------------------------
    |
    | Here you may configure as many generative AI "models" as you wish,
    | supported Models: gpt-3.5-turbo
     */
    'models' => [
        'gpt-3.5-turbo' => [
            'model' => 'gpt-3.5-turbo',
            'system_prompt' => env('GPT_3_5_turbo', json_encode([
                'is_logical' => '<true,false>',
                'rate' => '1 to 10',
                'is_correct' => '<true,false>',
                'answer_analysis' => "analysis interviewee's about the answer",
            ])),
            'system_prompt_placeholders' => [
                env('GPT_3_5_TURBO_SYSTEM_PROMPT_PLACEHOLDERS', ['_RESPONSE_JSON_STRUCTURE_']),
            ],
            'content_prompt_place_holders' => [
                env('GPT_3_5_TURBO_CONTENT_PROMPT_PLACEHOLDERS', ['_QUESTION_TEXT_', '_INTERVIEWEE_ANSWER_']),
            ],
        ],
    ],
];
