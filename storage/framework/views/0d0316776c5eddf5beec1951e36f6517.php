<?php
    $logo=\App\Models\Utility::get_file('uploads/logo/');
    $logo_img=\App\Models\Utility::getValByName('company_logo');
    $logo_light = \App\Models\Utility::getValByName('company_light_logo');
    $s_logo=\App\Models\Utility::get_file('uploads/store_logo/');
    // $dark_logo = Utility::getValByName('company_logo_dark');
    // $light_logo = Utility::getValByName('company_logo_light');
    $company_logo = \App\Models\Utility::GetLogo();
    $company_favicon = Utility::getValByName('company_favicon');
    $store_logo = asset(Storage::url('uploads/store_logo/'));
    $lang = Utility::getValByName('default_language');
    if (Auth::user()->type != 'super admin') {
        $store_lang = $store->lang;
    }
?>

<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Store Theme Settings')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('title'); ?>

    <?php echo e(__('Store Theme Settings')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Home')); ?></a></li>
    <li class="breadcrumb-item"><a href="<?php echo e(route('theme.index')); ?>"><?php echo e(__('themes')); ?></a></li>
    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Store Theme Settings')); ?></li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('action-btn'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('css-page'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('libs/summernote/summernote-bs4.css')); ?>">
    <style>
        hr {
            margin: 8px;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xl-3">
                    <div class="card sticky-top" style="top:30px">
                        <div class="list-group list-group-flush" id="useradd-sidenav">
                            <?php if(Auth::user()->type != 'super admin'): ?>
                                <?php if($theme == 'theme1' || $theme == 'theme2' || $theme == 'theme3' || $theme == 'theme4'): ?>
                                    <a href="#Header_Setting" id="Header_Setting_tab"
                                        class="list-group-item list-group-item-action border-0"><?php echo e(__('Home Header')); ?>

                                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                    </a>
                                    <a href="#Features_Setting" id="Features_Setting_tab"
                                        class="list-group-item list-group-item-action border-0"><?php echo e(__('Home Featured Course')); ?>

                                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                    </a>
                                    <a href="#Categories" id="Categories_tab"
                                        class="list-group-item list-group-item-action border-0"><?php echo e(__('Home Categories')); ?>

                                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                    </a>
                                    <a href="#Sale_Setting" id="On_Sale_tab"
                                        class="list-group-item list-group-item-action border-0"><?php echo e(__('Home On Sale')); ?>

                                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                    </a>
                                    <a href="#Email_Subscriber_Setting" id="Email_Subscriber_Setting_tab"
                                        class="list-group-item list-group-item-action border-0"><?php echo e(__('Home Email Subscriber')); ?>

                                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                    </a>
                                    <a href="#Footer_1" id="Footer_1_tab"
                                        class="list-group-item list-group-item-action border-0"><?php echo e(__('Home Footer 1')); ?>

                                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                    </a>
                                    <a href="#Footer_2" id="Footer_2_tab"
                                        class="list-group-item list-group-item-action border-0"><?php echo e(__('Home Footer 2')); ?>

                                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                    </a>
                                    <a href="#Footer_3" id="Footer_3_tab"
                                        class="list-group-item list-group-item-action border-0"><?php echo e(__('Home Footer 3')); ?>

                                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                    </a>
                                <?php endif; ?>
                                <?php if($theme == 'theme2'): ?>
                                    <a href="#Brand_Logo" id="Brand_Logo_tab"
                                        class="list-group-item list-group-item-action border-0"><?php echo e(__('Home Brand Logo')); ?>

                                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php if(Auth::user()->type != 'super admin'): ?>

                    <div class="col-xl-9">
                        <div class="col-lg-12 col-sm-12 col-md-12">
                            <div class="row">
                                <?php echo e(Form::open(['route' => ['store.storeeditproducts', [$store->slug, $theme]], 'method' => 'post', 'enctype' => 'multipart/form-data'])); ?>

                                    <?php $__currentLoopData = $getStoreThemeSetting; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $json_key => $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $id = '';
                                            if ($section['section_name'] == 'Home-Header') {
                                                $id = 'Header_Setting';
                                            }
                                            if ($section['section_name'] == 'Home-Featured Course') {
                                                $id = 'Features_Setting';
                                            }
                                            if ($section['section_name'] == 'Home-Categories') {
                                                $id = 'Categories';
                                            }
                                            if ($section['section_name'] == 'Home-On Sale') {
                                                $id = 'Sale_Setting';
                                            }
                                            if ($section['section_name'] == 'Home-Email-subscriber') {
                                                $id = 'Email_Subscriber_Setting';
                                            }
                                            if ($section['section_name'] == 'Home-Footer') {
                                                $id = 'Footer_1';
                                            }
                                            if ($section['section_name'] == 'Home-Footer') {
                                                $id = 'Footer_2';
                                            }
                                            if ($section['section_name'] == 'Home-Footer') {
                                                $id = 'Footer_3';
                                            }
                                            if ($section['section_name'] == 'Home-Brand-Logo') {
                                                $id = 'Brand_Logo';
                                            }

                                        ?>
                                        <input type="hidden" name="array[<?php echo e($json_key); ?>][section_name]" value="<?php echo e($section['section_name']); ?>">
                                        <input type="hidden" name="array[<?php echo e($json_key); ?>][section_slug]" value="<?php echo e($section['section_slug']); ?>">
                                        <input type="hidden" name="array[<?php echo e($json_key); ?>][array_type]" value="<?php echo e($section['array_type']); ?>">
                                        <input type="hidden" name="array[<?php echo e($json_key); ?>][loop_number]" value="<?php echo e($section['loop_number']); ?>">
                                        <?php
                                            $loop = 1;
                                            $section = (array) $section;
                                        ?>
                                        <?php if($json_key == 0 || $json_key-1 > -1 && $getStoreThemeSetting[$json_key-1]['section_slug'] != $section['section_slug']): ?>
                                            <div class="card " id="<?php echo e($id); ?>" >
                                                <div class="card-header d-flex justify-content-between">
                                                    <div> <h5> <?php echo e($section['section_name']); ?> </h5> </div>
                                                    <div class="text-end">
                                                        <div class="form-check form-switch form-switch-right">
                                                            <input type="hidden" name="array[<?php echo e($json_key); ?>][section_enable]<?php echo e($section['section_enable']); ?>"
                                                                value="off">
                                                            <input type="checkbox" class="form-check-input mx-2 off switch" data-toggle="switchbutton"
                                                                name="array[<?php echo e($json_key); ?>][section_enable]<?php echo e($section['section_enable']); ?>"
                                                                id="array[<?php echo e($json_key); ?>]<?php echo e($section['section_slug']); ?>"
                                                                <?php echo e($section['section_enable'] == 'on' ? 'checked' : ''); ?>>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php endif; ?>

                                                <div class="card-body">
                                                    <?php

                                                        $loops = 1;
                                                    ?>
                                                    <?php if($section['array_type'] == 'multi-inner-list'): ?>
                                                        <?php
                                                            $loops = $section['loop_number'];
                                                        ?>
                                                    <?php endif; ?>
                                                    <?php for($i = 0; $i < $loops; $i++): ?>

                                                        <div class="row">
                                                            <?php
                                                                foreach ($section['inner-list'] as $inner_list_key => $field) {
                                                                $field = (array)$field;
                                                            ?>

                                                            <?php
                                                            $text_class = '';
                                                            if (in_array($field['field_slug'],['homepage-email-subscriber-title', 'homepage-email-subscriber-sub-text','homepage-email-subscriber-thumbnail']))
                                                            {
                                                                $text_class = 'subscriber_box';
                                                            }
                                                            ?>
                                                            <input type="hidden" name="array[<?php echo e($json_key); ?>][inner-list][<?php echo e($inner_list_key); ?>][field_name]" value="<?php echo e($field['field_name']); ?>">
                                                            <input type="hidden" name="array[<?php echo e($json_key); ?>][inner-list][<?php echo e($inner_list_key); ?>][field_slug]" value="<?php echo e($field['field_slug']); ?>">
                                                            <input type="hidden" name="array[<?php echo e($json_key); ?>][inner-list][<?php echo e($inner_list_key); ?>][field_help_text]" value="<?php echo e($field['field_help_text']); ?>">
                                                            <input type="hidden" name="array[<?php echo e($json_key); ?>][inner-list][<?php echo e($inner_list_key); ?>][field_default_text]" value="<?php echo e($field['field_default_text']); ?>">
                                                            <input type="hidden" name="array[<?php echo e($json_key); ?>][inner-list][<?php echo e($inner_list_key); ?>][field_type]" value="<?php echo e($field['field_type']); ?>">

                                                            <?php if($field['field_type'] == 'text'): ?>
                                                                <div class="col-sm-6  <?php echo e($text_class); ?>">
                                                                    <div class="form-group">
                                                                        <label
                                                                            class="form-label"><?php echo e($field['field_name']); ?></label>
                                                                        <?php
                                                                            $checked1 = $field['field_default_text'];
                                                                            if (!empty($section[$field['field_slug']][$i])) {
                                                                                $checked1 = $section[$field['field_slug']][$i];
                                                                            }
                                                                        ?>
                                                                        <?php if($section['array_type'] == 'multi-inner-list'): ?>
                                                                            <input type="text"
                                                                                name="array[<?php echo e($json_key); ?>][<?php echo e($field['field_slug']); ?>][<?php echo e($i); ?>]"
                                                                                class="form-control" value="<?php echo e($checked1); ?>"
                                                                                placeholder="<?php echo e($field['field_help_text']); ?>">
                                                                        <?php else: ?>
                                                                            <input type="text"
                                                                                name="array[<?php echo e($json_key); ?>][inner-list][<?php echo e($inner_list_key); ?>][field_default_text]" class="form-control"
                                                                                value="<?php echo e($field['field_default_text']); ?>"
                                                                                placeholder="<?php echo e($field['field_help_text']); ?>" id="">
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>

                                                            <?php if($field['field_type'] == 'text area'): ?>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label
                                                                            class="form-label"><?php echo e($field['field_name']); ?></label>
                                                                        <?php
                                                                            $checked1 = $field['field_default_text'];
                                                                            if (!empty($section[$field['field_slug']][$i])) {
                                                                                $checked1 = $section[$field['field_slug']][$i];
                                                                            }
                                                                        ?>
                                                                        <?php if($section['array_type'] == 'multi-inner-list'): ?>
                                                                            <textarea class="form-control" name="array[<?php echo e($json_key); ?>][<?php echo e($field['field_slug']); ?>][<?php echo e($i); ?>]"
                                                                                rows="3" placeholder="<?php echo e($field['field_help_text']); ?>"><?php echo e($checked1); ?></textarea>
                                                                        <?php else: ?>
                                                                            <textarea class="form-control" name="array[<?php echo e($json_key); ?>][inner-list][<?php echo e($inner_list_key); ?>][field_default_text]" 
                                                                                rows="3" placeholder="<?php echo e($field['field_help_text']); ?>"><?php echo e($field['field_default_text']); ?></textarea>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>

                                                            <?php if($field['field_type'] == 'photo upload'): ?>
                                                                <div class="col-sm-6 <?php echo e($text_class); ?>">
                                                                    <?php if($section['array_type'] == 'multi-inner-list'): ?>
                                                                        <?php
                                                                            $checked2 = $field['field_default_text'];
                                                                            if (!empty($section[$field['field_slug']])) {
                                                                                $checked2 = $section[$field['field_slug']][$i];
                                                                                if (is_array($checked2)) {
                                                                                    $checked2 = $checked2['field_prev_text'];
                                                                                }
                                                                            }
                                                                        ?>
                                                                        <div class="form-group">
                                                                            <label class="form-label"><?php echo e($field['field_name']); ?></label>
                                                                            <input type="hidden" name="array[<?php echo e($json_key); ?>][<?php echo e($field['field_slug']); ?>][<?php echo e($i); ?>][field_prev_text]"
                                                                                value="<?php echo e($checked2); ?>">
                                                                            <input type="file" name="array[<?php echo e($json_key); ?>][<?php echo e($field['field_slug']); ?>][<?php echo e($i); ?>][image]"
                                                                                class="form-control" placeholder="<?php echo e($field['field_help_text']); ?>">
                                                                        </div>
                                                                        <?php if(isset($checked2) && !is_array($checked2)): ?>
                                                                            <img src="<?php echo e(asset(Storage::url('uploads/' . $checked2))); ?>" style="width: auto; max-height: 80px;" class="cate-img-shadow">
                                                                        <?php else: ?>
                                                                            <img src="<?php echo e(asset(Storage::url('uploads/' . $field['field_default_text']))); ?>" style="width: auto; max-height: 80px;" class="cate-img-shadow">
                                                                        <?php endif; ?>
                                                                    <?php else: ?>
                                                                        <div class="form-group">
                                                                            <label class="form-label"><?php echo e($field['field_name']); ?></label>
                                                                            <input type="hidden" name="array[<?php echo e($json_key); ?>][inner-list][<?php echo e($inner_list_key); ?>][field_prev_text]" value="<?php echo e($field['field_default_text']); ?>">
                                                                            <input type="file" name="array[<?php echo e($json_key); ?>][inner-list][<?php echo e($inner_list_key); ?>][field_default_text]" class="form-control"
                                                                                placeholder="<?php echo e($field['field_help_text']); ?>">
                                                                        </div>

                                                                        <img src="<?php echo e(asset(Storage::url('uploads/' . $field['field_default_text']))); ?>" class="cate-img-shadow"
                                                                            <?php if($field['field_slug'] == 'homepage-footer-logo'): ?> style="width: auto; height: 80px;" <?php else: ?> style="width: 200px; height: 200px;" <?php endif; ?>>
                                                                    <?php endif; ?>
                                                                </div>
                                                            <?php endif; ?>

                                                            <?php if($field['field_type'] == 'button'): ?>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label
                                                                            class="form-label"><?php echo e($field['field_name']); ?></label>
                                                                        <input type="text"
                                                                            name="array[<?php echo e($json_key); ?>][inner-list][<?php echo e($inner_list_key); ?>][field_default_text]"
                                                                            class="form-control"
                                                                            value="<?php echo e($field['field_default_text']); ?>"
                                                                            placeholder="<?php echo e($field['field_help_text']); ?>">
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>
                                                            <?php
                                                                $checked = '';
                                                                if ($field['field_slug'] == 'homepage-email-subscriber-display') {
                                                                    $checked = $field['field_default_text'] == 'on' ? 'checked' : '';
                                                                }
                                                                if ($field['field_slug'] == 'homepage-email-subscriber-display') {
                                                                    // echo $field['field_default_text'];
                                                                    $checked = $field['field_default_text'] == 'on' ? 'checked' : '';
                                                                }
                                                            ?>

                                                            <?php if($field['field_type'] == 'checkbox'): ?>
                                                                <div class="col-sm-6">
                                                                    <label class="form-label"><?php echo e($field['field_name']); ?></label>
                                                                    <div class="form-check form-switch form-switch-right mb-2">
                                                                        <?php if($section['array_type'] == 'multi-inner-list'): ?>
                                                                            <?php
                                                                                $checked1 = '';
                                                                                if (!empty($section[$field['field_slug']][$i]) && $section[$field['field_slug']][$i] == 'on') {
                                                                                    $checked1 = 'checked';
                                                                                }
                                                                            ?>
                                                                            <input type="hidden" name="array[<?php echo e($json_key); ?>][<?php echo e($field['field_slug']); ?>][<?php echo e($i); ?>]" value="off">
                                                                            <input type="checkbox" class="form-check-input mx-2"
                                                                                name="array[<?php echo e($json_key); ?>][<?php echo e($field['field_slug']); ?>][<?php echo e($i); ?>]"
                                                                                id="array[<?php echo e($section['section_slug']); ?>][<?php echo e($field['field_slug']); ?>]"
                                                                                <?php echo e($checked1); ?>>
                                                                        <?php else: ?>

                                                                            <input type="hidden" name="array[<?php echo e($json_key); ?>][inner-list][<?php echo e($inner_list_key); ?>][field_default_text]" value="off">
                                                                            <input type="checkbox" class="form-check-input mx-2 enable_subscriber"

                                                                                name="array[<?php echo e($json_key); ?>][inner-list][<?php echo e($inner_list_key); ?>][field_default_text]"
                                                                                id=" array[<?php echo e($section['section_slug']); ?>][<?php echo e($field['field_slug']); ?>] "
                                                                                <?php echo e($checked); ?>>
                                                                        <?php endif; ?>

                                                                        <label class="form-check-label" for="array[ <?php echo e($section['section_slug']); ?>][<?php echo e($field['field_slug']); ?>]"></label>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>

                                                            <?php if($field['field_type'] == 'multi file upload'): ?>
                                                                <div class="form-group">
                                                                    <label class="form-label"><?php echo e($field['field_name']); ?></label>

                                                                    

                                                                    <input type="file" name="array[<?php echo e($json_key); ?>][inner-list][<?php echo e($inner_list_key); ?>][multi_image][]"
                                                                     class="form-control custom-input-file" multiple>
                                                                </div>
                                                                <div id="img-count" class="badge badge-success rounded-pill"></div>
                                                                <div class="col-12">
                                                                    <div class="card-wrapper p-3 lead-common-box">
                                                                        <?php if(!empty($field['image_path'])): ?>
                                                                        <?php $__currentLoopData = $field['image_path']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $file_pathh): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                        <div class="card mb-3 border shadow-none product_Image" data-value="<?php echo e($file_pathh); ?>">
                                                                            <div class="px-3 py-3">
                                                                                <div class="row align-items-center">
                                                                                    <div class="col ml-n2">
                                                                                        <p class="card-text small text-muted">
                                                                                                    <input type="hidden" name='array[<?php echo e($json_key); ?>][prev_image][]' value="<?php echo e($file_pathh); ?>">
                                                                                                    <img class="rounded" src=" <?php echo e(asset(Storage::url('uploads/'.$file_pathh))); ?>" width="70px" alt="Image placeholder" data-dz-thumbnail>
                                                                                                </p>
                                                                                            </div>
                                                                                            <div class="col-auto actions">
                                                                                                <a class="action-item" href=" <?php echo e(asset(Storage::url('uploads/'.$file_pathh))); ?>" download="" data-toggle="tooltip" data-original-title="<?php echo e(__('Download')); ?>">
                                                                                                    <i class="fas fa-download"></i>
                                                                                                </a>
                                                                                            </div>
                                                                                            <div class="col-auto actions">
                                                                                                <a name="deleteRecord" class="action-item deleteRecord"  data-name="<?php echo e('uploads/'.$file_pathh); ?>">
                                                                                                    <i class="fas fa-trash"></i>
                                                                                                </a>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>
                                                            <?php } ?>
                                                        </div>
                                                    <?php endfor; ?>

                                                </div>
                                                <?php if($json_key+1 <= count($getStoreThemeSetting)-1): ?>
                                                    <?php if($getStoreThemeSetting[$json_key+1]['section_slug'] != $section['section_slug']): ?>
                                            </div>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <div class="row">
                                        <div class="col-lg-12 col-sm-12 col-md-12">
                                            <div class="card-footer">
                                                <div class="col-sm-12">
                                                    <div class="text-end">
                                                        <?php echo e(Form::submit(__('Save Changes'), ['class' => 'btn btn-xs btn-primary py-3 px-3'])); ?>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php echo e(Form::close()); ?>

                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
            <!-- [ sample-page ] end -->
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('script-page'); ?>

    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.repeater/1.2.1/jquery.repeater.min.js"></script>

    <script>
         var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300
        })
    </script>
    <script>
        $(document).ready(function() {
            $('.repeater').repeater({
                initEmpty: false,
                show: function() {
                    $(this).slideDown();
                },
                hide: function(deleteElement) {
                    if (confirm('Are you sure you want to delete this element?')) {
                        $(this).slideUp(deleteElement);
                    }
                },
                isFirstItemUndeletable: true
            })
        });

        // $(".deleteRecord").click(function() {
        //     var name = $(this).data("name");
        //     var token = $("meta[name='csrf-token']").attr("content");
        //     $.ajax({
        //         url: '<?php echo e(route('brand.file.delete', [$store->slug, $theme, '_name'])); ?>'.replace('_name',
        //             name),
        //         type: 'DELETE',
        //         data: {
        //             "name": name,
        //             "_token": token,
        //         },
        //         success: function(response) {
        //             show_toastr('Success', response.success, 'success');
        //             $('.product_Image[data-value="' + response.name + '"]').remove();
        //         },
        //         error: function(response) {
        //             show_toastr('Error', response.error, 'error');
        //         }
        //     });
        // });
        $(".deleteRecord").click(function(e) {
            e.preventDefault();
            var id = $(this).data("name");
            var data = {
                'image': id,
                '_token': $("meta[name='csrf-token']").attr("content")
            };

           $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "<?php echo e(route('product.image.delete')); ?>",
                    data: data,
                    context: this,
                    success: function(data){
                        $(this).closest('.product_Image').remove();
                        $('.submit_all').click();
                    }
                });
        });
    </script>
    <script src="<?php echo e(asset('libs/summernote/summernote-bs4.js')); ?>"></script>
    <script>
        var Dropzones = function() {
            var e = $('[data-toggle="dropzone1"]'),
                t = $(".dz-preview");
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            e.length && (Dropzone.autoDiscover = !1, e.each(function() {
                var e, a, n, o, i;
                e = $(this), a = void 0 !== e.data("dropzone-multiple"), n = e.find(t), o = void 0, i = {
                    url: "<?php echo e(route('store.storeeditproducts', [$store->slug, $theme])); ?>",
                    headers: {
                        'x-csrf-token': CSRF_TOKEN,
                    },
                    thumbnailWidth: null,
                    thumbnailHeight: null,
                    previewsContainer: n.get(0),
                    previewTemplate: n.html(),
                    maxFiles: 10,
                    parallelUploads: 10,
                    autoProcessQueue: true,
                    uploadMultiple: true,
                    acceptedFiles: a ? null : "image/*",
                    success: function(file, response) {
                        if (response.status == "success") {
                            show_toastr('success', response.success, 'success');
                        } else {
                            show_toastr('Error', response.msg, 'error');
                        }
                    },
                    error: function(file, response) {
                        // Dropzones.removeFile(file);
                        if (response.error) {
                            show_toastr('Error', response.error, 'error');
                        } else {
                            show_toastr('Error', response, 'error');
                        }
                    },
                    init: function() {
                        var myDropzone = this;
                    }

                }, n.html(""), e.dropzone(i)
            }))
        }()

        $("#eventBtn").click(function() {
            $("#BigButton").clone(true).appendTo("#fileUploadsContainer").find("input").val("").end();
        });
        $("#testimonial_eventBtn").click(function() {
            $("#BigButton2").clone(true).appendTo("#fileUploadsContainer2").find("input").val("").end();
        });

        $(document).on('click', '#remove', function() {
            var qq = $('.BigButton').length;

            if (qq > 1) {
                var dd = $(this).attr('data-id');

                $(this).parents('#BigButton').remove();
            }
        });

        $("input[type='file']").on("change", function() {
            var numFiles = $(this).get(0).files.length
            $('#img-count').html(numFiles + ' Images selected');
        })
    </script>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\curso\resources\views/settings/edit_theme.blade.php ENDPATH**/ ?>