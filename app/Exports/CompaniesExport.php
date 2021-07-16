<?php

namespace App\Exports;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CompaniesExport extends Controller implements WithHeadings, FromCollection, ShouldAutoSize
{
    use Exportable;

    /**
     * @var Company
     */
    private $companies = [];

    public function __construct($_id)
    {
        $currentUser = $this->authLogin('');
        if ($this->checkAuthorize($currentUser, 'company', 'export_all') ||
            $this->checkAuthorize($currentUser, 'company', 'export')) {
            if ($this->checkAuthorize($currentUser, 'company', 'export_all')) {
                $companyList = Company::query()
                    ->get();
            } else {
                $companyList = Company::query()
                    ->where('user_id', 'all', [$_id])
                    ->get();
            }

            // Unset _id field
            $companyList->transform(function($i) {
                unset($i->_id);
                return $i;
            });


            // Fix data field user_id
            $userIdList = [];
            foreach ($companyList as $company) {
                if (!is_array($company->user_id)) {
                    continue;
                }
                foreach ($company->user_id as $user_id) {
                    if (!in_array($user_id, $userIdList)) {
                        array_push($userIdList, $user_id);
                    }
                }
            }

            $tmpArray = [];
            $userList = User::query()
                ->whereIn('_id', $userIdList)
                ->get();
            foreach ($companyList as $company) {
                foreach ($company->user_id as $user_id) {
                    if (!is_array($company->user_id)) {
                        continue;
                    }
                    foreach ($userList as $user) {
                        if ($user_id == $user->_id && !in_array($user->email, $tmpArray)) {
                            array_push($tmpArray, $user->email);
                        }
                    }
                }
                $company->user_id = implode(', ', $tmpArray);
                $tmpArray = [];
            }

            $this->companies = $companyList;
        }
    }

    public function collection()
    {
        return $this->companies;
    }

    public function headings(): array
    {
        return [
            'id',
            'name',
            'code',
            'address',
            'field',
            'email',
            'phone',
            'user',
            'status',
            'created_at',
            'updated_at'
        ];
    }
//
//    public function query()
//    {
//        return Company::query();
//    }
}
