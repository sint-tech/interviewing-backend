<?php

namespace Tests\Feature\App\Organization\InvitationManagement;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Domain\Vacancy\Models\Vacancy;
use Illuminate\Support\Facades\Bus;
use Database\Seeders\SintAdminsSeeder;
use Domain\Organization\Models\Employee;
use App\Exceptions\LimitExceededException;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Domain\InterviewManagement\Models\InterviewTemplate;
use App\Admin\InvitationManagement\Jobs\ImportInvitationsFromExcelJob;

class ImportInvitationsControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected Employee $employee;

    protected UploadedFile $excelFile;

    protected UploadedFile $wrong_cc;

    protected UploadedFile $duplicate_emails;

    public function setUp(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();

        $this->seed(SintAdminsSeeder::class);

        $this->employee = Employee::factory()->createOne();

        $this->excelFile = (new UploadedFile(
            public_path('tests/invitations.csv'),
            'invitations.csv',
            'csv',
            null,
            true
        ));

        $this->wrong_cc = (new UploadedFile(
            public_path('tests/wrong_country_code_invitations.csv'),
            'wrong_cc.csv',
            'csv',
            null,
            true
        ));

        $this->duplicate_emails = (new UploadedFile(
            public_path('tests/duplicate_emails_invitations.csv'),
            'wrong_email.csv',
            'csv',
            null,
            true
        ));

        $this->actingAs($this->employee, 'organization');
    }

    /** @test  */
    public function itShouldCallImportInvitationsFromExcelJobWhenHitEndpoint(): void
    {
        Bus::fake();

        $vacancy = Vacancy::factory()->for($this->employee->organization, 'organization')->createOne([
            'interview_template_id' => InterviewTemplate::factory()
                ->for($this->employee, 'creator')
                ->createOne()->getKey(),
        ]);

        $this->post(route('organization.invitations.import'), [
            'file' => $this->excelFile,
            'vacancy_id' => $vacancy->getKey(),
            'should_be_invited_at' => now()->addDays(5)->format('Y-m-d H:i'),
        ])->assertSuccessful();

        Bus::assertDispatched(ImportInvitationsFromExcelJob::class);
    }

    /** @test  */
    public function itShouldNotSaveInvitationsWhenLimitExceeded(): void
    {
        $vacancy = Vacancy::factory()->for($this->employee->organization, 'organization')->createOne([
            'interview_template_id' => InterviewTemplate::factory()
                ->for($this->employee, 'creator')
                ->createOne()->getKey(),
        ]);

        $this->employee->organization->update([
            'limit' => 1,
        ]);

        $this->post(route('organization.invitations.import'), [
            'file' => $this->excelFile,
            'vacancy_id' => $vacancy->getKey(),
            'should_be_invited_at' => now()->addDays(5)->format('Y-m-d H:i'),
        ]);

        $this->assertDatabaseCount('invitations', 0);
    }

    /** @test  */
    public function itShouldReturnExceptionWhenExceedInvitationsLimit(): void
    {
        $this->expectException(LimitExceededException::class);
        $this->expectExceptionMessage('You have exceeded your invitation limit');

        $vacancy = Vacancy::factory()->for($this->employee->organization, 'organization')->createOne([
            'interview_template_id' => InterviewTemplate::factory()
                ->for($this->employee, 'creator')
                ->createOne()->getKey(),
        ]);

        $this->employee->organization->update([
            'limit' => 1,
        ]);

        ImportInvitationsFromExcelJob::dispatchSync(
            $this->excelFile,
            $vacancy->getKey(),
            $vacancy->interview_template_id,
            now()->addDays(5),
        );
    }

    /** @test  */
    public function itShouldAddConsumptionWhenInvitationIsCreated(): void
    {
        $vacancy = Vacancy::factory()->for($this->employee->organization, 'organization')->createOne([
            'interview_template_id' => InterviewTemplate::factory()
                ->for($this->employee, 'creator')
                ->createOne()->getKey(),
        ]);

        $this->post(route('organization.invitations.import'), [
            'file' => $this->excelFile,
            'vacancy_id' => $vacancy->getKey(),
            'should_be_invited_at' => now()->addDays(5)->format('Y-m-d H:i'),
        ]);

        $this->assertEquals(
            $this->employee->refresh()->organization->interview_consumption,
            4
        );
    }

    /** @test  */
    public function itShouldNotAddConsumptionWhenEmailIsDuplicated(): void
    {
        $vacancy = Vacancy::factory()->for($this->employee->organization, 'organization')->createOne([
            'interview_template_id' => InterviewTemplate::factory()
                ->for($this->employee, 'creator')
                ->createOne()->getKey(),
        ]);

        $this->post(route('organization.invitations.import'), [
            'file' => $this->duplicate_emails,
            'vacancy_id' => $vacancy->getKey(),
            'should_be_invited_at' => now()->addDays(5)->format('Y-m-d H:i'),
        ]);

        $this->assertEquals(
            $this->employee->refresh()->organization->interview_consumption,
            2
        );
    }

        /** @test  */
        public function itShouldNotAddConsumptionWhenCountryCodeIsWrong(): void
        {
            $vacancy = Vacancy::factory()->for($this->employee->organization, 'organization')->createOne([
                'interview_template_id' => InterviewTemplate::factory()
                    ->for($this->employee, 'creator')
                    ->createOne()->getKey(),
            ]);

            $this->post(route('organization.invitations.import'), [
                'file' => $this->wrong_cc,
                'vacancy_id' => $vacancy->getKey(),
                'should_be_invited_at' => now()->addDays(5)->format('Y-m-d H:i'),
            ]);

            $this->assertEquals(
                $this->employee->refresh()->organization->interview_consumption,
                2
            );
        }
}
