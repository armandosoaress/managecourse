<?php

namespace App\Exports;

use App\Models\Course;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CoursesExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $user  = \Auth::user();
        $data = Course::where('created_by',\Auth:: user()->creatorId())->where('store_id',$user->current_store)->get();
        foreach($data as $k => $course)
        {
            $store  = Course::stores($course->store_id);

            unset($course->has_certificate, $course->quiz, $course->level, $course->lang, $course->duration, $course->is_free, $course->has_discount, $course->is_preview,$course->thumbnail,$course->created_by);
            // $data[$k]["course_id"] = \Auth::user()->customerNumberFormat($customer->customer_id);
            // $data[$k]["balance"]   = \Auth::user()->priceFormat($customer->balance);
            $data[$k]["store_id"]     = $store;

        }

        return $data;
    }

    public function headings(): array
    {
        return [
            "ID",
            "Store Name",
            "Title",
            "Type",
            "Course Requirements",
            "Course Description",
            "Status",
            "Category",
            "Sub Category",
            "Price",
            "Discount",
            "Featured Course",
            "Preview Type",
            "Preview Content",
            "Host",
            "Meta Keywords",
            "Meta Description",
            "created_at",
            "updated_at",
        ];
    }
}
