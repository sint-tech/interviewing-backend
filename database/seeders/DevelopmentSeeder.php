<?php

namespace Database\Seeders;

use Domain\AnswerManagement\Models\Answer;
use Domain\AnswerManagement\Models\AnswerVariant;
use Domain\Candidate\Models\RegistrationReason;
use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\JobTitle\Models\JobTitle;
use Domain\Organization\Models\Organization;
use Domain\QuestionManagement\Models\Question;
use Domain\QuestionManagement\Models\QuestionCluster;
use Domain\QuestionManagement\Models\QuestionVariant;
use Domain\Skill\Models\Skill;
use Domain\Users\Models\User;
use Illuminate\Database\Seeder;

class DevelopmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $env = env('APP_ENV', 'development');

        if ($env != 'development' && $env != 'local') {
            return;
        }

        $this->call([
            SintAdminsSeeder::class,
        ]);


        if (JobTitle::query()->doesntExist()) {
            JobTitle::factory(10)->create(['availability_status' => 'active']);
        }

        if (RegistrationReason::query()->doesntExist()) {
            RegistrationReason::factory(10)->create(['availability_status' => 'active']);
        }
        Skill::factory(10)->create();

        QuestionCluster::factory(10)->for(
            User::query()->first(), 'creator'
        )
            ->has(
                Question::factory()
                    ->for(
                        User::query()->first(),
                        'creator'
                    )
                    ->has(
                        QuestionVariant::factory(10)
                            ->for(
                                User::query()->first(),
                        'creator'
                            )
                            ->for(
                                User::query()->first(),
                                'owner'
                            ),
                        'questionVariants'
                    ),
                'questions'
        )->create();

        InterviewTemplate::factory(5)
            ->for(
                User::query()->first(),
                'creator'
            )
            ->for(
                User::query()->first(),
                'owner'
            )
            ->create()
            ->each(function (InterviewTemplate $template) {
                $question_variant = QuestionVariant::query()
                    ->whereHas('question',fn($q) => $q->has('questionCluster'))
                    ->with('question.questionCluster')
                    ->inRandomOrder()
                    ->first();

                $template->questionVariants()->attach($question_variant,['question_cluster_id' => $question_variant->question->questionCluster->getKey()]);
            });

        Answer::factory()->count(10)
            ->create();

        AnswerVariant::factory()
            ->count(10)
            ->for(
                User::query()->first(),
                'creator'
            )
            ->for(
                User::query()->first(),
                'owner'
            )->create();
    }
}
