<?php

namespace Database\Seeders;

use App\Models\Secret;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SecretSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Secret::create([
            'hash'=>Hash::make('Ez egy titok'),
            'secretText'=>'Ez egy titok',
            'createdAt'=>Carbon::now(),
            'expireAfter'=>Carbon::now()->addMinutes(5),
            'expireAfterViews'=>6
        ]);
    }
}
