<?php

namespace App\Exports;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Customer;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomersExport extends Controller implements WithHeadings, FromCollection, ShouldAutoSize
{
    use Exportable;

    /**
     * @var Customer
     */
    private $customers = [];

    public function __construct($_id)
    {
        $currentUser = $this->authLogin('');
        if ($this->checkAuthorize($currentUser, 'customer', 'export') ||
            $this->checkAuthorize($currentUser, 'customer', 'export_all')) {

            if ($this->checkAuthorize($currentUser, 'customer', 'export_all')) {
                $customerList = Customer::query()->take(1000)->get();
            } else {
                $customerList = Customer::query()
                    ->where('user_id', 'all', [$_id])
                    ->take(1000)
                    ->get();
            }

            // Fix data field user_id
            $userIdList = [];
            foreach ($customerList as $customer) {
                if (!is_array($customer->user_id)) {
                    continue;
                }
                foreach ($customer->user_id as $user_id) {
                    if (!in_array($user_id, $userIdList)) {
                        array_push($userIdList, $user_id);
                    }
                }
            }

            $tmpArray = [];
            $userList = User::query()
                ->whereIn('_id', $userIdList)
                ->get();
            foreach ($customerList as $customer) {
                if (!is_array($customer->user_id)) {
                    continue;
                }
                foreach ($customer->user_id as $user_id) {
                    foreach ($userList as $user) {
                        if ($user_id == $user->_id && !in_array($user->email, $tmpArray)) {
                            array_push($tmpArray, $user->email);
                        }
                    }
                }
                $customer->user_id = implode(', ', $tmpArray);
                $tmpArray = [];
            }

            // Fix data field company_id
            $companyIdList = [];
            foreach ($customerList as $customer) {
                if (!in_array($customer->company_id, $companyIdList)) {
                    array_push($companyIdList, $customer->company_id);
                }
            }
            $companyList = Company::query()
                ->whereIn('_id', $companyIdList)
                ->get();
            foreach ($customerList as $customer) {
                foreach ($companyList as $company) {
                    if ($customer->company_id == $company->_id) {
                        $customer->company_id = $company->email;
                    }
                }
            }

            $this->customers = $customerList;
        }
    }

    public function collection()
    {
        return $this->customers;
    }

    public function headings(): array
    {
        return [
            '_id',
            'id',
            'name',
            'birth',
            'gender',
            'job',
            'address',
            'email',
            'phone',
            'company',
            'user',
            'status',
            'created_at',
            'updated_at'
        ];
    }
}
