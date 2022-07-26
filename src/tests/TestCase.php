<?php

namespace Tests;

use App\Models\ClassicLink;
use App\Models\Link;
use App\Models\MusicLink;
use App\Models\MusicSublink;
use App\Models\ShowsLink;
use App\Models\ShowsSublink;
use App\Models\User;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Mock base test data using factories.
     *
     * @return void
     */
    protected function mockBaseTestData()
    {
        // Create a user
        $this->user = User::factory()->create();

        // Create a classic link
        $classicLink = ClassicLink::factory()->create();

        $this->classicLink = $classicLink->link()->create([
            'user_id' => $this->user->id,
        ]);

        // Create a music link
        $musicLink = MusicLink::factory()->create();

        $this->musicLink = $musicLink->link()->create([
            'user_id' => $this->user->id,
        ]);

        // Create 2 music sublinks
        $musicSublink1 = MusicSublink::factory()->create();

        $this->musicSublink1 = $musicSublink1->sublink()->create([
            'link_id' => $this->musicLink->id,
        ]);

        $musicSublink2 = MusicSublink::factory()->create();

        $this->musicSublink2 = $musicSublink2->sublink()->create([
            'link_id' => $this->musicLink->id,
        ]);

        // Create a shows link
        $showsLink = ShowsLink::factory()->create();

        $this->showsLink = $showsLink->link()->create([
            'user_id' => $this->user->id,
        ]);

        // Create 2 shows sublinks
        $showsSublink1 = ShowsSublink::factory()->create();
        
        $this->showsSublink1 = $showsSublink1->sublink()->create([
            'link_id' => $this->showsLink->id,
        ]);

        $showsSublink2 = ShowsSublink::factory()->create();

        $this->showsSublink2 = $showsSublink2->sublink()->create([
            'link_id' => $this->showsLink->id,
        ]);
    }

    /**
     * Get valid store input data.
     *
     * @return array
     */
    protected function provideValidStoreData()
    {
        return [
            [
                'test classic link' => [
                    'type' => Link::TYPE_CLASSIC_LINK,
                    'title' => 'Classic',
                    'url' => 'https://www.classic.com/',
                ],
            ],
            [
                'test music link' => [
                    'type' => Link::TYPE_MUSIC_LINK,
                    'title' => 'Music',
                    'sublinks' => [
                        json_encode([
                            'name' => 'Music 1',
                            'url' => 'https://www.music.com/1'
                        ]),
                        json_encode([
                            'name' => 'Music 2',
                            'url' => 'https://www.music.com/2'
                        ]),
                    ],
                ],
            ],
            [
                'test shows link' => [
                    'type' => Link::TYPE_SHOWS_LINK,
                    'title' => 'Shows',
                    'sublinks' => [
                        json_encode([
                            'name' => 'Show 1',
                            'url' => 'https://www.shows.com/1',
                            'status' => ShowsSublink::STATUS_ON_SALE,
                            'date' => '2022-02-02',
                            'venue' => 'Opera House, Sydney',
                        ]),
                        json_encode([
                            'name' => 'Show 2',
                            'url' => 'https://www.shows.com/2',
                            'status' => ShowsSublink::STATUS_NOT_ON_SALE,
                            'date' => '2022-02-02',
                            'venue' => 'Opera House, Sydney',
                        ]),
                        json_encode([
                            'name' => 'Show 3',
                            'url' => 'https://www.shows.com/3',
                            'status' => ShowsSublink::STATUS_SOLD_OUT,
                            'date' => '2022-02-02',
                            'venue' => 'Opera House, Sydney',
                        ]),
                    ],
                ],
            ],
        ];
    }

    /**
     * Get invalid store input data.
     *
     * @return array
     */
    protected function provideInvalidStoreData()
    {
        return [
            [
                'test invalid link type' => [
                    'type' => 'test',
                    'title' => 'Test',
                ],
            ],
            [
                'test classic link with no url' => [
                    'type' => Link::TYPE_CLASSIC_LINK,
                    'title' => 'Classic',
                ],
            ],
            [
                'test classic link with invalid url' => [
                    'type' => Link::TYPE_CLASSIC_LINK,
                    'title' => 'Classic',
                    'url' => 'Classic',
                ],
            ],
            [
                'test classic link with long title (< 144)' => [
                    'type' => Link::TYPE_CLASSIC_LINK,
                    'title' => str_repeat('Classic', 100),
                    'url' => 'https://www.classic.com/',
                ],
            ],
            [
                'test music link with invalid sublinks' => [
                    'type' => Link::TYPE_MUSIC_LINK,
                    'title' => 'Music',
                    'sublinks' => [
                        [
                            'name' => 'Music 1',
                            'url' => 'https://www.music.com/1',
                        ],
                    ],
                ],
            ],
            [
                'test music link with no url in sublinks' => [
                    'type' => Link::TYPE_MUSIC_LINK,
                    'title' => 'Music',
                    'sublinks' => [
                        json_encode([
                            'name' => 'Music 1',
                        ]),
                    ],
                ],
            ],
            [
                'test music link with invalid url in sublinks' => [
                    'type' => Link::TYPE_MUSIC_LINK,
                    'title' => 'Music',
                    'sublinks' => [
                        json_encode([
                            'name' => 'Music 1',
                            'url' => 'Music 1',
                        ]),
                    ],
                ],
            ],
            [
                'test shows link with invalid sublinks' => [
                    'type' => Link::TYPE_SHOWS_LINK,
                    'title' => 'Shows',
                    'sublinks' => 'Show 1',
                ],
            ],
            [
                'test shows link with no status in sublinks' => [
                    'type' => Link::TYPE_SHOWS_LINK,
                    'title' => 'Shows',
                    'sublinks' => [
                        json_encode([
                            'name' => 'Show 1',
                            'url' => 'https://www.shows.com/1',
                            'date' => '2022-02-02',
                            'venue' => 'Opera House, Sydney',
                        ]),
                    ],
                ],
            ],
            [
                'test shows link with invalid status in sublinks' => [
                    'type' => Link::TYPE_SHOWS_LINK,
                    'title' => 'Shows',
                    'sublinks' => [
                        json_encode([
                            'name' => 'Show 1',
                            'url' => 'https://www.shows.com/1',
                            'status' => 'ONSALE',
                            'date' => '2022-02-02',
                            'venue' => 'Opera House, Sydney',
                        ]),
                    ],
                ],
            ],
            [
                'test shows link with invalid date in sublinks' => [
                    'type' => Link::TYPE_SHOWS_LINK,
                    'title' => 'Shows',
                    'sublinks' => [
                        json_encode([
                            'name' => 'Show 1',
                            'url' => 'https://www.shows.com/1',
                            'status' => ShowsSublink::STATUS_ON_SALE,
                            'date' => '2022-22-22',
                            'venue' => 'Opera House, Sydney',
                        ]),
                    ],
                ],
            ],
        ];
    }
}
