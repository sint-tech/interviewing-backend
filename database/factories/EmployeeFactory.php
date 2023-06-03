<?php

namespace Database\Factories;

use Domain\Organization\Models\Employee;
use Domain\Organization\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->email,
            'password' => Hash::make('password'),
            'is_organization_manager' => $this->faker->boolean(90),
            'organization_id' => Organization::factory()->create()->getKey(),
        ];
    }
}
