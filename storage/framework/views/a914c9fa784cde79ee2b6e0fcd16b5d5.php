<?php
$logo = \App\Models\Utility::get_file('uploads/logo/');
// $company_logo = Utility::getValByName('company_logo');
$company_logo = \App\Models\Utility::GetLogo();
$user = \Auth::user();
$plan = $user->currentPlan;

$profile=\App\Models\Utility::get_file('uploads/profile/');
$current_store = \Auth::user()->currentstore();

if ($currantLang == null) {
    $currantLang = 'en';
}
$LangName = $user->currentlang;
?>

<?php if(isset($setting['cust_theme_bg']) && $setting['cust_theme_bg'] == 'on'): ?>
    <header class="dash-header transprent-bg">
<?php else: ?>
    <header class="dash-header">
<?php endif; ?>
    <div class="header-wrapper">
        <div class="me-auto dash-mob-drp">
            <ul class="list-unstyled">
                <li class="dash-h-item mob-hamburger">
                    <a href="#!" class="dash-head-link" id="mobile-collapse">
                        <div class="hamburger hamburger--arrowturn">
                            <div class="hamburger-box">
                                <div class="hamburger-inner"></div>
                            </div>
                        </div>
                    </a>
                </li>
                <li class="dropdown dash-h-item drp-company">
                    <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" aria-expanded="false">
                        
                        <span class="theme-avtar"><img alt="Image placeholder"  style="width:30px;"
                                src="<?php echo e(!empty($users->avatar) ? $profile . $users->avatar : $profile . '/avatar.png'); ?>"></span>
                        <span class="hide-mob ms-2"><?php echo e(__('Hi')); ?>, <?php echo e(\Auth::user()->name); ?>!</span>
                        <i class="ti ti-chevron-down drp-arrow nocolor hide-mob"></i>
                    </a>

                    <div class="dropdown-menu dash-h-dropdown">
                        <a href="<?php echo e(route('profile')); ?>" class="dropdown-item">
                            <i class="ti ti-user"></i>
                            <span><?php echo e(__('My profile')); ?></span>
                        </a>

                        <a href="<?php echo e(route('logout')); ?>" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('frm-logout').submit();">
                            <i class="ti ti-power"></i>
                            <span><?php echo e(__('Logout')); ?></span>
                        </a>
                        <form id="frm-logout" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none">
                            <?php echo e(csrf_field()); ?>

                        </form>
                    </div>
                </li>
            </ul>
        </div>
        <div class="ms-auto">
            <ul class="list-unstyled">
                <?php if (is_impersonating($guard = null)) : ?>
                    <li class="dropdown dash-h-item drp-company">
                        <a class="btn btn-danger btn-sm me-3" href="<?php echo e(route('exit.company')); ?>"><i class="ti ti-ban"></i>
                            <?php echo e(__('Exit Company Login')); ?>

                        </a>
                    </li>
                <?php endif; ?>
                <?php if(auth()->guard('web')->check()): ?>
                    <?php if(Auth::user()->type != 'super admin'): ?>
                        <li class="dropdown dash-h-item drp-language">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create store')): ?>
                                <a href="#" data-size="xl" data-url="<?php echo e(route('store-resource.create')); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Create New Store')); ?>" class="dash-head-link dropdown-toggle arrow-none me-0 cust-btn">
                                    <i class="ti ti-circle-plus"></i><span class="hide-mob"><?php echo e(__('Create New Store')); ?></span>
                                </a>
                            <?php endif; ?>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if(Auth::user()->type != 'super admin'): ?>
                    <li class="dropdown dash-h-item drp-language">
                        <a class="dash-head-link dropdown-toggle arrow-none me-0 cust-btn"
                        data-bs-toggle="dropdown"
                        href="#"
                        role="button"
                        aria-haspopup="false"
                        aria-expanded="false" data-bs-toggle="tooltip" data-bs-placement="bottom"   data-bs-original-title="Select your bussiness">
                        <i class="ti ti-building-store"></i>
                        <span class="hide-mob"><?php echo e(__(ucfirst($current_store->name))); ?></span>
                        <i class="ti ti-chevron-down drp-arrow nocolor"></i>
                        </a>
                        <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                            <?php
                                $user = \Auth::user()->currentuser();
                            ?>
                            <?php $__currentLoopData = $user->stores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $store): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($store->is_active): ?>
                                    <a href="<?php if(Auth::user()->current_store == $store->id): ?>#<?php else: ?> <?php echo e(route('change_store',$store->id)); ?> <?php endif; ?>" title="<?php echo e($store->name); ?>" class="dropdown-item">
                                        <?php if(Auth::user()->current_store == $store->id): ?>
                                            <i class="ti ti-checks text-primary"></i>
                                        <?php endif; ?>
                                        <span><?php echo e($store->name); ?></span>
                                    </a>
                                <?php else: ?>
                                    <a href="#" class="dropdown-item" title="<?php echo e(__('Locked')); ?>">
                                        <i class="ti ti-lock"></i>
                                        <span><?php echo e($store->name); ?></span>
                                        <?php if(isset($store->pivot->permission)): ?>
                                            <?php if($store->pivot->permission =='Owner'): ?>
                                                <span class="badge bg-primary"><?php echo e(__($store->pivot->permission)); ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary"><?php echo e(__('Shared')); ?></span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </a>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <div class="dropdown-divider m-0"></div>
                        </div>
                    </li>
                <?php endif; ?>
                <li class="dropdown dash-h-item drp-language">

                    <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="ti ti-world nocolor"></i>
                        <span class="drp-text hide-mob"><?php echo e(ucFirst($LangName->fullName)); ?></span>
                        <i class="ti ti-chevron-down drp-arrow nocolor"></i>
                    </a>
                    <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                        <?php $__currentLoopData = App\Models\Utility::languages(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('change.language', $code)); ?>"
                                class="dropdown-item <?php echo e($currantLang == $code ? 'text-primary' : ''); ?>">
                                <span><?php echo e(ucFirst($lang)); ?></span>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create language')): ?>
                            <a href="#" class="dropdown-item py-1 text-primary" data-ajax-popup="true" data-size="md" data-title="<?php echo e(__('Create Language')); ?>" data-url="<?php echo e(route('create.language')); ?>">
                                <?php echo e(__('Create Language')); ?>

                            </a>
                        <?php endif; ?>
                        <?php if(Auth::user()->type == 'super admin'): ?>
                            <a href="<?php echo e(route('manage.language',[$currantLang])); ?>" class="dropdown-item py-1 text-primary">
                                <?php echo e(__('Manage Language')); ?>

                            </a>
                        <?php endif; ?>
                    </div>
                </li>

            </ul>
        </div>
    </div>
</header>


<?php /**PATH C:\xampp\htdocs\curso\resources\views/partials/admin/header.blade.php ENDPATH**/ ?>