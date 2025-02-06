{{Form::model($role,array('route' => array('roles.update', $role->id), 'method' => 'PUT')) }}
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{Form::label('name',__('Name'),['class'=>'form-label'])}}
                {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Role Name')))}}
                @error('name')
                <small class="invalid-name" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </small>
                @enderror
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                @if(!empty($permissions))
                    <h6 class="my-3">{{__('Assign Permission to Roles')}} </h6>
                    <table class="table  mb-0" id="dataTable-1">
                        <thead>
                        <tr>
                            <th>
                                <input type="checkbox" class="form-check-input align-middle" name="checkall"  id="checkall" >
                            </th>
                            <th>{{__('Module')}} </th>
                            <th>{{__('Permissions')}} </th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $modules=['dashboard','store analytics','order','user','role','store','course','content','chapter','practice file','faq','category','subcategory','custom page','blog','subscriber','course coupon','student','student logs','zoom meeting','plan','store settings'];
                            if(\Auth::user()->type == 'super admin'){
                                $modules[] = 'language';
                                $modules[] = 'permission';
                            }
                        @endphp
                        @foreach($modules as $module)
                            <tr>
                                <td><input type="checkbox" class="form-check-input align-middle ischeck"  data-id="{{str_replace(' ', '', $module)}}" ></td>
                                <td><label class="ischeck" data-id="{{str_replace(' ', '', $module)}}">{{ ucfirst($module) }}</label></td>
                                {{-- <td>{{ ucfirst($module) }}</td>--}}
                                <td>
                                    <div class="row">
                                        @if(in_array('manage '.$module,(array) $permissions))
                                            @if($key = array_search('manage '.$module,$permissions))
                                                <div class="col-md-3 custom-control custom-checkbox">
                                                    {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'form-check-input isscheck isscheck_'.str_replace(' ', '', $module),'id' =>'permission'.$key])}}
                                                    {{Form::label('permission'.$key,'Manage',['class'=>'form-check-label'])}}<br>
                                                </div>
                                            @endif
                                        @endif
                                        @if(in_array('create '.$module,(array) $permissions))
                                            @if($key = array_search('create '.$module,$permissions))
                                                <div class="col-md-3 custom-control custom-checkbox">
                                                    {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'form-check-input isscheck isscheck_'.str_replace(' ', '', $module),'id' =>'permission'.$key])}}
                                                    {{Form::label('permission'.$key,'Create',['class'=>'form-check-label'])}}<br>
                                                </div>
                                            @endif
                                        @endif
                                        @if(in_array('edit '.$module,(array) $permissions))
                                            @if($key = array_search('edit '.$module,$permissions))
                                                <div class="col-md-3 custom-control custom-checkbox">
                                                    {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'form-check-input isscheck isscheck_'.str_replace(' ', '', $module),'id' =>'permission'.$key])}}
                                                    {{Form::label('permission'.$key,'Edit',['class'=>'form-check-label'])}}<br>
                                                </div>
                                            @endif
                                        @endif
                                        @if(in_array('delete '.$module,(array) $permissions))
                                            @if($key = array_search('delete '.$module,$permissions))
                                                <div class="col-md-3 custom-control custom-checkbox">
                                                    {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'form-check-input isscheck isscheck_'.str_replace(' ', '', $module),'id' =>'permission'.$key])}}
                                                    {{Form::label('permission'.$key,'Delete',['class'=>'form-check-label'])}}<br>
                                                </div>
                                            @endif
                                        @endif
                                        @if(in_array('show '.$module,(array) $permissions))
                                            @if($key = array_search('show '.$module,$permissions))
                                                <div class="col-md-3 custom-control custom-checkbox">
                                                    {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'form-check-input isscheck isscheck_'.str_replace(' ', '', $module),'id' =>'permission'.$key])}}
                                                    {{Form::label('permission'.$key,'Show',['class'=>'form-check-label'])}}<br>
                                                </div>
                                            @endif
                                        @endif
                                        @if(in_array('change '.$module,(array) $permissions))
                                            @if($key = array_search('change '.$module,$permissions))
                                                <div class="col-md-3 custom-control custom-checkbox">
                                                    {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'form-check-input isscheck isscheck_'.str_replace(' ', '', $module),'id' =>'permission'.$key])}}
                                                    {{Form::label('permission'.$key,'Change',['class'=>'form-check-label'])}}<br>
                                                </div>
                                            @endif
                                        @endif
                                        @if(in_array('import '.$module,(array) $permissions))
                                            @if($key = array_search('import '.$module,$permissions))
                                                <div class="col-md-3 custom-control custom-checkbox">
                                                    {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'form-check-input isscheck isscheck_'.str_replace(' ', '', $module),'id' =>'permission'.$key])}}
                                                    {{Form::label('permission'.$key,'Import',['class'=>'form-check-label'])}}<br>
                                                </div>
                                            @endif
                                        @endif
                                        @if(in_array('export '.$module,(array) $permissions))
                                            @if($key = array_search('export '.$module,$permissions))
                                                <div class="col-md-3 custom-control custom-checkbox">
                                                    {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'form-check-input isscheck isscheck_'.str_replace(' ', '', $module),'id' =>'permission'.$key])}}
                                                    {{Form::label('permission'.$key,'Export',['class'=>'form-check-label'])}}<br>
                                                </div>
                                            @endif
                                        @endif
                                        @if(in_array('upload '.$module,(array) $permissions))
                                            @if($key = array_search('upload '.$module,$permissions))
                                                <div class="col-md-3 custom-control custom-checkbox">
                                                    {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'form-check-input isscheck isscheck_'.str_replace(' ', '', $module),'id' =>'permission'.$key])}}
                                                    {{Form::label('permission'.$key,'Upload',['class'=>'form-check-label'])}}<br>
                                                </div>
                                            @endif
                                        @endif
                                        @if(in_array('social media '.$module,(array) $permissions))
                                            @if($key = array_search('social media '.$module,$permissions))
                                                <div class="col-md-3 custom-control custom-checkbox">
                                                    {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'form-check-input isscheck isscheck_'.str_replace(' ', '', $module),'id' =>'permission'.$key])}}
                                                    {{Form::label('permission'.$key,'Social Media',['class'=>'form-check-label'])}}<br>
                                                </div>
                                            @endif
                                        @endif
                                        @if(in_array('reset password '.$module,(array) $permissions))
                                            @if($key = array_search('reset password '.$module,$permissions))
                                                <div class="col-md-3 custom-control custom-checkbox">
                                                    {{Form::checkbox('permissions[]',$key,$role->permission, ['class'=>'form-check-input isscheck isscheck_'.str_replace(' ', '', $module),'id' =>'permission'.$key])}}
                                                    {{Form::label('permission'.$key,'Reset Password',['class'=>'form-check-label'])}}<br>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>

<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
</div>
{{Form::close()}}


<script>
    $(document).ready(function () {
        $("#checkall").click(function(){
            $('input:checkbox').not(this).prop('checked', this.checked);
        });
        $(".ischeck").click(function(){
            var ischeck = $(this).data('id');
            $('.isscheck_'+ ischeck).prop('checked', this.checked);
        });
    });
</script>
