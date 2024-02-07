<?php

namespace Tests\Feature\App\Organization\InvitationManagement;

use App\Admin\InvitationManagement\Jobs\ImportInvitationsFromExcelJob;
use Database\Seeders\SintAdminsSeeder;
use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\Organization\Models\Employee;
use Domain\Vacancy\Models\Vacancy;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class ImportInvitationsControllerTest extends TestCase
{
    use DatabaseMigrations,WithFaker;

    protected Employee $employee;

    protected UploadedFile $excelFile;

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

        $this->actingAs($this->employee, 'organization');
    }

    /** @test  */
    public function itShouldCallImportInvitationsFromExcelJobWhenHitEndpoint(): void
    {
        Bus::fake();

        $vacancy = Vacancy::factory()->createOne([
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
}
