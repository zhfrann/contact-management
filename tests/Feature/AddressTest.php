<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddressTest extends TestCase
{
    public function testCreateSuccess(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->post("/api/contacts/$contact->id/addresses", [
            // 'street' => 'test street',
            'city' => 'Konawe',
            // 'province' => 'South Sulawesi',
            'country' => 'Indonesia',
            'postal_code' => '64312',
        ], [
            'Authorization' => 'test'
        ])->assertStatus(201)
            ->assertJson([
                'data' => [
                    'street' => null,
                    'city' => 'Konawe',
                    'province' => '',
                    'country' => 'Indonesia',
                    'postal_code' => '64312',
                ]
            ]);
    }

    public function testCreateFailed(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->post("/api/contacts/$contact->id/addresses", [
            'street' => 'test street',
            'city' => 'Konawe',
            'province' => 'South Sulawesi',
            // 'country' => 'Indonesia',
            'postal_code' => '64312',
        ], [
            'Authorization' => 'test'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'country' => [
                        'The country field is required.'
                    ]
                ]
            ]);
    }

    public function testCreateContactNotFound(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->post("/api/contacts/" . ($contact->id + 1) . "/addresses", [
            // 'street' => 'test street',
            'city' => 'Konawe',
            // 'province' => 'South Sulawesi',
            'country' => 'Indonesia',
            'postal_code' => '64312',
        ], [
            'Authorization' => 'test'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Contact not found'
                    ]
                ]
            ]);
    }
}
