<?php

namespace Domain\QuestionManagement\Builders;

use Domain\QuestionManagement\Enums\QuestionClusterRecommendationEnum;
use Illuminate\Database\Eloquent\Builder;

class QuestionClusterRecommendationBuilder extends Builder
{
    public function whereType(string $operation = '=',QuestionClusterRecommendationEnum|array $value,string $boolean = 'and')
    {
        $this->where('type',$operation,$value,$boolean);

        return $this;
    }

    public function whereTypeIsAdvice():self
    {
        return $this->whereType('=',QuestionClusterRecommendationEnum::Advice);
    }

    public function whereTypeIsImpact():self
    {
        return $this->whereType('=',QuestionClusterRecommendationEnum::Impact);
    }
}
