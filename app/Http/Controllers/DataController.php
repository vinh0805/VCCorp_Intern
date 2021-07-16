<?php

namespace App\Http\Controllers;

use App\Models\Tmp;
use Exception;
use Illuminate\Http\Request;

class DataController extends Controller
{
    public function deleteReviewData($_id)
    {
        $currentUser = $this->authLogin('');

        $tmp = Tmp::query()->find($_id);

        if (!$tmp) {
            $data = [
                'message' => 'Xóa dữ liệu không thành công! Dữ liệu không tồn tại!',
                'success' => false
            ];
            return response()->json($data);
        }

        // Check isset role
        if (!$this->checkAuthorize($currentUser, $tmp['collection'], 'import')) {
            $data = [
                'message' => 'Bạn không có quyền xóa đối với dữ liệu này!',
                'success' => false
            ];
            return response()->json($data);
        }


        try {
            if ($tmp->delete()) {
                $data = [
                    'message' => 'Xóa dữ liệu thành công!',
                    'success' => true
                ];

                return response()->json($data);

            } else {
                $data = [
                    'message' => 'Xóa dữ liệu thất bại! Dữ liệu không tồn tại',
                    'success' => false
                ];

                return response()->json($data);
            }
        } catch (Exception $e) {
            $data = [
                'message' => 'Xóa dữ liệu thất bại!',
                'success' => false
            ];

            return response()->json($data);
        }
    }

    public function getPaginatedData(Request $request)
    {
        $currentUser = $this->authLogin('');

        $fields_in_db = explode(',', $request['fields_in_db']);
        $collection = $request['collection'];
        $random_key = $request['import_form_random_key'];
        $default_fields = $request['default_fields'] ? explode(',', $request['default_fields']) : [];
        $old_fields = $request['old_fields'] ? explode(',', $request['old_fields']) : [];
        $new_fields = $request['new_fields'] ? explode('.', $request['new_fields']) : [];
        $defaultData = [];
        if ($collection == 'company') {
            $defaultData = [
                'id' => 1,
                'name' => 'ABC',
                'code' => 'CT',
                'field' => 'Nhiều lĩnh vực',
                'address' => 'Ha Noi',
                'email' => 'ctabc@gmail.com',
                'phone' => '099 999 9999',
                'user_id' => [],
                'status' => 'Đang hoạt động',
                'created_at' => '2021-06-23T07:59:51.975000Z',
                'updated_at' => '2021-06-12T05:09:50.169000Z'
            ];
        } elseif ($collection == 'customer') {
            $defaultData = [
                'id' => "1",
                'name' => 'Khách hàng ABC',
                'birth' => '01/01/1999',
                'gender' => 'Nữ',
                'job' => 'Kinh doanh tự do',
                'address' => 'Ha Noi',
                'email' => 'khabc@gmail.com',
                'phone' => '099 999 9999',
                'company_id' => null,
                'user_id' => [],
                'status' => 'Đang hoạt động',
                'created_at' => '2021-06-23T07:59:51.975000Z',
                'updated_at' => '2021-06-12T05:09:50.169000Z'
            ];
        } elseif ($collection == 'product') {
            $defaultData = [
                'id' => 1,
                'name' => 'Sản phẩm A',
                'code' => 'SPA',
                'price' => 1000000,
                'image' => null,
                'remain' => 1000,
                'user_id' => [],
                'status' => 'Đang hoạt động',
                'created_at' => '2021-06-23T07:59:51.975000Z',
                'updated_at' => '2021-06-12T05:09:50.169000Z'
            ];
        }

        $allTmpCompanies = Tmp::query()
            ->where('collection', $collection)
            ->where('current_user', $currentUser->_id)
            ->where('random_key', $random_key)
            ->paginate(10);

        $view = view('import.review3')
            ->with('allTmpData', $allTmpCompanies)
            ->with('fields_in_db', $fields_in_db)
            ->with('defaultData', $defaultData)
            ->with('default_fields', $default_fields)
            ->with('old_fields', $old_fields)
            ->with('new_fields', $new_fields)
            ->render();

        return [$view, $request['step_import'], $collection, $random_key];
    }

}
