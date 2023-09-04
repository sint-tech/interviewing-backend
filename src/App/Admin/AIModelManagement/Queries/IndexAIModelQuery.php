<?php

namespace App\Admin\AIModelManagement\Queries;

use Domain\AiPromptMessageManagement\Models\AIModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class IndexAIModelQuery extends QueryBuilder
{
    public function __construct(Request $request)
    {
        $subject = AIModel::query();

        parent::__construct($subject, $request);
    }
}
