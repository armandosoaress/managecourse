<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Student')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('title'); ?>
    <?php echo e(__('Student')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Home')); ?></a></li>
    <li class="breadcrumb-item"><?php echo e(__('Student')); ?></li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('action-btn'); ?>
<div class="text-end align-items-end d-flex justify-content-end">
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('export student')): ?>
        <a href="<?php echo e(route('student.export')); ?>" class="btn btn-sm btn-primary btn-icon ms-1" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(__('Export')); ?>"><i class="ti ti-file-export text-white"></i></a>
    <?php endif; ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage student logs')): ?>
        <a href="<?php echo e(route('student.logs')); ?>" class="btn btn-primary btn-sm ms-1" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(__('Student Logs')); ?>"><i class="ti ti-user-check"></i>
        </a>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

    <!-- Listing -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <!-- Table -->
                <div class="card-body table-border-style">
                    <div class="table-responsive overflow_hidden">
                        <table id="pc-dt-simple" class="table">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col"><?php echo e(__('Student Avatar')); ?></th>
                                    <th scope="col"><?php echo e(__('Name')); ?></th>
                                    <th scope="col"><?php echo e(__('Email')); ?></th>
                                    <th scope="col"><?php echo e(__('Phone No')); ?></th>
                                    <th scope="col" class="text-center"><?php echo e(__('Action')); ?></th>
                                </tr>
                            </thead>
                            <?php if(count($students) > 0 && !empty($students)): ?>
                                <tbody class="list">
                                    <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <a href="<?php echo e(\App\Models\Utility::get_file('uploads/profile/'.$student->avatar)); ?>" target="_blank">
                                                    <img alt="Image placeholder" src="<?php echo e(\App\Models\Utility::get_file('uploads/profile/'.$student->avatar)); ?>" class="" style="width: 40px;">
                                                </a>
                                            </td>
                                            <td><?php echo e($student->name); ?></td>
                                            <td><?php echo e($student->email); ?></td>
                                            <td><?php echo e($student->phone_number); ?></td>
                                            <td>
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <!-- Actions -->
                                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('show student')): ?>
                                                        <div class="actions ml-3">
                                                            <div class="action-btn bg-warning ms-2">
                                                                <a href="<?php echo e(route('student.show',$student->id)); ?>" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" title="<?php echo e(__('Details')); ?>"> <span class="text-white"> <i class="ti ti-eye"></i></span></a>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            <?php else: ?>
                                <tbody>
                                    <tr>
                                        <td colspan="7">
                                            <div class="text-center">
                                                <i class="fas fa-folder-open text-primary" style="font-size: 48px;"></i>
                                                <h2><?php echo e(__('Opps')); ?>...</h2>
                                                <h6><?php echo e(__('No data Found')); ?>. </h6>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            <?php endif; ?>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\curso\resources\views/student/index.blade.php ENDPATH**/ ?>