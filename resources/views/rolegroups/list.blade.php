@extends('layouts.app')
@section('main_content')
    <div class="row">
        <div class="col-xs-12 col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <span>Danh sách nhóm quyền</span>
                    <div class="add-device-group">
                        <a href="javascript:void(0)" id="btnCreateRoleGroup" data-bs-original-title="Thêm mới nhóm quyền"
                            class="btn btn-primary btn-sm">
                            <i class="bx bx-plus me-1"></i>
                            Thêm mới
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table id="list_rolegroup_table" class="table table-striped table-bordered dataTable no-footer mt-3">

                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- start modal add device --}}
    <div class="modal fade" id="create_rolegroup_modal" tabindex="-1" role="dialog"
        aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myExtraLargeModalLabel">Thêm mới nhóm quyền</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- device_code and sign_date --}}
                    <div class="row">
                        <div class="form-group">
                            <label class="form-label">Tên nhóm quyền <span class="required">*</span></label>
                            <input type="text" class="form-control" id="role_name" name="role_name">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12 col-xs-12 d-flex justify-content-end">
                            <button class="btn btn-success" type="button" id="btn_create_single_rolegroup">Thêm
                                mới</button>
                        </div>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.end modal add device -->

    {{-- start modal add device --}}
    <div class="modal fade" id="update_rolegroup_modal" tabindex="-1" role="dialog"
        aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myExtraLargeModalLabel">Cập nhật nhóm quyền</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="rolegroup_id" id="rolegroup_id">
                    {{-- device_code and sign_date --}}
                    <div class="row">
                        <div class="form-group">
                            <label class="form-label">Tên nhóm quyền <span class="required">*</span></label>
                            <input type="text" class="form-control" id="update_role_name" name="update_role_name">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12 col-xs-12 d-flex justify-content-end">
                            <button class="btn btn-success" type="button" id="btn_update_single_rolename">Cập
                                nhật</button>
                        </div>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.end modal update device -->

    <input type="hidden" id="_token" value="{{ csrf_token() }}">
@endsection

@section('scripts')
    <script src="{{ URL::asset($custom_asset . '/assets/libs/bootstrap_table/bootstrap-table.js') }}"></script>
    <script src="{{ URL::asset($custom_asset . '/assets/js/pages_js/list_role_group.js') }}"></script>
@endsection
@section('styles')
    <link href="{{ URL::asset($custom_asset . '/assets/libs/bootstrap_table/bootstrap-table.css') }}" rel="stylesheet"
        type="text/css" />
@endsection
