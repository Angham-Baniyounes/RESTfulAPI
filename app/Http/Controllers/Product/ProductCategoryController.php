<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\ApiController;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductCategoryController extends ApiController
{
    public function index(Product $product)
    {
        $categories = $product->categories;

        return $this->showAll($categories);
    }

    public function update(Request $request, Product $product, Category $category)
    {
        // interact with many to many relationship # attach, sync, syncWithoutDetaching

        // $product->categories()->attach([$category->id]); # add new with repeated value
        // $product->categories()->sync([$category->id]); #  add new but remove another (all old data)
        $product->categories()->syncWithoutDetaching([$category->id]); # add new without removing previous one

        return $this->showAll($product->categories);
    }

    public function destroy(Product $product, Category $category)
    {
        if(! $product->categories()->find($category->id)) {
            return $this->errorResponse('The specified category is not a category of this product', 404);
        }

        # detach() for delete the relationship
        $product->categories()->detach($category->id);

        return $this->showAll($product->categories);
    }
}
