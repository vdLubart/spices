<?php

namespace App\Tests\Controller\V1;

use App\Model\Spice;
use Illuminate\Database\Eloquent\Model;

class SpiceListTest extends SpiceController
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

        $this->spice = new Spice();
        $this->spice->name = $this->faker->word;
        $this->spice->status = 'outOfStock';
        $this->spice->save();
    }

    /** @test - successfully get all spice list */
    function successfully_get_all_spice_list() {
        $this->client->request('GET', '/v1/spice/list', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $this->accessToken
        ]);
        $responseContent = json_decode($this->client->getResponse()->getContent());

        $firstSpice = Spice::first();

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertCount(4, $responseContent->items);
        $this->assertEquals($firstSpice->name, $responseContent->items[0]->name);
        $this->assertEquals(1, $responseContent->page);
        $this->assertEquals(10, $responseContent->perPage);
        $this->assertEquals(4, $responseContent->total);
    }

    /** @test - get first page with two spices */
    function get_first_page_with_two_spices() {
        $this->client->request('GET', '/v1/spice/list?perPage=2', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $this->accessToken
        ]);
        $responseContent = json_decode($this->client->getResponse()->getContent());

        $firstSpice = Spice::first();

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertCount(2, $responseContent->items);
        $this->assertEquals($firstSpice->name, $responseContent->items[0]->name);
        $this->assertEquals(1, $responseContent->page);
        $this->assertEquals(2, $responseContent->perPage);
        $this->assertEquals(4, $responseContent->total);
    }

    /** @test - get second page with two spices per page */
    function get_second_page_with_two_spices_per_page() {
        $this->client->request('GET', '/v1/spice/list?perPage=2&page=2', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $this->accessToken
        ]);
        $responseContent = json_decode($this->client->getResponse()->getContent());

        $lastSpice = Spice::orderBy('id', 'desc')->first();

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertCount(2, $responseContent->items);
        $this->assertEquals($lastSpice->name, $responseContent->items[1]->name);
        $this->assertEquals(2, $responseContent->page);
        $this->assertEquals(2, $responseContent->perPage);
        $this->assertEquals(4, $responseContent->total);
    }

    /** @test - get empty list on request third page */
    function get_empty_list_on_request_third_page() {
        $this->client->request('GET', '/v1/spice/list?perPage=2&page=3', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $this->accessToken
        ]);
        $responseContent = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertCount(0, $responseContent->items);
        $this->assertEquals(3, $responseContent->page);
        $this->assertEquals(2, $responseContent->perPage);
        $this->assertEquals(4, $responseContent->total);
    }

    /** @test - apply default values on wrong query parameters type */
    function apply_default_values_on_wrong_query_parameters_type() {
        $this->client->request('GET', '/v1/spice/list?perPage=qwe&page=qwe', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $this->accessToken
        ]);
        $responseContent = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertCount(4, $responseContent->items);
        $this->assertEquals(1, $responseContent->page);
        $this->assertEquals(10, $responseContent->perPage);
        $this->assertEquals(4, $responseContent->total);
    }

    /** @test - apply default values on negative query parameters */
    function apply_default_values_on_negative_query_parameters() {
        $this->client->request('GET', '/v1/spice/list?perPage=-1&page=-1', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $this->accessToken
        ]);
        $responseContent = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertCount(4, $responseContent->items);
        $this->assertEquals(1, $responseContent->page);
        $this->assertEquals(10, $responseContent->perPage);
        $this->assertEquals(4, $responseContent->total);
    }

    /** @test - find the spices by query */
    function find_the_spices_by_query() {
        $firstSpice = Spice::first();

        $this->client->request('GET', '/v1/spice/list?q=' . $firstSpice->name, [], [], [
            'HTTP_Authorization' => 'Bearer ' . $this->accessToken
        ]);
        $responseContent = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertCount(1, $responseContent->items);
        $this->assertEquals(1, $responseContent->page);
        $this->assertEquals(10, $responseContent->perPage);
        $this->assertEquals(1, $responseContent->total);
        $this->assertEquals($firstSpice->name, $responseContent->items[0]->name);
    }

    /** @test - find spices by status */
    function find_spices_by_status() {
        $this->client->request('GET', '/v1/spice/list/outOfStock', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $this->accessToken
        ]);
        $responseContent = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertCount(2, $responseContent->items);
        $this->assertEquals(1, $responseContent->page);
        $this->assertEquals(10, $responseContent->perPage);
        $this->assertEquals(2, $responseContent->total);
        $this->assertEquals('outOfStock', $responseContent->items[0]->status);
    }

    /** @test - return 404 if status is wrong */
    function return_404_if_status_is_wrong() {
        $this->client->request('GET', '/v1/spice/list/out_of_stock', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $this->accessToken
        ]);
        json_decode($this->client->getResponse()->getContent());

        $this->assertResponseStatusCodeSame(404);
    }

    /** @test - count statuses on spice list */
    function count_statuses_on_spice_list() {
        $this->client->request('GET', '/v1/spice/list/statuses', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $this->accessToken
        ]);
        $responseContent = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertCount(4, (array)$responseContent);
        $this->assertEquals(1, $responseContent->full);
        $this->assertEquals(1, $responseContent->runningOut);
        $this->assertEquals(2, $responseContent->outOfStock);
        $this->assertEquals(4, $responseContent->all);
    }

    /** @test - count statuses on spice list when one status is empty */
    function count_statuses_on_spice_list_when_one_status_is_empty() {
        Spice::find(1)->delete();

        $this->client->request('GET', '/v1/spice/list/statuses', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $this->accessToken
        ]);
        $responseContent = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertCount(4, (array)$responseContent);
        $this->assertEquals(0, $responseContent->full);
        $this->assertEquals(1, $responseContent->runningOut);
        $this->assertEquals(2, $responseContent->outOfStock);
        $this->assertEquals(3, $responseContent->all);
    }
}