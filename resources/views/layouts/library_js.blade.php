<!-- JAVASCRIPT -->
    <script src="{{URL::asset($custom_asset.'/assets/libs/jquery/jquery-3.6.1.min.js')}}"></script>
    
    <script src="{{URL::asset($custom_asset.'/assets/libs/jqueryvalidation/jquery.validate.min.js')}}"></script>
    <script src="{{URL::asset($custom_asset.'/assets/libs/jqueryvalidation/jqueryvalidation.js')}}"></script>
    <script src="{{URL::asset($custom_asset.'/assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{URL::asset($custom_asset.'/assets/libs/simplebar/simplebar.min.js')}}"></script>
    <script src="{{URL::asset($custom_asset.'/assets/libs/node-waves/waves.min.js')}}"></script>
    <script src="{{URL::asset($custom_asset.'/assets/libs/feather-icons/feather.min.js')}}"></script>
    <script src="{{URL::asset($custom_asset.'/assets/js/pages/plugins/lord-icon-2.1.0.js')}}"></script>
    {{-- <script src="{{URL::asset($custom_asset.'/assets/js/plugins.js')}}"></script> --}}

    <script type="text/javascript" src="{{URL::asset($custom_asset.'/assets/libs/moment/min/moment.min.js')}}"></script>
    <script type="text/javascript" src="{{URL::asset($custom_asset.'/assets/libs/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js')}}"></script>


    {{-- highcharts --}}
    <script type="text/javascript" src="{{URL::asset($custom_asset.'/assets/libs/hightchart/highcharts.js')}}"></script>
    <script type="text/javascript" src="{{URL::asset($custom_asset.'/assets/libs/hightchart/series-label.js')}}"></script>
    <script type="text/javascript" src="{{URL::asset($custom_asset.'/assets/libs/hightchart/accessibility.js')}}"></script>
    
    <!-- apexcharts -->
    <script src="{{URL::asset($custom_asset.'/assets/libs/apexcharts/apexcharts.min.js')}}"></script>

    <!-- Vector map-->
    <script src="{{URL::asset($custom_asset.'/assets/libs/jsvectormap/js/jsvectormap.min.js')}}"></script>
    <script src="{{URL::asset($custom_asset.'/assets/libs/jsvectormap/maps/world-merc.js')}}"></script>

    <!--Swiper slider js-->
    <script src="{{URL::asset($custom_asset.'/assets/libs/swiper/swiper-bundle.min.js')}}"></script>

    <!-- Dashboard init -->
    <script src="{{URL::asset($custom_asset.'/assets/js/pages/dashboard-ecommerce.init.js')}}"></script>

    <script src="{{URL::asset($custom_asset.'/assets/libs/sweetalert2/sweetalert2.min.js')}}"></script>
    <script src="{{URL::asset($custom_asset.'/assets/libs/toastr/toastr.min.js')}}"></script>
    <script src="{{URL::asset($custom_asset.'/assets/libs/prismjs/prism.js')}}"></script>
    
    @yield('scripts')
    <!-- App js -->
    <script src="{{URL::asset($custom_asset.'/assets/js/app.js')}}"></script>
    
    <!-- password-addon init -->
    <script src="{{URL::asset($custom_asset.'/assets/js/pages/password-addon.init.js')}}"></script>
    <script src="{{URL::asset($custom_asset.'/assets/js/pages_js/layout.js')}}"></script>
    <script>
        $(document).ready(function() {
            $('.offcanvas-backdrop').addClass('d-none');
            var url = window.location; 
            var element = $('.nav-link').filter(function() {
                var t1 = this.href;
                var t2 = url; 
                var t3 = url.href.indexOf(this.href);
                return this.href == url.href; 
            }).addClass('active');
            $('.nav-link.active').parents('.collapse').addClass('show').parents('.collapsed').addClass("active");
            $('.collapse.show').siblings('.collapsed').addClass("active");
        });
    </script>