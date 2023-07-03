@extends('layouts.app')
@section('main_content')
    <div class="row">
        <div class="col-xs-12 col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <span>Danh sách gói cước</span>
                    <div class="add-package-group">
                        <a class="btn btn-success btn-sm d-none" data-bs-target="#expot_excel_package_modal" data-bs-toggle="modal">
                            <i class="bx bxs-file-export me-1"></i>
                            Xuất
                        </a>
                        <a class="btn btn-success btn-sm" data-bs-target="#import_excel_modal" data-bs-toggle="modal">
                            <i class="bx bx-plus me-1"></i>
                            Thêm mới tệp tin
                        </a>
                        <a data-bs-toggle="modal" data-bs-target="#create_package_modal" data-bs-original-title="Thêm mới 1 gói cước" class="btn btn-primary btn-sm">
                            <i class="bx bx-plus me-1"></i>
                            Thêm mới
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <form action="{{ route('export-packages') }}" method="get" class="d-flex">
                            <div class="col-md-2 col-xs-12 mx-2">
                                <label class="form-label">Tìm kiếm gói cước</label>
                                <input type="text" class="form-control" id="search_value">
                            </div>
                            <div class="col-md-2 me-2">
                                <div class="form-group">
                                    <label class="form-label">Trạng thái</label>
                                    <select name="status_filter" id="status_filter" class="form-select">
                                        <option value="">Trạng thái</option>
                                        <option value="1">Kích hoạt</option>
                                        <option value="2">Vô hiệu hóa</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <a class="btn btn-success btnSearch me-2" data-bs-original-title="Tìm kiếm gói cước" data-bs-toggle="tooltip" data-bs-placement="top">
                                    <i class="bx bx-search"></i>
                                </a>
                                <button type="submit" class="btn btn-secondary btnExport" data-bs-original-title="Xuất dữ liệu " data-bs-toggle="tooltip" data-bs-placement="top">
                                    <i class="bx bx-export"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    <table id="list_packages_table"  class="table table-striped table-bordered dataTable no-footer mt-3">

                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- start modal create package --}}
    <div class="modal fade" id="create_package_modal" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myExtraLargeModalLabel">Thêm mới thông tin gói cước</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{route('create-package')}}" id="create_package_form" method="post">
                        @csrf
                        {{-- first name and last name --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Tên gói cước<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="package_name" name="package_name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Mã gói cước<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="package_code" name="package_code">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Thời gian sử dụng (tháng)<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="time_used" name="time_used">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Khuyến mại (tháng)</label>
                                    <input type="text" class="form-control" id="promotion_time" name="promotion_time">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Trạng thái gói cước<span class="required">*</span></label>
                                    <select name="active_package" id="active_package" class="form-select">
                                        <option value="">--Chọn--</option>
                                        <option value="1">Kích hoạt</option>
                                        <option value="2">Vô hiệu hóa</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 col-xs-12 d-flex justify-content-end">
                                <button class="btn btn-success" type="submit" id="btn_create_single_package">Thêm mới</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.end modal add package -->

    {{-- start modal update package --}}
    <div class="modal fade" id="update_package_modal" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myExtraLargeModalLabel">Cập nhật thông tin gói cước</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{route('update-package')}}" id="update_package_form" method="post">
                        @csrf
                        <input type="hidden" name="package_id" id="package_id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Tên gói cước<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="update_package_name" name="update_package_name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Mã gói cước<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="update_package_code" name="update_package_code">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Thời gian sử dụng (tháng)<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="update_time_used" name="update_time_used">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Khuyến mại (tháng)</label>
                                    <input type="text" class="form-control" id="update_promotion_time" name="update_promotion_time">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Trạng thái gói cước<span class="required">*</span></label>
                                    <select name="update_active_package" id="update_active_package" class="form-select">
                                        <option value="">--Chọn--</option>
                                        <option value="1">Kích hoạt</option>
                                        <option value="2">Vô hiệu hóa</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 col-xs-12 d-flex justify-content-end">
                                <button class="btn btn-success" type="submit" id="btn_update_single_package">Cập nhật</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.end modal update package -->


    {{-- start modal import excel package --}}
    <div class="modal fade" id="import_excel_modal" tabindex="-1" data-bs-backdrop="static" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myExtraLargeModalLabel">Import thông tin gói cước</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{route('import-excel-package')}}" id="import_excel_form" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row" id="import_group">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">
                                        Chọn file
                                        <a href="{{ $template_import ?? null }}" data-bs-toggle="tooltip" data-bs-original-title="Tải file mẫu" data-bs-placement="top" class="ms-2 " name="file_template" id="file_template" download><i class="bx bxs-cloud-download bx-sm"></i></a>
                                    </label>
                                    <input type="file" name="fileExcel" id="fileExcel" id="" class="form-control">
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
    </div><!-- /.end modal import excel package -->

    {{-- modal package_detail --}}
    <div class="modal fade" id="view_package_detail_modal" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myExtraLargeModalLabel">Chi tiết gói cước</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="pkd_package_id" id="pkd_package_id">
                    <div class="row">
                    <form action="{{ route('save-package-detail') }}" id="save_package_detail_form" method="post">
                            @csrf
                            <input type="hidden" name="package_detail_id" id="package_detail_id"/>
                            <div class="row d-flex">
                                <div class="col-md-3 d-flex flex-column">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-label">Ngày bắt đầu<span class="required">*</span></label>
                                            <input type="text" name="start_date" id="start_date" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                            <label class="form-label">Ngày kết thúc<span class="required">*</span></label>
                                            <input type="text" name="end_date" id="end_date" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label">Giá trước thuế (VND)<span class="required">*</span></label>
                                        <input type="text" name="price" id="price" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label">Giá sau thuế (VND)<span class="required">*</span></label>
                                        <input type="text" name="price_vat" id="price_vat" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-3 d-flex flex-column justify-content-between">
                                    <div class="form-group">
                                        <label class="form-label">Số quyết định<span class="required">*</span></label>
                                        <input type="text" name="decision_number" id="decision_number" class="form-control" />
                                    </div>
                                    <div id="action" class="col-md-12 d-flex align-items-start justify-content-end mb-md-1 mt-2">
                                        <div class="action_group">
                                            <button class="btn btn-success btn-sm" id="btnAddPackageDetail">Tạo mới</button>
                                            <button type="submit" class="btn btn-info btn-sm me-1 d-none" id="btnUpdatePackageDetail">Cập nhật</button>
                                            <button type="button" class="btn btn-success btn-sm me-1 d-none" id="btnPackageDetailCancel">Hủy bỏ</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="list_package_detail_group">
                        <table id="list_package_detail_table"  class="table table-striped table-bordered dataTable no-footer mt-3">

                        </table>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.end modal import excel  customer -->

    <input type="hidden" id="_token" value="{{ csrf_token() }}">
@endsection

@section('scripts')
    <script src="{{URL::asset($custom_asset.'/assets/libs/bootstrap_table/bootstrap-table.js')}}"></script>
    <script src="{{URL::asset($custom_asset.'/assets/libs/imask/imask.min.js')}}"></script>
    <script src="{{URL::asset($custom_asset.'/assets/js/pages_js/list_packages.js')}}"></script>
    <script src="{{URL::asset($custom_asset.'/assets/js/pages/password-addon.init.js')}}"></script>
@endsection
@section('styles')
    <link href="{{URL::asset($custom_asset.'/assets/libs/bootstrap_table/bootstrap-table.css')}}" rel="stylesheet" type="text/css" />
@endsection
