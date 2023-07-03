var total_errors_import = 0;

$(function () {
    window.list_customers = {
        init: function () {
            list_customers.GetListDataCustomers();
            list_customers.CreateSingleCustomer();
            list_customers.UpdateCustomerModal();
            list_customers.UpdateSingleCustomer();
            // list_customers.DeactiveCustomer();
            list_customers.SetTooltip();
            list_customers.SearchData();
            list_customers.ClearModal();
            list_customers.DeleteSingleCustomer();
            list_customers.CheckInputImportChange();
            list_customers.SaveImportExcel();
            list_customers.CheckFileExcelImport();
            list_customers.ViewCccd();
            list_customers.ViewAddressesModal();
            list_customers.SaveAddress();
            list_customers.ClearFormAddress();
            list_customers.EditAddress();
            list_customers.CancelUpdateAddress();
            list_customers.DeleteSingleAddress();
            list_customers.PreviewImageIdentityNumber();
            $('#sltYear').change(function () {
                var id = $('#inputCustomerIdReport').val();
                list_customers.chart_revenues(id);
            });
            $("#firstname").keyup(function () {
                var str = $(this).val();
                if (str != null && str != '') {
                    str = list_customers.toNonAccentVietnamese(str);
                    var acronym = str.split(/\s/).reduce((response, word) => response += word.slice(0, 1), '');
                    $('#code').val(acronym + '_' + Math.floor((Math.random() * 100) + 1));
                }
                else {
                    ('#code').val('');
                }
            });
            $("#update_firstname").keyup(function () {
                var str = $(this).val();
                if (str != null && str != '') {
                    str = list_customers.toNonAccentVietnamese(str);
                    var acronym = str.split(/\s/).reduce((response, word) => response += word.slice(0, 1), '');
                    $('#update_code').val(acronym + '_' + Math.floor((Math.random() * 100) + 1));
                }
                else {
                    ('#update_code').val('');
                }
            });
        },
        PreviewImageIdentityNumber: function () {
            $(document).on('change', '#cccd_front', function () {
                list_customers.ImportImage(this, 'front');
            });

            $(document).on('change', '#cccd_backside', function () {
                list_customers.ImportImage(this, 'backside');
            });

            $(document).on('change', '#update_cccd_front', function () {
                list_customers.ImportImageUpdate(this, 'front');
            });
            $(document).on('change', '#update_cccd_backside', function () {
                list_customers.ImportImageUpdate(this, 'backside');
            });
        },
        ImportImageUpdate: function (input, flag = 'front') {
            let fileReference = input.files && input.files[0];

            if (fileReference) {
                var reader = new FileReader();

                if (flag == 'front') {

                    reader.onload = (event) => {
                        $('#update_atag-preview_cccd_front').attr('href', event.target.result)
                        $('#update_preview_cccd_front').attr('src', event.target.result);
                        $('.update_preview-cccd_front-group').removeClass('d-none');
                    }
                } else {
                    reader.onload = (event) => {
                        $('#update_atag-preview_cccd_backside').attr('href', event.target.result)
                        $('#update_preview_cccd_backside').attr('src', event.target.result);
                        $('.update_preview-cccd_backside-group').removeClass('d-none');
                    }
                }

                reader.readAsDataURL(fileReference);
            }
        },
        ImportImage: function (input, flag = 'front') {
            let fileReference = input.files && input.files[0];

            if (fileReference) {
                var reader = new FileReader();

                if (flag == 'front') {

                    reader.onload = (event) => {
                        $('#atag-preview_cccd_front').attr('href', event.target.result)
                        $('#preview_cccd_front').attr('src', event.target.result);
                        $('.preview-cccd_front-group').removeClass('d-none');
                    }
                } else {
                    reader.onload = (event) => {
                        $('#atag-preview_cccd_backside').attr('href', event.target.result)
                        $('#preview_cccd_backside').attr('src', event.target.result);
                        $('.preview-cccd_backside-group').removeClass('d-none');
                    }
                }

                reader.readAsDataURL(fileReference);
            }
        },
        DeleteSingleAddress: function () {
            $(document).on('click', '.btnDeleteAddress', function () {
                var url = '/delete-address';
                var id = $(this).data('id');
                var customer_id = $('#customer_addresses_id').val();
                var title = "Xác nhận xóa địa chỉ?";
                debugger
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
                                id,
                                "customer_id": customer_id,
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
                                    list_customers.List_Customer_Addresses_Refresh();
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
        CancelUpdateAddress: function () {
            $(document).on('click', '#btnAddressCancel', function () {
                list_customers.ClearFormAddress();
            });
        },
        EditAddress: function () {
            $(document).on('click', '.btnEditAddress', function () {
                var address = $(this).data('address');
                var longitude = $(this).data('longitude');
                var latitude = $(this).data('latitude');
                var address_id = $(this).data('id');
                $('#address_id').val(address_id);
                $('#address_text').val(address);
                $('#longitude').val(longitude);
                $('#latitude').val(latitude);
                $('#btnAddAddress').addClass('d-none');
                $('#btnUpdateAddress').removeClass('d-none');
                $('#btnAddressCancel').removeClass('d-none');
            });
        },
        ClearFormAddress: function () {
            $('#address_id').val("");
            $('#address_text').val("");
            $('#longitude').val("");
            $('#latitude').val("");
            $('#btnAddAddress').removeClass('d-none');
            $('#btnUpdateAddress').addClass('d-none');
            $('#btnAddressCancel').addClass('d-none');
        },
        List_Customer_Addresses_Refresh: function () {
            $('#list_addresses_table').bootstrapTable('refresh');
        },
        GetListDataAddress: function (customer_id) {
            $('#list_addresses_table').bootstrapTable({
                url: '/get-data-address',
                queryParams: function (p) {
                    var param = $.extend(true, {
                        offset: p.offset,
                        limit: p.limit,
                        customer_id: customer_id
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
                pageSize: 5,
                pageList: [10, 50, 100],
                columns: [
                    {
                        title: "Chức năng",
                        valign: 'middle',
                        align: 'center',
                        class: 'CssAction',
                        formatter: function (value, row, index) {
                            var btnEdit = `<a data-bs-original-title="Cập nhật địa chỉ" data-bs-toggle="tooltip" data-bs-placement="top" data-id="${row.id}" data-address="${row.address}" data-longitude="${row.longitude}" data-latitude="${row.latitude}" class="btnTooltip btn btn-sm btn-info btnEditAddress me-1 mb-1" ><i class="bx bx-pencil"></i></a> `;
                            var btnDelete = `<a data-bs-original-title="Xóa địa chỉ" data-bs-toggle="tooltip" data-bs-placement="top" data-id="${row.id}" data-username="${row.address}" data-code="${row.longitude}" data-firstname="${row.latitude}" class="btnTooltip btn btn-sm btn-danger btnDeleteAddress me-1 mb-1" ><i class="bx bx-trash"></i></a> `;
                            var action = btnEdit + btnDelete;
                            return action;
                        },
                    },
                    {
                        title: "Thông tin địa chỉ",
                        align: 'center',
                        valign: 'left',
                        formatter: function (value, row, index) {
                            var address = row.address ? `<div class="row"><div class="col-md-4 d-flex justify-content-end">Địa chỉ: </div><div class="col-md-8 d-flex justify-content-start">${row.address}</div></div>` : ``;
                            var longitude = row.longitude ? `<div class="row"><div class="col-md-4 d-flex justify-content-end">Kinh độ: </div><div class="col-md-8 d-flex justify-content-start">${row.longitude}</div></div>` : ``;
                            var latitude = row.latitude ? `<div class="row"><div class="col-md-4 d-flex justify-content-end">Vĩ độ: </div><div class="col-md-8 d-flex justify-content-start">${row.latitude}</div></div>` : ``;
                            var html = address + longitude + latitude;
                            return html;
                        }
                    },

                ],
                formatNoMatches: function () {
                    return 'Chưa có dữ liệu địa chỉ của khách hàng';
                },
            });
        },
        SaveAddress: function () {
            $('#save_address_form').validate({
                rules: {
                    address_text: {
                        required: true
                    },
                    longitude: {
                        required: false
                    },
                    latitude: {
                        required: false,
                    },
                },
                messages: {
                    address_text: {
                        required: "Thiếu địa chỉ khách hàng"
                    },
                    longitude: {
                        required: "Thiếu kinh độ"
                    },
                    latitude: {
                        required: "Thiếu vĩ độ"
                    },
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
                submitHandler: function (form, event) {
                    event.preventDefault();
                    var data = new FormData();
                    var cccd_front = $('#cccd_front')[0].files[0];
                    var cccd_backside = $('#cccd_backside')[0].files[0];

                    data.append('address', $('#address_text').val() || "");
                    data.append('longitude', $('#longitude').val() || "");
                    data.append('latitude', $('#latitude').val() || "");
                    data.append('customer_id', $('#customer_addresses_id').val() || "");
                    data.append('id', $('#address_id').val() || "");

                    data.append('_token', $('#_token').val());

                    $.ajax({
                        url: form.action,
                        processData: false,
                        contentType: false,
                        type: form.method,
                        data: data,
                        beforeSend: function () {
                            var html = '<i class="bx bx-loader-circle loading"></i> đang lưu';
                            var address_id = $('address_id').val();
                            if (address_id) {
                                $('#btnUpdateAddress').html("").html(html).attr("disabled", true);
                            } else {
                                $('#btnAddAddress').html("").html(html).attr("disabled", true);
                            }
                        },
                        success: function (response) {
                            var address_id = $('address_id').val();
                            var message = response.message || "";
                            var title = "Lỗi";
                            var type = "error";

                            var address_id = $('address_id').val();
                            if (address_id) {
                                $('#btnUpdateAddress').html("").html("Cập nhật").attr("disabled", false);
                            } else {
                                $('#btnAddAddress').html("").html("Tạo mới").attr("disabled", false);
                            }

                            if (response.success) {
                                type = "success";
                                title = "Thành công";
                                toastr.success(message, title);
                                list_customers.ClearFormAddress();
                                list_customers.List_Customer_Addresses_Refresh();
                            } else {
                                toastr.error(message, title);
                            }

                        },
                        error: function (request, status, error) {
                            var address_id = $('address_id').val();
                            if (address_id) {
                                $('#btnUpdateAddress').html("").html("Cập nhật").attr("disabled", false);
                            } else {
                                $('#btnAddAddress').html("").html("Tạo mới").attr("disabled", false);
                            }
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
        ViewAddressesModal: function () {
            $(document).on('click', '.btnViewAddresses', function () {
                var customer_id = $(this).data('id');
                $('#customer_addresses_id').val(customer_id);
                list_customers.GetListDataAddress(customer_id);
                $('#view_address_modal').modal('show');
            });
        },
        toNonAccentVietnamese: function (str) {
            str = str.replace(/A|Á|À|Ã|Ạ|Â|Ấ|Ầ|Ẫ|Ậ|Ă|Ắ|Ằ|Ẵ|Ặ/g, "A");
            str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, "a");
            str = str.replace(/E|É|È|Ẽ|Ẹ|Ê|Ế|Ề|Ễ|Ệ/, "E");
            str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, "e");
            str = str.replace(/I|Í|Ì|Ĩ|Ị/g, "I");
            str = str.replace(/ì|í|ị|ỉ|ĩ/g, "i");
            str = str.replace(/O|Ó|Ò|Õ|Ọ|Ô|Ố|Ồ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ỡ|Ợ/g, "O");
            str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, "o");
            str = str.replace(/U|Ú|Ù|Ũ|Ụ|Ư|Ứ|Ừ|Ữ|Ự/g, "U");
            str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, "u");
            str = str.replace(/Y|Ý|Ỳ|Ỹ|Ỵ/g, "Y");
            str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, "y");
            str = str.replace(/Đ/g, "D");
            str = str.replace(/đ/g, "d");
            str = str.replace(/\u0300|\u0301|\u0303|\u0309|\u0323/g, "");
            str = str.replace(/\u02C6|\u0306|\u031B/g, "");
            return str;
        },
        ViewCccd: function () {
            $(document).on('click', '.btnViewCccd', function () {
                var cccd_backside = $(this).data('cccd_backside_url');
                var cccd_front = $(this).data('cccd_front_url');
                $('#cccd_backside_link').attr("href", cccd_backside);
                $('#cccd_backside_img').attr("src", cccd_backside);

                $('#cccd_front_link').attr("href", cccd_front);
                $('#cccd_front_img').attr("src", cccd_front);

                $('#view_cccd_modal').modal('show');
            });
        },
        CheckInputImportChange: function () {
            $(document).on('click', '#fileExcel', function () {
                $('#btn_save_excel').addClass('d-none');
                $('#btn_check_file_excel').removeClass('d-none');
            });
        },
        SaveImportExcel: function () {
            $('#btn_save_excel').on('click', function () {
                var data_import = $('#list_errors_in_file').bootstrapTable('getData');

                if (total_errors_import > 0 || data_import.length <= 0) {
                    return;
                }
                var data = new FormData();
                data.append('data', JSON.stringify(data_import));
                data.append('_token', $('#_token').val());

                $.ajax({
                    url: '/save-file-customers-data',
                    type: 'post',
                    processData: false,
                    contentType: false,
                    data: data,
                    beforeSend: function () {
                        var html = '<i class="bx bx-loader-circle loading"></i> đang xử lý';
                        $('#btn_save_excel').html("").html(html).attr("disabled", true);
                    },
                    success: function (response) {
                        var message = response.message || "";
                        var title = "Lỗi";
                        var type = "error";
                        $('#btn_save_excel').html("").html("Lưu lại").attr('disabled', false);
                        if (response.success) {
                            type = "success";
                            title = "Thành công";
                            toastr.success(message, title);
                            list_customers.ListCustomersRefresh();
                            $('#import_excel_modal').modal('hide');
                        } else {
                            toastr.error(message, title);
                        }

                    },
                    error: function (request, status, error) {
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
        CheckFileExcelImport: function () {
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
                invalidHandler: function (form, validator) {
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
                submitHandler: function (form, event) {
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
                        beforeSend: function () {
                            var html = '<i class="bx bx-loader-circle loading"></i> đang xử lý';
                            $('#btn_check_file_excel').html("").html(html).attr("disabled", true);
                        },
                        success: function (response) {
                            var message = response.message || "";
                            var title = "Lỗi";
                            var type = "error";
                            $('#btn_check_file_excel').html("").html("Kiểm tra").attr('disabled', false);

                            if (response[0].rows) {
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
                                            formatter: function (value, row, index) {
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
                                        { field: '0', title: "Họ và tên" },
                                        { field: '1', title: 'CCCD/CMT' },
                                        { field: '2', title: 'Email' },
                                        { field: '3', title: 'SDT' },
                                        {
                                            field: '4', title: 'Loại khách hàng', formatter: function (value, row, index) {
                                                return list_customers.GetTypeCustomer(row['4']);
                                            }
                                        },
                                        { field: '5', title: 'Tài khoản' },

                                    ],

                                    formatNoMatches: function () {
                                        return 'File tải lên hợp lệ. Nhấn nút lưu để hoàn tất nhiệm vụ';
                                    },
                                    pagination: true,
                                    totalRows: response[0].total
                                });
                                $('.btnTooltip').tooltip();
                                if (response[0].totalError > 0) {
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
                        error: function (request, status, error) {
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
        DeleteSingleCustomer: function () {
            $(document).on('click', '.btnDelete', function () {
                var url = '/delete-customer';
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
                }).then(function (result) {
                    if (result.value) {
                        $.ajax({
                            url: url,
                            method: 'delete',
                            data: {
                                id,
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
                                    list_customers.ListCustomersRefresh();
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
        SearchData: function () {
            $(document).on('click', '.btnSearch', function () {
                list_customers.ListCustomersRefresh();
            });
        },
        SetTooltip: function () {
            $(document).on('mouseover', '.btnTooltip', function () {
                $(this).tooltip();
            });
        },
        DeactiveCustomer: function () {
            $(document).on('click', '.btnActive', function () {
                var url = '/deactive-customer';
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
                                var title = "Lôi";
                                if (res.success) {
                                    title = "Thành công";
                                    type = "success";
                                    toastr.success(message, title);
                                    list_customers.ListCustomersRefresh();
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
        UpdateCustomerModal: function () {
            $(document).on('click', '.btnEdit', function () {
                var id = $(this).data('id');
                var customer_code = $(this).data('code');
                var customer_name = $(this).data('firstname');
                var cccd = $(this).data('cccd');
                var email = $(this).data('email');
                var username = $(this).data('username');
                var phone = $(this).data('phone');
                var customer_type = $(this).data('customer_type');
                var customer_address = $(this).data('customer_address');
                var cccd_front_url = $(this).data('cccd_front_url');
                var cccd_backside_url = $(this).data('cccd_backside_url');

                if (cccd_front_url) {
                    $('#update_atag-preview_cccd_front').attr('href', cccd_front_url)
                    $('#update_preview_cccd_front').attr('src', cccd_front_url);
                    $('.update_preview-cccd_front-group').removeClass('d-none');

                }

                if (cccd_backside_url) {
                    $('#update_atag-preview_cccd_backside').attr('href', cccd_backside_url)
                    $('#update_preview_cccd_backside').attr('src', cccd_backside_url);
                    $('.update_preview-cccd_backside-group').removeClass('d-none');
                }

                $('#customer_id').val(id);
                $('#update_code').val(customer_code);
                $('#update_firstname').val(customer_name);
                $('#update_cccd').val(cccd);
                $('#update_email').val(email);
                $('#update_username').val(username);
                $('#update_phone').val(phone);
                $('#update_customer_type').val(customer_type).change();
                $('#update_customer_address').val(customer_address);

                $('#update_customer_modal').modal('show');
            });
        },
        ListCustomersRefresh: function () {
            $('#list_customers_table').bootstrapTable('refresh');
        },
        CreateSingleCustomer: function () {
            $('#create_customer_form').validate({
                rules: {
                    code: {
                        required: true
                    },
                    firstname: {
                        required: true
                    },
                    last_name: {
                        required: true,
                    },
                    username: {
                        required: true,
                    },
                    cccd: {
                        required: true,
                        number: true
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    phone: {
                        required: true,
                        number: true,
                        minlength: 10,
                        maxlength: 10,
                    },
                    customer_type: {
                        required: true,
                    },
                    cccd_front: {
                        required: true,
                        extension: "jpg|jepg|png"
                    },
                    cccd_backside: {
                        required: true,
                        extension: "jpg|jepg|png"
                    },
                },
                messages: {
                    code: {
                        required: "Thiếu mã khách hàng"
                    },
                    firstname: {
                        required: "Thiếu tên khách hàng"
                    },
                    username: {
                        required: "Thiếu tài khoản khách hàng"
                    },
                    cccd: {
                        required: "Thiếu số giấy tờ",
                        number: "Số giấy tờ phải là số"
                    },
                    email: {
                        required: "Thiếu email khách hàng",
                        email: "Không đúng định dạng email"
                    },
                    phone: {
                        required: "Thiếu số điện thoại",
                        number: "Số điện thoại chỉ bao gồm ký tự số",
                        minlength: "Số điện thoại phải đúng 10 ký tự",
                        maxlength: "Số điện thoại phải đúng 10 ký tự",
                    },
                    customer_type: {
                        required: "Thiếu loại khách hàng",
                    },
                    cccd_front: {
                        required: "Thiếu ảnh giấy tờ mặt trước",
                        extension: "Định dạng ảnh không hợp lệ",
                    },
                    cccd_backside: {
                        required: "Thiếu ảnh giấy tờ mặt sau",
                        extension: "Định dạng ảnh không hợp lệ",
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
                submitHandler: function (form, event) {
                    event.preventDefault();
                    var data = new FormData();
                    var cccd_front = $('#cccd_front')[0].files[0];
                    var cccd_backside = $('#cccd_backside')[0].files[0];

                    data.append('code', $('#code').val() || "");
                    data.append('firstname', $('#firstname').val() || "");
                    data.append('last_name', $('#last_name').val() || "");
                    data.append('cccd', $('#cccd').val() || "");
                    data.append('email', $('#email').val() || "");
                    data.append('phone', $('#phone').val() || "");
                    data.append('customer_type', $('#customer_type').val() || "");
                    data.append('customer_address', $('#customer_address').val() || "");
                    data.append('username', $('#username').val() || "");
                    data.append('cccd_front', cccd_front || "");
                    data.append('cccd_backside', cccd_backside || "");

                    data.append('_token', $('#_token').val());

                    $.ajax({
                        url: form.action,
                        processData: false,
                        contentType: false,
                        type: form.method,
                        data: data,
                        beforeSend: function () {
                            var html = '<i class="bx bx-loader-circle loading"></i> đang lưu';
                            $('#btn_create_single_customer').html("").html(html).attr("disabled", true);
                        },
                        success: function (response) {
                            var message = response.message || "";
                            var title = "Lỗi";
                            var type = "error";
                            $('#btn_create_single_customer').html("").html("Thêm mới").attr('disabled', false);
                            if (response.success) {
                                type = "success";
                                title = "Thành công";
                                toastr.success(message, title);
                                $('#create_customer_modal').modal('hide');
                                list_customers.ListCustomersRefresh();
                            } else {
                                toastr.error(message, title);
                            }

                        },
                        error: function (request, status, error) {
                            $('#btn_create_single_customer').html("").html("Thêm mới").attr('disabled', false);
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
        UpdateSingleCustomer: function () {
            $('#update_customer_form').validate({
                rules: {
                    update_code: {
                        required: true
                    },
                    update_firstname: {
                        required: true
                    },
                    update_username: {
                        required: true,
                    },
                    update_cccd: {
                        required: true,
                        number: true
                    },
                    update_email: {
                        required: true,
                        email: true
                    },
                    update_phone: {
                        required: true,
                        number: true,
                        minlength: 10,
                        maxlength: 10,
                    },
                    update_customer_type: {
                        required: true,
                    },
                    update_customer_type: {
                        required: true,
                    },
                    update_cccd_front: {
                        // required: true,
                        extension: "jpg|jepg|png"
                    },
                    update_cccd_backside: {
                        // required: true,
                        extension: "jpg|jepg|png"
                    },
                },
                messages: {
                    update_code: {
                        required: "Thiếu mã khách hàng"
                    },
                    update_firstname: {
                        required: "Thiếu họ tên khách hàng"
                    },
                    update_username: {
                        required: "Thiếu tài khoản khách hàng"
                    },
                    update_cccd: {
                        required: "Thiếu số giấy tờ",
                        number: "Số giấy tờ phải là số"
                    },
                    update_email: {
                        required: "Thiếu email khách hàng",
                        email: "Không đúng định dạng email"
                    },
                    update_phone: {
                        required: "Thiếu số điện thoại",
                        number: "Số điện thoại chỉ bao gồm ký tự số",
                        minlength: "Số điện thoại phải đúng 10 ký tự",
                        maxlength: "Số điện thoại phải đúng 10 ký tự",
                    },
                    update_customer_type: {
                        required: "Thiếu loại khách hàng",
                    },
                    update_cccd_front: {
                        // required: "Thiếu ảnh giấy tờ mặt trước",
                        extension: "Định dạng ảnh không hợp lệ",
                    },
                    update_cccd_backside: {
                        // required: "Thiếu ảnh giấy tờ mặt sau",
                        extension: "Định dạng ảnh không hợp lệ",
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
                submitHandler: function (form, event) {
                    event.preventDefault();
                    var data = new FormData();
                    var cccd_front = $('#update_cccd_front')[0].files[0];
                    var cccd_backside = $('#update_cccd_backside')[0].files[0];

                    data.append('code', $('#update_code').val() || "");
                    data.append('firstname', $('#update_firstname').val() || "");
                    data.append('cccd', $('#update_cccd').val() || "");
                    data.append('email', $('#update_email').val() || "");
                    data.append('phone', $('#update_phone').val() || "");
                    data.append('customer_type', $('#update_customer_type').val() || "");
                    data.append('username', $('#update_username').val() || "");
                    data.append('cccd_front', cccd_front || "");
                    data.append('cccd_backside', cccd_backside || "");

                    data.append('id', $('#customer_id').val() || "");
                    data.append('_token', $('#_token').val());

                    $.ajax({
                        url: form.action,
                        processData: false,
                        contentType: false,
                        type: form.method,
                        data: data,
                        beforeSend: function () {
                            var html = '<i class="bx bx-loader-circle loading"></i> đang lưu';
                            $('#btn_update_single_customer').html("").html(html).attr("disabled", true);
                        },
                        success: function (response) {
                            var message = response.message || "";
                            var title = "Lỗi";
                            var type = "error";
                            $('#btn_update_single_customer').html("").html("Cập nhật").attr('disabled', false);
                            if (response.success) {
                                type = "success";
                                title = "Thành công";
                                toastr.success(message, title);
                                list_customers.ListCustomersRefresh();
                                $('#update_customer_modal').modal('hide');
                            } else {
                                toastr.error(message, title);
                            }

                        },
                        error: function (request, status, error) {
                            $('#btn_update_single_customer').html("").html("Cập nhật").attr('disabled', false);
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
        GetListDataCustomers: function () {
            $('#list_customers_table').bootstrapTable({
                url: '/get-list-data-customers',
                queryParams: function (p) {
                    var param = $.extend(true, {
                        offset: p.offset,
                        limit: p.limit,
                        type: $('#customer_type_filter').val() || '',
                        search_value: $('#search_filter').val() || ""
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
                        formatter: function (value, row, index) {
                            var btnViewAddresses = `<a data-bs-toggle="tooltip" data-bs-placement="top" class="btnTooltip btn btn-danger btn-sm btnViewAddresses me-1 mb-1" data-id="${row.id}" data-bs-original-title="Sổ địa chỉ"><i class="las la-address-book"></i></a>`;
                            var btnViewCccd = (row.cccd_front_url || row.cccd_backside_url) ? `<a data-bs-toogle="tooltip" data-bs-original-title="Ảnh giấy tờ" data-bs-placement="top" class="btnTooltip btnViewCccd mb-1 me-1 btn-primary btn btn-sm" data-cccd_front_url="${row.cccd_front_url}" data-cccd_backside_url="${row.cccd_backside_url}" data-id="${row.id}"><i class="bx bx-show"></i></a>` : ``;
                            var btnDelete = `<a data-bs-toggle="tooltip" data-bs-placement="top" class="btnTooltip btn btn-danger btn-sm btnDelete me-1 mb-1" data-id="${row.id}" data-bs-original-title="Xóa"><i class="bx bx-trash"></i></a>`;
                            var btnEdit = `<a data-bs-original-title="Cập nhật" data-bs-toggle="tooltip" data-bs-placement="top" data-cccd_front_url="${row.cccd_front_url}" data-cccd_backside_url="${row.cccd_backside_url}" data-username="${row.username}" data-id="${row.id}" data-code="${row.code}" data-firstname="${row.firstname}" data-cccd="${row.cccd}" data-email="${row.email}" data-phone="${row.phone}" data-customer_type="${row.type}" data-customer_address="${row.address}"  class="btnTooltip btn btn-sm btn-info btnEdit me-1 mb-1" ><i class="bx bx-pencil"></i></a> `;
                            var action = btnViewAddresses + btnViewCccd + btnEdit + btnDelete;
                            return action;
                        },
                    },
                    {
                        title: "Thông tin khách hàng",
                        align: 'center',
                        valign: 'left',
                        formatter: function (value, row, index) {
                            var customer_code = row.code ? `<div class="row"><div class="col-md-4 d-flex justify-content-end">Mã khách hàng: </div><div class="col-md-8 d-flex justify-content-start">${row.code}</div></div>` : ``;
                            var customer_name = row.firstname ? `<div class="row"><div class="col-md-4 d-flex justify-content-end">Tên khách hàng: </div><div class="col-md-8 d-flex justify-content-start">${row.firstname}</div></div>` : ``;
                            var email = row.email ? `<div class="row"><div class="col-md-4 d-flex justify-content-end">Email: </div><div class="col-md-8 d-flex justify-content-start">${row.email}</div></div>` : ``;
                            var phone = row.phone ? `<div class="row"><div class="col-md-4 d-flex justify-content-end">SDT: </div><div class="col-md-8 d-flex justify-content-start">${row.phone}</div></div>` : ``;
                            var address = row.address ? `<div class="row"><div class="col-md-4 d-flex justify-content-end">Địa chỉ: </div><div class="col-md-8 d-flex justify-content-start">${row.address}</div></div>` : ``;
                            var cccd = row.cccd ? `<div class="row"><div class="col-md-4 d-flex justify-content-end">Số giấy tờ: </div><div class="col-md-8 d-flex justify-content-start">${row.cccd}</div>` : ``;
                            var html = `${customer_name + customer_code + email + phone + address + cccd}`;
                            return html;
                        }
                    },
                    {
                        title: "Loại khách hàng",
                        align: 'center',
                        valign: 'left',
                        formatter: function (value, row, index) {
                            var html = list_customers.GetTypeCustomer(row.type);
                            return html;
                        }
                    },

                ],
                formatNoMatches: function () {
                    return 'Chưa có dữ liệu';
                },
            });
        },
        GetTypeCustomer: function (type = 1) {
            const TYPE = {
                1: 'Cá nhân',
                2: "Doanh nghiệp",
            };

            if (!TYPE.hasOwnProperty(type)) {
                return 'N/A';
            }

            var html = '<span class="font-size-12 p-2">' + TYPE[type] + '</span>';
            return html;
        },
        GetStatus: function (status = 2, id) {
            const STATUS = {
                2: "Đã vô hiệu hóa",
                1: 'Đã kích hoạt',
            };

            if (!STATUS.hasOwnProperty(status)) {
                return 'N/A';
            }

            var flag = "danger";
            if (status == 1) {
                flag = "success";
            }
            var html = `<span data-id="${id}" data-bs-original-title="${status == 1 ? 'Kích hoạt' : 'Vô hiệu hóa'}" data-active="${status}" data-bs-toggle="tooltip" data-bs-placement="top"  class="btnTooltip btn btnActive btn-${status == 1 ? 'success' : 'danger'} mx-1 badge bg-${flag} font-size-12 p-2">${STATUS[status]}</span>`;
            return html;
        },
        ClearModal: function () {
            $(document).on('hidden.bs.modal', '.modal', function () {
                $(this).find("input[type=text],input[type=file],input[type=email],input[name=id],textarea,select")
                    .val('')
                    .end();
                $('#update_customer_form').validate().resetForm();
                $('#create_customer_form').validate().resetForm();
                $('#error_array_in_file_excel_group').addClass('d-none');
                $('#list_errors_in_file').bootstrapTable('destroy').html("");
                $("label.error").html('').remove();
                $('.error').removeClass('error');
                $('#list_addresses_table').bootstrapTable('destroy');
                total_errors_import = 0;
            });
        }
    }
});

$(document).ready(function () {
    list_customers.init();
})
