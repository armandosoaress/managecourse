<?php
    $logo=\App\Models\Utility::get_file('uploads/logo/');
    $company_logo = \App\Models\Utility::GetLogo();
    if(\Auth::user()->type!='Owner' && \Auth::user()->type!='super admin'){
        $user = \App\Models\User::where('id',\Auth::user()->created_by)->first();
    }
    else {
        $user = \Auth::user();
    }
    $plan =$user->currentPlan;
?>

<?php if(isset($setting['cust_theme_bg']) && $setting['cust_theme_bg'] == 'on'): ?>
    <nav class="dash-sidebar light-sidebar transprent-bg">
<?php else: ?>
    <nav class="dash-sidebar light-sidebar">
<?php endif; ?>

    <div class="navbar-wrapper">
        <div class="m-header  main-logo">
            <a href="<?php echo e(route('dashboard')); ?>" class="b-brand">
                <!-- ========   change your logo hear   ============ -->
                <img src="<?php echo e($logo . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-dark.png') .'?'. time()); ?>"
                    alt="<?php echo e(config('app.name', 'LMSGo SaaS')); ?>" class="logo logo-lg nav-sidebar-logo" />

                    
            </a>
        </div>
        <div class="navbar-content">
            <ul class="dash-navbar">
                <?php if(Auth::user()->type == 'super admin'): ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage dashboard')): ?>
                        <li class="dash-item <?php echo e((\Request::route()->getName()=='dashboard') ? ' active' : ''); ?>">
                            <a href="<?php echo e(route('dashboard')); ?>" class="dash-link"><span class="dash-micon"><i class="ti ti-home"></i></span><span class="dash-mtext"><?php echo e(__('Dashboard')); ?></span></a>
                        </li>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage store')): ?>
                        <li class="dash-item <?php echo e((\Request::route()->getName()=='store-resource.index' || \Request::route()->getName()=='store.grid' || \Request::route()->getName()=='store.subDomain' || \Request::route()->getName()=='store.customDomain') ? ' active' : ''); ?>">
                            <a href="<?php echo e(route('store-resource.index')); ?>" class="dash-link"><span class="dash-micon"><i class="ti ti-shopping-cart"></i></span><span class="dash-mtext"><?php echo e(__('Stores')); ?></span></a>
                        </li>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage coupon')): ?>
                        <li class="dash-item <?php echo e((\Request::route()->getName()=='coupons.index' || \Request::route()->getName()=='coupons.show')  ? ' active' : ''); ?>">
                            <a href="<?php echo e(route('coupons.index')); ?>" class="dash-link"><span class="dash-micon"><i class="ti ti-discount-2"></i></span><span class="dash-mtext"><?php echo e(__('Coupons')); ?></span></a>
                        </li>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage plan')): ?>
                        <li class="dash-item <?php echo e((\Request::route()->getName()=='plans.index' || \Request::route()->getName()=='stripe') ? ' active' : ''); ?>">
                            <a href="<?php echo e(route('plans.index')); ?>" class="dash-link"><span class="dash-micon"><i class="ti ti-award"></i></span><span class="dash-mtext"><?php echo e(__('Plans')); ?></span></a>
                        </li>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage plan request')): ?>
                        <li class="dash-item <?php echo e((\Request::route()->getName()=='plan_request.index') ? ' active' : ''); ?>">
                            <a href="<?php echo e(route('plan_request.index')); ?>" class="dash-link <?php echo e(request()->is('plan_request*') ? 'active' : ''); ?>"><span class="dash-micon"><i class="ti ti-brand-telegram"></i></span><span class="dash-mtext"><?php echo e(__('Plan Request')); ?></span></a>
                        </li>
                    <?php endif; ?>

                    <?php echo $__env->make('landingpage::menu.landingpage', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage settings')): ?>
                        <li class="dash-item <?php echo e((\Request::route()->getName()=='setting.index' || \Request::route()->getName()=='store.editproducts') ? ' active' : ''); ?>">
                            <a href="<?php echo e(route('settings')); ?>" class="dash-link"><span class="dash-micon"><i class="ti ti-settings"></i></span><span class="dash-mtext">
                                    <?php echo e(__('Settings')); ?>

                            </span></a>
                        </li>
                    <?php endif; ?>

                <?php else: ?>
                    <?php if( Gate::check('manage dashboard') || Gate::check('manage store analytics') || Gate::check('manage order') || Gate::check('show order')): ?>
                        <li class="dash-item dash-hasmenu <?php echo e((\Request::route()->getName()=='orders.index' || \Request::route()->getName()=='orders.show') ? ' active dash-trigger' : ''); ?>">
                            <a href="#!" class="dash-link"><span class="dash-micon"><i class="ti ti-home"></i></span><span class="dash-mtext"><?php echo e(__('Dashboard')); ?></span><span class="dash-arrow"><i data-feather="chevron-right"></i></span></a>
                            <ul class="dash-submenu">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage dashboard')): ?>
                                    <li class="dash-item">
                                        <a class="dash-link" href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage store analytics')): ?>
                                    <li class="dash-item">
                                        <a class="dash-link" href="<?php echo e(route('storeanalytic')); ?>"><?php echo e(__('Store Analytics')); ?></a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage order')): ?>
                                    <li class="dash-item <?php echo e((\Request::route()->getName()=='orders.index' || \Request::route()->getName()=='orders.show') ? ' active' : ''); ?>">
                                        <a class="dash-link" href="<?php echo e(route('orders.index')); ?>"><?php echo e(__('Orders')); ?></a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage store settings')): ?>
                        <li class="dash-item dash-hasmenu <?php echo e((Request::segment(1) == 'theme.index' || \Request::route()->getName()=='store.editproducts')  ? ' active dash-trigger ' : 'collapsed'); ?>">
                            <a href="<?php echo e(route('theme.index')); ?>" class="dash-link"><span class="dash-micon"><i class="ti ti-layout-2"></i></span><span class="dash-mtext"><?php echo e(__('Themes')); ?></span></a>
                        </li>
                    <?php endif; ?>

                    <?php if( Gate::check('manage user') || Gate::check('manage role')): ?>
                        <li class="dash-item dash-hasmenu  <?php echo e((Request::segment(1) == 'users' || Request::segment(1) == 'users-logs'|| Request::segment(1) == 'roles' || Request::segment(1) == 'permissions' )?' active dash-trigger':''); ?>">
                            <a href="#!" class="dash-link "><span class="dash-micon"><i class="ti ti-users"></i></span><span class="dash-mtext"><?php echo e(__('Staff')); ?></span>
                                <span class="dash-arrow"><i data-feather="chevron-right"></i></span>
                            </a>
                            <ul class="dash-submenu <?php echo e(( Request::segment(1) == 'roles' || Request::segment(1) == 'permissions')?'show':''); ?>">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage user')): ?>
                                    <li class="dash-item <?php echo e((Request::route()->getName() == 'users.index' || Request::route()->getName() == 'users.create' || Request::route()->getName() == 'users.edit' || Request::route()->getName() == 'users.logs' || Request::route()->getName() == 'userslog.view' || Request::route()->getName() == 'userslog.destroy') ? ' active' : ''); ?>">
                                        <a class="dash-link" href="<?php echo e(route('users.index')); ?>"><?php echo e(__('User')); ?></a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage role')): ?>
                                    <li class="dash-item <?php echo e((Request::route()->getName() == 'roles.index' || Request::route()->getName() == 'roles.create' || Request::route()->getName() == 'roles.edit') ? ' active' : ''); ?>">
                                        <a class="dash-link" href="<?php echo e(route('roles.index')); ?>"><?php echo e(__('Role')); ?></a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    <?php endif; ?>
                    <?php if( Gate::check('manage course') ||  Gate::check('manage category') ||   Gate::check('manage subcategory') ||  Gate::check('manage custom page') ||  Gate::check('manage blog') ||  Gate::check('manage subscriber') ||  Gate::check('manage course coupon')): ?>
                        <li class="dash-item dash-hasmenu <?php echo e((\Request::route()->getName()=='course.index' || \Request::route()->getName()=='course.create' || \Request::route()->getName()=='course.edit' || \Request::route()->getName()=='chapters.create' || \Request::route()->getName()=='chapters.edit' || \Request::route()->getName()=='category.index' || \Request::route()->getName()=='subcategory.index' || \Request::route()->getName()=='custom-page.index' || \Request::route()->getName()=='blog.index' || \Request::route()->getName()=='subscriptions.index' || \Request::route()->getName()=='product-coupon.index' || \Request::route()->getName()=='product-coupon.show') ? 'active dash-trigger' : ''); ?>">
                            <a href="#!" class="dash-link"><span class="dash-micon"><i class="ti ti-license"></i></span><span class="dash-mtext"><?php echo e(__('Shop')); ?></span><span class="dash-arrow"><i data-feather="chevron-right"></i></span></a>
                            <ul class="dash-submenu">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage course')): ?>
                                <li class="dash-item  <?php echo e((\Request::route()->getName()=='course.index' || \Request::route()->getName()=='course.create' || \Request::route()->getName()=='course.edit' || \Request::route()->getName()=='chapters.create' || \Request::route()->getName()=='chapters.edit') ? ' active' : ''); ?>">
                                    <a class="dash-link" href="<?php echo e(route('course.index')); ?>"> <?php echo e(__('Course')); ?></a>
                                </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage category')): ?>
                                    <li class="dash-item">
                                        <a class="dash-link" href="<?php echo e(route('category.index')); ?>"><?php echo e(__('Category')); ?></a>
                                    </li>
                                <?php endif; ?>

                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage subcategory')): ?>
                                    <li class="dash-item">
                                        <a class="dash-link" href="<?php echo e(route('subcategory.index')); ?>"><?php echo e(__('Subcategory')); ?></a>
                                    </li>
                                <?php endif; ?>

                                <?php if($plan->additional_page == 'on'): ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage custom page')): ?>
                                        <li class="dash-item">
                                            <a class="dash-link" href="<?php echo e(route('custom-page.index')); ?>"><?php echo e(__('Custom Page')); ?></a>
                                        </li>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if($plan->blog == 'on'): ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage blog')): ?>
                                        <li class="dash-item">
                                            <a class="dash-link" href="<?php echo e(route('blog.index')); ?>"><?php echo e(__('Blog')); ?></a>
                                        </li>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage subscriber')): ?>
                                    <li class="dash-item">
                                        <a class="dash-link" href="<?php echo e(route('subscriptions.index')); ?>"> <?php echo e(__('Subscriber')); ?></a>
                                    </li>
                                <?php endif; ?>

                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage course coupon')): ?>
                                    <li class="dash-item  <?php echo e((\Request::route()->getName()=='product-coupon.index' || \Request::route()->getName()=='product-coupon.show') ? ' active' : ''); ?>">
                                        <a class="dash-link" href="<?php echo e(route('product-coupon.index')); ?>"><?php echo e(__('Coupons')); ?></a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    <?php endif; ?>



                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage student')): ?>
                        <li class="dash-item dash-hasmenu <?php echo e(Request::segment(1) == 'student.index' || \Request::route()->getName()=='student.logs' || Request::route()->getName() == 'student.show'  ? ' active dash-trigger ' : 'collapsed'); ?>">
                            <a href="<?php echo e(route('student.index')); ?>" class="dash-link"><span class="dash-micon"><i class="ti ti-user"></i></span><span class="dash-mtext"><?php echo e(__('Student')); ?></span></a>
                        </li>
                    <?php endif; ?>


                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage plan')): ?>
                        <li class="dash-item <?php echo e((\Request::route()->getName()=='plans.index' || \Request::route()->getName()=='stripe') ? ' active' : ''); ?>">
                            <a href="<?php echo e(route('plans.index')); ?>" class="dash-link"><span class="dash-micon"><i class="ti ti-award"></i></span><span class="dash-mtext"><?php echo e(__('Plans')); ?></span></a>
                        </li>
                    <?php endif; ?>

                    <?php if( \Auth::user()->type == 'Owner'): ?>
                        <li class="dash-item <?php echo e((\Request::route()->getName()=='notification-templates.index') ? ' active' : ''); ?>">
                            <a href="<?php echo e(route('notification-templates.index')); ?>" class="dash-link"><span class="dash-micon"><i class="ti ti-notification"></i></span><span class="dash-mtext"><?php echo e(__('Notification')); ?></span></a>
                        </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage zoom meeting')): ?>
                        <li class="dash-item <?php echo e((\Request::route()->getName()=='zoom-meeting.index' || \Request::route()->getName()=='zoom-meeting.calender') ? ' active' : ''); ?>">
                            <a href="<?php echo e(route('zoom-meeting.index')); ?>" class="dash-link"><span class="dash-micon"><i class="ti ti-video"></i></span><span class="dash-mtext"><?php echo e(__('Zoom Meeting')); ?></span></a>
                        </li>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage store settings')): ?>
                        <li class="dash-item <?php echo e((\Request::route()->getName()=='setting.index' ) ? ' active' : ''); ?>">
                            <a href="<?php echo e(route('settings')); ?>" class="dash-link"><span class="dash-micon"><i class="ti ti-settings"></i></span><span class="dash-mtext">
                                    <?php echo e(__('Store Settings')); ?>

                            </span></a>
                        </li>
                    <?php endif; ?>

                <?php endif; ?>
            </ul>

        </div>
    </div>
</nav>





<?php /**PATH C:\xampp\htdocs\curso\resources\views/partials/admin/sidebar.blade.php ENDPATH**/ ?>