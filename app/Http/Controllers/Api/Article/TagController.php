<?php

namespace App\Http\Controllers\Api\Article;

use App\Http\Controllers\Controller;
use App\Http\Resources\Articles\TagResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Articles\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TagController extends Controller
{
    use ApiResponseTrait;

    /**
     * GET /api/v1/tags
     */
    public function index(): JsonResponse
    {
        $tags = Tag::withCount('articles')->orderBy('name')->get();

        return $this->successResponse(TagResource::collection($tags));
    }

    /**
     * POST /api/v1/tags
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Tag::class);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:tags,name'],
            'slug' => ['nullable', 'string', 'max:120', 'unique:tags,slug'],
        ]);

        $tag = Tag::create($data);

        return $this->createdResponse(new TagResource($tag), 'Tag berhasil dibuat.');
    }

    /**
     * PUT /api/v1/tags/{tag}
     */
    public function update(Request $request, Tag $tag): JsonResponse
    {
        $this->authorize('update', $tag);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('tags', 'name')->ignore($tag->id)],
            'slug' => ['nullable', 'string', 'max:120', Rule::unique('tags', 'slug')->ignore($tag->id)],
        ]);

        $tag->update($data);

        return $this->successResponse(new TagResource($tag), 'Tag berhasil diperbarui.');
    }

    /**
     * DELETE /api/v1/tags/{tag}
     */
    public function destroy(Tag $tag): JsonResponse
    {
        $this->authorize('delete', $tag);

        $tag->articles()->detach();
        $tag->delete();

        return $this->noContentResponse();
    }
}
