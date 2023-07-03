@extends('layouts.engineer')
@section('main_content')
    <div class="row d-flex justify-content-center">
        <div class="col-md-12 col-xs-12">
            <div class="step-arrow-nav mb-2">
                <ul class="nav nav-pills custom-nav nav-justified" role="tablist">
                    <li class="nav-item" role="presentation" id="tab_waiting_jobs_list">
                        <button class="nav-link active" id="steparrow-gen-info-tab" data-bs-toggle="pill" data-bs-target="#steparrow-gen-info" type="button" role="tab" aria-controls="steparrow-gen-info" aria-selected="true" data-position="0">
                            <div>
                                Chờ nhận
                                <span class="position-absolute ms-2 fs-10 translate-middle badge rounded-pill bg-danger" id="waiting_receive_job_number">0</span>
                            </div>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation" id="tab_processing_jobs_list">
                        <button class="nav-link" id="steparrow-description-info-tab" data-bs-toggle="pill" data-bs-target="#steparrow-description-info" type="button" role="tab" aria-controls="steparrow-description-info" aria-selected="false" data-position="1" tabindex="-1">
                            <div>
                                Đang xử lý
                                <span class="position-absolute ms-2 fs-10 translate-middle badge rounded-pill bg-info" id="processing_job_number">0</span>
                            </div>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation"  id="tab_completed_jobs_list">
                        <button class="nav-link" id="pills-experience-tab" data-bs-toggle="pill" data-bs-target="#pills-experience" type="button" role="tab" aria-controls="pills-experience" aria-selected="false" data-position="2" tabindex="-1">
                            <div>
                                Đã xong hoặc đã từ chối
                                <span class="position-absolute ms-2 fs-10 translate-middle badge rounded-pill bg-success" id="completed_job_number">0</span>
                            </div>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-md-12 col-xs-12">
            <div class="tab-content">
                <div class="tab-pane fade active show" id="steparrow-gen-info" role="tabpanel" aria-labelledby="steparrow-gen-info-tab">
                    <div id="list_waiting_receive_jobs">
                        
                    </div>
                </div>
                <!-- end tab pane -->
        
                <div class="tab-pane fade" id="steparrow-description-info" role="tabpanel" aria-labelledby="steparrow-description-info-tab">
                    <div id="list_processing_jobs">
                        
                    </div>
                </div>
                <!-- end tab pane -->
        
                <div class="tab-pane fade" id="pills-experience" role="tabpanel" aria-labelledby="#pills-experience-tab">
                    <div class="input-group mb-3">
                        <label class="input-group-text" for="inputGroupSelect01">Trạng thái công việc</label>
                        <select class="form-select" id="filter_job_status" style="background-color: var(--vz-input-group-addon-bg);">
                            <option selected value="4">Đã xong</option>
                            <option value="6">Đã từ chối</option>
                        </select>
                    </div>
                    <div id="list_completed_jobs">
                        
                    </div>
                </div>
                <!-- end tab pane -->
            </div>
        </div>
    </div>

    <div class="modal fade" id="forward_step_modal" data-bs-backdrop="static" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header ps-2">
                    <h5 class="modal-title" id="myExtraLargeModalLabel">Xác nhận làm công việc</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('process-receive-job') }}" id="receive_job_form" method="post">
                        @csrf
                        <input type="hidden" name="job_id" id="job_id">
                        <div class="row mt-2 d-flex">
                            <div class="col-4 col-xs-12">Tên công việc</div>
                            <div class="col-8" id="job_name"></div>
                        </div>  
                        <div class="row mt-2 d-flex justify-content-center">   
                            <div class="col-md-12 col-xs-12">
                                <div class="form-group">
                                    <label class="form-label mb-1">Thêm người thực hiện</label>
                                    <select name="additional_assign" id="additional_assign" data-placeholder="--Chọn--" class="form-select" multiple="">
                                        <option value="">--Chọn--</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2 d-flex justify-content-center">
                            <div class="col-md-12 col-xs-12">
                                <div class="form-group">
                                    <label class="form-label mb-1">Ghi chú</label>
                                    <textarea type="text" name="job_note" id="job_note" id="" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 col-xs-12 d-flex justify-content-end">
                                <button class="btn btn-success" type="submit" id="btn_process_receive_job">Xác nhận</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

    {{-- modal reject job --}}
    <div class="modal fade" id="reject_job_modal" data-bs-backdrop="static" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header ps-2">
                    <h5 class="modal-title" id="myExtraLargeModalLabel">Từ chối công việc</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('reject-job') }}" id="reject_job_form" method="post">
                        @csrf
                        <input type="hidden" name="job_reject_id" id="job_reject_id">
                        <div class="row mt-2 d-flex">
                            <div class="col-md-12 col-xs-12">
                                <div class="form-group">
                                    <label class="form-label mb-1">Lý do</label>
                                    <input type="text" name="reason_reject" id="reason_reject" id="" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 col-xs-12 d-flex justify-content-end">
                                <button class="btn btn-success" type="submit" id="btn_reject_job">Xác nhận</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    
    {{-- modal out job --}}
    <div class="modal fade" id="out_job_modal" data-bs-backdrop="static" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header ps-2">
                    <h5 class="modal-title" id="myExtraLargeModalLabel">Bỏ công việc</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('out-job') }}" id="out_job_form" method="post">
                        @csrf
                        <input type="hidden" name="out_job_id" id="out_job_id">
                        <div class="row mt-2 d-flex">
                            <div class="col-md-12 col-xs-12">
                                <div class="form-group">
                                    <label class="form-label mb-1">Lý do</label>
                                    <input type="text" name="reason_out_job" id="reason_out_job" id="" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 col-xs-12 d-flex justify-content-end">
                                <button class="btn btn-success" type="submit" id="btn_out_job">Xác nhận</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    
    {{-- modal out job --}}
    <div class="modal fade" id="mark_completed_job_modal" data-bs-backdrop="static" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header ps-2">
                    <h5 class="modal-title" id="myExtraLargeModalLabel">Hoàn thành công việc</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('mark-completed-job') }}" id="mark_completed_job_form" method="post">
                        @csrf
                        <input type="hidden" name="mark_completed_job_id" id="mark_completed_job_id">
                        <div class="row mt-2 d-flex">
                            <div class="col-md-12 col-xs-12">
                                <div class="form-group">
                                    <label class="form-label mb-1">Ghi chú</label>
                                    <input type="text" name="note_mark_completed" id="note_mark_completed" id="" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 col-xs-12 d-flex justify-content-end">
                                <button class="btn btn-success" type="submit" id="btn_mark_completed_job">Xác nhận</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    
    {{-- modal add engineer employee --}}
    <div class="modal fade" id="add_engineer_modal" data-bs-backdrop="static" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header ps-2">
                    <h5 class="modal-title" id="myExtraLargeModalLabel">Bổ sung nhân viên thực hiện</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('add-engineer-job') }}" id="add_engineer_in_processing_step_form" method="post">
                        @csrf
                        <input type="hidden" name="add_engineer_for_job_id" id="add_engineer_for_job_id">
                        <div class="row mt-2 d-flex">
                            <div class="col-md-12 col-xs-12">
                                <div class="form-group">
                                    <label class="form-label mb-1">Lý do</label>
                                    <input type="text" name="reaseon_add_engineer_employee" id="reaseon_add_engineer_employee" id="" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2 d-flex">
                            <div class="col-md-12 col-xs-12">
                                <div class="form-group">
                                    <label class="form-label mb-1">Chọn nhân viên</label>
                                    <select name="add_engineer_employee_select" id="add_engineer_employee_select" data-placeholder="--Chọn--" class="form-select" multiple="">
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-12 col-xs-12 d-flex justify-content-end">
                                <button class="btn btn-success" type="submit" id="btn_add_employee_in_processing_job">Thêm</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    
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

    <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="getUnassignEmployeeByJob" id="getUnassignEmployeeByJob" value="{{ route('get-unassign-employee-by-job') }}">
@endsection

@section('styles')
    <style>
        .input-group-text { 
            border: 1px solid #e1e1e1;
        }
        .form-select {
            border: 1px solid #e5e5e5;
        }
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
    <link rel="stylesheet" type="text/css" href="{{URL::asset($custom_asset.'/assets/libs/choices.js/public/assets/styles/choices.min.css')}}" />
    <link href="{{URL::asset($custom_asset.'/assets/libs/select2/dist/css/select2.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{URL::asset($custom_asset.'/assets/libs/fancybox/style/jquery.fancybox.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('scripts')
    <script src="{{URL::asset($custom_asset.'/assets/libs/choices.js/public/assets/scripts/choices.min.js')}}" type="text/javascript"></script>
    <script src="{{URL::asset($custom_asset.'/assets/libs/select2/dist/js/select2.full.min.js')}}"></script>    
    <script src="{{URL::asset($custom_asset.'/assets/libs/fancybox/script/jquery.fancybox.min.js')}}"></script>    
    <script src="{{URL::asset($custom_asset.'/assets/js/pages_js/list_job_of_engineer.js')}}"></script>
@endsection