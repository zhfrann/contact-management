<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactSeeder;
use Database\Seeders\SearchSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContactTest extends TestCase
{
    public function testCreateSuccess(): void
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/contacts', [
            'first_name' => 'john',
            'last_name' => 'doe',
            'email' => 'johndoe@gmail.com',
            'phone' => '+6281122223333'
        ], [
            'Authorization' => 'test'
        ])->assertStatus(201)
            ->assertJson([
                'data' => [
                    'first_name' => 'john',
                    'last_name' => 'doe',
                    'email' => 'johndoe@gmail.com',
                    'phone' => '+6281122223333'
                ]
            ]);
    }

    public function testCreateFailed(): void
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/contacts', [
            'first_name' => '',
            'last_name' => 'doe',
            'email' => 'wrong email format',
            'phone' => '+6281122223333'
        ], [
            'Authorization' => 'test'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'first_name' => [
                        "The first name field is required."
                    ],
                    'email' => [
                        "The email field must be a valid email address."
                    ]
                ]
            ]);
    }

    public function testCreateUnauthorized(): void
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/contacts', [
            'first_name' => 'john',
            'last_name' => 'doe',
            'email' => 'johndoe@gmail.com',
            'phone' => '+6281122223333'
        ], [
            'Authorization' => 'wrong token'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Unauthorized'
                    ]
                ]
            ]);
    }

    public function testGetSuccess(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->get("/api/contacts/$contact->id", [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'first_name' => 'test',
                    'last_name' => 'test',
                    'email' => 'test@gmail.com',
                    'phone' => '12345'
                ]
            ]);
    }

    public function testGetNotFound(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->get("/api/contacts/" . ($contact->id + 1), [
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

    public function testGetOtherUserContact(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->get("/api/contacts/$contact->id", [
            'Authorization' => 'test2'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Contact not found'
                    ]
                ]
            ]);
    }

    public function testUpdateSuccess(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->put("api/contacts/$contact->id", [
            'first_name' => 'new first name',
            'last_name' => 'new last name',
            'email' => 'newemail@gmail.com',
            'phone' => '987654321',
        ], [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'first_name' => 'new first name',
                    'last_name' => 'new last name',
                    'email' => 'newemail@gmail.com',
                    'phone' => '987654321',
                ]
            ]);
    }

    public function testUpdateValidationError(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->put("api/contacts/$contact->id", [
            'first_name' => '',
            'last_name' => 'new last name',
            'email' => 'newemail@gmail.com',
            'phone' => '987654321',
        ], [
            'Authorization' => 'test'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'first_name' => [
                        'The first name field is required.'
                    ]
                ]
            ]);
    }

    public function testUpdateUnauthorized(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->put("api/contacts/$contact->id", [
            'first_name' => 'new first name',
            'last_name' => 'new last name',
            'email' => 'newemail@gmail.com',
            'phone' => '987654321',
        ], [
            'Authorization' => 'wrong token'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Unauthorized'
                    ]
                ]
            ]);
    }

    public function testUpdateOtherUserContact(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->put("api/contacts/" . ($contact->id), [
            'first_name' => 'new first name',
            'last_name' => 'new last name',
            'email' => 'newemail@gmail.com',
            'phone' => '987654321',
        ], [
            'Authorization' => 'test2'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Contact not found'
                    ]
                ]
            ]);
    }

    public function testDeleteSuccess(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->delete("api/contacts/$contact->id", [], [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => true
            ]);
    }

    public function testDeleteNotFound(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->delete("api/contacts/" . ($contact->id + 1), [], [
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

    public function testSearchByFirstName(): void
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?name=first', [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->json();

        self::assertEquals(15, count($response['data']));  //data per page
        self::assertEquals(20, $response['meta']['total']);  //total data
    }

    public function testSearchByLastName(): void
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?name=last', [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->json();

        self::assertEquals(15, count($response['data']));  //data per page
        self::assertEquals(20, $response['meta']['total']);  //total data
    }

    public function testSearchByEmail(): void
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?email=test', [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->json();

        self::assertEquals(15, count($response['data']));  //data per page
        self::assertEquals(20, $response['meta']['total']);  //total data
    }

    public function testSearchByPhone(): void
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?phone=1111', [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->json();

        self::assertEquals(15, count($response['data']));  //data per page
        self::assertEquals(20, $response['meta']['total']);  //total data
    }

    public function testSearchNotFound(): void
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?email=wrongEmail', [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->json();

        self::assertEquals(0, count($response['data']));  //data per page
        self::assertEquals(0, $response['meta']['total']);  //total data
    }

    public function testSearchWithPage(): void
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?page=2', [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->json();

        self::assertEquals(5, count($response['data']));  //data per page
        self::assertEquals(20, $response['meta']['total']);  //total data
        self::assertEquals(2, $response['meta']['current_page']);  //total data
    }

    public function testSearchWithSizeAndPage(): void
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?size=8&page=2', [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->json();

        self::assertEquals(8, count($response['data']));  //data per page
        self::assertEquals(20, $response['meta']['total']);  //total data
        self::assertEquals(2, $response['meta']['current_page']);  //total data
    }
}
