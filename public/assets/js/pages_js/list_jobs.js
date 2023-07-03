var list_color = ['bg-success', 'bg-primary', 'bg-dark', 'text-danger', 'text-warning'];
var list_icon = ['ri-global-line', 'ri-user-location-line', 'ri-pen-nib-line', 'ri-compasses-2-line', 'ri-scissors-cut-fill', 'ri-pencil-ruler-2-fill', 'ri-artboard-line', 'ri-paint-line'];
var list_border_color = ['border-left-01005e', 'border-left-f1af2c', 'border-left-45CB85', 'border-left-f12c7a', 'border-left-6a78f1', 'border-left-6a78f1'];
$(function() {
    window.list_jobs = {
        init: function() {
            var choice, choice_update;
            list_jobs.GetListDataJobs();
            list_jobs.CreateSingleJob();
            list_jobs.UpdateJobModal();
            list_jobs.UpdateSingleJob();
            list_jobs.DeactiveJob();
            list_jobs.SetTooltip();
            list_jobs.DatetimepickerSetup();
            list_jobs.SetChoiceJS();
            list_jobs.SearchData();
            list_jobs.DeleteSingleJob();
            list_jobs.ClearModal();
            list_jobs.GetEngineerEmployeeInUpdateForm();
            list_jobs.GetEngineerEmployeeInCreateForm();
            list_jobs.CreateJobCodeByCustomer();
            list_jobs.GetListJobReasonByJobType();
            list_jobs.ViewHistory();
            list_jobs.GetBuildingByPartner();
            list_jobs.SetCustomerByContractId();
            list_jobs.GetDataAddressByCustomerId();
            list_jobs.GetInfoJobModal();
            list_jobs.GetJobProcessData();
            list_jobs.CommentAction();
            list_jobs.CommentReply();
            list_jobs.RemoveFileUpload();
            list_jobs.PreviewImageInput();
            list_jobs.GetCommentOfProcessItem();
            list_jobs.TriggerOpenFileUpload();
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
                    $('#comment_job_proccess_id').val("");
                    $('#post_comment_form').addClass('d-none');
                }
                if ( ! job_process_id || is_show_collappse == false ) return ;

                list_jobs.GetCommentOfJobProcessData(job_process_id);
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
                list_jobs.ClearPreviewImage();
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
                list_jobs.ImportImage(this);
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
                    list_jobs.RenderComment(res.data_comments, job_proccess_id);
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
                                list_jobs.ClearPreviewImage();
                                
                                list_jobs.RenderComment(response.data_comments, response.job_proccess_id);
                                $('#comment_input').val('');
                                $('#file_attach').val('');
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
        GetInfoJobModal: function() {
            $(document).on('click', '.btnGetInfoJob', function() {
                var job_id = $(this).data('id');
                $('#info_job_id').val(job_id);
                $('#comment_job_id').val(job_id);
                // list_jobs.GetCommentData(job_id);
                
                list_jobs.GetJobProcessData(job_id);
                $('#job_info_modal').modal('show');
            });
        },
        GetDataAddressByCustomerId: function() {
            $(document).on('change', '#customer_id', function() {
                var customer_id = $(this).val();
                if ( ! customer_id ) return ;

                $.ajax({
                    url: '/get-data-address',
                    type: 'get',
                    dataType: 'json',
                    data: {
                        customer_id: customer_id
                    },
                    success: function (res) {
                        var html = '<option value=""> -- Chọn -- </option>';
                        if (res.rows && res.rows.length > 0) {
                            $.each(res.rows, function (i, ele) {
                                html += '<option value="' + ele.id + '">' + ele.address + '</option>';
                            });
                        }
                        $('#address_id').html("").html(html);
                    }
                });                
            });

            $(document).on('change', '#update_customer_id', function() {
                var customer_id = $(this).val();
                if ( ! customer_id ) return ;

                $.ajax({
                    url: '/get-data-address',
                    type: 'get',
                    dataType: 'json',
                    data: {
                        customer_id: customer_id
                    },
                    success: function (res) {
                        var html = '<option value=""> -- Chọn -- </option>';
                        if (res.rows && res.rows.length > 0) {
                            $.each(res.rows, function (i, ele) {
                                html += '<option value="' + ele.id + '">' + ele.address + '</option>';
                            });
                        }
                        $('#update_address_id').html("").html(html);
                    }
                });                
            });
        },
        SetCustomerByContractId: function () {
            $(document).on('change', '#contract_id', function() {
                var customer_id = $(this).find('option:selected').data('customer_id');
                if ( ! customer_id ) return;

                $('#customer_id').val(customer_id).change();
            });

            $(document).on('change', '#update_contract_id', function() {
                var customer_id = $(this).find('option:selected').data('customer_id');
                if ( ! customer_id ) return;

                $('#update_customer_id').val(customer_id).change();
            });
        },
        GetBuildingByPartner: function() {
            $('#partner_id').change(function () {
                $.ajax({
                    url: '/list-building-by-partner',
                    type: 'get',
                    dataType: 'json',
                    data: {
                        id: $(this).val()
                    },
                    success: function (res) {
                        var html = '<option value=""> -- Chọn -- </option>';
                        if (res.data && res.data.length > 0) {
                            $.each(res.data, function (i, ele) {
                                html += '<option value="' + ele.id + '">' + ele.building_name + ' (' + ele.building_code + ') - ' + ele.percent_share +'% </option>';
                            });
                        }
                        $('#building_id').html("").html(html);
                        // $('#building_id').select2({
                        //     dropdownParent: $('#create_contract_modal')
                        // });
                    }
                });
            });

            $('#update_partner_id').change(function () {
                $.ajax({
                    url: '/list-building-by-partner',
                    type: 'get',
                    dataType: 'json',
                    data: {
                        id: $(this).val()
                    },
                    success: function (res) {
                        var html = '<option value="">-- Chọn --</option>';
                        if (res != null && res.data != null && res.data.length > 0) {
                            $.each(res.data, function (i, ele) {
                                html += '<option value="' + ele.id + '">' + ele.building_name + ' (' + ele.building_code + ') - ' + ele.percent_share + '% </option>';
                            });
                            $('#update_building_id').html("").html(html);
                            // $('#update_building_id').select2({
                            //     dropdownParent: $('#create_job_modal')
                            // });
                        }
                    }
                });
            });
        },
        ViewHistory: function() {
            $(document).on('click', '.btnViewHistory', function() {
                
                var id = $(this).data('id');
                if ( ! id ) return ;

                $.ajax({
                    url: '/engineer/get-job-process-data',
                    type: 'get',
                    data: {
                        "job_id": id,
                        "_token": $('#_token').val()
                    },
                    success: function(res) {
                        var html = ``;
                        if (res.success) {
                            $.each(res.list_job_processes, function (index, item) {
                                html += `<div class="swiper-slide">
                                            <div class="card pt-2 border-0 item-box text-center">
                                                <div class="timeline-content p-3 rounded">
                                                    <div>
                                                        <p class="text-muted fw-medium mb-0">${moment(item.created_at).format("DD-MM-YYYY")}</p>
                                                        <h6 class="mb-0">${item.action}</h6>
                                                    </div>
                                                </div>
                                                <div class="time"><span class="badge bg-success">${moment(item.created_at).format("HH:mm")}</span></div>
                                            </div>
                                        </div>
                                    `;
                            });
                        }
                        $('#time_line_body').html("").html(html);
                        $('#history_job_modal').modal('show');
                    }
                });
            });
        },
        GetListJobReasonGeneral: async function(job_type, appendto) {
            if ( ! job_type || ! appendto ) return;
            $.ajax({
                url: 'get-reason-job-list',
                type: 'GET',
                data: {
                    "_token": $('#_token').val(),
                    "job_type": job_type
                },
                success: function(res) {
                    var html = `<option value="">--Chọn--</option>`;
                    var job_reason_list = res.job_reason_list;
                    $.each(job_reason_list, function(index, reason_item) {
                        html += `<option value="${reason_item.id}">${reason_item.value_setting}</option>`
                    });

                    $(`#${appendto}`).html("").html(html);
                }
            });
        },
        GetListJobReasonByJobType: function() {
            $(document).on('change', '#job_type', async function() {
                var job_type = $(this).val();

                if (job_type == 2 || job_type == 3) {
                    $('#label_contract_id > span.is_required').html("").html(`<span class="required">*</span>`)
                    $('#contract_id').attr('disabled', false);
                } else {
                    $('#contract_id').val("").change();
                    $('#label_contract_id > span.is_required').html("");
                    $('#contract_id').attr('disabled', true);
                }

                if ( ! job_type ) {
                    $('#reason').html("").html(`<option value="">--Chọn--</option>`);
                    return;
                };
                
                await list_jobs.GetListJobReasonGeneral(job_type, "reason");
            });

            $(document).on('change', '#update_job_type', async function() {
                var job_type = $(this).val();

                if (job_type == 2 || job_type == 3) {
                    $('#label_update_contract_id > span.is_required').html("").html(`<span class="required">*</span>`)
                    $('#update_contract_id').attr('disabled', false);
                } else {
                    $('#update_contract_id').val("").change();
                    $('#label_update_contract_id > span.is_required').html("");
                    $('#update_contract_id').attr('disabled', true);
                }

                if ( ! job_type ) {
                    $('#update_reason').html("").html(`<option value="">--Chọn--</option>`);
                    return;
                };
                await list_jobs.GetListJobReasonGeneral(job_type, "update_reason");
            });
        },
        CreateJobCodeByCustomer: function() {
            $(document).on("change", '#customer_id', function() {
                var id = $(this).val();
                var code = $(this).find(':selected').data('customer_code');
                var code_no_space = (code || "").toString().indexOf(' ') > 0 ? code.replace(/\s+/g, '') : code;
                var job_code = `${code_no_space}_${moment().format('YYYYMMDDHHmm')}`;
                $('#job_code').val(job_code);
            });
            
            $(document).on("change", '#update_customer_id', function() {
                var id = $(this).val();
                var code = $(this).find(':selected').data('customer_code');
                var code_no_space = (code || "").toString().indexOf(' ') > 0 ? code.replace(/\s+/g, '') : code;
                var job_code = `${code_no_space}_${moment().format('YYYYMMDDHHmm')}`;
                $('#update_job_code').val(job_code);
            });
        },
        GetEngineerEmployeeInCreateForm: function() {
            $(document).on('change', '#employee_ids', function() {
                var array_id_value = $(this).val();

                if(array_id_value.length > 0) {
                    $.ajax({
                        url: '/engineer/get-engineer-employee-data',
                        type: 'get',
                        data: {
                            "array_employee_id": array_id_value,
                            "_token": $('#_token').val()
                        },
                        success: function(res) {
                            var engineer_employee_list = `<div class="employee_title_header col-md-12">
                                        <hr class="hr">
                                        <div class="d-flex justify-content-between">
                                            <div class="employee_header_name col-md-2 ms-5">Tên (username)</div>
                                            <div class="vr"></div>
                                            <div class="employee_header_name col-md-2 text-center">SDT</div>
                                            <div class="vr"></div>
                                            <div class="employee_header_mobile col-md-2 me-5">Email</div>
                                        </div>
                                        <hr class="hr">
                                    </div>`;
                            $.each(res.engineer_employee_data, function(key, item) {
                                engineer_employee_list += `<div class="employee_item_info">
                                    <div class="d-flex justify-content-between">
                                        <div class="job_item_name col-md-2 text-center ms-5">${item.lastname} ${item.firstname}(${item.username})</div>
                                        <div class="vr"></div>
                                        <div class="job_item_status col-md-2 text-center">${item.phone}</div>
                                        <div class="vr"></div>
                                        <div class="job_item_status col-md-2 me-5">${item.email}</div>
                                    </div>
                                    <hr class="hr">
                                </div>`;
                            });
                            if (engineer_employee_list.length > 0) {
                                $('#list_employee').html('').html(engineer_employee_list).removeClass('d-none');
                            } else {
                                $('#list_employee').addClass('d-none');
                            }
                        }
                    });
                } else {
                    $('#list_employee').html("").addClass('d-none');
                }

            });
        },
        GetEngineerEmployeeInUpdateForm: function() {
            $(document).on('change', '#update_employee_ids', function() {
                var array_id_value = $(this).val();

                if(array_id_value.length > 0) {
                    $.ajax({
                        url: '/engineer/get-engineer-employee-data',
                        type: 'get',
                        data: {
                            "array_employee_id": array_id_value,
                            "_token": $('#_token').val()
                        },
                        success: function(res) {
                            var engineer_employee_list = `<div class="employee_title_header col-md-12">
                                        <hr class="hr">
                                        <div class="d-flex justify-content-between">
                                            <div class="vr"></div>
                                            <div class="employee_header_name col-md-2 ms-5">Tên (username)</div>
                                            <div class="vr"></div>
                                            <div class="employee_header_name col-md-2 text-center">SDT</div>
                                            <div class="vr"></div>
                                            <div class="employee_header_mobile col-md-2 me-5">Email</div>
                                            <div class="vr"></div>
                                        </div>
                                        <hr class="hr">
                                    </div>`;
                            $.each(res.engineer_employee_data, function(key, item) {
                                engineer_employee_list += `<div class="employee_item_info">
                                    <div class="d-flex justify-content-between">
                                        <div class="vr"></div>
                                        <div class="job_item_name col-md-2 text-center ms-5">${item.lastname} ${item.firstname}(${item.username})</div>
                                        <div class="vr"></div>
                                        <div class="job_item_status col-md-2 text-center">${item.phone}</div>
                                        <div class="vr"></div>
                                        <div class="job_item_status col-md-2 me-5">${item.email}</div>
                                        <div class="vr"></div>
                                    </div>
                                    <hr class="hr">
                                </div>`;
                            });
                            if (engineer_employee_list.length > 0) {
                                $('#list_employee_update').html('').html(engineer_employee_list).removeClass('d-none');
                            } else {
                                $('#list_employee_update').addClass('d-none');
                            }
                        }
                    });
                } else {
                    $('#list_employee_update').html("").addClass('d-none')
                }

            });
        },
        DeleteSingleJob: function() {
            $(document).on('click', '.btnDelete', function() {
                var url = '/delete-job';
                var id = $(this).data('id');
                var title = "Xác nhận xóa?";
                Swal.fire({
                    title: title,
                    text: "",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Đồng ý',
                    cancelButtonText: 'Đóng'
                }).then(function(result) {
                    if (result.value) {
                        $.ajax({
                            url: url,
                            method: 'delete',
                            data: {
                                id,
                                "_token": $('#_token').val()
                            },
                            success: function(res) {
                                var message = res.message || "Lỗi";
                                var type = "error";
                                var title = "Lôi";
                                if (res.success) {
                                    title = "Thành công";
                                    type = "success";
                                    toastr.success(message, title);
                                    list_jobs.ListJobsRefresh();
                                } else {
                                    toastr.error(message, title);
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    title: "Lỗi",
                                    text: "Lỗi server",
                                    icon: "error",
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'Đóng'
                                })
                            }
                        })
                    }
                });
            });
        },
        SearchData: function() {
            $(document).on('click', '.btnSearch', function() {
                list_jobs.ListJobsRefresh();
            });
        },
        SetChoiceJS: function() {
            // choice = new Choices('#employee_ids', {
            //     removeItemButton: true,
            // });
            // choice_update = new Choices('#update_employee_ids', {
            //     removeItemButton: true,
            // });
            $('#update_employee_ids').select2({
                dropdownParent : $('#update_job_modal')
            });
            
            $('#update_building_id, #update_customer_id').select2({
                dropdownParent : $('#update_job_modal')
            });

            $('#update_partner_id').select2({
                dropdownParent : $('#update_job_modal')
            });

            $('#update_contract_id, #update_address_id').select2({
                dropdownParent : $('#update_job_modal')
            });

            $('#building_id').select2({
                dropdownParent : $('#create_job_modal')
            });

            $('#customer_id').select2({
                dropdownParent : $('#create_job_modal')
            });
            
            $('#partner_id').select2({
                dropdownParent : $('#create_job_modal')
            });
            
            $('#employee_ids').select2({
                dropdownParent : $('#create_job_modal')
            });
            
            $('#contract_id').select2({
                dropdownParent : $('#create_job_modal')
            });
            
            $('#address_id').select2({
                dropdownParent : $('#create_job_modal')
            });
        },
        DatetimepickerSetup: function() {
            // $('#start_at, #update_start_at, #end_date, #update_end_date').datetimepicker({
            //     autoclose: true,
            //     dateFormat: 'yy-mm-dd',
            //     todayBtn: true,
            //     todayHighlight: true,
            //     weekStart: 1,
            //     format: 'dd-mm-yyyy',
            //     minView: 2,
            //     viewMode: 'days',
            // });
        },
        SetTooltip: function() {
            $(document).on('mouseover', '.btnTooltip', function() {
                $(this).tooltip();
            });
        },
        DeactiveJob: function() {
            $(document).on('click', '.btnActive', function() {
                var url = '/deactive-job';
                var id = $(this).data('id');
                var title = "Xác nhận đổi trạng thái?";
                var is_active = $(this).data('active');
                Swal.fire({
                    title: title,
                    text: "",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Đồng ý',
                    cancelButtonText: 'Đóng'
                }).then(function(result) {
                    if (result.value) {
                        $.ajax({
                            url: url,
                            method: 'put',
                            data: {
                                "id": id,
                                is_active,
                                "_token": $('#_token').val()
                            },
                            success: function(res) {
                                var message = res.message || "Lỗi";
                                var type = "error";
                                var title = "Lôi";
                                if (res.success) {
                                    title = "Thành công";
                                    type = "success";
                                    toastr.success(message, title);
                                    list_jobs.ListJobsRefresh();
                                } else {
                                    toastr.error(message, title);
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    title: "Lỗi",
                                    text: "Lỗi server",
                                    icon: "error",
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'Đóng'
                                })
                            }
                        })
                    }
                });
            });
        },
        UpdateJobModal: function() {
            $(document).on('click', '.btnEdit', async function() {
                var id = $(this).data('id');
                var job_code = $(this).data('job_code');
                var job_type = $(this).data('job_type');
                var building_id = $(this).data('building_id');
                var customer_id = $(this).data('customer_id');
                var description = $(this).data('description');
                var job_status = $(this).data('job_status');
                var contract_id = $(this).data('contract_id');
                var address_id = $(this).data('address_id');
                var partner_id = $(this).data('partner_id');
                var reason = $(this).data('reason');

                var employee_id_array = ($(this).data('employee_id_array').toString().search(',') != -1 && $(this).data('employee_id_array')) != "" ? $(this).data('employee_id_array').split(',') : $(this).data('employee_id_array');

                $('#job_id').val(id);
                $('#update_job_code').val(job_code).attr('disabled', true);
                $('#update_job_type').val(job_type).change().attr('disabled', true);

                if ( contract_id ) 
                {
                    await $('#update_contract_id').attr('disabled', false).val(contract_id).change().attr('disabled', true);
                }
                $('#update_customer_id').attr('disabled', false).val(customer_id).change().attr('disabled', true);

                setTimeout(async () => {
                    await $('#update_reason').val(reason).change().attr('disabled', true);
                    await $('#update_address_id').val(address_id).change();
                }, 1000);
                await $('#update_partner_id').val(partner_id).change();
                
                setTimeout(() => {
                    $('#update_building_id').val(building_id).change();
                }, 1000);
                $('#update_description').val(description);
                $('#update_job_status').val(job_status).change();
                if (employee_id_array != "") {
                    $('#update_employee_ids').val(employee_id_array).trigger('change');
                }

                $('#update_job_modal').modal('show');
            });
        },
        ListJobsRefresh: function() {
            $('#list_jobs_table').bootstrapTable('refresh');
        },
        CreateSingleJob: function() {
            $('#create_job_form').validate({
                rules: {
                    job_code: {
                        required: true
                    },
                    job_type: {
                        required: true
                    },
                    contract_id: {
                        required: function() {
                            var job_type = parseInt($('#job_type').val());
                            var required = job_type == 2 || job_type == 3;
                            return required;
                        }
                    },
                    partner_id: {
                        required: function() {
                            var address_id = $('#address_id').val();
                            return ! address_id;
                        },
                    },
                    building_id: {
                        required: function () {
                            var partner_id = $('#partner_id').val() != "";
                            return partner_id;
                        },
                    },
                    address_id: {
                        required: function() {
                            var partner_id = $('#partner_id').val() == "";
                            return partner_id;
                        },
                    },
                    customer_id: {
                        required: true,
                    },
                    employee_ids: {
                        required: true,
                    },
                    reason: {
                        required: true,
                    }
                },
                messages: {
                    job_code: {
                        required: "Thiếu mã công việc"
                    },
                    job_type: {
                        required: "Thiếu tên công việc"
                    },
                    contract_id: {
                        required: "Thiếu thông tin hợp đồng",
                    },
                    partner_id: {
                        required: "Thiếu đối tác hoặc địa chỉ khách hàng",
                    },
                    building_id: {
                        required: "Thiếu tòa nhà",
                    },
                    address_id: {
                        required: "Thiếu đối tác hoặc địa chỉ khách hàng",
                    },
                    customer_id: {
                        required: "Thiếu thông tin khách hàng",
                    },
                    employee_ids: {
                        required: "Thiếu nhân viên",
                    },
                    reason: {
                        required: "Thiếu lý do công việc",
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
                errorPlacement: function(error, element) {
                    //Custom position: first name
                    if (element.attr("name") == "employee_ids" ) {
                        error.insertAfter("#employee_ids_group");
                    } else if (element.attr("name") == "customer_id" ) {
                        error.insertAfter("#customer_id_group");
                    } else if (element.attr("name") == "building_id" ) {
                        error.insertAfter("#building_id_group");
                    } else if (element.attr("name") == "contract_id" ) {
                        error.insertAfter("#contract_id_group");
                    }
                    else if (element.attr("name") == "partner_id" ) {
                        error.insertAfter("#partner_id_group");
                    }
                    else if (element.attr("name") == "address_id" ) {
                        error.insertAfter("#address_id_group");
                    }
                    else
                        error.insertAfter(element);
                },
                submitHandler: function(form, event) {
                    event.preventDefault();
                    var data = new FormData();
                    data.append('job_code', $('#job_code').val() || "");
                    data.append('job_type', $('#job_type').val() || "");
                    data.append('building_id', $('#building_id').val() || "");
                    data.append('customer_id', $('#customer_id').val() || "");
                    data.append('description', $('#description').val() || "");
                    data.append('employee_ids', $('#employee_ids').val() || "");
                    data.append('contract_id', $('#contract_id').val() || "");
                    data.append('partner_id', $('#partner_id').val() || "");
                    data.append('address_id', $('#address_id').val() || "");
                    data.append('reason', $('#reason').val() || "");

                    data.append('_token', $('#_token').val());

                    $.ajax({
                        url: form.action,
                        processData: false,
                        contentType: false,
                        type: form.method,
                        data: data,
                        beforeSend: function() {
                            var html = '<i class="bx bx-loader-circle loading"></i> đang lưu';
                            $('#btn_create_single_job').html("").html(html).attr("disabled", true);
                        },
                        success: function(response) {
                            var message = response.message || "";
                            var title = "Lỗi";
                            var type = "error";
                            $('#btn_create_single_job').html("").html("Thêm mới").attr('disabled', false);
                            if (response.success) {
                                type = "success";
                                title = "Thành công";
                                toastr.success(message, title);
                                list_jobs.ListJobsRefresh();
                                $('#create_job_modal').modal('hide');
                            } else {
                                toastr.error(message, title);
                            }

                        },
                        error: function(request, status, error) {
                            $('#btn_create_single_job').html("").html("Thêm mới").attr('disabled', false);
                            Swal.fire({
                                title: "Lỗi",
                                text: "Lỗi server",
                                icon: "error",
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Đóng'
                            });
                        }
                    });
                }
            });
        },
        UpdateSingleJob: function() {
            $('#update_job_form').validate({
                rules: {
                    update_partner_id: {
                        required: true,
                    },
                    update_building_id: {
                        required: function () {
                            var address_id = $('#update_address_id').val();
                            return ! address_id;
                        },
                    },
                    update_address_id: {
                        required: function() {
                            var building_id = $('#update_building_id').val();
                            return ! building_id;
                        },
                    },
                    update_employee_ids: {
                        required: true,
                    },
                    update_reason: {
                        required: true,
                    }
                },
                messages: {
                    update_partner_id: {
                        required: "Thiếu thông tin đối tác",
                    },
                    update_building_id: {
                        required: "Thiếu tòa nhà hoặc địa chỉ",
                    },
                    update_address_id: {
                        required: "Thiếu tòa nhà hoặc địa chỉ",
                    },
                    update_employee_ids: {
                        required: "Thiếu nhân viên",
                    },
                    update_reason: {
                        required: "Thiếu lý do công việc",
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
                errorPlacement: function(error, element) {
                    //Custom position: first name
                    if (element.attr("name") == "update_employee_ids" ) {
                        error.insertAfter("#update_employee_ids_group");
                    }
                    else
                        error.insertAfter(element);
                },
                submitHandler: function(form, event) {
                    event.preventDefault();
                    var data = new FormData();
                    data.append('building_id', $('#update_building_id').val() || "");
                    data.append('description', $('#update_description').val() || "");
                    data.append('employee_ids', $('#update_employee_ids').val() || "");
                    data.append('job_status', $('#update_job_status').val() || "");
                    data.append('partner_id', $('#update_partner_id').val() || "");
                    data.append('customer_id', $('#update_customer_id').val() || "");
                    data.append('address_id', $('#update_address_id').val() || "");

                    data.append('id', $('#job_id').val() || "");
                    data.append('_token', $('#_token').val());

                    $.ajax({
                        url: form.action,
                        processData: false,
                        contentType: false,
                        type: form.method,
                        data: data,
                        beforeSend: function() {
                            var html = '<i class="bx bx-loader-circle loading"></i> đang lưu';
                            $('#btn_update_single_job').html("").html(html).attr("disabled", true);
                        },
                        success: function(response) {
                            var message = response.message || "";
                            var title = "Lỗi";
                            var type = "error";
                            $('#btn_update_single_job').html("").html("Cập nhật").attr('disabled', false);
                            if (response.success) {
                                type = "success";
                                title = "Thành công";
                                toastr.success(message, title);
                                list_jobs.ListJobsRefresh();
                                $('#update_job_modal').modal('hide');
                            } else {
                                toastr.error(message, title);
                            }

                        },
                        error: function(request, status, error) {
                            $('#btn_update_single_job').html("").html("Cập nhật").attr('disabled', false);
                            Swal.fire({
                                title: "Lỗi",
                                text: "Lỗi server",
                                icon: "error",
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Đóng'
                            });
                        }
                    });
                }
            });
        },
        GetListDataJobs: function() {
            $('#list_jobs_table').bootstrapTable({
                url: '/get-list-data-jobs',
                queryParams: function(p) {
                    var param = $.extend(true, {
                        offset: p.offset,
                        limit: p.limit,
                        status: $('#status_filter').val() || '',
                        search_value: $('#search_value_filter').val() || ''
                    }, p);
                    return param;
                },
                destroy: true,
                striped: true,
                sidePagination: 'client',
                pagination: true,
                paginationHAlign: 'right',
                paginationVAlign: "bottom",
                search: false,
                undefinedText: "N/A",
                pageSize: 10,
                pageList: [10, 50, 100],
                columns: [
                    {
                        title: "Chức năng",
                        valign: 'middle',
                        align: 'center',
                        class: 'CssAction',
                        formatter: function(value, row, index) {

                            var employee_id_user_array = [];
                            row.assigned_employee_groups.forEach(employee => {
                                employee_id_user_array.push(employee.id);
                            });
                            var btnGetJobProcess = `<a data-bs-original-title="Trạng thái công việc"  data-bs-toggle="tooltip" data-bs-placement="top" class="btnTooltip btn btn-sm btn-warning me-1 mb-1 btnGetInfoJob"  data-id="${row.id}"><i class="bx bx-info-circle"></i></a>`;
                            var btnViewHistory = `<a data-bs-original-title="Lịch sử công việc"  data-bs-toggle="tooltip" data-bs-placement="top"  class="btnTooltip btnViewHistory mb-1 me-1 btn btn-secondary btn-sm" data-id="${row.id}"><i class="ri-eye-line"></i></a>`;
                            var btnActive = true ? `` : `<a data-bs-original-title="${row.is_active == 1 ? 'Kích hoạt' : 'Vô hiệu hóa'}" data-active="${row.is_active}"  data-bs-toggle="tooltip" data-bs-placement="top"  class="btnTooltip btnDeactive mb-1 me-1 btn ${row.is_active == 0 ? 'btn-danger btnUnlock': 'btn-success btnUnlock'} btn-sm" data-id="${row.id}"><i class="bx bx-lock-alt"></i></a>`;
                            var btnDelete = `<a  data-bs-toggle="tooltip" data-bs-placement="top" class="btnTooltip btn btn-danger btn-sm btnDelete mb-1" data-id="${row.id}" data-bs-original-title="Xóa"><i class="bx bx-trash"></i></a>`;
                            var btnEdit = `<a data-bs-original-title="Cập nhật" data-employee_id_array="${employee_id_user_array.join(",")}" data-bs-toggle="tooltip" data-bs-placement="top" data-contract_id="${row.contract_id}" data-address_id="${row.address_id}" data-partner_id="${row.partner_id}" data-reason="${row.reason}" data-id="${row.id}" data-job_code="${row.job_code}" data-job_type="${row.job_type.id}" data-job_status="${row.status}" data-building_id="${row.building_id}" data-customer_id="${row.customer_id}" data-start_at="${row.start_at}" data-end_date="${row.end_date}" data-job_status="${row.status}" data-description="${row.descriptions}"  class="btnTooltip btn btn-sm btn-info btnEdit me-1 mb-1" ><i class="bx bx-pencil"></i></a>`;
                            var action = btnGetJobProcess + btnViewHistory + btnActive + btnEdit + btnDelete;
                            return action;
                        },
                    },
                    {
                        title: "Thông tin công việc",
                        align: 'center',
                        valign: 'left',
                        formatter: function(value, row, index) {
                            var job_code = row.job_code ? `<div class="row"><div class="col-md-3 d-flex justify-content-end">Mã công việc:</div><div class="col-md-8 d-flex justify-content-start">${row.job_code}</div></div>` : ``;
                            var job_type = row.job_type ? `<div class="row"><div class="col-md-3 d-flex justify-content-end">Loại công việc:</div><div class="col-md-8 d-flex justify-content-start">${row.job_type.value_setting}</div></div>` : ``;
                            var building = (row.building != null || row.address != null) ? `<div class="row"><div class="col-md-3 d-flex justify-content-end">Địa điểm:</div><div class="col-md-8 d-flex justify-content-start">${row.building != null ? ( "tòa nhà " + row.building.building_name + " tại " + row.building.address) : (row.address ? row.address.address : "")}</div></div>` : ``;
                            var customer = row.customer ? `<div class="row"><div class="col-md-3 d-flex justify-content-end">Khách hàng:</div><div class="col-md-8 d-flex justify-content-start">${row.customer.last_name } ${row.customer.firstname} - ${row.customer.email} - ${row.customer.phone}</div></div>` : ``;
                            var reason = row.reason_job ? `<div class="row"><div class="col-md-3 d-flex justify-content-end">Lý do:</div><div class="col-md-8 d-flex justify-content-start">${row.reason_job.value_setting }</div></div>` : ``;
                            var html = job_code + job_type + building + customer + reason;
                            return html;
                        }
                    },
                    {
                        title: "Người tạo",
                        align: 'center',
                        valign: 'left',
                        formatter: function(value, row, index) {
                            var created_by_username = `<div>${row?.created_by?.username}</div>`;
                            var created_by_fullname = `<div>${row?.created_by?.lastname} ${row?.created_by?.firstname}</div>`;
                            var html = created_by_username + created_by_fullname;
                            return html;
                        }
                    },
                    {
                        title: "Trạng thái",
                        align: 'center',
                        valign: 'left',
                        formatter: function(value, row, index) {
                            var status = list_jobs.GetStatus(row.status);
                            var assigned_engineer = [];
                            $.each(row.assigned_employee_groups, function(index, item) {
                                // assigned_engineer.push(`${item.lastname} ${item.firstname} (${item.username})`);
                                assigned_engineer.push(`${item.lastname} ${item.firstname}`);
                            });
                            var assign_info = assigned_engineer.length >= 1 ? `<div>${assigned_engineer.length > 1 ? `Những người ` : `Người `} được giao: ${assigned_engineer.join(', ')}</div>` : ``;
                            var html = status + assign_info;
                            return html;
                        }
                    },
                    
                ],
                formatNoMatches: function() {
                    return 'Chưa có dữ liệu';
                },
            });
        },
        GetStatus: function(status) {
            const STATUS = {
                1: 'Chưa giao cho ai',
                2: "Đã giao, chưa ai nhận",
                3: "Đang xử lý",
                4: "Đã hoàn thành",
                5: "Công việc không khả thi",
                6: "Đã giao nhưng bị từ chối",
            };
            var flag = "danger";
            if (status == 1) {
                flag = "info";
            } else if(status == 3) {
                flag = 'primary';
            } else if(status == 4) {
                flag = 'success';
            }
            var html = `<span data-bs-original-title="${STATUS[status]}" data-active="${status}" data-bs-toggle="tooltip" data-bs-placement="top"  class="btnTooltip btn mx-1 badge bg-${flag} font-size-12 p-2">${STATUS[status]}</span>`;
            return html;
        },
        ClearModal: function() {
            $(document).on('hidden.bs.modal', '.modal', function() {
                $(this).find("input[type=text],input[type=file],input[type=email],input[name=id],textarea,select")
                    .val('')
                    .end()
                    .find("input[type=checkbox], input[type=radio]")
                    .prop("checked", "")
                    .end();
                    var name_modal = $(this)[0].id;
                    if (name_modal ==  'create_job_modal') {
                        $("#employee_ids").val('').change(); 
                        $('#job_type').val('').change();
                        $('#contract_id').val('').change();
                        $('#customer_id').val('').change();
                        $('#partner_id').val('').change();
                        $('#building_id').val('').change();
                        $('#job_code').val('');
                        $('#address_id').val('').change();
                    }
                    else { 
                        $("#update_employee_ids").val('').change(); 
                        $('#update_job_type').val('').change();
                        $('#update_contract_id').val('').change();
                        $('#update_customer_id').val('').change();
                        $('#update_partner_id').val('').change();
                        $('#update_building_id').val('').change();
                        $('#update_job_code').val('');
                        $('#update_address_id').val('').change();
                    }

                $('#update_job_form')?.validate().resetForm();
                $('#create_job_form')?.validate().resetForm();
                $('#error_array_in_file_excel_group').addClass('d-none');
                $('#list_job_errors_in_file').bootstrapTable('destroy').html("");

                list_jobs.HideCommentForm();
                
                $(".error").removeClass("error");
                // .find("input[type=checkbox], input[type=radio]")
                // .prop("checked", "")
                // .end();
            })
        },
    }
});

$(document).ready(function() {
    list_jobs.init();
})
