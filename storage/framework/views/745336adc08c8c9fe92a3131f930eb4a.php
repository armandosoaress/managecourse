<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Store Analytics')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('title'); ?>
    <?php echo e(__('Store Analytics')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><?php echo e(__('Home')); ?></li>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('css-page'); ?>  
<?php $__env->stopPush(); ?>
<?php $__env->startSection('content'); ?>

    <div class="row">
        <!-- [ sample-page ] start -->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5><?php echo e(__('Visitors')); ?></h5>
                </div>
                <div class="card-body">
                    <div id="apex-storedashborad" data-color="primary" data-height="200"></div>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xxl-6">
                    <div class="card min-h-490 overflow-auto">
                        <div class="card-header d-flex justify-content-between">
                            <h5><?php echo e(__('Top URLs')); ?></h5>
                            
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <?php $__currentLoopData = $visitor_url; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td>
                                                    <h6 class="m-0"><?php echo e($url->url); ?></h6>
                                                </td>
                                                <td>
                                                    <h6 class="m-0"><?php echo e($url->total); ?></h6>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card min-h-360 overflow-auto">
                        <div class="card-header d-flex justify-content-between">
                            <h5><?php echo e(__('Devices')); ?></h5>
                        </div>
                        <div class="card-body">
                            <div id="pie-storedashborad"></div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-6">               
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h5><?php echo e(__('Platforms')); ?></h5>
                        </div>
                        <div class="card-body">
                            <div id="user_platform-chart"></div>
                        </div>
                    </div> 
                    <div class="card min-h-360 overflow-auto">
                        <div class="card-header d-flex justify-content-between">
                            <h5><?php echo e(__('Browsers')); ?></h5>
                        </div>
                        <div class="card-body">
                            <div id="pie-storebrowser"></div>
                        </div>
                    </div>                 
                </div>
            </div>
        </div>
    </div>
            
<?php $__env->stopSection(); ?>

<?php $__env->startPush('script-page'); ?>
    <script>
        PurposeStyle = function () {
    var e = getComputedStyle(document.body);
    return {
        colors: {
            gray: {100: "#f6f9fc", 200: "#e9ecef", 300: "#dee2e6", 400: "#ced4da", 500: "#adb5bd", 600: "#8898aa", 700: "#525f7f", 800: "#32325d", 900: "#212529"},
            theme: {
                primary: e.getPropertyValue("--primary") ? e.getPropertyValue("--primary").replace(" ", "") : "#6e00ff",
                info: e.getPropertyValue("--info") ? e.getPropertyValue("--info").replace(" ", "") : "#00B8D9",
                success: e.getPropertyValue("--success") ? e.getPropertyValue("--success").replace(" ", "") : "#36B37E",
                danger: e.getPropertyValue("--danger") ? e.getPropertyValue("--danger").replace(" ", "") : "#FF5630",
                warning: e.getPropertyValue("--warning") ? e.getPropertyValue("--warning").replace(" ", "") : "#FFAB00",
                dark: e.getPropertyValue("--dark") ? e.getPropertyValue("--dark").replace(" ", "") : "#212529"
            },
            transparent: "transparent"
        }, fonts: {base: "Nunito"}
    }
}

var PurposeStyle = PurposeStyle();

        (function() {
            var options = {
                chart: {
                    height: 250,
                    type: 'area',
                    toolbar: {
                        show: false,
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: 2,
                    curve: 'smooth'
                },


                series: [{
                    name: "<?php echo e(__('Totle Page View')); ?>",
                    data: <?php echo json_encode($chartData['data']); ?>

                }, {
                    name: "<?php echo e(__('Unique Page View')); ?>",
                    data: <?php echo json_encode($chartData['unique_data']); ?>

                    
                }],

                xaxis: {
                    categories: <?php echo json_encode($chartData['label']); ?>,
                    
                    title: {
                        text: 'Days'
                    }
                },
                colors: ['#e83e8c','#ffa21d'],

                grid: {
                    strokeDashArray: 4,
                },
                legend: {
                    show: false,
                },
                // markers: {
                //     size: 4,
                //     colors: ['#FFA21D'],
                //     opacity: 0.9,
                //     strokeWidth: 2,
                //     hover: {
                //         size: 7,
                //     }
                // },
                yaxis: {
                    tickAmount: 3,
                }
            };
            var chart = new ApexCharts(document.querySelector("#apex-storedashborad"), options);
            chart.render();
        })();

        var options = {
            series: <?php echo json_encode($devicearray['data']); ?>,
            
            chart: {
                width: 350,
                type: 'pie',
            },
            colors: ["#685ee5", "#3ec9d6", "#ffa21d", "#e83e8c"],
            labels: <?php echo json_encode($devicearray['label']); ?>,
            
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom',
                    }
                }
            }]
        };
        var chart = new ApexCharts(document.querySelector("#pie-storedashborad"), options);
        chart.render();


        var options = {
            series: <?php echo json_encode($browserarray['data']); ?>,
            chart: {
                width: 350,
                type: 'pie',
            },
            colors: ["#685ee5", "#3ec9d6", "#ffa21d", "#e83e8c"],
            labels: <?php echo json_encode($browserarray['label']); ?>,
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom',
                    }
                }
            }]
        };
        var chart = new ApexCharts(document.querySelector("#pie-storebrowser"), options);
        chart.render();
    </script>
    <script>
        var WorkedHoursChart = (function() {
            var $chart = $('#user_platform-chart');
            function init($this) {
                var options = {
                    chart: {
                        width: '100%',
                        type: 'bar',
                        zoom: {
                            enabled: false
                        },
                        toolbar: {
                            show: false
                        },
                        shadow: {
                            enabled: false,
                        },

                    },

                    plotOptions: {
                        bar: {
                            horizontal: false,
                            distributed: true,
                            columnWidth: '25%',
                            borderRadius: 12,
                            endingShape: 'rounded'
                        },
                    },
                    colors: ["#685ee5", "#3ec9d6", "#ffa21d", "#e83e8c"],
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    },
                    series: [{
                        name: 'Platform',
                       
                        data: <?php echo json_encode($platformarray['data']); ?>,
                        
                        
                    }],
                    xaxis: {
                        labels: {
                            // format: 'MMM',
                            style: {
                                colors: PurposeStyle.colors.gray[600],
                                fontSize: '14px',
                                fontFamily: PurposeStyle.fonts.base,
                                cssClass: 'apexcharts-xaxis-label',
                            },
                        },
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: true,
                            borderType: 'solid',
                            color: PurposeStyle.colors.gray[300],
                            height: 6,
                            offsetX: 0,
                            offsetY: 0
                        },
                        title: {
                            text: '<?php echo e(__('Platform')); ?>'
                        },                        
                        categories: <?php echo json_encode($platformarray['label']); ?>,

                    },
                    yaxis: {
                        labels: {
                            style: {
                                color: PurposeStyle.colors.gray[600],
                                fontSize: '12px',
                                fontFamily: PurposeStyle.fonts.base,
                            },
                        },
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: true,
                            borderType: 'solid',
                            color: PurposeStyle.colors.gray[300],
                            height: 6,
                            offsetX: 0,
                            offsetY: 0
                        }
                    },
                    fill: {
                        type: 'solid',
                        opacity: 1

                    },
                    // markers: {
                    //     size: 4,
                    //     opacity: 0.7,
                    //     strokeColor: "#fff",
                    //     strokeWidth: 3,
                    //     hover: {
                    //         size: 7,
                    //     }
                    // },
                    grid: {
                        borderColor: PurposeStyle.colors.gray[300],
                        strokeDashArray: 5,
                    },
                    dataLabels: {
                        enabled: false
                    }
                }
                // Get data from data attributes
                var dataset = $this.data().dataset,
                    labels = $this.data().labels,
                    color = $this.data().color,
                    height = $this.data().height,
                    type = $this.data().type;

                // Inject synamic properties
                // options.colors = [
                //     PurposeStyle.colors.theme[color]
                // ];
                // options.markers.colors = [
                //     PurposeStyle.colors.theme[color]
                // ];
                options.chart.height = height ? height : 350;
                // Init chart
                var chart = new ApexCharts($this[0], options);
                // Draw chart
                setTimeout(function() {
                    chart.render();
                }, 300);
            }

            // Events
            if ($chart.length) {
                $chart.each(function() {
                    init($(this));
                });
            }
        })();
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\curso\resources\views/store-analytic.blade.php ENDPATH**/ ?>