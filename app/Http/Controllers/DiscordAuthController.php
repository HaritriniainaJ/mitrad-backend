<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DiscordAuthController extends Controller
{
    public function redirect()
    {
        $params = http_build_query([
            'client_id' => env('DISCORD_CLIENT_ID'),
            'redirect_uri' => env('DISCORD_REDIRECT_URI'),
            'response_type' => 'code',
            'scope' => 'identify email guilds',
            'prompt' => 'none',
        ]);
        return redirect('https://discord.com/oauth2/authorize?' . $params);
    }

    public function callback(Request $request)
    {
        Log::info('=== DISCORD CALLBACK START ===');
        $code = $request->get('code');
        if (!$code) {
            Log::error('No code received');
            return redirect(env('FRONTEND_URL', 'https://mi-trad-work.vercel.app') . '/login?error=no_code');
        }
        try {
            Log::info('Exchanging code for token...');
            $tokenResponse = Http::timeout(30)->asForm()->post('https://discord.com/api/oauth2/token', [
                'client_id' => env('DISCORD_CLIENT_ID'),
                'client_secret' => env('DISCORD_CLIENT_SECRET'),
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => env('DISCORD_REDIRECT_URI'),
            ]);
            Log::info('Token response status: ' . $tokenResponse->status());
            if (!$tokenResponse->ok()) {
                Log::error('Token error: ' . $tokenResponse->body());
                return redirect(env('FRONTEND_URL', 'https://mi-trad-work.vercel.app') . '/login?error=token_error&msg=' . urlencode($tokenResponse->body()));
            }
            $tokenData = $tokenResponse->json();
            $accessToken = $tokenData['access_token'];
            Log::info('Got access token, fetching user...');
            $userResponse = Http::timeout(30)->withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->get('https://discord.com/api/users/@me');
            Log::info('User response status: ' . $userResponse->status());
            $discordUser = $userResponse->json();
            Log::info('Discord user: ' . json_encode(array_keys($discordUser)));
            $discordId = $discordUser['id'];
            $email = $discordUser['email'] ?? null;
            $name = $discordUser['global_name'] ?? $discordUser['username'] ?? 'Discord User';
            $avatar = isset($discordUser['avatar'])
                ? 'https://cdn.discordapp.com/avatars/' . $discordId . '/' . $discordUser['avatar'] . '.png'
                : null;
            $user = User::where('discord_id', $discordId)->first();
            if (!$user && $email) {
                $user = User::where('email', $email)->first();
            }
            if ($user) {
                $user->update(['discord_id' => $discordId, 'avatar' => $avatar ?? $user->avatar]);
            } else {
                $user = User::create([
                    'name' => $name,
                    'email' => $email ?? $discordId . '@discord.local',
                    'discord_id' => $discordId,
                    'avatar' => $avatar,
                    'password' => bcrypt(Str::random(32)),
                ]);
            }
            $token = $user->createToken('auth_token')->plainTextToken;
            Log::info('Success! Redirecting...');
            $userData = urlencode(json_encode([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'bio' => $user->bio,
                'country' => $user->country,
                'experience' => $user->experience,
                'trading_style' => $user->trading_style,
                'broker' => $user->broker,
                'banner' => $user->banner,
                'is_public' => $user->is_public,
                'password_set' => $user->password_set,
                'favorite_pairs' => $user->favorite_pairs,
                'custom_setups' => $user->custom_setups,
            ]));
            return redirect(env('FRONTEND_URL', 'https://mi-trad-work.vercel.app') . '/login?token=' . urlencode($token) . '&user=' . $userData);
        } catch (\Exception $e) {
            Log::error('Discord callback error: ' . $e->getMessage());
            return redirect(env('FRONTEND_URL', 'https://mi-trad-work.vercel.app') . '/login?error=discord_error&msg=' . urlencode($e->getMessage()));
        }
    }
}