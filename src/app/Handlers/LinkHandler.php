<?php

namespace App\Handlers;

use App\Models\Link;
use App\Models\Sublink;
use App\Models\User;

use Illuminate\Database\Eloquent\Relations\Relation;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LinkHandler
{
	/**
	 * Error msg for user not found
	 */
	public const ERROR_MSG_USER_NOT_FOUND = 'User not found.';

	/**
	 * @var array
	 */
	protected $request;

	/**
	 * @var User
	 */
	protected $user;

	/**
	 * LinkHandler constructor.
	 *
	 * @param array $request
	 * @throws HttpException
	 */
	public function __construct(array $request)
	{
		$this->request = $request;

		$this->user = User::byUuid($this->request['userUuid'])->first();

		if (!$this->user) {
            throw new HttpException(404, self::ERROR_MSG_USER_NOT_FOUND);
        }
	}

	/**
	 * Index links.
	 *
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function indexLinks()
	{
		$links = $this->user->links();

        if (!empty($this->request['sortBy'])) {
            $links->orderBy($this->request['sortBy'], $this->request['orderBy'] ?? 'asc');
        }

        return $links->get();
	}

	/**
	 * Store a link.
	 *
	 * @return Link
	 */
	public function storeLink()
	{
        $class = Relation::getMorphedModel($this->request['type']);

        $model = $class::create($this->request);

        $link = $model->link()->create([
            'user_id' => $this->user->id,
        ]);

        if (!empty($this->request['sublinks'])
        	&& in_array($this->request['type'], Link::TYPE_LIST_WITH_SUBLINKS)) {
            $this->storeSublinks($link, $this->request['sublinks']);
        }

        return $link->load(['linkable', 'sublinks']);
	}

	/**
	 * Store sublinks to the given link.
	 *
	 * @param Link $link
	 * @param array $sublinks
	 * @return void
	 */
	protected function storeSublinks(Link $link, array $sublinks)
	{
		foreach ($this->request['sublinks'] as $sublink) {
            $sublink = json_decode($sublink, true);

            $class = Relation::getMorphedModel($this->request['type'] . Sublink::SUFFIX_SUBLINK_TYPE);

            $model = $class::create($sublink);

            $model->sublink()->create([
                'link_id' => $link->id,
            ]);
        }
	}
}
