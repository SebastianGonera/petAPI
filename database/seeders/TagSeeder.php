<?php

namespace Database\Seeders;

use App\Models\Tag as PetTag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            ['name' => 'friendly'],
            ['name' => 'shy'],
            ['name' => 'playful'],
        ];

        foreach ($tags as $tag) {
            PetTag::create($tag);
        }
    }
}
