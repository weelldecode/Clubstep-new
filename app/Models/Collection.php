<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Schema;
use App\Domain\Collections\Enums\CollectionVisibility;
use App\Domain\Collections\Enums\CollectionStatus;

class Collection extends Model
{
    use HasFactory;

    protected static ?bool $hasCollectionTagTable = null;

    protected $fillable = [
        "name",
        "slug",
        "description",
        "status",
        "user_id",
        "visibility",
        "status",
        "type",
    ];

    protected $casts = [
        "visibility" => CollectionVisibility::class,
        "status" => CollectionStatus::class,
    ];

    // Uma coleção tem muitos itens
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
    {
        return $this->belongsToMany(
            Category::class,
            "category_collection",
            "collection_id",
            "category_id",
        );
    }

    /**
     * Compatibilidade com dados legados que ainda estao no pivot item_collection.
     */
    public function legacyCategories()
    {
        return $this->belongsToMany(
            Category::class,
            "item_collection",
            "collection_id",
            "parent_id",
        )->wherePivot("type", "category");
    }

    public function tags()
    {
        if (!self::hasCollectionTagTable()) {
            return $this->legacyTags();
        }

        return $this->belongsToMany(
            Tag::class,
            "collection_tag",
            "collection_id",
            "tag_id",
        );
    }

    /**
     * Compatibilidade com dados legados no pivot item_collection.
     */
    public function legacyTags()
    {
        return $this->belongsToMany(
            Tag::class,
            "item_collection",
            "collection_id", // FK do collection
            "parent_id", // FK da tag
        )->wherePivot("type", "tag");
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getCoverUrlAttribute(): string
    {
        if (!empty($this->image_path)) {
            return asset("storage/" . ltrim((string) $this->image_path, "/"));
        }

        $item = $this->relationLoaded("items")
            ? $this->items->first()
            : $this->items()->select("image_path", "images")->latest()->first();

        if ($item) {
            if (!empty($item->image_path)) {
                return asset("storage/" . ltrim((string) $item->image_path, "/"));
            }

            $images = is_array($item->images)
                ? $item->images
                : json_decode((string) $item->images, true);

            if (!empty($images[0])) {
                return asset("storage/" . ltrim((string) $images[0], "/"));
            }
        }

        return asset("images/placeholders/collection-default.svg");
    }

    private static function hasCollectionTagTable(): bool
    {
        if (self::$hasCollectionTagTable === null) {
            self::$hasCollectionTagTable = Schema::hasTable("collection_tag");
        }

        return self::$hasCollectionTagTable;
    }
}
