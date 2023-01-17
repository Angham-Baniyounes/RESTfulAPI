<?php

namespace App\Models;

use App\Transformers\CategoryTransformer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    public $transformer = CategoryTransformer::class;
    use HasFactory;

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $hidden = [
        'pivot'
    ];

    protected  $fillable = [
        'name',
        'description',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }


}
