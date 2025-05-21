<?php

namespace App\Livewire\MyAccount;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\CommissionDistribution;

class Wallet extends Component
{
    protected $listeners = ['refreshComponent'];

    public function render()
    {
        $user = Auth::user();
        $user = $this->get_user_info();
        $payment_method = getSettings('payment_method', true, true);
        $payment_method = json_decode($payment_method);

        // Отримуємо загальну суму очікуваних платежів
        $pendingTotal = CommissionDistribution::where('user_id', $user->id)
            ->pending()
            ->sum('amount');

        return view('livewire.' . config('constants.theme') . '.my-account.wallet', [
            'user_info' => $user,
            'payment_method' => $payment_method,
            'pending_total' => $pendingTotal
        ])->title("Wallet |");
    }


    public function get_user_info()
    {
        return Auth::user();
    }

    public function refreshComponent()
    {
        $this->dispatch('$refresh');
    }
}
