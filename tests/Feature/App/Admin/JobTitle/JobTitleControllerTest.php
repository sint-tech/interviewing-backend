<?php

namespace Tests\Feature\App\Admin\JobTitle;

use Domain\JobTitle\Models\JobTitle;
use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Feature\Traits\AuthenticationInstallation;
use Tests\TestCase;

class JobTitleControllerTest extends TestCase
{
    use DatabaseMigrations,AuthenticationInstallation,WithFaker;

    protected User $sintUser;

    const INDEX = 'admin.job-titles.index';

    const SHOW = 'admin.job-titles.show';

    const STORE = 'admin.job-titles.store';

    const UPDATE = 'admin.job-titles.update';

    const DESTROY = 'admin.job-titles.destroy';

    public function setUp(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();

        $this->installPassport();

        Artisan::call('db:seed', [
            '--class' => 'SintAdminsSeeder',
        ]);

        $this->sintUser = User::query()->first();

        $this->actingAs($this->sintUser, 'api');
    }

    /** @test  */
    public function itShouldRetrieveJobTitles()
    {
        JobTitle::factory(25)->create();

        $this->get(route(static::INDEX))
            ->assertSuccessful()
            ->assertJsonCount(25, 'data');
    }

    /** @test  */
    public function itShouldShowSingleJobTitle()
    {
        $this->get(route(static::SHOW, [100]))->assertNotFound();

        JobTitle::factory(1)->create();

        $this->get(route(static::SHOW, [1]))
            ->assertSuccessful()
            ->assertJson(function (AssertableJson $json) {
                return $json->first(function (AssertableJson $data) {
                    return $data->hasAll(['id', 'title', 'description', 'availability_status', 'created_at']);
                });
            });
    }

    /** @test  */
    public function itShouldStoreJobTitle()
    {
        $this->assertDatabaseEmpty(JobTitle::class);

        $this->post(route(static::STORE), [
            'title' => 'this is title',
            'description' => $this->faker->text(255),
            'availability_status' => $this->faker->randomElement(['active', 'inactive']),
        ])
            ->assertSuccessful()
            ->assertJson(function (AssertableJson $json) {
                return $json->where('data.id', 1)
                    ->where('data.title', 'this is title')
                    ->etc();
            });
    }

    /** @test */
    public function itShouldUpdateJobTitle()
    {
        $jobTitle = JobTitle::factory()->createOne();

        $this->put(route(static::UPDATE, $jobTitle), [
            'title' => 'updated title',
        ])->assertSuccessful()->assertJson(function (AssertableJson $json) {
            return $json->where('data.title', 'updated title');
        });
    }
}
