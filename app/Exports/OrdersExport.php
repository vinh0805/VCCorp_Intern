<?php

namespace App\Exports;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrdersExport extends Controller implements  WithHeadings, FromCollection, ShouldAutoSize
{
    use Exportable;

    /**
     * @var Order
     */
    private $orders = [];

    public function __construct($_id)
    {
        $currentUser = $this->authLogin('');
        if ($this->checkAuthorize($currentUser, 'order', 'export') ||
            $this->checkAuthorize($currentUser, 'order', 'export_all')) {

            if ($this->checkAuthorize($currentUser, 'order', 'export_all')) {
                $orderList = Order::query()->take(1000)->get();
            } else {
                $orderList = Order::query()
                    ->where('user_id', 'all', [$_id])
                    ->take(1000)
                    ->get();
            }


            // Fix data field user_id
            $userIdList = [];
            foreach ($orderList as $order) {
                if (!is_array($order->user_id)) {
                    continue;
                }
                foreach ($order->user_id as $user_id) {
                    if (!in_array($user_id, $userIdList)) {
                        array_push($userIdList, $user_id);
                    }
                }
            }

            $tmpArray = [];
            $userList = User::query()
                ->whereIn('_id', $userIdList)
                ->get();
            foreach ($orderList as $order) {
                foreach ($order->user_id as $user_id) {
                    if (!is_array($order->user_id)) {
                        continue;
                    }
                    foreach ($userList as $user) {
                        if ($user_id == $user->_id && !in_array($user->email, $tmpArray)) {
                            array_push($tmpArray, $user->email);
                        }
                    }
                }
                $order->user_id = implode(', ', $tmpArray);
                $tmpArray = [];
            }

            foreach ($orderList as $order) {
                $company = Company::query()->find($order->company_id);
                $order->company_id = isset($company) ? $company->name : null;

                $customer = Customer::query()->find($order->customer_id);
                $order->customer_id = isset($customer) ? $customer->name : null;
            }


            $this->orders = $orderList;
        }
    }

    public function collection()
    {
        return $this->orders;
    }

    public function headings(): array
    {
        return [
            '_id',
            'id',
            'customer',
            'company',
            'time',
            'products',
            'price',
            'tax',
            'total_price',
            'address',
            'user',
            'status',
            'created_at',
            'updated_at'
        ];
    }
}
