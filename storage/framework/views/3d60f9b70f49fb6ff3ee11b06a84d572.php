<?php
    $setting = App\Models\Utility::colorset();
    $color = (!empty($setting['color'])) ? $setting['color'] : 'theme-3';
    $logo = \App\Models\Utility::get_file('uploads/logo/');
    $company_logo = \App\Models\Utility::GetLogo();
    $settings = App\Models\Utility::settings();
    $footer_text = isset(Utility::settings()['footer_text']) ? Utility::settings()['footer_text'] : '';
    if($setting['cust_darklayout'] == 'on'){
        $company_logo = 'logo-light.png';
    }else{
        $company_logo = 'logo-dark.png';
    }
    $lang = \App::getLocale('lang');
    if($lang == 'ar' || $lang == 'he'){
        $setting['SITE_RTL'] = 'on';
    }
?>


<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" dir="<?php echo e($setting['SITE_RTL'] == 'on'?'rtl':''); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="LMSGo - Learning Management System">
    <meta name="keywords" content="Dashboard Template"/>
    <meta name="author" content="Rajodiya Infotech">

    <title> <?php echo e(( Utility::getValByName('title_text')) ? Utility::getValByName('title_text') : config('app.name', 'LMSGo-SaaS')); ?> - <?php echo $__env->yieldContent('page-title'); ?> </title>
    <!-- Favicon -->
    <link rel="icon" href="<?php echo e(\App\Models\Utility::get_file('uploads/logo/').'/favicon.png'.'?'. time()); ?>" type="image/png">

    <!-- font css -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/fonts/tabler-icons.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/fonts/feather.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/fonts/fontawesome.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/fonts/material.css')); ?>">

    <!-- vendor css -->

    <!-- vendor css -->
    <?php if($setting['SITE_RTL'] == 'on'): ?>
        <link rel="stylesheet" href="<?php echo e(asset('assets/css/style-rtl.css')); ?>">
    <?php endif; ?>
    <?php if( isset($setting['cust_darklayout']) && $setting['cust_darklayout'] == 'on'): ?>
        <link rel="stylesheet" href="<?php echo e(asset('assets/css/style-dark.css')); ?>">
    <?php else: ?>
        <link rel="stylesheet" href="<?php echo e(asset('assets/css/style.css')); ?>">
    <?php endif; ?>

    <?php if(isset($setting['SITE_RTL']) && $setting['SITE_RTL'] == 'on'): ?>
        <link rel="stylesheet" href="<?php echo e(asset('assets/css/custom-auth-rtl.css')); ?>" id="main-style-link">
    <?php else: ?>
        <link rel="stylesheet" href="<?php echo e(asset('assets/css/custom-auth.css')); ?>" id="main-style-link">
    <?php endif; ?>
    <?php if($setting['cust_darklayout']=='on'): ?>
        <link rel="stylesheet" href="<?php echo e(asset('assets/css/custom-auth-dark.css')); ?>" id="main-style-link">
    <?php endif; ?>
    <?php if($setting['cust_darklayout'] == 'on'): ?>
        <style>
            .g-recaptcha {
                filter: invert(1) hue-rotate(180deg) !important;
            }
        </style>
    <?php endif; ?>

</head>
<body class="<?php echo e($color); ?>">
    <div class="custom-login">
        <div class="login-bg-img">
            <img src="<?php echo e(asset('assets/images/auth/'.$color.'.svg')); ?>" class="login-bg-1">
            <img src="<?php echo e(asset('assets/images/auth/common.svg')); ?>" class="login-bg-2">
        </div>
        <div class="bg-login bg-primary"></div>
        <div class="custom-login-inner">
            <header class="dash-header">
                <nav class="navbar navbar-expand-md default">
                    <div class="container">
                        <div class="navbar-brand">
                            <a href="#">
                                <img src="<?php echo e($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-dark.png').'?'. time()); ?>" class="logo" alt="logo" loading="lazy" style="width: 150px;" />
                            </a>
                        </div>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarlogin">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarlogin" style="flex-grow: 0;">
                            <ul class="navbar-nav align-items-center ms-auto mb-2 mb-lg-0">
                                <?php echo $__env->make('landingpage::layouts.buttons', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                <?php echo $__env->yieldContent('language-bar'); ?>
                            </ul>
                        </div>
                    </div>
                </nav>
            </header>
            <main class="custom-wrapper">
                <div class="custom-row">
                    <div class="card">
                        <?php echo $__env->yieldContent('content'); ?>
                    </div>
                </div>
            </main>
            <footer>
                <div class="auth-footer">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <span>&copy; <?php echo e(date('Y')); ?> <?php echo e((App\Models\Utility::getValByName('footer_text')) ? App\Models\Utility::getValByName('footer_text') :config('app.name', 'LmsGo Saas')); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</body>

<!-- Required Js -->
<script src="<?php echo e(asset('assets/js/vendor-all.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/plugins/bootstrap.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/plugins/feather.min.js')); ?>"></script>
<script src="<?php echo e(asset('js/jquery.min.js')); ?>"></script>
<script>
    feather.replace();
</script>
<script>
    feather.replace();
    var pctoggle = document.querySelector("#pct-toggler");
    if (pctoggle) {
        pctoggle.addEventListener("click", function() {
            if (
                !document.querySelector(".pct-customizer").classList.contains("active")
            ) {
                document.querySelector(".pct-customizer").classList.add("active");
            } else {
                document.querySelector(".pct-customizer").classList.remove("active");
            }
        });
    }
    function removeClassByPrefix(node, prefix) {
        for (let i = 0; i < node.classList.length; i++) {
            let value = node.classList[i];
            if (value.startsWith(prefix)) {
                node.classList.remove(value);
            }
        }
    }
</script>

<script src="<?php echo e(asset('js/site.core.js')); ?>"></script>
<script src="<?php echo e(asset('js/site.js')); ?>"></script>


<?php echo $__env->yieldPushContent('custom-scripts'); ?>
<?php if($settings['enable_cookie'] == 'on'): ?>
        <?php echo $__env->make('layouts.cookie_consent', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>
</html>
<?php /**PATH C:\xampp\htdocs\curso\resources\views/layouts/auth.blade.php ENDPATH**/ ?>