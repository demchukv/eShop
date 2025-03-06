<?php

namespace App\Livewire\Header;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Header extends Component
{
    protected $listeners = ['cart_count', 'changeLang', 'changeCurrency'];

    public $cart_count = "";

    public $user_id = "";
    public function __construct()
    {
        $this->user_id = Auth::user() != '' ? Auth::user()->id : NUll;
    }

    public function cart_count($cart_count)
    {
        $this->cart_count = $cart_count;
    }

    public function mount()
    {
        $store_id = session('store_id');
        if ($store_id == "") {
            abort(503);
        }
    }

    public function render()
    {
        $settings = getSettings('web_settings', true, true);
        $settings = json_decode($settings);

        $currencies = fetchDetails('currencies') ?? [];

        $languages = fetchDetails('languages') ?? [];

        $store_details = fetchDetails('stores', ['status' => 1], '*');
        return view('components.header.header', [
            'settings' => $settings,
            'currencies' => $currencies,
            'languages' => $languages,
            'stores' => $store_details,
        ]);
    }

    public function changeLang($lang)
    {
        if ($lang != "") {
            $is_rtl = fetchdetails('languages', ['code' => $lang], 'is_rtl');
            $is_rtl = isset($is_rtl) && !empty($is_rtl) ? $is_rtl[0]->is_rtl : '';
            app()->setLocale($lang);
            session()->put('locale', $lang);
            session()->put('is_rtl', $is_rtl);
            return $this->redirect(" ", true);
        }
    }
    public function changeCurrency($currency)
    {
        if ($currency != "") {
            session()->put('currency', $currency);
            return $this->redirect(" ", true);
        }
    }
}
