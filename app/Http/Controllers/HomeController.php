<?php

namespace App\Http\Controllers;

use App\Http\Enums\GroupUserStatus;
use App\Http\Resources\GroupResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Group;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $userID = Auth::id();
        $user = $request->user();
        $posts = Post::postsForTimeline($userID)
            ->select('posts.*')
            ->leftJoin('followers AS f', function ($join) use ($userID) {
                $join->on('posts.user_id', '=', 'f.user_id')
                    ->where('f.follower_id', '=', $userID);
            })
            ->leftJoin('group_users AS gu', function ($join) use ($userID) {
                $join->on('gu.group_id', '=', 'posts.group_id')
                    ->where('gu.user_id', '=', $userID)
                    ->where('gu.status', GroupUserStatus::APPROVED->value);
            })
            ->where(function ($query) use ($userID) {
                $query->whereNotNull('f.follower_id')
                    ->orWhereNotNull('gu.group_id');
            })
            ->whereNot('posts.user_id', $userID)
            ->paginate(5);

        $posts = PostResource::collection($posts);
        if ($request->wantsJson()) {
            return $posts;
        }

        $groups = Group::query()
            ->with('currentUserGroup')
            ->select(['groups.*'])
            ->join('group_users AS gu', 'gu.group_id', 'groups.id')
            ->where('gu.user_id', Auth::id())
            ->orderBy('gu.role')
            ->orderBy('name', 'desc')
            ->get();

        return Inertia::render('Home', [
            'posts' => $posts,
            'groups' => GroupResource::collection($groups),
            'followings' => UserResource::collection($user->followings)
        ]);
    }
}
