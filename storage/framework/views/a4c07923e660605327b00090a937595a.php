<?php
    $settings = Utility::settings();
    $plansetting = App\Models\Utility::plansetting();
?>
<?php echo e(Form::open(['route' => 'zoom-meeting.store', 'id' => 'store-user', 'method' => 'post'])); ?>

<div class="row">
    <?php if($plansetting['enable_chatgpt'] && $plansetting['enable_chatgpt']=='on'): ?>
        <div class="text-end align-items-center justify-content-between">
            <a href="#" class="btn btn-primary btn-sm mx-1" data-size="md" data-ajax-popup-over="true" data-url="<?php echo e(route('generate',['zoom meeting'])); ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(__('Generate')); ?>" data-title="<?php echo e(__('Generate Content With AI')); ?>">
                <i class="fas fa-robot"></i> <?php echo e(__('Generate With AI')); ?>

            </a>
        </div>
    <?php endif; ?>
    <div class="form-group col-md-12">
        <?php echo e(Form::label('title', __('Topic'),['class'=>'form-label'])); ?>

        <?php echo e(Form::text('title', null, ['class' => 'form-control', 'placeholder' => __('Enter Meeting Title'), 'required' => 'required'])); ?>

    </div>
    <div class="form-group col-md-6">
        <?php echo e(Form::label('courses', __('Courses'),['class'=>'form-label'])); ?>

        <?php echo e(Form::select('id', $courses, null, ['class' => 'form-control select2', 'id' => 'course_id'])); ?>

    </div>
    <div class="form-group col-md-6">
        <div>
            <?php echo e(Form::label('students', __('Student'),['class'=>'form-label'])); ?>

            <div id="students-div">
                <?php echo e(Form::select('students[]',[], null, ['class' => 'form-select', 'id' => 'student_id'])); ?>

            </div>
        </div>
    </div>
    <div class="form-group col-md-6">
        <?php echo e(Form::label('datetime', __('Start Date / Time'),['class'=>'form-label'])); ?>

        <?php echo e(Form::text('start_date',null,['class' => 'form-control date', 'placeholder' => __('Select Date/Time'), 'required' => 'required'])); ?>

    </div>
    <div class="form-group col-md-6">
        <?php echo e(Form::label('duration', __('Duration'),['class'=>'form-label'])); ?>

        <?php echo e(Form::number('duration', null, ['class' => 'form-control', 'placeholder' => __('Enter Duration'), 'required' => 'required'])); ?>

    </div>

    <div class="form-group col-md-6">
        <?php echo e(Form::label('password', __('Password ( Optional )'),['class'=>'form-label'])); ?>

        <?php echo e(Form::password('password', ['class' => 'form-control', 'placeholder' => __('Enter Password')])); ?>

    </div>
    <?php if(isset($settings['google_calender_enabled']) && $settings['google_calender_enabled'] == 'on'): ?>
        <div class="form-group col-md-6">
            <?php echo e(Form::label('synchronize_type',__('Synchronize in Google Calendar ?'),array('class'=>'form-label'))); ?>

            <div class=" form-switch">
                <input type="checkbox" class="form-check-input mt-2" name="synchronize_type" id="switch-shadow" value="google_calender">
                <label class="form-check-label" for="switch-shadow"></label>
            </div>
        </div>
    <?php endif; ?>
</div>
<div class="modal-footer">
    <input type="button" value="<?php echo e(__('Cancel')); ?>" class="btn btn-secondary btn-light" data-bs-dismiss="modal">
    <input type="submit" value="<?php echo e(__('Create')); ?>" class="btn btn-primary" id="create-client">
</div>
<?php echo e(Form::close()); ?>


<script type="text/javascript">
    $(document).ready(function() {

        $('.date').daterangepicker({
            "singleDatePicker": true,
            "timePicker": true,
            "locale": {
                "format": 'MM/DD/YYYY H:mm'
            },
            "timePicker24Hour": true,
        }, function(start, end, label) {
            console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format(
                'YYYY-MM-DD') + ' (predefined range: ' + label + ')');
        });
        getStudents($('#course_id').val());
    });
</script>

<?php /**PATH C:\xampp\htdocs\curso\resources\views/zoom_meeting/create.blade.php ENDPATH**/ ?>