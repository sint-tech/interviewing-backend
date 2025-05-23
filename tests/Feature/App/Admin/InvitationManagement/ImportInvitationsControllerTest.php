<?php

namespace Tests\Feature\App\Admin\InvitationManagement;

use App\Admin\InvitationManagement\Jobs\ImportInvitationsFromExcelJob;
use Database\Seeders\SintAdminsSeeder;
use Domain\Invitation\Models\Invitation;
use Domain\Users\Models\User;
use Domain\Vacancy\Models\Vacancy;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ImportInvitationsControllerTest extends TestCase
{
    use RefreshDatabase,WithFaker;

    protected User $sintUser;

    protected UploadedFile $excelFile;

    public function setUp(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();

        $this->seed(SintAdminsSeeder::class);

        $this->sintUser = User::query()->first();

        $this->excelFile = (new UploadedFile(
            public_path('tests/csvs/invitations.csv'),
            'invitations.csv',
            'csv',
            null,
            true
        ));
    }

    /** @test  */
    public function itShouldCallImportInvitationsFromExcelJobWhenHitEndpoint(): void
    {
        Bus::fake();

        $vacancy = Vacancy::factory()->createOne();

        $this->actingAs($this->sintUser, 'admin')->post(route('admin.invitations.import'), [
            'file' => $this->excelFile,
            'vacancy_id' => $vacancy->getKey(),
            'interview_template_id' => $vacancy->interviewTemplate->getKey(),
            'should_be_invited_at' => now()->addDays(5)->format('Y-m-d H:i'),
        ])->assertSuccessful();

        Bus::assertDispatched(ImportInvitationsFromExcelJob::class);
    }

    /** @test  */
    public function itShouldCreateInvitations(): void
    {
        $vacancy = Vacancy::factory()->createOne();

        $this->assertCount(0, Invitation::query()->get());

        $this->actingAs($this->sintUser, 'admin')->post(route('admin.invitations.import'), [
            'file' => $this->excelFile,
            'vacancy_id' => $vacancy->getKey(),
            'interview_template_id' => $vacancy->interviewTemplate->getKey(),
            'should_be_invited_at' => now()->addDays(5)->format('Y-m-d H:i'),
        ])->assertSuccessful();

        $this->assertCount(4, Invitation::query()->get());
    }
}
