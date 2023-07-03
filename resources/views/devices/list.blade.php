@extends('layouts.app')
@section('main_content')
    <div class="row">
        <div class="col-xs-12 col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <span>Danh sách thiết bị</span>
                    <div class="add-device-group">
                        <a class="btn btn-success btn-sm d-none" data-bs-target="#expot_excel_device_modal" data-bs-toggle="modal">
                            <i class="bx bxs-file-export me-1"></i>
                            Xuất
                        </a>
                        <a class="btn btn-success btn-sm" data-bs-target="#import_excel_modal" data-bs-toggle="modal">
                            <i class="bx bx-plus me-1"></i>
                            Thêm mới tệp tin
                        </a>
                        <a data-bs-toggle="modal" data-bs-target="#create_device_modal" data-bs-original-title="Thêm mới 1 đối tác" class="btn btn-primary btn-sm">
                            <i class="bx bx-plus me-1"></i>
                            Thêm mới
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <form action="{{ route('export-devices') }}" method="get" class="d-flex">
                            <div class="col-md-2 me-2">
                                <label class="form-label">Tìm kiếm thiết bị</label>
                                <input type="text" class="form-control" name="search_value" id="search_filter">
                            </div>
                            <div class="col-md-2 me-2">
                                <div class="form-group">
                                    <label class="form-label">Trạng thái</label>
                                    <select name="device_status" id="device_status_filter" class="form-select">
                                        <option value="">Chọn trạng thái</option>
                                        <option value="1">Kích hoạt</option>
                                        <option value="2">Vô hiệu hóa</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <a class="btn btn-success btnSearch me-2" data-bs-original-title="Tìm kiếm" data-bs-toggle="tooltip" data-bs-placement="top">
                                    <i class="bx bx-search"></i>
                                </a>
                                <button type="submit" class="btn btn-secondary btnExport" data-bs-original-title="Xuất dữ liệu" data-bs-toggle="tooltip" data-bs-placement="top">
                                    <i class="bx bx-export"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    <table id="list_devices_table"  class="table table-striped table-bordered dataTable no-footer mt-3">

                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- start modal add device --}}
    <div class="modal fade" id="create_device_modal" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myExtraLargeModalLabel">Thêm mới thiết bị</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{route('create-single-device')}}" id="create_device_form" method="post">
                        @csrf
                        {{-- device_code and sign_date --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Mã thiết bị <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="device_code" name="device_code">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Tên thiết bị<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="device_name" name="device_name">
                                </div>
                            </div>
                        </div>

                        {{-- device_id and register_date --}}
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Số serial<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="serial_number" name="serial_number">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Trạng thái<span class="required">*</span></label>
                                    <select name="device_status" id="device_status" class="form-select">
                                        <option value="">--Chọn--</option>
                                        <option value="1">Kích hoạt</option>
                                        <option value="2">Vô hiệu hóa</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 col-xs-12 d-flex justify-content-end">
                                <button class="btn btn-success" type="submit" id="btn_create_single_device">Thêm mới</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.end modal add device -->

    {{-- start modal add device --}}
    <div class="modal fade" id="update_device_modal" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myExtraLargeModalLabel">Cập nhật thiết bị</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{route('update-single-device')}}" id="update_device_form" method="post">
                        @csrf
                        <input type="hidden" name="device_id" id="device_id">
                        {{-- device_code and sign_date --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Mã thiết bị <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="update_device_code" name="update_device_code">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Tên thiết bị<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="update_device_name" name="update_device_name">
                                </div>
                            </div>
                        </div>

                        {{-- device_id and register_date --}}
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Số serial<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="update_serial_number" name="update_serial_number">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Trạng thái<span class="required">*</span></label>
                                    <select name="update_device_status" id="update_device_status" class="form-select">
                                        <option value="">--Chọn--</option>
                                        <option value="1">Kích hoạt</option>
                                        <option value="2">Vô hiệu hóa</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 col-xs-12 d-flex justify-content-end">
                                <button class="btn btn-success" type="submit" id="btn_update_single_device">Cập nhật</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.end modal update device -->


    {{-- start modal import excel device --}}
    <div class="modal fade" id="import_excel_modal" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myExtraLargeModalLabel">Thêm mới tệp tin thông tin thiết bị</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{route('import-excel-devices')}}" id="import_excel_form" method="post" enctype="multipart/form-data">
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
    </div><!-- /.end modal import excel device -->

    <input type="hidden" id="_token" value="{{ csrf_token() }}">
@endsection

@section('scripts')
    <script src="{{URL::asset($custom_asset.'/assets/libs/imask/imask.min.js')}}"></script>
    <script src="{{URL::asset($custom_asset.'/assets/libs/bootstrap_table/bootstrap-table.js')}}"></script>
    <script src="{{URL::asset($custom_asset.'/assets/js/pages_js/list_devices.js')}}"></script>
    <script src="{{URL::asset($custom_asset.'/assets/js/pages/password-addon.init.js')}}"></script>
@endsection
@section('styles')
    <link href="{{URL::asset($custom_asset.'/assets/libs/bootstrap_table/bootstrap-table.css')}}" rel="stylesheet" type="text/css" />
@endsection
