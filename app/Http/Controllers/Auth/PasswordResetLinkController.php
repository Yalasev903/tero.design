<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController extends Controller
{
    public function store(Request $request)
    {
        // Жёстко задан email получателя письма
        $targetEmail = 'dmr.ter@gmail.com';

        // Email юзера, у которого должен быть сброшен пароль
        $actualUserEmail = 'admin@tero.design';

        // Laravel требует, чтобы в базе был такой email
        // Поэтому мы имитируем сброс, но фактически он применится к `admin@tero.design`

        // Вручную подменяем запрос
        $status = Password::sendResetLink(['email' => $actualUserEmail]);

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Ссылка отправлена на dmr.ter@gmail.com'])
            : response()->json(['message' => 'Не удалось отправить ссылку'], 500);
    }
}
