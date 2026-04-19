<?php

namespace App\Http\Controllers\Api\Article;

use App\Http\Controllers\Controller;
use App\Http\Resources\Articles\CommentResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Articles\Article;
use App\Models\Articles\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    use ApiResponseTrait;

    /**
     * GET /api/v1/articles/{slug}/comments
     */
    public function index(string $slug): JsonResponse
    {
        $article = Article::where('slug', $slug)->published()->firstOrFail();

        $comments = $article->comments()
            ->with('user:id,name')
            ->latest('created_at')
            ->paginate(20);

        return $this->successResponse(
            CommentResource::collection($comments)->response()->getData(true)
        );
    }

    /**
     * POST /api/v1/articles/{slug}/comments
     */
    public function store(Request $request, string $slug): JsonResponse
    {
        $article = Article::where('slug', $slug)->published()->firstOrFail();

        $data = $request->validate([
            'name'    => [Auth::check() ? 'nullable' : 'required', 'string', 'max:100'],
            'content' => ['required', 'string', 'min:3', 'max:2000'],
        ]);

        $comment = $article->comments()->create([
            'user_id'    => Auth::id(),
            'name'       => $data['name'] ?? null,
            'content'    => $data['content'],
            'created_at' => now(),
        ]);

        $comment->load('user:id,name');

        return $this->createdResponse(new CommentResource($comment), 'Komentar berhasil dikirim.');
    }

    /**
     * DELETE /api/v1/comments/{comment}
     */
    public function destroy(Comment $comment): JsonResponse
    {
        // Pemilik komentar atau author artikel boleh hapus
        $user = Auth::user();
        if ($comment->user_id !== $user->id && $comment->article->author_id !== $user->id) {
            return $this->errorResponse('Tidak diizinkan.', 403);
        }

        $comment->delete();

        return $this->noContentResponse();
    }
}
