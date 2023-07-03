var total_errors_import = 0;
$(function() {
    window.list_buildings = {
        init: function() {
            list_buildings.DatetimepickerSetup();
            list_buildings.GetListDataBuildings();
            list_buildings.CreateSingleBuilding();
            list_buildings.UpdateBuildingModal();
            list_buildings.UpdateSingleBuilding();
            list_buildings.DeactiveBuilding();
            list_buildings.SetTooltip();
            list_buildings.ClearModal();
            list_buildings.FormatImask();
            list_buildings.Delete();
            list_buildings.Search();
            list_buildings.CheckInputImportChange();
            list_buildings.SaveImportExcel();
            list_buildings.CheckFileExcelImport();
            $('#partner_id').select2({
                dropdownParent: $('#create_building_modal')
            });
            $('#update_partner_id').select2({
                dropdownParent: $('#update_building_modal')
            });
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
                    url: '/save-file-buildings-data',
                    type: 'post',
                    processData: false,
                    contentType: false,
                    data: data,
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader("_token", $('#_token').val());
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
                            list_buildings.ListBuildingsRefresh();
                            $('#import_excel_modal').modal('hide');
                        } else {
                            toastr.error(message, title);
                        }

                    },
                    error: function(request, status, error) {
                        $('#btn_check_file_excel').html("").html("Kiểm tra").attr('disabled', false);
                        $('#btn_save_excel').html("").html("Lưu lại").attr('disabled', false);
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
                            
                            if(response[0].rows) {
                                $('#error_array_in_file_excel_group').removeClass('d-none');
                                $('#list_errors_in_file').bootstrapTable('destroy');
                                $('#list_errors_in_file').bootstrapTable({
                                    data: response[0].rows,
                                    textUndefined: 'N/A',
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
                                        { field: '0', title: 'Mã tòa nhà' },
                                        { field: '1', title: "Tên tòa nhà"},
                                        { field: '2', title: 'Chủ sở hữu'},
                                        { field: '3', title: 'Địa chỉ'},
                                        { field: '4', title: 'Kinh độ'},
                                        { field: '5', title: 'Vĩ độ'},
                                        { field: '6', title: 'Đầu mối tòa nhà'},
                                        { field: '7', title: 'Số điện thoại đầu mối'},
                                        { field: '8', title: 'Đối tác hạ tầng'},
                                        { field: '9', title: 'Loại hình hợp tác', formatter: function(value, row, index) {
                                            return list_buildings.GetTypeCooperate(row['9']);
                                        }},
                                        { field: '10', title: 'Tỉ lệ chia sẻ'},
                                        // { field: '11', title: "Trạng thái", formatter: function(value, row, index) {
                                        //     return list_buildings.GetStatus(row['11']);
                                        // }},

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
                                else {
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
        Search: function() {
            $(document).on('click', '.btnSearch', function() {
                list_buildings.ListBuildingsRefresh();
            });
        },
        Delete: function() {
            $(document).on('click', '.btnDelete', function() {
                var url = '/delete-building';
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
                                "id": id,
                                "_token": $('#_token').val()
                            },
                            success: function(res) {
                                var message = res.message || "Lỗi";
                                var title = "Lôi";
                                if (res.success) {
                                    title = "Thành công";
                                    toastr.success(message, title);
                                    list_buildings.ListBuildingsRefresh();
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
        FormatImask: function() {
            IMask(document.getElementById("percent_share"), {
                mask: Number, // enable number mask
                min: 0,
                max: 100,
                thousandsSeparator: ''
            });

            IMask(document.getElementById("update_percent_share"), {
                mask: Number, // enable number mask
                min: 0,
                max: 100,
                thousandsSeparator: ''
            });
        },
        DatetimepickerSetup: function() {
            $('#contract_date, #update_contract_date').datetimepicker({
                autoclose: true,
                dateFormat: 'yy-mm-dd',
                todayBtn: true,
                todayHighlight: true,
                weekStart: 1,
                format: 'dd-mm-yyyy',
                minView: 2,
                viewMode: 'days',
            });
        },
        SetTooltip: function() {
            $(document).on('mouseover', '.btnTooltip', function() {
                $(this).tooltip();
            });
        },
        DeactiveBuilding: function() {
            $(document).on('click', '.btnActive', function() {
                var url = '/deactive-building';
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
                                    list_buildings.ListBuildingsRefresh();
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
        UpdateBuildingModal: function() {
            $(document).on('click', '.btnEdit', function() {
                var id = $(this).data('id');
                var building_code = $(this).data('building_code');
                var building_name = $(this).data('building_name');
                var building_company = $(this).data('building_company');
                var building_address = $(this).data('building_address');
                var building_longitude = $(this).data('building_longitude');
                var building_latitude = $(this).data('building_latitude');
                var contact_name = $(this).data('contact_name');
                var contact_phone = $(this).data('contact_phone');
                var partner_id = $(this).data('partner_id');
                var cooperate_type = $(this).data('cooperate_type');
                var percent_share = $(this).data('percent_share');
                var is_active = $(this).data('is_active');

                $('#building_id').val(id);
                $('#update_building_code').val(building_code);
                $('#update_building_name').val(building_name);
                $('#update_building_company').val(building_company);
                $('#update_building_address').val(building_address);
                $('#update_building_longitude').val(building_longitude);
                $('#update_building_latitude').val(building_latitude);
                $('#update_contact_name').val(contact_name);
                $('#update_contact_phone').val(contact_phone);
                $('#update_partner_id').val(partner_id).trigger('change');
                $('#update_cooperate_type').val(cooperate_type).change();
                $('#update_percent_share').val(percent_share);
                $('#update_is_active').val(is_active).change();

                $('#update_building_modal').modal('show');
            });
        },
        ListBuildingsRefresh: function() {
            $('#list_buildings_table').bootstrapTable('refresh');
        },
        CreateSingleBuilding: function() {
            $('#create_building_form').validate({
                rules: {
                    building_code: {
                        required: true
                    },
                    building_name: {
                        required: true
                    },
                    // building_company: {
                    //     required: true,
                    // },
                    building_address: {
                        required: true,
                    },
                    building_longitude: {
                        required: true
                    },
                    building_latitude: {
                        required: true
                    },
                    contract_code: {
                        required: true,
                    },
                    contract_date: {
                        required: true,
                    },
                    // contact_name: {
                    //     required: true
                    // },
                    contact_phone: {
                        // required: true,
                        number: true,
                        minlength: 10,
                        maxlength: 10
                    },
                    partner_id: {
                        required: true,
                    },
                    cooperate_type: {
                        required: true,
                    },
                    percent_share: {
                        required: true,
                        number: true,
                        min: 0,
                        max: 100
                    },
                    is_active: {
                        required: true,
                    },
                },
                messages: {
                    building_code: {
                        required: "Thiếu mã tòa nhà"
                    },
                    building_name: {
                        required: "Thiếu tên tòa nhà"
                    },
                    // building_company: {
                    //     required: "Thiếu tên chủ sở hữ",
                    // },
                    building_address: {
                        required: "Thiếu địa chỉ",
                    },
                    building_longitude: {
                        required: "Thiếu kinh độ"
                    },
                    building_latitude: {
                        required: "Thiếu vĩ độ"
                    },
                    contract_code: {
                        required: "Thiếu mã phụ lục hợp đồng",
                    },
                    contract_date: {
                        required: "Thiếu ngày ký hợp đồng",
                    },
                    contact_name: {
                        required: "Thiếu đầu mối tòa nhà"
                    },
                    contact_phone: {
                        required: "Thiếu số điện thoại đầu mối",
                        number: "Số điện thoại chỉ bao gồm số",
                        minlength: "Số điện thoại phải đúng 10 ký tự",
                        maxlength: "Số điện thoại phải đúng 10 ký tự"
                    },
                    partner_id: {
                        required: "Thiếu mã đối tác",
                    },
                    partner_infrastructure: {
                        required: "Thiếu đối tác hạ tầng",
                    },
                    cooperate_type: {
                        required: "Thiếu loại hình hợp tác",
                    },
                    percent_share: {
                        required: "Thiếu tỉ lệ chia sẻ",
                        number: "Tỉ lệ chia sẻ phải là số từ 0 -> 100",
                        min: "Tỉ lệ chia sẻ nhỏ nhất là 0 và lớn nhất 100",
                        max: "Tỉ lệ chia sẻ nhỏ nhất là 0 và lớn nhất 100"
                    },
                    is_active: {
                        required: "Thiếu trạng thái tòa nhà",
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
                errorPlacement: function (error, element) {
                    if (element.attr("name") == "partner_id")
                        error.insertAfter("#partner_id_group");
                    else
                        error.insertAfter(element);
                },
                submitHandler: function(form, event) {
                    event.preventDefault();
                    var data = new FormData();
                    data.append('building_code', $('#building_code').val() || "");
                    data.append('building_name', $('#building_name').val() || "");
                    data.append('building_company', $('#building_company').val() || "");
                    data.append('building_address', $('#building_address').val() || "");
                    data.append('building_longitude', $('#building_longitude').val() || "");
                    data.append('building_latitude', $('#building_latitude').val() || "");
                    data.append('contract_code', $('#contract_code').val() || "");
                    data.append('contract_date', moment($('#contract_date').val(), 'DD-MM-YYYY').format('YYYY-MM-DD') || "");
                    data.append('contact_name', $('#contact_name').val() || "");
                    data.append('contact_phone', $('#contact_phone').val() || "");
                    data.append('partner_id', $('#partner_id').val() || "");
                    data.append('cooperate_type', $('#cooperate_type').val() || "");
                    data.append('percent_share', $('#percent_share').val() || "");
                    data.append('is_active', $('#is_active').val() || "");

                    data.append('_token', $('#_token').val());

                    $.ajax({
                        url: form.action,
                        processData: false,
                        contentType: false,
                        type: form.method,
                        data: data,
                        beforeSend: function() {
                            var html = '<i class="bx bx-loader-circle loading"></i> đang lưu';
                            $('#btn_create_single_building').html("").html(html).attr("disabled", true);
                        },
                        success: function(response) {
                            var message = response.message || "";
                            var title = "Lỗi";
                            var type = "error";
                            $('#btn_create_single_building').html("").html("Thêm mới").attr('disabled', false);
                            if (response.success) {
                                type = "success";
                                title = "Thành công";
                                toastr.success(message, title);
                                list_buildings.ListBuildingsRefresh();
                                $('#create_building_modal').modal('hide');
                            } else {
                                toastr.error(message, title);
                            }

                        },
                        error: function(request, status, error) {
                            $('#btn_create_single_building').html("").html("Thêm mới").attr('disabled', false);
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
        UpdateSingleBuilding: function() {
            $('#update_building_form').validate({
                rules: {
                    update_building_code: {
                        required: true
                    },
                    update_building_name: {
                        required: true
                    },
                    // update_building_company: {
                    //     required: true,
                    // },
                    update_building_address: {
                        required: true,
                    },
                    update_building_longitude: {
                        required: true
                    },
                    update_building_latitude: {
                        required: true
                    },
                    update_contact_phone: {
                        number: true,
                        minlength: 10,
                        maxlength: 10
                    },
                    update_partner_id: {
                        required: true,
                    },
                    update_cooperate_type: {
                        required: true,
                    },
                    update_percent_share: {
                        required: true,
                        number: true,
                        min: 0,
                        max: 100
                    },
                    update_is_active: {
                        required: true,
                    },
                },
                messages: {
                    update_building_code: {
                        required: "Thiếu mã tòa nhà"
                    },
                    update_building_name: {
                        required: "Thiếu tên tòa nhà"
                    },
                    // update_building_company: {
                    //     required: "Thiếu tên chủ sở hữ",
                    // },
                    update_building_address: {
                        required: "Thiếu địa chỉ",
                    },
                    update_building_longitude: {
                        required: "Thiếu kinh độ"
                    },
                    update_building_latitude: {
                        required: "Thiếu vĩ độ"
                    },
                    update_contact_name: {
                        required: "Thiếu đầu mối tòa nhà"
                    },
                    update_contact_phone: {
                        required: "Thiếu số điện thoại đầu mối",
                        number: "Số điện thoại chỉ bao gồm số",
                        minlength: "Số điện thoại phải đúng 10 ký tự",
                        maxlength: "Số điện thoại phải đúng 10 ký tự"
                    },
                    update_partner_id: {
                        required: "Thiếu đối tác hạ tầng",
                    },
                    update_cooperate_type: {
                        required: "Thiếu loại hình hợp tác",
                    },
                    update_percent_share: {
                        required: "Thiếu tỉ lệ chia sẻ",
                        number: "Tỉ lệ chia sẻ phải là số từ 0 -> 100",
                        min: "Tỉ lệ chia sẻ nhỏ nhất là 0 và lớn nhất 100",
                        max: "Tỉ lệ chia sẻ nhỏ nhất là 0 và lớn nhất 100"
                    },
                    update_is_active: {
                        required: "Thiếu trạng thái tòa nhà",
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
                errorPlacement: function (error, element) {
                    if (element.attr("name") == "update_partner_id")
                        error.insertAfter("#update_partner_id_group");
                    else
                        error.insertAfter(element);
                },
                submitHandler: function(form, event) {
                    event.preventDefault();
                    var data = new FormData();
                    data.append('building_code', $('#update_building_code').val() || "");
                    data.append('building_name', $('#update_building_name').val() || "");
                    data.append('building_company', $('#update_building_company').val() || "");
                    data.append('building_address', $('#update_building_address').val() || "");
                    data.append('building_longitude', $('#update_building_longitude').val() || "");
                    data.append('building_latitude', $('#update_building_latitude').val() || "");
                    data.append('contact_name', $('#update_contact_name').val() || "");
                    data.append('contact_phone', $('#update_contact_phone').val() || "");
                    data.append('partner_id', $('#update_partner_id').val() || "");
                    data.append('cooperate_type', $('#update_cooperate_type').val() || "");
                    data.append('percent_share', $('#update_percent_share').val() || "");
                    data.append('is_active', $('#update_is_active').val() || "");

                    data.append('id', $('#building_id').val() || "");
                    data.append('_token', $('#_token').val());

                    $.ajax({
                        url: form.action,
                        processData: false,
                        contentType: false,
                        type: form.method,
                        data: data,
                        beforeSend: function() {
                            var html = '<i class="bx bx-loader-circle loading"></i> đang lưu';
                            $('#btn_update_single_building').html("").html(html).attr("disabled", true);
                        },
                        success: function(response) {
                            var message = response.message || "";
                            var title = "Lỗi";
                            var type = "error";
                            $('#btn_update_single_building').html("").html("Cập nhật").attr('disabled', false);
                            if (response.success) {
                                type = "success";
                                title = "Thành công";
                                toastr.success(message, title);
                                list_buildings.ListBuildingsRefresh();
                                $('#update_building_modal').modal('hide');
                            } else {
                                toastr.error(message, title);
                            }

                        },
                        error: function(request, status, error) {
                            $('#btn_update_single_building').html("").html("Cập nhật").attr('disabled', false);
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
        GetListDataBuildings: function() {
            $('#list_buildings_table').bootstrapTable({
                url: '/get-list-data-buildings',
                queryParams: function(p) {
                    var param = $.extend(true, {
                        offset: p.offset,
                        limit: p.limit,
                        search_value: $('#search_filter').val(),
                        status: $('#status_filter').val(),
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
                        title: "Thông tin toà nhà",
                        align: 'center',
                        valign: 'left',
                        formatter: function(value, row, index) {
                            var building_code = `<div>Mã tòa nhà: ${row.building_code}</div>`;
                            var building_name = row.building_name ? `<div>Tên tòa nhà: ${row.building_name}</div>` : ``;
                            var building_company = row.building_company ? `<div>Chủ sở hữu: ${row.building_company}</div>` : ``;
                            var html = building_code + building_name + building_company;
                            return html;
                        }
                    },
                    {
                        title: "Thông tin địa chỉ",
                        align: 'center',
                        valign: 'left',
                        formatter: function(value, row, index) {
                            var address = row.address ? `<div>Địa chỉ: ${row.address}</div>` : ``;
                            var building_longitude = row.longitude ? `<div>Kinh độ: ${row.longitude}</div>` : ``;
                            var building_latitude = row.latitude ? `<div>Vĩ độ: ${row.latitude}</div>` : ``;
                            var html = address + building_longitude + building_latitude;
                            return html;
                        }
                    },
                    {
                        title: "Thông tin thêm",
                        align: 'center',
                        valign: 'left',
                        formatter: function(value, row, index) {
                            var percent_share = row.percent_share ? `<div>Tỉ lệ chia sẻ: ${row.percent_share} %</div>` : ``;
                            var partner_infrastructure = row.partner_infrastructure ? `<div>Đối tác hạ tầng: ${row.partner_infrastructure}</div>` : ``;
                            var cooperate_type = row.cooperate_type ? `<div>Loại hình hợp tác: ${list_buildings.GetTypeCooperate(row.cooperate_type)} </div>` : ``;
                            var contract_waiting_sign = row.contract_await_sign.length > 0 ? `<div>${row.contract_await_sign.length} hợp đồng chờ ký</div>` : ``;
                            var contract_active = row.contract_active.length > 0 ? `<div>${row.contract_active.length} hợp đồng đang hoạt động </div>` : ``;
                            var contract_expired = row.contract_expired.length > 0 ? `<div>${row.contract_expired.length} hợp đồng hết hạn </div>` : ``;
                            var html = percent_share + partner_infrastructure + cooperate_type + contract_waiting_sign + contract_active + contract_expired;
                            return html;
                        }
                    },
                    {
                        title: "Thông tin liên hệ",
                        align: 'center',
                        valign: 'left',
                        formatter: function(value, row, index) {
                            var contact_name = row.contact_name ? `<div>Đầu mối tòa nhà: ${row.contact_name}</div>` : ``;
                            var contact_phone = row.contact_phone ? `<div>SDT liên hệ: ${row.contact_phone}</div>` : ``;
                            var html = contact_name + contact_phone;
                            return html;
                        }
                    },
                    {
                        title: "Trạng thái",
                        align: 'center',
                        valign: 'left',
                        formatter: function(value, row, index) {
                            var html = list_buildings.GetStatus(row.is_active, row.id);
                            return html;
                        }
                    },
                    {
                        title: "Chức năng",
                        valign: 'middle',
                        align: 'center',
                        class: 'CssAction',
                        formatter: function(value, row, index) {
                            var btnActive = true ? `` : `<a data-bs-original-title="${row.is_active == 1 ? 'Kích hoạt' : 'Vô hiệu hóa'}" data-active="${row.is_active}"  data-bs-toggle="tooltip" data-bs-placement="top"  class="btnTooltip btnDeactive mb-1 me-1 btn ${row.is_active == 0 ? 'btn-danger btnUnlock': 'btn-success btnUnlock'} btn-sm" data-id="${row.id}"><i class="bx bx-lock-alt"></i></a>`;
                            var btnDelete = `<a  data-bs-toggle="tooltip" data-bs-placement="top" class="btnTooltip btn btn-danger btn-sm btnDelete mb-1" data-id="${row.id}" data-bs-original-title="Xóa"><i class="bx bx-trash"></i></a>`;
                            var btnEdit = `<a data-bs-original-title="Cập nhật" data-bs-toggle="tooltip" data-bs-placement="top"  data-id="${row.id}" data-building_code="${row.building_code}" data-building_name="${row.building_name}" data-building_company="${row.building_company}" data-building_address="${row.address}" data-building_longitude="${row.longitude}" data-building_latitude="${row.latitude}" data-contact_name="${row.contact_name}" data-contact_phone="${row.contact_phone}" data-partner_id="${row.partner_id}" data-cooperate_type="${row.cooperate_type}" data-percent_share="${row.percent_share}" data-is_active="${row.is_active}"  class="btnTooltip btn btn-sm btn-info btnEdit me-1 mb-1" ><i class="bx bx-pencil"></i></a>`;
                            var action = btnEdit + btnDelete;
                            return action;
                        },
                    }
                ],
                formatNoMatches: function() {
                    return 'Chưa có dữ liệu';
                },
            });
        },
        GetTypeCooperate: function(type_cooperate = 1) {
            const TYPE = {
                1: "Mobifone tự triển khai",
                2: 'Một phần',
                3: 'Toàn trình',
            };

            if(! TYPE.hasOwnProperty(type_cooperate)) {
                return 'N/A';
            }

            var html = '<span class="mx-1 font-size-12 p-2">' + TYPE[type_cooperate] + '</span>';
            return html;
        },
        GetStatus: function(status, id) {
            const STATUS = {
                2: "Đã vô hiệu hóa",
                1: 'Đã kích hoạt',
            };

            if(! STATUS.hasOwnProperty(status)) {
                return 'N/A';
            }

            var flag = "danger";
            if (status == 1) {
                flag = "success";
            }
            var html = `<span data-id="${id}" data-bs-original-title="${status == 1 ? 'Kích hoạt' : 'Vô hiệu hóa'}" data-active="${status}" data-bs-toggle="tooltip" data-bs-placement="top"  class="btnTooltip btn btnActive btn-${status == 1 ? 'success' : 'danger'} mx-1 badge bg-${flag} font-size-12 p-2">${STATUS[status]}</span>`;
            return html;
        },
        ClearModal: function() {
            $(document).on('hidden.bs.modal', '.modal', function() {
                $(this).find("input[type=text],input[type=file],input[type=email],input[name=id],textarea,select")
                    .val('')
                    .end();
                $('#update_building_form')?.validate().resetForm();
                $('#create_building_form')?.validate().resetForm();
                $('#error_array_in_file_excel_group').addClass('d-none');
                $('#list_building_errors_in_file').bootstrapTable('destroy').html("");
                // $('#btn_save_excel_employee').hide();
                // $('#btn_check_file_excel_employee').show();
                $(".error").removeClass("error");
                // .find("input[type=checkbox], input[type=radio]")
                // .prop("checked", "")
                // .end();
            })
        },
    }
});

$(document).ready(function() {
    list_buildings.init();
})
