<?php

namespace App\Admin\AnswerManagement\Requests;

use Domain\AnswerManagement\Enums\AnswerVariantOwnerEnum;
use Domain\Organization\Models\Organization;
use Domain\Users\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Support\Rules\MorphExistRule;

class AnswerVariantStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'answer_id' => ['required',Rule::exists('answers','id')->withoutTrashed()],
            'text'      => ['required','string','min:3','max:100000'],
            'description'   => ['nullable','string','min:3','max:1000'],
            'score'         => ['required','between:1,10'],
            'owner'         => ['required','array',
                (new MorphExistRule(AnswerVariantOwnerEnum::class,$this->input('owner.type') == 'admin' ? 'users' : null))
                ->configMorphNames('id','type')
            ],
        ];
    }

    public function getOwnerObject():User|Organization
    {
        $query = match ($this->validated('owner.type')) {
            AnswerVariantOwnerEnum::Organization->value => Organization::query(),
            AnswerVariantOwnerEnum::Admin->value    => User::query(),
        };

        return $query->find($this->validated('owner.id'));
    }
}
