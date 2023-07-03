@extends('layouts.app')
@section('main_content')
    <div class="row">
        <div class="col-xs-12 col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <span>Danh sách hợp đồng</span>
                    <div class="add-contract-group">
                        <a class="btn btn-success btn-sm d-none" data-bs-target="#expot_excel_contract_modal" data-bs-toggle="modal">
                            <i class="bx bxs-file-export me-1"></i>
                            Xuất
                        </a>
                        <a class="btn btn-success btn-sm" data-bs-target="#import_excel_modal"
                            data-bs-toggle="modal">
                            <i class="bx bx-plus me-1"></i>
                            Thêm mới tệp tin
                        </a>
                        <a data-bs-toggle="modal" data-bs-target="#create_contract_modal"
                            data-bs-original-title="Thêm mới 1 đối tác" class="btn btn-primary btn-sm">
                            <i class="bx bx-plus me-1"></i>
                            Thêm mới
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <form action="{{ route('export-contracts') }}" method="get" class="d-flex">
                            <div class="col-md-2 me-2">
                                <div class="form-group">
                                    <label class="form-label">Loại hình hợp tác</label>
                                    <select name="type_cooperate" id="type_cooperate_filter" class="form-select">
                                        <option value=""> -- Chọn -- </option>
                                        <option value="1">Mobifone tự triển khai</option>
                                        <option value="2">Một phần</option>
                                        <option value="3">Toàn trình</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 me-2">
                                <div class="form-group">
                                    <label class="form-label">Trạng thái</label>
                                    <select name="contract_status" id="contract_status_filter" class="form-select">
                                        <option value="0">Tất cả trạng thái</option>
                                        <option value="1">Chờ ký</option>
                                        <option value="2">Đang sử dụng</option>
                                        <option value="3">Đã hết hạn</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 me-2">
                                <div class="form-group">
                                    <label class="form-label">Tìm kiếm hợp đồng</label>
                                    <input name="search_value" id="search_filter" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <a class="btn btn-success btnSearch me-2" data-bs-original-title="Tìm kiếm" data-bs-toggle="tooltip"
                                    data-bs-placement="top">
                                    <i class="bx bx-search"></i>
                                </a>

                                <button type="submit" class="btn btn-secondary btnExport" data-bs-original-title="Xuất dữ liệu" data-bs-toggle="tooltip" data-bs-placement="top">
                                    <i class="bx bx-export"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    <table id="list_contracts_table" class="table table-striped table-bordered dataTable no-footer mt-3">

                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- start modal add contract --}}
    <div class="modal fade" id="create_contract_modal" tabindex="-1" role="dialog"
        aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myExtraLargeModalLabel">Thêm mới hợp đồng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('create-single-contract') }}" id="create_contract_form" method="post">
                        @csrf
                        {{-- contract_code and sign_date --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Mã hợp đồng<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="contract_code" name="contract_code">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Loại hình hợp tác<span class="required">*</span></label>
                                    <select name="type_cooperate" id="type_cooperate" class="form-select">
                                        <option value=""> -- Chọn -- </option>
                                        <option value="1">Mobifone tự triển khai</option>
                                        <option value="2">Một phần</option>
                                        <option value="3">Toàn trình</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- package_id and register_date --}}
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group" id="package_id_group">
                                    <label class="form-label">Gói cước<span class="required">*</span></label>
                                    <select name="package_id" id="package_id" class="form-select">
                                        <option value=""> -- Chọn -- </option>
                                        @if ($list_packages)
                                            @foreach ($list_packages as $item)
                                                <option data-prices="{{$item->prices}}" data-prices_vat="{{$item->prices_vat}}" value="{{ $item->id }}">{{ $item->package_name }} - {{ number_format($item->prices) . 'đ' }} - ({{ $item->time_used }} + {{ $item->promotion_time }})</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Ngày bắt đầu gói cước<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="start_date_package" name="start_date_package">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Thiết bị</label>
                                    <select name="device_id" id="device_id" class="form-select">
                                        <option value=""> -- Chọn -- </option>
                                        @if ($list_devices)
                                            @foreach ($list_devices as $item)
                                                <option value="{{ $item->id }}">{{ $item->device_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Mã hóa đơn</label>
                                    <input type="text" class="form-control" id="bill_code" name="bill_code">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Ngày hóa đơn</label>
                                    <input type="text" class="form-control" name="bill_date" id="bill_date" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Số tiền</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="bill_price" id="bill_price" readonly="readonly"/>
                                        <span class="input-group-text">Sau thuế</span>
                                        <input type="text" class="form-control" name="bill_price_vat" id="bill_price_vat" readonly="readonly"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group" id="partner_id_group">
                                    <label class="form-label">Đối tác hạ tầng<span class="required">*</span></label>
                                    <select class="form-select" name="partner_id" id="partner_id">
                                        <option value=""> -- Chọn -- </option>
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
                                    <label class="form-label">Tòa nhà<span class="required">*</span></label>
                                    <select class="form-select" name="building_id" id="building_id">
                                        <option value=""> -- Chọn -- </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group" id="customer_id_group">
                                    <label class="form-label">Khách hàng<span class="required">*</span></label>
                                    <select class="form-select" name="customer_id" id="customer_id">
                                        <option value=""> -- Chọn -- </option>
                                        @if ($list_customer)
                                            @foreach ($list_customer as $item)
                                                <option value="{{ $item->id }}">{{ $item->firstname }} {{ $item->last_name }} - {{ $item->phone }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" id="address_id_group">
                                    <label class="form-label">Địa chỉ khách hàng<span class="required">*</span></label>
                                    <select class="form-select" name="address_id" id="address_id">
                                        <option value=""> -- Chọn -- </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Trạng thái<span class="required">*</span></label>
                                    <select name="contract_status" id="contract_status" class="form-select">
                                        <option value=""> -- Chọn -- </option>
                                        <option value="1">Chờ ký</option>
                                        <option value="2">Đang sử dụng</option>
                                        <option value="3">Đã hết hạn</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Mã cửa hàng</label>
                                    <input name="shopcode" id="shopcode" class="form-control" />
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Mã khách hàng <span class="required">*</span></label>
                                    <input name="customer_code" id="customer_code" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" id="employee_id_group">
                                    <label class="form-label">Mã nhân viên <span class="required">*</span></label>
                                    <select type="text" class="form-select" name="employee_id" id="employee_id">
                                        <option value=""> -- Chọn -- </option>
                                        @if ($list_employees)
                                            @foreach ($list_employees as $employee_item)
                                                <option value="{{ $employee_item->id }}">{{ $employee_item->firstname }} {{ $employee_item->lastname }} - {{ $employee_item->phone }}</option>
                                            @endforeach

                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 col-xs-12 d-flex justify-content-end">
                                <button class="btn btn-success" type="submit" id="btn_create_single_contract">Thêm mới</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.end modal add contract -->

    {{-- start modal add contract --}}
    <div class="modal fade" id="update_contract_modal" tabindex="-1" role="dialog"
        aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myExtraLargeModalLabel">Cập nhật thông tin hợp đồng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('update-single-contract') }}" id="update_contract_form" method="post">
                        @csrf
                        <input type="hidden" name="contract_id" id="contract_id">
                        {{-- contract_code and sign_date --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Mã hợp đồng<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="update_contract_code"
                                        name="update_contract_code">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Loại hình hợp tác<span class="required">*</span></label>
                                    <select name="update_type_cooperate" id="update_type_cooperate" class="form-select">
                                        <option value=""> -- Chọn -- </option>
                                        <option value="1">Mobifone tự triển khai</option>
                                        <option value="2">Một phần</option>
                                        <option value="3">Toàn trình</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- package_id and register_date --}}
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group" id="update_package_id_group">
                                    <label class="form-label">Gói cước<span class="required">*</span></label>
                                    <select name="update_package_id" id="update_package_id" class="form-select">
                                        <option value=""> -- Chọn -- </option>
                                        @if ($list_packages)
                                            @foreach ($list_packages as $item)
                                            <option data-prices="{{$item->prices}}" data-prices_vat="{{$item->prices_vat}}" value="{{ $item->id }}">{{ $item->package_name }} - {{ number_format($item->prices) . 'đ' }} - ({{ $item->time_used }} + {{ $item->promotion_time }})</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Ngày bắt đầu gói cước<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="update_start_date_package" name="update_start_date_package">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Thiết bị</label>
                                    <select name="update_device_id" id="update_device_id" class="form-select">
                                        <option value=""> -- Chọn -- </option>
                                        @if ($list_devices)
                                            @foreach ($list_devices as $item)
                                                <option value="{{ $item->id }}">{{ $item->device_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Mã hóa đơn</label>
                                    <input type="text" class="form-control" id="update_bill_code" name="update_bill_code">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Ngày hóa đơn</label>
                                    <input type="text" class="form-control" name="update_bill_date" id="update_bill_date" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Số tiền<span class="required">*</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="update_bill_price" id="update_bill_price" readonly="readonly"/>
                                        <span class="input-group-text">Sau thuế</span>
                                        <input type="text" class="form-control" name="update_bill_price_vat" id="update_bill_price_vat" readonly="readonly"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group" id="update_customer_id_group">
                                    <label class="form-label">Khách hàng<span class="required">*</span></label>
                                    <select class="form-select" name="update_customer_id" id="update_customer_id">
                                        <option value=""> -- Chọn -- </option>
                                        @if ($list_customer)
                                            @foreach ($list_customer as $item)
                                                <option value="{{ $item->id }}">{{ $item->firstname }}
                                                    {{ $item->last_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" id="update_address_id_group">
                                    <label class="form-label">Địa chỉ khách hàng<span class="required">*</span></label>
                                    <select class="form-select" name="update_address_id" id="update_address_id">
                                        <option value=""> -- Chọn -- </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group" id="update_partner_id_group">
                                    <label class="form-label">Đối tác hạ tầng<span class="required">*</span></label>
                                    <select class="form-select" name="update_partner_id" id="update_partner_id">
                                        <option value=""> -- Chọn -- </option>
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
                                    <label class="form-label">Tòa nhà<span class="required">*</span></label>
                                    <select class="form-select" name="update_building_id" id="update_building_id">
                                        <option value=""> -- Chọn -- </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Trạng thái<span class="required">*</span></label>
                                    <select name="update_contract_status" id="update_contract_status" class="form-select">
                                        <option value=""> -- Chọn -- </option>
                                        <option value="1">Chờ ký</option>
                                        <option value="2">Đang sử dụng</option>
                                        <option value="3">Đã hết hạn</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Mã cửa hàng</label>
                                    <input name="update_shopcode" id="update_shopcode" class="form-control" />
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Mã khách hàng <span class="required">*</span></label>
                                    <input name="update_customer_code" id="update_customer_code" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" id="update_employee_id_group">
                                    <label class="form-label">Mã nhân viên <span class="required">*</span></label>
                                    <select type="text" class="form-select" name="update_employee_id" id="update_employee_id">
                                        <option value=""> -- Chọn -- </option>
                                        @if ($list_employees)
                                            @foreach ($list_employees as $employee_item)
                                                <option value="{{ $employee_item->id }}">{{ $employee_item->firstname }} {{ $employee_item->lastname }} - {{ $employee_item->phone }}</option>
                                            @endforeach

                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 col-xs-12 d-flex justify-content-end">
                                <button class="btn btn-success" type="submit" id="btn_update_single_contract">Cập nhật</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.end modal update contract -->


    {{-- start modal import excel contract --}}
    <div class="modal fade" id="import_excel_modal" data-bs-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myExtraLargeModalLabel">Thêm mới tệp tin thông tin hợp đồng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('import-excel-contracts') }}" id="import_excel_form" method="post"
                        enctype="multipart/form-data">
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
                                <table id="list_errors_in_file" class="table table-striped table-bordered dataTable no-footer mt-3">

                                </table>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 col-xs-12 d-flex justify-content-end">
                                {{-- <button class="btn btn-success" type="submit" id="btn_check_file_excel">Kiểm tra</button> --}}
                                <button class="btn btn-danger d-none" type="button" id="btn_save_excel">Lưu lại</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.end modal import excel contract -->

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

    {{-- start modal add job --}}
    <div class="modal fade" id="forward_to_equipment_installation_job_modal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myExtraLargeModalLabel">Thêm mới công việc lắp đặt</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{route('create-equipment-job')}}" id="create_job_form" method="post">
                        @csrf
                        <input type="hidden" name="job_contract_id" id="job_contract_id">
                        <input type="hidden" name="job_id" id="job_id">
                        {{-- customer_id and job_code --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Khách hàng<span class="required">*</span></label>
                                    <select name="job_customer_id" id="job_customer_id" class="form-select">
                                        <option value="">--Chọn--</option>
                                        @if ($getAllCustomers)
                                            @foreach ($getAllCustomers as $customer)
                                                <option data-customer_code="{{ $customer->code }}" value="{{ $customer->id }}">{{ $customer->last_name }} {{ $customer->firstname }} {{ $customer->phone ? '- ' . $customer->phone : '' }} {{ $customer->email ? '- ' . $customer->email : '' }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Mã công việc <span class="required">*</span></label>
                                    <input type="text" readonly class="form-control" id="job_code" name="job_code">
                                </div>
                            </div>

                        </div>

                        {{-- job_type and job_reason --}}
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Loại công việc<span class="required">*</span></label>
                                    <select name="job_type" id="job_type" disabled class="form-select">
                                        <option value="">-- Chọn --</option>
                                        @if ($jobTypeList)
                                            @foreach ($jobTypeList as $job_item)
                                                <option value="{{ $job_item->id }}" @if ($job_item->id == $EQUIPMENT_INSTALLATION) selected @endif>{{ $job_item->value_setting }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Lý do tạo công việc<span class="required">*</span></label>
                                    <select name="reason" id="reason" class="form-select">
                                        <option value="">--Chọn--</option>
                                        @if ($jobReasonList)
                                            @foreach ($jobReasonList as $job_reason_item)
                                                <option value="{{ $job_reason_item->id }}" @if ($job_reason_item->id == $EQUIPMENT_INSTALLATION) selected @endif>{{ $job_reason_item->value_setting }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- address and description --}}
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group" id="job_address_id_group">
                                    <label class="form-label">Địa chỉ khách hàng<span class="required">*</span></label>
                                    <select name="job_address_id" id="job_address_id" class="form-select">
                                        <option value=""> -- Chọn -- </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Mô tả</label>
                                    <input name="description" id="description" class="form-control" />
                                </div>
                            </div>
                        </div>

                        {{-- building --}}
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group" id="job_partner_id_group">
                                    <label class="form-label">Đối tác<span class="required">*</span></label>
                                    <select name="job_partner_id" id="job_partner_id" class="form-select">
                                        <option value=""> -- Chọn-- </option>
                                        @if ($allPartners)
                                            @foreach ($allPartners as $partner)
                                                <option value="{{ $partner->id }}">{{ $partner->partner_name }} - {{ $partner->partner_code }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Tòa nhà<span class="required">*</span></label>
                                    <select name="job_building_id" id="job_building_id" class="form-select">
                                        <option value="">--Chọn--</option>
                                        @if ($getAllBuildings)
                                            @foreach ($getAllBuildings as $building)
                                                <option value="{{ $building->id }}">{{ $building->building_name }} - {{ $building->building_code }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- status and job_employee_ids --}}
                        <div class="row mt-3">
                            {{-- employee_ids --}}
                            <div class="col-md-6">
                                <div class="form-group" id="employee_ids_group">
                                    <label class="form-label">Nhân viên thực hiện<span class="required">*</span></label>
                                    <select name="job_employee_ids" id="job_employee_ids" class="form-select" multiple>
                                        @if ($getAllEmployees)
                                            @foreach ($getAllEmployees as $item)
                                                <option value="{{ $item->id }}">{{ $item->lastname }} {{ $item->firstname }} {{ $item->email . " - " . $item->phone }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div id="list_employee" class="d-none mt-3">

                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12 col-xs-12 d-flex justify-content-end">
                                <button class="btn btn-success" type="submit" id="btn_create_single_job">Thêm mới</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.end modal add job -->

    <input type="hidden" id="_token" value="{{ csrf_token() }}">
@endsection

@section('scripts')
    <script src="{{ URL::asset($custom_asset . '/assets/libs/imask/imask.min.js') }}"></script>
    <script src="{{ URL::asset($custom_asset . '/assets/libs/bootstrap_table/bootstrap-table.js') }}"></script>
    <script src="{{ URL::asset($custom_asset . '/assets/libs/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ URL::asset($custom_asset . '/assets/js/accounting.js') }}"></script>
    <script src="{{ URL::asset($custom_asset . '/assets/js/pages_js/list_contracts.js') }}"></script>
    <script src="{{ URL::asset($custom_asset . '/assets/js/pages/password-addon.init.js') }}"></script>
    <script src="{{URL::asset($custom_asset.'/assets/libs/fancybox/script/jquery.fancybox.min.js')}}"></script>
@endsection
@section('styles')
    <link href="{{ URL::asset($custom_asset . '/assets/libs/bootstrap_table/bootstrap-table.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset($custom_asset . '/assets/libs/select2/dist/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{URL::asset($custom_asset.'/assets/libs/fancybox/style/jquery.fancybox.min.css')}}" rel="stylesheet" type="text/css" />
@endsection
