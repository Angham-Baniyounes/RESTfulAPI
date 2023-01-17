<?php

namespace App\Models;

use App\Transformers\ProductTransformer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    public $transformer = ProductTransformer::class;

    use HasFactory;

    use SoftDeletes;

    const AVAILABLE_PRODUCT = 'available';
    const UNAVAILABLE_PRODUCT = 'unavailable';

    protected $fillable = [
        'name',
        'description',
        'quantity',
        'status',
//        'image',
        'seller_id',
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = [
        'pivot'
    ];

    public function isAvailable() {
        return $this->status == Product::AVAILABLE_PRODUCT;
    }

    public function categories() {
        return $this->belongsToMany(Category::class);
    }

    public function seller() {
        return $this->belongsTo(Seller::class);
    }

    public function transactions() {
        return $this->hasMany(Transaction::class);
    }
}
