<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    protected array $supportedProviders = ['google', 'discord', 'tiktok'];



    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'بيانات الدخول غير صحيحة.'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('admin_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }

    /**
     * Redirect user to OAuth Provider
     */
    public function redirect(string $provider)
    {
        if (!in_array($provider, $this->supportedProviders)) {
            return response()->json(['message' => 'مزود التسجيل غير مدعوم.'], 400);
        }

        return Socialite::driver($provider)->stateless()->redirect();
    }

    /**
     * Handle Provider Callback & Authenticate User
     */
    public function callback(string $provider)
    {
        if (!in_array($provider, $this->supportedProviders)) {
            return response()->json(['message' => 'مزود التسجيل غير مدعوم.'], 400);
        }

        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
        } catch (\Exception $e) {
            return redirect('/?error=auth_failed');
        }

        $providerIdColumn = $provider . '_id';

        // 1. Find existing user by Social ID
        $user = User::where($providerIdColumn, $socialUser->getId())->first();

        // 2. If not found, check matching email
        if (!$user && $socialUser->getEmail()) {
            $user = User::where('email', $socialUser->getEmail())->first();
        }

        // 3. Create new user if not exists
        if (!$user) {
            $user = User::create([
                'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'مستخدم ' . ucfirst($provider),
                'email' => $socialUser->getEmail() ?? $socialUser->getId() . "@{$provider}.local",
                'role' => 'user', // Default audience user
                $providerIdColumn => $socialUser->getId(),
                'avatar' => $socialUser->getAvatar(),
                'password' => null, // Social users do not require a password
            ]);
        } else {
            // Update existing user with Social ID and Avatar if missing
            $user->update([
                $providerIdColumn => $socialUser->getId(),
                'avatar' => $user->avatar ?? $socialUser->getAvatar(),
            ]);
        }

        // Log the user into Laravel session / Sanctum
        Auth::login($user, true);

        // Issue Sanctum API token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Redirect back to frontendSPA with token
        return redirect('/?token=' . $token);
    }
}