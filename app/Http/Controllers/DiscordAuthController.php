<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
class DiscordAuthController extends Controller
{
        public function redirect()
    {
        return Socialite::driver('discord')
            ->scopes(['identify', 'email', 'guilds'])
            ->with(['prompt' => 'none'])
            ->stateless()
            ->redirect();
    }

    public function callback()
    {
        try {
            $discordUser = Socialite::driver('discord')->stateless()->user();
            $serverId = env('DISCORD_SERVER_ID');
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $discordUser->token,
            ])->get('https://discord.com/api/users/@me/guilds');
            $guilds = $response->json();
            Log::info('Discord guilds response', [
                'guilds' => $guilds,
                'serverId' => $serverId,
                'status' => $response->status()
            ]);
            $isMember = true;
            if (!$isMember) {
                return redirect('https://mi-trad-work.vercel.app/login?error=not_member');
            }
            $email = $discordUser->getEmail();
            $discordId = $discordUser->getId();
            Log::info('Discord user info', ['email' => $email, 'discordId' => $discordId]);
            $user = User::where('discord_id', $discordId)->first();
            if (!$user && $email) {
                $user = User::where('email', $email)->first();
            }
            Log::info('User found', ['user' => $user?->id]);
            if ($user) {
                $user->update([
                    'discord_id' => $discordId,
                    'avatar' => $discordUser->getAvatar() ?? $user->avatar,
                ]);
            } else {
                $user = User::create([
                    'name' => $discordUser->getName() ?? $discordUser->getNickname() ?? 'Discord User',
                    'email' => $email ?? $discordId . '@discord.local',
                    'discord_id' => $discordId,
                    'avatar' => $discordUser->getAvatar(),
                    'password' => bcrypt(Str::random(32)),
                ]);
            }
            $token = $user->createToken('auth_token')->plainTextToken;
            Log::info('Token created', ['token' => substr($token, 0, 10)]);
        $userData = urlencode(json_encode([
            'id'            => $user->id,
            'name'          => $user->name,
            'email'         => $user->email,
            'avatar'        => $user->avatar,
            'bio'           => $user->bio,
            'country'       => $user->country,
            'experience'    => $user->experience,
            'trading_style' => $user->trading_style,
            'broker'        => $user->broker,
            'banner'        => $user->banner,
            'is_public'     => $user->is_public,
            'password_set'  => $user->password_set,
            'favorite_pairs'=> $user->favorite_pairs,
            'custom_setups' => $user->custom_setups,
        ]));
            return redirect('https://mi-trad-work.vercel.app/login?token=' . urlencode($token) . '&user=' . $userData);
        } catch (\Exception $e) {
            Log::error('Discord error: ' . $e->getMessage());
            return redirect('https://mi-trad-work.vercel.app/login?error=discord_error&msg=' . urlencode($e->getMessage()));
        }
    }
}