<?php

namespace App\Tests\Controller\V1;

use App\Model\Spice;
use Illuminate\Database\Eloquent\Model;

class MassUpdateSpiceTest extends SpiceController
{
    protected Spice $spice;

    public function setUp():void
    {
        parent::setUp();

        $this->spice = new Spice();
        $this->spice->name = $this->faker->unique()->word();
        $this->spice->status = 'full';
        $this->spice->save();

        $this->spice = new Spice();
        $this->spice->name = $this->faker->word;
        $this->spice->status = 'runningOut';
        $this->spice->save();

        $this->spice = new Spice();
        $this->spice->name = $this->faker->word;
        $this->spice->status = 'outOfStock';
        $this->spice->save();
    }

    /** @test - update statuses of all spices to full */
    function update_statuses_of_all_spices_to_full() {
        $_POST = [
            'status' => 'full',
            'ids' => [1, 2, 3]
        ];
        $this->client->request('PATCH', '/v1/spices', $_POST, [], [
            'HTTP_Authorization' => 'Bearer ' . $this->accessToken
        ]);
        $responseContent = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(207);
        $this->assertCount(3, $responseContent);
        $this->assertEquals('full', $responseContent[0]->status);
        $this->assertEquals('full', $responseContent[1]->status);
        $this->assertEquals('full', $responseContent[2]->status);

        $spices = Spice::all();

        $this->assertEquals('full', $spices[0]->status);
        $this->assertEquals('full', $spices[1]->status);
        $this->assertEquals('full', $spices[2]->status);
    }

    /** @test - update statuses for only valid ids */
    function update_statuses_for_only_valid_ids() {
        $_POST = [
            'status' => 'full',
            'ids' => [1, 2, 'wrong']
        ];
        $this->client->request('PATCH', '/v1/spices', $_POST, [], [
            'HTTP_Authorization' => 'Bearer ' . $this->accessToken
        ]);
        $responseContent = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(207);
        $this->assertCount(2, $responseContent);
        $this->assertEquals('full', $responseContent[0]->status);
        $this->assertEquals('full', $responseContent[1]->status);

        $spices = Spice::all();

        $this->assertEquals('full', $spices[0]->status);
        $this->assertEquals('full', $spices[1]->status);
        $this->assertEquals('outOfStock', $spices[2]->status);
    }

    /** @test - cannot update spices if status is wrong */
    function cannot_update_spices_if_status_is_wrong() {
        $_POST = [
            'status' => $this->faker->word,
            'ids' => [1, 2, 3]
        ];
        $this->client->request('PATCH', '/v1/spices', $_POST, [], [
            'HTTP_Authorization' => 'Bearer ' . $this->accessToken
        ]);
        $responseContent = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseStatusCodeSame(422);
        $this->assertEquals('Validation failed', $responseContent->message);
        $this->assertCount(1, $responseContent->errors);
        $this->assertEquals('status', $responseContent->errors[0]->property);
        $this->assertEquals('The value you selected is not a valid choice.', $responseContent->errors[0]->message);
    }

    /** @test - return empty list if all ids are wrong */
    function return_empty_list_if_all_ids_are_wrong() {
        $_POST = [
            'status' => 'full',
            'ids' => ['totally', 'wrong']
        ];
        $this->client->request('PATCH', '/v1/spices', $_POST, [], [
            'HTTP_Authorization' => 'Bearer ' . $this->accessToken
        ]);
        $responseContent = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(207);
        $this->assertCount(0, $responseContent);

        $spices = Spice::all();

        $this->assertEquals('full', $spices[0]->status);
        $this->assertEquals('runningOut', $spices[1]->status);
        $this->assertEquals('outOfStock', $spices[2]->status);
    }
}