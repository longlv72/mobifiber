<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-layout-mode="light" data-layout-width="fluid" data-layout-position="fixed" data-layout-style="default"><head>

    <meta charset="utf-8">
    <title>Mobifone - Manager System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin &amp; Dashboard Template" name="description">
    <meta content="Themesbrand" name="author">
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- Layout config Js -->
    <script src="assets/js/layout.js"></script>
    <!-- Bootstrap Css -->
    <link href="{{URL::asset($custom_asset.'/assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css">
    <!-- Icons Css -->
    <link href="{{URL::asset($custom_asset.'/assets/css/icons.min.css')}}" rel="stylesheet" type="text/css">
    <!-- App Css-->
    <link href="{{URL::asset($custom_asset.'/assets/css/app.min.css')}}" rel="stylesheet" type="text/css">
    <!-- custom Css-->
    <link href="{{URL::asset($custom_asset.'/assets/css/custom.min.css')}}" rel="stylesheet" type="text/css">

</head>

<body cz-shortcut-listen="true">

    <!-- auth-page wrapper -->
    <div class="auth-page-wrapper py-5 d-flex justify-content-center align-items-center min-vh-100">

        <!-- auth-page content -->
        <div class="auth-page-content overflow-hidden p-0">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-xl-4 text-center">
                        <div class="error-500 position-relative">
                            <img src="assets/images/error500.png" alt="" class="img-fluid error-500-img error-img">
                            <h1 class="title text-muted">403</h1>
                        </div>
                        <div>
                            <h4>Từ chối truy cập!</h4>
                            <p class="text-muted w-75 mx-auto">Bạn không có quyền truy cập tính năng này.</p>
                            <a href="/" class="btn btn-success"><i class="mdi mdi-home me-1"></i>Quay lại trang chủ</a>
                        </div>
                    </div><!-- end col-->
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end auth-page content -->
    </div>
    <!-- end auth-page-wrapper -->



</body></html>
