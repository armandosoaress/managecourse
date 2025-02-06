<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentLoginDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentlogController extends Controller
{
    public function index(Request $request)
    {
        if(\Auth::user()->can('manage student logs')){
            $objUser = \Auth::user();
            $time = date_create($request->month);
            $firstDayofMOnth = (date_format($time, 'Y-m-d'));
            $lastDayofMonth =    \Carbon\Carbon::parse($request->month)->endOfMonth()->toDateString();
            $studentsList = Student::where('store_id', '=', $objUser->current_store)->get()->pluck('name', 'id');
            $studentsList->prepend('All', '');

            if ($request->month == null) {
                $students = DB::table('student_login_details')
                    ->join('students', 'student_login_details.student_id', '=', 'students.id')
                    ->select(DB::raw('student_login_details.*, students.name as student_name , students.email as student_email'))
                    ->where(['student_login_details.created_by' => $objUser->id])->where(['student_login_details.store_id' => $objUser->current_store]);

            } else {
                $students = DB::table('student_login_details')
                    ->join('students', 'student_login_details.student_id', '=', 'students.id')
                    ->select(DB::raw('student_login_details.*, students.name as student_name , students.email as student_email'))
                    ->where(['student_login_details.created_by' => $objUser->id])->where(['student_login_details.store_id' => $objUser->current_store]);
            }

            if (!empty($request->month)) {
                $students->where('date', '>=', $firstDayofMOnth);
                $students->where('date', '<=', $lastDayofMonth);
            }
            if (!empty($request->student)) {
                $students->where(['student_id'  => $request->student]);
            }

            $students = $students->get();
            return view('student_log.index', compact('students', 'studentsList'));
        }
        else{
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function view($id)
    {
        if(\Auth::user()->can('show student logs')){
            $students = StudentLoginDetail::find($id);
            return view('student_log.view', compact('students'));
        }
        else{
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }
    public function destroy($id)
    {
        if(\Auth::user()->can('delete student logs')){
            $student = StudentLoginDetail::find($id);
            if ($student) {
                $student->delete();
                return redirect()->back()->with('success', __('Student Logs successfully deleted .'));
            } else {
                return redirect()->back()->with('error', __('Something is wrong.'));
            }
        }
        else{
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }
}
