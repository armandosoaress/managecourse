<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Zoom Meeting')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startPush('css-page'); ?>
<link rel="stylesheet" type="text/css" href="<?php echo e(asset('css/daterangepicker.css')); ?>">
<?php $__env->stopPush(); ?>
<?php $__env->startSection('title'); ?>
    <?php echo e(__('Zoom Meeting')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Home')); ?></a></li>
    <li class="breadcrumb-item"><?php echo e(__('Zoom Meeting')); ?></li>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('action-btn'); ?>
<div class="text-end align-items-end d-flex justify-content-end">
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage zoom meeting')): ?>
        <div class="btn btn-sm btn-primary btn-icon ms-1">
            <a href="<?php echo e(route('zoom-meeting.calender')); ?>" class="" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(__('Calendar')); ?>"><i class="ti ti-calendar text-white"></i></a>
        </div>
    <?php endif; ?>

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create zoom meeting')): ?>
        <div class="btn btn-sm btn-primary btn-icon ms-1">
            <a href="#" class="" id="add-user" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(__('Create Meeting')); ?>" data-ajax-popup="true" data-size="lg" data-title="<?php echo e(__('Create Meeting')); ?>" data-url="<?php echo e(route('zoom-meeting.create')); ?>"><i class="ti ti-plus text-white"></i></a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body table-border-style">
                <div class="table-responsive overflow_hidden">
                    <table id="pc-dt-simple" class="table">
                        <thead class="thead-light">
                            <tr>
                                <th> <?php echo e(__('TITLE')); ?> </th>
                                <th> <?php echo e(__('COURSE')); ?>  </th>
                                <th> <?php echo e(__('STUDENT')); ?>  </th>
                                <th> <?php echo e(__('MEETING TIME')); ?> </th>
                                <th> <?php echo e(__('DURATION')); ?> </th>
                                <th> <?php echo e(__('JOIN URL')); ?> </th>
                                <th> <?php echo e(__('STATUS')); ?> </th>
                                <th class="text-right"> <?php echo e(__('Action')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $meetings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($item->title); ?></td>
                                    <td><?php echo e($item->getCourseInfo->title); ?></td>
                                    <td>
                                        <div class="avatar-group">
                                            <?php $__currentLoopData = $item->students($item->student_id); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $projectUser): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <a href="#" class="user-group1">
                                                    <img alt="" <?php if(!empty($users->avatar)): ?> src="<?php echo e($profile.'/'.$projectUser->avatar); ?>" <?php else: ?> avatar="<?php echo e((!empty($projectUser) ? $projectUser->name:'')); ?>" <?php endif; ?> data-original-title="<?php echo e((!empty($projectUser)?$projectUser->name:'hello')); ?>" data-toggle="tooltip" data-original-title="<?php echo e((!empty($projectUser)?$projectUser->name:'')); ?>" class="">
                                                </a>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </td>
                                    <td><?php echo e($item->start_date); ?></td>
                                    <td><?php echo e($item->duration); ?> <?php echo e(__("Minutes")); ?></td>
                                    <td>
                                        <?php if($item->created_by == \Auth::user()->current_store && $item->checkDateTime()): ?>
                                        <a href="<?php echo e($item->start_url); ?>" target="_blank"> <?php echo e(__('Start meeting')); ?> <i class="fas fa-external-link-square-alt "></i></a>
                                        <?php elseif($item->checkDateTime()): ?>
                                            <a href="<?php echo e($item->join_url); ?>" target="_blank"> <?php echo e(__('Join meeting')); ?> <i class="fas fa-external-link-square-alt "></i></a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>

                                    </td>
                                    <td>
                                        <?php if($item->checkDateTime()): ?>
                                            <?php if($item->status == 'waiting'): ?>
                                                <span class="badge bg-info p-2 px-3 rounded"><?php echo e(ucfirst($item->status)); ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-success p-2 px-3 rounded"><?php echo e(ucfirst($item->status)); ?></span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge bg-danger p-2 px-3 rounded"><?php echo e(__("End")); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-right">
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete zoom meeting')): ?>
                                            <div class="action-btn bg-danger ms-2">
                                                <?php echo Form::open(['method' => 'DELETE', 'route' => ['zoom-meeting.destroy', $item->id]]); ?>

                                                    <a href="#!" class="mx-3 btn btn-sm  align-items-center show_confirm" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(__('Delete')); ?>">
                                                        <i class="ti ti-trash text-white"></i>
                                                    </a>
                                                <?php echo Form::close(); ?>

                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<?php $__env->stopSection(); ?>
<?php $__env->startPush('script-page'); ?>
<script src="<?php echo e(url('js/daterangepicker.js')); ?>"></script>
<script type="text/javascript">

$(document).on('change', '#course_id', function() {
    getStudents($(this).val());
});
// alert('hgjh');
function getStudents(id){

    $("#students-div").html('');
        $('#students-div').append('<select class="form-control" id="student_id" name="students[]"  multiple></select>');

    $.get("<?php echo e(url('get-students')); ?>/"+id, function(data, status){

        var list = '';
        $('#student_id').empty();
        if(data.length > 0){
            list += "<option value=''>  </option>";
        }else{
            list += "<option value=''> <?php echo e(__('No Students')); ?> </option>";
        }
        $.each(data, function(i, item) {

            list += "<option value='"+item.id+"'>"+item.name+"</option>"
        });
        $('#student_id').html(list);
        var multipleCancelButton = new Choices('#student_id', {
                        removeItemButton: true,
        });
    });
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\curso\resources\views/zoom_meeting/index.blade.php ENDPATH**/ ?>