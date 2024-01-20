<?php

namespace App\Tests\Controller;

use App\Model\Spice;

class AddSpiceTest extends SpiceController {
    /** @test - add a new spice to the database */
    function add_a_new_spice_to_the_database() {
        $_POST = [
            'name' => $name = $this->faker->word,
            'status' => $status = 'full'
        ];
        $this->client->request('POST', '/spice', $_POST, [], [
            'HTTP_Authorization' => 'Bearer ' . $this->accessToken
        ]);
        $responseContent = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(201);
        $this->assertEquals($name, $responseContent->name);
        $this->assertEquals($status, $responseContent->status);
        $this->assertNotEmpty($responseContent->id);
        $this->assertNotEmpty($responseContent->created_at);
        $this->assertNotEmpty($responseContent->updated_at);

        $spice = Spice::first();
        $this->assertEquals($name, $spice->name);
        $this->assertEquals($status, $spice->status);
    }

    /** @test - cannot create a new spice without name */
    function cannot_create_a_new_spice_without_name() {
        $_POST = [
            'status' => 'full'
        ];
        $this->client->request('POST', '/spice', $_POST, [], [
            'HTTP_Authorization' => 'Bearer ' . $this->accessToken
        ]);
        $responseContent = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseStatusCodeSame(422);

        $spice = Spice::all();
        $this->assertCount(0, $spice);
        $this->assertEquals('Validation failed', $responseContent->message);
        $this->assertCount(1, $responseContent->errors);
        $this->assertEquals('name', $responseContent->errors[0]->property);
    }

    /** @test - cannot create a new spice without status */
    function cannot_create_a_new_spice_without_status() {
        $_POST = [
            'name' => $this->faker->name
        ];
        $this->client->request('POST', '/spice', $_POST, [], [
            'HTTP_Authorization' => 'Bearer ' . $this->accessToken
        ]);
        $responseContent = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseStatusCodeSame(422);

        $spice = Spice::all();
        $this->assertCount(0, $spice);
        $this->assertEquals('Validation failed', $responseContent->message);
        $this->assertCount(1, $responseContent->errors);
        $this->assertEquals('status', $responseContent->errors[0]->property);
    }

    /** @test - multiple validation error messages are available */
    function multiple_validation_error_messages_are_available() {
        $_POST = [];
        $this->client->request('POST', '/spice', $_POST, [], [
            'HTTP_Authorization' => 'Bearer ' . $this->accessToken
        ]);
        $responseContent = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseStatusCodeSame(422);

        $spice = Spice::all();
        $this->assertCount(0, $spice);
        $this->assertEquals('Validation failed', $responseContent->message);
        $this->assertCount(2, $responseContent->errors);
    }

    /** @test - cannot create a new spice if provided status is wrong */
    function cannot_create_a_new_spice_if_provided_status_is_wrong() {
        $_POST = [
            'name' => $this->faker->name,
            'status' => $this->faker->name
        ];
        $this->client->request('POST', '/spice', $_POST, [], [
            'HTTP_Authorization' => 'Bearer ' . $this->accessToken
        ]);
        $responseContent = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseStatusCodeSame(422);

        $spice = Spice::all();
        $this->assertCount(0, $spice);
        $this->assertEquals('Validation failed', $responseContent->message);
        $this->assertCount(1, $responseContent->errors);
        $this->assertEquals('status', $responseContent->errors[0]->property);
        $this->assertEquals('The value you selected is not a valid choice.', $responseContent->errors[0]->message);
    }
}
