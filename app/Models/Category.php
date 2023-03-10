<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'en_name',
        'slug',
        'en_description',
        'ar_name',
        'ar_description',
        'status',
        'order',
    ];

    protected $appends = [
        'image_path',
        'image_url',
    ];

    // public function image(): MorphOne
    // {
    //     return $this->morphOne(Image::class, 'imageable');
    // }

    /**
     * @return HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function getImagePathAttribute()
    {
        $image_model = $this->image;
        if (empty($image_model->file_path)):
            $image_path = null;
        else:
            $image_path = $image_model->file_path;
        endif;

        return $image_path;
    }

    public function getImageUrlAttribute()
    {
        if (empty($this->image_path)):
            $image_url = null;
        else:
            $image_url = url('/uploads/' . $this->image_path);
        endif;

        return $image_url;
    }



}
