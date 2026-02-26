<?php

namespace App\Http\Controllers;

use App\Models\TradingAccount;
use Illuminate\Http\Request;

class TradingAccountController extends Controller
{
    public function index(Request $request)
    {
        $accounts = $request->user()->tradingAccounts;
        return response()->json($accounts);
    }

public function store(Request $request)
{
    $request->validate([
        'name'    => 'required|string|max:255',
        'capital' => 'nullable|numeric|min:0',
        'broker'  => 'nullable|string|max:255',
        'type'    => 'nullable|string|max:255',
    ]);

    $account = $request->user()->tradingAccounts()->create([
        'name'    => $request->name,
        'capital' => $request->capital ?? 10000,
        'broker'  => $request->broker ?? '',
        'type'    => $request->type ?? 'Personnel',
    ]);

    return response()->json($account, 201);
}

public function update(Request $request, $id)
{
    $account = $request->user()->tradingAccounts()->findOrFail($id);

    $request->validate([
        'name'    => 'nullable|string|max:255',
        'capital' => 'nullable|numeric|min:0',
        'broker'  => 'nullable|string|max:255',
        'type'    => 'nullable|string|max:255',
    ]);

    $account->update($request->only(['name', 'capital', 'broker', 'type']));

    return response()->json($account);
}

    public function destroy(Request $request, $id)
    {
        $account = $request->user()->tradingAccounts()->findOrFail($id);
        $account->delete();
        return response()->json(['message' => 'Compte supprimé']);
    }
}