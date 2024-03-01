<?php
namespace Domain\AiPromptMessageManagement\Enums;

enum PromptTemplateVariableEnum: string
{
    case QuestionClusterAvgScore = '_QUESTION_CLUSTER_AVG_SCORE_';
    case QuestionClusterName = '_QUESTION_CLUSTER_NAME_';
    case JobTitle = '_JOB_TITLE_';

    public static function statsVariables(): array
    {
        return [
            self::QuestionClusterAvgScore->value,
            self::QuestionClusterName->value,
        ];
    }

    public static function textVariables(): array
    {
        return [
            self::JobTitle->value,
        ];
    }
}
