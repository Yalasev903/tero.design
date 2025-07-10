<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController extends Controller
{
    public function store(Request $request)
    {
        // Email зашит жёстко, независимо от того, что введёт пользователь
        $fixedEmail = 'dmr.ter@gmail.com';

        $status = Password::sendResetLink(['email' => $fixedEmail]);

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Ссылка отправлена на почту.'])
            : response()->json(['message' => 'Не удалось отправить ссылку.'], 500);
    }
}
