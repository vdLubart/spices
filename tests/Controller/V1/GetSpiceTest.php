<?php

namespace App\Tests\Controller\V1;

use App\Model\Spice;

class GetSpiceTest extends SpiceController
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

    /** @test - successfully get a spice */
    function successfully_get_a_spice() {
        $this->client->request('GET', '/v1/spice/' . $this->spice->id, [], [], [
            'HTTP_Authorization' => 'Bearer ' . $this->accessToken
        ]);
        $responseContent = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertEquals($this->spice->id, $responseContent->id);
        $this->assertEquals($this->spice->name, $responseContent->name);
        $this->assertEquals($this->spice->status, $responseContent->status);
    }

    /** @test - return 404 status if spice not found */
    function return_404_status_if_spice_not_found() {
        $this->client->request('GET', '/v1/spice/' . $this->faker->numberBetween(10, 20), [], [], [
            'HTTP_Authorization' => 'Bearer ' . $this->accessToken
        ]);
        $responseContent = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseStatusCodeSame(404);
        $this->assertEquals('Spice not found', $responseContent->message);
    }

    /** @test - return 404 if spice is id is not numerical */
    function return_404_if_spice_is_id_is_not_numerical() {
        $this->client->request('GET', '/v1/spice/' . $this->faker->word, [], [], [
            'HTTP_Authorization' => 'Bearer ' . $this->accessToken
        ]);
        json_decode($this->client->getResponse()->getContent());

        $this->assertResponseStatusCodeSame(404);
    }
}
