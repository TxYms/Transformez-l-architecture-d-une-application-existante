<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tag\StoreTagRequest;
use App\Http\Resources\TagResource;
use App\Http\Responses\ApiResponse;
use App\Services\TagService;
use Illuminate\Http\JsonResponse;

class TagController extends Controller
{
    public function __construct(private readonly TagService $tags) {}

    public function index(): JsonResponse
    {
        return ApiResponse::success(TagResource::collection($this->tags->list()), 'Tags retrieved.');
    }

    public function store(StoreTagRequest $request): JsonResponse
    {
        $tag = $this->tags->create($request->validated());

        return ApiResponse::success(TagResource::make($tag), 'Tag created.', 201);
    }
}
