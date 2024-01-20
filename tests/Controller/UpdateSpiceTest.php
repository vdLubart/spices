<?php

namespace App\Tests\Controller;

use App\Model\Spice;

class UpdateSpiceTest extends SpiceController
{
    protected Spice $spice;

    public function setUp():void
    {
        parent::setUp();

        $this->spice = new Spice();
        $this->spice->name = $this->faker->word;
        $this->spice->status = 'running out';
        $this->spice->save();
    }

    /** @test - successfully update a spice */
    function successfully_update_a_spice() {
        $_POST = [
            'id' => $this->spice->id,
            'name' => $name = $this->faker->word,
            'status' => $status = 'full'
        ];

        $this->client->request('PUT', '/spice', $_POST, [], [
            'HTTP_Authorization' => 'Bearer ' . $this->accessToken
        ]);
        $responseContent = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertEquals($this->spice->id, $responseContent->id);
        $this->assertEquals($name, $responseContent->name);
        $this->assertEquals($status, $responseContent->status);
        $this->assertNotEmpty($responseContent->created_at);
        $this->assertNotEmpty($responseContent->updated_at);

        $spice = Spice::first();
        $this->assertEquals($this->spice->id, $spice->id);
        $this->assertEquals($name, $spice->name);
        $this->assertEquals($status, $spice->status);
    }

    /** @test - cannot update spice without id */
    function cannot_update_spice_without_id() {
        $_POST = [
            'name' => $this->faker->word,
            'status' => 'full'
        ];

        $this->client->request('PUT', '/spice', $_POST, [], [
            'HTTP_Authorization' => 'Bearer ' . $this->accessToken
        ]);
        $responseContent = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseStatusCodeSame(422);
        $this->assertEquals('Validation failed', $responseContent->message);
        $this->assertCount(1, $responseContent->errors);
        $this->assertEquals('id', $responseContent->errors[0]->property);
        $this->assertEquals('This value should not be blank.', $responseContent->errors[0]->message);
    }

    /** @test - cannot update spice if one of parameters is missed */
    function cannot_update_spice_if_one_of_parameters_is_missed() {
        $_POST = [
            'id' => $this->spice->id,
            'name' => $this->faker->word
        ];

        $this->client->request('PUT', '/spice', $_POST, [], [
            'HTTP_Authorization' => 'Bearer ' . $this->accessToken
        ]);
        $responseContent = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseStatusCodeSame(422);
        $this->assertEquals('Validation failed', $responseContent->message);
        $this->assertCount(1, $responseContent->errors);
        $this->assertEquals('status', $responseContent->errors[0]->property);
        $this->assertEquals('This value should not be blank.', $responseContent->errors[0]->message);
    }

    /** @test - cannot update spice if id does not exist */
    function cannot_update_spice_if_id_does_not_exist() {
        $_POST = [
            'id' => $this->spice->id + 1,
            'name' => $this->faker->word,
            'status' => 'full'
        ];

        $this->client->request('PUT', '/spice', $_POST, [], [
            'HTTP_Authorization' => 'Bearer ' . $this->accessToken
        ]);
        $responseContent = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseStatusCodeSame(422);
        $this->assertEquals('Validation failed', $responseContent->message);
        $this->assertCount(1, $responseContent->errors);
        $this->assertEquals('id', $responseContent->errors[0]->property);
        $this->assertEquals('The spice with id 2 does not exist.', $responseContent->errors[0]->message);
    }

    /** @test - cannot update spice if status value is wrong */
    function cannot_update_spice_if_status_value_is_wrong() {
        $_POST = [
            'id' => $this->spice->id,
            'name' => $this->faker->word,
            'status' => $this->faker->word,
        ];

        $this->client->request('PUT', '/spice', $_POST, [], [
            'HTTP_Authorization' => 'Bearer ' . $this->accessToken
        ]);
        $responseContent = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseStatusCodeSame(422);
        $this->assertEquals('Validation failed', $responseContent->message);
        $this->assertCount(1, $responseContent->errors);
        $this->assertEquals('status', $responseContent->errors[0]->property);
        $this->assertEquals('The value you selected is not a valid choice.', $responseContent->errors[0]->message);
    }
}
