<?php
//
//namespace App\Imports;
//
//use App\Models\Order;
//use Illuminate\Support\Collection;
//use Maatwebsite\Excel\Concerns\ToCollection;
//
//class OrdersImport implements ToCollection
//{
//    /**
//     * @param Collection $rows
//     * @return void
//     */
//    public function collection(Collection $rows)
//    {
//        foreach ($rows as $row) {
//            $customer = new Order([
//                '_id' => $row[0],
//                'id' => $row[1],
//                'name' => $row[2],
//                'birth' => $row[3],
//                'gender' => $row[4],
//                'job' => $row[5],
//                'address' => $row[6],
//                'email' => $row[7],
//                'phone' => $row[8],
//                'user_id' => $row[9],
//                'company_id' => $row[10],
//                'status' => $row[11],
//                'created_at' => $row[12],
//                'updated_at' => $row[13]
//            ]);
//
//            $customer->save();
//        }
//
//
//
////        Order::create([
////            '_id' => $row[0],
////            'id' => $row[1],
////            'name' => $row[2],
////            'birth' => $row[3],
////            'gender' => $row[4],
////            'job' => $row[5],
////            'address' => $row[6],
////            'email' => $row[7],
////            'phone' => $row[8],
////            'user_id' => $row[9],
////            'company_id' => $row[10],
////            'status' => $row[11],
////            'created_at' => $row[12],
////            'updated_at' => $row[13]
////        ]);
////        return new Order([
////            '_id' => $row[0],
////            'id' => $row[1],
////            'name' => $row[2],
////            'birth' => $row[3],
////            'gender' => $row[4],
////            'job' => $row[5],
////            'address' => $row[6],
////            'email' => $row[7],
////            'phone' => $row[8],
////            'user_id' => $row[9],
////            'company_id' => $row[10],
////            'status' => $row[11],
////            'created_at' => $row[12],
////            'updated_at' => $row[13]
////        ]);
//    }
//}


namespace App\Imports;

use App\Models\Company;
use App\Models\Customer;
use App\Models\Order;
//use Carbon\Carbon;
use App\Models\Product;
use App\Models\User;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\AfterImport;

class OrdersImport implements
    ToModel,
    WithHeadingRow,
    WithChunkReading
{
    use Importable, SkipsErrors, SkipsFailures, RegistersEventListeners;

    public function model(array $row)
    {
        $productList = [];
        $error = false;

        $products = json_decode($row[4]);

//        for ($i = 0; $i < count($products); $i++) {
//            $product = Product::find($products[$i]->product);
//
//            // Continue if can't find product
//            if (!isset($product)) {
//                continue;
//            }
//
//            if ($products[$i]->number >= $product->remain) {
//                $error = true;
//                break;
//            }
//
//            $tmp = [
//                'product' => $product->_id,
//                'number' => $products[$i]->number
//            ];
//            array_push($productList, $tmp);
//        }
//
//        if ($error) {
//            return redirect('orders')->with('errorMessage',
//                'Thêm đơn hàng mới thất bại!\nSố lượng hàng tồn kho không đủ.');
//        }

        $user_id = User::where('name', $row[9])->first()->_id;

        $customer = Customer::where('name', $row[2])->first();
        $customer_id = isset($customer) ? $customer->_id : null;

        $company = Company::where('name', $row[3])->first();
        $company_id = isset($company) ? $company->_id : null;


        return new Order([
            '_id' => $row[0],
            'id' => $row[1],
            'customer_id' => $customer_id,
            'company_id' => $company_id,
            'products' => $products,
            'price' => $row[5],
            'tax' => $row[6],
            'total_price' => $row[7],
            'time' => $row[8],
            'address' => $row[10],
            'user_id' => $user_id,
            'status' => $row[11],
            'created_at' => $row[12],
            'updated_at' => $row[13]
        ]);
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
