<?php

namespace App\Tests\Controller;

use App\Model\Spice;
use Illuminate\Database\Eloquent\Model;

class PatchSpiceTest extends SpiceController
{
    protected Spice $spice;

    public function setUp():void
    {
        parent::setUp();

        $this->spice = new Spice();
        $this->spice->name = $this->faker->word;
        $this->spice->status = 'full';
        $this->spice->save();
    }

    /** @test - successfully patch spices name */
    function successfully_patch_spices_name() {
        $_POST = [
            'id' => $this->spice->id,
            'name' => $name = $this->faker->word
        ];
        $this->client->request('PATCH', '/spice', $_POST, [], [
            'HTTP_Authorization' => 'Bearer ' . $this->accessToken
        ]);
        $responseContent = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertEquals($this->spice->id, $responseContent->id);
        $this->assertEquals($name, $responseContent->name);
        $this->assertEquals($this->spice->status, $responseContent->status);
        $this->assertNotEmpty($responseContent->created_at);
        $this->assertNotEmpty($responseContent->updated_at);

        $spice = Spice::first();
        $this->assertEquals($this->spice->id, $spice->id);
        $this->assertEquals($name, $spice->name);
        $this->assertEquals($this->spice->status, $spice->status);
    }

    /** @test - successfully patch spice status */
    function successfully_patch_spice_status() {
        $_POST = [
            'id' => $this->spice->id,
            'status' => $status = 'out of stock'
        ];
        $this->client->request('PATCH', '/spice', $_POST, [], [
            'HTTP_Authorization' => 'Bearer ' . $this->accessToken
        ]);
        $responseContent = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertEquals($this->spice->id, $responseContent->id);
        $this->assertEquals($this->spice->name, $responseContent->name);
        $this->assertEquals($status, $responseContent->status);
        $this->assertNotEmpty($responseContent->created_at);
        $this->assertNotEmpty($responseContent->updated_at);

        $spice = Spice::first();
        $this->assertEquals($this->spice->id, $spice->id);
        $this->assertEquals($this->spice->name, $spice->name);
        $this->assertEquals($status, $spice->status);
    }

    /** @test - cannot patch spice without id */
    function cannot_patch_spice_without_id() {
        $_POST = [
            'name' => $this->faker->word
        ];
        $this->client->request('PATCH', '/spice', $_POST, [], [
            'HTTP_Authorization' => 'Bearer ' . $this->accessToken
        ]);
        $responseContent = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseStatusCodeSame(422);
        $this->assertEquals('Validation failed', $responseContent->message);
        $this->assertCount(1, $responseContent->errors);
        $this->assertEquals('id', $responseContent->errors[0]->property);
        $this->assertEquals('This value should not be blank.', $responseContent->errors[0]->message);
    }

    /** @test - cannot patch spice if status is wrong */
    function cannot_patch_spice_if_status_is_wrong() {
        $_POST = [
            'id' => $this->spice->id,
            'status' => $this->faker->word
        ];
        $this->client->request('PATCH', '/spice', $_POST, [], [
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