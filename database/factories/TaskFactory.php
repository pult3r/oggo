<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Filament\Enums\TaskStatus;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('next Monday', 'next Monday +'.rand(0,6).' days');
        $endDate = $this->faker->dateTimeBetween($startDate, $startDate->format('Y-m-d').' +'.rand(1,10).' days');

        $statuses = TaskStatus::cases() ; 

        return [
            'id' => Str::uuid()->toString(),
            'name' => $this->faker->company(),
            'description' => $this->faker->sentence(50),
            'startdate' => $startDate,
            'enddate' => $endDate,
            'status' => $statuses[rand(0, count($statuses)-1)]->name
        ];
    }
}
