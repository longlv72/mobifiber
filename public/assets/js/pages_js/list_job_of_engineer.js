var list_color = ['bg-success', 'bg-primary', 'bg-dark', 'text-danger', 'text-warning'];
var list_icon = ['ri-global-line', 'ri-user-location-line', 'ri-pen-nib-line', 'ri-compasses-2-line', 'ri-scissors-cut-fill', 'ri-pencil-ruler-2-fill', 'ri-artboard-line', 'ri-paint-line'];
var list_border_color = ['border-left-01005e', 'border-left-f1af2c', 'border-left-45CB85', 'border-left-f12c7a', 'border-left-6a78f1', 'border-left-6a78f1'];

$(function() {
    window.list_engineers = {
        init: function() {
            list_engineers.GetModelForwardStep();
            list_engineers.SetChoice();
            list_engineers.GetWaitingReceiveJobList();
            list_engineers.ProcessSingleJob();
            list_engineers.ClearModal();
            list_engineers.RefreshWaitingJobsList();    
            list_engineers.GetProcessingJobsList();
            // list_engineers.GetCompletedJobsList();
            list_engineers.SetDatetimepicker();
            list_engineers.RejectJobModal();
            list_engineers.RejectJob();
            list_engineers.AddEngineerEmployee();
            list_engineers.AddEngineerEmployeeModal();
            list_engineers.OutJobModal();
            list_engineers.OutJob();
            list_engineers.MarkCompletedJobModal();
            list_engineers.MarkCompletedJob();
            list_engineers.GetInfoJobModal();
            list_engineers.CommentAction();
            list_engineers.CommentReply();
            list_engineers.CancelReply();
            list_engineers.TriggerOpenFileUpload();
            list_engineers.PreviewImageInput();
            list_engineers.RemoveFileUpload();
            list_engineers.GetCommentOfProcessItem();
            list_engineers.SearchCompleteOrRejectJob();
        },
        DeleteComment: function() {
            $(document).on('click', '.btnDeleteComment d-none', function() {
                var id = $(this).data('id');
                var parent_id = $(this).data('parent_id');
                
                $.ajax({
                    url: '/engineer/delete-comment',
                    type: 'delete',
                    data: {
                        "_token": $('#_token').val(),
                        "id": id,
                        "parent_id": parent_id
                    },
                    success: function(res) {
                        
                    }
                });
            });
        },
        ClearPreviewImage: function() {
            $('#atag-preview').attr('href', "")
            $('#preview').attr('src', "");
            $('.preview-image-group').addClass('d-none');
            $('.pdf_preview').addClass('d-none');
            $('.image_preview').addClass('d-none');
        },
        RemoveFileUpload: function() {
            $(document).on("click", '.btnRemoveFileUpload', function() {
                list_engineers.ClearPreviewImage();
                $('#file_attach').val('');
            });
        },
        ImportImage: function(input) {
            let fileReference = input.files && input.files[0];
            var type = fileReference.type;
            if (fileReference && type == 'application/pdf') {
                var reader = new FileReader();

                reader.onload = (event) => {
                    $('#a_pdf_preview').attr('href', event.target.result).html(fileReference.name);
                    // $('#preview').attr('src', event.target.result);
                    $('.preview-image-group').removeClass('d-none');
                    $('.pdf_preview').removeClass('d-none');
                }

                reader.readAsDataURL(fileReference);
                return ;
            } 
            //  
            if (fileReference) {
                var reader = new FileReader();

                reader.onload = (event) => {
                    $('#atag-preview').attr('href', event.target.result)
                    $('#preview').attr('src', event.target.result);
                    $('.preview-image-group').removeClass('d-none');
                    $('.image_preview').removeClass('d-none');
                }

                reader.readAsDataURL(fileReference);
                return ;
            }
        },
        PreviewImageInput: function() {
            $(document).on('change', '#file_attach', function() {
                var this_file = this.files.length;
                if ( this_file <= 0 ) return ;
                list_engineers.ImportImage(this);
            });
        },
        FancyBoxImagePreview: function() {
            $('[data-fancybox]').fancybox({
                // Options will go here
                buttons: [
                    'close',
                    'delete'
                ],
                btnTpl: {
                    //and this is where I defined it
                    delete: '<a download data-fancybox-delete class="fancybox-button fancybox-button--delete" title="Delete" href="#">' +
                        '<i class="fas fa-trash-alt"></i>' +
                        "</a>"
                },
                wheel: false,
                transitionEffect: "slide",
                // thumbs          : false,
                // hash            : false,
                loop: true,
                // keyboard        : true,
                toolbar: false,
                // animationEffect : false,
                // arrows          : true,
                clickContent: false
            });
        },
        TriggerOpenFileUpload: function() {
            $(document).on('click', '#btnTriggerOpenFile', function() {
                debugger
                $('#file_attach').trigger('click');
            });
        },
        CancelReply: function() {
            $(document).on('click', '#btnCancelReply', function() {
                $('#reply_target_user_name').html("");
                $('#reply_target_group').addClass('d-none').data('reply_comment_id', "");
                // $('#comment_input').focus();
            });
        },
        CommentReply: function() {
            $(document).on('click', '.btnReply', function() {
                $('#comment_input').focus();
                return ;
            });
        },
        GetCommentOfJobProcessData: function(job_proccess_id) {
            if ( ! job_proccess_id ) return ;

            $.ajax({
                url: '/engineer/get-comment-data',
                type: 'get',
                data: {
                    "job_proccess_id": job_proccess_id,
                    "_token": $('#_token').val()
                },
                success: function(res) {
                    list_engineers.RenderComment(res.data_comments, job_proccess_id);
                }
            });
        },
        RenderComment: function(data_comments, job_proccess_id) {
            if ( ! data_comments || data_comments.length <= 0) {
                $('#comment_group_id' + job_proccess_id).html("").html("Chưa có bình luận nào cho trạng thái này");
                return ;
            }

            var comment_list = ``;
            
            $.each(data_comments, function(key, comment_item) {

                var color_comment_item = list_color[Math.floor(Math.random()*list_color.length)];
                var file_name_item_info = '';
                if (comment_item.pathinfo && comment_item.pathinfo.extension == 'pdf') {
                    file_name_item_info = `<a href="${comment_item.url_path}" target="blank" download>Tải tài liệu</a>`;
                } else if (comment_item.pathinfo && comment_item.pathinfo.extension != 'pdf') {
                    file_name_item_info = `<div>${`<a href="${comment_item.url_path}" data-fancybox="group"><img src="${comment_item.url_path}" style="width: 150px;"/></a>`}</div>`
                }
                comment_list += `<div class="d-flex mt-2">
                    <div class="flex-shrink-0">
                        <span class="rounded-circle bg-secondary d-inline-block d-flex justify-content-center align-items-center text-light ${color_comment_item}" style="width: 25px; height: 25px;">${comment_item.created_by.username.charAt(0).toUpperCase()}</span>
                    </div>
                    <div class="flex-grow-1 ms-2 comment-parent">  
                        <h5 class="fs-14 mb-1"><a class="text-body">${comment_item.created_by.username}</a></h5>
                        ${comment_item.content ? `<p class="mb-0"><span class="text-dark mb-1 ps-2 pe-2 py-1 bg-soft-primary rounded d-inline-block">${comment_item.content}</span></p>` : ``}
                        ${comment_item.url_path ? `<div>${file_name_item_info}</div>` : ``}
                        <small class="text-muted fs-10 me-2">${moment(comment_item.created_at).format('YYYY-MM-DD HH:mm')}</small><a href="javascript: void(0);" class="badge text-muted bg-light btnReply fs-13" data-user_name="${comment_item.created_by.username}" data-parent_id="${comment_item.id}" data-job_proccess_id="${job_proccess_id}"><i class="mdi mdi-reply"></i> Phản hồi</a>
                        <a href="javascript: void(0);" class="badge text-muted bg-light btnDeleteComment d-none" data-parent_id="${comment_item.parent_id}" data-user_name="${comment_item.created_by.username}" data-parent_id="40" data-id="${comment_item.id}"><i class=""></i>Xóa</a>
                    </div>
                </div>`;
            });
            $('#comment_group_id' + job_proccess_id).html("").html(comment_list);

        },
        CommentAction: function() {
            $('#post_comment_form').validate({
                rules: {
                    file_attach: {
                        extension: "jpg|jepg|png|pdf"
                    },
                },
                messages: {
                    file_attach: "File đính kèm chỉ bao gồm file ảnh và pdf",
                },
                onkeyup: false,
                onblur: true,
                onfocusout: false,
                invalidHandler: function(form, validator) {
                    var errors = validator.numberOfInvalids();
                    if (errors) {
                        validator.errorList[0].element.focus();
                    }
                },
                submitHandler: function(form, event) {
                    event.preventDefault();
                    var file_upload = $('#file_attach')[0].files[0];
                    var comment_content = $('#comment_input').val();
                    if (! comment_content && !file_upload) {
                        return ;
                    };
                    var data = new FormData();
                    data.append('comment_content', comment_content || "");
                    data.append('job_proccess_id', $('#comment_job_proccess_id').val() || "");
                    // data.append('parent_id', $('#reply_target_group').data('reply_comment_id'));
                    data.append('file_attach', file_upload); 
                    data.append('_token', $('#_token').val());

                    $.ajax({
                        url: form.action,
                        processData: false,
                        contentType: false,
                        type: form.method,
                        data: data,
                        beforeSend: function() {
                            var html = '<i class="bx bx-loader-circle loading"></i> đang lưu';
                            $('.btnLeaveComment').html("").html(html).attr("disabled", true);
                        },
                        success: function(response) {
                            var message = response.message || "";
                            var title = "Lỗi";
                            var type = "error";
                            $('.btnLeaveComment').html("").html(`<i class="ri-send-plane-2-fill align-bottom"></i>`).attr('disabled', false);
                            if (response.success) {

                                // handle preview image 
                                list_engineers.ClearPreviewImage();
                                
                                list_engineers.RenderComment(response.data_comments, response.job_proccess_id);
                                $('#comment_input').val('');
                                // $('#comment_number').text(`(${response.number_comments || 0})`);
                                // $('#comment_group').find('.simplebar-content').html('').html(html_list_comment || `<div class="d-flex justify-content-center mt-4">Hiện chưa có comment nào</div>`);
                            }
                        },
                        error: function(request, status, error) {
                            $('.btnLeaveComment').html("").html(`<i class="ri-send-plane-2-fill align-bottom"></i>`).attr('disabled', false);
                            toastr.error("Lỗi server", "Lỗi");
                        }
                    });
                }
            });
        },
        TriggerCancelReply: function() {
            $('#btnCancelReply').trigger('click');
        },
        GetInfoJobModal: function() {
            $(document).on('click', '.btnGetInfoJob', function() {
                var job_id = $(this).data('id');
                $('#info_job_id').val(job_id);
                $('#comment_job_id').val(job_id);
                // list_engineers.GetCommentData(job_id);
                
                list_engineers.GetJobProcessData(job_id);
                $('#job_info_modal').modal('show');
            });
        },
        HideCommentForm: function() {
            $('#comment_job_proccess_id').val("");
            $('#post_comment_form').addClass('d-none');
        },
        GetCommentOfProcessItem: function() {
            $(document).on('click', '.job_process_item', function() {
                var job_process_id = $(this).data('id');
                var is_show_collappse = $(this).attr("aria-expanded") == "true";
                if (is_show_collappse) {
                    $('#comment_job_proccess_id').val(job_process_id);
                    $('#post_comment_form').removeClass('d-none');
                } else {
                    list_engineers.HideCommentForm();
                }
                if ( ! job_process_id || is_show_collappse == false ) return ;

                list_engineers.GetCommentOfJobProcessData(job_process_id);
            });
        },
        GetJobProcessData: function(id) {
            var job_id = id;
            if (! job_id) {
                return;
            }
            $.ajax({
                url: '/engineer/get-job-process-data',
                type: 'get',
                data: {
                    "job_id": job_id,
                    "_token": $('#_token').val()
                },
                success: function(res) {
                    if (res.success) {
                        var html_list_job_processes = '';
                        
                        $.each(res.list_job_processes, function(key, item) {        
                            var icon_item = list_icon[Math.floor(Math.random()*list_icon.length)];
                            var border_item = list_border_color[Math.floor(Math.random()*list_border_color.length)];

                            html_list_job_processes += `
                                <div class="accordion-item shadow">
                                    <div class="accordion-header ${border_item}" id="accordionwithiconExample1">
                                        <div class="accordion-button job_process_item pb-1" data-id="${item.id}" type="button" data-bs-toggle="collapse" data-bs-target="#job_process_id${item.id}" aria-expanded="true" aria-controls="job_process_id${item.id}">
                                            <div class="mb-0 pb-0">
                                                <small>${moment(item.created_at).format('HH:mm DD-MM-YYYY')}</small>
                                                <br/>
                                                <p class="mt-2">${item.action}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="job_process_id${item.id}" class="accordion-collapse collapse" aria-labelledby="accordionwithiconExample1" data-bs-parent="#accordionWithicon">
                                        <div class="accordion-body comment-body" id="comment_group_id${item.id}" data-id="${item.id}">
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        var html = `
                            <div class="accordion custom-accordionwithicon mb-5" id="accordionWithicon">
                                ${html_list_job_processes}
                            </div>
                        `;
                        $('#job_process_group').find('.simplebar-content').html('').html(html || `<div class="d-flex justify-content-center mt-4">Lịch sử công việc trống</div>`);

                    } else {
                        // toastr.error(message, title);
                    }
                }
            });
        },
        MarkCompletedJob: function() {
            $('#mark_completed_job_form').validate({
                rules: {
                    note_mark_completed: {
                        required: true
                    }
                },
                messages: {
                    note_mark_completed: {
                        required: "Thiếu ghi chú"
                    }
                },
                onkeyup: false,
                onblur: true,
                onfocusout: false,
                invalidHandler: function(form, validator) {
                    var errors = validator.numberOfInvalids();
                    if (errors) {
                        validator.errorList[0].element.focus();
                    }
                },
                submitHandler: function(form, event) {
                    event.preventDefault();
                    var data = new FormData();
                    data.append('note', $('#note_mark_completed').val() || "");
                    data.append('job_id', $('#mark_completed_job_id').val() || "");
                    
                    data.append('_token', $('#_token').val());

                    $.ajax({
                        url: form.action,
                        processData: false,
                        contentType: false,
                        type: form.method,
                        data: data,
                        beforeSend: function() {
                            var html = '<i class="bx bx-loader-circle loading"></i> đang lưu';
                            $('#btn_mark_completed_job').html("").html(html).attr("disabled", true);
                        },
                        success: function(response) {
                            var message = response.message || "";
                            var title = "Lỗi";
                            var type = "error";
                            $('#btn_mark_completed_job').html("").html("Xác nhận").attr('disabled', false);
                            if (response.success) {
                                type = "success";
                                title = "Thành công";
                                toastr.success(message, title, {"positionClass": "toast-top-center"});
                                $('#mark_completed_job_modal').modal('hide');
                                var processing_job_number = parseInt($('#processing_job_number').text());
                                $('#processing_job_number').text(parseInt(processing_job_number > 1 ? (processing_job_number - 1) : 0) );
                                var job_id = response[0].job_id;
                                var job_item_id = '#job_item' + job_id;
                                $(job_item_id).remove();

                                $('#completed_job_number').text(parseInt($('#completed_job_number').text()) + 1);
                            } else {
                                toastr.error(message, title);
                            }
                        },
                        error: function(request, status, error) {
                            $('#btn_mark_completed_job').html("").html("Xác nhận").attr('disabled', false);
                            toastr.error("Lỗi server", "Lỗi", {"positionClass": "toast-top-center"});
                        }
                    });
                }
            });
        },
        MarkCompletedJobModal: function() {
            $(document).on('click', '.btn_comfirm_completed_job', function(){
                var job_id = $(this).data('id');
                $('#mark_completed_job_id').val(job_id);
                $('#mark_completed_job_modal').modal('show');
            });
        },
        OutJobModal: function() {
            $(document).on('click', '.btnOutJob', function() {
                var job_id = $(this).data('id');

                $('#out_job_id').val(job_id);

                $('#out_job_modal').modal('show');
            });
        },
        OutJob: function() {
            $('#out_job_form').validate({
                rules: {
                    reaseon_out_job: {
                        required: true
                    }
                },
                messages: {
                    reaseon_out_job: {
                        required: "Thiếu lý do bỏ công việc"
                    }
                },
                onkeyup: false,
                onblur: true,
                onfocusout: false,
                invalidHandler: function(form, validator) {
                    var errors = validator.numberOfInvalids();
                    if (errors) {
                        validator.errorList[0].element.focus();
                    }
                },
                submitHandler: function(form, event) {
                    event.preventDefault();
                    var data = new FormData();
                    data.append('reason', $('#reason_out_job').val() || "");
                    data.append('job_id', $('#out_job_id').val() || "");
                    
                    data.append('_token', $('#_token').val());

                    $.ajax({
                        url: form.action,
                        processData: false,
                        contentType: false,
                        type: form.method,
                        data: data,
                        beforeSend: function() {
                            var html = '<i class="bx bx-loader-circle loading"></i> đang lưu';
                            $('#btn_out_job').html("").html(html).attr("disabled", true);
                        },
                        success: function(response) {
                            var message = response.message || "";
                            var title = "Lỗi";
                            var type = "error";
                            $('#btn_out_job').html("").html("Xác nhận").attr('disabled', false);
                            if (response.success) {
                                type = "success";
                                title = "Thành công";
                                toastr.success(message, title, {"positionClass": "toast-top-center"});
                                $('#out_job_modal').modal('hide');
                                var processing_job_number = parseInt($('#processing_job_number').text());
                                $('#processing_job_number').text(parseInt(processing_job_number > 1 ? (processing_job_number - 1) : 0) );
                                var job_id = response[0].job_id;
                                var job_item_id = '#job_item' + job_id;
                                $(job_item_id).remove();
                            } else {
                                toastr.error(message, title);
                            }

                        },
                        error: function(request, status, error) {
                            $('#btn_out_job').html("").html("Xác nhận").attr('disabled', false);
                            toastr.error("Lỗi server", "Lỗi", {"positionClass": "toast-top-center"});
                        }
                    });
                }
            });
        },
        SearchCompleteOrRejectJob: function() {
            $(document).on('change', '#filter_job_status', function() {
                list_engineers.GetCompletedJobsList();
            });
            $(document).on('click', '#tab_completed_jobs_list', function() { 
                list_engineers.GetCompletedJobsList();
            });
        },
        GetCompletedJobsList: function() {
            $.ajax({
                url: '/engineer/get-completed-jobs-list',
                data: {
                    "status": $('#filter_job_status').val()
                },
                type: 'get',
                processData: true,
                success: function(res) {
                    var html_list_job = '';
                    if (res[0].getCompletedJobsList) {
                        $.each(res[0].getCompletedJobsList, function(key, item) {
                            var ribbon_title = item.status == 4 ? "Đã xong" : "Đã từ chối";
                            var ribbon_flag = item.status == 4 ? "success" : "danger";
                            html_list_job += `<div class="card ribbon-box right">
                                <div class="card-body pb-2 pe-2">
                                    <div class="ribbon ribbon-${ribbon_flag} ribbon-shape pt-1 pb-1" style="font-weight:300;">${ribbon_title}</div>
                                    <h5 class="fs-14 text-start"><a href="">${item.reason_job.value_setting}</a></h5>
                                    <div class="ribbon-content text-muted mt-3 job_item my-1 bg-fff rounded d-flex justify-content-between mb-0" id="job_item${item.id}">
                                        <div class="job_item_info">
                                            <div class="job_item_status">
                                                <a href="">Ngày bắt đầu: ${moment(item.created_at, 'YYYY-MM-DD').format('DD-MM-YYYY')}</a>
                                            </div>
                                            <div class="job_item_status">
                                            <a href="">Địa điểm: ${item.building ? (item.building.building_name + ' ' + item.building.address) : (item.address ? item.address.address : ``)}</a>
                                            </div>
                                        </div>
                                        <div class="job_item_action d-flex align-items-end">
                                            <button class="btn btn-sm btn-info me-1 btnGetInfoJob"  data-id="${item.id}" data-job_name="${item.job_name}" data-description="${item.descriptions}" data-status="${item.status}">
                                                <i class="mdi mdi-information-variant"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                        });
                        `
                        <div class="card ribbon-box border mb-lg-0">
                            <div class="card-body text-muted">
                                <div class="ribbon-two ribbon-two-success"><span>Success</span></div>
                                <p class="mb-0">Quisque nec turpis at urna dictum luctus. Suspendisse convallis dignissim eros at volutpat. In egestas mattis dui. Aliquam mattis dictum aliquet. Nulla sapien mauris, eleifend et sem ac, commodo dapibus odio. Vivamus pretium nec odio cursus.</p>
                            </div>
                        </div>`
                    }
                    $('#list_completed_jobs').html('').html(html_list_job || `<div class="d-flex justify-content-center mt-4">Không có công việc nào</div>`);
                }
            })
        },
        AddEngineerEmployeeModal: function() {
            $(document).on('click', '.btnAddEngineerEmployee', function() {
                var id = $(this).data('id');
                var _token = $('#_token').val();

                $.ajax({
                    url: '/engineer/get-unassign-employee-by-job',
                    type: 'get',
                    data: {
                        "job_id": id,
                        "_token": _token
                    },
                    success: function(res) {
                        if (add_engineer_in_processing_step_choice) {
                            add_engineer_in_processing_step_choice.setChoices([], 'value', 'label', false);
                            add_engineer_in_processing_step_choice.destroy();
                        }
                        $('#add_engineer_employee_select').html('')
                        $.each(res[0].listEmployee, function(index, option) {
                            $('#add_engineer_employee_select').append($('<option>', {
                                value: option.id,
                                text: option.username
                            }));
                        });
            
                        add_engineer_in_processing_step_choice = new Choices('#add_engineer_employee_select', {
                            removeItemButton: true,
                            placeholder: true,
                            placeholderValue: "--Chọn--",
                        });
                        $('#add_engineer_for_job_id').val(id);
                        $('#add_engineer_modal').modal('show');
                    }
                })
            });
        },
        AddEngineerEmployee: function() {
            $('#add_engineer_in_processing_step_form').validate({
                rules: {
                    reaseon_add_engineer_employee: {
                        required: true
                    },
                    add_engineer_employee_select: {
                        required: true
                    }
                },
                messages: {
                    reaseon_add_engineer_employee: {
                        required: "Thiếu lý do thêm nhân viên"
                    },
                    add_engineer_employee_select: {
                        required: "Chọn nhân viên để hoàn thành công việc này"
                    },
                },
                onkeyup: false,
                onblur: true,
                onfocusout: false,
                invalidHandler: function(form, validator) {
                    var errors = validator.numberOfInvalids();
                    if (errors) {
                        validator.errorList[0].element.focus();
                    }
                },
                submitHandler: function(form, event) {
                    event.preventDefault();
                    var data = new FormData();
                    data.append('job_id', $('#add_engineer_for_job_id').val() || "");
                    data.append('reason', $('#reaseon_add_engineer_employee').val() || "");
                    data.append('employee_id_array', $('#add_engineer_employee_select').val() || "");
                    
                    data.append('_token', $('#_token').val());

                    $.ajax({
                        url: form.action,
                        processData: false,
                        contentType: false,
                        type: form.method,
                        data: data,
                        beforeSend: function() {
                            var html = '<i class="bx bx-loader-circle loading"></i> đang lưu';
                            $('#btn_add_employee_in_processing_job').html("").html(html).attr("disabled", true);
                        },
                        success: function(response) {
                            var message = response.message || "";
                            var title = "Lỗi";
                            var type = "error";
                            $('#btn_add_employee_in_processing_job').html("").html("Thêm").attr('disabled', false);
                            if (response.success) {
                                type = "success";
                                title = "Thành công";
                                toastr.success(message, title, {"positionClass": "toast-top-center"});
                                $('#add_engineer_modal').modal('hide');
                                list_engineers.GetWaitingReceiveJobList();
                            } else {
                                toastr.error(message, title);
                            }
                        },
                        error: function(request, status, error) {
                            $('#btn_add_employee_in_processing_job').html("").html("Thêm").attr('disabled', false);
                            toastr.error("Lỗi server", "Lỗi", {"positionClass": "toast-top-center"});
                        }
                    });
                }
            });            
        },
        RejectJobModal: function() {
            $(document).on('click', '.btnRejectJob', function() {
                var id = $(this).data('id');
                $('#job_reject_id').val(id);
                $('#reject_job_modal').modal('show');
            });
        },
        RejectJob: function() {
            $('#reject_job_form').validate({
                rules: {
                    reason_reject: {
                        required: true
                    }
                },
                messages: {
                    reason_reject: {
                        required: "Thiếu lý do từ chối công việc"
                    }
                },
                onkeyup: false,
                onblur: true,
                onfocusout: false,
                invalidHandler: function(form, validator) {
                    var errors = validator.numberOfInvalids();
                    if (errors) {
                        validator.errorList[0].element.focus();
                    }
                },
                submitHandler: function(form, event) {
                    event.preventDefault();
                    var data = new FormData();
                    data.append('reason_reject', $('#reason_reject').val() || "");
                    data.append('job_id', $('#job_reject_id').val() || "");
                    
                    data.append('_token', $('#_token').val());

                    $.ajax({
                        url: form.action,
                        processData: false,
                        contentType: false,
                        type: form.method,
                        data: data,
                        beforeSend: function() {
                            var html = '<i class="bx bx-loader-circle loading"></i> đang lưu';
                            $('#btn_reject_job').html("").html(html).attr("disabled", true);
                        },
                        success: function(response) {
                            var message = response.message || "";
                            var title = "Lỗi";
                            $('#btn_reject_job').html("").html("Xác nhận từ chối").attr('disabled', false);
                            if (response.success) {
                                type = "success";
                                title = "Thành công";
                                toastr.success(message, title, {"positionClass": "toast-top-center"});
                                $('#reject_job_modal').modal('hide');
                                list_engineers.GetWaitingReceiveJobList();
                            } else {
                                toastr.error(message, title);
                            }

                        },
                        error: function(request, status, error) {
                            $('#btn_reject_job').html("").html("Xác nhận từ chối").attr('disabled', false);
                            toastr.error("Lỗi server", "Lỗi", {"positionClass": "toast-top-center"});
                        }
                    });
                }
            });
        },
        SetDatetimepicker: function() {
            $('#job_end_date, #job_start_at').datetimepicker({
                autoclose: true,
                // dateFormat: 'yy-mm-dd',
                todayBtn: true,
                todayHighlight: true,
                weekStart: 1,
                format: 'hh:ii:ss dd-mm-yyyy',
                // minView: 2,
                // viewMode: 'days',
            });
        },
        GetProcessingJobsList: function() {
            $(document).on('click', '#tab_processing_jobs_list', function() {
                $.ajax({
                    url: '/engineer/get-processing-jobs-list',
                    data: {
                        _token
                    },
                    type: 'get',
                    processData: false,
                    success: function(res) {
                        var html_list_job = '';
                        $.each(res[0].getProcessingJobsList, function(key, item) {
                            html_list_job += `<div class="job_item my-1 bg-fff rounded p-2 d-flex justify-content-between mb-3" id="job_item${item.id}">
                                <div class="job_item_info">
                                    <div class="job_item_name">
                                        <a href="">${item.job_type.value_setting}. Lý do: ${item.reason_job.value_setting}</a>
                                    </div>
                                    <div class="job_item_status">
                                        <a href="">Ngày bắt đầu: ${moment(item.created_at, 'YYYY-MM-DD').format('DD-MM-YYYY')}</a>
                                    </div>
                                    <div class="job_item_status">
                                    <a href="">Địa điểm: ${item.building ? (item.building.building_name + ' ' + item.building.address) : (item.address ? item.address.address : ``)}</a>
                                    </div>
                                </div>
                                <div class="job_item_action d-flex align-items-center">
                                    <button class="btn btn-sm btnOutJob btn-danger me-1" data-id="${item.id}">
                                        <i class="las la-times"></i>
                                    </button>
                                    <button class="btn btn-sm btnAddEngineerEmployee btn-danger me-1" data-id="${item.id}">
                                        <i class="bx bx-user-plus"></i>
                                    </button>
                                    <button class="btn btn-sm btn-info me-1 btnGetInfoJob" data-id="${item.id}">
                                        <i class="mdi mdi-information-variant"></i>
                                    </button>
                                    <button class="btn btn-sm btn-success btn_comfirm_completed_job me-1" data-id="${item.id}" data-job_name="${item.job_name}" data-description="${item.descriptions}" data-status="${item.status}" >
                                        <i class="mdi mdi-arrow-right-thin"></i>
                                    </button>
                                </div>
                            </div>`;
                        });
                        // var waiting_receive_job_number = res[0].listJobOfEmployee.length || 0;
                        // var processing_job_number = res[0].processing_job_number.length || 0;
                        // var completed_job_number = res[0].completed_job_number.length || 0;
                        // $('#waiting_receive_job_number').text(waiting_receive_job_number);
                        // $('#processing_job_number').text()
                        // $('#completed_job_number').text()
                        $('#list_processing_jobs').html('').html(html_list_job || `<div class="d-flex justify-content-center mt-4">DS công việc đang xử lý hiện rỗng</div>`);
                    }
                });
            });
        },
        RefreshWaitingJobsList: function() {
            $(document).on('click', '#tab_waiting_jobs_list', function() {
                list_engineers.GetWaitingReceiveJobList();
            });
        },
        ClearModal: function() {
            $(document).on('hidden.bs.modal', '.modal', function() {
                $(this).find("input[type=text],input[type=file],input[type=email],input[name=id],textarea,select")
                    .val('')
                    .end();
                $('#forward_step_modal')?.validate().resetForm();
                $('#reject_job_form')?.validate().resetForm();
                $(".error").removeClass("error");
                list_engineers.HideCommentForm();
            })
        },
        GetWaitingReceiveJobList: function() {
            $.ajax({
                url: '/engineer/list-job-of-employee',
                data: {
                    _token
                },
                type: 'get',
                processData: false,
                success: function(res) {
                    var html_list_job = '';
                    $.each(res[0].listJobOfEmployee, function(key, item) {
                        var engineer_employee_id_array = [];
                        item.assigned_employee_groups.forEach(employee => {
                            engineer_employee_id_array.push(employee.id);
                            });
                        html_list_job += `<div class="job_item my-1 bg-fff rounded p-2 d-flex justify-content-between mb-3" id="job_item${item.id}">
                            <div class="job_item_info">
                                <div class="job_item_name">
                                <a href="">${item.job_type.value_setting}. Lý do: ${item.reason_job.value_setting}</a>
                                </div>
                                <div class="job_item_status">
                                    <a href="">Ngày bắt đầu: ${moment(item.created_at, 'YYYY-MM-DD').format('DD-MM-YYYY')}</a>
                                </div>
                                <div class="job_item_status">
                                        <a href="">Địa điểm: ${item.building ? (item.building.building_name + ' ' + item.building.address) : (item.address ? item.address.address : ``)}</a>
                                    </div>
                            </div>
                            <div class="job_item_action d-flex align-items-center">
                                <button class="btn btn-sm btn-danger btnRejectJob me-1" data-id="${item.id}">
                                    <i class="bx bx-x"></i>
                                </button>
                                <button class="btn btn-sm btn-info btnGetInfoJob me-1" data-id="${item.id}">
                                    <i class="mdi mdi-information-variant"></i>
                                </button>
                                <button class="btn btn-sm btn-success btn_forward_step me-1" data-engineer_employee_id_array="${engineer_employee_id_array}" data-id="${item.id}" data-job_name="${item.job_type.value_setting} - ${item.reason_job.value_setting}" data-description="${item.descriptions}" data-status="${item.status}" >
                                    <i class="mdi mdi-arrow-right-thin"></i>
                                </button>
                            </div>
                        </div>`;
                    });
                    var waiting_receive_job_number = res[0].listJobOfEmployee.length || 0;
                    var processing_job_number = res[0].processing_job_number.length || 0;
                    var completed_job_number = res[0].completed_job_number.length || 0;
                    $('#waiting_receive_job_number').text(waiting_receive_job_number);
                    $('#processing_job_number').text(processing_job_number)
                    $('#completed_job_number').text(completed_job_number)
                    $('#list_waiting_receive_jobs').html('').html(html_list_job || `<div class="d-flex justify-content-center mt-4">Danh sách công việc hiện rỗng</div>`);
                }
            });
        },
        SetChoice: function() {
            // additional_assign_choice = new Choice('#additional_assign', {
            //     removeItemButton: true,
            // });
        },
        GetModelForwardStep: function() {
            $(document).on('click', '.btn_forward_step', function() {
                var job_name = $(this).data('job_name');
                var description = $(this).data('description');
                var status = $(this).data('status');
                var id = $(this).data('id');
                var url = $('#getUnassignEmployeeByJob').val();
                var _token = $('#_token').val();

                $.ajax({
                    url: url,
                    type: 'get',
                    data: {
                        "job_id": id,
                        "_token": _token,
                    },
                    success: function(res) {
                        if (additional_assign_choice) {
                            additional_assign_choice.setChoices([], 'value', 'label', false);
                            additional_assign_choice.destroy();
                        }
                        $('#additional_assign').html('')
                        $.each(res[0].listEmployee, function(index, option) {
                            $('#additional_assign').append($('<option>', {
                                value: option.id,
                                text: option.username
                            }));
                        });
            
                        additional_assign_choice = new Choices('#additional_assign', {
                            removeItemButton: true,
                            placeholder: true,
                            placeholderValue: "--Chọn--",
                        });
                    }
                });

                $('#job_description').html('').html(description);
                $('#status').val(status).trigger('change');
                $('#job_name').html('').html(job_name);
                $('#job_id').val(id);

                $('#forward_step_modal').modal('show');
            });
        },
        ProcessSingleJob: function() {
            $('#receive_job_form').validate({
                rules: {
                    job_end_date: {
                        required: false,
                    },
                    job_start_at: {
                        required: false,
                    }
                },
                messages: {
                    job_end_date: {
                        required: "Thiếu thời gian bắt đầu làm việc",
                    },
                    job_start_at: {
                        required: "Thiếu thời gian dự kiến hoàn thành",
                    },
                },
                onkeyup: false,
                onblur: true,
                onfocusout: false,
                invalidHandler: function(form, validator) {
                    var errors = validator.numberOfInvalids();
                    if (errors) {
                        validator.errorList[0].element.focus();
                    }
                },
                submitHandler: function(form, event) {
                    event.preventDefault();
                    var data = new FormData();
                    data.append('job_id', $('#job_id').val() || "");
                    data.append('job_note', $('#job_note').val() || "");
                    data.append('additional_assign', $('#additional_assign').val() || "");
                    // data.append('job_end_date', moment($('#job_end_date').val(), 'HH:mm:ss DD-MM-YYYY').format('YYYY-MM-DD HH:mm:ss'));
                    // data.append('job_start_at', moment($('#job_start_at').val(), 'HH:mm:ss DD-MM-YYYY').format('YYYY-MM-DD HH:mm:ss'));
                    
                    data.append('_token', $('#_token').val());
                    $.ajax({
                        url: form.action,
                        processData: false,
                        contentType: false,
                        type: form.method,
                        data: data,
                        beforeSend: function() {
                            var html = '<i class="bx bx-loader-circle loading"></i> đang lưu';
                            $('#btn_process_receive_job').html("").html(html).attr("disabled", true);
                        },
                        success: function(response) {
                            var message = response.message || "";
                            var title = "Lỗi";
                            var type = "error";
                            $('#btn_process_receive_job').html("").html("Xác nhận làm việc").attr('disabled', false);
                            if (response.success) {
                                type = "success";
                                title = "Thành công";
                                var job_id = response[0].job_id;
                                var job_item_id = '#job_item' + job_id;
                                $('#forward_step_modal').modal('hide');
                                $(job_item_id).remove();
                                toastr.success(message, title, {"positionClass": "toast-top-center"});
                                $('#waiting_receive_job_number').text(parseInt($('#waiting_receive_job_number').text()) - 1);
                                $('#processing_job_number').text(parseInt($('#processing_job_number').text()) + 1);
                            } else {
                                toastr.error(message, title);
                            }

                        },
                        error: function(request, status, error) {
                            $('#btn_process_receive_job').html("").html("Xác nhận làm việc").attr('disabled', false);
                            toastr.error("Lỗi server");
                        }
                    });
                }
            });
        }
    }
});

var additional_assign_choice;
var add_engineer_in_processing_step_choice;
$(document).ready(function () {
    list_engineers.init();
});