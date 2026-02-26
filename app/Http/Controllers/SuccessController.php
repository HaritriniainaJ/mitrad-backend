<?php
namespace App\Http\Controllers;
use App\Models\Success;
use Illuminate\Http\Request;

class SuccessController extends Controller {
    public function index(Request $request) {
        return response()->json(
            Success::where('user_id', $request->user()->id)
                ->orderByDesc('date')->get()
        );
    }

    public function store(Request $request) {
        $request->validate(['title' => 'required|string', 'date' => 'required|date']);

        // Éviter doublons pour succès automatiques
        if ($request->badge_key) {
            $existing = Success::where('user_id', $request->user()->id)
                ->where('badge_key', $request->badge_key)->first();
            if ($existing) return response()->json($existing, 200);
        }

        $success = Success::create([
            'user_id'   => $request->user()->id,
            'title'     => $request->title,
            'date'      => $request->date,
            'note'      => $request->note,
            'images'    => $request->images ?? [],
            'type'      => $request->type ?? 'manual',
            'badge_key' => $request->badge_key,
        ]);
        return response()->json($success, 201);
    }

    public function update(Request $request, $id) {
        $success = Success::where('user_id', $request->user()->id)->findOrFail($id);
        $success->update($request->only(['title', 'date', 'note', 'images']));
        return response()->json($success);
    }

    public function destroy(Request $request, $id) {
        $success = Success::where('user_id', $request->user()->id)->findOrFail($id);
        $success->delete();
        return response()->json(['message' => 'Supprimé']);
    }
}