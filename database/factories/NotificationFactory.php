<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Proposal;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['info', 'success', 'warning', 'danger', 'primary'];
        $userTypes = ['mahasiswa', 'dosen', 'reviewer', 'operator'];
        
        return [
            'user_identifier' => $this->faker->unique()->numerify('########'),
            'user_type' => $this->faker->randomElement($userTypes),
            'title' => $this->faker->sentence(3),
            'message' => $this->faker->paragraph(2),
            'type' => $this->faker->randomElement($types),
            'data' => [
                'action_url' => $this->faker->url(),
                'action_text' => $this->faker->word(),
            ],
            'proposal_id' => Proposal::factory(),
            'read_at' => $this->faker->optional(0.3)->dateTimeBetween('-1 week', 'now'),
        ];
    }

    /**
     * Indicate that the notification is unread.
     */
    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'read_at' => null,
        ]);
    }

    /**
     * Indicate that the notification is read.
     */
    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'read_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    /**
     * Indicate that the notification is for mahasiswa.
     */
    public function forMahasiswa(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'mahasiswa',
        ]);
    }

    /**
     * Indicate that the notification is for dosen.
     */
    public function forDosen(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'dosen',
        ]);
    }

    /**
     * Indicate that the notification is for reviewer.
     */
    public function forReviewer(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'reviewer',
        ]);
    }

    /**
     * Indicate that the notification is for operator.
     */
    public function forOperator(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'operator',
        ]);
    }

    /**
     * Indicate that the notification is of info type.
     */
    public function info(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'info',
        ]);
    }

    /**
     * Indicate that the notification is of success type.
     */
    public function success(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'success',
        ]);
    }

    /**
     * Indicate that the notification is of warning type.
     */
    public function warning(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'warning',
        ]);
    }

    /**
     * Indicate that the notification is of danger type.
     */
    public function danger(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'danger',
        ]);
    }
}
