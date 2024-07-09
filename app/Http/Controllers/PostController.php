<?php

namespace App\Http\Controllers;

use App\Http\Enums\ReactionEnum;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\PostResource;
use App\Models\Comment;
use App\Models\Post;
use App\Models\PostAttachment;
use App\Models\Reaction;
use App\Models\User;
use App\Notifications\CommentCreated;
use App\Notifications\CommentDeleted;
use App\Notifications\PostCreated;
use App\Notifications\PostDeleted;
use App\Notifications\ReactionAddedOnComment;
use App\Notifications\ReactionAddedOnPost;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use function Pest\Laravel\delete;

class PostController extends Controller
{
    public function view(Post $post)
    {
        $post->loadCount('reactions');
        $post->load([
            'comments' => function ($query) {
                $query->withCount('reactions'); // SELECT * FROM comments WHERE post_id IN (1, 2, 3...)
                // SELECT COUNT(*) from reactions
            },
        ]);

        return inertia('Post/View', [
            'post' => new PostResource($post)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validated();
        $user = $request->user();

        DB::beginTransaction();
        $allFilePaths = [];
        try {
            $post = Post::create($data);
            /** @var UploadedFile[] $files */
            $files = $data['attachments'] ?: [];
            foreach ($files as $file) {
                $path = $file->store('attachments/' . $post->id, 'public');
                $allFilePaths[] = $path;
                PostAttachment::create([
                    'post_id' => $post->id,
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'mime' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'created_by' => $user->id
                ]);
            }
            DB::commit();

            $group = $post->group;

            if ($group) {
                $users = $group->approvedUsers()->where('users.id', '!=', $user->id)->get();
                Notification::send($users, new PostCreated($post, $group));
            }
        } catch (\Exception $e) {
            foreach ($allFilePaths as $path) {
                Storage::disk('public')->delete($path);
            }
            DB::rollBack();
        }

        return back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();

        DB::beginTransaction();
        $allFilePaths = [];
        try {
            $data = $request->validated();
            $post->update($data);

            $deleted_ids = $data['deleted_file_ids'] ?? [];


            $attachments = PostAttachment::query()
                ->where('post_id', $post->id)
                ->whereIn('id', $deleted_ids)
                ->get();

            foreach ($attachments as $attachment) {
                $attachment->delete();
            }

            /** @var \Illuminate\Http\UploadedFile[] $files */
            $files = $data['attachments'] ?: [];
            foreach ($files as $file) {
                $path = $file->store('attachments/' . $post->id, 'public');
                $allFilePaths[] = $path;
                PostAttachment::create([
                    'post_id' => $post->id,
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'mime' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'created_by' => $user->id
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            foreach ($allFilePaths as $path) {
                Storage::disk('public')->delete($path);
            }
            DB::rollBack();
            throw $e;
        }
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $id = Auth::id();

        if ($post->isOwner($id) || $post->group || $post->group->isAdmin($id)){
            $post->delete();

            if (!$post->isOwner($id)){
                $post->user->notify(new PostDeleted($post->group));
            }

            return back();
        }
        return response("You don't have permission to delete this post", 403);
    }

    public function downloadAttachment(PostAttachment $attachment): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return response()->download(Storage::disk('public')->path($attachment->path), $attachment->name);
    }

    public function postReaction(Request $request, Post $post): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $data = $request->validate([
            'reaction' => [Rule::enum(ReactionEnum::class)]
        ]);

        $userID = Auth::id();
        $userReaction = Reaction::all()->where('object_id', $post->id)->where('object_type', Post::class)->first();

        if ($userReaction) {
            $hasReaction = false;
            $userReaction->delete();
        } else {
            $hasReaction = true;
            Reaction::create([
                'object_id' => $post->id,
                'object_type' => Post::class,
                'user_id' => $userID,
                'type' => $data['reaction']
            ]);
        }

        if (!$post->isOwner($userID)) {
            $user = User::where('id', $userID)->first();
            $post->user->notify(new ReactionAddedOnPost($post, $user));
        }

        $reactions = Reaction::all()->where('object_id', $post->id)->where('object_type', Post::class)->count();

        return response([
            'num_of_reactions' => $reactions,
            'current_user_has_reaction' => $hasReaction,
        ]);
    }

    public function postComment(Post $post, Request $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $data = $request->validate([
            'comment' => ['required'],
            'parent_id' => ['nullable', 'exists:comments,id']
        ]);

        $comment = Comment::create([
            'post_id' => $post->id,
            'comment' => nl2br($data['comment']),
            'user_id' => Auth::id(),
            'parent_id' => $data['parent_id'] ?: null
        ]);

        $post = $comment->post;
        $post->user->notify(new CommentCreated($comment, $post));

        return response(new CommentResource($comment), 201);
    }

    public function deleteComment(Comment $comment): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $post = $comment->post;
        $id = Auth::id();

        if ($comment->isOwner($id) || $post->isOwner($id)){
            $comment->delete();

            if (!$comment->isOwner($id)){
                $comment->user->notify(new CommentDeleted($comment, $post));
            }

            return response('', 204);
        }

        return response('You Don\'t Have Permission To Delete This Comment...', 403);
    }

    public function updateComment(UpdateCommentRequest $request, Comment $comment): CommentResource
    {
        $data = $request->validated();

        $comment->update([
            'comment' => nl2br($data['comment'])
        ]);

        return new CommentResource($comment);
    }

    public function commentReaction(Request $request, Comment $comment): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $data = $request->validate([
            'reaction' => [Rule::enum(ReactionEnum::class)]
        ]);

        $userID = Auth::id();
        $userReaction = Reaction::all()->where('object_id', $comment->id)->where('object_type', Comment::class)->first();

        if ($userReaction) {
            $hasReaction = false;
            $userReaction->delete();
        } else {
            $hasReaction = true;
            Reaction::create([
                'object_id' => $comment->id,
                'object_type' => Comment::class,
                'user_id' => $userID,
                'type' => $data['reaction']
            ]);

            if (!$comment->isOwner($userID)) {
                $user = User::where('id', $userID)->first();
                $comment->user->notify(new ReactionAddedOnComment($comment->post, $comment, $user));
            }
        }

        $reactions = Reaction::all()->where('object_id', $comment->id)->where('object_type', Comment::class)->count();

        return response([
            'num_of_reactions' => $reactions,
            'current_user_has_reaction' => $hasReaction,
        ]);
    }
}
