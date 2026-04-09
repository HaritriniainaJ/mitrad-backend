<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    private function checkAdmin(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->is_admin) {
            abort(403, 'Accès refusé');
        }
    }

    public function users(Request $request)
    {
        $this->checkAdmin($request);

        $users = User::select(
            'id', 'name', 'email', 'is_admin', 'is_active',
            'provider', 'avatar', 'country', 'broker',
            'experience', 'trading_style', 'is_public', 'created_at'
        )->orderBy('created_at', 'desc')->get();

        $discord  = $users->where('provider', 'discord')->values();
        $classic  = $users->where('provider', '!=', 'discord')
                          ->whereNull('provider')
                          ->merge($users->where('provider', null))
                          ->unique('id')->values();

        return response()->json([
            'discord' => $discord,
            'classic' => $classic,
            'total'   => $users->count(),
        ]);
    }

    public function updateUser(Request $request, $id)
    {
        $this->checkAdmin($request);

        $user = User::findOrFail($id);
        $data = $request->validate([
            'is_active' => 'sometimes|boolean',
            'is_admin'  => 'sometimes|boolean',
            'is_public' => 'sometimes|boolean',
        ]);

        $user->update($data);
        return response()->json(['success' => true, 'user' => $user]);
    }

    public function deleteUser(Request $request, $id)
    {
        $this->checkAdmin($request);

        $user = User::findOrFail($id);
        if ($user->id === $request->user()->id) {
            return response()->json(['error' => 'Impossible de se supprimer soi-même'], 400);
        }

        $user->delete();
        return response()->json(['success' => true]);
    }
}