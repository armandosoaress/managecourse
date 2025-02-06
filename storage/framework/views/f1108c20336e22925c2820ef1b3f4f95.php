<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Themes')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('title'); ?>
    <?php echo e(__('Themes')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Home')); ?></a></li>
    <li class="breadcrumb-item"><?php echo e(__('Manage Themes')); ?></li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

    <!-- Listing -->
    <div class="">
        <?php echo e(Form::open(['route' => ['store.changetheme', $store_settings->id], 'method' => 'POST'])); ?>

        <div class="d-flex mb-3 align-items-center justify-content-between">
            <h3 class="mb-2"><?php echo e(__('Themes')); ?></h3>
            <input id="themefile" name="themefile" type="hidden" value="theme1">
            <?php echo e(Form::submit(__('Save Changes'), ['class' => 'btn btn-primary'])); ?>

        </div>

        <div class="border border-primary rounded p-3">
            <div class="row gy-4 ">
                <?php $__currentLoopData = Utility::themeOne(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-xl-3 col-lg-4 col-md-6 overflow-hidden cc-selector">
                        <div class="border border-primary rounded">
                            <div class="theme-card-inner">
                                <div class="screen theme-image border rounded">
                                    <img src="<?php echo e(asset(Storage::url('uploads/store_theme/' . $key . '/Home.png'))); ?>"
                                        class="color1 img-center pro_max_width pro_max_height <?php echo e($key); ?>_img"
                                        data-id="<?php echo e($key); ?>">
                                </div>
                                <div class="theme-content mt-3">
                                    <div class="row gutters-xs justify-content-center"
                                        id="<?php echo e($key); ?>">
                                        <?php $__currentLoopData = $v; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $css => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="col-auto">
                                                <label class="colorinput">
                                                    <input name="theme_color" type="radio"
                                                        value="<?php echo e($css); ?>"
                                                        data-key="theme<?php echo e($loop->iteration); ?>"
                                                        data-theme="<?php echo e($key); ?>"
                                                        data-imgpath="<?php echo e($val['img_path']); ?>"
                                                        class="colorinput-input color-<?php echo e($loop->index++); ?>"
                                                        <?php echo e(isset($store_settings['store_theme']) && $store_settings['store_theme'] == $css ? 'checked' : ''); ?>>
                                                    <span class="colorinput-color"
                                                        style="background:#<?php echo e($val['color']); ?>"></span>
                                                </label>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <div class="col-auto">
                                            <?php if(isset($store_settings['theme_dir']) && $store_settings['theme_dir'] == $key): ?>
                                                <a href="<?php echo e(route('store.editproducts', [$store_settings->slug, $key])); ?>"
                                                    class="btn btn-outline-primary theme_btn" type="button"
                                                    id="button-addon2"><?php echo e(__('Edit')); ?></a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php echo e(Form::close()); ?>

    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('script-page'); ?>
<script>
    $(document).on('click', 'input[name="theme_color"]', function() {
        var eleParent = $(this).attr('data-theme');
        $('#themefile').val(eleParent);
        // $('#themefile').val($(this).attr('data-key'));
        var imgpath = $(this).attr('data-imgpath');
        $('.' + eleParent + '_img').attr('src', imgpath);
    });

    $(document).ready(function() {
        setTimeout(function(e) {
            var checked = $("input[type=radio][name='theme_color']:checked");
            $('#themefile').val(checked.attr('data-theme'));
            // $('#themefile').val(checked.attr('data-key'));
            $('.' + checked.attr('data-theme') + '_img').attr('src', checked.attr('data-imgpath'));
        }, 300);
    });

    $(".color1").click(function() {
        var dataId = $(this).attr("data-id");
        $('#' + dataId).trigger('click');
        var first_check = $('#' + dataId).find('.color-0').trigger("click");
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\curso\resources\views/admin_store/theme.blade.php ENDPATH**/ ?>