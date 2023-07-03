$(function () {
    window.list_role = {
        init: function () {
            list_role.getListRole($('#role_group_id').val());
        },
        updateRole: function (id, action) {
            $.ajax({
                url: '/update-roles',
                type: 'get',
                dataType: 'json',
                data: {
                    id: id,
                    action: action
                },
                success: function (res) {
                    if (res.success) {
                        toastr.success(res.message, "Thành công");
                    }
                    else {
                        toastr.error(res.message, "Lỗi");
                    }
                }
            });
        },
        getListRole: function (id) {
            $.ajax({
                url: '/get-list-roles',
                type: 'get',
                data: {
                    role_id: id
                },
                dataType: 'json',
                success: function (res) {
                    $('#list_role_table').bootstrapTable('destroy');
                    $('#list_role_table').bootstrapTable({
                        data: res.rows,
                        columns: [
                            {
                                field: 'module_display',
                                title: 'Chức năng',
                                align: 'center'
                            },
                            {
                                field: 'role_add',
                                title: "Thêm mới",
                                align: 'center',
                                formatter: function (value, row, index) {
                                    return '<input class="form-check-input checkAdd" type="checkbox" ' + (value == 1 ? '' : 'checked') + '>';
                                },
                                events: {
                                    'change .checkAdd': function (e, value, row, index) {
                                        list_role.updateRole(row.id, 'role_add');
                                    }
                                }
                            },
                            {
                                field: 'role_edit',
                                title: 'Cập nhật',
                                align: 'center',
                                formatter: function (value, row, index) {
                                    return '<input class="form-check-input checkEdit" type="checkbox" ' + (value == 1 ? '' : 'checked') + '>';
                                },
                                events: {
                                    'change .checkEdit': function (e, value, row, index) {
                                        list_role.updateRole(row.id, 'role_edit');
                                    }
                                }
                            },
                            {
                                field: 'role_delete',
                                title: "Xóa",
                                align: 'center',
                                formatter: function (value, row, index) {
                                    return '<input class="form-check-input checkDelete" type="checkbox" ' + (value == 1 ? '' : 'checked') + '>';
                                },
                                events: {
                                    'change .checkDelete': function (e, value, row, index) {
                                        list_role.updateRole(row.id, 'role_delete');
                                    }
                                }
                            },
                            {
                                field: 'role_view',
                                title: "Xem/chi tiết",
                                align: 'center',
                                formatter: function (value, row, index) {
                                    return '<input class="form-check-input checkView" type="checkbox" ' + (value == 1 ? '' : 'checked') + '>';
                                },
                                events: {
                                    'change .checkView': function (e, value, row, index) {
                                        list_role.updateRole(row.id, 'role_view');
                                    }
                                }
                            },
                            {
                                field: 'role_import',
                                title: "Thêm mới tệp",
                                align: 'center',
                                formatter: function (value, row, index) {
                                    return '<input class="form-check-input checkImport" type="checkbox" ' + (value == 1 ? '' : 'checked') + '>';
                                },
                                events: {
                                    'change .checkImport': function (e, value, row, index) {
                                        list_role.updateRole(row.id, 'role_import');
                                    }
                                }
                            },
                            {
                                field: 'role_export',
                                title: "Xuất tệp",
                                align: 'center',
                                formatter: function (value, row, index) {
                                    return '<input class="form-check-input checkExport" type="checkbox" ' + (value == 1 ? '' : 'checked') + '>';
                                },
                                events: {
                                    'change .checkExport': function (e, value, row, index) {
                                        list_role.updateRole(row.id, 'role_export');
                                    }
                                }
                            },
                            {
                                field: 'role_report',
                                title: "Báo cáo",
                                align: 'center',
                                formatter: function (value, row, index) {
                                    return '<input class="form-check-input checkReport" type="checkbox" ' + (value == 1 ? '' : 'checked') + '>';
                                },
                                events: {
                                    'change .checkReport': function (e, value, row, index) {
                                        list_role.updateRole(row.id, 'role_report');
                                    }
                                }
                            },

                        ],
                        pagination: true,
                        totalRows: res.total
                    });
                }
            });
        }
    }
});
$(document).ready(function () {
    list_role.init();
});
