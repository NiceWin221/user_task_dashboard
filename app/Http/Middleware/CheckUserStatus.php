<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $email = $request->input('email');

        $user = User::where('email', $email)->first();

        // Cek apakah user ada dan statusnya inactive
        if ($user && $user->status === false) {
            return response()->json(['message' => 'Your account is inactive'], 403);
        }

        return $next($request);
    }
}
