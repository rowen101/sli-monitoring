<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;

class ClientController extends Controller
{

    public function viewclient()
    {
      return Client::latest()->get();
    }

    public function index()
    {
        $client = Client::query()
        ->when(request('query'), function ($query, $searchQuery) {
            $query->where('first_name', 'like', "%{$searchQuery}%");
        })
        ->latest()
        ->paginate(setting('pagination_limit'));

         return $client;

    }

    public function store()
    {
        request()->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:users,email'

        ]);

        return Client::create([
            'first_name' => request('first_name'),
            'last_name' => request('last_name'),
            'email' => request('email')

        ]);
    }

    public function update(Client $client)
    {
        request()->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:users,email,'.$client->id,

        ]);

        $client->update([
            'first_name' => request('first_name'),
            'last_name' => request('last_name'),
            'email' => request('email'),

        ]);

        return $client;
    }

    public function destory(Client $client)
    {
        $client->delete();

        return response()->noContent();
    }

    public function bulkDelete()
    {
        Client::whereIn('id', request('ids'))->delete();

        return response()->json(['message' => 'Clients deleted successfully!']);
    }
}
