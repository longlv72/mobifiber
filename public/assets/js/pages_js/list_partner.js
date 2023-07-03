var total_errors_import = 0;
$(function() {
    window.list_partner = {
        init: function() {
            list_partner.GetListPartner();
            // list_partner.ListPartnerRefresh();
            list_partner.CreateSinglePartner();
            list_partner.UpdatePartnerModal();
            list_partner.UpdateSinglePartner();
            list_partner.ClearModal();
            list_partner.DeleteSinglePartner();
            list_partner.CheckFileExcelPartner();
            list_partner.ImportExcelPartner();
            list_partner.Activepartner();
            list_partner.SearchPartner();
            $('#btn_save_excel_partner').hide();
            list_partner.CheckButtonImportChange();
        },
        CheckButtonImportChange: function() {
            $(document).on('click', '#partnerExcel', function() {
                $('#btn_save_excel_partner').hide();
                $('#btn_check_file_excel_partner').show();
            });
        },
        SearchPartner: function() {
            $(document).on('click', '.btnSearch', function() {
                list_partner.ListPartnerRefresh();
            });
        },
        SetTooltip: function() {
            $('.btnTooltip').tooltip();
        },
        Activepartner: function() {
            $(document).on('click', '.btnActive', function() {
                var url = '/deactive-partner';
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
                    cancelButtonText: 'Hủy bỏ'
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
                                    list_partner.ListPartnerRefresh();
                                    // $('#add_partner_modal').modal('show');
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
                                    confirmButtonText: 'OK'
                                });
                            }
                        })
                    }
                });
            });
        },
        ImportExcelPartner: function() {
            $('#btn_save_excel_partner').on('click', function() {
                var data_import = $('#list_errors_in_file').bootstrapTable('getData');

                if (total_errors_import > 0 || data_import.length <= 0) {
                    return ;
                }
                var data = new FormData();
                data.append('data', JSON.stringify(data_import));
                data.append('_token', $('#_token').val());

                $.ajax({
                    url: '/save-file-partner-data',
                    type: 'post',
                    processData: false,
                    contentType: false,
                    data: data,
                    beforeSend: function() {
                        var html = '<i class="bx bx-loader-circle loading"></i> đang xử lý';
                        $('#btn_save_excel_partner').html("").html(html).attr("disabled", true);
                    },
                    success: function(response) {
                        var message = response.message || "";
                        var title = "Lỗi";
                        var type = "error";
                        $('#btn_save_excel_partner').html("").html("Lưu lại").attr('disabled', false);
                        if (response.success) {
                            type = "success";
                            title = "Thành công";
                            toastr.success(message, title);
                            list_partner.ListPartnerRefresh();
                            $('#import_excel_partner_modal').modal('hide');
                        } else {
                            toastr.error(message, title);
                        }

                    },
                    error: function(request, status, error) {
                        $('#btn_check_file_excel_partner').html("").html("Kiểm tra").attr('disabled', false);
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
            });
        },
        CheckFileExcelPartner: function() {
            $('#import_excel_partner_form').validate({
                rules: {
                    partnerExcel: {
                        required: true,
                        extension: 'xlsx|xls'
                    }
                },
                messages: {
                    partnerExcel: {
                        required: "Thiếu file thông tin đối tác",
                        extension: "File tải lên không đúng định dạng"
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
                errorPlacement: function (error, element) {
                    if (element.attr("name") == "partnerExcel")
                        error.insertAfter("#import_group");
                    else
                        error.insertAfter(element);
                },
                submitHandler: function(form, event) {
                    event.preventDefault();
                    var data = new FormData();
                    var file_data = $('#partnerExcel')[0].files[0];
                    data.append('partnerExcel', $('#partnerExcel')[0].files[0] || "");

                    data.append('_token', $('input[name="_token"]').val());

                    $.ajax({
                        url: form.action,
                        processData: false,
                        contentType: false,
                        type: form.method,
                        data: data,
                        beforeSend: function() {
                            var html = '<i class="bx bx-loader-circle loading"></i> đang xử lý';
                            $('#btn_check_file_excel_partner').html("").html(html).attr("disabled", true);
                        },
                        success: function(response) {
                            var message = response.message || "";
                            var title = "Lỗi";
                            var type = "error";
                            $('#btn_check_file_excel_partner').html("").html("Kiểm tra").attr('disabled', false);

                            if(response[0].rows) {
                                $('#error_array_in_file_excel_group').removeClass('d-none');
                                $('#list_errors_in_file').bootstrapTable('destroy');
                                $('#list_errors_in_file').bootstrapTable({
                                    data: response[0].rows,
                                    columns: [
                                        {
                                            field: 'error',
                                            classes: "error",
                                            title: 'Lỗi',
                                            formatter: function(value, row, index) {
                                                var btnDelete = `<a data-error_length="${row.error.length}" class="btn btn-danger btn-sm btnDeleteImport me-1"><i class="bx bx-trash"></i></a>`;
                                                var html = row.error.length > 0 ? `<a class="btn btn-danger btn-sm btnTooltip" data-bs-toogle="tooltip" data-bs-placement="top" data-bs-original-title="${row.error}"><i class="bx bx-info-circle"></i></a>` : '<a data-bs-toogle="tooltip" data-bs-placement="top" data-bs-original-title="Hợp lệ, không có lỗi" class="btn btn-success btn-sm btnTooltip"><i class="bx bx-check"></i></a>';
                                                return `<div class="d-flex" data-bs-toogle="tooltip" data-bs-placement="top" data-bs-original-title="Xóa dòng này">${btnDelete + html}</div>`;
                                            },
                                            events: {
                                                'click .btnDeleteImport': function (e, value, row, index) {
                                                    var error_length = row.error.length || 0;
                                                    debugger
                                                    if (error_length > 0) {
                                                        total_errors_import = total_errors_import - 1;
                                                    }
                                                    if (total_errors_import > 0) {
                                                        $('#btn_save_excel_partner').hide();
                                                        $('#btn_check_file_excel_partner').show();
                                                    } else {
                                                        $('#btn_save_excel_partner').show();
                                                        $('#btn_check_file_excel_partner').hide();
                                                    }
                                                    $("#list_errors_in_file").bootstrapTable('remove', {
                                                        field: '$index',
                                                        values: [index]
                                                    });
                                                },
                                            }
                                        },
                                        { field: '0', title: 'Tên đối tác' },
                                        { field: '1', title: "Email"},
                                        { field: '2', title: 'Số điện thoại' },
                                        { field: '3', title: "Địa chỉ"},
                                        { field: '4', title: "Mã code đối tác"},
                                        { field: '5', title: "Giấy phép KD"},
                                        { field: '6', title: "Số tk NH"},
                                        { field: '7', title: "Tên NH"},
                                        { field: '8', title: "Đầu mối"},
                                        { field: '9', title: "SDT đầu mối"},
                                        { field: '10', title: "Loại hình hợp tác", formatter: function(value, row, index) {
                                            return list_partner.GetTypeCooperate(row['10']);
                                        }},

                                    ],
                                    formatNoMatches: function() {
                                        return 'File tải lên hợp lệ. Nhấn nút lưu để hoàn tất import';
                                    },
                                      pagination: true,
                                      totalRows: response[0].total
                                });
                                $('.btnTooltip').tooltip();
                                if(response[0].totalError > 0){
                                    total_errors_import = response[0].row_errors;
                                    $('#btn_save_excel_partner').hide();
                                }
                                else{
                                    total_errors_import = 0;
                                    $('#btn_save_excel_partner').show();
                                    $('#btn_check_file_excel_partner').hide();
                                }
                            }

                        },
                        error: function(request, status, error) {
                            $('#btn_check_file_excel_partner').html("").html("Kiểm tra").attr('disabled', false);
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
        DeleteSinglePartner: function() {
            $(document).on('click', '.btnDelete', function() {
                var url = '/delete-partner';
                var id = $(this).data('id');
                var title = "Xác nhận xóa đối tác?";
                Swal.fire({
                    title: title,
                    text: "",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Đồng ý',
                    cancelButtonText: 'Hủy bỏ'
                }).then(function(result) {
                    if (result.value) {
                        $.ajax({
                            url: url,
                            method: 'delete',
                            data: {
                                "id": id,
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
                                    list_partner.ListPartnerRefresh();
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
                                    confirmButtonText: 'OK'
                                })
                            }
                        })
                    }
                });
            });
        },
        ClearModal: function() {
            $(document).on('hidden.bs.modal', '.modal', function() {
                $(this).find("input[type=text],input[type=file],input[type=email],input[name=id],textarea,select")
                    .val('')
                    .end();
                $('#update_partner_form')?.validate().resetForm();
                $('#create_partner_form')?.validate().resetForm();
                $('#import_excel_partner_form')?.validate().resetForm();
                $('#error_array_in_file_excel_group').addClass('d-none');
                $('#list_errors_in_file').bootstrapTable('destroy').html("");
                $('#btn_save_excel_partner').hide();
                $('#btn_check_file_excel_partner').show();
                $(".error").removeClass("error");
                $(".error").remove();
                total_errors_import = 0;
                // .find("input[type=checkbox], input[type=radio]")
                // .prop("checked", "")
                // .end();
            })
        },
        UpdatePartnerModal: function() {
            $(document).on('click', '.btnEdit', function() {
                var id = $(this).data('id');
                var partner_name = $(this).data('name');
                var phone = $(this).data('mobile');
                var email = $(this).data('email');
                var address = $(this).data('address');
                var code = $(this).data('code');
                var type = $(this).data('type');
                var business_license = $(this).data('business_license');
                var contract_code = $(this).data('contract_code');
                var number_bank = $(this).data('number_bank');
                var bank_name = $(this).data('bank_name');
                var contact_phone = $(this).data('contact_phone');
                var contact_name = $(this).data('contact_name');
                var cooperate = $(this).data('cooperate');
                var is_active = $(this).data('is_active');

                $('#partner_id').val(id);
                $('#update_partner_name').val(partner_name);
                $('#update_partner_email').val(email);
                $('#update_partner_phone').val(phone);
                $('#update_partner_address').val(address);
                $('#update_partner_type').val(type);
                $('#update_partner_code').val(code);
                $('#update_business_license').val(business_license);
                $('#update_contract_code').val(contract_code);
                $('#update_number_bank').val(number_bank);
                $('#update_bank_name').val(bank_name);
                $('#update_contact_phone').val(contact_phone);
                $('#update_contact_name').val(contact_name);
                $('#update_cooperate').val(cooperate);
                $('#update_status_partner').val(is_active);

                $('#update_partner_modal').modal('show');
            });
        },
        ListPartnerRefresh: function() {
            $('#list_partner_table').bootstrapTable('refresh');
        },
        CreateSinglePartner: function() {
            $('#create_partner_form').validate({
                rules: {
                    partner_name: "required",
                    partner_email: {
                        required: true,
                        email: true
                    },
                    partner_phone: {
                        required: true,
                        number: true,
                        maxlength: 10,
                        minlength: 10
                    },
                    partner_code: {
                        required: true
                    },
                    contract_code: {
                        required: true
                    },
                    business_license: {
                        required: true
                    },
                    number_bank: {
                        required: true,
                        number: true,
                    },
                    bank_name: {
                        required: true
                    },
                    cooperate: {
                        required: true
                    },
                    status_partners: {
                        required: true
                    }
                },
                messages: {
                    partner_name: "Thiếu tên đối tác",
                    partner_email: {
                        required: "Thiếu thông tin email",
                        email: "Email không đúng định dạng"
                    },
                    partner_phone: {
                        required: "Thiếu thông tin số điện thoại",
                        maxlength: "Số điện thoại chỉ có đúng 10 số",
                        minlength: "Số điện thoại phải có đúng 10 số",
                        number: "Số điện thoại chỉ bao gồm kí tự số"
                    },
                    partner_code: {
                        required: "Thiếu mã đối tác"
                    },
                    business_license: {
                        required: "Thiếu giấy phép kinh doanh"
                    },
                    contract_code: {
                        required: "Số hợp đồng"
                    },
                    number_bank: {
                        required: "Thiếu STK ngân hàng",
                        number: "Số tài khoản phải là số"
                    },
                    cooperate: {
                        required: "Thiếu loại hình hợp tác"
                    },
                    bank_name: {
                        required: "Thiếu tên ngân hàng"
                    },
                    status_partners: {
                        required: "Thiếu trạng thái đối tác"
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
                    data.append('partner_name', $('#partner_name').val() || "");
                    data.append('partner_email', $('#partner_email').val() || "");
                    data.append('partner_phone', $('#partner_phone').val() || "");
                    data.append('partner_address', $('#partner_address').val() || "");
                    data.append('partner_type', $('#partner_type').val() || "");
                    data.append('partner_code', $('#partner_code').val() || "");
                    data.append('business_license', $('#business_license').val() || "");
                    data.append('contract_code', $('#contract_code').val() || "");
                    data.append('number_bank', $('#number_bank').val() || "");
                    data.append('bank_name', $('#bank_name').val() || "");
                    data.append('contact_phone', $('#contact_phone').val() || "");
                    data.append('contact_name', $('#contact_name').val() || "");
                    data.append('cooperate', $('#cooperate').val() || "");
                    data.append('is_active', $('#status_partners').val() || "");

                    data.append('_token', $('input[name="_token"]').val());

                    $.ajax({
                        url: form.action,
                        processData: false,
                        contentType: false,
                        type: form.method,
                        data: data,
                        beforeSend: function() {
                            var html = '<i class="bx bx-loader-circle loading"></i> đang lưu';
                            $('#btn_add_single_partner').html("").html(html).attr("disabled", true);
                        },
                        success: function(response) {
                            var message = response.message || "";
                            var title = "Lỗi";
                            var type = "error";
                            $('#btn_add_single_partner').html("").html("Thêm mới").attr('disabled', false);
                            if (response.success) {
                                type = "success";
                                title = "Thành công";
                                toastr.success(message, title);
                                $('#add_partner_modal').modal('hide');
                                list_partner.ListPartnerRefresh();
                            } else {
                                toastr.error(message, title);
                            }

                        },
                        error: function(request, status, error) {
                            $('#btn_add_single_partner').html("").html("Thêm mới").attr('disabled', false);
                            Swal.fire({
                                title: "Lỗi",
                                text: "Lỗi server",
                                icon: "error",
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        },
        UpdateSinglePartner: function() {
            $('#update_partner_form').validate({
                rules: {
                    update_partner_name: "required",
                    update_partner_email: {
                        required: true,
                        email: true
                    },
                    update_partner_phone: {
                        required: true,
                        number: true,
                        maxlength: 10,
                        minlength: 10
                    },
                    update_partner_code: {
                        required: true
                    },
                    update_business_license: {
                        required: true
                    },
                    update_contract_code: {
                        required: true
                    },
                    update_number_bank: {
                        required: true
                    },
                    update_bank_name: {
                        required: true
                    },
                    update_cooperate: {
                        required: true
                    },
                    update_status_partner: {
                        required: true
                    }
                },
                messages: {
                    update_partner_name: "Thiếu tên đối tác",
                    partner_email: {
                        required: "Thiếu thông tin email",
                        email: "Email không đúng định dạng"
                    },
                    update_partner_phone: {
                        required: "Thiếu số điện thoại",
                        maxlength: "Số điện thoại chỉ có đúng 10 số",
                        minlength: "Số điện thoại phải có đúng 10 số",
                        number: "Số điện thoại chỉ bao gồm kí tự số"
                    },
                    update_partner_code: {
                        required: "Thiếu mã đối tác"
                    },
                    update_business_license: {
                        required: "Thiếu giấy phép kinh doanh"
                    },
                    update_contract_code: {
                        required: "Số hợp đồng"
                    },
                    update_number_bank: {
                        required: "Thiếu STK ngân hàng"
                    },
                    update_cooperate: {
                        required: "Thiếu loại hình hợp tác"
                    },
                    update_bank_name: {
                        required: "Thiếu tên ngân hàng"
                    },
                    update_status_partner: {
                        required: "Thiếu trạng thái đối tác"
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
                    data.append('partner_name', $('#update_partner_name').val() || "");
                    data.append('partner_email', $('#update_partner_email').val() || "");
                    data.append('partner_phone', $('#update_partner_phone').val() || "");
                    data.append('partner_address', $('#update_partner_address').val() || "");
                    data.append('partner_type', $('#update_partner_type').val() || "");
                    data.append('partner_code', $('#update_partner_code').val() || "");
                    data.append('business_license', $('#update_business_license').val() || "");
                    data.append('contract_code', $('#update_contract_code').val() || "");
                    data.append('number_bank', $('#update_number_bank').val() || "");
                    data.append('bank_name', $('#update_bank_name').val() || "");
                    data.append('contact_phone', $('#update_contact_phone').val() || "");
                    data.append('contact_name', $('#update_contact_name').val() || "");
                    data.append('cooperate', $('#update_cooperate').val() || "");
                    data.append('is_active', $('#update_status_partner').val() || "");
                    data.append('id', $('#partner_id').val() || "");

                    data.append('_token', $('input[name="_token"]').val());

                    $.ajax({
                        url: form.action,
                        processData: false,
                        contentType: false,
                        type: form.method,
                        data: data,
                        beforeSend: function() {
                            var html = '<i class="bx bx-loader-circle loading"></i> đang lưu';
                            $('#btn_update_single_partner').html("").html(html).attr("disabled", true);
                        },
                        success: function(response) {
                            var message = response.message || "";
                            var title = "Lỗi";
                            var type = "error";
                            $('#btn_update_single_partner').html("").html("Cập nhật").attr('disabled', false);
                            if (response.success) {
                                type = "success";
                                title = "Thành công";
                                toastr.success(message, title);
                                list_partner.ListPartnerRefresh();
                                $('#update_partner_modal').modal('hide');
                            } else {
                                toastr.error(message, title);
                            }

                        },
                        error: function(request, status, error) {
                            $('#btn_update_single_partner').html("").html("Cập nhật").attr('disabled', false);
                            Swal.fire({
                                title: "Lỗi",
                                text: "Lỗi server",
                                icon: "error",
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        },
        GetStatus: function(status = 2, id) {
            const STATUS = {
                2: "Đã vô hiệu hóa",
                1: 'Đã kích hoạt',
            };

            if(! STATUS.hasOwnProperty(status) || !status) {
                return 'N/A';
            }

            var flag = "danger";
            if (status == 1) {
                flag = "success";
            }
            var html = `<span data-id="${id}" data-bs-original-title="${status == 1 ? 'Kích hoạt' : 'Vô hiệu hóa'}" data-active="${status}" data-bs-toggle="tooltip" data-bs-placement="top"  class="btnTooltip btn btnActive btn-${status == 1 ? 'success' : 'danger'} mx-1 badge bg-${flag} font-size-12 p-2">${STATUS[status]}</span>`;
            return html;
        },
        GetTypePartner: function(type) {
            const TYPE = {
                2: "Tổ chức",
                1: 'Cá nhân',
            };

            if(! TYPE.hasOwnProperty(type) || !type) {
                return 'N/A';
            }

            var html = `<span font-size-12 p-2">${TYPE[type]}</span>`;
            return html;
        },
        GetTypeCooperate: function(type){
            const TYPE = {
                1: "Mobifone tự triển khai",
                2: 'Một phần',
                3: 'Toàn trình',
            };

            if(! TYPE.hasOwnProperty(type) || !type) {
                return 'N/A';
            }
            
            var html = `<span font-size-12 p-2">${TYPE[type]}</span>`;
            return html;
        },
        GetListPartner: function() {
            $('#list_partner_table').bootstrapTable({
                url: '/get-list-data-partner',
                queryParams: function(p) {
                    var param = $.extend(true, {
                        offset: p.offset,
                        limit: p.limit,
                        status: $('#status_filter').val(),
                        cooperate: $('#cooperate_filter').val(),
                        search_value: $('#search_name').val(),
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
                        title: "Thông tin đối tác",
                        align: 'center',
                        valign: 'left',
                        formatter: function(value, row, index) {
                            var partner_name = `<div class="row"><div class="col-md-3 d-flex justify-content-end">Tên đối tác:</div><div class="col-md-8 d-flex justify-content-start"> ${row.partner_name}</div></div>`;

                            var email = row.email ? `<div class="row"><div class="col-md-3 d-flex justify-content-end">Email:</div><div class="col-md-8 d-flex justify-content-start"> ${row.email}</div></div>` : ``;
                            var phone = row.phone ? `<div class="row"><div class="col-md-3 d-flex justify-content-end">SDT:</div><div class="col-md-8 d-flex justify-content-start"> ${row.phone}</div></div>` : ``;
                            var address = row.address ? `<div class="row"><div class="col-md-3 d-flex justify-content-end">Địa chỉ:</div><div class="col-md-8 d-flex justify-content-start"> ${row.address}</div></div>` : ``;
                            var partner_code = row.partner_code ? `<div class="row"><div class="col-md-3 d-flex justify-content-end">Mã đối tác:</div><div class="col-md-8 d-flex justify-content-start"> ${row.partner_code}</div></div>` : ``;
                            var cooperate = row.cooperate ? `<div class="row"><div class="col-md-3 d-flex justify-content-end">Loại hợp tác:</div><div class="col-md-8 d-flex justify-content-start"> ${list_partner.GetTypeCooperate(row.cooperate)}</div></div>` : ``;
                            var html = `<div>${partner_name} ${email} ${phone} ${address} ${partner_code} ${cooperate}</div>`;
                            return html;
                        }
                    },
                    {
                        title: "Thông tin ngân hàng",
                        align: 'center',
                        valign: 'left',
                        formatter: function(value, row, index) {
                            var bank_name = row.bank_name ? `<div>Tên ngân hàng: ${row.bank_name}</div>` : ``;
                            var bank_number = row.number_bank ? `<div>Số TK: ${row.number_bank}</div>` : ``;
                            var html = bank_name + bank_number;
                            return html;
                        }
                    },
                    {
                        title: "Trạng thái",
                        align: 'center',
                        valign: 'left',
                        formatter: function(value, row, index) {
                            var html = list_partner.GetStatus(row.is_active || 0, row.id);
                            return html;
                        }
                    },
                    {
                        title: "Chức năng",
                        valign: 'middle',
                        align: 'center',
                        // width: '100px',
                        class: 'CssAction',
                        formatter: function(value, row, index) {
                            var btnActive = `<a data-bs-original-title="${row.is_active == 1 ? 'Kích hoạt' : 'Vô hiệu hóa'}" data-active="${row.is_active}"  data-bs-toggle="tooltip" data-bs-placement="top"  class="btnTooltip mb-1 me-1 btn ${row.is_active == 0 ? 'btn-danger btnUnlock': 'btn-success btnUnlock'} btn-sm" data-id="${row.id}"><i class="bx bx-lock-alt"></i></a>`;
                            var btnDelete = `<a  data-bs-toggle="tooltip" data-bs-placement="top" class="btnTooltip btn btn-danger btn-sm btnDelete mb-1" data-id="${row.id}" data-bs-original-title="Xóa"><i class="bx bx-trash"></i></a>`;
                            var btnEdit = `<a data-bs-original-title="Cập nhật" data-bs-toggle="tooltip" data-bs-placement="top" data-name="${row.partner_name}" data-id="${row.id}" data-mobile="${row.phone}" data-is_active="${row.is_active}" data-email="${row.email}" data-address="${row.address}" data-code="${row.partner_code}" data-type="${row.type}" data-business_license="${row.business_license}" data-number_bank="${row.number_bank}" data-bank_name="${row.bank_name}" data-contact_phone="${row.contact_phone}" data-contact_name="${row.contact_name}" data-cooperate="${row.cooperate}" class="btnTooltip btn btn-sm btn-info btnEdit me-1 mb-1" ><i class="bx bx-pencil"></i></a>`;
                            var action = btnEdit + btnDelete;
                            return action;
                        },
                    }
                ],
                formatNoMatches: function() {
                    return 'Chưa có dữ liệu';
                },
            })
        }
    }
});

$(document).ready(function() {
    list_partner.init();
});
