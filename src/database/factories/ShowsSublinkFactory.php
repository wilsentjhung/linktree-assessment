<?php

namespace Database\Factories;

use App\Models\ShowsSublink;

use Illuminate\Database\Eloquent\Factories\Factory;

class ShowsSublinkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'url' => $this->faker->url(),
            'status' => $this->faker->randomElement(ShowsSublink::STATUS_LIST),
            'date' => $this->faker->date(),
            'venue' => $this->faker->name(),
        ];
    }
}
