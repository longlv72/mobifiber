$(function() {
    window.layout = {
        init: function() {
            layout.ShowModalProfile();
            layout.SaveProfile();
            layout.SavePassword();
            layout.ShowChangePasswordModal();
            layout.ClearModalLayout();
        },
        ClearModalLayout: function () {
            $(document).on('hidden.bs.modal', '#change_password_info_user_modal, #update_info_user_modal', function () {
                $(this).find("input[type=text],input[type=file],input[type=password],input,textarea,select")
                    .val('')
                    .end();
                $('#form_change_password')?.validate().resetForm();
                $(".error").removeClass("error");
            })
        },
        SavePassword: function() {
            $("#form_change_password").validate({
                rules: {
                    old_password: {
                        required: true,
                    },
                    new_password: {
                        required: true,
                        minlength: 6,
                    },
                    conf_password: {
                        required: true,
                        equalTo: "#new_password",
                    },
                },
                messages: {
                    old_password: {
                        required: "Thiếu mật khẩu cũ",
                    },
                    new_password: {
                        required: "Thiếu mật khẩu mới",
                        minlength: "Mật khẩu mới tối thiểu 6 ký tự"
                    },
                    conf_password: {
                        required: "Thiếu xác nhận mật khẩu",
                        equalTo: "Xác nhận mật khẩu không khớp",
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
                    data.append('old_password', $('#old_password').val() || "");
                    data.append('new_password', $('#new_password').val() || "");

                    data.append('_token', $('input[name="_token"]').val());

                    $.ajax({
                        url: form.action,
                        processData: false,
                        contentType: false,
                        type: form.method,
                        data: data,
                        beforeSend: function() {
                            var html = '<i class="bx bx-loader-circle loading"></i> đang lưu';
                            $('#btn_change_password').html("").html(html).attr("disabled", true);
                        },
                        success: function(response) {
                            var message = response.message || "";
                            var title = "Lỗi";
                            var type = "error";
                            $('#btn_change_password').html("").html("Lưu").attr('disabled', false);
                            if (response.success) {
                                type = "success";
                                title = "Thành công";
                                toastr.success(message, title);
                                $('#change_password_info_user_modal').modal('hide');
                            } else {
                                toastr.error(message, title);
                            }

                        },
                        error: function(request, status, error) {
                            $('#btn_change_password').html("").html("Lưu").attr('disabled', false);
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
        ShowChangePasswordModal: function() {
            $(document).on('click', '.btn_modal_change_password', function() {
                var pf_user_id = $("#pf_user_id").val();

                $('#change_pass_user_id').val(pf_user_id);
                $('#change_password_info_user_modal').modal('show');
            });
        },
        ShowModalProfile: function() {
            $(document).on('click', '.btn_get_profile', function() {
                var pf_user_id = $("#pf_user_id").val();
                var pf_phone = $("#pf_phone").val();
                var pf_username = $("#pf_username").val();
                var pf_address = $("#pf_address").val();
                var pf_email = $("#pf_email").val();
                var pf_first_name = $("#pf_first_name").val();
                var pf_last_name = $("#pf_last_name").val();

                $('#u_username').val(pf_username);
                $('#u_email').val(pf_email);
                $('#u_first_name').val(pf_first_name);
                $('#u_last_name').val(pf_last_name);
                $('#u_address').val(pf_address);
                $('#u_phone').val(pf_phone);

                $('#update_info_user_modal').modal('show');
            });
        },
        SaveProfile: function() {
            $("#form_update_profile").validate({
                rules: {
                    u_email: {
                        required: true,
                        email: true
                    },
                    u_first_name: {
                        required: true
                    },
                    u_last_name: {
                        required: true,
                    },
                    u_phone: {
                        required: true,
                    },
                },
                messages: {
                    u_email: {
                        required: "Thiếu email",
                        email: "Email không đúng định dạng"
                    },
                    u_first_name: {
                        required: "Thiếu tên"
                    },
                    u_last_name: {
                        required: "Thiếu họ đệm",
                    },
                    u_phone: {
                        required: "Thiếu số điện thoại",
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
                    data.append('email', $('#u_email').val() || "");
                    data.append('first_name', $('#u_first_name').val() || "");
                    data.append('last_name', $('#u_last_name').val() || "");
                    data.append('phone', $('#u_phone').val() || "");
                    data.append('address', $('#u_address').val() || "");

                    data.append('_token', $('input[name="_token"]').val());

                    $.ajax({
                        url: form.action,
                        processData: false,
                        contentType: false,
                        type: form.method,
                        data: data,
                        beforeSend: function() {
                            var html = '<i class="bx bx-loader-circle loading"></i> đang lưu';
                            $('#btn_save_info').html("").html(html).attr("disabled", true);
                        },
                        success: function(response) {
                            var message = response.message || "";
                            var title = "Lỗi";
                            var type = "error";
                            $('#btn_save_info').html("").html("Lưu").attr('disabled', false);
                            if (response.success) {
                                type = "success";
                                title = "Thành công";
                                toastr.success(message, title);
                                // $('#update_info_user_modal').modal('hide');
                                var data_user = response.data;
                                $('#pf_phone').val(data_user.phone);
                                $('#pf_address').val(data_user.address);
                                $('#pf_email').val(data_user.email);
                                $('#pf_first_name').val(data_user.firstname);
                                $('#pf_last_name').val(data_user.lastname);
                            } else {
                                toastr.error(message, title);
                            }

                        },
                        error: function(request, status, error) {
                            $('#btn_save_info').html("").html("Lưu").attr('disabled', false);
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
        }
    }
});

$(document).ready(function() {
    layout.init();
});