<?php
// app/Http/Middleware/HandleReferralCode.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use App\Traits\ReferralCodeTrait;
use Symfony\Component\HttpFoundation\Response;

class HandleReferralCode
{
    use ReferralCodeTrait;

    /**
     * Обробка вхідного запиту для відстеження referral_code.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Отримуємо referral_code із запиту (GET-параметр)
        $referralCode = $request->query('referral_code');

        if ($referralCode && !auth()->check() && $this->validateReferralCode($referralCode)) {
            // Зберігаємо в сесії
            $request->session()->put('referral_code', $referralCode);

            // Зберігаємо в cookies (на 30 днів, наприклад)
            Cookie::queue('referral_code', $referralCode, 43200); // 43200 хвилин = 30 днів
        }

        return $next($request);
    }
}
