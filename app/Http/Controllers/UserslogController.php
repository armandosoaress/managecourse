<?php

namespace App\Http\Controllers;

use App\Models\LoginDetail;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserslogController extends Controller
{
    public function  index(Request $request)
    {
        // $users = LoginDetail::get();
        $objUser = \Auth::user();
        $time = date_create($request->month);
        $firstDayofMOnth = (date_format($time, 'Y-m-d'));
        $lastDayofMonth =    \Carbon\Carbon::parse($request->month)->endOfMonth()->toDateString();
        $usersList = User::where('created_by', '=', $objUser->creatorId())->where('current_store', '=', $objUser->current_store)->whereNotIn('type', ['super admin', 'Owner'])->get()->pluck('name', 'id');
        $usersList->prepend('All', '');

        if ($request->month == null) {
            $users = DB::table('login_details')
                ->join('users', 'login_details.user_id', '=', 'users.id')
                ->select(DB::raw('login_details.*, users.name as user_name ,  users.email as user_email , users.type as user_type'))
                ->where(['login_details.created_by' => $objUser->id])->where(['login_details.store_id' => $objUser->current_store]);

            //$user = User::where('created_by', \Auth::user()->ownerId())->get()->pluck('name', 'id');
        } else {
            $users = DB::table('login_details')
                ->join('users', 'login_details.user_id', '=', 'users.id')
                ->select(DB::raw('login_details.*, users.name as user_name ,  users.email as user_email , users.type as user_type'))
                ->where(['login_details.created_by' => $objUser->id])->where(['login_details.store_id' => $objUser->current_store]);
        }
        if (!empty($request->month)) {
            $users->where('date', '>=', $firstDayofMOnth);
            $users->where('date', '<=', $lastDayofMonth);
        }
        if (!empty($request->user)) {
            $users->where(['user_id'  => $request->user]);
        }

        $users = $users->get();
        return view('user_log.index', compact('users', 'usersList'));
    }

    public function view($id)
    {
        $users = LoginDetail::find($id);
        return view('user_log.view', compact('users'));
    }
    public function destroy($id)
    {
        $user = LoginDetail::find($id);
        if ($user) {
            $user->delete();
            return redirect()->back()->with('success', __('User Logs successfully deleted .'));
        } else {
            return redirect()->back()->with('error', __('Something is wrong.'));
        }
    }

}
