@extends('layouts.app')
@section('main_content')
<div class="row">
    <div class="col-lg-4 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-light text-primary rounded-circle shadow fs-3">
                            <i class="ri-money-dollar-circle-fill align-middle"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="text-uppercase fw-semibold fs-12 text-muted mb-1">
                            Khách hàng hiện hữu
                        </p>
                        <h4 class=" mb-0"><span class="">{{ count($existing_customers) }}</span></h4>
                    </div>
                </div>
            </div><!-- end card body -->
        </div><!-- end card -->
    </div><!-- end col -->
    <div class="col-lg-4 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-light text-primary rounded-circle shadow fs-3">
                            <i class="ri-arrow-up-circle-fill align-middle"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="text-uppercase fw-semibold fs-12 text-muted mb-1">
                        Khách hàng tiềm năng
                        </p>
                        <h4 class=" mb-0"><span class="">{{ $potential_customers }}</span></h4>
                    </div>
                </div>
            </div><!-- end card body -->
        </div><!-- end card -->
    </div><!-- end col -->
</div>
    <div class="row">
        <div class="col-xs-12 col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <span>Danh sách khách hàng</span>
                    <div class="add -customer-group">
                        <a class="btn btn-success btn-sm d-none" data-bs-target="#expot_excel_customer_modal" data-bs-toggle="modal">
                            <i class="bx bxs-file-export me-1"></i>
                            Xuất
                        </a>
                        <a class="btn btn-success btn-sm" data-bs-target="#import_excel_modal" data-bs-toggle="modal">
                            <i class="bx bx-plus me-1"></i>
                            Thêm mới tệp tin
                        </a>
                        <a data-bs-toggle="modal" data-bs-target="#create_customer_modal" data-bs-original-title="Thêm mới 1 khách hàng" class="btn btn-primary btn-sm">
                            <i class="bx bx-plus me-1"></i>
                            Thêm mới
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <form action="{{ route('export-customer') }}" method="get" class="d-flex">
                            <div class="col-md-3 ms-2 me-2">
                                <label class="form-label">Loại khách hàng</label>
                                <select name="customer_type_filter" id="customer_type_filter" class="form-select">
                                    <option value="">--Tất cả loại khách hàng--</option>
                                    <option value="1">Cá nhân</option>
                                    <option value="2">Doanh nghiệp</option>
                                </select>
                            </div>
                            <div class="col-md-2 me-2">
                                <div class="form-group">
                                    <label class="form-label">Tìm kiếm khách hàng</label>
                                    <input name="search_filter" id="search_filter" class="form-control" />
                                </div>
                            </div>
                            <div class="me-2 d-flex align-items-end">
                                <a class="btn btn-success btnSearch" data-bs-original-title="Tìm kiếm khách hàng" data-bs-toggle="tooltip" data-bs-placement="top">
                                    <i class="bx bx-search"></i>
                                </a>
                            </div>

                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-secondary btnExport" data-bs-original-title="Xuất dữ liệu khách hàng" data-bs-toggle="tooltip" data-bs-placement="top">
                                    <i class="bx bx-export"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    <table id="list_customers_table"  class="table table-striped table-bordered dataTable no-footer mt-3">

                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- start modal add  customer --}}
    <div class="modal fade" id="create_customer_modal" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myExtraLargeModalLabel">Thêm mới thông tin khách hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{route('create-single-customer')}}" id="create_customer_form" method="post">
                        @csrf
                        {{-- last_name and firstname --}}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Họ và tên<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="firstname" name="firstname">
                                </div>
                            </div>
                        </div>

                        {{-- username and customer_code --}}
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Tài khoản<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="username" name="username">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Mã ID<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="code" name="code" readonly="readonly">
                                </div>
                            </div>
                        </div>

                        {{-- cccd and phone --}}
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">CCCD/CMT<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="cccd" name="cccd">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Số điện thoại<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="phone" name="phone">
                                </div>
                            </div>
                        </div>

                        {{-- email and customer_type --}}
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Email<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="email" name="email">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Loại khách hàng<span class="required">*</span></label>
                                    <select name="customer_type" id="customer_type" class="form-select">
                                        <option value="">-- Chọn --</option>
                                        <option value="1">Cá nhân</option>
                                        <option value="2">Doanh nghiệp</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- cccd_front and cccd_backside --}}
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Ảnh giấy tờ mặt trước<span class="required">*</span></label>
                                    <input type="file" class="form-control" id="cccd_front" accept="image/*" name="cccd_front">
                                    <div class="preview-cccd_front-group d-none mt-2">
                                        <a href="" id="atag-preview_cccd_front" data-fancybox="group">
                                            <img id="preview_cccd_front" src="" alt="Image preview" style="width: 150px;" />
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Ảnh giấy tờ mặt sau<span class="required">*</span></label>
                                    <input type="file" class="form-control" id="cccd_backside" accept="image/*" name="cccd_backside">
                                    <div class="preview-cccd_backside-group d-none mt-2">
                                        <a href="" id="atag-preview_cccd_backside" data-fancybox="group">
                                            <img id="preview_cccd_backside" src="" alt="Image preview" style="width: 150px;" />
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12 col-xs-12 d-flex justify-content-end">
                                <button class="btn btn-success" type="submit" id="btn_create_single_customer">Thêm mới</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.end modal add  customer -->

    {{-- start modal update  customer --}}
    <div class="modal fade" id="update_customer_modal" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myExtraLargeModalLabel">Cập nhật thông tin khách hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{route('update-single-customer')}}" id="update_customer_form" method="post">
                        @csrf
                        <input type="hidden" name="customer_id" id="customer_id">
                        {{-- họ, tên đệm và tên khách hàng --}}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Họ và tên</label>
                                    <input type="text" class="form-control" id="update_firstname" name="update_firstname">
                                </div>
                            </div>
                        </div>

                        {{-- username và Mã ID --}}
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Tài khoản<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="update_username" name="update_username">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Mã ID <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="update_code" name="update_code" readonly="readonly">
                                </div>
                            </div>
                        </div>

                        {{--  cccd and phone --}}
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">CCCD/CMT<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="update_cccd" name="update_cccd">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Số điện thoại<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="update_phone" name="update_phone">
                                </div>
                            </div>
                        </div>

                        {{-- email and customer_type --}}
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Email<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="update_email" name="update_email">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Loại khách hàng <span class="required">*</span></label>
                                    <select name="update_customer_type" id="update_customer_type" class="form-select">
                                        <option value="">-- Chọn --</option>
                                        <option value="1">Cá nhân</option>
                                        <option value="2">Doanh nghiệp</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- cccd_front and cccd_backside --}}
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Ảnh giấy tờ mặt trước</label>
                                    <input type="file" class="form-control" id="update_cccd_front" accept="image/*" name="update_cccd_front">
                                    <div class="update_preview-cccd_front-group d-none mt-2">
                                        <a href="" id="update_atag-preview_cccd_front" data-fancybox="group">
                                            <img id="update_preview_cccd_front" src="" alt="Image preview" style="width: 150px;" />
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Ảnh giấy tờ mặt sau</label>
                                    <input type="file" class="form-control" id="update_cccd_backside" accept="image/*" name="update_cccd_backside">
                                    <div class="update_preview-cccd_backside-group d-none mt-2">
                                        <a href="" id="update_atag-preview_cccd_backside" data-fancybox="group">
                                            <img id="update_preview_cccd_backside" src="" alt="Image preview" style="width: 150px;" />
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12 col-xs-12 d-flex justify-content-end">
                                <button class="btn btn-success" type="submit" id="btn_update_single_customer">Cập nhật</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.end modal update  customer -->


    {{-- start modal import excel  customer --}}
    <div class="modal fade" id="import_excel_modal" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myExtraLargeModalLabel">Thêm mới tệp thông tin khách hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{route('import-excel-customers')}}" id="import_excel_form" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row" id="import_group">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">
                                        Chọn file
                                        <a href="{{ $template_import ?? null }}" data-bs-toggle="tooltip" data-bs-original-title="Tải file mẫu" data-bs-placement="top" class="ms-2 " name="file_template" id="file_template" download><i class="bx bxs-cloud-download bx-sm"></i></a>
                                    </label>
                                    <input type="file" name="fileExcel" id="fileExcel" class="form-control"/>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <div class="form-group">
                                    <button class="btn btn-success" type="submit" id="btn_check_file_excel">Kiểm tra</button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-xs-12 d-none" id="error_array_in_file_excel_group">
                                <table id="list_errors_in_file"  class="table table-striped table-bordered dataTable no-footer mt-3">

                                </table>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 col-xs-12 d-flex justify-content-end">
                                <button class="btn btn-danger d-none" type="button" id="btn_save_excel">Lưu lại</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.end modal import excel  customer -->

    {{-- start modal view image cccd--}}
    <div class="modal fade" id="view_cccd_modal" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myExtraLargeModalLabel">Ảnh giấy tờ của khách hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Ảnh mặt trước</label>
                            <div class="mt-2">
                                <a href="" id="cccd_front_link" data-fancybox="group">
                                    <img id="cccd_front_img" src="" alt="Image preview" style="width: 150px;" />
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>Ảnh mặt sau</label>
                            <div class="mt-2">
                                <a href="" id="cccd_backside_link" data-fancybox="group">
                                    <img id="cccd_backside_img" src="" alt="Image preview" style="width: 150px;" />
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.end modal import excel  customer -->

    {{-- start modal view address --}}
    <div class="modal fade" id="view_address_modal" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myExtraLargeModalLabel">Sổ địa chỉ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="customer_addresses_id" id="customer_addresses_id">
                    <div class="row">
                        <form action="{{ route('save-address') }}" id="save_address_form" method="post">
                            @csrf
                            <input type="hidden" name="address_id" id="address_id"/>
                            <div class="row d-flex">
                                <div class="col-md-5 me-1">
                                    <div class="form-group">
                                        <label class="form-label">Địa chỉ<span class="required">*</span></label>
                                        <input type="text" name="address_text" id="address_text" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-2 me-1">
                                    <div class="form-group">
                                        <label class="form-label">Kinh độ</label>
                                        <input type="text" name="longitude" id="longitude" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-2 me-1">
                                    <div class="form-group">
                                        <label class="form-label">Vĩ độ</label>
                                        <input type="text" name="latitude" id="latitude" class="form-control" />
                                    </div>
                                </div>
                                <div id="action" class="col-md-2 d-flex align-items-end mb-md-1">
                                    <button class="btn btn-success btn-sm" id="btnAddAddress">Tạo mới</button>
                                    <button type="submit" class="btn btn-info btn-sm me-1 d-none" id="btnUpdateAddress">Cập nhật</button>
                                    <button type="button" class="btn btn-success btn-sm me-1 d-none" id="btnAddressCancel">Hủy bỏ</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="list_address_group">
                        <table id="list_addresses_table"  class="table table-striped table-bordered dataTable no-footer mt-3">

                        </table>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.end modal import excel  customer -->

    <input type="hidden" id="_token" value="{{ csrf_token() }}">
@endsection

@section('scripts')
    <script src="{{URL::asset($custom_asset.'/assets/libs/imask/imask.min.js')}}"></script>
    <script src="{{ URL::asset($custom_asset . '/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{URL::asset($custom_asset.'/assets/libs/bootstrap_table/bootstrap-table.js')}}"></script>
    <script src="{{ URL::asset($custom_asset . '/assets/js/accounting.js') }}"></script>
    <script src="{{URL::asset($custom_asset.'/assets/js/pages_js/list_customers.js')}}"></script>
    <script src="{{URL::asset($custom_asset.'/assets/js/pages/password-addon.init.js')}}"></script>
    <script src="{{URL::asset($custom_asset.'/assets/libs/fancybox/script/jquery.fancybox.min.js')}}"></script>
@endsection
@section('styles')
    <link href="{{URL::asset($custom_asset.'/assets/libs/bootstrap_table/bootstrap-table.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{URL::asset($custom_asset.'/assets/libs/fancybox/style/jquery.fancybox.min.css')}}" rel="stylesheet" type="text/css" />
@endsection
