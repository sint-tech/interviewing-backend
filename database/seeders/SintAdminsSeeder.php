<?php

namespace Database\Seeders;

use Domain\Users\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SintAdminsSeeder extends Seeder
{
    protected array $emails = [
        'ahmed.badawy@sint.com',
        'mohammed.badawy@sint.com',
        'mostafa.gouda@sint.com',
        'mohamed.mokbel@sint.com',
        'muaz.soliman@sint.com',
        'mohamed.ghannam@sint.com',
    ];

    protected string $defaultPassword = 'password';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->emails as $email) {
            User::query()->firstOrCreate(['email' => strtolower($email)], [
                'name' => Str::of($email)->remove('@sint.com')->replace('.', ' ')->ucfirst(),
                'password' => Hash::make($this->defaultPassword),
            ]);
        }
    }
}
