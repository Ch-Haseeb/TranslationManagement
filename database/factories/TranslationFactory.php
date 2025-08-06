<?php

namespace Database\Factories;

use App\Models\Translation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Translation>
 */
class TranslationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Translation::class;

    public function definition(): array
    {
        $locales = ['en', 'fr', 'es', 'de', 'it'];
        $tags = ['web', 'mobile', 'desktop', 'email', 'sms'];

        return [
            'key' => $this->faker->words(3, true) . '.' . $this->faker->word(),
            'locale' => $this->faker->randomElement($locales),
            'content' => $this->faker->sentence(10),
            'tags' => $this->faker->randomElements($tags, rand(1, 3)),
        ];
    }
}
