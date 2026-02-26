<?php
namespace App\Http\Controllers;
use App\Models\Objective;
use Illuminate\Http\Request;

class ObjectiveController extends Controller {
    public function index(Request $request) {
        return response()->json(
            Objective::where('user_id', $request->user()->id)->orderByDesc('created_at')->get()
        );
    }
    public function store(Request $request) {
        $request->validate(['text' => 'required|string']);
        $obj = Objective::create([
            'user_id'     => $request->user()->id,
            'text'        => $request->text,
            'description' => $request->description,
            'target_date' => $request->target_date,
            'image'       => $request->image,
            'completed'   => false,
        ]);
        return response()->json($obj, 201);
    }
    public function update(Request $request, $id) {
        $obj = Objective::where('user_id', $request->user()->id)->findOrFail($id);
        $obj->update($request->only(['text', 'description', 'target_date', 'image', 'completed']));
        return response()->json($obj);
    }
    public function destroy(Request $request, $id) {
        $obj = Objective::where('user_id', $request->user()->id)->findOrFail($id);
        $obj->delete();
        return response()->json(['message' => 'Supprimé']);
    }
}