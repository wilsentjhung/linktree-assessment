<?php

namespace Tests\Unit\Handlers;

use App\Handlers\LinkHandler;
use App\Models\Link;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class LinkHandlerTest extends TestCase
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
	 * @group LinkHandler
	 */
	public function indexLinksSuccess()
	{
		$handler = new LinkHandler([
			'userUuid' => $this->user->uuid,
		]);

		$links = $handler->indexLinks()->toArray();

		$classicLink = $this->classicLink->linkable()->first();

		$musicLink = $this->musicLink->linkable()->first();
		$musicSublink1 = $this->musicSublink1->linkable()->first();
		$musicSublink2 = $this->musicSublink2->linkable()->first();

		$showsLink = $this->showsLink->linkable()->first();
		$showsSublink1 = $this->showsSublink1->linkable()->first();
		$showsSublink2 = $this->showsSublink2->linkable()->first();

		$this->assertEquals(3, count($links));

		// Check classic link
		$this->assertEquals($this->classicLink->id, $links[0]['id']);
		$this->assertEquals($this->classicLink->linkable_type, $links[0]['linkable_type']);
		$this->assertEquals($classicLink->title, $links[0]['linkable']['title']);
		$this->assertEquals($classicLink->url, $links[0]['linkable']['url']);
		$this->assertEmpty($links[0]['sublinks']);

		// Check music link and its sublinks
		$this->assertEquals($this->musicLink->id, $links[1]['id']);
		$this->assertEquals($this->musicLink->linkable_type, $links[1]['linkable_type']);
		$this->assertEquals($musicLink->title, $links[1]['linkable']['title']);
		$this->assertEquals(2, count($links[1]['sublinks']));
		$this->assertEquals($this->musicSublink1->id, $links[1]['sublinks'][0]['id']);
		$this->assertEquals($this->musicSublink2->id, $links[1]['sublinks'][1]['id']);
		$this->assertEquals($musicSublink1->url, $links[1]['sublinks'][0]['linkable']['url']);
		$this->assertEquals($musicSublink2->url, $links[1]['sublinks'][1]['linkable']['url']);

		// Check shows link and its sublinks
		$this->assertEquals($this->showsLink->id, $links[2]['id']);
		$this->assertEquals($this->showsLink->linkable_type, $links[2]['linkable_type']);
		$this->assertEquals($showsLink->title, $links[2]['linkable']['title']);
		$this->assertEquals(2, count($links[2]['sublinks']));
		$this->assertEquals($this->showsSublink1->id, $links[2]['sublinks'][0]['id']);
		$this->assertEquals($this->showsSublink2->id, $links[2]['sublinks'][1]['id']);
		$this->assertEquals($showsSublink1->status, $links[2]['sublinks'][0]['linkable']['status']);
		$this->assertEquals($showsSublink2->status, $links[2]['sublinks'][1]['linkable']['status']);
	}

	/**
	 * @test
	 * @group LinkHandler
	 * @dataProvider provideValidStoreData
	 * @param array $params
	 */
	public function storeLinkSuccess($params)
	{
		$params['userUuid'] = $this->user->uuid;

		$handler = new LinkHandler($params);

		$link = $handler->storeLink()->toArray();

		$this->assertEquals($params['type'], $link['linkable_type']);
		$this->assertEquals($params['title'], $link['linkable']['title']);

		if ($params['type'] === Link::TYPE_CLASSIC_LINK) {
			$this->assertEquals($params['url'], $link['linkable']['url']);
			$this->assertEmpty($link['sublinks']);
		} else {
			$this->assertEquals(count($params['sublinks']), count($link['sublinks']));
		}
	}

	/**
	 * @test
	 * @group LinkHandler
	 */
	public function constructorShouldThrowExceptionDueToUserNotFound()
	{
		$this->expectException(HttpException::class);

		new LinkHandler([
			'userUuid' => 'd8f56fb5-f098-432a-84a9-71b7750fa187',
		]);
	}
}
