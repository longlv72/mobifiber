var total_errors_import = 0;
$(function() {
    window.list_employee = {
        init: function() {
            list_employee.GetListDataEmployee();
            list_employee.DeleteEmployee();
            list_employee.DeActiveEmployee();
            list_employee.ClearModal();
            list_employee.UpdateEmployeeModal();
            list_employee.CreateEmployee();
            list_employee.UpdateEmployee();
            list_employee.GetListDataEmployee();
            list_employee.SearchEmployee();
            list_employee.CheckFileExcelImport();
            list_employee.SaveImportExcel();
            list_employee.CheckInputImportChange();
        },
        CheckInputImportChange: function() {
            $(document).on('click', '#fileExcel', function() {
                $('#btn_save_excel').addClass('d-none');
                $('#btn_check_file_excel').removeClass('d-none');
            });
        },
        SaveImportExcel: function() {
            $('#btn_save_excel').on('click', function() {
                var data_import = $('#list_errors_in_file').bootstrapTable('getData');

                if (total_errors_import > 0 || data_import.length <= 0) {
                    return ;
                }
                var data = new FormData();
                data.append('data', JSON.stringify(data_import));
                data.append('_token', $('#_token').val());

                $.ajax({
                    url: '/save-file-employee-data',
                    type: 'post',
                    processData: false,
                    contentType: false,
                    data: data,
                    beforeSend: function() {
                        var html = '<i class="bx bx-loader-circle loading"></i> đang xử lý';
                        $('#btn_save_excel').html("").html(html).attr("disabled", true);
                    },
                    success: function(response) {
                        var message = response.message || "";
                        var title = "Lỗi";
                        var type = "error";
                        $('#btn_save_excel').html("").html("Lưu lại").attr('disabled', false);
                        if (response.success) {
                            type = "success";
                            title = "Thành công";
                            toastr.success(message, title);
                            list_employee.ListEmployeeRefresh();
                            $('#import_excel_modal').modal('hide');
                        } else {
                            toastr.error(message, title);
                        }

                    },
                    error: function(request, status, error) {
                        $('#btn_check_file_excel').html("").html("Kiểm tra").attr('disabled', false);
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
        CheckFileExcelImport: function() {
            $('#import_excel_form').validate({
                rules: {
                    fileExcel: {
                        required: true,
                        extension: 'xlsx|xls'
                    }
                },
                messages: {
                    fileExcel: {
                        required: "Thiếu file tải lên",
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
                    if (element.attr("name") == "fileExcel")
                        error.insertAfter("#import_group");
                    else
                        error.insertAfter(element);
                },
                submitHandler: function(form, event) {
                    event.preventDefault();
                    var data = new FormData();
                    data.append('fileExcel', $('#fileExcel')[0].files[0] || "");

                    data.append('_token', $('input[name="_token"]').val());

                    $.ajax({
                        url: form.action,
                        processData: false,
                        contentType: false,
                        type: form.method,
                        data: data,
                        beforeSend: function() {
                            var html = '<i class="bx bx-loader-circle loading"></i> đang xử lý';
                            $('#btn_check_file_excel').html("").html(html).attr("disabled", true);
                        },
                        success: function(response) {
                            var message = response.message || "";
                            var title = "Lỗi";
                            var type = "error";
                            $('#btn_check_file_excel').html("").html("Kiểm tra").attr('disabled', false);

                            // if (response.success) {
                            //     type = "success";
                            //     title = "Thành công";
                            //     toastr.success(message, title);
                            //     list.ListPartnerRefresh();
                            // } else {
                            //     toastr.error(message, title);
                            // }
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

                                                    if (error_length > 0) {
                                                        total_errors_import = total_errors_import - 1;
                                                    }
                                                    if (total_errors_import > 0) {
                                                        $('#btn_save_excel').hide();
                                                        $('#btn_check_file_excel').show();

                                                        $('#btn_save_excel').addClass('d-none');
                                                        $('#btn_check_file_excel').removeClass('d-none');
                                                    } else {
                                                        $('#btn_save_excel').removeClass('d-none');
                                                        $('#btn_check_file_excel').addClass('d-none');
                                                        
                                                    }
                                                    $("#list_errors_in_file").bootstrapTable('remove', {
                                                        field: '$index',
                                                        values: [index]
                                                    });
                                                },
                                            }
                                        },
                                        { field: '0', title: 'Tên NV' },
                                        { field: '1', title: "Họ NV"},
                                        { field: '2', title: 'Tên đăng nhập' },
                                        { field: '3', title: "Email"},
                                        { field: '4', title: "SDT liên hệ"},
                                        { field: '5', title: "Đơn vị thực quản lý", formatter: function(value, row, index) {
                                            return list_employee.GetRealManageUnit(row['5']);
                                        }},
                                        { field: '6', title: "Mật khẩu"},
                                        { field: '7', title: "Loại TK", formatter: function(value, row, index) {
                                            return list_employee.GetTypeUser(row['7']);
                                        }},

                                    ],

                                    formatNoMatches: function() {
                                        return 'File tải lên hợp lệ. Nhấn nút lưu để hoàn tất nhiệm vụ';
                                    },
                                      pagination: true,
                                      totalRows: response[0].total
                                });
                                $('.btnTooltip').tooltip();
                                if(response[0].totalError > 0){
                                    total_errors_import = response[0].row_errors;
                                    $('#btn_save_excel').addClass('d-none');
                                }
                                else{
                                    total_errors_import = 0;
                                    $('#btn_save_excel').removeClass('d-none');
                                    $('#btn_check_file_excel').addClass('d-none');
                                }
                            }

                        },
                        error: function(request, status, error) {
                            $('#btn_check_file_excel').html("").html("Kiểm tra").attr('disabled', false);
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
        GetTypeUser: function(type) {
            const TYPE = {
                1: "Quản trị viên (admin)",
                2: 'Nhân viên kỹ thuật',
            };

            if(! TYPE.hasOwnProperty(type) || !type) {
                return 'N/A';
            }
            var html = `<span font-size-12 p-2">${TYPE[type]}</span>`;
            return html;
        },
        SearchEmployee: function() {
            $(document).on('click', '.btnSearch', function() {
                list_employee.ListEmployeeRefresh();
            });
        },
        DeleteEmployee: function() {
            $(document).on('click', '.btnDelete', function() {
                var url = '/delete-employee';
                var id = $(this).data('id');
                var title = "Xác nhận xóa tài khoản nhân viên?";
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
                                    list_employee.ListEmployeeRefresh();
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
        DeActiveEmployee: function() {
            $(document).on('click', '.btnActive', function() {
                var url = '/deactive-employee';
                var id = $(this).data('id');
                var title = "Xác nhận đổi trạng thái nhân viên?";
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
                                    list_employee.ListEmployeeRefresh();
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
        ClearModal: function() {
            $(document).on('hidden.bs.modal', '.modal', function() {
                $(this).find("input[type=text],input[type=file],input[type=email],input[name=id],textarea,select")
                    .val('')
                    .end();
                $('#update_employee_form')?.validate().resetForm();
                $('#create_employee_form')?.validate().resetForm();
                $('#error_array_in_file_excel_group').addClass('d-none');
                $('#list_employee_errors_in_file').bootstrapTable('destroy').html("");
                // $('#btn_save_excel_employee').hide();
                // $('#btn_check_file_excel_employee').show();
                $(".error").removeClass("error");
                // .find("input[type=checkbox], input[type=radio]")
                // .prop("checked", "")
                // .end();
            })
        },
        UpdateEmployeeModal: function() {
            $(document).on('click', '.btnEdit', function() {
                var id = $(this).data('id');
                var username = $(this).data('username');
                var role_group_id = $(this).data('role_group_id');
                var lastname = $(this).data('last_name');
                var firstname = $(this).data('firstname');
                var phone = $(this).data('mobile');
                var email = $(this).data('email');
                var address = $(this).data('address');
                var is_active = $(this).data('is_active');
                var real_manage_unit = $(this).data('real_manage_unit');
                $('#employee_id').val(id);

                $('#update_employee_first_name').val(firstname);
                $('#update_employee_last_name').val(lastname);
                $('#update_employee_username').val(username);
                $('#update_employee_email').val(email);
                $('#update_employee_phone').val(phone);
                $('#update_employee_address').val(address);
                $('#update_active_account').val(is_active).change();
                $('#update_role_group_id').val(role_group_id).change();
                $('#update_real_manage_unit').val(real_manage_unit).change();

                $('#update_employee_modal').modal('show');
            });
        },
        ListEmployeeRefresh: function() {
            $('#list_employee_table').bootstrapTable('refresh');
        },
        CreateEmployee: function() {
            $('#create_employee_form').validate({
                rules: {
                    employee_first_name: "required",
                    employee_last_name: "required",
                    employee_email: {
                        required: true,
                        email: true
                    },
                    employee_phone: {
                        required: true,
                        number: true,
                        maxlength: 10,
                        minlength: 10
                    },
                    employee_username: {
                        required: true
                    },
                    password: {
                        required: true,
                        minlength: 6
                    },
                    confirm_password: {
                        required: true,
                        equalTo : "#password"
                    },
                    role_group_id: {
                        required: true,
                    },
                    real_manage_unit: {
                        required: true,
                    }
                },
                messages: {
                    employee_first_name: "Thiếu tên",
                    employee_last_name: "Thiếu họ",
                    employee_email: {
                        required: "Thiếu thông tin email",
                        email: "Email không đúng định dạng"
                    },
                    employee_phone: {
                        required: "Thiếu thông tin số điện thoại",
                        maxlength: "Số điện thoại phải có đúng 10 số",
                        minlength: "Số điện thoại phải có đúng 10 số",
                        number: "Số điện thoại chỉ bao gồm kí tự số"
                    },
                    employee_username: {
                        required: "Thiếu username"
                    },
                    password: {
                        required: "Thiếu mật khẩu",
                        minlength: "Mật khẩu tối thiểu 6 kí tự"
                    },
                    confirm_password: {
                        required: "Thiếu xác nhận mật khẩu",
                        equalTo : "Xác nhận mật khẩu không khớp",
                        number: "Số tài khoản phải là số"
                    },
                    role_group_id: {
                        required: "Thiếu loại tài khoản",
                    },
                    real_manage_unit: {
                        required: "Thiếu đơn vị thực quản lý",
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
                    if (element.attr("name") == "password" )
                        error.insertAfter("#password_group");
                    else if  (element.attr("name") == "conf_password" )
                        error.insertAfter("#confirm_password_group");
                    else
                        error.insertAfter(element);
                },
                submitHandler: function(form, event) {
                    event.preventDefault();
                    var data = new FormData();
                    data.append('first_name', $('#employee_first_name').val() || "");
                    data.append('last_name', $('#employee_last_name').val() || "");
                    data.append('username', $('#employee_username').val() || "");
                    data.append('email', $('#employee_email').val() || "");
                    data.append('phone', $('#employee_phone').val() || "");
                    data.append('real_manage_unit', $('#real_manage_unit').val() || "");
                    data.append('password', $('#password').val() || "");
                    data.append('is_active', $('#active_account').val() || "");
                    data.append('role_group_id', $('#role_group_id').val() || "");
                    data.append('_token', $('input[name="_token"]').val());

                    $.ajax({
                        url: form.action,
                        processData: false,
                        contentType: false,
                        type: form.method,
                        data: data,
                        beforeSend: function() {
                            var html = '<i class="bx bx-loader-circle loading"></i> đang lưu';
                            $('#btn_add_single_employee').html("").html(html).attr("disabled", true);
                        },
                        success: function(response) {
                            var message = response.message || "";
                            var title = "Lỗi";
                            var type = "error";
                            $('#btn_add_single_employee').html("").html("Thêm mới").attr('disabled', false);
                            if (response.success) {
                                type = "success";
                                title = "Thành công";
                                toastr.success(message, title);
                                list_employee.ListEmployeeRefresh();
                                $('#add_employee_modal').modal('hide');
                            } else {
                                toastr.error(message, title);
                            }

                        },
                        error: function(request, status, error) {
                            $('#btn_add_single_employee').html("").html("Thêm mới").attr('disabled', false);
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
        UpdateEmployee: function() {
            $('#update_employee_form').validate({
                rules: {
                    update_employee_first_name: "required",
                    update_employee_last_name: "required",
                    update_employee_email: {
                        required: true,
                        email: true
                    },
                    update_employee_phone: {
                        required: true,
                        number: true,
                        maxlength: 10,
                        minlength: 10
                    },
                    update_role_group_id: {
                        required: true,
                    },
                    update_real_manage_unit: {
                        required: true,
                    }
                },
                messages: {
                    update_employee_first_name: "Thiếu tên",
                    update_employee_last_name: "Thiếu họ",
                    update_employee_email: {
                        required: "Thiếu thông tin email",
                        email: "Email không đúng định dạng"
                    },
                    update_employee_phone: {
                        required: "Thiếu thông tin số điện thoại",
                        maxlength: "Số điện thoại phải có đúng 10 số",
                        minlength: "Số điện thoại phải có đúng 10 số",
                        number: "Số điện thoại chỉ bao gồm kí tự số"
                    },
                    update_role_group_id: {
                        required: "Thiếu loại tài khoản",
                    },
                    update_real_manage_unit: {
                        required: "Thiếu đơn vị thực quản lý",
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
                    data.append('first_name', $('#update_employee_first_name').val() || "");
                    data.append('last_name', $('#update_employee_last_name').val() || "");
                    data.append('email', $('#update_employee_email').val() || "");
                    data.append('phone', $('#update_employee_phone').val() || "");
                    data.append('address', $('#update_employee_address').val() || "");
                    data.append('is_active', $('#update_active_account').val() || "");
                    data.append('role_group_id', $('#update_role_group_id').val() || "");
                    data.append('real_manage_unit', $('#update_real_manage_unit').val() || "");
                    data.append('id', $('#employee_id').val() || "");

                    data.append('_token', $('input[name="_token"]').val());

                    $.ajax({
                        url: form.action,
                        processData: false,
                        contentType: false,
                        type: form.method,
                        data: data,
                        beforeSend: function() {
                            var html = '<i class="bx bx-loader-circle loading"></i> đang lưu';
                            $('#btn_update_single_employee').html("").html(html).attr("disabled", true);
                        },
                        success: function(response) {
                            var message = response.message || "";
                            var title = "Lỗi";
                            var type = "error";
                            $('#btn_update_single_employee').html("").html("Cập nhật").attr('disabled', false);
                            if (response.success) {
                                type = "success";
                                title = "Thành công";
                                toastr.success(message, title);
                                list_employee.ListEmployeeRefresh();
                                $('#update_employee_modal').modal('hide');
                            } else {
                                toastr.error(message, title);
                            }

                        },
                        error: function(request, status, error) {
                            $('#btn_update_single_employee').html("").html("Cập nhật").attr('disabled', false);
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
        GetStatus: function(status, id) {
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
        GetRealManageUnit: function(unit) {
            const UNIT = {
                1: "Công ty kinh doanh",
                2: 'Trung tâm mạng lưới',
                3: 'Đại lý',
            };

            if(! UNIT.hasOwnProperty(unit) || !unit) {
                return 'N/A';
            }

            var html = `<span data-bs-original-title="Đơn vị thực quản lý" data-bs-toggle="tooltip" data-bs-placement="top"  class="btnTooltip btn btn-success'} mx-1 badge bg-success font-size-12 p-2">${UNIT[unit]}</span>`;
            return html;
        },
        GetListDataEmployee: function() {
            $('#list_employee_table').bootstrapTable({
                url: '/get-list-data-employee',
                queryParams: function(p) {
                    var param = $.extend(true, {
                        offset: p.offset,
                        limit: p.limit,
                        role_group_id: $('#role_group_id_filter').val(),
                        search_value: $('#search_value_filter').val(),
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
                        title: "Thông tin nhân viên",
                        align: 'center',
                        valign: 'left',
                        formatter: function(value, row, index) {
                            var employee_name = `<div>Tên NV: ${row.lastname} ${row.firstname}</div>`;
                            var email = row.email ? `<div>Email: ${row.email}</div>` : ``;
                            var phone = row.phone ? `<div>Số điện thoại: ${row.phone}</div>` : ``;
                            var real_manage_unit = row.real_manage_unit ? `<div>Đơn vị thực quản lý: ${list_employee.GetRealManageUnit(row.real_manage_unit)}</div>` : ``;
                            var html = employee_name + email + phone + real_manage_unit;
                            return html;
                        }
                    },
                    {
                        title: "Loại tài khoản",
                        align: 'center',
                        valign: 'left',
                        formatter: function(value, row, index) {
                            var html = row.role?.role_name || 'Chưa được phân quyền';
                            return html;
                        }
                    },
                    {
                        title: "Trạng thái",
                        align: 'center',
                        valign: 'left',
                        formatter: function(value, row, index) {
                            var html = list_employee.GetStatus(row.is_active || 0, row.id);
                            return html;
                        }
                    },
                    {
                        title: "Chức năng",
                        valign: 'middle',
                        align: 'center',
                        class: 'CssAction',
                        formatter: function(value, row, index) {
                            var btnActive = true ? `` : `<a data-bs-original-title="${row.is_active == 1 ? 'Kich hoat' : 'Vo hieu hoa'}" data-active="${row.is_active}"  data-bs-toggle="tooltip" data-bs-placement="top"  class="btnTooltip btnDeactive mb-1 me-1 btn ${row.is_active == 2 ? 'btn-danger btnUnlock': 'btn-success btnUnlock'} btn-sm" data-id="${row.id}"><i class="bx bx-lock-alt"></i></a>`;
                            var btnDelete = `<a  data-bs-toggle="tooltip" data-bs-placement="top" class="btnTooltip btn btn-danger btn-sm btnDelete mb-1" data-id="${row.id}" data-bs-original-title="Xóa"><i class="bx bx-trash"></i></a>`;
                            var btnEdit = `<a data-bs-original-title="Cập nhật" data-bs-toggle="tooltip" data-bs-placement="top" data-role_group_id="${row.role_group_id}" data-username="${row.username}" data-real_manage_unit="${row.real_manage_unit}" data-id="${row.id}" data-mobile="${row.phone}" data-is_active="${row.is_active}" data-email="${row.email}" data-address="${row.address}" data-last_name="${row.lastname}" data-firstname="${row.firstname}" class="btnTooltip btn btn-sm btn-info btnEdit me-1 mb-1" ><i class="bx bx-pencil"></i></a>`;
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
    list_employee.init();
});
