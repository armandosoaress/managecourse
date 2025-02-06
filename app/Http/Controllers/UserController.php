<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Plan;
use App\Models\Store;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;
use Lab404\Impersonate\Impersonate;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->can('manage user'))
        {
            $user = \Auth::user()->current_store;
            $users = User::where('created_by','=',\Auth::user()->creatorId())->where('current_store',$user)->get();
            return view('users.index',compact('users'));
        }
        else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (\Auth::user()->can('create user')) {
            $user  = \Auth::user();
            $roles = Role::where('created_by', '=', $user->creatorId())->where('store_id',$user->current_store)->get()->pluck('name', 'id');
            return view('users.create',compact('roles'));
        }else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (\Auth::user()->can('create user')) {

            $validator = \Validator::make(
                $request->all(),
                [
                    'email' => [
                        'required',
                        Rule::unique('users')->where(function ($query) {
                        return $query->where('created_by', \Auth::user()->id)->where('current_store',\Auth::user()->current_store);
                        })
                    ],
                ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $user        = \Auth::user();
            $total_user = $user->countUsers();
            $creator     = User::find($user->creatorId());
            $plan        = Plan::find($creator->plan);

            if($total_user < $plan->max_users || $plan->max_users == -1)
            {
                $default_lang = DB::table('settings')->select('value')->where('name', 'default_language')->where('created_by', '=', \Auth::user()->creatorId())->where('store_id', '=', \Auth::user()->current_store)->first();
                $objUser    = \Auth::user()->creatorId();
                $objUser = User::find($objUser);
                $role_r = Role::findById($request->role);
                $date = date("Y-m-d H:i:s");
                $user =  new User();
                $user->name =  $request['name'];
                $user->email =  $request['email'];
                $user->password = Hash::make($request['password']);
                $user->type = $role_r->name;
                $user->lang = $default_lang->value ?? 'en';
                $user->created_by = \Auth::user()->creatorId();
                $user->email_verified_at = $date;
                $user->current_store = $objUser->current_store;
                $user->save();

                $user->assignRole($role_r);
                return redirect()->route('users.index')->with('success', __('User successfully created.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Your User limit is over Please upgrade plan'));
            }
        }
        else{
             return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('profile');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        if (\Auth::user()->can('edit user')) {
            $roles = Role::where('created_by', '=', \Auth::user()->creatorId())->where('store_id',\Auth::user()->current_store)->get()->pluck('name', 'id');
            return view('users.edit', compact('user', 'roles'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        if (\Auth::user()->can('edit user')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'email' => ['required',
                                Rule::unique('users')->where(function ($query)  use ($user) {
                                return $query->whereNotIn('id',[$user->id])->where('created_by',  \Auth::user()->creatorId())->where('current_store', \Auth::user()->current_store);
                            })
                ],
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $role          = Role::findById($request->role);
            $input         = $request->all();
            $input['type'] = $role->name;
            $user->fill($input)->save();

            $user->assignRole($role);
            $roles[] = $request->role;
            $user->roles()->sync($roles);
            return redirect()->route('users.index')->with('success', 'User successfully updated.');
        }
        else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if (\Auth::user()->can('delete user')) {
            $user->delete();

            return redirect()->route('users.index')->with('success', 'User successfully deleted.');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function reset($id){
        if (\Auth::user()->can('reset password user')) {
            $Id        = \Crypt::decrypt($id);

            $user = User::find($Id);

            $employee = User::where('id', $Id)->first();

            return view('users.reset', compact('user', 'employee'));
        }
        else{
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }
    public function updatePassword(Request $request, $id){
        if (\Auth::user()->can('reset password user')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'password' => 'required|confirmed|same:password_confirmation',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $user                 = User::where('id', $id)->first();
            $user->forceFill([
                'password' => Hash::make($request->password),
            ])->save();

            return redirect()->route('users.index')->with(
                'success',
                'User Password successfully updated.'
            );
        }
        else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function LoginWithCompany(Request $request, User $user,  $id)
    {
        $user = User::find($id);
        if ($user && auth()->check()) {
            Impersonate::take($request->user(), $user);
            return redirect('/dashboard');
        }
    }

    public function ExitCompany(Request $request)
    {
        \Auth::user()->leaveImpersonation($request->user());
        return redirect('/dashboard');
    }

    public function CompnayInfo($id)
    {
        if(!empty($id)){
            $data = $this->Counter($id);
            if($data['is_success']){
                $users_data = $data['response']['users_data'];
                $store_data = $data['response']['store_data'];
                return view('user.companyinfo', compact('id','users_data','store_data'));
            }
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function Counter($id)
    {
        $response = [];
        if(!empty($id))
        {
            $totalstore= Store::where('created_by', $id)
            ->selectRaw('COUNT(*) as total_store, SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as disable_store, SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_store')
            ->first();
            $stores = Store::where('created_by',$id)->get();
            $users_data = [];
            foreach($stores as $store)
            {
                $users = User::where('created_by',$id)->where('current_store',$store->id)->selectRaw('COUNT(*) as total_users, SUM(CASE WHEN is_disable = 0 THEN 1 ELSE 0 END) as disable_users, SUM(CASE WHEN is_disable = 1 THEN 1 ELSE 0 END) as active_users')->first();

                $users_data[$store->name] = [
                    'store_id' => $store->id,
                    'total_users' => !empty($users->total_users) ? $users->total_users : 0,
                    'disable_users' => !empty($users->disable_users) ? $users->disable_users : 0,
                    'active_users' => !empty($users->active_users) ? $users->active_users : 0,
                ];
            }
            $store_data =[
                'total_store' =>  $totalstore->total_store,
                'disable_store' => $totalstore->disable_store,
                'active_store' => $totalstore->active_store,
            ];
            $response['users_data'] = $users_data;
            $response['store_data'] = $store_data;

            return [
                'is_success' => true,
                'response' => $response,
            ];
        }
        return [
            'is_success' => false,
            'error' => 'Plan is deleted.',
        ];
    }

    public function UserUnable(Request $request)
    {
        if(!empty($request->id) && !empty($request->company_id))
        {
            if($request->name == 'user')
            {
                User::where('id', $request->id)->update(['is_disable' => $request->is_disable]);
                $data = $this->Counter($request->company_id);

            }
            elseif($request->name == 'store')
            {
                $company = User::find($request->company_id);
                if($company->current_store != $request->id )
                {
                    Store::where('id',$request->id)->update(['is_active' => $request->is_disable]);
                }
                else
                {
                    return response()->json(['error' => __('Active Store can not disable.')]);
                }

                if($request->is_disable == 0)
                {
                    User::where('current_store',$request->id)->where('type','!=','company')->update(['is_disable' => $request->is_disable]);
                }
                $data = $this->Counter($request->company_id);
            }
            if($data['is_success'])
            {
                $users_data = $data['response']['users_data'];
                $store_data = $data['response']['store_data'];
            }
            if($request->is_disable == 1){

                return response()->json(['success' => __('Successfully Unable.'),'users_data' => $users_data, 'store_data' => $store_data]);
            }else
            {
                return response()->json(['success' => __('Successfull Disable.'),'users_data' => $users_data, 'store_data' => $store_data]);
            }
        }
        return response()->json('error');
    }
}
