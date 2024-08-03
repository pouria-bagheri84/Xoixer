<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Post
 *
 * @property Group $group
 * @package App\Models
 */
class Post extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'body',
        'user_id',
        'group_id',
        'pinned',
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(PostAttachment::class)->latest();
    }

    public function reactions(): MorphMany
    {
        return $this->morphMany(Reaction::class, 'object');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->latest()->take(5);
    }

    public static function postsForTimeline($userId, $getLatest = true): Builder
    {
        $query = Post::query() // SELECT * FROM posts
        ->withCount('reactions') // SELECT COUNT(*) from reactions
        ->with([
            'user',
            'group',
            'group.currentUserGroup',
            'attachments',
            'comments' => function ($query) {
                $query->withCount('reactions'); // SELECT * FROM comments WHERE post_id IN (1, 2, 3...)
                // SELECT COUNT(*) from reactions
            },
            'comments.user',
            'comments.reactions' => function ($query) use ($userId) {
                $query->where('user_id', $userId); // SELECT * from reactions WHERE user_id = ?
            },
            'reactions' => function ($query) use ($userId) {
                $query->where('user_id', $userId); // SELECT * from reactions WHERE user_id = ?
            }]);
        if ($getLatest){
            $query->latest();
        }

        return $query;
    }

    public function isOwner($userID): bool
    {
        return $this->user_id == $userID;
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($post) {
            $post->deleted_by = auth()->id();
            $post->save();
        });
    }
}
