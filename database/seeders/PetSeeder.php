<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Pet;
use App\Models\Tag as PetTag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pets=[
            [
                'name'=>'Ala',
                'category_id'=>Category::inRandomOrder()->first()->id,
                'photoUrls'=>json_encode('https://picsum.photos/300.jpg'),
                'status'=>'pending',
            ],
            [
                'name'=>'Ola',
                'category_id'=>Category::inRandomOrder()->first()->id,
                'photoUrls'=>json_encode('https://picsum.photos/200.jpg'),
                'status'=>'sold',
            ],
            [
                'name'=>'Bala',
                'category_id'=>Category::inRandomOrder()->first()->id,
                'photoUrls'=>json_encode('https://picsum.photos/400.jpg'),
                'status'=>'available',
            ],
        ];

        foreach($pets as $petData){
            $pet=Pet::create($petData);
            $tags=PetTag::inRandomOrder()->take(1)->get();
            $pet->tags()->attach($tags);
        }
    }
}
