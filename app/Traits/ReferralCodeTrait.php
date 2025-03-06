<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

trait ReferralCodeTrait
{
    public $REFERRAL_CODE_LENGTH = 12;
    public $MAX_GENERATION_ATTEMPTS = 10;

    private function generateUniqueReferralCode()
    {
        $attempts = 0;
        do {
            $code = Str::random($this->REFERRAL_CODE_LENGTH);
            $attempts++;

            if ($attempts >= $this->MAX_GENERATION_ATTEMPTS) {
                throw new \Exception('Unable to generate unique referral code after ' . $this->MAX_GENERATION_ATTEMPTS . ' attempts');
            }
        } while (User::where('referral_code', $code)->exists());

        return $code;
    }

    private function validateReferralCode($code)
    {
        if (empty($code)) {
            return true;
        }

        return preg_match('/^[A-Za-z0-9]+$/', $code) && strlen($code) == $this->REFERRAL_CODE_LENGTH;
    }

    private function generateAndSaveReferralCode($user)
    {
        if (empty($user->referral_code)) {
            try {
                $user->referral_code = $this->generateUniqueReferralCode();
                $user->save();
            } catch (\Exception $e) {
                Log::error('Failed to generate referral code: ' . $e->getMessage());
            }
        }
    }
}
