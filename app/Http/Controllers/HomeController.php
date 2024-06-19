<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $userID = Auth::id();

        $posts = Post::query()
            ->withCount('reactions')
            ->with([
                'comments' => function ($query) use ($userID) {
                    $query->withCount('reactions');
                },
                'reactions' => function ($query) use ($userID){
                $query->where('user_id', $userID);
            }])
            ->latest()
            ->paginate(10);

        $posts = PostResource::collection($posts);
        if ($request->wantsJson()){
            return $posts;
        }

        return Inertia::render('Home', [
            'posts' => $posts
        ]);
    }
}
