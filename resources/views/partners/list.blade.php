@extends('layouts.app')
@section('main_content')
    <div class="row">
        <div class="col-xs-12 col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <span>Danh sách đối tác</span>
                    <div class="add-partner-group">
                        <a class="btn btn-success btn-sm d-none" data-bs-target="#import_excel_partner_modal" data-bs-toggle="modal">
                            <i class="bx bxs-file-export me-1"></i>
                            Xuất
                        </a>
                        <a class="btn btn-success btn-sm" data-bs-target="#import_excel_partner_modal" data-bs-toggle="modal">
                            <i class="bx bx-plus me-1"></i>
                            Thêm mới tệp tin
                        </a>
                        <a data-bs-toggle="modal" data-bs-target="#add_partner_modal" data-bs-original-title="Thêm mới 1 đối tác" class="btn btn-primary btn-sm">
                            <i class="bx bx-plus me-1"></i>
                            Thêm mới
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <form action="{{ route('export-partners') }}" method="get" class="d-flex">
                            <div class="col-md-2 col-xs-12 ms-2">
                                <label class="form-label">Trạng thái</label>
                                <select name="status" id="status_filter" class="form-select">
                                    <option value="">--Chọn--</option>
                                    <option value="1">Đã kích hoạt</option>
                                    <option value="2">Đã bị vô hiệu hóa</option>
                                </select>
                            </div>
                            <div class="col-md-2 col-xs-12 ms-2">
                                <label class="form-label">Loại hình hợp tác</label>
                                <select name="cooperate" id="cooperate_filter" class="form-select">
                                    <option value="">Chọn</option>
                                    <option value="1">Mobifone tự triển khai</option>
                                    <option value="2">Một phần</option>
                                    <option value="3">Toàn trình</option>
                                </select>
                            </div>
                            <div class="col-md-2 col-xs-12 ms-2">
                                <label class="form-label">Tìm kiếm đối tác</label>
                                <input name="search_value" id="search_name" class="form-control" />
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <a class="btn btn-success btnSearch me-2 ms-2" data-bs-original-title="Tìm kiếm đối tác" data-bs-toggle="tooltip" data-bs-placement="top">
                                    <i class="bx bx-search"></i>
                                </a>
                                
                                <button type="submit" class="btn btn-secondary btnExport" data-bs-original-title="Xuất dữ liệu" data-bs-toggle="tooltip" data-bs-placement="top">
                                    <i class="bx bx-export"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    <table id="list_partner_table"  class="table table-striped table-bordered dataTable no-footer mt-3">

                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- start modal add partner --}}
    <div class="modal fade" id="add_partner_modal" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myExtraLargeModalLabel">Thêm mới đối tác</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{route('create-partner')}}" id="create_partner_form" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Tên đối tác<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="partner_name" name="partner_name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Email<span class="required">*</span></label>
                                    <input type="email" class="form-control" id="partner_email" name="partner_email">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Số điện thoại liên hệ<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="partner_phone" name="partner_phone">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Địa chỉ</label>
                                    <input type="text" class="form-control" id="partner_address" name="partner_address">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Mã code đối tác<span class="required">*</span></label>
                                    <input type="text" name="partner_code" id="partner_code" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Giấy phép kinh doanh<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="business_license" name="business_license">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Số tài khoản<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="number_bank" name="number_bank">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Tên ngân hàng<span class="required">*</span></label>
                                    <input type="text" name="bank_name" id="bank_name" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Đầu mối</label>
                                    <input type="text" class="form-control" id="contact_name" name="contact_name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Số điện thoại đầu mối</label>
                                    <input type="text" name="contact_phone" id="contact_phone" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Loại hình hợp tác<span class="required">*</span></label>
                                    <select name="cooperate" id="cooperate" class="form-select">
                                        <option value="">Chọn</option>
                                        <option value="1">Mobifone tự triển khai</option>
                                        <option value="2">Một phần</option>
                                        <option value="3">Toàn trình</option>
                                    </select>

                                </div>
                            </div>
                            <div class="col-md-6 col-xs-12">
                                <label class="form-label">Trạng thái</label>
                                <select name="status_partners" id="status_partners" class="form-select">
                                    <option value="">Chọn</option>
                                    <option value="1">Kích hoạt</option>
                                    <option value="2">Vô hiệu hóa</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 col-xs-12 d-flex justify-content-end">
                                <button class="btn btn-success" type="submit" id="btn_add_single_partner">Thêm mới</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.end modal add partner -->

    {{-- start modal add partner --}}
    <div class="modal fade" id="update_partner_modal" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myExtraLargeModalLabel">Cập nhật đối tác</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{route('update-partner')}}" id="update_partner_form" method="post">
                        @csrf
                        <input type="hidden" name="partner_id" id="partner_id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Tên đối tác<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="update_partner_name" name="update_partner_name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Email<span class="required">*</span></label>
                                    <input type="email" class="form-control" id="update_partner_email" name="update_partner_email">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Số điện thoại liên hệ<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="update_partner_phone" name="update_partner_phone">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Địa chỉ</label>
                                    <input type="text" class="form-control" id="update_partner_address" name="update_partner_address">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Mã code đối tác<span class="required">*</span></label>
                                    <input type="text" name="update_partner_code" id="update_partner_code" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Giấy phép kinh doanh<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="update_business_license" name="update_business_license">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Số tài khoản<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="update_number_bank" name="update_number_bank">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Tên ngân hàng<span class="required">*</span></label>
                                    <input type="text" name="update_bank_name" id="update_bank_name" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Đầu mối</label>
                                    <input type="text" class="form-control" id="update_contact_name" name="update_contact_name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Số điện thoại đầu mối</label>
                                    <input type="text" name="update_contact_phone" id="update_contact_phone" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Loại hình hợp tác<span class="required">*</span></label>
                                    <select name="update_cooperate" id="update_cooperate" class="form-select">
                                        <option value="">Chọn</option>
                                        <option value="1">Mobifone tự triển khai</option>
                                        <option value="2">Một phần</option>
                                        <option value="3">Toàn trình</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-xs-12">
                                <label class="form-label">Trạng thái</label>
                                <select name="update_status_partner" id="update_status_partner" class="form-select">
                                    <option value="">Chọn</option>
                                    <option value="1">Kích hoạt</option>
                                    <option value="2">Vô hiệu hóa</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 col-xs-12 d-flex justify-content-end">
                                <button class="btn btn-success" type="submit" id="btn_update_single_partner">Cập nhật</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.end modal update partner -->


    {{-- start modal import excel partner --}}
    <div class="modal fade" id="import_excel_partner_modal" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myExtraLargeModalLabel">Thêm mới tệp thông tin đối tác</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{route('import-excel-partner')}}" id="import_excel_partner_form" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row" id="import_group">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">
                                        Chọn file
                                        <a href="{{ $template_partner ?? null }}" data-bs-toggle="tooltip" data-bs-original-title="Tải file mẫu" data-bs-placement="top" class="ms-2 " name="file_template" id="file_template" download><i class="bx bxs-cloud-download bx-sm"></i></a>
                                    </label>
                                    <input type="file" name="partnerExcel" id="partnerExcel" id="" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <div class="form-group">
                                    <button class="btn btn-success" type="submit" id="btn_check_file_excel_partner">Kiểm tra</button>
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
                                <button class="btn btn-danger" type="button" id="btn_save_excel_partner">Lưu lại</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.end modal import excel partner -->

    <input type="hidden" id="_token" value="{{ csrf_token() }}">
@endsection

@section('scripts')
    <script src="{{URL::asset($custom_asset.'/assets/libs/bootstrap_table/bootstrap-table.js')}}"></script>
    <script src="{{URL::asset($custom_asset.'/assets/js/pages_js/list_partner.js')}}"></script>
@endsection
@section('styles')
    <link href="{{URL::asset($custom_asset.'/assets/libs/bootstrap_table/bootstrap-table.css')}}" rel="stylesheet" type="text/css" />
@endsection
