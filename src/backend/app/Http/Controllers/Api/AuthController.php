<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    /**
     * 1. Daftar pengguna baharu (Register)
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        // Cipta pengguna baharu (role default ialah 'customer')
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => $validated['password'], // Automatik di-hash oleh User model casts()
            'role'     => 'customer',
        ]);

        // Jana token Sanctum (INSERT ke dalam table personal_access_tokens)
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran berjaya',
            'data'    => [
                'user'  => $user,
                'token' => $token,
            ],
        ], 201);
    }

    /**
     * 2. Log masuk pengguna (Login) & keluarkan Token
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // Cari pengguna berdasarkan email
        $user = User::where('email', $validated['email'])->first();

        // Semak sama ada pengguna wujud DAN password sepadan dengan hash dalam database
        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau kata laluan tidak sah',
            ], 401);
        }

        // Jana token baharu untuk sesi log masuk ini
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Log masuk berjaya',
            'data'    => [
                'user'  => $user,
                'token' => $token,
            ],
        ]);
    }

    /**
     * 3. Dapatkan maklumat pengguna semasa (Current User Profile)
     */
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Maklumat profil pengguna',
            'data'    => $request->user(),
        ]);
    }

    /**
     * 4. Kemas kini profil pengguna sendiri (Update Own Profile)
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => [
                'required',
                'string',
                'email',
                'max:255',
                // Benarkan pengguna kekalkan email sendiri tanpa ralat unique
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:6',
        ]);

        $user->name  = $validated['name'];
        $user->email = $validated['email'];

        // Jika pengguna isi password baharu, baru kita kemas kini password
        if (!empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil berjaya dikemaskini',
            'data'    => $user,
        ]);
    }

    /**
     * 5. Log keluar (Logout) & batalkan token semasa
     */
    public function logout(Request $request)
    {
        // Padam token yang sedang digunakan dalam request ini sahaja
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Log keluar berjaya',
        ]);
    }
}