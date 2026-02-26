<?php
namespace App\Http\Controllers;
use App\Models\DailyAnalysis;
use Illuminate\Http\Request;

class DailyAnalysisController extends Controller {
    public function index(Request $request) {
        $analyses = DailyAnalysis::where('user_id', $request->user()->id)
            ->orderByDesc('date')->get();
        return response()->json($analyses);
    }
public function store(Request $request) {
    $request->validate([
        'date'  => 'required|date',
        'pairs' => 'required|array',
    ]);
    $existing = DailyAnalysis::where('user_id', $request->user()->id)
        ->where('date', $request->date)->first();
    if ($existing) {
        return response()->json(['message' => 'Analyse déjà existante pour cette date'], 422);
    }
    $analysis = DailyAnalysis::create([
        'user_id' => $request->user()->id,
        'date'    => $request->date,
        'title'   => $request->title,
        'pairs'   => $request->pairs,
    ]);
    return response()->json($analysis, 201);
}

public function update(Request $request, $id) {
    $analysis = DailyAnalysis::where('user_id', $request->user()->id)->findOrFail($id);
    $analysis->update([
        'pairs' => $request->pairs,
        'title' => $request->title,
    ]);
    return response()->json($analysis);
}

    public function destroy(Request $request, $id) {
        $analysis = DailyAnalysis::where('user_id', $request->user()->id)->findOrFail($id);
        $analysis->delete();
        return response()->json(['message' => 'Supprimé']);
    }
}