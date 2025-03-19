<?php

namespace App\Livewire\MyAccount;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ReferralStats extends Component
{
    public $referralStats = [];
    public $user_info;

    public function mount()
    {
        $this->user_info = Auth::user();
        if (!$this->user_info) {
            return redirect()->route('login');
        }
        $this->loadReferralStats();
    }

    public function loadReferralStats()
    {
        // Отримуємо рефералів поточного користувача і групуємо за ролями
        $user = Auth::user();
        $this->referralStats = $user->referrals()
            ->with('role') // Завантажуємо пов'язану роль
            ->get()
            ->groupBy(function ($referral) {
                return $referral->role->name ?? 'unknown'; // Групуємо за назвою ролі
            })
            ->map(function ($group) {
                return $group->count(); // Підраховуємо кількість у кожній групі
            })
            ->only(['members', 'dealer', 'manager', 'seller']) // Обмежуємо ролями members, dealer, manager
            ->all();

        // dd($user->referrals()->toSql(), $user->referrals()->getBindings());
    }

    public function render()
    {
        // dd($this->referralStats);
        return view('livewire.' . config('constants.theme') . '.my-account.referral-stats', [
            'user_info' => $this->user_info,
            'referralStats' => $this->referralStats,
        ])->title("Referral stats |");
    }
}
