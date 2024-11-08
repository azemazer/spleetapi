<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $groups = $request->user()->groups;

        return response($groups);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
        ]);
        $additional_store_array = [
            'admin' => $request->user()->id,
            'code' => Str::random(10),
        ];
        $validated = array_merge($additional_store_array, $validated);
        $group = Group::create($validated);
        $group->users()->attach($request->user()->id);

        return response($group);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $group = Group::with(
            'users', 
            'payments',
            'receipts', 
            'receipts.users',
            'receipts.products',
            'receipts.products.user'
        )
        ->where('id', $id)
        ->first();

        // Transformer to show 

        return response($group);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'id'    => 'required|integer',
            'name'  => 'required|string',
        ]);
        Group::where('id', $validated['id'])
        ->update(["name" => $validated['name']]);

        return response(Group::find($validated["id"]));
    }

    /**
     * Join a group.
     */
    public function join(Request $request)
    {
        $validated = $request->validate([
            'code'  => 'required|string',
        ]);

        $group = Group::where('code', $validated['code'])->first();
        if(!$group){
            return response('No group found.');
        }

        $group->users()->sync($request->user()->id, false);
        return response($group);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $group = Group::findOrFail($id);
        $group->delete();
        return response('Deleted');
    }
}
