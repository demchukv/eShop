<?php

namespace App\Livewire\MyAccount;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Favorites extends Component
{
    protected $listeners = ['refreshComponent'];

    public function render()
    {
        $store_id = session('store_id');
        $user = Auth::user();

        $limit = 10;
        $offset =  0;
        $total = 0;

        $res = getFavorites(user_id:$user->id,store_id:$store_id);

        return view('livewire.' . config('constants.theme') . '.my-account.favorites', [
            'user_info' => $user,
            'regular_wishlist' => $res['regular_product'],
            'combo_wishlist' => $res['combo_products'],
        ])->title("Wishlist |");
    }
    public function refreshComponent()
    {
        $this->dispatch('$refresh');
    }
}
