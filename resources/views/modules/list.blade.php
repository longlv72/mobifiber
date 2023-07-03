@extends('layouts.app')
@section('main_content')
    <div class="row">
        <div class="col-xs-12 col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <span>Chức năng</span>
                    <div class="add-partner-group">
                        <a data-bs-toggle="modal" data-bs-target="#add_partner_modal" data-bs-original-title="Thêm mới 1 đối tác" class="btn btn-primary btn-sm">
                            <i class="bx bx-plus me-1"></i>
                            Thêm mới
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table id="list_module_table"  class="table table-striped table-bordered dataTable no-footer mt-3">
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
                            <div class="col-md-12 col-xs-12 d-flex justify-content-end">
                                <button class="btn btn-success" type="submit" id="btn_update_single_partner">Cập nhật</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.end modal update partner -->

    <input type="hidden" id="_token" value="{{ csrf_token() }}">
@endsection

@section('scripts')
    <script src="{{URL::asset($custom_asset.'/assets/libs/bootstrap_table/bootstrap-table.js')}}"></script>
    <script src="{{URL::asset($custom_asset.'/assets/js/pages_js/list_partner.js')}}"></script>
@endsection
@section('styles')
    <link href="{{URL::asset($custom_asset.'/assets/libs/bootstrap_table/bootstrap-table.css')}}" rel="stylesheet" type="text/css" />
@endsection
