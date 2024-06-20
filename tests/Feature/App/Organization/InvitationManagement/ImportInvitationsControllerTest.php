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
    protected UploadedFile $invitations_csv;
    protected UploadedFile $wrong_cc_csv;
    protected UploadedFile $duplicate_emails_csv;
    protected UploadedFile $invitations_xlsx;
    protected UploadedFile $wrong_cc_xlsx;
    protected UploadedFile $duplicate_emails_xlsx;

    public function setUp(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();

        $this->seed(SintAdminsSeeder::class);

        $this->employee = Employee::factory()->createOne();

        $this->invitations_csv = (new UploadedFile(
            public_path('tests/csvs/invitations.csv'),
            'invitations.csv',
            'csv',
            null,
            true
        ));

        $this->wrong_cc_csv = (new UploadedFile(
            public_path('tests/csvs/wrong_country_code_invitations.csv'),
            'wrong_cc.csv',
            'csv',
            null,
            true
        ));

        $this->duplicate_emails_csv = (new UploadedFile(
            public_path('tests/csvs/duplicate_emails_invitations.csv'),
            'wrong_email.csv',
            'csv',
            null,
            true
        ));

        $this->invitations_xlsx = (new UploadedFile(
            public_path('tests/xlsxs/invitations.xlsx'),
            'invitations.xlsx',
            'xlsx',
            null,
            true
        ));

        $this->wrong_cc_xlsx = (new UploadedFile(
            public_path('tests/xlsxs/wrong_country_code_invitations.xlsx'),
            'wrong_cc.xlsx',
            'xlsx',
            null,
            true
        ));

        $this->duplicate_emails_xlsx = (new UploadedFile(
            public_path('tests/xlsxs/duplicate_emails_invitations.xlsx'),
            'wrong_email.xlsx',
            'xlsx',
            null,
            true
        ));


        $this->actingAs($this->employee, 'organization');
    }

    /** @test  */
    public function itShouldCallImportInvitationsFromExcelJobWhenHitEndpointCsv(): void
    {
        Bus::fake();

        $vacancy = Vacancy::factory()->for($this->employee->organization, 'organization')->createOne([
            'interview_template_id' => InterviewTemplate::factory()
                ->for($this->employee, 'creator')
                ->createOne()->getKey(),
        ]);

        $this->post(route('organization.invitations.import'), [
            'file' => $this->invitations_csv,
            'vacancy_id' => $vacancy->getKey(),
            'should_be_invited_at' => now()->addDays(5)->format('Y-m-d H:i'),
        ])->assertSuccessful();

        Bus::assertDispatched(ImportInvitationsFromExcelJob::class);
    }

    /** @test  */
    public function itShouldCallImportInvitationsFromExcelJobWhenHitEndpointXlsx(): void
    {
        Bus::fake();

        $vacancy = Vacancy::factory()->for($this->employee->organization, 'organization')->createOne([
            'interview_template_id' => InterviewTemplate::factory()
                ->for($this->employee, 'creator')
                ->createOne()->getKey(),
        ]);

        $this->post(route('organization.invitations.import'), [
            'file' => $this->invitations_xlsx,
            'vacancy_id' => $vacancy->getKey(),
            'should_be_invited_at' => now()->addDays(5)->format('Y-m-d H:i'),
        ])->assertSuccessful();

        Bus::assertDispatched(ImportInvitationsFromExcelJob::class);
    }

    /** @test  */
    public function itShouldNotSaveInvitationsWhenLimitExceededCsv(): void
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
            'file' => $this->invitations_csv,
            'vacancy_id' => $vacancy->getKey(),
            'should_be_invited_at' => now()->addDays(5)->format('Y-m-d H:i'),
        ]);

        $this->assertDatabaseCount('invitations', 0);
    }

    /** @test  */
    public function itShouldNotSaveInvitationsWhenLimitExceededXlsx(): void
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
            'file' => $this->invitations_xlsx,
            'vacancy_id' => $vacancy->getKey(),
            'should_be_invited_at' => now()->addDays(5)->format('Y-m-d H:i'),
        ]);

        $this->assertDatabaseCount('invitations', 0);
    }

    /** @test  */
    public function itShouldReturnExceptionWhenExceedInvitationsLimitCsv(): void
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
            $this->invitations_csv,
            $vacancy->getKey(),
            $vacancy->interview_template_id,
            now()->addDays(5),
        );
    }

    /** @test  */
    public function itShouldReturnExceptionWhenExceedInvitationsLimitXlsx(): void
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
            $this->invitations_xlsx,
            $vacancy->getKey(),
            $vacancy->interview_template_id,
            now()->addDays(5),
        );
    }

    /** @test  */
    public function itShouldAddConsumptionWhenInvitationIsCreatedCsv(): void
    {
        $vacancy = Vacancy::factory()->for($this->employee->organization, 'organization')->createOne([
            'interview_template_id' => InterviewTemplate::factory()
                ->for($this->employee, 'creator')
                ->createOne()->getKey(),
        ]);

        $this->post(route('organization.invitations.import'), [
            'file' => $this->invitations_csv,
            'vacancy_id' => $vacancy->getKey(),
            'should_be_invited_at' => now()->addDays(5)->format('Y-m-d H:i'),
        ]);

        $this->assertEquals(
            $this->employee->refresh()->organization->interview_consumption,
            4
        );
    }

    /** @test  */
    public function itShouldAddConsumptionWhenInvitationIsCreatedXlsx(): void
    {
        $vacancy = Vacancy::factory()->for($this->employee->organization, 'organization')->createOne([
            'interview_template_id' => InterviewTemplate::factory()
                ->for($this->employee, 'creator')
                ->createOne()->getKey(),
        ]);

        $this->post(route('organization.invitations.import'), [
            'file' => $this->invitations_xlsx,
            'vacancy_id' => $vacancy->getKey(),
            'should_be_invited_at' => now()->addDays(5)->format('Y-m-d H:i'),
        ]);

        $this->assertEquals(
            $this->employee->refresh()->organization->interview_consumption,
            4
        );
    }

    /** @test  */
    public function itShouldNotAddConsumptionWhenEmailIsDuplicatedCsv(): void
    {
        $vacancy = Vacancy::factory()->for($this->employee->organization, 'organization')->createOne([
            'interview_template_id' => InterviewTemplate::factory()
                ->for($this->employee, 'creator')
                ->createOne()->getKey(),
        ]);

        $this->post(route('organization.invitations.import'), [
            'file' => $this->duplicate_emails_csv,
            'vacancy_id' => $vacancy->getKey(),
            'should_be_invited_at' => now()->addDays(5)->format('Y-m-d H:i'),
        ]);

        $this->assertEquals(
            $this->employee->refresh()->organization->interview_consumption,
            2
        );
    }

    /** @test  */
    public function itShouldNotAddConsumptionWhenEmailIsDuplicatedXlsx(): void
    {
        $vacancy = Vacancy::factory()->for($this->employee->organization, 'organization')->createOne([
            'interview_template_id' => InterviewTemplate::factory()
                ->for($this->employee, 'creator')
                ->createOne()->getKey(),
        ]);

        $this->post(route('organization.invitations.import'), [
            'file' => $this->duplicate_emails_xlsx,
            'vacancy_id' => $vacancy->getKey(),
            'should_be_invited_at' => now()->addDays(5)->format('Y-m-d H:i'),
        ]);

        $this->assertEquals(
            $this->employee->refresh()->organization->interview_consumption,
            2
        );
    }

    /** @test  */
    public function itShouldNotAddConsumptionWhenCountryCodeIsWrongCsv(): void
    {
        $vacancy = Vacancy::factory()->for($this->employee->organization, 'organization')->createOne([
            'interview_template_id' => InterviewTemplate::factory()
                ->for($this->employee, 'creator')
                ->createOne()->getKey(),
        ]);

        $this->post(route('organization.invitations.import'), [
            'file' => $this->wrong_cc_csv,
            'vacancy_id' => $vacancy->getKey(),
            'should_be_invited_at' => now()->addDays(5)->format('Y-m-d H:i'),
        ]);

        $this->assertEquals(
            $this->employee->refresh()->organization->interview_consumption,
            2
        );
    }

    /** @test  */
    public function itShouldNotAddConsumptionWhenCountryCodeIsWrongXlsx(): void
    {
        $vacancy = Vacancy::factory()->for($this->employee->organization, 'organization')->createOne([
            'interview_template_id' => InterviewTemplate::factory()
                ->for($this->employee, 'creator')
                ->createOne()->getKey(),
        ]);

        $this->post(route('organization.invitations.import'), [
            'file' => $this->wrong_cc_xlsx,
            'vacancy_id' => $vacancy->getKey(),
            'should_be_invited_at' => now()->addDays(5)->format('Y-m-d H:i'),
        ]);

        $this->assertEquals(
            $this->employee->refresh()->organization->interview_consumption,
            2
        );
    }
}
