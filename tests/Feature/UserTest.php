<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testRegisterSuccess(): void
    {
        $this->post('/api/users', [
            'username' => 'john',
            'password' => 'j0hn_secret',
            'name' => 'John Doe'
        ])->assertStatus(201)
            ->assertJson([
                'data' => [
                    'username' => 'john',
                    'name' => 'John Doe',
                ]
            ]);
    }

    public function testRegisterFailed(): void
    {
        $this->post('/api/users', [
            'username' => '',
            'password' => '',
            'name' => ''
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'username' => [
                        'The username field is required.'
                    ],
                    'name' => [
                        'The name field is required.'
                    ],
                    'password' => [
                        'The password field is required.'
                    ]
                ]
            ]);
    }

    public function testRegisterUsernameAlreadyExists(): void
    {
        $this->testRegisterSuccess();

        $this->post('/api/users', [
            'username' => 'john',
            'password' => 'j0hn_secret',
            'name' => 'John Doe'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    "username" => [
                        "Username already registered"
                    ]
                ]
            ]);
    }
}
