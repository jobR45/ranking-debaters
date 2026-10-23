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

        // Use method_exists check to avoid static analyzer errors for stateless()
        $driver = Socialite::driver($provider);
        if (method_exists($driver, 'stateless')) {
            return $driver->stateless()->redirect();
        }

        return $driver->redirect();
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
            $driver = Socialite::driver($provider);
            $socialUser = method_exists($driver, 'stateless')
                ? $driver->stateless()->user()
                : $driver->user();
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
                'password' => null,
            ]);
        } else {
            // Update existing user with Social ID and Avatar if missing
            $user->update([
                $providerIdColumn => $socialUser->getId(),
                'avatar' => $user->avatar ?? $socialUser->getAvatar(),
            ]);
        }

        // Issue Sanctum API token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Encode user payload so frontend gets the correct role immediately
        $userData = urlencode(json_encode($user));

        // Redirect back to frontend SPA with BOTH token and user object
        return redirect("/?token={$token}&user={$userData}");
    }

    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken();

        // Check if it's a database token (PersonalAccessToken) and not a session transient token
        if (method_exists($token, 'delete')) {
            $token->delete();
        } else {
            // If it's a session request, revoke all tokens or clear web session
            $request->user()->tokens()->delete();
            Auth::guard('web')->logout();
        }

        return response()->json([
            'message' => 'تم تسجيل الخروج بنجاح'
        ]);
    }
}