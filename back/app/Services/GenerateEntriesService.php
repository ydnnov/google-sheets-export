<?php

namespace App\Services;

use App\Enums\Entry\Status;
use Faker\Generator;
use App\Models\Entry;

class GenerateEntriesService
{
    protected Generator $faker;

    public function __construct(Generator $faker)
    {
        $this->faker = $faker;
    }

    public function createEntries(int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            Entry::create([
                'status' => $this->faker->randomElement(
                    array_column(Status::cases(), 'value')
                ),
                'content' => $this->faker->sentence(),
            ]);
        }
    }
}
