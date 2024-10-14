<?php

namespace App\Http\Controllers\Api\Article;

use App\Http\Controllers\Controller;
use App\Http\Resources\Article\ArticleResource;
use App\Models\Article;
use App\Models\Preference;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function fetch(Request $request)
    {
        $article = new Article;
        if (!empty($request->search)) {
            $search = "%" . $request->search . "%";
            $article = $article->where(function ($q) use ($search) {
                $q->where('keyword', 'LIKE', $search)
                    ->orWhere('category', 'LIKE', $search)
                    ->orWhere('published_date', 'LIKE', $search)
                    ->orWhereHas('source', function ($query) use ($search) {
                        $query->where('name', 'LIKE', $search);
                    });
            });
        }

        $articles = $article->orderBy('created_at', 'DESC')->paginate(20);

        return response()->json(['status' => 'success', 'data' => [
            "total_items" => $articles->total(),
            "total_pages" => ceil($articles->total() / $articles->perPage()),
            "per_page" => $articles->perPage(),
            "current_page" => $articles->currentPage(),
            'articles' => ArticleResource::collection($articles)
        ]]);
    }

    public function retrieve(Article $article)
    {
        return response()->json(['status' => 'success', 'data' => ['articles' => new ArticleResource($article)]]);
    }

    public function feed()
    {
        $preference = Preference::where('user_id', auth()->id())->first();

        if (!$preference) {
            return response()->json(['status' => 'error', 'message' => 'this user has not set preference!'], 404);
        }
        
        $articles = Article::orWhere('author', $preference->authors)
        ->orWhereIn('category', $preference->categories)
        ->orderBy('created_at', 'DESC')
        ->paginate(20);
        
        return response()->json(['status' => 'success', 'data' => [
            "total_items" => $articles->total(),
            "total_pages" => ceil($articles->total() / $articles->perPage()),
            "per_page" => $articles->perPage(),
            "current_page" => $articles->currentPage(),
            'articles' => ArticleResource::collection($articles)
        ]]);
    }
}
