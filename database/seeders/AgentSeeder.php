<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AgentSeeder extends Seeder
{
    public function run(): void
    {
        $agents = [
            [
                'name'     => 'Ahmad Faiz',
                'email'    => 'ahmad.faiz@capbay.com',
                'password' => Hash::make('agent123'),
                'role'     => 'agent',
            ],
            [
                'name'     => 'Nurul Aina',
                'email'    => 'nurul.aina@capbay.com',
                'password' => Hash::make('agent123'),
                'role'     => 'agent',
            ],
            [
                'name'     => 'Raj Kumar',
                'email'    => 'raj.kumar@capbay.com',
                'password' => Hash::make('agent123'),
                'role'     => 'agent',
            ],
        ];

        foreach ($agents as $agent) {
            User::updateOrCreate(
                ['email' => $agent['email']],
                $agent
            );
        }
    }
}
