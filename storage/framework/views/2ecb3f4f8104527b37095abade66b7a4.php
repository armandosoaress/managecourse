
<?php echo e(Form::open(array('url'=>'store-resource','method'=>'post'))); ?>

<div class="row">
    <?php if(\Auth::user()->type == 'super admin'): ?>
        <?php if(Utility::getValByName('chatgpt_key')): ?>
            <div class="text-end align-items-center justify-content-between">
                <a href="#" class="btn btn-sm btn-primary" data-size="md" data-ajax-popup-over="true" data-url="<?php echo e(route('generate',['store'])); ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(__('Generate')); ?>" data-title="<?php echo e(__('Generate Content With AI')); ?>">
                    <i class="fas fa-robot"></i> <?php echo e(__('Generate With AI')); ?>

                </a>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <?php
            $plansetting = \App\Models\Utility::plansetting();
        ?>
        <?php if($plansetting['enable_chatgpt'] && $plansetting['enable_chatgpt']=='on'): ?>
            <div class="text-end align-items-center justify-content-between">
                <a href="#" class="btn btn-sm btn-primary" data-size="md" data-ajax-popup-over="true" data-url="<?php echo e(route('generate',['store'])); ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(__('Generate')); ?>" data-title="<?php echo e(__('Generate Content With AI')); ?>">
                    <i class="fas fa-robot"></i> <?php echo e(__('Generate With AI')); ?>

                </a>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <div class="col-12">
        <div class="form-group">
            <?php echo e(Form::label('store_name',__('Store Name'))); ?>

            <?php echo e(Form::text('store_name',null,array('class'=>'form-control','placeholder'=>__('Enter Store Name'),'required'=>'required'))); ?>

        </div>
        <?php if(\Auth::user()->type != 'super admin'): ?>
            <div class="form-group">
                <?php echo e(Form::label('store_theme',__('Store Theme'))); ?>

            </div>
            <div class="border border-primary rounded p-3">
                <div class="row gy-4 ">
                    <input id="themefile1" name="themefile" type="hidden" value="theme1">
                    <?php $__currentLoopData = Utility::themeOne(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-xl-4 col-lg-4 col-md-6 overflow-hidden cc-selector mb-2">
                            <div class="border border-primary rounded">
                            <div class="theme-card-inner">
                                <div class="screen border rounded ">
                                    <img src="<?php echo e(asset(Storage::url('uploads/store_theme/' . $key . '/Home.png'))); ?>"
                                        class="color1 img-center pro_max_width pro_max_height <?php echo e($key); ?>_img"
                                        data-id="<?php echo e($key); ?>">
                                </div>
                                <div class="theme-content mt-3">
                                    <div class="row gutters-xs justify-content-center"
                                        id="<?php echo e('radio_'.$key); ?>">
                                        <?php $__currentLoopData = $v; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $css => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="col-auto">
                                                <label class="colorinput">
                                                    <input name="theme_color" type="radio"
                                                        value="<?php echo e($css); ?>"
                                                        data-key="theme<?php echo e($loop->iteration); ?>"
                                                        data-theme="<?php echo e($key); ?>"
                                                        data-imgpath="<?php echo e($val['img_path']); ?>"
                                                        class="colorinput-input color-<?php echo e($loop->index++); ?>"
                                                        <?php echo e(isset($store_settings['store_theme']) && $store_settings['store_theme'] == $css && $store_settings['theme_dir'] == $key ? 'checked' : ''); ?>>
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
        <?php endif; ?>
    </div>
    <?php if(\Auth::user()->type == 'super admin'): ?>
    <div class="col-12">
        <div class="form-group">
            <?php echo e(Form::label('name',__('Name'))); ?>

            <?php echo e(Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))); ?>

        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            <?php echo e(Form::label('email',__('Email'))); ?>

            <?php echo e(Form::email('email',null,array('class'=>'form-control','placeholder'=>__('Enter Email'),'required'=>'required'))); ?>

        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            <?php echo e(Form::label('password',__('Password'))); ?>

            <?php echo e(Form::password('password',array('class'=>'form-control','placeholder'=>__('Enter Password'),'required'=>'required'))); ?>

        </div>
    </div>
    <?php endif; ?>
</div>
    <div class="modal-footer">
        <input type="button" value="<?php echo e(__('Cancel')); ?>" class="btn btn-secondary btn-light" data-bs-dismiss="modal">
        <?php echo e(Form::submit(__('Save'),array('class'=>'btn btn-primary'))); ?>

    </div>


<?php echo e(Form::close()); ?>


<script>
    $('body').on('click', 'input[name="theme_color"]', function() {
        var eleParent = $(this).attr('data-theme');
        $('#themefile1').val(eleParent);
        var imgpath = $(this).attr('data-imgpath');
        $('.' + eleParent + '_img').attr('src', imgpath);
    });

    $('body').ready(function() {

        setTimeout(function(e) {
            var checked = $("input[type=radio][name='theme_color']:checked");
            $('#themefile1').val(checked.attr('data-theme'));
            $('.' + checked.attr('data-theme') + '_img').attr('src', checked.attr('data-imgpath'));
        }, 300);
    });
    $(".color1").click(function() {
        var dataId = $(this).attr("data-id");
        $('#radio_' + dataId).trigger('click');
        var first_check = $('#radio_' + dataId).find('.color-0').trigger("click");
        $( ".theme-card" ).each(function() {
            $(".theme-card").removeClass('selected');
        });
        $('.s_' +dataId ).addClass('selected');
    });
</script>
<?php /**PATH C:\xampp\htdocs\curso\resources\views/admin_store/create.blade.php ENDPATH**/ ?>