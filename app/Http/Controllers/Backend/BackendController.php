<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class BackendController extends Controller
{
    //

    public function dashboard(){
        $count_of_products = Product::count();
        $count_of_categories = Category::count();
        $count_of_orders = Order::count();
        return view('dashboard', compact('count_of_products', 'count_of_orders', 'count_of_categories'));

    }
}
