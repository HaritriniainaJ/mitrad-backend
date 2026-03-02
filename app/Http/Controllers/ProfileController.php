<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $data = $user->toArray();
        $data['has_password'] = !empty($user->password) && !str_starts_with($user->password, '$2y$') ? false : ($user->discord_id ? true : true);
        // Simple : si connecté via Discord et mot de passe aléatoire, has_password = false
        $data['has_password'] = $user->discord_id && strlen($user->password) > 0 
            ? (bool) $user->getRememberToken() !== false && strlen($user->password) > 20
            : true;
        return response()->json($data);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'name'          => 'sometimes|string',
            'bio'           => 'sometimes|string|nullable',
            'country'       => 'sometimes|string|nullable',
            'experience'    => 'sometimes|string|nullable',
            'trading_style' => 'sometimes|string|nullable',
            'broker'        => 'sometimes|string|nullable',
            'avatar'        => 'sometimes|string|nullable',
            'banner'        => 'sometimes|string|nullable',
            'is_public'     => 'sometimes|boolean',
            'favorite_pairs'=> 'sometimes|array|nullable',
            'custom_setups' => 'sometimes|array|nullable',
        ]);
        $user->update($data);
        return response()->json($user->fresh());
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);
        $user = $request->user();
        if (!Hash::check($data['current_password'], $user->password)) {
            return response()->json(['message' => 'Mot de passe actuel incorrect'], 422);
        }
        $user->update(['password' => bcrypt($data['password'])]);
        return response()->json(['message' => 'Mot de passe mis à jour']);
    }
    public function setPassword(Request $request)
    {
        $user = $request->user();
        // Seulement si l'utilisateur n'a pas encore de vrai mot de passe (connecté via Discord)
        if ($user->password_set) {
            return response()->json(['message' => 'Utilisez la route updatePassword'], 403);
        }
        $data = $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);
        $user->update(['password' => bcrypt($data['password']), 'password_set' => true]);
        return response()->json(['message' => 'Mot de passe défini avec succès']);
    }
}