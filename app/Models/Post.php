<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
// use Spatie\Tags\HasTags;

class Post extends Model
{
    use HasFactory;
    // use HasTags;

    /**
     * @var string
     */
    protected $table = 'blog_posts';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'banner',
        'content',
        'published_at',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'published_at' => 'date',
    ];

    /**
     * @var array<string>
     */
    protected $appends = [
        'banner_url',
    ];

    public function bannerUrl(): Attribute
    {
        return Attribute::get(fn () => asset(Storage::url($this->banner)));
    }

    public function scopePublished(Builder $query)
    {
        return $query->whereNotNull('published_at');
    }

    public function scopeDraft(Builder $query)
    {
        return $query->whereNull('published_at');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class, 'blog_author_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'blog_category_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeFilter($query, array $filters = []): void
    {
        $query->when($filters['search'] ?? false, fn ($query, $search) => $query
            ->where('title', 'LIKE', "%$search%")
            ->orWhere('content', 'LIKE', "%$search%"));

        $query->when($filters['category'] ?? false, fn ($query, $category) => $query
            ->whereHas('Category', function ($query) use ($category) {
                $query->where('id', $category);
            }));
    }

    public static function getRelatedPost(Post $post, int $count = 4)
    {
        $relatedPost = collect();
        $allPost = Post::all();

        foreach ($allPost as $otherPost) {
            if ($otherPost->id != $post->id) {
                similar_text($otherPost->title, $post->title, $percent);

                if ($percent >= 80) {
                    $relatedPost->push($otherPost);
                    if ($relatedPost->count() == $count) {
                        return $relatedPost->shuffle();
                    }
                }
            }
        }
        if ($relatedPost->count() < $count) {
            $sameCategoryPosts = Post::where('blog_category_id', $post->blog_category_id)
                ->whereNot('id', $post->id)
                ->whereNotIn('id', $relatedPost->pluck('id')->toArray())
                ->take($count - $relatedPost->count())
                ->get();
            $relatedPost = $relatedPost->concat($sameCategoryPosts);
        }
        return $relatedPost->shuffle();
    }
}
