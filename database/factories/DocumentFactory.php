<?php

namespace Database\Factories;

use App\Models\Tag;
use App\Models\User;
use App\Enum\Language;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'author'     => fake()->name(),
            'title'      => fake()->sentence(),
            'pages'      => fake()->numberBetween(1, 500),
            'language'   => fake()->randomElement(Language::values()),
            'visibility' => fake()->randomElement(['public', 'private']),
            'path'       => fake()->filePath(),
            'size'       => fake()->numberBetween(100, 5000),
            'user_id'    => User::inRandomOrder()->first()->id
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function ($document) {
            if (Tag::count() === 0) {
                Tag::factory()->count(10)->create();
            }

            $tags = Tag::inRandomOrder()->limit(rand(1, 5))->pluck('id');
            $document->tags()->attach($tags);
        });
    }
}
