<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use App\Http\Requests;
use App\ItemList;
use App\Item;
use Auth;

class ListController extends Controller
{
    public function createList(Request $request)
    {
    	$this->validate($request, [
            'title' => 'required|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'due_date' => 'required|date',
        ]);

		$list = ItemList::create([
            'title' => $request['title'],
            'user_id' => Auth::user()->id,
            'latitude' => $request['latitude'],
            'longitude' => $request['longitude'],
        ]);

        foreach ($request['names'] as $key => $item) {
            $i = new Item;
            $i->name = $item;
            $i->save();
            $list->items()->save($i, ['quantity' => $request['quantities'][$key]]);
        }

        return response()->json(['success' => true, 'list_id' => $list->id]);
    }

    public function getLists()
    {
        return ItemList::user(Auth::user()->id)->get();
    }

    public function getList($id)
    {
        $list = ItemList::find($id);

        $items = $list->items;
        foreach ($items as $item) {
            $item->quantity = floatval($item->pivot->quantity);
        }
        $list->items = $items;
        
        return $list;
    }
}
