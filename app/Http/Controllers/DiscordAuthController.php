<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DiscordAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('discord')
            ->scopes(['identify', 'email'])
            ->stateless()
            ->redirect();
    }

    public function callback(Request $request)
    {
        $code = $request->get('code');
        if (!$code) {
            return redirect('https://projournalmitrad.vercel.app/login?error=discord_error&msg=No+code');
        }

        // Eviter de traiter le meme code deux fois
        $cacheKey = 'discord_code_' . md5($code);
        if (Cache::has($cacheKey)) {
            return redirect('https://projournalmitrad.vercel.app/login?error=discord_error&msg=Code+already+used');
        }
        Cache::put($cacheKey, true, 60);

        try {
            $discordUser = Socialite::driver('discord')->stateless()->user();
            $email = $discordUser->getEmail();
            $discordId = $discordUser->getId();

            $user = User::where('discord_id', $discordId)->first();
            if (!$user && $email) {
                $user = User::where('email', $email)->first();
            }

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

            return redirect('https://projournalmitrad.vercel.app/login?token=' . urlencode($token) . '&user=' . $userData);

        } catch (\Exception $e) {
            Log::error('Discord error: ' . $e->getMessage());
            return redirect('https://projournalmitrad.vercel.app/login?error=discord_error&msg=' . urlencode($e->getMessage()));
        }
    }
}