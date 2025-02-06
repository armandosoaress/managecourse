<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Order')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('title'); ?>
    <?php echo e(__('Orders ')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('action-btn'); ?>
<div class="text-end align-items-end d-flex justify-content-end">
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('expport order')): ?>
        <div class="btn btn-sm btn-primary btn-icon">
            <a href="<?php echo e(route('order.export')); ?>" class="" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(__('Export')); ?>"><i class="ti ti-file-export text-white"></i></a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Home')); ?></a></li>
    <li class="breadcrumb-item"><?php echo e(__('Order')); ?></li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('filter'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('css-page'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/custom.css')); ?>">
<?php $__env->stopPush(); ?>
<?php $__env->startSection('content'); ?>
<?php
    $user = \Auth::user();
    $plan = App\Models\Plan::where('id',$user->plan)->first();
    $total_storage = $user->storage_limit;
?>
<?php if($plan->storage_limit <= $total_storage && $plan->storage_limit != -1): ?>
    <small
        class="text-danger"><?php echo e(__('Note : Your plan storage limit is over , so you can not see customer uploaded payment receipt.')); ?></small>
<?php endif; ?>
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body table-border-style">
                <div class="table-responsive overflow_hidden">
                    <table id="pc-dt-simple" class="table">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col"><?php echo e(__('Orders')); ?></th>
                                <th scope="col" class="sort"><?php echo e(__('Date')); ?></th>
                                <th scope="col" class="sort"><?php echo e(__('Name')); ?></th>
                                <th scope="col" class="sort"><?php echo e(__('Value')); ?></th>
                                <th scope="col" class="sort"><?php echo e(__('Payment Type')); ?></th>
                                <th scope="col" class="sort"><?php echo e(__('Receipt')); ?></th>
                                <th scope="col" class="text-right"><?php echo e(__('Action')); ?></th>
                            </tr>
                        </thead>
                        <?php if(!empty($orders) && count($orders) > 0): ?>
                            <tbody>
                                <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td scope="row">
                                            <?php if(\Auth::user()->can('show order')): ?>
                                                <a href="<?php echo e(route('orders.show',$order->id)); ?>" class="btn btn-sm btn-icon btn-outline-primary order2_badge">
                                                    <span class="btn-inner--text"><?php echo e($order->order_id); ?></span>
                                                </a>
                                            <?php else: ?>
                                                <span class="btn-inner--text"><?php echo e($order->order_id); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="order">
                                            <span class="h6 text-sm font-weight-bold mb-0"><?php echo e(Utility::dateFormat($order->created_at)); ?></span>
                                        </td>
                                        <td>
                                            <span class="client"><?php echo e($order->name); ?></span>
                                        </td>
                                        <td>
                                            <span class="value text-sm mb-0"><?php echo e(Utility::priceFormat($order->price)); ?></span>
                                        </td>
                                        <td>
                                            <span class="taxes text-sm mb-0"><?php echo e($order->payment_type); ?></span>
                                        </td>
                                        <td>
                                            <?php if($order->payment_type == 'Bank Transfer' && $plan->storage_limit < $total_storage && $plan->storage_limit != -1): ?>
                                                <a href="<?php echo e(asset(Storage::url($order->receipt))); ?>" title="Invoice"
                                                    target="_blank">
                                                    <i class="fas fa-file-invoice"></i>
                                                </a>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center justify-content-center">
                                                <!-- Actions -->
                                                <div class="actions ml-3">
                                                    <?php if($order->payment_status == 'pending' && $order->payment_type == 'Bank Transfer'): ?>
                                                        <div class="action-btn bg-warning ms-2">
                                                            <a  class="mx-3 btn btn-sm  align-items-center"
                                                                data-url="<?php echo e(route('user.bank_transfer.show',$order->id)); ?>"
                                                                data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip" title=""
                                                                data-title="<?php echo e(__('Payment Status')); ?>"
                                                                data-bs-original-title="<?php echo e(__('Payment Status')); ?>">
                                                                <i class="ti ti-caret-right text-white"></i>
                                                            </a>
                                                        </div>

                                                    <?php endif; ?>
                                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('show order')): ?>
                                                        <div class="action-btn bg-warning ms-2">
                                                            <a href="<?php echo e(route('orders.show',$order->id)); ?>" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" title="<?php echo e(__('Details')); ?>"> <span class="text-white"> <i class="ti ti-eye"></i></span></a>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete order')): ?>
                                                        <div class="action-btn bg-danger ms-2">
                                                            <?php echo Form::open(['method' => 'DELETE', 'route' => ['orders.destroy', $order->id]]); ?>

                                                                <a href="#!" class="mx-3 btn btn-sm align-items-center show_confirm" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(__('Delete')); ?>">
                                                                    <i class="ti ti-trash text-white"></i>
                                                                </a>
                                                            <?php echo Form::close(); ?>

                                                        </div>
                                                    <?php endif; ?>
                                                </div>
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\curso\resources\views/orders/index.blade.php ENDPATH**/ ?>