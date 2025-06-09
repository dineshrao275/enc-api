<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $articles = Article::where('user_id', Auth::id())->get();
        return response()->json(["success" => true, "data" => ["articles" => $articles]]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->errors()->first()], 422);
        }

        $validated = $validator->validated();

        $path = $request->hasFile('image')
            ? $request->file('image')->store('articles', 'public')
            : null;

        $article = Article::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'image_path' => $path,
            'user_id' => Auth::id(),
        ]);

        return response()->json([
            "success" => true,
            "message" => "Article created successfully.",
            "data" => ["article" => $article]
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($article)
    {
        $article = Article::find($article);
        if (!$article) {
            return response()->json([
                "success" => false,
                "data" => [],
                "message" => "Article not found",
            ], 404);
        }
        $this->authorize('view', $article);

        return response()->json([
            "success" => true,
            "data" => ["article" => $article]
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,$article)
    {
        $article = Article::find($article);
        if (!$article) {
            return response()->json([
                "success" => false,
                "data" => [],
                "message" => "Article not found",
            ], 404);
        }

        $this->authorize('update', $article);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->errors()->first()], 422);
        }

        $validated = $validator->validated();

        if ($request->hasFile('image')) {
            if ($article->image_path) {
                Storage::disk('public')->delete($article->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('articles', 'public');
        }

        $article->update($validated);

        return response()->json([
            "success" => true,
            "message" => "Article updated successfully.",
            "data" => ["article" => $article]
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($article)
    {
        $article = Article::find($article);
        if (!$article) {
            return response()->json([
                "success" => false,
                "data" => [],
                "message" => "Article not found",
            ], 404);
        }
        $this->authorize('delete', $article);

        if ($article->image_path) {
            Storage::disk('public')->delete($article->image_path);
        }

        $article->delete();

        return response()->json([
            "success" => true,
            "message" => "Article deleted successfully."
        ], 200);
    }
}
