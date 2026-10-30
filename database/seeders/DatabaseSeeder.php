<?php

namespace Database\Seeders;

use App\Models\Note;
use App\Models\Tag;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $tags = Tag::factory()->count(5)->sequence(
            ['name' => 'travail'],
            ['name' => 'perso'],
            ['name' => 'courses'],
            ['name' => 'idées'],
            ['name' => 'lectures'],
        )->create();

        Note::factory()->count(10)->recycle($user)->recycle($tags)->create();
    }
}
