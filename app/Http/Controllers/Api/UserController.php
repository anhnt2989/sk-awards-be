<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = User::query()->orderBy('name');

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        $users = $query->get()->map(fn(User $u) => $this->format($u));

        return response()->json($users);
    }

    public function changePassword(Request $request, User $user): JsonResponse
    {
        $requester = $request->user();
        abort_unless($requester->isAdmin() || $requester->id === $user->id, 403, 'Không có quyền thực hiện thao tác này.');

        $data = $request->validate([
            'new_password' => 'required|string|min:6',
        ]);

        $user->update(['password' => Hash::make($data['new_password'])]);

        return response()->json(['message' => 'Đổi mật khẩu thành công.']);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        abort_unless($request->user()->isAdmin(), 403, 'Chỉ quản trị viên mới có quyền này.');

        $data = $request->validate([
            'name'     => 'sometimes|string|max:255',
            'email'    => 'sometimes|email|max:255|unique:users,email,' . $user->id,
            'role'     => 'sometimes|in:admin,judge,submitter,accountant',
            'title'    => 'sometimes|nullable|string|max:255',
            'password' => 'sometimes|string|min:6',
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return response()->json($this->format($user));
    }

    private function format(User $u): array
    {
        return [
            'id'    => $u->id,
            'name'  => $u->name,
            'email' => $u->email,
            'role'  => $u->role,
            'title' => $u->title,
        ];
    }
}
