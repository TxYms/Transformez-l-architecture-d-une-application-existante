<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Note\IndexNoteRequest;
use App\Http\Requests\Note\StoreNoteRequest;
use App\Http\Requests\Note\UpdateNoteRequest;
use App\Http\Resources\NoteResource;
use App\Http\Responses\ApiResponse;
use App\Services\NoteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class NoteController extends Controller
{
    public function __construct(private readonly NoteService $notes) {}

    public function index(IndexNoteRequest $request): JsonResponse
    {
        $tagId = $request->validated('tag_id');

        $notes = $this->notes->listFor($request->user(), $tagId !== null ? (int) $tagId : null);

        return ApiResponse::success(NoteResource::collection($notes), 'Notes retrieved.');
    }

    public function store(StoreNoteRequest $request): JsonResponse
    {
        $note = $this->notes->createFor($request->user(), $request->validated());

        return ApiResponse::success(NoteResource::make($note), 'Note created.', 201);
    }

    public function show(int $id): JsonResponse
    {
        $note = $this->notes->find($id);
        Gate::authorize('view', $note);

        return ApiResponse::success(NoteResource::make($note), 'Note retrieved.');
    }

    public function update(UpdateNoteRequest $request, int $id): JsonResponse
    {
        $note = $this->notes->find($id);
        Gate::authorize('update', $note);

        $note = $this->notes->update($note, $request->validated());

        return ApiResponse::success(NoteResource::make($note), 'Note updated.');
    }

    public function destroy(int $id): JsonResponse
    {
        $note = $this->notes->find($id);
        Gate::authorize('delete', $note);

        $this->notes->delete($note);

        return ApiResponse::success(null, 'Note deleted.');
    }
}
