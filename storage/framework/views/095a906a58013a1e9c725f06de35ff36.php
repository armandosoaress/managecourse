<?php
    $logo = \App\Models\Utility::get_file('uploads/logo/');
    $users = \Auth::user();
    $currantLang = $users->currentLanguages();
    $languages = Utility::languages();
    $footer_text = isset(Utility::settings()['footer_text']) ? Utility::settings()['footer_text'] : '';
    $settings = App\Models\Utility::settings();
    $setting = App\Models\Utility::colorset();
    $color = (!empty($setting['color'])) ? $setting['color'] : 'theme-3';

    $lang = \App::getLocale('lang');
    if($lang == 'ar' || $lang == 'he'){
        $setting['SITE_RTL'] = 'on';
    }
?>


<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" dir="<?php echo e($setting['SITE_RTL'] == 'on' ? 'rtl' : ''); ?>">
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<?php echo $__env->make('partials.admin.head', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<body class="<?php echo e($color); ?>">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>

    <?php echo $__env->make('partials.admin.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php echo $__env->make('partials.admin.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <div class="page-content">
        <?php echo $__env->make('partials.admin.content', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>

    <footer class="dash-footer" id="footer-main">
        <div class="footer-wrapper">
            <div class="py-1">
                <p class="text-sm mb-0">&copy; <?php echo e(date('Y')); ?> <?php echo e((App\Models\Utility::getValByName('footer_text')) ? App\Models\Utility::getValByName('footer_text') :config('app.name', 'LmsGo Saas')); ?></p>
            </div>
        </div>
    </footer>

    <div class="modal fade" id="commonModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modelCommanModelLabel"></h5>
                    <a type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></a>
                </div>
                <div class="modal-body">

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="commonModalOver" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modelCommanModelLabel"></h5>
                    <a type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></a>
                </div>
                <div class="modal-body">

                </div>
            </div>
        </div>
    </div>

    <?php echo $__env->make('partials.admin.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php if($settings['enable_cookie'] == 'on'): ?>
        <?php echo $__env->make('layouts.cookie_consent', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
</body>



</html>
<?php /**PATH C:\xampp\htdocs\curso\resources\views/layouts/admin.blade.php ENDPATH**/ ?>