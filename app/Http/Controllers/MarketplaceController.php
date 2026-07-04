<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;

class MarketplaceController extends Controller
{
    public function index()
    {
        $categories = ProductCategory::orderBy('name')->get();

        $products = Product::select(['id', 'name', 'slug', 'description', 'price', 'product_category_id'])
            ->with([
                'primaryImage' => fn ($q) => $q->select(['product_media.id', 'product_media.product_id', 'product_media.media_url']),
                'ecommerceLinks',
                'category',
            ])
            ->where('is_published', true)
            ->get();

        return view('marketplace.index', compact('products', 'categories'));
    }

    public function detail($slug)
    {
        $product = Product::with(['media', 'ecommerceLinks', 'category'])
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return view('marketplace.show', compact('product'));
    }
}
