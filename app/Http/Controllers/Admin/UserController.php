<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThemeVsc;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::query()
            ->when(request('query'), function ($query, $searchQuery) {
                $query->where('name', 'like', "%{$searchQuery}%");
            })
            ->latest()
            ->paginate(setting('pagination_limit'));

        return $users;
    }

    public function listuser()
    {
        $data = User::where('is_active', 1)
        ->get();

        return response()->json($data);
    }
    public function store()
    {
        request()->validate([
            'name' => 'required|unique:users,name',
            'email' => 'required|unique:users,email',
            'password' => 'required|min:8',
            'first_name' => 'required',
            'last_name' => 'required',
            'position' => 'required',
            'gender' => 'required',
        ]);

        $user = User::create([
            'name' => request('name'),
            'email' => request('email'),
            'password' => bcrypt(request('password')),
            'first_name' => request('first_name'),
            'last_name' => request('last_name'),
            'position' => request('position'),
            'gender' => request('gender'),
        ]);

        ThemeVsc::create([
            'userid' => $user->id,
            'background' => '#B98D65',
            'active_background' => '#72461F'

        ]);

        return $user;
    }

    public function update(User $user)
    {
        request()->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email,'.$user->id,
            'password' => 'sometimes|min:8',
            'first_name' => 'required',
            'last_name' => 'required',
            'position' => 'required',
            'gender' => 'required'

        ]);

        $user->update([
            'name' => request('name'),
            'email' => request('email'),
            'password' => request('password') ? bcrypt(request('password')) : $user->password,
            'email' => request('email'),
            'first_name' => request('first_name'),
            'last_name' => request('last_name'),
            'position' => request('position'),
            'gender' => request('gender')

        ]);

        return $user;
    }


    public function destory(User $user)
    {
        $user->delete();

        return response()->noContent();
    }

    public function changesitehead(User $user)
    {
        $user->update([
            'sitehead_user_id' => request('sitehead_user_id'),
        ]);

        return response()->json(['success' => true]);
    }

    public function changeRole(User $user)
    {
        $user->update([
            'role' => request('role'),
        ]);

        return response()->json(['success' => true]);
    }

    public function bulkDelete()
    {
        User::whereIn('id', request('ids'))->delete();

        return response()->json(['message' => 'Users deleted successfully!']);
    }
}
