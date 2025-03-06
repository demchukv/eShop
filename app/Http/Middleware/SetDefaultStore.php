<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class SetDefaultStore
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('SetDefaultStore middleware started', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'session_store_id' => session('store_id'),
            'session_store_slug' => session('store_slug'),
            'query_store' => $request->query('store')
        ]);

        $sqlDumpPath = base_path('eshop_plus.sql');
        $installViewPath = resource_path('views/install.blade.php');
        // Check if the installation has been completed
        if (!file_exists($sqlDumpPath) && !file_exists($installViewPath)) {

            $defaultStore = Store::where('is_default_store', 1)
                ->where('status', 1)
                ->first();

            $default_store_id = $defaultStore ? $defaultStore->id : '';
            $default_store_name = $defaultStore ? $defaultStore->name : '';
            $default_store_image = $defaultStore ? $defaultStore->image : '';
            $default_store_slug = $defaultStore ? $defaultStore->slug : '';

            // Встановлюємо дефолтний магазин тільки якщо він ще не встановлений
            if (!session()->has('store_id')) {
                Log::info('Setting default store in session', [
                    'default_store_id' => $default_store_id,
                    'default_store_slug' => $default_store_slug
                ]);
                session([
                    'store_id' => $default_store_id,
                    'store_name' => $default_store_name,
                    'store_image' => $default_store_image,
                    'store_slug' => $default_store_slug,
                    'default_store_slug' => $default_store_slug,
                ]);
            }

            if (!$request->session()->has('show_store_popup')) {
                $request->session()->put('show_store_popup', true);
            }

            // Обробляємо параметр store тільки якщо він явно вказаний
            if (isset($request->query()['store']) && ($request->query()['store'] != null)) {
                $store_slug = $request->query()['store'];
                $store = fetchDetails('stores', ['slug' => $store_slug], "*");
                Log::info('Processing store parameter', [
                    'requested_store_slug' => $store_slug,
                    'found_store' => count($store) > 0 ? $store[0]->id : null,
                    'current_session_store_id' => session('store_id')
                ]);

                if (count($store) > 0 && isset($store[0]) && $store[0]->id != session('store_id')) {
                    Log::info('Updating store in session', [
                        'old_store_id' => session('store_id'),
                        'new_store_id' => $store[0]->id,
                        'new_store_slug' => $store[0]->slug
                    ]);
                    session()->forget(['store_id', 'store_name', 'store_image', 'store_slug']);
                    session()->put('store_id', $store[0]->id);
                    session()->put('store_name', $store[0]->name);
                    session()->put('store_image', $store[0]->image);
                    session()->put('store_slug', $store[0]->slug);
                }
            }
        }
        Log::info('SetDefaultStore middleware completed', [
            'session_store_id' => session('store_id'),
            'session_store_slug' => session('store_slug')
        ]);
        return $next($request);
    }
}
