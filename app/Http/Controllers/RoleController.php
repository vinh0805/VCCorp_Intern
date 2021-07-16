<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Exception;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function authorizeForCollection($collection)
    {
        $currentUser = $this->authLogin('admin');

        $roles = Role::query()
            ->where('collection', $collection)
            ->orderBy('id')
            ->get();

        return view('roles.roles')
            ->with('currentUser', $currentUser)
            ->with('roles', $roles)
            ->with('collection', $collection);
    }

    public function updateRole(Request $request)
    {
        $this->authLogin('admin');

        $data = $request->all();

        $error = false;

        $role_ids = array_keys($data['role']);

        // Update role
        foreach ($role_ids as $role_id) {
            $role = Role::query()
            ->find($role_id);

            if (isset($role)) {
                $role['permission_list'] = array_keys($data['role'][$role_id]);
                if (!$role->save()) {
                    $error = true;
                }
            }
        }

        // Update role with null value
        $null_roles = Role::query()
            ->where('collection', $data['collection'])
            ->whereNotIn('_id', $role_ids)
            ->get();

        foreach ($null_roles as $role) {
            $role->permission_list = [];
            if (!$role->save()) {
                $error = true;
            }
        }

        if ($error) {
            $data = [
                'message' => 'Cập nhật phân quyền thất bại!',
                'success' => false
            ];
            return response()->json($data);

        } else {
            $data = [
                'message' => 'Cập nhật phân quyền thành công!',
                'success' => true
            ];
            return response()->json($data);
        }
    }

    public function saveRole(Request $request, $_id)
    {
        $this->authLogin('admin');

        $data = $request->all();

        $role = Role::query()
            ->find($_id);

        if (!isset($role)) {
            return response()->json([
                'message' => 'Cập nhật phân quyền thất bại! Dữ liệu không tồn tại!',
                'success' => false
            ]);
        }

        $role['name'] = @$data['name'];
        $role['permission_list'] = array_keys(@$data['permission']);

        if (!$role->save()) {
            return response()->json([
                'message' => 'Cập nhật phân quyền thất bại! Có lỗi khi lưu dữ liệu!',
                'success' => false
            ]);
        } else {
            return response()->json([
                'message' => 'Cập nhật phân quyền thành công!',
                'success' => true
            ]);
        }
    }

    public function createRole(Request $request)
    {
        $this->authLogin('admin');

        $data = $request->all();

        $newRole = new Role([
            'id' => Role::getLastRoleId() + 1,
            'name' => $data['name'],
            'collection' => $data['collection'],
            'permission_list' => array_keys($data['permission'])
        ]);

        if ($newRole->save()) {
            $data = [
                'message' => 'Tạo nhóm quyền mới thành công!',
                'success' => true
            ];
            return response()->json($data);

        } else {
            $data = [
                'message' => 'Tạo nhóm quyền mới thất bại!',
                'success' => false
            ];
            return response()->json($data);
        }
    }

    public function deleteRole($_id)
    {
        $this->authLogin('isAdmin');

        $role = Role::query()->find($_id);
        if (isset($role)) {
            try {
                if ($role->delete()) {
                    $data = [
                        'message' => 'Xóa nhóm quyền thành công!',
                        'success' => true
                    ];

                    return response()->json($data);
                }
            } catch (Exception $e) {
            }
        }

        $data = [
            'message' => 'Xóa nhóm quyền thất bại! Dữ liệu không tồn tại.',
            'success' => false
        ];

        return response()->json($data);
    }

    public function getDataToEditRole($_id)
    {
        $this->authLogin('admin');

        $editRole = Role::query()->find($_id);

        if (isset($editRole)) {
            return response()->json($editRole);
        } else {
            return response()->json([
                'message' => 'Không có dữ liệu!',
                'success' => false
            ]);
        }
    }

    public function showErrorView()
    {
        return view('errors.permission');
    }
}
