<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Models\Link;
use App\Models\ShowsLink;
use App\Models\ShowsSublink;

use Tests\TestCase;

class LinkControllerTest extends TestCase
{
	/**
	 * Set up test case.
	 */
	public function setUp(): void
	{
		parent::setUp();

		$this->mockBaseTestData();
	}

	/**
	 * @test
	 * @group LinkController@V1
	 */
	public function indexShouldReturn200()
	{
		$params = [
			'userUuid' => $this->user->uuid,
		];

		$res = $this->call('GET', '/api/v1/links', $params);
		$content = json_decode($res->getContent(), true);

		$classicLink = $this->classicLink->linkable()->first();

		$musicLink = $this->musicLink->linkable()->first();
		$musicSublink1 = $this->musicSublink1->linkable()->first();
		$musicSublink2 = $this->musicSublink2->linkable()->first();

		$showsLink = $this->showsLink->linkable()->first();
		$showsSublink1 = $this->showsSublink1->linkable()->first();
		$showsSublink2 = $this->showsSublink2->linkable()->first();

		$res->assertStatus(200);
		$this->assertEquals(3, count($content));

		// Check classic link
		$this->assertEquals($this->classicLink->id, $content[0]['id']);
		$this->assertEquals($this->classicLink->linkable_type, $content[0]['linkable_type']);
		$this->assertEquals($classicLink->title, $content[0]['linkable']['title']);
		$this->assertEquals($classicLink->url, $content[0]['linkable']['url']);
		$this->assertEmpty($content[0]['sublinks']);

		// Check music link and its sublinks
		$this->assertEquals($this->musicLink->id, $content[1]['id']);
		$this->assertEquals($this->musicLink->linkable_type, $content[1]['linkable_type']);
		$this->assertEquals($musicLink->title, $content[1]['linkable']['title']);
		$this->assertEquals(2, count($content[1]['sublinks']));
		$this->assertEquals($this->musicSublink1->id, $content[1]['sublinks'][0]['id']);
		$this->assertEquals($this->musicSublink2->id, $content[1]['sublinks'][1]['id']);
		$this->assertEquals($musicSublink1->url, $content[1]['sublinks'][0]['linkable']['url']);
		$this->assertEquals($musicSublink2->url, $content[1]['sublinks'][1]['linkable']['url']);

		// Check shows link and its sublinks
		$this->assertEquals($this->showsLink->id, $content[2]['id']);
		$this->assertEquals($this->showsLink->linkable_type, $content[2]['linkable_type']);
		$this->assertEquals($showsLink->title, $content[2]['linkable']['title']);
		$this->assertEquals(2, count($content[2]['sublinks']));
		$this->assertEquals($this->showsSublink1->id, $content[2]['sublinks'][0]['id']);
		$this->assertEquals($this->showsSublink2->id, $content[2]['sublinks'][1]['id']);
		$this->assertEquals($showsSublink1->status, $content[2]['sublinks'][0]['linkable']['status']);
		$this->assertEquals($showsSublink2->status, $content[2]['sublinks'][1]['linkable']['status']);
	}

	/**
	 * @test
	 * @group LinkController@V1
	 */
	public function indexShouldReturn200WithSortingParams()
	{
		$params = [
			'userUuid' => $this->user->uuid,
			'sortBy' => 'created_at',
			'orderBy' => 'asc',
		];

		$res = $this->json('GET', '/api/v1/links', $params);
		$content = json_decode($res->getContent(), true);

		$res->assertStatus(200);
		$this->assertEquals(3, count($content));
	}

	/**
	 * @test
	 * @group LinkController@V1
	 */
	public function indexShouldReturn404DueToUserNotFound()
	{
		$params = [
			'userUuid' => 'd8f56fb5-f098-432a-84a9-71b7750fa187',
		];

		$res = $this->json('GET', '/api/v1/links', $params);
		$content = json_decode($res->getContent(), true);

		$res->assertStatus(404);
		$this->assertEquals('User not found.', $content['msg']);
	}

	/**
	 * @test
	 * @group LinkController@V1
	 */
	public function indexShouldReturn422DueToInvalidUserUuid()
	{
		$params = [
			'userUuid' => 'd8f56fb5',
		];

		$res = $this->json('GET', '/api/v1/links', $params);
		$content = json_decode($res->getContent(), true);

		$res->assertStatus(422);
		$this->assertEquals('The user uuid must be a valid UUID.', $content['errors']['userUuid'][0]);
	}

	/**
	 * @test
	 * @group LinkController@V1
	 */
	public function indexShouldReturn422DueToInvalidSortingParams()
	{
		$params = [
			'userUuid' => $this->user->uuid,
			'sortBy' => 'updated_at',
			'orderBy' => 'ascending',
		];

		$res = $this->json('GET', '/api/v1/links', $params);
		$content = json_decode($res->getContent(), true);

		$res->assertStatus(422);
		$this->assertEquals('The selected sort by is invalid.', $content['errors']['sortBy'][0]);
		$this->assertEquals('The selected order by is invalid.', $content['errors']['orderBy'][0]);
	}

	/**
	 * @test
	 * @group LinkController@V1
	 * @dataProvider provideValidStoreData
	 * @param array $params
	 */
	public function storeShouldReturn200(array $params)
	{
		$params['userUuid'] = $this->user->uuid;

		$res = $this->json('POST', '/api/v1/links', $params);
		$content = json_decode($res->getContent(), true);

		$res->assertStatus(200);
		$this->assertEquals($params['type'], $content['linkable_type']);
		$this->assertEquals($params['title'], $content['linkable']['title']);

		if ($params['type'] === Link::TYPE_CLASSIC_LINK) {
			$this->assertEquals($params['url'], $content['linkable']['url']);
			$this->assertEmpty($content['sublinks']);
		} else {
			$this->assertEquals(count($params['sublinks']), count($content['sublinks']));
		}
	}

	/**
	 * @test
	 * @group LinkController@V1
	 */
	public function storeShouldReturn404DueToUserNotFound()
	{
		$params = [
			'userUuid' => 'd8f56fb5-f098-432a-84a9-71b7750fa187',
			'type' => Link::TYPE_CLASSIC_LINK,
			'title' => 'Classic',
			'url' => 'https://www.classic.com/',
		];

		$res = $this->json('POST', '/api/v1/links', $params);
		$content = json_decode($res->getContent(), true);

		$res->assertStatus(404);
		$this->assertEquals('User not found.', $content['msg']);
	}

	/**
	 * @test
	 * @group LinkController@V1
	 * @dataProvider provideInvalidStoreData
	 * @param array $params
	 */
	public function storeShouldReturn422(array $params)
	{
		$params['userUuid'] = $this->user->uuid;

		$res = $this->json('POST', '/api/v1/links', $params);

		$res->assertStatus(422);
	}
}
