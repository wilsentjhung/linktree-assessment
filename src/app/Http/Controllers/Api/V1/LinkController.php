<?php

namespace App\Http\Controllers\Api\V1;

use App\Handlers\LinkHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\IndexLinkRequest;
use App\Http\Requests\StoreLinkRequest;
use App\Models\Link;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LinkController extends Controller
{

    /**
     * Index links.
     * GET /api/v1/links
     *
     * @param IndexLinkRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @TODO Add a caching layer for better performance
     */
    public function index(IndexLinkRequest $request)
    {
        try {
            $handler = new LinkHandler($request->all());

            $links = $handler->indexLinks();
        } catch (HttpException $e) {
            return response()->json(['msg' => $e->getMessage()], $e->getStatusCode());
        } catch (Exception $e) {
            return response()->json(['msg' => $e->getMessage()], 500);
        }

        return response()->json($links);
    }

    /**
     * Store a link.
     * POST /api/v1/links
     * 
     * @param StoreLinkRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreLinkRequest $request)
    {
        try {
            $handler = new LinkHandler($request->all());

            $link = $handler->storeLink();
        } catch (HttpException $e) {
            return response()->json(['msg' => $e->getMessage()], $e->getStatusCode());
        } catch (Exception $e) {
            return response()->json(['msg' => $e->getMessage()], 500);
        }

        return response()->json($link);
    }

    /**
     * Show a link.
     * GET /api/v1/links/{link}
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Link $link)
    {
        return response()->json($link);
    }

    /**
     * Destroy a link.
     * DELETE /api/v1/links/{link}
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Link $link)
    {
        $link->delete();

        return response()->json($link);
    }
}
