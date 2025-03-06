<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserStatus;
use App\Models\Role;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class UserStatusController extends Controller
{
    public function index()
    {
        $user_status = UserStatus::with('user')->get();
        return view('admin.pages.tables.user_status', compact('user_status'));
    }

    public function list(Request $request)
    {
        $limit = $request->input('limit', 10);
        $offset = $request->input('offset', 0);
        $sort = $request->input('sort', 'id');
        $order = $request->input('order', 'desc');

        $query = UserStatus::with('user');

        // Сортування по полях з таблиці users
        if (in_array($sort, ['user_name', 'birthdate'])) {
            if ($sort === 'user_name') {
                $query->join('users', 'user_statuses.user_id', '=', 'users.id')
                    ->orderBy('users.first_name', $order)
                    ->orderBy('users.last_name', $order)
                    ->select('user_statuses.*');
            } else if ($sort === 'birthdate') {
                $query->join('users', 'user_statuses.user_id', '=', 'users.id')
                    ->orderBy('users.birthdate', $order)
                    ->select('user_statuses.*');
            }
        } else {
            $query->orderBy($sort, $order);
        }

        if ($request->input('search')) {
            $search = $request->input('search');
            $query->whereHas('user', function ($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $total = $query->count();
        $rows = $query->skip($offset)->take($limit)->get();

        $data = [];
        foreach ($rows as $row) {
            $photos = json_decode($row->photos) ?? [];
            $photoUrls = array_map(function ($photo) {
                $photo = ltrim($photo, 'storage/');
                return asset('storage/' . $photo);
            }, $photos);

            $actionLink = '<button type="button" class="btn ' . ($row->status === 'pending' ? 'btn-primary' : 'btn-info') . ' btn-sm edit-user-status"
                data-id="' . $row->id . '"
                data-status="' . $row->status . '"
                data-type="' . $row->type . '"
                data-first-name="' . $row->user->first_name . '"
                data-last-name="' . $row->user->last_name . '"
                data-email="' . $row->user->email . '"
                data-country-code="' . $row->user->country_code . '"
                data-mobile="' . ($row->user->mobile ?? "") . '"
                data-telegram="' . ($row->user->telegram_username ?? "") . '"
                data-passport="' . ($row->passport ?? "") . '"
                data-tax-id="' . ($row->tax_id ?? "") . '"
                data-message="' . $row->message . '"
                data-notes="' . ($row->notes ?? "") . '"
                data-photos=\'' . json_encode($photoUrls) . '\'
                >' . ($row->status == 'pending' ? labels('admin_labels.edit', 'Edit') : labels('admin_labels.view', 'View')) . '</button>';

            $data[] = [
                'id' => $row->id,
                'user_id' => $row->user_id,
                'user_name' => $row->user->first_name . ' ' . $row->user->last_name,
                'birthdate' => $row->user->birthdate,
                'new_role' => $row->type,
                'status' => $row->status,
                'created_at' => $row->created_at->format('Y-m-d H:i:s'),
                'operate' => $actionLink
            ];
        }

        return response()->json([
            'total' => $total,
            'rows' => $data
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:user_statuses,id',
            'status' => 'required|in:approved,rejected',
            'notes' => 'nullable|string|max:1000'
        ]);

        $userStatus = UserStatus::with('user')->findOrFail($request->id);
        $userStatus->status = $request->status;
        $userStatus->notes = $request->notes;

        if ($request->status === 'approved') {
            // Оновлюємо роль користувача
            $user = $userStatus->user;

            // Отримуємо ID ролі з таблиці roles
            $roleId = Role::where('name', $userStatus->type)->value('id');

            if ($roleId) {
                $user->role_id = $roleId;
                $user->save();
            } else {
                // Якщо роль не знайдена, повертаємо помилку
                return response()->json([
                    'success' => false,
                    'message' => labels('admin_labels.role_not_found', 'Role not found in the database')
                ], 422);
            }
        }

        $userStatus->save();

        return response()->json([
            'success' => true,
            'message' => labels('admin_labels.status_updated', 'Status updated successfully')
        ]);
    }
}
