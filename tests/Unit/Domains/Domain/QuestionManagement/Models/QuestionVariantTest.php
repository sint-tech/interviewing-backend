<?php

namespace Tests\Unit\Domains\Domain\QuestionManagement\Models;

use Tests\TestCase;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Domain\Organization\Models\Employee;
use Domain\QuestionManagement\Enums\QuestionVariantStatusEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Domain\QuestionManagement\Models\QuestionVariant;

class QuestionVariantTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itShouldRetrieveAllVariantsForSintUser()
    {
        $sintUser = User::factory()->createOne();
        $this->actingAs($sintUser, 'admin');

        DB::enableQueryLog();
        QuestionVariant::all();
        $query = DB::getQueryLog()[0];

        $this->assertEquals('select * from "question_variants" where "question_variants"."deleted_at" is null', $query['query']);
    }

    /** @test */
    public function itShouldRetrieveAllVariantsForOrganizationAndPublicVariants()
    {
        $employee = Employee::factory()->createOne();
        $this->actingAs($employee, 'organization');

        DB::enableQueryLog();
        QuestionVariant::all();
        $query = DB::getQueryLog()[0];

        $this->assertEquals('select * from "question_variants" where "question_variants"."deleted_at" is null and ("organization_id" = ? or "status" = ?)', $query['query']);
        $this->assertEquals($employee->organization_id, $query['bindings'][0]);
        $this->assertEquals(QuestionVariantStatusEnum::Public->value, $query['bindings'][1]);
    }
}
