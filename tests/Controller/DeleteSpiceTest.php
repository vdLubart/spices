<?php

namespace App\Tests\Controller;

use App\Model\Spice;
use App\Tests\Controller\SpiceController;

class DeleteSpiceTest extends SpiceController
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

    /** @test - successfully delete spice */
    function successfully_delete_spice() {
        $this->client->request('DELETE', '/spice/' . $this->spice->id, [], [], [
            'HTTP_Authorization' => 'Bearer ' . $this->accessToken
        ]);
        json_decode($this->client->getResponse()->getContent());

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(204);

        $this->assertCount(0, Spice::all());
    }

    /** @test - return 404 if spice does not exist */
    function return_404_if_spice_does_not_exist() {
        $this->client->request('DELETE', '/spice/' . $this->faker->numberBetween(10, 20), [], [], [
            'HTTP_Authorization' => 'Bearer ' . $this->accessToken
        ]);
        $responseContent = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseStatusCodeSame(404);
        $this->assertEquals('Spice not found', $responseContent->message);
    }

    /** @test - return 404 if spice is id is not numerical */
    function return_404_if_spice_is_id_is_not_numerical() {
        $this->client->request('DELETE', '/spice/' . $this->faker->word, [], [], [
            'HTTP_Authorization' => 'Bearer ' . $this->accessToken
        ]);
        json_decode($this->client->getResponse()->getContent());

        $this->assertResponseStatusCodeSame(404);
    }
}