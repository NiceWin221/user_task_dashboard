<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
  public function index()
  {
    $user = User::all();
    return response()->json($user);
  }

  public function getUserByToken(Request $request)
  {
    // Mengambil user berdasarkan token
    $user = Auth::user();

    // Jika user tidak ditemukan (misalnya token tidak valid)
    if (!$user) {
      return response()->json(['message' => 'Unauthorized'], 401);
    }

    // Mengembalikan data user
    return response()->json($user);
  }

  public function getUserById($id)
  {
    // Cek apakah pengguna dengan ID tersebut ada
    $user = User::find($id);

    if (!$user) {
      return response()->json([
        'message' => 'User not found',
      ], 404);
    }

    return response()->json($user);
  }
}
