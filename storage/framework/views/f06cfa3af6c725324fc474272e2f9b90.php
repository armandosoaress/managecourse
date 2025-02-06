<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Dashboard')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('title'); ?>
    <?php echo e(__('Dashboard')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><?php echo e(__('Home')); ?></li>
<?php $__env->stopSection(); ?>

<?php
    $logo=\App\Models\Utility::get_file('uploads/logo/');
    $company_logo = Utility::getValByName('company_logo');
    $profile=\App\Models\Utility::get_file('uploads/profile/');
    $logo1=\App\Models\Utility::get_file('uploads/is_cover_image/');
    $setting = App\Models\Utility::settings();
?>

<?php $__env->startSection('content'); ?>
    <div class="page-content">
        <!-- Page title -->
        <?php if(\Auth::user()->type == 'super admin'): ?>
            <div class="row">
                <!-- [ sample-page ] start -->
                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-xxl-6">
                            <div class="row">
                                <div class="col-lg-4 col-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="theme-avtar bg-primary">
                                                <i class="fas fa-cube"></i>
                                            </div>
                                            <h6 class="mb-3 mt-3"><?php echo e(__('Total Store')); ?></h6>
                                            <h4 class="mb-0"><?php echo e($user->total_user); ?></h4>
                                            <div class="col-auto">
                                                <h6 class="text-muted mb-1 mt-2"><?php echo e(__('Paid Store')); ?></h6>
                                                <span
                                                    class="h6 font-weight-bold mb-0 "><?php echo e($user['total_paid_user']); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="theme-avtar bg-warning">
                                                <i class="fas fa-cart-plus"></i>
                                            </div>
                                            <h6 class="mb-3 mt-3"><?php echo e(__('Total Orders')); ?></h6>
                                            <h4 class="mb-0"><?php echo e($user->total_orders); ?></h4>
                                            <div class="col-auto">
                                                <h6 class="text-muted mb-1 mt-2"><?php echo e(__('Total Order Amount')); ?></h6>
                                                <span
                                                    class="h6 font-weight-bold mb-0 "><?php echo e(env('CURRENCY_SYMBOL') . $user['total_orders_price']); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="theme-avtar bg-danger">
                                                <i class="fas fa-shopping-bag"></i>
                                            </div>
                                            <h6 class="mb-3 mt-3"><?php echo e(__('Total Plans')); ?></h6>
                                            <h4 class="mb-0"><?php echo e($user['total_plan']); ?></h4>
                                            <div class="col-auto">
                                                <h6 class="text-muted mb-1 mt-2"><?php echo e(__('Most Purchase Plan')); ?></h6>
                                                <span
                                                    class="h6 font-weight-bold mb-0 "><?php echo e(!empty($user['most_purchese_plan']) ? $user['most_purchese_plan'] : '-'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5><?php echo e(__('Recent Orders')); ?></h5>
                                </div>
                                <div class="card-body">
                                    <div id="plan_order" data-color="primary" data-height="230"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- [ sample-page ] end -->
            </div>
        <?php else: ?>
            <div class="row">
                <!-- [ sample-page ] start -->
                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-xxl-5">
                            <div class="card">
                                <div class="card-body stats welcome-card">
                                    <div class="row align-items-center mb-4">
                                        <div class="col-xxl-12">
                                            <h3 class="mb-1" id="greetings"></h3>
                                            <h4 class="f-w-400">
                                                <img src="<?php echo e(\App\Models\Utility::get_file('uploads/profile/' . (!empty(Auth::user()->avatar) ? Auth::user()->avatar : 'avatar.png'))); ?>" alt="user-image" class="wid-35 me-2 img-thumbnail rounded-circle"><?php echo e(__(Auth::user()->name)); ?>

                                            </h4>
                                            <p><?php echo e(__('Have a nice day! Did you know that you can quickly add your favorite course or category to the store?')); ?></p>
                                            <div class="dropdown quick-add-btn">
                                                <a class="btn btn-primary btn-q-add dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false"> <i class="ti ti-plus drp-icon"></i>
                                                    <span class="ms-2 me-2"><?php echo e(__('Quick add')); ?></span>
                                                </a>
                                                <?php if(Gate::check('create course') || Gate::check('create category') || Gate::check('create subcategory')): ?>
                                                    <div class="dropdown-menu">
                                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create course')): ?>
                                                            <a href="<?php echo e(route('course.create')); ?>" class="dropdown-item"><span><?php echo e(__('Add new Course')); ?></span>
                                                            </a>
                                                        <?php endif; ?>

                                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create category')): ?>
                                                            <a href="#" data-size="md" data-url="<?php echo e(route('category.create')); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Create New Category')); ?>"
                                                            class="dropdown-item" data-bs-placement="top"><span><?php echo e(__('Add new Category')); ?></span></a>
                                                        <?php endif; ?>

                                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create subcategory')): ?>
                                                            <a href="#" data-size="md" data-url="<?php echo e(route('subcategory.create')); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Create New Subcategory')); ?>" class="dropdown-item" data-bs-placement="top"><span><?php echo e(__('Add new Subcategory')); ?></span></a>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card min-h-390">
                                <div class="card-header d-flex justify-content-between">
                                    <h5 ><?php echo e(__('Storage Status')); ?> <small>(<?php echo e($users->storage_limit . 'MB'); ?> / <?php echo e($plan->storage_limit . 'MB'); ?>)</small></h5>
                                </div>
                                <div class="shadow-none mb-0">
                                    <div class="card-body border rounded  p-3">
                                        <div id="device-chart"></div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-xxl-7">
                            <div class="row">
                                <div class="col-lg-3 col-6">
                                    <div class="card">
                                        <div class="card-body stats">
                                            <div class="theme-avtar bg-primary qrcode">
                                                <?php echo QrCode::generate($store_id['store_url']); ?>

                                            </div>
                                            <h6 class="mb-3 mt-4 "><?php echo e($store_id->name); ?></h6>
                                            <a href="#" class="btn btn-primary btn-sm text-sm cp_link mb-0" data-link="<?php echo e($store_id['store_url']); ?>" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="<?php echo e(__('Click to copy link')); ?>"><?php echo e(__('Store Link')); ?></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <div class="card">
                                        <div class="card-body stats">
                                            <div class="theme-avtar bg-info">
                                                <i class="fas fa-cube"></i>
                                            </div>
                                            <h6 class="mb-3 mt-4 "><?php echo e(__('Total Course')); ?></h6>
                                            <h4 class="mb-0"><?php echo e($newproduct); ?></h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <div class="card">
                                        <div class="card-body stats">
                                            <div class="theme-avtar bg-warning">
                                                <i class="fas fa-cart-plus"></i>
                                            </div>
                                            <h6 class="mb-3 mt-4 "><?php echo e(__('Total Sales')); ?></h6>
                                            <h4 class="mb-0"><?php echo e(Utility::priceFormat($totle_sale)); ?></h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <div class="card">
                                        <div class="card-body stats">
                                            <div class="theme-avtar bg-danger">
                                                <i class="fas fa-shopping-bag"></i>
                                            </div>
                                            <h6 class="mb-3 mt-4 "><?php echo e(__('Total Orders')); ?></h6>
                                            <h4 class="mb-0"><?php echo e($totle_order); ?></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card min-h-390 overflow-auto">
                                <div class="card-header d-flex justify-content-between">
                                    <h5><?php echo e(__('Top Course')); ?></h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th scope="col" class="sort" data-sort="name"><?php echo e(__('Course')); ?> </th>
                                                    <th scope="col" class="sort text-right" data-sort="completion"> <?php echo e(__('Price')); ?></th>
                                                </tr>
                                            </thead>
                                            <?php if(count($products) > 0 && !empty($item_id) && !empty($products)): ?>
                                                <tbody class="list">
                                                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php $__currentLoopData = $item_id; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <?php if($product->id == $item): ?>
                                                                <tr>
                                                                    <th scope="row">
                                                                        <div class="media align-items-center gap-3">
                                                                            <div>
                                                                                <?php if(!empty($product->thumbnail)): ?>
                                                                                    <img alt="Image placeholder" src="<?php echo e(\App\Models\Utility::get_file('uploads/thumbnail/' . $product->thumbnail)); ?>" width="80px">
                                                                                <?php else: ?>
                                                                                    <img alt="Image placeholder" src="<?php echo e(asset(Storage::url('uploads/thumbnail/default.jpg'))); ?>" class="" style="width: 80px;">
                                                                                <?php endif; ?>
                                                                            </div>
                                                                            <div class="media-body ml-4">
                                                                                <span class="mb-0 h6 text-sm"><?php echo e($product->title); ?></span>
                                                                            </div>
                                                                        </div>
                                                                    </th>
                                                                    <td class="text-right">
                                                                        <div>
                                                                            <span class="completion mr-2 text-dark text-right"><?php echo e(Utility::priceFormat($product->price)); ?></span>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            <?php endif; ?>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card min-h-390 overflow-auto">
                                <div class="card-header">
                                    <h5><?php echo e(__('Orders')); ?></h5>
                                </div>
                                <div class="card-body">
                                    <div id="apex-dashborad" data-color="primary" data-height="230"></div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header d-flex justify-content-between">
                                    <h5><?php echo e(__('Recent Orders')); ?></h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th scope="col"><?php echo e(__('Orders')); ?></th>
                                                    <th scope="col" class="sort"><?php echo e(__('Date')); ?></th>
                                                    <th scope="col" class="sort"><?php echo e(__('Name')); ?></th>
                                                    <th scope="col" class="sort"><?php echo e(__('Value')); ?></th>
                                                    <th scope="col" class="sort"><?php echo e(__('Payment Type')); ?></th>
                                                    <th scope="col" class="text-center"><?php echo e(__('Status')); ?></th>
                                                    <th scope="col" class="text-end"><?php echo e(__('Action')); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if(!empty($new_orders) && count($new_orders) > 0): ?>
                                                    <?php $__currentLoopData = $new_orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php if($order->status != 'Cancel Order'): ?>
                                                            <tr>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <a href="<?php echo e(route('orders.show', $order->id)); ?>" class="btn btn-outline-primary btn-sm text-sm order-badge">
                                                                            <span class="btn-inner--text"><?php echo e($order->order_id); ?></span>
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <h6 class="m-0">
                                                                        <?php echo e(Utility::dateFormat($order->created_at)); ?>

                                                                    </h6>
                                                                </td>
                                                                <td>
                                                                    <h6 class="m-0"><?php echo e($order->name); ?></h6>
                                                                </td>
                                                                <td>
                                                                    <h6 class="m-0"> <?php echo e(Utility::priceFormat($order->price)); ?> <h6>
                                                                </td>
                                                                <td>
                                                                    <h6 class="m-0"><?php echo e($order->payment_type); ?><h6>
                                                                </td>
                                                                <td>
                                                                    <div class="actions ml-3">
                                                                        <div class="d-flex row justify-content-center">
                                                                            <button type="button" class="btn btn-sm <?php echo e($order->payment_status == 'success' || $order->payment_status == 'succeeded' || $order->payment_status == 'approved' ? 'btn-soft-success' : 'btn-soft-info'); ?> btn-icon rounded-pill">
                                                                                <span class="btn-inner--icon">
                                                                                    <?php if($order->payment_status == 'pendding'): ?>
                                                                                        <i class="fas fa-check"></i>
                                                                                    <?php else: ?>
                                                                                        <i class="fa fa-check-double"></i>
                                                                                    <?php endif; ?>
                                                                                </span>
                                                                                <?php if($order->payment_status == 'pendding'): ?>
                                                                                    <span class="btn-inner--text">
                                                                                        <?php echo e(__('Pending')); ?>:
                                                                                        <?php echo e(\App\Models\Utility::dateFormat($order->created_at)); ?>

                                                                                    </span>
                                                                                <?php else: ?>
                                                                                    <span class="btn-inner--text">
                                                                                        <?php echo e(__('Delivered')); ?>:
                                                                                        <?php echo e(\App\Models\Utility::dateFormat($order->updated_at)); ?>

                                                                                    </span>
                                                                                <?php endif; ?>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="actions ml-3">
                                                                        <div class="d-flex align-items-center justify-content-end">
                                                                            <div class="action-btn bg-warning ms-2">
                                                                                <a href="<?php echo e(route('orders.show', $order->id)); ?>" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(__('Details')); ?>"><i class="ti ti-eye text-white"></i></a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php endif; ?>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- [ sample-page ] end -->
            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('script-page'); ?>
    <?php if(\Auth::user()->type == 'super admin'): ?>
        <script>
            (function() {
                var options = {
                    chart: {
                        height: 250,
                        type: 'area',
                        toolbar: {
                            show: false,
                        },
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        width: 2,
                        curve: 'smooth'
                    },


                    series: [{
                        name: "Order",
                        data: <?php echo json_encode($chartData['data']); ?>

                    }],

                    xaxis: {
                        axisBorder: {
                            show: !1
                        },
                        type: "MMM",
                        categories: <?php echo json_encode($chartData['label']); ?>,
                        title: {
                            text: 'Days'
                        }

                    },
                    colors: ['#e83e8c'],

                    grid: {
                        strokeDashArray: 4,
                    },
                    legend: {
                        show: false,
                    },
                    yaxis: {
                        tickAmount: 3,
                        title: {
                            text: 'Amountsidebar'
                        }

                    }
                };
                var chart = new ApexCharts(document.querySelector("#plan_order"), options);
                chart.render();
            })();
        </script>
    <?php else: ?>
        <script>
            var timezone = '<?php echo e(!empty($setting['timezone']) ? $setting['timezone'] : 'Asia/Kolkata'); ?>';

            let today = new Date(new Date().toLocaleString("en-US", {
                timeZone: timezone
            }));
            var curHr = today.getHours()
            var target = document.getElementById("greetings");

            if (curHr < 12) {
                target.innerHTML = "Good Morning,";
            } else if (curHr < 17) {
                target.innerHTML = "Good Afternoon,";
            } else {
                target.innerHTML = "Good Evening,";
            }
        </script>
        <script>
            $(document).ready(function() {
                $('.cp_link').on('click', function() {
                    var value = $(this).attr('data-link');
                    var $temp = $("<input>");
                    $("body").append($temp);
                    $temp.val(value).select();
                    document.execCommand("copy");
                    $temp.remove();
                    show_toastr('Success', '<?php echo e(__('Link copied')); ?>', 'success')
                });
            });

            (function() {
                var options = {
                    chart: {
                        height: 250,
                        type: 'area',
                        toolbar: {
                            show: false,
                        },
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        width: 2,
                        curve: 'smooth'
                    },


                    series: [{
                        name: "<?php echo e(__('Order')); ?>",
                        data: <?php echo json_encode($chartData['data']); ?>

                    }],

                    xaxis: {
                        axisBorder: {
                            show: !1
                        },
                        type: "MMM",
                        categories: <?php echo json_encode($chartData['label']); ?>,
                        title: {
                            text: 'Days'
                        }

                    },
                    colors: ['#e83e8c'],

                    grid: {
                        strokeDashArray: 4,
                    },
                    legend: {
                        show: false,
                    },
                    yaxis: {
                        tickAmount: 3,
                        title: {
                            text: 'Amount'
                        }
                    }
                };
                var chart = new ApexCharts(document.querySelector("#apex-dashborad"), options);
                chart.render();
            })();
        </script>

        <script>
            (function () {
                var options = {
                    series: [<?php echo e($storage_limit); ?>],
                    chart: {
                        height: 590,
                        type: 'radialBar',
                        offsetY: -30,
                        sparkline: {
                            enabled: true
                        }
                    },
                    plotOptions: {
                        radialBar: {
                            startAngle: -90,
                            endAngle: 90,
                            track: {
                                background: "#e7e7e7",
                                strokeWidth: '97%',
                                margin: 5, // margin is in pixels
                            },
                            dataLabels: {
                                name: {
                                    show: true
                                },
                                value: {
                                    offsetY: -50,
                                    fontSize: '20px'
                                }
                            }
                        }
                    },
                    grid: {
                        padding: {
                            top: -10
                        }
                    },
                    colors: ["#6FD943"],
                    labels: ['Used'],
                };
                var chart = new ApexCharts(document.querySelector("#device-chart"), options);
                chart.render();
            })();
        </script>
    <?php endif; ?>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\curso\resources\views/home.blade.php ENDPATH**/ ?>