<?php
// app/Http/Middleware/HandleReferralCode.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use App\Traits\ReferralCodeTrait;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use App\Models\ReferralCode;

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
        $referral_code_from_path = $request->route('referral_code'); // Отримуємо referral_code із шляху

        if ($referralCode && !auth()->check() && $this->validateReferralCode($referralCode)) {
            // Зберігаємо в сесії
            $request->session()->put('referral_code', $referralCode);
            // Зберігаємо в cookies (на 30 днів, наприклад)
            Cookie::queue('referral_code', $referralCode, 43200); // 43200 хвилин = 30 днів
        }

        // Обробка нового dealer_referral_code із шляху
        if ($referral_code_from_path && !auth()->check()) {
            $referral = ReferralCode::where('code', $referral_code_from_path)->with('dealer')->first();
            if ($referral) {
                $referral_data = [
                    'product_id' => $referral->product_id,
                    'dealer_id' => $referral->dealer_id,
                ];
                $request->session()->put('dealer_referral', $referral_data);
                Cookie::queue('dealer_referral', json_encode($referral_data), 43200);

                $request->session()->put('referral_code', $referral->dealer->referral_code);
                Cookie::queue('referral_code', $referral->dealer->referral_code, 43200); // 43200 хвилин = 30 днів
            } else {
                Log::warning('Invalid referral code: ' . $referral_code_from_path);
            }
        }

        return $next($request);
    }
}
