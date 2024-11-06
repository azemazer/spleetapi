<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Receipt;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'group' => 'required|integer',
            'products' => 'required|array',
            'products.*.name' => 'required|string',
            'products.*.price' => 'required|integer',
        ]);
        $receipt = Receipt::create([
            "name" => $validated["name"],
            "group_id" => $validated["group"]
        ]);
        $receipt->users()->attach($request->user(), [
            "creator" => true
        ]);

        foreach($validated["products"] as $product){
            $additional_product_array = [
                "receipt_id" => $receipt->id
            ];
            $product = array_merge($product, $additional_product_array);
            Product::create([
                "name" => $product["name"],
                "price" => $product["price"],
                "receipt_id" => $receipt->id,
            ]);
        }

        $final_receipt = Receipt::with('products')
        ->where('id', $receipt->id)
        ->firstOrFail();
        return response($final_receipt);
    }

    /**
     * Display the specified resource.
     */
    public function show(Receipt $receipt, $id)
    {
        $receipt = Receipt::with('users', 'products')
        ->where('id', $id)
        ->firstOrFail();

        return response($receipt);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Receipt $receipt)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Receipt $receipt, $id)
    {
        $receipt = Receipt::findOrFail($id);
        $receipt->delete();
        return response('Deleted.');
    }
}
