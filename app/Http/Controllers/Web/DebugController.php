<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Domain\Entities\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class DebugController extends Controller
{
    /**
     * Sistemdəki istifadəçi məlumatlarını yoxlama
     */
    public function checkUser()
    {
        $email = 'mektebadmin@infoline.edu.az';
        $user = User::where('email', $email)->first();
        
        if ($user) {
            // Log-a istifadəçi məlumatları yazılır
            Log::info('User Debug', [
                'id' => $user->id,
                'email' => $user->email,
                'user_type' => $user->user_type,
                'password_hash' => $user->password
            ]);

            return response()->json([
                'user_exists' => true,
                'user_details' => $user->toArray()
            ]);
        } 

        Log::warning('User Not Found', ['email' => $email]);
        return response()->json(['user_exists' => false]);
    }

    /**
     * Şifrənin düzgünlüyünü yoxlama
     */
    public function checkPassword()
    {
        $email = 'mektebadmin@infoline.edu.az';
        $plainPassword = 'Admin123!'; // Real şifrəni daxil edin

        $user = User::where('email', $email)->first();
        
        if ($user) {
            $passwordCheck = Hash::check($plainPassword, $user->password);

            Log::info('Password Check', [
                'email' => $email,
                'password_match' => $passwordCheck
            ]);

            return response()->json([
                'user_found' => true,
                'password_correct' => $passwordCheck
            ]);
        } 

        Log::warning('User Not Found for Password Check', ['email' => $email]);
        return response()->json(['user_found' => false]);
    }
}