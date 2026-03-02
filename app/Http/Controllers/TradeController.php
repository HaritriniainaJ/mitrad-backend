<?php
namespace App\Http\Controllers;
use App\Models\Trade;
use Illuminate\Http\Request;

class TradeController extends Controller
{
    public function index(Request $request, $accountId)
    {
        $account = $request->user()->tradingAccounts()->findOrFail($accountId);
        return response()->json($account->trades()->orderBy('date', 'desc')->get());
    }

    public function store(Request $request, $accountId)
    {
        $account = $request->user()->tradingAccounts()->findOrFail($accountId);

        $request->validate([
            'pair'         => 'required|string',
            'direction'    => 'required|in:BUY,SELL',
            'entry_price'  => 'required|numeric',
            'stop_loss' => 'nullable|numeric',
            'take_profit'  => 'nullable|numeric',
            'lot_size'     => 'nullable|numeric',
            'exit_price'   => 'nullable|numeric',
            'result_r'     => 'nullable|numeric',
            'result_dollar'=> 'nullable|numeric',
            'status'       => 'nullable|string',
            'date'         => 'nullable|string',
            'session'      => 'nullable|string',
            'setup'        => 'nullable|string',
            'emotion'      => 'nullable|string',
            'quality'      => 'nullable|integer',
            'entry_note'   => 'nullable|string',
            'exit_note'    => 'nullable|string',
            'trading_view_link' => 'nullable|string',
            'screenshot'   => 'nullable|string',
        ]);

        $trade = $account->trades()->create($request->all());
        return response()->json($trade, 201);
    }

    public function update(Request $request, $accountId, $tradeId)
    {
        $account = $request->user()->tradingAccounts()->findOrFail($accountId);
        $trade = $account->trades()->findOrFail($tradeId);
        $trade->update($request->all());
        return response()->json($trade);
    }

    public function importBulk(Request $request, $accountId)
    {
        $account = $request->user()->tradingAccounts()->findOrFail($accountId);
        $trades = $request->input('trades', []);
        $created = [];
        foreach ($trades as $tradeData) {
            $tradeData['is_imported'] = true;
            $created[] = $account->trades()->create($tradeData);
        }
        return response()->json($created, 201);
    }
    public function destroy(Request $request, $accountId, $tradeId)
    {
        $account = $request->user()->tradingAccounts()->findOrFail($accountId);
        $trade = $account->trades()->findOrFail($tradeId);
        $trade->delete();
        return response()->json(['message' => 'Trade supprimé']);
    }
}
