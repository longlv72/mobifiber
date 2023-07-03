@extends('layouts.app')
@section('main_content')
    <div class="row">
        <div class="col-xs-12 col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <span>Danh sách công việc</span>
                    <div class="add -job-group">
                        <a class="btn btn-success btn-sm d-none" data-bs-target="#expot_excel_job_modal" data-bs-toggle="modal">
                            <i class="bx bxs-file-export me-1"></i>
                            Xuất
                        </a>
                        <a class="btn btn-success btn-sm d-none" data-bs-target="#import_excel_job_modal" data-bs-toggle="modal">
                            <i class="bx bx-plus me-1"></i>
                            Thêm mới tệp tin
                        </a>
                        <a data-bs-toggle="modal" data-bs-target="#create_job_modal" data-bs-original-title="Thêm mới 1 đối tác" class="btn btn-primary btn-sm">
                            <i class="bx bx-plus me-1"></i>
                            Thêm mới
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-xs-12 ms-2">
                            <div class="">
                                <label class="form-label">Trạng thái</label>
                                <select name="status_filter" id="status_filter" class="form-select">
                                    <option value="">--Chọn--</option>
                                    <option value="1">Chưa giao cho ai</option>
                                    <option value="2">Đã giao, chưa ai nhận</option>
                                    <option value="3">Đang xử lý</option>
                                    <option value="4">Đã hoàn thành</option>
                                    <option value="6">Đã giao, bị từ chối</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-12 ms-2">
                            <div class="">
                                <label class="form-label">Tìm kiếm công việc</label>
                                <input name="search_value_filter" id="search_value_filter" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-4 d-flex align-items-end">
                            <a class="btn btn-success btnSearch" data-bs-original-title="Tìm kiếm công việc" data-bs-toggle="tooltip" data-bs-placement="top">
                                <i class="bx bx-search"></i>
                            </a>
                        </div>
                    </div>
                    <table id="list_jobs_table"  class="table table-striped table-bordered dataTable no-footer mt-3">

                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- start modal add job --}}
    <div class="modal fade" id="create_job_modal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myExtraLargeModalLabel">Thêm mới công việc</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{route('create-single-job')}}" id="create_job_form" method="post">
                        @csrf
                        {{-- job_type and job_reason --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Loại công việc<span class="required">*</span></label>
                                    <select name="job_type" id="job_type" class="form-select">
                                        <option value="">-- Chọn --</option>
                                        @if ($jobTypeList)
                                            @foreach ($jobTypeList as $job_item)
                                                <option value="{{ $job_item->id }}">{{ $job_item->value_setting }}</option>
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
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- contract and description--}}
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group" id="contract_id_group">
                                    <label class="form-label" id="label_contract_id">Hợp đồng <span class="is_required"></span></label>
                                    <select name="contract_id" id="contract_id" disabled class="form-select">
                                        <option value=""> -- Chọn -- </option>
                                        @if ($listContracts)
                                            @foreach ($listContracts as $contract)
                                                <option value="{{ $contract->id }}" data-customer_id="{{ $contract->customer->id }}">{{ $contract->code . " - " . $contract->customer->firstname }}</option>
                                            @endforeach
                                        @endif
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

                        {{-- customer_id and job_code --}}
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group" id="customer_id_group">
                                    <label class="form-label">Khách hàng<span class="required">*</span></label>
                                    <select name="customer_id" id="customer_id" class="form-select">
                                        <option value="">--Chọn--</option>
                                        @if ($getAllCustomers)
                                            @foreach ($getAllCustomers as $customer)
                                                <option data-customer_code="{{ $customer->code }}" value="{{ $customer->id }}">{{ $customer->last_name . " " . $customer->firstname }} {{ $customer->phone ? '- ' . $customer->phone : '' }} {{ $customer->email ? '- ' . $customer->email : '' }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Mã công việc <span class="required">*</span></label>
                                    <input type="text" readonly class="form-control mt-0" id="job_code" name="job_code">
                                </div>
                            </div>
                            
                        </div>

                        {{-- address of customer and status --}}
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group" id="address_id_group">
                                    <label class="form-label">Địa chỉ khách hàng<span class="required">*</span></label>
                                    <select name="address_id" id="address_id" class="form-select">
                                        <option value=""> -- Chọn -- </option>
                                    </select>
                                </div>
                            </div>

                            {{-- employee_ids --}}
                            <div class="col-md-6">
                                <div class="form-group" id="employee_ids_group">
                                    <label class="form-label">Nhân viên thực hiện<span class="required">*</span></label>
                                    <select name="employee_ids" id="employee_ids" class="form-select" multiple>
                                        @if ($getAllEmployees)
                                            @foreach ($getAllEmployees as $item)
                                                <option value="{{ $item->id }}">{{ $item->lastname }} {{ $item->firstname }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- partner and building --}}
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group" id="partner_id_group">
                                    <label class="form-label">Đối tác<span class="required">*</span></label>
                                    <select name="partner_id" id="partner_id" class="form-select">
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
                                <div class="form-group" id="building_id_group">
                                    <label class="form-label">Tòa nhà<span class="required">*</span></label>
                                    <select name="building_id" id="building_id" class="form-select">
                                        <option value=""> -- Chọn-- </option>
                                        {{-- @if ($getAllBuildings)
                                            @foreach ($getAllBuildings as $building)
                                                <option value="{{ $building->id }}">{{ $building->building_name }} - {{ $building->building_code }}</option>
                                            @endforeach
                                        @endif --}}
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

    {{-- start modal update job --}}
    <div class="modal fade" id="update_job_modal" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myExtraLargeModalLabel">Cập nhật thông tin công việc</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{route('update-single-job')}}" id="update_job_form" method="post">
                        @csrf
                        <input type="hidden" name="job_id" id="job_id">
                        {{-- job_type and reason --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Loại công việc<span class="required">*</span></label>
                                    <select name="update_job_type" id="update_job_type" class="form-select">
                                        <option value="">-- Chọn --</option>
                                        @if ($jobTypeList)
                                            @foreach ($jobTypeList as $job_item)
                                                <option value="{{ $job_item->id }}">{{ $job_item->value_setting }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Lý do tạo công việc<span class="required">*</span></label>
                                    <select name="update_reason" id="update_reason" class="form-select">
                                        <option value="" selected>--Chọn--</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- contract --}}
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group" id="contract_id_group">
                                    <label class="form-label" id="label_update_contract_id">Hợp đồng <span class="is_required"></span></label>
                                    <select name="update_contract_id" id="update_contract_id" class="form-select">
                                        <option value="" selected> -- Chọn -- </option>
                                        @if ($listContracts)
                                            @foreach ($listContracts as $contract)
                                                <option value="{{ $contract->id }}" data-customer_id="{{ $contract->customer->id }}">{{ $contract->code . " - " . $contract->customer->firstname }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- status and description --}}
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Mô tả</label>
                                    <input name="update_description" id="update_description" class="form-control" />
                                </div>
                            </div>
                        
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Trạng thái</label>
                                    <select name="update_job_status" id="update_job_status" class="form-select" >
                                        <option value="">--Chọn--</option>
                                        <option value="1">Chưa giao cho ai</option>
                                        <option value="2">Đã giao, chưa ai nhận</option>
                                        <option value="3">Đang xử lý</option>
                                        <option value="4">Đã hoàn thành</option>
                                        <option value="5">Công việc không khả thi</option>
                                        <option value="6">Đã giao nhưng bị từ chối</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- customer_id and job_code --}}
                        <div class="row mt-3">
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Khách hàng<span class="required">*</span></label>
                                    <select name="update_customer_id" id="update_customer_id" class="form-select">
                                        <option value=""> -- Chọn -- </option>
                                        @if ($getAllCustomers)
                                            @foreach ($getAllCustomers as $customer)
                                                <option data-customer_code="{{ $customer->code }}"  value="{{ $customer->id }}">{{ $customer->last_name . " " . $customer->firstname }} {{ $customer->phone ? '- ' . $customer->phone : '' }} {{ $customer->email ? '- ' . $customer->email : '' }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Mã công việc <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="update_job_code" readonly name="update_job_code">
                                </div>
                            </div>
                        </div>

                        {{-- address_id and employee_ids --}}
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group" id="update_address_id_group">
                                    <label class="form-label">Địa chỉ khách hàng<span class="required">*</span></label>
                                    <select name="update_address_id" id="update_address_id" class="form-select">
                                        <option value=""> -- Chọn -- </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" id="update_employee_ids_group">
                                    <label class="form-label">Nhân viên thực hiện<span class="required">*</span></label>
                                    <select name="update_employee_ids" id="update_employee_ids" class="form-select" multiple aria-placeholder="ok">
                                        @if ($getAllEmployees)
                                            @foreach ($getAllEmployees as $item)
                                                <option value="{{ $item->id }}">{{ $item->lastname }} {{ $item->firstname }} {{ $item->email ? '- ' . $item->email : '' }} {{ $item->phone ? '- ' .$item->phone : '' }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            
                        </div>

                        {{-- partner and building --}}
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group" id="update_partner_id_group">
                                    <label class="form-label">Đối tác<span class="required">*</span></label>
                                    <select name="update_partner_id" id="update_partner_id" class="form-select">
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
                                    <select name="update_building_id" id="update_building_id" class="form-select">
                                        <option value=""> -- Chọn-- </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div id="list_employee_update" class="d-none mt-3">
                            
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 col-xs-12 d-flex justify-content-end">
                                <button class="btn btn-success" type="submit" id="btn_update_single_job">Cập nhật</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.end modal update job -->


    {{-- start modal import excel job --}}
    <div class="modal fade" id="import_excel_job_modal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myExtraLargeModalLabel">Import thông tin đối tác</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{route('import-excel-jobs')}}" id="import_excel_job_form" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Chọn file</label>
                                    <input type="file" name="jobExcel" id="jobExcel" id="" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-xs-12 d-none" id="error_array_in_file_excel_group">
                                <table id="list_job_errors_in_file"  class="table table-striped table-bordered dataTable no-footer mt-3">

                                </table>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 col-xs-12 d-flex justify-content-end">
                                <button class="btn btn-success" type="submit" id="btn_check_file_excel_job">Kiểm tra</button>
                                <button class="btn btn-danger" type="button" id="btn_save_excel_job">Lưu lại</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.end modal import excel job -->
    
    {{-- start modal history job --}}
    <div class="modal fade" id="history_job_modal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title mb-3" id="myExtraLargeModalLabel">Lịch sử công việc</h5>
                    <button type="button" class="btn-close mb-3" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="background: #d7d6d6;">
                    <div class="row mt-4">
                        <div class="col-lg-12">
                            <div>
                                <div class="horizontal-timeline my-3">
                                    <div class="swiper timelineSlider">
                                        <div class="swiper-wrapper" id="time_line_body"></div>
                                        <div class="swiper-button-next"></div>
                                        <div class="swiper-button-prev"></div>
                                    </div>
                                </div>
                                <!--end timeline-->
                            </div>
                        </div>
                        <!--end col-->
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.end modal import excel job -->

    {{-- modal get job's info --}}
    <div class="modal fade" id="job_info_modal" data-bs-backdrop="static" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header ps-2">
                    <h5 class="modal-title" id="myExtraLargeModalLabel">Thông tin công việc</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="info_job_id" id="info_job_id">
                    <div class="job_info_group"></div>
                    <h5 class="mt-2 fs-14">Lịch sử công việc</h5>
                    <div data-simplebar style="height: 508px;" id="job_process_group" class="job_process_group px-3 mx-n3 mb-5">
                    </div>
                    <form class="mt-4 d-none" action="{{route('post-comment')}}" id="post_comment_form" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="comment_job_proccess_id" id="comment_job_proccess_id">
                        <input type="file" class="d-none" accept="application/pdf,image/*" name="file_attach" id="file_attach" />
                        <div class="preview-image-group d-flex justify-content-between d-none my-2">
                            <div class="image_preview d-none">
                                <a href="" id="atag-preview" data-fancybox="group">
                                    <img id="preview" src="" alt="Image preview" style="width: 150px;">
                                </a>
                            </div>
                            <div class="pdf_preview d-none">
                                <a href="" id="a_pdf_preview"></a>
                            </div>
                            <span><i class="bx bx-x-circle bx-xs btnRemoveFileUpload" style="z-index: 1000; cursor: pointer;"></i></span>
                        </div>
                        <div class="row g-0 align-items-end">
                            <div class="col-auto">
                                <button type="button" class="btn bg-soft-dark shadow" id="btnTriggerOpenFile">
                                    <i class="bx bxs-file-image"></i>
                                </button>
                            </div>
                            <div class="col">
                                {{-- <div id="reply_target_group" class="d-none" data-reply_comment_id><span>Đang trả lời </span> <span id="reply_target_user_name"></span> - <span id="btnCancelReply" class="fw-bold" role="button">Huỷ</span></div> --}}
                                <textarea class="form-control bg-light border-light" data-comment_parent_id id="comment_input" rows="1" placeholder="Để lại bình luận" autocomplete="off"></textarea>
                            </div>
                            <!--end col-->
                            <div class="col-auto text-end">
                                <div class="chat-input-links ms-2">
                                    <div class="links-list-item">
                                        <button type="submit" class="btn btn-success chat-send waves-effect waves-light shadow btnLeaveComment">
                                            <i class="ri-send-plane-2-fill align-bottom"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end row-->
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

    <input type="hidden" id="_token" value="{{ csrf_token() }}">
@endsection

@section('scripts')
    <script src="{{URL::asset($custom_asset.'/assets/libs/imask/imask.min.js')}}"></script>
    <script src="{{URL::asset($custom_asset.'/assets/libs/bootstrap_table/bootstrap-table.js')}}"></script>
    <script src="{{URL::asset($custom_asset.'/assets/libs/select2/dist/js/select2.full.min.js')}}"></script>
    <script src="{{URL::asset($custom_asset.'/assets/libs/choices.js/public/assets/scripts/choices.min.js')}}" type="text/javascript"></script>
    <script src="{{URL::asset($custom_asset.'/assets/js/pages/timeline.init.js')}}" type="text/javascript"></script>
    <script src="{{URL::asset($custom_asset.'/assets/libs/fancybox/script/jquery.fancybox.min.js')}}"></script>
    <script src="{{URL::asset($custom_asset.'/assets/js/pages_js/list_jobs.js')}}"></script>

@endsection
@section('styles')
<style>
    .border-left-01005e {
        border-left: 4px solid #01005e;
    }
    
    .border-left-f1af2c {
        border-left: 4px solid #f1af2c;
    }
    
    .border-left-f12c7a {
        border-left: 4px solid #f12c7a;
    }
    
    .border-left-45CB85 {
        border-left: 4px solid #45CB85;
    }
    
    .border-left-6a78f1 {
        border-left: 4px solid #d70000;
    }
    
    .border-left-6a78f1 {
        border-left: 4px solid #05c1aa;
    }

    .container-fluid {
        --vz-gutter-x: 1.5rem;
        --vz-gutter-y: 0;
        width: 100%;
        /* padding-right: calc(var(--vz-gutter-x) * .5);
        padding-left: calc(var(--vz-gutter-x) * .5); */
        padding: 0 !important;
        margin-right: auto;
        margin-left: auto;
    }

    .modal {
        --vz-modal-padding: 0.5rem;
    }
    .choices__list--dropdown {
        padding-left: 10px !important;
    }
    .bg-fff {
        background-color: #fff !important;
    }
</style>
    <link href="{{URL::asset($custom_asset.'/assets/libs/bootstrap_table/bootstrap-table.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{URL::asset($custom_asset.'/assets/libs/select2/dist/css/select2.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{URL::asset($custom_asset.'/assets/libs/fancybox/style/jquery.fancybox.min.css')}}" rel="stylesheet" type="text/css" />
    {{-- <link rel="stylesheet" type="text/css" href="{{URL::asset($custom_asset.'/assets/libs/choices.js/public/assets/styles/choices.min.css')}}" /> --}}
@endsection
