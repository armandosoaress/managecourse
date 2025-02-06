<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Login')); ?>

<?php $__env->stopSection(); ?>
<?php
    $languages = App\Models\Utility::languages();
    $settings = App\Models\Utility::settings();
    config(
        [
            'captcha.secret' => $settings['google_recaptcha_secret'],
            'captcha.sitekey' => $settings['google_recaptcha_key'],
            'options' => [
                'timeout' => 30,
            ],
        ]
    );
?>
<?php $__env->startSection('language-bar'); ?>
    <div class="lang-dropdown-only-desk">
        <li class="dropdown dash-h-item drp-language">
            <a class="dash-head-link dropdown-toggle btn" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="drp-text"> <?php echo e(ucFirst($languages[$lang])); ?>

                </span>
            </a>
            <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                <?php $__currentLoopData = Utility::languages(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('login',$code)); ?>" tabindex="0" class="dropdown-item <?php echo e($code == $lang ? 'active':''); ?>">
                        <span><?php echo e(ucFirst($language)); ?></span>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </li>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="card-body">
        <div>
            <h2 class="mb-3 f-w-600"><?php echo e(__('Login')); ?></h2>
        </div>
        <div class="custom-login-form">
            <?php echo e(Form::open(array('route'=>'login','method'=>'post','id'=>'loginForm','class'=>'needs-validation','novalidate'=>''))); ?>

                <div class="form-group mb-3">
                    <?php echo e(Form::label('email',__('Email'),array('class' => 'form-label','id'=>'email'))); ?>

                    <?php echo e(Form::text('email',null,array('class'=>'form-control','placeholder'=>__('Enter your email')))); ?>

                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="invalid-email text-danger" role="alert">
                                <strong><?php echo e($message); ?></strong>
                        </span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="form-group mb-3 pss-field">
                    <?php echo e(Form::label('password',__('Password'),array('class' => 'form-label','id'=>'password'))); ?>

                    <?php echo e(Form::password('password',array('class'=>'form-control','placeholder'=>__('Password')))); ?>

                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="invalid-password text-danger" role="alert">
                            <strong><?php echo e($message); ?></strong>
                        </span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="form-group mb-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                        <?php if(Route::has('password.request')): ?>
                            <span>
                                <a href="<?php echo e(route('password.request', $lang)); ?>" tabindex="0"><?php echo e(__('Forgot Your Password?')); ?></a>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if($settings['recaptcha_module'] == 'yes'): ?>
                    <div class="form-group col-lg-12 col-md-12 mt-3">
                        <?php echo NoCaptcha::display($settings['cust_darklayout']=='on' ? ['data-theme' => 'dark'] : []); ?>

                        <?php $__errorArgs = ['g-recaptcha-response'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="small text-danger" role="alert">
                            <strong><?php echo e($message); ?></strong>
                        </span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                <?php endif; ?>
                <div class="d-grid">
                    <?php echo e(Form::submit(__('Login'),array('class'=>'btn btn-primary mt-2','id'=>'saveBtn'))); ?>

                </div>
                <?php if(Utility::getValByName('signup')=='on'): ?>
                    <p class="my-4 text-center"><?php echo e(__("Don't have an account?")); ?>

                        <a href="<?php echo e(route('register',$lang)); ?>" tabindex="0"><?php echo e(__('Register')); ?></a>
                    </p>
                <?php endif; ?>
            <?php echo e(Form::close()); ?>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('custom-scripts'); ?>
    <?php if($settings['recaptcha_module'] == 'yes'): ?>
        <?php echo NoCaptcha::renderJs(); ?>

    <?php endif; ?>

    <script src="<?php echo e(asset('libs/jquery/dist/jquery.min.js')); ?>"></script>
    <script>
        $(document).ready(function () {
            $("#loginForm").submit(function (e) {
                $("#saveBtn").attr("disabled", true);
                return true;
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.auth', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\curso\resources\views/auth/login.blade.php ENDPATH**/ ?>