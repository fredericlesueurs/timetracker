<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Client;
use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = new User([
            'email' => 'frederic.lesueurs@gmail.com',
            'password' => Hash::make('admin@123'),
            'name' => 'Frédéric Lesueurs',
        ]);
        $user->email_verified_at = now();
        $user->remember_token = Str::random(10);

        $user->save();

        Client::factory()
            ->count(20)
            ->has(
                Project::factory()
                    ->count(2)
                    ->has(
                        Task::factory()
                            ->count(3),
                    ),
            )
            ->has(Comment::factory()->count(2))
            ->create();

        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
