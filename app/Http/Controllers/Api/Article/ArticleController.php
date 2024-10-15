<?php

namespace App\Http\Controllers\Api\Article;

use App\Http\Controllers\Controller;
use App\Http\Resources\Article\ArticleResource;
use App\Models\Article;
use App\Models\Preference;
use Illuminate\Http\Request;

/**
 * @group Article
 *
 * APIs for managing articles
 */
class ArticleController extends Controller
{
    /**
     * Fetch
     *
     * Articles Fetch
     *
     * @authenticated
     * @bodyParam search string optional Example: NEWSAPI
     * @response 200 scenario="success" {"status":"success","data":{"total":316,"per_page":20,"current_page":4,"articles":[{"id":286,"author":"","keywords":"us-news","categories":"us-news","content":"https://www.theguardian.com/about/2018/jan/09/a-letter-from-the-editor-in-chief-on-the-new-guardian-and-observer","published_date":"2018-01-09","source":"Guardian","created_at":null,"updated_at":null}]}}
     * 
     */
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

    /**
     * Retrieve
     *
     * Retrieve One Specific Article
     *
     * @authenticated
     * @urlParam id integer required The ID of the article. Example: 1000
     * @response 200 scenario="success" {"status":"success","data":{"articles":{"id":1000,"author":"Alcynna Lloyd,Dan Latu,Jordan Pandy","keywords":"technology","categories":"technology","content":"Business Insider is seeking nominations for its Rising Stars of Real Estate 2024 list.filadendron/Getty Images\r\n<ul><li>Nominations are now open for Business Insider's fifth annual list of Rising Sta… [+1736 chars]","published_date":"2024-09-25","source":"NewsApi","created_at":"2024-10-13T13:17:36.000000Z","updated_at":"2024-10-13T13:17:36.000000Z"}}}
     * 
     */
    public function retrieve(Article $article)
    {
        return response()->json(['status' => 'success', 'data' => ['articles' => new ArticleResource($article)]]);
    }

    /**
     * Feed
     *
     * Articles Fetch Based On User Preferences
     *
     * @authenticated
     * @bodyParam search string optional Example: NEWSAPI
     * @response 200 scenario="success" {"status":"success","data":{"total":316,"per_page":20,"current_page":4,"articles":[{"id":286,"author":"","keywords":"us-news","categories":"us-news","content":"https://www.theguardian.com/about/2018/jan/09/a-letter-from-the-editor-in-chief-on-the-new-guardian-and-observer","published_date":"2018-01-09","source":"Guardian","created_at":null,"updated_at":null}]}}
     * 
     */
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
