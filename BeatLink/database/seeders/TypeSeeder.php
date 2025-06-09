<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Type;

class TypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = ['lil uzi vert', 'kanye west', 'travis scott', 'bob marley', 'queen', 'marylin manson', 'metallica', 'lady gaga'];

        foreach ($types as $type) {
            Type::firstOrCreate(['name' => strtolower($type)]);
        }
    }
}
