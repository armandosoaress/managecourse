<!-- Page JS -->

<!-- Required Js -->
<script src="<?php echo e(asset('libs/jquery/dist/jquery.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/plugins/popper.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/plugins/simplebar.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/plugins/perfect-scrollbar.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/plugins/bootstrap.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/plugins/feather.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/dash.js')); ?>"></script>

<script src="<?php echo e(asset('assets/js/plugins/dropzone-amd-module.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/plugins/choices.min.js')); ?>"></script>
<script src="<?php echo e(asset('libs/bootstrap-notify/bootstrap-notify.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/plugins/sweetalert2.all.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/plugins/apexcharts.min.js')); ?>"></script>


<script src="<?php echo e(asset('libs/select2/dist/js/select2.min.js')); ?>"></script>


<script src="<?php echo e(asset('assets/js/plugins/bootstrap-switch-button.min.js')); ?>"></script>
<!-- Time picker -->
<script src="<?php echo e(asset('assets/js/plugins/flatpickr.min.js')); ?>"></script>
<!-- datepicker -->
<script src="<?php echo e(asset('assets/js/plugins/datepicker-full.min.js')); ?>"></script>
<script src="<?php echo e(asset('libs/moment/min/moment.min.js')); ?>"></script>

<script src="<?php echo e(asset('assets/js/plugins/simple-datatables.js')); ?>"></script>
    <script>
    if($("#pc-dt-simple").length > 0) {
        const dataTable = new simpleDatatables.DataTable("#pc-dt-simple");
    }
    </script>

<!-- switch button -->
<script src="<?php echo e(asset('assets/js/plugins/bootstrap-switch-button.min.js')); ?>"></script>
<script src="<?php echo e(asset('js/letter.avatar.js')); ?>"></script>

<?php echo $__env->yieldPushContent('script-page'); ?>

<script src="<?php echo e(asset('js/custom.js')); ?>"></script>


<script>
  function show_toastr(title, message, type) {
      var o, i;
      var icon = '';
      var cls = '';
      if (type == 'success') {
          icon = 'fas fa-check-circle';
          // cls = 'success';
          cls = 'primary';
      } else {
          icon = 'fas fa-times-circle';
          cls = 'danger';
      }

    //   console.log(type, cls);
      $.notify({
          icon: icon,
          title: " " + title,
          message: message,
          url: ""
      }, {
          element: "body",
          type: cls,
          allow_dismiss: !0,
          placement: {
              from: 'top',
              align: 'right'
          },
          offset: {
              x: 15,
              y: 15
          },
          spacing: 10,
          z_index: 1080,
          delay: 2500,
          timer: 2000,
          url_target: "_blank",
          mouse_over: !1,
          animate: {
              enter: o,
              exit: i
          },
          // danger
          template: '<div class="toast text-white bg-' + cls +
              ' fade show" role="alert" aria-live="assertive" aria-atomic="true">' +
              '<div class="d-flex">' +
              '<div class="toast-body"> ' + message + ' </div>' +
              '<button type="button" class="btn-close btn-close-white me-2 pt-3 m-auto" data-notify="dismiss" data-bs-dismiss="toast" aria-label="Close"></button>' +
              '</div>' +
              '</div>'
          // template: '<div class="alert alert-{0} alert-icon alert-group alert-notify" data-notify="container" role="alert"><div class="alert-group-prepend alert-content"><span class="alert-group-icon"><i data-notify="icon"></i></span></div><div class="alert-content"><strong data-notify="title">{1}</strong><div data-notify="message">{2}</div></div><button type="button" class="close" data-notify="dismiss" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'
      });
  }
</script>

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

    var themescolors = document.querySelectorAll(".themes-color > a");
    for (var h = 0; h < themescolors.length; h++) {
        var c = themescolors[h];

        c.addEventListener("click", function(event) {
            var targetElement = event.target;
            if (targetElement.tagName == "SPAN") {
                targetElement = targetElement.parentNode;
            }
            var temp = targetElement.getAttribute("data-value");
            removeClassByPrefix(document.querySelector("body"), "theme-");
            document.querySelector("body").classList.add(temp);
        });
    }

    var custthemebg = document.querySelector("#cust-theme-bg");
    custthemebg.addEventListener("click", function() {
        if (custthemebg.checked) {
            document.querySelector(".dash-sidebar").classList.add("transprent-bg");
            document
                .querySelector(".dash-header:not(.dash-mob-header)")
                .classList.add("transprent-bg");
        } else {
            document.querySelector(".dash-sidebar").classList.remove("transprent-bg");
            document
                .querySelector(".dash-header:not(.dash-mob-header)")
                .classList.remove("transprent-bg");
        }
    });

    var custdarklayout = document.querySelector("#cust-darklayout");
    custdarklayout.addEventListener("click", function() {
        if (custdarklayout.checked) {
            document
                .querySelector(".m-header > .b-brand > .logo-lg")
                .setAttribute("src","<?php echo e(asset('/storage/uploads/logo/logo-light.png')); ?>");
            document
                .querySelector("#main-style-link")
                .setAttribute("href","<?php echo e(asset('assets/css/style-dark.css')); ?>");
        } else {
            document
                .querySelector(".m-header > .b-brand > .logo-lg")
                .setAttribute("src", "<?php echo e(asset('/storage/uploads/logo/logo-dark.png')); ?>");
            document
                .querySelector("#main-style-link")
                .setAttribute("href", "<?php echo e(asset('assets/css/style.css')); ?>");
        }
    });

    function removeClassByPrefix(node, prefix) {
        for (let i = 0; i < node.classList.length; i++) {
            let value = node.classList[i];
            if (value.startsWith(prefix)) {
                node.classList.remove(value);
            }
        }
    }
</script>



<?php if(Session::has('success')): ?>
    <script>
        show_toastr('<?php echo e(__('Success')); ?>', '<?php echo session('success'); ?>', 'success');
    </script>
    <?php echo e(Session::forget('success')); ?>

<?php endif; ?>
<?php if(Session::has('error')): ?>
    <script>
        show_toastr('<?php echo e(__('Error')); ?>', '<?php echo session('error'); ?>', 'error');
    </script>
    <?php echo e(Session::forget('error')); ?>

<?php endif; ?>




<?php /**PATH C:\xampp\htdocs\curso\resources\views/partials/admin/footer.blade.php ENDPATH**/ ?>