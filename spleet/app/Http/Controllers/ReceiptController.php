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

        foreach ($validated["products"] as $product) {
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
        // Transformer to transform price
        foreach ($final_receipt->products as $product){
            $product->price = floatval($product->price) / 100;
        }
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
        $validated = $request->validate([
            'id' => 'required|integer',
            'name' => 'required|string',
            'image' => 'nullable|string',  // Image est une chaîne de caractères (base64)
            'removed_ids' => 'nullable|array',
            'products' => 'required|array',
            'products.*.name' => 'required|string',
            'products.*.price' => 'required|numeric',
            'products.*.id' => 'nullable|integer',
            'products.*.status' => 'nullable|integer',
        ]);
    
        // Met à jour le nom du ticket et l'image si présente
        Receipt::where('id', $validated["id"])
            ->update([
                "name" => $validated['name'],
                "image" => isset($validated["image"]) ? $validated["image"] : null,  // Utilisation de validated["image"]
            ]);
    
        // Supprime les relations produit-ticket qui sont à supprimer
        if (!empty($validated['removed_ids'])) {
            Product::destroy($validated['removed_ids']);
        }
    
        // Boucle sur les produits pour créer ou mettre à jour
        foreach ($validated["products"] as $product) {
            if (!empty($product["id"])) {
                // Met à jour le produit avec son statut
                Product::where('id', $product["id"])
                    ->update([
                        "name" => $product["name"],
                        "price" => $product["price"],
                        "status" => $product["status"], // Met à jour le statut
                    ]);
            } else {
                // Crée un nouveau produit avec son statut
                Product::create([
                    "name" => $product["name"],
                    "price" => $product["price"],
                    "receipt_id" => $validated["id"],
                    "status" => $product["status"], // Attribue le statut au nouveau produit
                ]);
            }
        }
    
        // Récupère les données mises à jour avec les relations
        $updated_receipt = Receipt::with('users', 'products')
            ->where('id', $validated["id"])
            ->firstOrFail();
        return response($updated_receipt);
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
