<?php

namespace App\Exports;

use App\Models\ProductCoupon;
use App\Models\Store;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $user             = \Auth::user();
        $store_id         = Store::where('id', $user->current_store)->first();
        $data = Student::where('store_id',$store_id->id)->get();
        foreach($data as $k => $customer)
        {
            $store         = Store::where('id', $user->current_store)->first();
            $data[$k]["name"]  = $customer->name;
            $data[$k]["email"]  = $customer->email;
            $data[$k]["phone_number"]  = $customer->phone_number;
            $data[$k]["store_id"]  = $store->name;
            $data[$k]["lang"]  = $store->lang;

            unset($customer->id,$customer->created_by,$customer->created_at,$customer->updated_at,$customer->avatar,$customer->courses_id);
        }

        return $data;
    }

    public function headings(): array
    {
        return [
        "NAME",
        "EMAIL",
        "PHONE NO",
        "STORE",
        "Language"
        ];
    }
}
