<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $articles = Article::query()
            ->latest()
            ->paginate(6)
            ->withQueryString();

        return view('articles.index', compact('articles'));
    }

    public function apiIndex(Request $request)
    {
        $paginator = Article::query()
            ->latest()
            ->paginate(6)
            ->withQueryString();

        return response()->json([
            'data' => $paginator->getCollection()->map(function (Article $article) {
                return [
                    'id' => $article->id,
                    'title' => $article->title,
                    'slug' => $article->slug,
                    'category' => $article->category,
                    'image' => $article->image,
                    'image_url' => asset($article->image),
                    'created_at' => $article->created_at,
                    'created_at_formatted' => optional($article->created_at)->translatedFormat('d-m-Y'),
                ];
            }),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ]);
    }

    public function show(string $slug)
    {
        $article = Article::where('slug', $slug)->firstOrFail();
        return view('articles.show', compact('article'));
    }
}


