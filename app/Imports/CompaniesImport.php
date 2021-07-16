<?php

namespace App\Imports;

use App\Models\Tmp;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class CompaniesImport implements ToCollection, WithChunkReading
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
