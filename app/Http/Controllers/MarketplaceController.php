<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    public function index() {
        $products = Product::select(['id', 'name', 'slug', 'description', 'price'])
            ->with(['primaryImage' => fn($q) => $q->select(['product_media.id', 'product_media.product_id', 'product_media.media_url'])])
            ->where('is_published', true)
            ->get();

        return view('marketplace.index', compact('products'));
    }
}
