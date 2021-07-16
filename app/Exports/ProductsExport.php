<?php

namespace App\Exports;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductsExport extends Controller implements  WithHeadings, FromCollection, ShouldAutoSize
{
    use Exportable;

    /**
     * @var Product
     */
    private $products = [];

    public function __construct($_id)
    {
        $currentUser = $this->authLogin('');
        if ($this->checkAuthorize($currentUser, 'product', 'export') ||
            $this->checkAuthorize($currentUser, 'product', 'export_all')) {

            if ($this->checkAuthorize($currentUser, 'product', 'export_all')) {
                $productList = Product::query()->take(1000)->get();
            } else {
                $productList = Product::query()
                    ->where('user_id', 'all', [$_id])
                    ->take(1000)
                    ->get();
            }

            // Fix data field user_id
            $userIdList = [];
            foreach ($productList as $product) {
                if (!is_array($product->user_id)) {
                    continue;
                }
                foreach ($product->user_id as $user_id) {
                    if (!in_array($user_id, $userIdList)) {
                        array_push($userIdList, $user_id);
                    }
                }
            }

            $tmpArray = [];
            $userList = User::query()
                ->whereIn('_id', $userIdList)
                ->get();
            foreach ($productList as $product) {
                foreach ($product->user_id as $user_id) {
                    if (!is_array($product->user_id)) {
                        continue;
                    }
                    foreach ($userList as $user) {
                        if ($user_id == $user->_id && !in_array($user->email, $tmpArray)) {
                            array_push($tmpArray, $user->email);
                        }
                    }
                }
                $product->user_id = implode(', ', $tmpArray);
                $tmpArray = [];
            }

            $this->products = $productList;
        }
    }

    public function collection()
    {
        return $this->products;
    }

    public function headings(): array
    {
        return [
            '_id',
            'id',
            'name',
            'code',
            'price',
            'image',
            'remain',
            'user',
            'status',
            'created_at',
            'updated_at'
        ];
    }
}
