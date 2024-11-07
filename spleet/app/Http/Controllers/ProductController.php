<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'id'    => 'required|integer',
            'status'  => 'required|integer',
        ]);

        // Status: 0 = non pris en charge; 1 = pris en charge mais non payé; 2 = payé
        Product::where('id', $validated["id"])
        ->update([
            "status" => $validated["status"],
            "user_id" => $validated["status"] > 0 ? $request->user()->id : null,
        ]);

        return response("Done");
    }

}
