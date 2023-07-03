@extends('layouts.app')
@section('main_content')
    <div class="row">
        <div class="col-xs-12 col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <span>Danh sách tòa nhà</span>
                    <div class="add-building-group">
                        <span data-bs-toggle="tooltip" data-bs-original-title="Cách lấy vĩ độ - kinh độ" data-bs-placement="top">
                            <a class="btn btn-success btn-sm" data-bs-target="#guide_building_modal" data-bs-toggle="modal">
                                <i class="ri-eye-fill align-middle me-1"></i>
                            </a>
                        </span>
                        <a class="btn btn-success btn-sm" data-bs-target="#import_excel_modal"
                            data-bs-toggle="modal">
                            <i class="bx bx-plus me-1"></i>
                            Thêm mới tệp tin
                        </a>
                        <a data-bs-toggle="modal" data-bs-target="#create_building_modal"
                            data-bs-original-title="Thêm mới 1 đối tác" class="btn btn-primary btn-sm">
                            <i class="bx bx-plus me-1"></i>
                            Thêm mới
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <form action="{{ route('export-buildings') }}" method="get" class="d-flex">
                            <div class="col-md-2 mx-2">
                                <div class="form-group">
                                    <label class="form-label">Tìm kiếm tòa nhà</label>
                                    <input name="search_value" id="search_filter" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-2 me-2">
                                <div class="form-group">
                                    <label class="form-label">Trạng thái</label>
                                    <select name="status" id="status_filter" class="form-select">
                                        <option value="">--Chọn--</option>
                                        <option value="1">Đã kích hoạt</option>
                                        <option value="2">Đã bị vô hiệu hóa</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <a class="btn btn-success btnSearch me-2" data-bs-original-title="Tìm kiếm tòa nhà"
                                    data-bs-toggle="tooltip" data-bs-placement="top">
                                    <i class="bx bx-search"></i>
                                </a>
                                
                                <button type="submit" class="btn btn-secondary btnExport" data-bs-original-title="Xuất dữ liệu " data-bs-toggle="tooltip" data-bs-placement="top">
                                    <i class="bx bx-export"></i>
                                </button>
                            </div>
                        </form>
                        
                    </div>
                    <table id="list_buildings_table" class="table table-striped table-bordered dataTable no-footer mt-3">

                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- start modal add building --}}
    <div class="modal fade" id="create_building_modal" tabindex="-1" role="dialog"
        aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myExtraLargeModalLabel">Thêm mới tòa nhà</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('create-single-building') }}" id="create_building_form" method="post">
                        @csrf
                        {{-- building_code and sign_date --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Mã tòa nhà <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="building_code" name="building_code">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Tên tòa nhà<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="building_name" name="building_name">
                                </div>
                            </div>
                        </div>

                        {{-- building_id and building_address --}}
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Chủ sở hữu</label>
                                    <input type="text" class="form-control" id="building_company"
                                        name="building_company">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Địa chỉ<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="building_address"
                                        name="building_address">
                                </div>
                            </div>
                        </div>

                        {{-- longitude and latitue --}}
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Kinh độ<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="building_longitude"
                                        name="building_longitude">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Vĩ độ<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="building_latitude"
                                        name="building_latitude">
                                </div>
                            </div>
                        </div>
                        {{-- contact_name and contact_phone --}}
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Đầu mối tòa nhà</label>
                                    <input type="text" class="form-control" id="contact_name" name="contact_name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Số điện thoại đầu mối</label>
                                    <input type="text" class="form-control" id="contact_phone" name="contact_phone">
                                </div>
                            </div>
                        </div>

                        {{-- partner_infrastructure and cooperate_type --}}
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group" id="partner_id_group">
                                    <label class="form-label">Đối tác hạ tầng<span class="required">*</span></label>
                                    <select class="form-select" id="partner_id" name="partner_id">
                                        <option value="">--Chọn--</option>
                                        @if ($list_partner)
                                            @foreach ($list_partner as $item)
                                                <option value="{{ $item->id }}">{{ $item->partner_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Loại hình hợp tác<span class="required">*</span></label>
                                    <select name="cooperate_type" id="cooperate_type" class="form-select">
                                        <option value="">Chọn</option>
                                        <option value="1">Mobifone tự triển khai</option>
                                        <option value="2">Một phần</option>
                                        <option value="3">Toàn trình</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- percent_share and is_active --}}
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Tỉ lệ chia sẻ<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="percent_share" name="percent_share">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Trạng thái<span class="required">*</span></label>
                                    <select name="is_active" id="is_active" class="form-select">
                                        <option value="">--Chọn--</option>
                                        <option value="1">Kích hoạt</option>
                                        <option value="2">Vô hiệu hóa</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12 col-xs-12 d-flex justify-content-end">
                                <button class="btn btn-success" type="submit" id="btn_create_single_building">Thêm mới</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.end modal add building -->

    {{-- start modal update building --}}
    <div class="modal fade" id="update_building_modal" tabindex="-1" role="dialog"
        aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myExtraLargeModalLabel">Cập nhật thông tin tòa nhà</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('update-single-building') }}" id="update_building_form" method="post">
                        @csrf
                        <input type="hidden" name="building_id" id="building_id">
                        {{-- building_code and sign_date --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Mã tòa nhà <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="update_building_code"
                                        name="update_building_code">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Tên tòa nhà<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="update_building_name" name="update_building_name">
                                </div>
                            </div>
                        </div>

                        {{-- building_id and building_address --}}
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Chủ sở hữu</label>
                                    <input type="text" class="form-control" id="update_building_company" name="update_building_company">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Địa chỉ<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="update_building_address" name="update_building_address">
                                </div>
                            </div>
                        </div>

                        {{-- longitude and latitue --}}
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Kinh độ<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="update_building_longitude" name="update_building_longitude">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Vĩ độ<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="update_building_latitude" name="update_building_latitude">
                                </div>
                            </div>
                        </div>

                        {{-- contact_name and contact_phone --}}
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Đầu mối tòa nhà</label>
                                    <input type="text" class="form-control" id="update_contact_name" name="update_contact_name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Số điện thoại đầu mối</label>
                                    <input type="text" class="form-control" id="update_contact_phone" name="update_contact_phone">
                                </div>
                            </div>
                        </div>

                        {{-- partner_infrastructure and cooperate_type --}}
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group" id="update_partner_id_group">
                                    <label class="form-label">Đối tác hạ tầng<span class="required">*</span></label>
                                    <select class="form-select" id="update_partner_id" name="update_partner_id">
                                        <option value="">--Chọn--</option>
                                        @foreach ($list_partner as $item)
                                            <option value="{{ $item->id }}">{{ $item->partner_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Loại hình hợp tác<span class="required">*</span></label>
                                    <select name="update_cooperate_type" id="update_cooperate_type" class="form-select">
                                        <option value="">Chọn</option>
                                        <option value="1">Mobifone tự triển khai</option>
                                        <option value="2">Một phần</option>
                                        <option value="3">Toàn trình</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- percent_share and is_active --}}
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Tỉ lệ chia sẻ<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="update_percent_share" name="update_percent_share">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Trạng thái<span class="required">*</span></label>
                                    <select name="update_is_active" id="update_is_active" class="form-select">
                                        <option value="">--Chọn--</option>
                                        <option value="1">Kích hoạt</option>
                                        <option value="2">Vô hiệu hóa</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 col-xs-12 d-flex justify-content-end">
                                <button class="btn btn-success" type="submit" id="btn_update_single_building">Cập nhật</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.end modal update building -->


    {{-- start modal import excel building --}}
    <div class="modal fade" id="import_excel_modal" tabindex="-1" role="dialog"aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myExtraLargeModalLabel">Thêm tệp tin thông tin tòa nhà</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('import-excel-buildings') }}" id="import_excel_form" method="post" enctype="multipart/form-data">
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
                                <table id="list_errors_in_file"
                                    class="table table-striped table-bordered dataTable no-footer mt-3">

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
    </div><!-- /.end modal import excel building -->
    
    {{-- start modal import excel building --}}
    <div class="modal fade" id="guide_building_modal" tabindex="-1" role="dialog"aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myExtraLargeModalLabel">Hướng dẫn lấy kinh độ vĩ độ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <img name="guide_image" id="guide_image" src="{{url('/uploads/guides/image_2023_06_23T04_00_48_608Z.png')}}" class="form-control">
                        </div>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.end modal import excel building -->

    <input type="hidden" id="_token" value="{{ csrf_token() }}">
@endsection

@section('scripts')
    <script src="{{ URL::asset($custom_asset . '/assets/libs/imask/imask.min.js') }}"></script>
    <script src="{{ URL::asset($custom_asset . '/assets/libs/bootstrap_table/bootstrap-table.js') }}"></script>
    <script src="{{ URL::asset($custom_asset . '/assets/libs/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ URL::asset($custom_asset . '/assets/js/pages_js/list_buildings.js') }}"></script>
    <script src="{{ URL::asset($custom_asset . '/assets/js/pages/password-addon.init.js') }}"></script>
@endsection
@section('styles')
    <link href="{{ URL::asset($custom_asset . '/assets/libs/bootstrap_table/bootstrap-table.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ URL::asset($custom_asset . '/assets/libs/select2/dist/css/select2.min.css') }}" rel="stylesheet"
        type="text/css" />
@endsection
