<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Tag extends Model
{
    use HasFactory;
    //
    protected static ?bool $hasCollectionTagTable = null;

    protected $fillable = ["name", "slug", "description", "type", "parent_id"];

    public function parent()
    {
        return $this->belongsTo(Tag::class, "parent_id");
    }

    public function children()
    {
        return $this->hasMany(Tag::class, "parent_id");
    }

    public function collections()
    {
        if (!self::hasCollectionTagTable()) {
            return $this->legacyCollections();
        }

        return $this->belongsToMany(
            Collection::class,
            "collection_tag",
            "tag_id",
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
            "parent_id", // FK da tag
            "collection_id", // FK do collection
        )->wherePivot("type", "tag");
    }
    public function items()
    {
        return $this->belongsToMany(
            Item::class,
            "items_tag",
            "tag_id",
            "item_id",
        );
    }

    private static function hasCollectionTagTable(): bool
    {
        if (self::$hasCollectionTagTable === null) {
            self::$hasCollectionTagTable = Schema::hasTable("collection_tag");
        }

        return self::$hasCollectionTagTable;
    }
}
