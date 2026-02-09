<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    //
    protected $fillable = ["name", "image", "slug", "description", "type", "parent_id"];

    public function items()
    {
        return $this->belongsToMany(Item::class, "items_categories");
    }
    
    public function collections()
    {
        return $this->belongsToMany(
            Collection::class,
            "category_collection",
            "category_id",
            "collection_id",
        );
    }

    /**
     * Compatibilidade com dados legados no pivot item_collection.
     */
    public function legacyCollections()
    {
        return $this->belongsToMany(
            Collection::class,
            "item_collection",
            "parent_id",
            "collection_id",
        )->wherePivot("type", "category");
    }
    public function featuredItem()
    {
        return $this->belongsToMany(Item::class, "items_categories")
            ->latest() // baseado em created_at do item
            ->limit(1);
    }

    public function childrenRecursive()
    {
        return $this->children()->with("childrenRecursive");
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, "parent_id");
    }

    public function children()
    {
        return $this->hasMany(Category::class, "parent_id");
    }

    public function getImageUrlAttribute(): string
    {
        if (!empty($this->image)) {
            return asset("storage/" . ltrim((string) $this->image, "/"));
        }

        if ($this->relationLoaded("collections")) {
            $firstCollection = $this->collections->first();
            if ($firstCollection) {
                return $firstCollection->cover_url;
            }
        }

        if ($this->relationLoaded("legacyCollections")) {
            $firstLegacyCollection = $this->legacyCollections->first();
            if ($firstLegacyCollection) {
                return $firstLegacyCollection->cover_url;
            }
        }

        return asset("images/placeholders/category-default.svg");
    }
}
