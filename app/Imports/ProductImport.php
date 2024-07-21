<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductImport implements ToModel, WithHeadingRow
{
    
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

        return new Product([
            'category_id' => $row['category'],
            'code' => $row['code'],
            'name' => $row['name'],
            'description' => $row['description'],
            'buying_price' => $row['buying_price'],
            'price' => $row['selling_price'],
            'taxable' => 1,
        ]);
    }
}
