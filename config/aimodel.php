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
        'gpt-3-5-turbo' => [
            'model' => 'gpt-3.5-turbo',
            'system_prompt' => env('GPT_3_5_turbo', json_encode([
                'is_logical' => '<true|false>',
                'correctness_rate' => '<you score for correctness rate evaluation for interviewee answer from 1 to 10>',
                'is_correct' => '<true|false>',
                'answer_analysis' => "<your analysis and justification about the interviewee's answer>",
                'english_score' => '<your score for English language evaluation for interviewee answer from 1 to 10>',
                'english_score_analysis' => "<your analysis and justification about why interviewee's answer got that english_score>",
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
