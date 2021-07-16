<?php

namespace App\Imports;

use App\Models\Tmp;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ProductsImport implements ToCollection, WithChunkReading
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
//
//namespace App\Imports;
//
//use App\Models\Customer;
////use Carbon\Carbon;
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
//class ProductsImport implements
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
//        $user_id = User::query()
//            ->where('name', $row[7])
//            ->first()->_id;
//
//        return new Customer([
//            '_id' => $row[0],
//            'id' => $row[1],
//            'name' => $row[2],
//            'code' => $row[3],
//            'price' => $row[4],
//            'image' => $row[5],
//            'remain' => $row[6],
//            'user_id' => $user_id,
//            'status' => $row[8],
//            'created_at' => $row[9],
//            'updated_at' => $row[10]
//        ]);
//    }
//
//    public function rules(): array
//    {
//        return [
//            '*.name' => ['required', "min:3", "max:100"]
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
