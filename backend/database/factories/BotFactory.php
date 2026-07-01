<?php

namespace Database\Factories;

use App\Models\Bot;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Bot>
 */
class BotFactory extends Factory
{
    protected $model = Bot::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true) . ' Bot';

        return [
            'workspace_id' => Workspace::factory(),
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(1000, 9999),
            'description' => fake()->optional()->sentence(),
            'system_prompt' => fake()->optional()->sentence(),
            'model' => Bot::DEFAULT_MODEL,
            'status' => 'draft',
        ];
    }
}
