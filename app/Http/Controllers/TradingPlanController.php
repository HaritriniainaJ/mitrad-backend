<?php
// FICHIER : app/Http/Controllers/TradingPlanController.php

namespace App\Http\Controllers;

use App\Models\TradingPlanRule;
use App\Models\TradingPlanChecklist;
use Illuminate\Http\Request;

class TradingPlanController extends Controller
{
    // ──────────────────────────────────────────────────────────────────────
    // RÈGLES
    // ──────────────────────────────────────────────────────────────────────

    /** GET /api/plan — liste toutes les règles de l'user connecté */
    public function index(Request $request)
    {
        $rules = TradingPlanRule::where('user_id', $request->user()->id)
            ->orderBy('order')
            ->orderBy('created_at')
            ->get();

        return response()->json($rules);
    }

    /** POST /api/plan — crée une nouvelle règle */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'images'      => 'nullable|array',
            'order'       => 'nullable|integer',
        ]);

        $rule = TradingPlanRule::create([
            'user_id'     => $request->user()->id,
            'title'       => $data['title'],
            'description' => $data['description'] ?? '',
            'images'      => $data['images'] ?? [],
            'order'       => $data['order'] ?? 0,
        ]);

        return response()->json($rule, 201);
    }

    /** PUT /api/plan/{id} — modifie une règle */
    public function update(Request $request, $id)
    {
        $rule = TradingPlanRule::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $data = $request->validate([
            'title'       => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'images'      => 'sometimes|nullable|array',
            'order'       => 'sometimes|nullable|integer',
        ]);

        $rule->update($data);

        return response()->json($rule);
    }

    /** DELETE /api/plan/{id} — supprime une règle */
    public function destroy(Request $request, $id)
    {
        $rule = TradingPlanRule::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $rule->delete();

        return response()->json(['message' => 'Règle supprimée']);
    }

    // ──────────────────────────────────────────────────────────────────────
    // CHECKLIST QUOTIDIENNE
    // ──────────────────────────────────────────────────────────────────────

    /** GET /api/plan/checklist/{date} — récupère la checklist d'un jour */
    public function getChecklist(Request $request, $date)
    {
        $checklist = TradingPlanChecklist::where('user_id', $request->user()->id)
            ->where('date', $date)
            ->first();

        return response()->json([
            'date'       => $date,
            'checkedIds' => $checklist ? $checklist->checked_ids : [],
        ]);
    }

    /** POST /api/plan/checklist — sauvegarde (upsert) la checklist du jour */
    public function saveChecklist(Request $request)
    {
        $data = $request->validate([
            'date'       => 'required|date',
            'checkedIds' => 'required|array',
        ]);

        $checklist = TradingPlanChecklist::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'date'    => $data['date'],
            ],
            [
                'checked_ids' => $data['checkedIds'],
            ]
        );

        return response()->json([
            'date'       => $data['date'],
            'checkedIds' => $checklist->checked_ids,
        ]);
    }
}
