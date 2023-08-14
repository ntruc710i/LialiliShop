<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition()
    {
        
        $title = $this->faker->sentence;
        $slug = Str::slug($title);
        return [
            "title" => $title,
            "category_id" => $this->faker->numberBetween($min=1, $max=4),
            "slug" => $slug,
            "price" => $this->faker->numberBetween($min=10000, $max=70000),
            "description" => $this->faker->text(200),
            "image" => $this->faker->imageUrl($width = 640, $height = 480),
            "rate" => $this->faker->randomFloat($nbMaxDecimals = NULL, $min = 0, $max = 10),
        ];
    }
}
