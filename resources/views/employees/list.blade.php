@extends('layouts.app')
@section('main_content')
    <div class="row">
        <div class="col-xs-12 col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <span>Danh sách nhân viên</span>
                    <div class="add-employee-group">
                        <a class="btn btn-success btn-sm d-none" data-bs-target="#expot_excel_employee_modal" data-bs-toggle="modal">
                            <i class="bx bxs-file-export me-1"></i>
                            Xuất
                        </a>
                        <a class="btn btn-success btn-sm" data-bs-target="#import_excel_modal"data-bs-toggle="modal">
                            <i class="bx bx-plus me-1"></i>
                            Thêm mới tệp tin
                        </a>
                        <a data-bs-toggle="modal" data-bs-target="#add_employee_modal"
                            data-bs-original-title="Thêm mới 1 đối tác" class="btn btn-primary btn-sm">
                            <i class="bx bx-plus me-1"></i>
                            Thêm mới
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <form action="{{ route('export-employees') }}" method="get" class="d-flex">
                            <div class="col-md-3 mx-2">
                                <label class="form-label">Loại nhân viên</label>
                                <select name="role_group_id" id="role_group_id_filter" class="form-select">
                                    <option value="">-- Chọn --</option>
                                    @if ($list_role_groups)
                                        @foreach ($list_role_groups as $role_group_item)
                                            <option value="{{ $role_group_item->id }}">{{ $role_group_item->role_name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3 me-2">
                                <label class="form-label">Tìm kiếm nhân viên</label>
                                <input type="text" class="form-control" name="search_value" id="search_value_filter">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <a class="btn btn-success btnSearch me-2" data-bs-original-title="Tìm kiếm nhân viên"
                                    data-bs-toggle="tooltip" data-bs-placement="top">
                                    <i class="bx bx-search"></i>
                                </a>
                                <button type="submit" class="btn btn-secondary btnExport" data-bs-original-title="Xuất dữ liệu" data-bs-toggle="tooltip" data-bs-placement="top">
                                    <i class="bx bx-export"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    <table id="list_employee_table" class="table table-striped table-bordered dataTable no-footer mt-3">

                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- start modal add employee --}}
    <div class="modal fade" id="add_employee_modal" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myExtraLargeModalLabel">Thêm mới tài khoản nhân viên</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('create-employee') }}" id="create_employee_form" method="post">
                        @csrf
                        {{-- first name and last name --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Tên<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="employee_first_name"
                                        name="employee_first_name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Họ<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="employee_last_name"
                                        name="employee_last_name">
                                </div>
                            </div>
                        </div>

                        {{-- username and email --}}
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Username<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="employee_username"
                                        name="employee_username">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Email<span class="required">*</span></label>
                                    <input type="email" class="form-control" id="employee_email" name="employee_email">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Số điện thoại liên hệ<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="employee_phone" name="employee_phone">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Đơn vị thực quản lý<span class="required">*</span></label>
                                    <select name="real_manage_unit" id="real_manage_unit" class="form-select">
                                        <option value=""> -- Chọn -- </option>
                                        <option value="1"> Công ty kinh doanh </option>
                                        <option value="2"> Trung tâm mạng lưới </option>
                                        <option value="3"> Đại lý </option>
                                    </select>
                                    {{-- <input type="text" class="form-control" id="employee_address" name="employee_address"> --}}
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Mật khẩu<span class="required">*</span></label>
                                    <div class="position-relative auth-pass-inputgroup" id="password_group">
                                        <input type="password" class="form-control pe-5 password-input"
                                            autocomplete="new-password" name="password" id="password"
                                            placeholder="Mật khẩu mới">
                                        <button
                                            class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted shadow-none password-addon"
                                            type="button"><i
                                                class="ri-eye-fill align-middle"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Nhập lại mật khẩu<span class="required">*</span></label>
                                    <div class="position-relative auth-pass-inputgroup" id="confirm_password_group">
                                        <input type="password" class="form-control pe-5 password-input"
                                            autocomplete="new-password" name="confirm_password" id="confirm_password"
                                            placeholder="Xác nhận mật khẩu">
                                        <button
                                            class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted shadow-none password-addon"
                                            type="button" ><i
                                                class="ri-eye-fill align-middle"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Kích hoạt tài khoản<span class="required">*</span></label>
                                    <select name="active_account" id="active_account" class="form-select">
                                        <option value="">-- Chọn --</option>
                                        <option value="1">Kích hoạt</option>
                                        <option value="2">Vô hiệu hóa</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Loaị tài khoản<span class="required">*</span></label>
                                    <select name="role_group_id" id="role_group_id" class="form-select">
                                        <option value="">-- Chọn --</option>
                                        @if ($list_role_groups)
                                            @foreach ($list_role_groups as $role_group_item)
                                                <option value="{{ $role_group_item->id }}">
                                                    {{ $role_group_item->role_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 col-xs-12 d-flex justify-content-end">
                                <button class="btn btn-success" type="submit" id="btn_add_single_employee">Thêm
                                    mới</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.end modal add employee -->

    {{-- start modal add employee --}}
    <div class="modal fade" id="update_employee_modal" tabindex="-1" role="dialog"
        aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myExtraLargeModalLabel">Cập nhật tài khoản nhân viên</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('update-employee') }}" id="update_employee_form" method="post">
                        @csrf
                        <input type="hidden" name="employee_id" id="employee_id">
                        {{-- first name and last name --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Họ và tên đệm<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="update_employee_first_name"
                                        name="update_employee_first_name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Tên<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="update_employee_last_name"
                                        name="update_employee_last_name">
                                </div>
                            </div>
                        </div>

                        {{-- username and email --}}
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Username<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="update_employee_username" disabled
                                        name="update_employee_username">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Email<span class="required">*</span></label>
                                    <input type="email" class="form-control" id="update_employee_email"
                                        name="update_employee_email">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Số điện thoại liên hệ<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="update_employee_phone"
                                        name="update_employee_phone">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Đơn vị thực quản lý<span class="required">*</span></label>
                                    <select name="update_real_manage_unit" id="update_real_manage_unit" class="form-select">
                                        <option value=""> -- Chọn -- </option>
                                        <option value="1"> Công ty kinh doanh </option>
                                        <option value="2"> Trung tâm mạng lưới </option>
                                        <option value="3"> Đại lý </option>
                                    </select>
                                    {{-- <input type="text" class="form-control" id="update_employee_address" --}}
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Kích hoạt tài khoản<span class="required">*</span></label>
                                    <select name="update_active_account" id="update_active_account" class="form-select">
                                        <option value="1">Kích hoạt</option>
                                        <option value="2">Vô hiệu hóa</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Loaị tài khoản<span class="required">*</span></label>
                                    <select name="update_role_group_id" id="update_role_group_id" class="form-select">
                                        <option value="">-- Chọn --</option>
                                        @if ($list_role_groups)
                                            @foreach ($list_role_groups as $role_group_item)
                                                <option value="{{ $role_group_item->id }}">
                                                    {{ $role_group_item->role_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 col-xs-12 d-flex justify-content-end">
                                <button class="btn btn-success" type="submit" id="btn_update_single_employee">Cập
                                    nhật</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.end modal update employee -->


    {{-- start modal import excel employee --}}
    <div class="modal fade" id="import_excel_modal" tabindex="-1" role="dialog"
        aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myExtraLargeModalLabel">Import thông nhân viên</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('import-excel-employee') }}" id="import_excel_form" method="post"
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
                                <button class="btn btn-danger d-none" type="button" id="btn_save_excel">Lưu lại</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.end modal import excel employee -->

    <input type="hidden" id="_token" value="{{ csrf_token() }}">
@endsection

@section('scripts')
    <script src="{{ URL::asset($custom_asset . '/assets/libs/bootstrap_table/bootstrap-table.js') }}"></script>
    <script src="{{ URL::asset($custom_asset . '/assets/js/pages_js/list_employee.js') }}"></script>
@endsection
@section('styles')
    <link href="{{ URL::asset($custom_asset . '/assets/libs/bootstrap_table/bootstrap-table.css') }}" rel="stylesheet"
        type="text/css" />
@endsection
