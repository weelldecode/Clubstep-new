<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
    use HasFactory;
    //
    protected $fillable = [
        "name",
        "slug",
        "description",
        "price",
        "file_url",
        "image_path",
        "images",
        "type",
        "features",
        "is_premium",
        "collection_id",
    ];

    protected $casts = [
        "images" => "array",
        "features" => "array",
    ];

    protected $appends = ["thumb", "preview_url"];

    // Um item pertence a uma coleção
    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function categories()
    {
        return $this->belongsToMany(
            Category::class,
            "items_categories",
            "item_id",
            "category_id",
        );
    }
    public function tags()
    {
        return $this->belongsToMany(
            Tag::class,
            "items_tag",
            "item_id", // FK do item na pivot
            "tag_id", // FK da tag na pivot
        );
    }
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, "favorites")->withTimestamps();
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function getThumbAttribute()
    {
        return $this->images[0] ?? null;
    }

    public function getPreviewUrlAttribute(): string
    {
        if (!empty($this->image_path)) {
            return asset("storage/" . ltrim((string) $this->image_path, "/"));
        }

        if (!empty($this->thumb)) {
            return asset("storage/" . ltrim((string) $this->thumb, "/"));
        }

        return asset("images/placeholders/item-default.svg");
    }

    public function is_premium(): string
    {
        return $this->is_premium ? "Exclusivo" : "Gratuito";
    }
}
