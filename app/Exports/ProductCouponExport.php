<?php

namespace App\Exports;

use App\Models\ProductCoupon;
use App\Models\Store;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductCouponExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $user  = \Auth::user();
        $data = ProductCoupon::where('created_by',\Auth:: user()->creatorId())->where('store_id',$user->current_store)->get();

        foreach($data as $k => $productcoupon)
        {
            unset($productcoupon->enable_flat, $productcoupon->flat_discount, $productcoupon->limit, $productcoupon->description, $productcoupon->created_by);

            $store=Store::find($productcoupon->store_id);
            $store_id=isset($store)?$store->name:'';

            $data[$k]["store_id"]=$store_id;
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            "ID",
            "Name",
            "Code",
            "Discount",
            "store Id",
            "created_at",
            "updated_at",
        ];
    }
}
