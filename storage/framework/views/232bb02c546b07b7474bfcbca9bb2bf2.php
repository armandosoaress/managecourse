<div class="dash-container">
    <div class="dash-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-3">
                        <div class="page-header-title">
                            <h4 class="m-b-10"><?php echo $__env->yieldContent('title'); ?></h4>
                        </div>
                        <ul class="breadcrumb">                           
                            <?php echo $__env->yieldContent('breadcrumb'); ?>
                        </ul>
                    </div>

                    <div class="col-md-6">
                        <div class="col-12">
                            <?php echo $__env->yieldContent('filter'); ?>
                        </div>
                        <div class="col-12 text-end">
                            <?php echo $__env->yieldContent('action-btn'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->
        <!-- [ Main Content ] start -->
        <?php echo $__env->yieldContent('content'); ?>

    </div>
</div>
<?php /**PATH C:\xampp\htdocs\curso\resources\views/partials/admin/content.blade.php ENDPATH**/ ?>