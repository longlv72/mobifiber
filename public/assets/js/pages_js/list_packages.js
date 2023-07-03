var total_errors_import = 0;
$(function() {
    window.list_packages = {
        init: function() {
            list_packages.GetListDataPackages();
            list_packages.DeletePackages();
            list_packages.DeActivePackages();
            list_packages.ClearModal();
            list_packages.UpdatePackageModal();
            list_packages.CreateSinglePackage();
            list_packages.UpdateSinglePackage();
            list_packages.SearchPackage();
            list_packages.FormatImask();
            list_packages.CheckInputImportChange();
            list_packages.SaveImportExcel();
            list_packages.CheckFileExcelImport();
            list_packages.ViewPackageDetail();
            list_packages.DatetimepickerSetup();
            list_packages.SavePackageDetail();
            list_packages.EditPackageDetail();
            list_packages.CancelUpdatePackageDetail();
            list_packages.DeletePackageDetail();
        },
        DeletePackageDetail: function() {
            $(document).on('click', '.btnDeletePkD', function () {
                var url = '/delete-package-detail';
                var id = $(this).data('id');
                var package_id = $('#pkd_package_id').val();
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
                                "id": id,
                                "package_id": package_id,
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
                                    list_packages.RefreshListPackageDetail();
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
        CancelUpdatePackageDetail: function() {
            $(document).on('click', '#btnPackageDetailCancel', function() {
                list_packages.ClearFormPackageDetail();
            });
        },
        EditPackageDetail: function() {
            $(document).on('click', '.btnEditPackageDetail', function() {
                var start_date      = $(this).data('start_date');
                var end_date        = $(this).data('end_date');
                var price           = $(this).data('price');
                var price_vat       = $(this).data('price_vat');
                var decision_number = $(this).data('decision_number');
                var package_detail_id = $(this).data('id');

                if (start_date) {
                    $('#start_date').datetimepicker('setDate', new Date(start_date));
                }
                
                if (end_date) {
                    $('#end_date').datetimepicker('setDate', new Date(end_date));
                }
                
                $('#price').val(price);
                $('#price_vat').val(price_vat);
                $('#decision_number').val(decision_number);
                $('#package_detail_id').val(package_detail_id);
                list_packages.FormatImask();
                $('#btnAddPackageDetail').addClass('d-none');
                $('#btnUpdatePackageDetail').removeClass('d-none');
                $('#btnPackageDetailCancel').removeClass('d-none');
            });
        },
        ClearFormPackageDetail: function() {
            $('#package_detail_id').val("");
            $('#start_date').val("");
            $('#end_date').val("");
            $('#price').val("");
            $('#price_vat').val("");
            $('#decision_number').val("");
            $('#btnAddPackageDetail').removeClass('d-none');
            $('#btnUpdatePackageDetail').addClass('d-none');
            $('#btnPackageDetailCancel').addClass('d-none');
        },
        DatetimepickerSetup: function () {
            $('#start_date, #end_date').datetimepicker({
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
        RefreshListPackageDetail: function() {
            $('#list_package_detail_table').bootstrapTable('refresh');
        },
        SavePackageDetail: function() {
            $('#save_package_detail_form').validate({
                rules: {
                    start_date: "required",
                    end_date: "required",
                    price: {
                        required: true,
                    },
                    price_vat: {
                        required: true,
                    },
                    decision_number: {
                        required: true,
                    },
                },
                messages: {
                    start_date: "Thiếu ngày bắt đầu gói cước",
                    end_date: "Thiếu ngày kết thúc",
                    price: {
                        required: "Thiếu giá trước thuế",
                    },
                    price_vat: {
                        required: "Thiếu giá sau thuế",
                    },
                    decision_number: {
                        required: "Thiếu số quyết định",
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
                    data.append('start_date', moment($('#start_date').val(), 'DD-MM-YYYY').format("YYYY-MM-DD") || "");
                    data.append('end_date', moment($('#end_date').val(), 'DD-MM-YYYY').format("YYYY-MM-DD") || "");
                    data.append('price', ($('#price').val() || 0).toString().replace(/,(?=\d{3})/g, ''));
                    data.append('price_vat', ($('#price_vat').val() || 0).toString().replace(/,(?=\d{3})/g, ''));
                    data.append('decision_number', $('#decision_number').val() || "");
                    data.append('id', $('#package_detail_id').val() || "");
                    data.append('package_id', $('#pkd_package_id').val() || "");

                    data.append('_token', $('#_token').val());

                    $.ajax({
                        url: form.action,
                        processData: false,
                        contentType: false,
                        type: form.method,
                        data: data,
                        beforeSend: function() {
                            var html = '<i class="bx bx-loader-circle loading"></i> đang lưu';
                            var package_detail_id = $('package_detail_id').val();
                            if ( ! package_detail_id ) {
                                $('#btnAddPackageDetail').html("").html(html).attr("disabled", true);
                            } else {
                                $('#btnUpdatePackageDetail').html("").html(html).attr("disabled", true);

                            }
                        },
                        success: function(response) {
                            var message = response.message || "";
                            var title = "Lỗi";
                            var type = "error";
                            $('#btnAddPackageDetail').html("").html("Thêm mới").attr('disabled', false);

                            var package_detail_id = $('package_detail_id').val();
                            if ( ! package_detail_id ) {
                                $('#btnAddPackageDetail').html("").html("Tạo mới").attr("disabled", false);
                            } else {
                                $('#btnUpdatePackageDetail').html("").html("Cập nhật").attr("disabled", false);

                            }

                            if (response.success) {
                                type = "success";
                                title = "Thành công";
                                toastr.success(message, title);
                                
                                list_packages.RefreshListPackageDetail();
                                list_packages.ClearFormPackageDetail();
                            } else {
                                toastr.error(message, title);
                            }

                        },
                        error: function(request, status, error) {
                            var package_detail_id = $('package_detail_id').val();
                            if ( ! package_detail_id ) {
                                $('#btnAddPackageDetail').html("").html("Tạo mới").attr("disabled", false);
                            } else {
                                $('#btnUpdatePackageDetail').html("").html("Cập nhật").attr("disabled", false);

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
        GetListPackageDetail: function(package_id) {
            if ( ! package_id ) return ;

            $('#list_package_detail_table').bootstrapTable({
                url: '/get-list-package-detail',
                queryParams: function (p) {
                    var param = $.extend(true, {
                        offset: p.offset,
                        limit: p.limit,
                        package_id: package_id
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
                            var btnEdit = `<a data-bs-original-title="Cập nhật chi tiết gói cuớc" data-bs-toggle="tooltip" data-bs-placement="top" data-id="${row.id}" data-start_date="${row.start_date}" data-end_date="${row.end_date}" data-price="${row.price}" data-price_vat="${row.price_vat}" data-decision_number="${row.decision_number}" class="btnTooltip btn btn-sm btn-info btnEditPackageDetail me-1 mb-1" ><i class="bx bx-pencil"></i></a> `;
                            var btnDelete = `<a data-bs-original-title="Xóa chi tiết gói cuớc" data-bs-toggle="tooltip" data-bs-placement="top" data-id="${row.id}" class="btnTooltip btn btn-sm btn-danger btnDeletePkD me-1 mb-1" ><i class="bx bx-trash"></i></a> `;
                            var action = btnEdit + btnDelete;
                            return action;
                        },
                    },
                    {
                        title: "Thông tin chi tiết gói cước",
                        align: 'center',
                        valign: 'left',
                        formatter: function (value, row, index) {
                            var start_date = row.start_date ? `<div class="row"><div class="col-md-4 d-flex justify-content-end">Ngày bắt đầu: </div><div class="col-md-8 d-flex justify-content-start">${moment(row.start_date).format('DD-MM-YYYY')}</div></div>` : ``;
                            var end_date = row.end_date ? `<div class="row"><div class="col-md-4 d-flex justify-content-end">Ngày kết thúc: </div><div class="col-md-8 d-flex justify-content-start">${moment(row.end_date).format('DD-MM-YYYY')}</div></div>` : ``;
                            var price = row.price ? `<div class="row"><div class="col-md-4 d-flex justify-content-end">Giá trước thuế: </div><div class="col-md-8 d-flex justify-content-start">${parseInt(row.price || 0).toLocaleString('vi-VN', { style: 'currency', currency: 'VND' })}</div></div>` : ``;
                            var price_vat = row.price_vat ? `<div class="row"><div class="col-md-4 d-flex justify-content-end">Giá sau thuế: </div><div class="col-md-8 d-flex justify-content-start">${parseInt(row.price_vat || 0).toLocaleString('vi-VN', { style: 'currency', currency: 'VND' })}</div></div>` : ``;
                            var decision_number = row.decision_number ? `<div class="row"><div class="col-md-4 d-flex justify-content-end">Số quyết định: </div><div class="col-md-8 d-flex justify-content-start">${row.decision_number}</div></div>` : ``;
                            var html = start_date + end_date + price + price_vat + decision_number;
                            return html;
                        }
                    },

                ],
                formatNoMatches: function () {
                    return 'Chưa có dữ liệu chi tiết gói cước này';
                },
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
                    url: '/save-file-package-data',
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
                            list_packages.ListPackageRefresh();
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
                                        { field: '0', title: 'Tên gói cước' },
                                        { field: '1', title: "Mã gói cước"},
                                        { field: '2', title: 'Giá trước thuế', formatter: function(value, row, index) {
                                            return row['2'] ? row['2'].toLocaleString('vi-VN', { style: 'currency', currency: 'VND' }) : 'N/A';
                                        }},
                                        { field: '3', title: "Giá sau thuế", formatter: function(value, row, index) {
                                            return row['3'] ? row['3'].toLocaleString('vi-VN', { style: 'currency', currency: 'VND' }) : 'N/A';
                                        }},
                                        { field: '4', title: "Thời gian sử dụng (tháng)"},
                                        { field: '5', title: "Khuyến mại (tháng)"},
                                        { field: '6', title: "Quyết định số"},
                                        // { field: '7', title: "Trạng thái gói cước", formatter: function(value, row, index) {
                                        //     return row['7'] == 1 ? 'Đã kích hoạt' : 'Đã vô hiệu hóa';
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
        FormatImask: function() {
            IMask(document.getElementById("price"), {
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


            IMask(document.getElementById("price_vat"), {
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
        SearchPackage: function() {
            $(document).on('click', '.btnSearch', function() {
                list_packages.ListPackageRefresh();
            });
        },
        DeletePackages: function() {
            $(document).on('click', '.btnDelete', function() {
                var url = '/delete-package';
                var id = $(this).data('id');
                var title = "Xác nhận xóa gói cước?";
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
                                    list_packages.ListPackageRefresh();
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
        DeActivePackages: function() {
            $(document).on('click', '.btnActive', function() {
                var url = '/deactive-package';
                var id = $(this).data('id');
                var title = "Xác nhận đổi trạng thái gói cước?";
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
                                    list_packages.ListPackageRefresh();
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
                $('#update_package_form')?.validate().resetForm();
                $('#create_package_form')?.validate().resetForm();
                $('#error_array_in_file_excel_group').addClass('d-none');
                $('#list_errors_in_file').bootstrapTable('destroy').html("");
                
                $('#list_package_detail_table').bootstrapTable('destroy').html("");

                $(".error").removeClass("error");
                // .find("input[type=checkbox], input[type=radio]")
                // .prop("checked", "")
                // .end();
            })
        },
        UpdatePackageModal: function() {
            $(document).on('click', '.btnEdit', function() {
                var id = $(this).data('id');
                var package_name = $(this).data('package_name');
                var package_code = $(this).data('package_code');
                var price = $(this).data('prices');
                var time_used = $(this).data('time_used');
                var prices_vat = $(this).data('prices_vat');
                var promotion_time = $(this).data('promotion_time');
                var decision = $(this).data('decision');
                var is_active = $(this).data('is_active');

                $('#package_id').val(id);
                $('#update_package_name').val(package_name);
                $('#update_package_code').val(package_code);
                $('#update_package_price').val(price);
                $('#update_package_price_vat').val(prices_vat);
                $('#update_time_used').val(time_used);
                $('#update_promotion_time').val(promotion_time);
                $('#update_descision').val(decision);
                $('#update_active_package').val(is_active).change();
                list_packages.FormatImask();

                $('#update_package_modal').modal('show');
            });
        },
        ListPackageRefresh: function() {
            $('#list_packages_table').bootstrapTable('refresh');
        },
        CreateSinglePackage: function() {
            $('#create_package_form').validate({
                rules: {
                    package_name: "required",
                    package_code: "required",
                    time_used: {
                        required: true,
                        number: true
                    },
                    active_package: {
                        required: true,
                    },
                },
                messages: {
                    package_name: "Thiếu tên gói cước",
                    package_code: "Thiếu mã gói cước",
                    time_used: {
                        required: "Thiếu thời gian sử dụng",
                        number: "Thời gian sử dụng phải là số"
                    },
                    promotion_time: {
                        number: "Thời gian khuyến mại phải là số"
                    },
                    active_package: {
                        required: "Thiếu trạng thái kích hoạt",
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
                    data.append('package_name', $('#package_name').val() || "");
                    data.append('package_code', $('#package_code').val() || "");
                    // data.append('package_price', ($('#package_price').val() || 0).toString().replace(/,(?=\d{3})/g, ''));
                    // data.append('package_price_vat', ($('#package_price_vat').val() || 0).toString().replace(/,(?=\d{3})/g, ''));
                    data.append('time_used', $('#time_used').val() || "");
                    data.append('promotion_time', $('#promotion_time').val() || "");
                    // data.append('descision', $('#descision').val() || "");
                    data.append('active_package', $('#active_package').val() || "");
                    data.append('_token', $('input[name="_token"]').val());

                    $.ajax({
                        url: form.action,
                        processData: false,
                        contentType: false,
                        type: form.method,
                        data: data,
                        beforeSend: function() {
                            var html = '<i class="bx bx-loader-circle loading"></i> đang lưu';
                            $('#btn_create_single_package').html("").html(html).attr("disabled", true);
                        },
                        success: function(response) {
                            var message = response.message || "";
                            var title = "Lỗi";
                            var type = "error";
                            $('#btn_create_single_package').html("").html("Thêm mới").attr('disabled', false);
                            if (response.success) {
                                type = "success";
                                title = "Thành công";
                                toastr.success(message, title);
                                list_packages.ListPackageRefresh();
                                $('#create_package_modal').modal('hide');
                            } else {
                                toastr.error(message, title);
                            }

                        },
                        error: function(request, status, error) {
                            $('#btn_create_single_package').html("").html("Thêm mới").attr('disabled', false);
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
        UpdateSinglePackage: function() {
            $('#update_package_form').validate({
                rules: {
                    update_package_name: "required",
                    update_package_code: "required",
                    update_time_used: {
                        required: true,
                        number: true,
                    },
                    update_promotion_time: {
                        number: true
                    },
                    update_active_package: {
                        required: true,
                    },
                    update_descision: {
                        required: true,
                    },
                },
                messages: {
                    update_package_name: "Thiếu tên gói cước",
                    update_package_code: "Thiếu mã gói cước",
                    update_time_used: {
                        required: "Thiếu thời gian sử dụng",
                        number: "Thời gian sử dụng phải là số"
                    },
                    update_promotion_time: {
                        number: "Thời gian khuyến mại phải là số"
                    },
                    update_active_package: {
                        required: "Thiếu trạng thái kích hoạt",
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
                    data.append('package_name', $('#update_package_name').val() || "");
                    data.append('package_code', $('#update_package_code').val() || "");
                    data.append('package_price', ($('#update_package_price').val() || 0).toString().replace(/,(?=\d{3})/g, ''));
                    data.append('package_price_vat', ($('#update_package_price_vat').val() || 0).toString().replace(/,(?=\d{3})/g, ''));
                    data.append('time_used', $('#update_time_used').val() || "");
                    data.append('promotion_time', $('#update_promotion_time').val() || "");
                    data.append('descision', $('#update_descision').val() || "");
                    data.append('active_package', $('#update_active_package').val() || "");
                    data.append('id', $('#package_id').val() || "");

                    data.append('_token', $('input[name="_token"]').val());

                    $.ajax({
                        url: form.action,
                        processData: false,
                        contentType: false,
                        type: form.method,
                        data: data,
                        beforeSend: function() {
                            var html = '<i class="bx bx-loader-circle loading"></i> đang lưu';
                            $('#btn_update_single_package').html("").html(html).attr("disabled", true);
                        },
                        success: function(response) {
                            var message = response.message || "";
                            var title = "Lỗi";
                            var type = "error";
                            $('#btn_update_single_package').html("").html("Cập nhật").attr('disabled', false);
                            if (response.success) {
                                type = "success";
                                title = "Thành công";
                                toastr.success(message, title);
                                list_packages.ListPackageRefresh();
                                $('#update_package_modal').modal('hide');
                            } else {
                                toastr.error(message, title);
                            }

                        },
                        error: function(request, status, error) {
                            $('#btn_update_single_package').html("").html("Cập nhật").attr('disabled', false);
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
        ViewPackageDetail: function() {
            $(document).on('click', '.btn_list_package_detail', function() {
                var package_id = $(this).data('id');
                
                $('#pkd_package_id').val(package_id);
                list_packages.GetListPackageDetail(package_id);
                $('#view_package_detail_modal').modal('show');
            });
        },
        GetListDataPackages: function() {
            $('#list_packages_table').bootstrapTable({
                url: '/get-list-data-package',
                queryParams: function(p) {
                    var param = $.extend(true, {
                        offset: p.offset,
                        limit: p.limit,
                        search_value: $('#search_value').val(),
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
                        title: "Chức năng",
                        valign: 'middle',
                        align: 'center',
                        class: 'CssAction',
                        formatter: function(value, row, index) {
                            var list_package_detail = `<a data-id="${row.id}" class="btn btn-secondary btn-sm me-1 mb-1 btn_list_package_detail"><i class=" bx bx-list-plus"></i></a>`
                            var btnActive = true ? `` : `<a data-bs-original-title="${row.is_active == 1 ? 'Kích hoạt' : 'Vô hiệu hóa'}" data-active="${row.is_active}"  data-bs-toggle="tooltip" data-bs-placement="top"  class="btnTooltip btnDeactive mb-1 me-1 btn ${row.is_active == 0 ? 'btn-danger btnUnlock': 'btn-success btnUnlock'} btn-sm" data-id="${row.id}"><i class="bx bx-lock-alt"></i></a>`;
                            var btnDelete = `<a  data-bs-toggle="tooltip" data-bs-placement="top" class="btnTooltip btn btn-danger btn-sm btnDelete mb-1" data-id="${row.id}" data-bs-original-title="Xóa"><i class="bx bx-trash"></i></a>`;
                            var btnEdit = `<a data-bs-original-title="Cập nhật" data-bs-toggle="tooltip" data-bs-placement="top" data-package_name="${row.package_name}" data-package_code="${row.package_code}" data-id="${row.id}" data-is_active="${row.is_active}" data-time_used="${row.time_used}" data-promotion_time="${row.promotion_time}" class="btnTooltip btn btn-sm btn-info btnEdit me-1 mb-1" ><i class="bx bx-pencil"></i></a>`;
                            var action = list_package_detail + btnEdit + btnDelete;
                            return action;
                        },
                    },
                    {
                        title: "Thông tin gói cước",
                        align: 'center',
                        valign: 'left',
                        formatter: function(value, row, index) {
                            var package_name = row.package_name ? `<div>Tên gói: ${row.package_name}</div>` : ``;
                            var package_code = row.package_code ? `<div>Mã gói cước: ${row.package_code}</div>` : ``;
                            var decision = row.package_detail && row.package_detail.decision_number ? `<div>Quyết định số: ${row.package_detail.decision_number}</div>` : ``;
                            var start_date = row.package_detail && row.package_detail.start_date ? `<div>Ngày bắt đầu: ${moment(row.package_detail.start_date).format('DD-MM-YYYY')}</div>` : ``;
                            var html = package_name + package_code + decision + start_date;
                            return html;
                        }
                    },
                    {
                        title: "Thông tin giá",
                        align: 'center',
                        valign: 'left',
                        formatter: function(value, row, index) {
                            var prices = row.package_detail && row.package_detail.price ? `<div>Giá trước thuế: ${parseFloat(row.package_detail.price || 0).toLocaleString('vi-VN', { style: 'currency', currency: 'VND' })}</div>` : ``;
                            var prices_vat = row.package_detail && row.package_detail.price_vat ? `<div>Giá sau thuế: ${parseFloat(row.package_detail.price_vat || 0).toLocaleString('vi-VN', { style: 'currency', currency: 'VND' })}</div>` : ``;
                            var html = prices + prices_vat;
                            return html;
                        }
                    },
                    {
                        title: "Thời gian sử dụng",
                        align: 'center',
                        valign: 'left',
                        formatter: function(value, row, index) {
                            var time_used = row.time_used ? `<div>Thời gian sử dụng: ${row.time_used} tháng</div>` : ``;
                            var promotion_time = row.promotion_time ? `<div>Thời gian khuyến mại: ${row.promotion_time} tháng</div>` : ``;
                            var html = time_used + promotion_time;
                            return html;
                        }
                    },
                    {
                        title: "Trạng thái",
                        align: 'center',
                        valign: 'left',
                        formatter: function(value, row, index) {
                            // debugger
                            var html = list_packages.GetStatus(row.is_active, row.id);
                            return html;
                        }
                    },
                ],
                formatNoMatches: function() {
                    return 'Chưa có dữ liệu';
                },
            })
        }
    }
});

$(document).ready(function() {
    list_packages.init();
})
