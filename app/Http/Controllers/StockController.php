<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index()
    {
        $stocks = \App\Models\Stock::with('product')->paginate(10);
        return view('stock.index', ['stocks'=>$stocks,'page'=>'Stocks des produits']);
    }
}
