var total_errors_import = 0;

$(function () {
    window.list_contracts = {
        init: function () {
            list_contracts.DatetimepickerSetup();
            list_contracts.GetListDataContracts();
            list_contracts.CreateSingleContract();
            list_contracts.FormatImask();
            list_contracts.UpdateContractModal();
            list_contracts.UpdateSingleContract();
            list_contracts.DeActiveContracts();
            list_contracts.SetTooltip();
            list_contracts.Search();
            list_contracts.ClearModal();
            list_contracts.CheckInputImportChange();
            list_contracts.SaveImportExcel();
            list_contracts.CheckFileExcelImport();
            list_contracts.Delete();
            list_contracts.CheckTypeCooperate();
            list_contracts.SetupSelect2();
            list_contracts.ViewCccd();
            list_contracts.GetAddressOfCustomer();
            list_contracts.ForwardToEquipmentInstallationJob();
            list_contracts.GetEngineerEmployeeInCreateForm();
            list_contracts.CreateSingleJob();
            list_contracts.GetDataAddressByCustomerId();
            $('#update_package_id').select2({
                dropdownParent : $('#update_contract_modal')
            });
            $('#package_id').select2({
                dropdownParent : $('#create_contract_modal')
            });
            $('#update_package_id').change(function(){
                var prices = $(this).find(':selected').data('prices');
                var prices_vat = $(this).find(':selected').data('prices_vat');
                $('#update_bill_price').val(accounting.formatMoney(prices, "", 0, ".", ",", "%v%s"));
                $('#update_bill_price_vat').val(accounting.formatMoney(prices_vat, "", 0, ".", ",", "%v%s"));
            });
            $('#package_id').change(function(){
                var prices = $(this).find(':selected').data('prices');
                var prices_vat = $(this).find(':selected').data('prices_vat');
                $('#bill_price').val(accounting.formatMoney(prices, "", 0, ".", ",", "%v%s"));
                $('#bill_price_vat').val(accounting.formatMoney(prices_vat, "", 0, ".", ",", "%v%s"));
            });

            $('#partner_id').change(function () {
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
                                html += '<option value="' + ele.id + '">' + ele.building_name + ' (' + ele.building_code + ') - ' + ele.percent_share +'% </option>';
                            });
                        }
                        $('#building_id').html("").html(html);
                        $('#building_id').select2({
                            dropdownParent: $('#create_contract_modal')
                        })
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
                        }
                        $('#update_building_id').html("").html(html);
                        $('#update_building_id').select2({
                            dropdownParent: $('#update_contract_modal')
                        })
                    }
                });
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

            $(document).on('change', '#job_customer_id', function() {
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
                        $('#job_address_id').html("").html(html);
                    }
                });                
            });
        },
        GetEngineerEmployeeInCreateForm: function() {
            $(document).on('change', '#job_employee_ids', function() {
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
        GetAddressOfCustomer: function() {
            $(document).on('change', '#customer_id', function() {
                var customer_id = $(this).val();
                if ( ! customer_id ) return ;
                $.ajax({
                    url: '/get-data-address',
                    type: 'get',
                    dataType: 'json',
                    data: {
                        customer_id: customer_id,
                        "_token": $('#_token').val()
                    },
                    success: function (res) {
                        var html = '<option value="">-- Chọn --</option>';
                        if (res != null && res.rows != null && res.rows.length > 0) {
                            $.each(res.rows, function (i, ele) {
                                html += '<option value="' + ele.id + '">' + ele.address + '</option>';
                            });
                        }
                        $('#address_id').html("").html(html);
                        $('#address_id').select2({
                            dropdownParent: $('#create_contract_modal')
                        })
                    }
                })
            });

            $(document).on('change', '#update_customer_id', function() {
                var customer_id = $(this).val();
                if ( ! customer_id ) return ;
                $.ajax({
                    url: '/get-data-address',
                    type: 'get',
                    dataType: 'json',
                    data: {
                        customer_id: customer_id,
                        "_token": $('#_token').val()
                    },
                    success: function (res) {
                        var html = '<option value="">-- Chọn --</option>';
                        if (res != null && res.rows != null && res.rows.length > 0) {
                            $.each(res.rows, function (i, ele) {
                                html += '<option value="' + ele.id + '">' + ele.address + '</option>';
                            });
                        }
                        $('#update_address_id').html("").html(html);
                        $('#update_address_id').select2({
                            dropdownParent: $('#update_contract_modal')
                        })
                    }
                })
            });
        },
        ViewCccd: function() {
            $(document).on('click', '.btnViewCccd', function() {
                var cccd_backside = $(this).data('cccd_backside_url');
                var cccd_front = $(this).data('cccd_front_url');
                $('#cccd_backside_link').attr("href", cccd_backside);
                $('#cccd_backside_img').attr("src", cccd_backside);

                $('#cccd_front_link').attr("href", cccd_front);
                $('#cccd_front_img').attr("src", cccd_front);

                $('#view_cccd_modal').modal('show');
            });
        },
        SetupSelect2: function() {
            $('#partner_id').select2({
                dropdownParent: $('#create_contract_modal')
            });
            $('#update_partner_id').select2({
                dropdownParent: $('#update_contract_modal')
            });
            $('#customer_id').select2({
                dropdownParent: $('#create_contract_modal')
            });
            $('#update_customer_id').select2({
                dropdownParent: $('#update_contract_modal')
            });
            $('#device_id').select2({
                dropdownParent: $('#create_contract_modal')
            });
            $('#package_id').select2({
                dropdownParent: $('#create_contract_modal')
            });
            $('#employee_id').select2({
                dropdownParent: $('#create_contract_modal')
            });
            $('#update_device_id').select2({
                dropdownParent: $('#update_contract_modal')
            });
            $('#update_employee_id').select2({
                dropdownParent: $('#update_contract_modal')
            });

            $('#job_partner_id').select2({
                dropdownParent: $('#forward_to_equipment_installation_job_modal')
            });
            $('#job_employee_ids').select2({
                dropdownParent: $('#forward_to_equipment_installation_job_modal')
            });
            $('#job_customer_id').select2({
                dropdownParent: $('#forward_to_equipment_installation_job_modal')
            }).attr('disabled', true);
        },
        CheckTypeCooperate: function() {
            $(document).on('change', '#type_cooperate', function() {
                var type_cooperate = $(this).val();
                if ( type_cooperate != 1 ) {
                    $('#building_id').attr("disabled", false);
                    return;
                }
                else {
                    $('#building_id').val("").change().attr("disabled", true);
                }
            });

            $(document).on('change', '#update_type_cooperate', function() {
                var type_cooperate = $(this).val();
                if ( type_cooperate != 1 ) {
                    $('#update_building_id').attr("disabled", false);
                    return;
                }
                else {
                    $('#update_building_id').val("").change().attr("disabled", true);
                }
            });
        },
        ClearPreviewImage: function() {
            $('#atag-preview').attr('href', "")
            $('#preview').attr('src', "");
            $('.preview-image-group').addClass('d-none');
            $('.preview_image').addClass('d-none');
            $('.pdf_preview').addClass('d-none');
            $('#a_pdf_preview').attr('src', "");
        },
        RemoveFileUpload: function() {
            $(document).on("click", '.btnRemoveFileUpload', function() {
                list_contracts.ClearPreviewImage();
                $('#attachFileContract').val('');
            });
        },
        PreviewImage: function() {
            $(document).on('change', '#attachFileContract', function() {
                list_contracts.ImportImage(this);
            });
            // $(document).on('change', '#update_attachFileContract', function() {
            //     list_contracts.ImportImageUpdate(this);
            // });
        },
        ImportImageUpdate: function(input) {
            let fileReference = input.files && input.files[0];

            if (fileReference) {
                var reader = new FileReader();

                reader.onload = (event) => {
                    $('#update_atag-preview').attr('href', event.target.result)
                    $('#update_preview').attr('src', event.target.result);
                    $('#update_preview-image-group').removeClass('d-none');
                }

                reader.readAsDataURL(fileReference);
            }
        },
        ImportImage: function(input) {
            let fileReference = input.files && input.files[0];
            var type = fileReference.type;
            var list_types_image = [
                'image/png',
                "image/jpeg",
                "image/pjpeg",
            ];
            if (fileReference && ! list_types_image.includes(type)) {
                list_contracts.ClearPreviewImage();
                var reader = new FileReader();

                reader.onload = (event) => {
                    $('#a_pdf_preview').attr('href', event.target.result).html(fileReference.name);
                    // $('#preview').attr('src', event.target.result);
                    $('.preview-image-group').removeClass('d-none');
                    $('.pdf_preview').removeClass('d-none');
                }

                reader.readAsDataURL(fileReference);
                return ;
            } else if (fileReference) {
                list_contracts.ClearPreviewImage();
                var reader = new FileReader();

                reader.onload = (event) => {
                    $('#atag-preview').attr('href', event.target.result)
                    $('#preview').attr('src', event.target.result);
                    $('.preview-image-group').removeClass('d-none');
                    $('.preview_image').removeClass('d-none');
                }

                reader.readAsDataURL(fileReference);
                return ;
            }
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
                    url: '/save-file-contracts-data',
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
                            list_contracts.ListContractsRefresh();
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

                    data.append('_token', $('#_token').val());

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

                            if(response[0]?.rows && response.success) {
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
                                        { field: '0', title: 'Mã hợp đồng' },
                                        { field: '1', title: 'Mã gói cước' },
                                        { field: '2', title: 'Ngày bắt đầu gói cước', formatter: function(value, row, index) {
                                            return row['2'] ? moment(row['2']).format('DD-MM-YYYY') : 'N/A';
                                        }},
                                        { field: '3', title: 'Mã thiết bị' },
                                        { field: '4', title: 'Mã hóa đơn' },
                                        { field: '5', title: 'Ngày tạo hóa đơn', formatter: function(value, row, index) {
                                            return row['5'] ? moment(row['5']).format('DD-MM-YYYY') : '';
                                        }},
                                        { field: '6', title: 'Mã đối tác' },
                                        { field: '7', title: 'Mã tòa nhà' },
                                        { field: '8', title: 'Mã ID khách hàng' },
                                        { field: '9', title: 'Mã khách hàng' },
                                        { field: '10', title: 'Loại hình hợp tác', formatter: function(value, row, index) {
                                            return  row['10'] ? `${row['10']} - ${list_contracts.GetTypeCooperate(row['10'])}` : 'N/A';
                                        }},
                                        { field: '11', title: 'Mã cửa hàng' },
                                        { field: '12', title: 'Mã nhân viên' },
                                        { field: '13', title: "Trạng thái HD", formatter: function(value, row, index) {
                                            return list_contracts.GetStatus(row['13']);
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
                                else {
                                    total_errors_import = 0;
                                    $('#btn_save_excel').removeClass('d-none');
                                    $('#btn_check_file_excel').addClass('d-none');
                                }
                            } else {
                                toastr.error(message, title);
                            }

                        },
                        error: function(request, status, error) {
                            $('#btn_check_file_excel').html("").html("Kiểm tra").attr('disabled', false);
                            Swal.fire({
                                title: "Lỗi",
                                text: "Lỗi. Tải lại tệp tin để thử lại hoặc liên hệ quản trị viên để xử lý",
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
        Search: function () {
            $(document).on('click', '.btnSearch', function () {
                list_contracts.ListContractsRefresh();
            });
        },
        Delete: function () {
            $(document).on('click', '.btnDeleteContract', function () {
                var url = '/delete-contract';
                var id = $(this).data('id');
                var title = "Xác nhận xóa?";
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
                }).then(function (result) {
                    if (result.value) {
                        $.ajax({
                            url: url,
                            method: 'delete',
                            data: {
                                "_token": $('#_token').val(),
                                "id": id,
                            },
                            success: function (res) {
                                var message = res.message || "Lỗi";
                                var type = "error";
                                var title = "Lôi";
                                if (res.success) {
                                    title = "Thành công";
                                    type = "success";
                                    toastr.success(message, title);
                                    list_contracts.ListContractsRefresh();
                                } else {
                                    toastr.error(message, title);
                                }
                            },
                            error: function () {
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
        SetTooltip: function () {
            $(document).on('mouseover', '.btnTooltip', function () {
                $(this).tooltip();
            });
        },
        DeActiveContracts: function () {
            $(document).on('click', '.btnActive', function () {
                var url = '/deactive-contract';
                var id = $(this).data('id');
                var title = "Xác nhận đổi trạng thái hợp đồng?";
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
                }).then(function (result) {
                    if (result.value) {
                        $.ajax({
                            url: url,
                            method: 'put',
                            data: {
                                "id": id,
                                is_active,
                                "_token": $('#_token').val()
                            },
                            success: function (res) {
                                var message = res.message || "Lỗi";
                                var type = "error";
                                var title = "Lôi";
                                if (res.success) {
                                    title = "Thành công";
                                    type = "success";
                                    toastr.success(message, title);
                                    list_contracts.ListContractsRefresh();
                                } else {
                                    toastr.error(message, title);
                                }
                            },
                            error: function () {
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
        UpdateContractModal: function () {
            $(document).on('click', '.btnEdit', function () {
                var id = $(this).data('id');
                var code = $(this).data('code');
                var package_id = $(this).data('package_id');
                var start_date_package = $(this).data('start_date_package');
                var device_id = $(this).data('device_id');
                var bill_code = $(this).data('bill_code');
                var bill_date = $(this).data('bill_date');
                var bill_prices = $(this).data('bill_prices');
                var bill_prices_vat = $(this).data('bill_prices_vat');
                var address_id = $(this).data('address_id');
                var partner_id = $(this).data('partner_id');
                var building_id = $(this).data('building_id');
                var customer_id = $(this).data('customer_id');
                var customer_code = $(this).data('customer_code');
                var employee_id = $(this).data('employee_id');


                var type_cooperate = $(this).data('type_cooperate');
                var status = $(this).data('status');

                $('#contract_id').val(id);
                $('#update_contract_code').val(code);
                if(package_id != ''){
                    $('#update_package_id').val(package_id).trigger('change');
                }
                if (start_date_package) {
                    $('#update_start_date_package').datetimepicker('setDate', new Date(start_date_package));
                }
                $('#update_device_id').val(device_id).change();
                $('#update_bill_code').val(bill_code);
                $('#update_bill_date').datetimepicker('setDate', new Date(bill_date));
                $('#update_bill_price').val(accounting.formatMoney(bill_prices, "", 0, ".", ",", "%v%s"));
                $('#update_bill_price_vat').val(accounting.formatMoney(bill_prices_vat, "", 0, ".", ",", "%v%s"));
                $('#update_customer_code').val(customer_code);
                $('#update_partner_id').val(partner_id).trigger('change');
                $('#update_employee_id').val(employee_id).trigger('change');

                $('#update_customer_id').val(customer_id).change();
                setTimeout(function(){
                    $('#update_address_id').val(address_id).trigger('change');
                }, 1000);
                $('#update_type_cooperate').val(type_cooperate).change();
                if (type_cooperate == 1) {
                    $('#update_building_id').val("").attr("disabled", true);
                } else {
                    setTimeout(function(){
                        $('#update_building_id').val(building_id).trigger('change');
                    }, 500);

                    $('#update_building_id').attr("disabled", false);
                }
                $('#update_contract_status').val(status).change();
                // list_contracts.FormatImask();

                $('#update_contract_modal').modal('show');
            });
        },
        FormatImask: function () {
            IMask(document.getElementById("bill_price"), {
                mask: Number, // enable number mask

                // other options are optional with defaults below
                scale: 0, // digits after point, 0 for integers
                signed: false, // disallow negative
                thousandsSeparator: ',', // any single char
                padFractionalZeros: false, // if true, then pads zeros at end to the length of scale
                normalizeZeros: true, // appends or removes zeros at ends
                radix: ',', // fractional delimiter
                mapToRadix: ['.'], // symbols to process as radix

                // additional number interval options (e.g.)
                min: 0,
                max: 10000000000000000
            });

            IMask(document.getElementById("update_bill_price"), {
                mask: Number, // enable number mask

                // other options are optional with defaults below
                scale: 0, // digits after point, 0 for integers
                signed: false, // disallow negative
                thousandsSeparator: ',', // any single char
                padFractionalZeros: false, // if true, then pads zeros at end to the length of scale
                normalizeZeros: true, // appends or removes zeros at ends
                radix: ',', // fractional delimiter
                mapToRadix: ['.'], // symbols to process as radix

                // additional number interval options (e.g.)
                min: 0,
                max: 10000000000000000
            });
        },
        ListContractsRefresh: function () {
            $('#list_contracts_table').bootstrapTable('refresh');
        },
        CreateSingleContract: function () {
            $('#create_contract_form').validate({
                rules: {
                    contract_code: {
                        required: true
                    },
                    package_id: {
                        required: true,
                    },
                    start_date_package: {
                        required: true,
                    },
                    device_id: {
                        // required: false
                    },
                    building_id: {
                        required: function() {
                            return parseInt($('#type_cooperate').val()) != 1;
                        }
                    },
                    bill_code: {
                        required: false,
                    },
                    bill_date: {
                        required: false,
                    },
                    bill_price: {
                        required: false,
                    },
                    develop_name: {
                        required: true,
                    },
                    partner_id: {
                        required: true,
                    },
                    address_id: {
                        required: true
                    },
                    customer_id: {
                        required: true,
                    },
                    type_cooperate: {
                        required: true,
                    },
                    contract_status: {
                        required: true,
                    },
                    shopcode: {
                        // required: true,
                    },
                    employee_id: {
                        required: true,
                    },
                    customer_code: {
                        required: true,
                    }
                },
                messages: {
                    contract_code: {
                        required: "Thiếu mã hợp đồng"
                    },
                    package_id: {
                        required: "Thiếu gói cước",
                    },
                    start_date_package: {
                        required: "Thiếu ngày đăng ký",
                    },
                    device_id: {
                        // required: "Thiếu thông tin thiết bị"
                    },
                    building_id: {
                        required: "Thiếu toà nhà"
                    },
                    bill_code: {
                        required: "Thiếu mã hóa đơn",
                    },
                    bill_date: {
                        required: "Thiếu ngày hóa đơn",
                    },
                    bill_price: {
                        required: "Thiếu số tiền",
                    },
                    develop_name: {
                        required: "Thiếu đơn vị nhà phát triển",
                    },
                    partner_id: {
                        required: "Thiếu đối tác hạ tầng",
                    },
                    address_id: {
                        required: "Thiếu địa chỉ",
                    },
                    customer_id: {
                        required: "Thiếu khách hàng",
                    },
                    type_cooperate: {
                        required: "Thiếu loại hình hợp tác",
                    },
                    contract_status: {
                        required: "Thiếu trạng thái hợp đồng",
                    },
                    employee_id: {
                        required: "Thiếu mã nhân viên",
                    },
                    customer_code: {
                        required: "Thiếu mã khách hàng",
                    }
                },
                onkeyup: false,
                onblur: true,
                onfocusout: false,
                invalidHandler: function (form, validator) {
                    var errors = validator.numberOfInvalids();
                    if (errors) {
                        validator.errorList[0].element.focus();
                    }
                },
                errorPlacement: function (error, element) {
                    if (element.attr("name") == "partner_id")
                        error.insertAfter("#partner_id_group");
                    else if (element.attr("name") == "customer_id")
                        error.insertAfter("#customer_id_group");
                    else if (element.attr("name") == "package_id")
                        error.insertAfter("#package_id_group");
                    else if (element.attr("name") == "address_id")
                        error.insertAfter("#address_id_group");
                    else if (element.attr("name") == "employee_id")
                        error.insertAfter("#employee_id_group");
                    else
                        error.insertAfter(element);
                },
                submitHandler: function (form, event) {
                    event.preventDefault();
                    var data = new FormData();
                    data.append('contract_code', $('#contract_code').val() || "");
                    data.append('package_id', $('#package_id').val() || "");
                    data.append('start_date_package', $('#start_date_package').val() ? moment($('#start_date_package').val(), 'DD-MM-YYYY').format('YYYY-MM-DD') : "");
                    data.append('device_id', $('#device_id').val() || "");
                    data.append('bill_code', $('#bill_code').val() || "");
                    data.append('bill_date', $('#bill_date').val() ? moment($('#bill_date').val(), 'DD-MM-YYYY').format('YYYY-MM-DD') : "");
                    data.append('bill_price', ($('#bill_price').val() || 0).toString().replace(/,(?=\d{3})/g, ''));
                    data.append('develop_name', $('#develop_name').val() || "");
                    data.append('partner_id', $('#partner_id').val() || "");
                    data.append('building_id', $('#building_id').val() || "");
                    data.append('address_id', $('#address_id').val() || "");
                    data.append('customer_id', $('#customer_id').val() || "");
                    data.append('type_cooperate', $('#type_cooperate').val() || "");
                    data.append('contract_status', $('#contract_status').val() || "");
                    data.append('shopcode', $('#shopcode').val() || "");
                    data.append('employee_id', $('#employee_id').val() || "");
                    data.append('customer_code', $('#customer_code').val() || "");
                    data.append('_token', $('input[name="_token"]').val());

                    $.ajax({
                        url: form.action,
                        processData: false,
                        contentType: false,
                        type: form.method,
                        data: data,
                        beforeSend: function () {
                            var html = '<i class="bx bx-loader-circle loading"></i> đang lưu';
                            $('#btn_create_single_contract').html("").html(html).attr("disabled", true);
                        },
                        success: function (response) {
                            var message = response.message || "";
                            var title = "Lỗi";
                            var type = "error";
                            $('#btn_create_single_contract').html("").html("Thêm mới").attr('disabled', false);
                            if (response.success) {
                                type = "success";
                                title = "Thành công";
                                toastr.success(message, title);
                                list_contracts.ListContractsRefresh();
                                $('#create_contract_modal').modal('hide');
                            } else {
                                toastr.error(message, title);
                            }

                        },
                        error: function (request, status, error) {
                            $('#btn_create_single_contract').html("").html("Thêm mới").attr('disabled', false);
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
        UpdateSingleContract: function () {
            $('#update_contract_form').validate({
                rules: {
                    update_contract_code: {
                        required: true
                    },
                    update_package_id: {
                        required: true,
                    },
                    update_start_date_package: {
                        required: true,
                    },
                    update_device_id: {
                        // required: true
                    },
                    update_building_id: {
                        required: function() {
                            return parseInt($('#update_type_cooperate').val()) != 1;
                        }
                    },
                    update_bill_code: {
                        required: false,
                    },
                    update_bill_date: {
                        required: false,
                    },
                    update_bill_price: {
                        required: false,
                    },
                    update_bill_price_vat: {
                        required: true,
                    },
                    update_develop_name: {
                        required: true,
                    },
                    update_partner_id: {
                        required: true,
                    },
                    update_address_id: {
                        required: true,
                    },
                    update_type_cooperate: {
                        required: true,
                    },
                    update_customer_id: {
                        required: true
                    },
                    update_contract_status: {
                        // required: true,
                    },
                    update_employee_id: {
                        required: true,
                    },
                    update_customer_code: {
                        required: true,
                    }
                },
                messages: {
                    update_contract_code: {
                        required: "Thiếu mã hợp đồng"
                    },
                    update_package_id: {
                        required: "Thiếu gói cước",
                    },
                    update_start_date_package: {
                        required: "Thiếu ngày đăng ký",
                    },
                    update_device_id: {
                        // required: "Thiếu thông tin thiết bị"
                    },
                    update_building_id: {
                        required: "Thiếu toà nhà"
                    },
                    update_bill_code: {
                        required: "Thiếu mã hóa đơn",
                    },
                    update_bill_date: {
                        required: "Thiếu ngày hóa đơn",
                    },
                    update_bill_price: {
                        required: "Thiếu số tiền",
                    },
                    update_bill_price_vat: {
                        required: "Thiếu số tiền sau thuế",
                    },
                    update_develop_name: {
                        required: "Thiếu đơn vị nhà phát triển",
                    },
                    update_partner_id: {
                        required: "Thiếu đối tác hạ tầng",
                    },
                    update_address_id: {
                        required: "Thiếu địa chỉ",
                    },
                    update_customer_id: {
                        required: "Thiếu khách hàng",
                    },
                    update_type_cooperate: {
                        required: "Thiếu loại hình hợp tác",
                    },
                    update_contract_status: {
                        required: "Thiếu trạng thái hợp đồng",
                    },
                    update_employee_id: {
                        required: "Thiếu nhân viên",
                    },
                    update_customer_code: {
                        required: "Thiếu mã ID khách hàng",
                    }
                },
                onkeyup: false,
                onblur: true,
                onfocusout: false,
                invalidHandler: function (form, validator) {
                    var errors = validator.numberOfInvalids();
                    if (errors) {
                        validator.errorList[0].element.focus();
                    }
                },
                errorPlacement: function (error, element) {
                    if (element.attr("name") == "update_partner_id")
                        error.insertAfter("#update_partner_id_group");
                    else if (element.attr("name") == "update_customer_id")
                        error.insertAfter("#update_customer_id_group");
                    else if (element.attr("name") == "update_address_id")
                        error.insertAfter("#update_address_id_group");
                    else if (element.attr("name") == "update_employee_id")
                        error.insertAfter("#update_employee_id_group");
                    else
                        error.insertAfter(element);
                },
                submitHandler: function (form, event) {
                    event.preventDefault();
                    var data = new FormData();
                    data.append('contract_code', $('#update_contract_code').val() || "");
                    data.append('package_id', $('#update_package_id').val() || "");
                    data.append('start_date_package', $('#update_start_date_package').val() ? moment($('#update_start_date_package').val(), 'DD-MM-YYYY').format('YYYY-MM-DD') : "");
                    data.append('device_id', $('#update_device_id').val() || "");
                    data.append('bill_code', $('#update_bill_code').val() || "");
                    data.append('bill_date', $('#update_bill_date').val() ? moment($('#update_bill_date').val(), 'DD-MM-YYYY').format('YYYY-MM-DD') : "");
                    // data.append('bill_price', ($('#update_bill_price').val() || 0).toString().replace(/,(?=\d{3})/g, ''));
                    // data.append('bill_price_vat', ($('#update_bill_price_vat').val() || 0).toString().replace(/,(?=\d{3})/g, ''));
                    data.append('develop_name', $('#update_develop_name').val() || "");
                    data.append('partner_id', $('#update_partner_id').val() || "");
                    data.append('building_id', $('#update_building_id').val() || "");
                    data.append('customer_id', $('#update_customer_id').val() || "");
                    data.append('address_id', $('#update_address_id').val() || "");
                    data.append('type_cooperate', $('#update_type_cooperate').val() || "");
                    data.append('contract_status', $('#update_contract_status').val() || "");
                    data.append('shopcode', $('#update_shopcode').val() || "");
                    data.append('employee_id', $('#update_employee_id').val() || "");
                    data.append('customer_code', $('#update_customer_code').val() || "");
                    data.append('id', $('#contract_id').val() || "");
                    data.append('_token', $('#_token').val());

                    $.ajax({
                        url: form.action,
                        processData: false,
                        contentType: false,
                        type: form.method,
                        data: data,
                        beforeSend: function () {
                            var html = '<i class="bx bx-loader-circle loading"></i> đang lưu';
                            $('#btn_update_single_contract').html("").html(html).attr("disabled", true);
                        },
                        success: function (response) {
                            var message = response.message || "";
                            var title = "Lỗi";
                            var type = "error";
                            $('#btn_update_single_contract').html("").html("Cập nhật").attr('disabled', false);
                            if (response.success) {
                                type = "success";
                                title = "Thành công";
                                toastr.success(message, title);
                                list_contracts.ListContractsRefresh();
                                $('#update_contract_modal').modal('hide');
                            } else {
                                toastr.error(message, title);
                            }

                        },
                        error: function (request, status, error) {
                            $('#btn_update_single_contract').html("").html("Cập nhật").attr('disabled', false);
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
        DatetimepickerSetup: function () {
            $('#start_date_package, #sign_date, #bill_date, #update_sign_date, #update_start_date_package, #update_bill_date').datetimepicker({
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
        ForwardToEquipmentInstallationJob: function() {
            $(document).on('click', '.btnForwardJob', function() {
                var id = $(this).data('id');
                var job_customer_id = $(this).data('job_customer_id');
                var job_building_id = $(this).data('job_building_id');
                var job_address_id = $(this).data('job_address_id');
                var job_partner_id = $(this).data('job_partner_id');
                var job_id = $(this).data('job_id');

                $('#job_contract_id').val(id);
                $('#job_customer_id').val(job_customer_id).change();
                $('#job_building_id').val(job_building_id).change().attr('disabled', true);
                setTimeout(() => {
                    $('#job_address_id').attr('disabled', false).val(job_address_id).change().attr('disabled', true);
                }, 500);
                var code = $('#job_customer_id').find(':selected').data('customer_code');
                var code_no_space = code.indexOf(' ') > 0 ? code.replace(/\s+/g, '') : code;
                var job_code = `${code_no_space}_${moment().format('YYYYMMDDHHmm')}`;
                $('#job_type').attr('disabled', false).val("41").change().attr('disabled', true);

                $('#job_id').val(job_id);
                $('#job_code').val(job_code);
                $('#job_partner_id').attr('disabled', false).val(job_partner_id).change().attr('disabled', true);

                $('#forward_to_equipment_installation_job_modal').modal('show');
            });
        },
        CreateSingleJob: function() {
            $('#create_job_form').validate({
                rules: {
                    job_employee_ids: {
                        required: true,
                    },
                    reason: {
                        required: true,
                    }
                },
                messages: {
                    job_employee_ids: {
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
                    if (element.attr("name") == "job_employee_ids" ) {
                        error.insertAfter("#employee_ids_group");
                    }
                    else
                        error.insertAfter(element);
                },
                submitHandler: function(form, event) {
                    event.preventDefault();
                    var data = new FormData();
                    data.append('job_id', $('#job_id').val() || "");
                    data.append('description', $('#description').val() || "");
                    data.append('employee_ids', $('#job_employee_ids').val() || "");
                    data.append('reason', $('#reason').val() || "");
                    data.append('contract_id', $('#job_contract_id').val() || "");
                    data.append('job_code', $('#job_code').val() || "");

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
                                list_contracts.ListContractsRefresh();
                                $('#forward_to_equipment_installation_job_modal').modal('hide');
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
        GetListDataContracts: function () {
            $('#list_contracts_table').bootstrapTable({
                url: '/get-list-data-contracts',
                queryParams: function (p) {
                    var param = $.extend(true, {
                        offset: p.offset,
                        limit: p.limit,
                        type_cooperate: $('#type_cooperate_filter').val(),
                        contract_status: $('#contract_status_filter').val(),
                        search_value: $('#search_filter').val(),
                    }, p);
                    return param;
                },
                destroy: true,
                striped: true,
                sidePagination: 'server',
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
                        formatter: function (value, row, index) {

                            var forward_job = parseInt(row.finished_servey) == 2 ? `<a data-bs-toogle="tooltip" data-bs-original-title="Chuyển sang CV lắp đặt" data-bs-placement="top" class="btnTooltip btnForwardJob mb-1 me-1 btn-success btn btn-sm" data-job_customer_id="${row.job_customer_id || ""}" data-job_id="${row.job_id || ""}" data-job_partner_id="${row.job_partner_id || ""}" data-job_address_id="${row.job_address_id || ""}" data-job_building_id="${row.job_building_id || ""}" data-id="${row.id}"><i class="ri-install-line"></i></a>` : ``;
                            var btnViewCccd = (row.customer && (row.customer.cccd_front_url || row.customer.cccd_backside_url)) ? `<a data-bs-toogle="tooltip" data-bs-original-title="Ảnh giấy tờ" data-bs-placement="top" class="btnTooltip btnViewCccd mb-1 me-1 btn-primary btn btn-sm" data-cccd_front_url="${row.customer.cccd_front_url}" data-cccd_backside_url="${row.customer.cccd_backside_url}" data-id="${row.id}"><i class="bx bx-show"></i></a>` : ``;
                            var btnActive = true ? `` : `<a data-bs-original-title="${row.is_active == 1 ? 'Kích hoạt' : 'Vô hiệu hóa'}" data-active="${row.status}"  data-bs-toggle="tooltip" data-bs-placement="top"  class="btnTooltip btnDeactive mb-1 me-1 btn ${row.status == 0 ? 'btn-danger btnUnlock' : 'btn-success btnUnlock'} btn-sm" data-id="${row.id}"><i class="bx bx-lock-alt"></i></a>`;
                            var btnDelete = `<a  data-bs-toggle="tooltip" data-bs-placement="top" class="btnTooltip btn btn-danger btn-sm btnDeleteContract mb-1" data-id="${row.id}" data-bs-original-title="Xóa"><i class="bx bx-trash"></i></a>`;
                            var btnEdit = `<a data-bs-original-title="Cập nhật" data-bs-toggle="tooltip" data-bs-placement="top"  data-id="${row.id}" data-customer_code="${row.customer_code || ""}" data-address_id="${row.address_id || ""}" data-code="${row.code || ""}" data-package_id="${row.package_id || ""}" data-start_date_package="${row.start_date_package || ""}" data-device_id="${row.device_id || ""}" data-bill_code="${row.bill_code || ""}" data-bill_date="${row.bill_date || ""}" data-bill_prices="${row.bill_prices || ""}" data-bill_prices_vat="${row.bill_prices_vat || ""}" data-employee_id="${row.employee_id || ""}" data-partner_id="${row.partner_id || ""}" data-customer_id="${row.customer_id || ""}" data-building_id="${row.buildings_id || ""}" data-type_cooperate="${row.type_cooperate || ""}" data-status="${row.status || ""}"  class="btnTooltip btn btn-sm btn-info btnEdit me-1 mb-1" ><i class="bx bx-pencil"></i></a>`;
                            var action = forward_job + btnViewCccd + btnEdit + btnDelete;
                            return action;
                        },
                    },
                    {
                        title: "Thông tin hợp đồng",
                        align: 'center',
                        valign: 'left',
                        formatter: function (value, row, index) {
                            var contract_code = `<div>Mã hợp đồng: ${row.code}</div>`;
                            var sign_date = row.sign_date ? `<div>Ngày ký: ${moment(row.sign_date, 'YYYY-MM-DD').format('DD-MM-YYYY')}</div>` : ``;
                            var type_cooperate = row.type_cooperate ? `<div>Loại hình hợp tác: ${list_contracts.GetTypeCooperate(row.type_cooperate)}</div>` : ``;
                            var html = contract_code + sign_date + type_cooperate;
                            return html;
                        }
                    },
                    {
                        title: "Thông tin đối tác",
                        align: 'center',
                        valign: 'left',
                        formatter: function (value, row, index) {
                            var partner_name = row.partner ? `<div>Đối tác: ${row.partner.partner_name}</div>` : ``;
                            var email = row.partner ? `<div>Email: ${row.partner.email}</div>` : ``;
                            var phone = row.partner ? `<div>SDT: ${row.partner.phone}</div>` : ``;
                            var html = email + phone;
                            return html;
                        }
                    },
                    {
                        title: "Thông tin khách hàng",
                        align: 'center',
                        valign: 'left',
                        formatter: function (value, row, index) {
                            var customer_name = row.customer ? `<div>Họ và tên: ${row.customer.last_name ? row.customer.last_name : ``} ${row.customer.firstname}</div>` : ``;
                            var email = row.customer ? `<div>Email: ${row.customer.email}</div>` : ``;
                            var phone = row.customer ? `<div>SDT: ${row.customer.phone}</div>` : ``;
                            var html = customer_name + email + phone;
                            return html;
                        }
                    },
                    {
                        title: "Thông tin gói cước",
                        align: 'center',
                        valign: 'left',
                        formatter: function (value, row, index) {
                            if (row.package && row.start_date_package) {
                                var expire_date = row.package ? moment(row.start_date_package).add(row.package.time_used, 'M') : "";
                                var start_date_package = moment(row.start_date_package);
                                var date_number_in_contract = moment.duration(expire_date.diff(start_date_package)).asDays();
                                var expired_date_number = Math.floor(moment.duration(moment(new Date()).diff(start_date_package)).asDays());
                                var rest_date_in_contract = date_number_in_contract - expired_date_number;
                            }

                            var pakage_name = row.package ? `<div>Tên: ${row.package.package_name} (${row.package.package_code})</div>` : ``;
                            var pakage_price = row.bill_prices ? `<div>Giá trước thuế: ${accounting.formatMoney(row.bill_prices, "đ", 0, ".", ",", "%v%s")}</div>` : ``;
                            var pakage_price_vat = row.bill_prices_vat ? `<div>Giá sau thuế: ${accounting.formatMoney(row.bill_prices_vat, "đ", 0, ".", ",", "%v%s")}</div>` : ``;
                            
                            var time_used = row.package_time_used ? `<div>Thời gian s.d: ${row.package_time_used} tháng </div>` : ``;
                            var start_end = expire_date ? `<div>Từ ${moment(row.start_date_package).format('DD-MM-YYYY')} đến ${moment(expire_date).format('DD-MM-YYYY')} </div>` : ``;
                            var rest_date = row.start_date_package ? `<div>Thời gian HĐ còn lại: ${rest_date_in_contract} ngày </div>` : ``;
                            var html = pakage_name + time_used + start_end + rest_date;
                            return html;
                        }
                    },
                    {
                        title: "Thông tin thiết bị",
                        align: 'center',
                        valign: 'left',
                        formatter: function (value, row, index) {
                            var device_name = row.device ? `<div>Tên TB: ${row.device.device_name}</div>` : ``;
                            var device_serial = row.device ? `<div>Serial: ${row.device.serial}</div>` : ``;
                            var sku = row.device ? `<div>SKU: ${row.device.device_code} </div>` : ``;
                            var html = device_name + device_serial + sku;
                            return html;
                        }
                    },
                    {
                        title: "Thông tin hóa đơn",
                        align: 'center',
                        valign: 'left',
                        formatter: function (value, row, index) {
                            var bill_code = row.bill_code ? `<div>Mã hóa đơn: ${row.bill_code}</div>` : ``;
                            var bill_price = row.bill_prices ? `<div>Số tiền: ${parseFloat(row.bill_prices || 0).toLocaleString('vi-VN', { style: 'currency', currency: 'VND' })}</div>` : ``;
                            var bill_prices_vat = row.bill_prices_vat ? `<div>Số tiền: ${parseFloat(row.bill_prices_vat || 0).toLocaleString('vi-VN', { style: 'currency', currency: 'VND' })}</div>` : ``;
                            var bill_date = row.bill_date ? `<div>Ngày lập: ${moment(row.bill_date).format('DD-MM-YYYY')}</div>` : ``;
                            var html = bill_code + bill_price + bill_prices_vat + bill_date;
                            return html;
                        }
                    },
                    {
                        title: "Thông tin tòa nhà",
                        align: 'center',
                        valign: 'left',
                        formatter: function (value, row, index) {
                            if (row.type_cooperate != 1) {
                                var build_name = row.building ? `<div>Tòa nhà: ${row.building.building_name}</div>` : ``;
                                var contact_name = row.building ? `<div>Liên hệ: ${row.building.contact_name}</div>` : ``;
                                var contact_phone = row.building ? `<div>SDT: ${row.building.contact_phone}</div>` : ``;
                                var percent_share = row.percent_share ? `<div>Tỉ lệ chia sẻ: ${row.percent_share}%</div>` : ``;
                                var html = build_name + contact_name + contact_phone + percent_share;
                            } else {
                                var html = "Mobifone tự triển khai"
                            }
                            return html;
                        }
                    },
                    {
                        title: "Trạng thái",
                        align: 'center',
                        valign: 'left',
                        formatter: function (value, row, index) {
                            var html = list_contracts.GetStatus(row.status, row.id);
                            return html;
                        }
                    },
                ],
                formatNoMatches: function () {
                    return 'Chưa có dữ liệu';
                },
            });
        },
        GetStatus: function (status, id) {
            const STATUS = {
                1: 'Chờ ký',
                2: "Đang sử dụng",
                3: "Đã hết hạn",
            };

            if(! STATUS.hasOwnProperty(status) || !status) {
                return 'N/A';
            }

            var flag = "danger";
            if (status == 1) {
                flag = "primary";
            } else if (status == 2) {
                flag = 'success';
            }
            var html = `<span data-id="${id}" data-active="${status}" data-bs-toggle="tooltip" data-bs-placement="top"  class="btnTooltip btn btnActives btn-${flag} mx-1 badge bg-${flag} font-size-12 p-2">${STATUS[status]}</span>`;
            return html;
        },
        GetTypeCooperate: function (type_cooperate) {
            const TYPE = {
                1: "Mobifone tự triển khai",
                2: 'Một phần',
                3: 'Toàn trình',
            };

            if(! TYPE.hasOwnProperty(type_cooperate) || !type_cooperate) {
                return 'N/A';
            }

            var html = '<span class="mx-1 font-size-12">' + TYPE[type_cooperate] + '</span>';
            return html;
        },
        ClearModal: function () {
            $(document).on('hidden.bs.modal', '.modal', function () {
                $(this).find("input[type=text],input[type=file],input[type=email],input,textarea,select")
                    .val('')
                    .end();
                $('#update_contract_form')?.validate().resetForm();
                $('#create_contract_form')?.validate().resetForm();
                $('#error_array_in_file_excel_group').addClass('d-none');
                $('#list_errors_in_file').bootstrapTable('destroy').html("");

                var name_modal = $(this)[0].id;
                    if (name_modal ==  'forward_to_equipment_installation_job_modal') {
                        $("#job_employee_ids").val('').change(); 
                    }
                $(".error").removeClass("error");

                // .find("input[type=checkbox], input[type=radio]")
                // .prop("checked", "")
                // .end();
            })
        },
    }
});

$(document).ready(function () {
    list_contracts.init();
})
