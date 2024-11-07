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
        $params = $request->all();
        $receipt = Receipt::create([
            "name" => $validated["name"],
            "group_id" => $validated["group"],
            "image" => isset($params["image"]) ? $params["image"] : null
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
    public function update(Request $request)
    {
        // dd("e");
        $validated = $request->validate([
            'id' => 'required|integer',
            'name' => 'required|string',
            'removed_ids' => 'required|array',
            'products' => 'required|array',
            'products.*.name' => 'required|string',
            'products.*.price' => 'required|integer',
            'products.*.id' => 'nullable|integer',
        ]);

        // On save le nom du receipt
        Receipt::where('id', $validated["id"])
        ->update(["name" => $validated['name']]);

        // On supprime les relations produit ticket qui sont à supprimer
        Product::destroy($validated['removed_ids']);

        // On boucle sur les produits. On ajoute les produits sans ID et on update les produits avec ID
        foreach ($validated["products"] as $product){
            if (isset($product["id"]) && $product["id"] > 0){
                Product::where('id', $product["id"])
                ->update([
                    "name" => $product["name"] ,
                    "price" => $product["price"] ,
                ]);
            } else {
                Product::create([
                    "name" => $product["name"],
                    "price" => $product["price"],
                    "receipt_id" => $validated["id"],
                ]);
            }
        }

        $updated_receipt = Receipt::with('users', 'products')
        ->where('id', $validated["id"])
        ->firstOrFail();

        return response(Receipt::find($validated["id"]));

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
