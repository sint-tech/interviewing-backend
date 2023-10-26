<?php

namespace Tests\Feature\App\Admin\Vacancy;

use Domain\Organization\Models\Organization;
use Domain\Users\Models\User;
use Domain\Vacancy\Models\Vacancy;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\Feature\Traits\AuthenticationInstallation;
use Tests\TestCase;

class VacancyControllerTest extends TestCase
{
    use DatabaseMigrations,AuthenticationInstallation,WithFaker;

    const INDEX_ROUTE_NAME = 'admin.vacancy.index';
    const SHOW_ROUTE_NAME = 'admin.vacancy.show';
    const STORE_ROUTE_NAME = 'admin.vacancy.store';
    const UPDATE_ROUTE_NAME = 'admin.vacancy.update';

    const DELETE_ROUTE_NAME = 'admin.vacancy.destroy';

    public User $sintUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();

        $this->installPassport();

        Artisan::call('db:seed', [
            '--class' => 'SintAdminsSeeder',
        ]);

        $this->sintUser = User::query()->first();
    }

    /** @test  */
    public function itShouldShowVacancies(): void
    {
        Vacancy::factory(15)->for($this->sintUser,'creator')->create();

        $this->actingAs($this->sintUser,'api')
            ->get(route(self::INDEX_ROUTE_NAME))
            ->assertSuccessful()
            ->assertJsonCount(15,'data');
    }

    /** @test  */
    public function itShouldShowAllOrganizationsVacancies(): void
    {
        Vacancy::factory(5)->for($this->sintUser,'creator')->create(['organization_id' => Organization::factory()->create()->getKey()]);
        Vacancy::factory(5)->for($this->sintUser,'creator')->create(['organization_id' => Organization::factory()->create()->getKey()]);
        Vacancy::factory(5)->for($this->sintUser,'creator')->create(['organization_id' => Organization::factory()->create()->getKey()]);

        $this->actingAs($this->sintUser,'api')
            ->get(route(self::INDEX_ROUTE_NAME))
            ->assertSuccessful()
            ->assertJsonCount(15,'data');
    }

    /** @test */
    public function itShouldShowAllVacanciesForOrganizationsAndSint(): void
    {
        Vacancy::factory(5)->for($this->sintUser,'creator')->create(['organization_id' => Organization::factory()->create()->getKey()]);
        Vacancy::factory(5)->for($this->sintUser,'creator')->create(['organization_id' => Organization::factory()->create()->getKey()]);
        Vacancy::factory(5)->for($this->sintUser,'creator')->create(['organization_id' => null]);

        $this->actingAs($this->sintUser,'api')
            ->get(route(self::INDEX_ROUTE_NAME))
            ->assertSuccessful()
            ->assertJsonCount(15,'data');
    }

    /** @test */
    public function itShouldShowVacancy(): void
    {
        $vacancy = Vacancy::factory()->for($this->sintUser,'creator')->create();

        $this->actingAs($this->sintUser,'api')
            ->get(route(self::SHOW_ROUTE_NAME,$vacancy))
            ->assertSuccessful();
    }

    /** @test  */
    public function itShouldShowOrganizationVacancy():void
    {
        $vacancy = Vacancy::factory()->for($this->sintUser,'creator')->create(['organization_id' => Organization::factory()->createOne()->getKey()]);

        $this->actingAs($this->sintUser,'api')
            ->get(route(self::SHOW_ROUTE_NAME,$vacancy))
            ->assertSuccessful();
    }

    /** @test  */
    public function itShouldShowSintVacancy(): void
    {
        $vacancy = Vacancy::factory()->for($this->sintUser,'creator')->create();

        $this->actingAs($this->sintUser,'api')
            ->get(route(self::SHOW_ROUTE_NAME,$vacancy))
            ->assertSuccessful();
    }

    /** @test  */
    public function itShouldStoreVacancy(): void
    {
        $data = $this->vacancyData();

        $this->actingAs($this->sintUser,'api')
            ->post(route(static::STORE_ROUTE_NAME),$data)
            ->assertSuccessful();
    }

    /** @test  */
    public function itShouldDeleteVacancy(): void
    {
        $vacancy = Vacancy::factory(5)->for($this->sintUser,'creator')->create();

        $this->assertCount(5,Vacancy::query()->get());

        $this->actingAs($this->sintUser,'api')
            ->delete(route(static::DELETE_ROUTE_NAME,$vacancy->first()));

        $this->assertCount(4,Vacancy::query()->get());
    }

    protected function vacancyData(): array
    {
        return [
            'title' => $this->faker->text(100),
            'description' => $this->faker->text(1000),
            'started_at' => now()->addDay()->format('Y-m-d H:m'),
            'ended_at' => now()->addDays(3)->format('Y-m-d H:m'),
            'max_reconnection_tries' => $this->faker->numberBetween(1,5),
            'organization_id' => Organization::factory()->createOne()->getKey(),
            'open_positions' => $this->faker->numberBetween(1,10)
        ];
    }
}
