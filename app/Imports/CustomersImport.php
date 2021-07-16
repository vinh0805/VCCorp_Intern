<?php

namespace App\Imports;

use App\Models\Tmp;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class CustomersImport implements ToCollection, WithChunkReading
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row)
        {
            $tmp = [];
            foreach ($row as $k => $r) {
                $tmp[$k] = $r;
            }
            Tmp::query()->create($tmp);
        }
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}

//<?php
//
//namespace App\Imports;
//
//use App\Models\Company;
//use App\Models\User;
//use Maatwebsite\Excel\Concerns\Importable;
//use Maatwebsite\Excel\Concerns\RegistersEventListeners;
//use Maatwebsite\Excel\Concerns\SkipsErrors;
//use Maatwebsite\Excel\Concerns\SkipsFailures;
//use Maatwebsite\Excel\Concerns\SkipsOnError;
//use Maatwebsite\Excel\Concerns\SkipsOnFailure;
//use Maatwebsite\Excel\Concerns\ToModel;
//use Maatwebsite\Excel\Concerns\WithChunkReading;
//use Maatwebsite\Excel\Concerns\WithEvents;
//use Maatwebsite\Excel\Concerns\WithHeadingRow;
//use Maatwebsite\Excel\Concerns\WithValidation;
//use Maatwebsite\Excel\Events\AfterImport;
//
//class CompaniesImport implements
//    ToModel,
//    WithHeadingRow,
//    SkipsOnError,
//    WithValidation,
//    SkipsOnFailure,
//    WithChunkReading,
//    WithEvents
//{
//    use Importable, SkipsErrors, SkipsFailures, RegistersEventListeners;
//
//    public function model(array $row)
//    {
//        $user_id = User::where('name', $row[8])->first()->_id;
//        return new Company([
//            '_id' => $row[0],
//            'id' => $row[1],
//            'name' => $row[2],
//            'code' => $row[3],
//            'field' => $row[4],
//            'address' => $row[5],
//            'email' => $row[6],
//            'phone' => $row[7],
//            'user_id' => $user_id,
//            'status' => $row[9],
//            'created_at' => $row[10],
//            'updated_at' => $row[11]
//        ]);
//    }
//
//    public function rules(): array
//    {
//        return [
//            '*.name' => ['required', "min:3", "max:100"],
//            '*.email' => ['required', 'email'],
//            '*.phone' => ['required', 'numeric', 'regex:/(0)[1-9]{1}[0-9]{1} [0-9]{3} [0-9]{4}/'],
//        ];
//    }
//
//    public function chunkSize(): int
//    {
//        return 1000;
//    }
//
//    public static function afterImport(AfterImport $event)
//    {
//    }
//
//}
////
////namespace App\Imports;
////
////use App\Models\Customer;
////use Illuminate\Support\Collection;
////use Maatwebsite\Excel\Concerns\ToCollection;
////
////class CustomersImport implements ToCollection
////{
////    /**
////     * @param Collection $rows
////     * @return void
////     */
////    public function collection(Collection $rows)
////    {
////        foreach ($rows as $row) {
////            $customer = new Customer([
////                '_id' => $row[0],
////                'id' => $row[1],
////                'name' => $row[2],
////                'birth' => $row[3],
////                'gender' => $row[4],
////                'job' => $row[5],
////                'address' => $row[6],
////                'email' => $row[7],
////                'phone' => $row[8],
////                'user_id' => $row[9],
////                'company_id' => $row[10],
////                'status' => $row[11],
////                'created_at' => $row[12],
////                'updated_at' => $row[13]
////            ]);
////
////            $customer->save();
////        }
////
////
////
//////        Customer::create([
//////            '_id' => $row[0],
//////            'id' => $row[1],
//////            'name' => $row[2],
//////            'birth' => $row[3],
//////            'gender' => $row[4],
//////            'job' => $row[5],
//////            'address' => $row[6],
//////            'email' => $row[7],
//////            'phone' => $row[8],
//////            'user_id' => $row[9],
//////            'company_id' => $row[10],
//////            'status' => $row[11],
//////            'created_at' => $row[12],
//////            'updated_at' => $row[13]
//////        ]);
//////        return new Customer([
//////            '_id' => $row[0],
//////            'id' => $row[1],
//////            'name' => $row[2],
//////            'birth' => $row[3],
//////            'gender' => $row[4],
//////            'job' => $row[5],
//////            'address' => $row[6],
//////            'email' => $row[7],
//////            'phone' => $row[8],
//////            'user_id' => $row[9],
//////            'company_id' => $row[10],
//////            'status' => $row[11],
//////            'created_at' => $row[12],
//////            'updated_at' => $row[13]
//////        ]);
////    }
////}
//
//
//namespace App\Imports;
//
//use App\Models\Company;
//use App\Models\Customer;
//use App\Models\User;
//use Maatwebsite\Excel\Concerns\Importable;
//use Maatwebsite\Excel\Concerns\RegistersEventListeners;
//use Maatwebsite\Excel\Concerns\SkipsErrors;
//use Maatwebsite\Excel\Concerns\SkipsFailures;
//use Maatwebsite\Excel\Concerns\SkipsOnError;
//use Maatwebsite\Excel\Concerns\SkipsOnFailure;
//use Maatwebsite\Excel\Concerns\ToModel;
//use Maatwebsite\Excel\Concerns\WithChunkReading;
//use Maatwebsite\Excel\Concerns\WithEvents;
//use Maatwebsite\Excel\Concerns\WithHeadingRow;
//use Maatwebsite\Excel\Concerns\WithValidation;
//use Maatwebsite\Excel\Events\AfterImport;
//
//class CustomersImport implements
//    ToModel,
//    WithHeadingRow,
//    SkipsOnError,
//    WithValidation,
//    SkipsOnFailure,
//    WithChunkReading,
//    WithEvents
//{
//    use Importable, SkipsErrors, SkipsFailures, RegistersEventListeners;
//
//    public function model(array $row)
//    {
//        $company_id = Company::where('name', $row[9])->first()->_id;
//        $user_id = User::where('name', $row[10])->first()->_id;
//
//        return new Customer([
//            '_id' => $row[0],
//            'id' => $row[1],
//            'name' => $row[2],
//            'birth' => $row[3],
//            'gender' => $row[4],
//            'job' => $row[5],
//            'address' => $row[6],
//            'email' => $row[7],
//            'phone' => $row[8],
//            'company_id' => $company_id,
//            'user_id' => $user_id,
//            'status' => $row[11],
//            'created_at' => $row[12],
//            'updated_at' => $row[13]
//        ]);
//    }
//
//    public function rules(): array
//    {
//        return [
//            '*.name' => ['required', "min:3", "max:100"],
//            '*.email' => ['required', 'email'],
//            '*.phone' => ['required', 'numeric', 'regex:/(0)[1-9]{1}[0-9]{1} [0-9]{3} [0-9]{4}/'],
//        ];
//    }
//
//    public function chunkSize(): int
//    {
//        return 1000;
//    }
//
//    public static function afterImport(AfterImport $event)
//    {
//    }
//
//}
