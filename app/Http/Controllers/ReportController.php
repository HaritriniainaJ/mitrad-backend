<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    // Sauvegarder un rapport et retourner le token
    public function store(Request $request)
    {
        $data = $request->input('data');
        if (!$data) {
            return response()->json(['error' => 'Missing data'], 400);
        }

        // Nettoyer les anciens rapports expirés
        Report::where('expires_at', '<', now())->delete();

        $token = Str::random(32);

        Report::create([
            'token'      => $token,
            'data'       => is_string($data) ? $data : json_encode($data),
            'expires_at' => now()->addHours(24),
        ]);

        return response()->json([
            'token' => $token,
            'url'   => 'https://projournalmitrad.vercel.app/share/' . $token,
            'expires_at' => now()->addHours(24)->toISOString(),
        ]);
    }

    // Récupérer un rapport par token
    public function show(string $token)
    {
        $report = Report::where('token', $token)->first();

        if (!$report) {
            return response()->json(['error' => 'not_found'], 404);
        }

        if ($report->expires_at->isPast()) {
            $report->delete();
            return response()->json(['error' => 'expired'], 410);
        }

        return response()->json([
            'data'       => json_decode($report->data, true),
            'expires_at' => $report->expires_at->toISOString(),
        ]);
    }
}
