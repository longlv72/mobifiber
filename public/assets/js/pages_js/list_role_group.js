$(function () {
    window.list_rolegroup = {
        init: function () {
            if ($('#list_rolegroup_table').length > 0) {
                list_rolegroup.getListRoleGroup();
            }
            $('#btnCreateRoleGroup').click(function () {
                $('#role_name').val('');
                $('#create_rolegroup_modal').modal('show');
            });
            $('#btn_update_single_rolename').click(function(){
                list_rolegroup.updateRole();
            });
            $('#btn_create_single_rolegroup').click(function(){
                list_rolegroup.createRole();
            });
        },
        createRole: function () {
            $.ajax({
                url: '/create-role-groups',
                type: 'get',
                dataType: 'json',
                data: {
                    role_name: $('#role_name').val()
                },
                success: function (res) {
                    if (res.success) {
                        $('#create_rolegroup_modal').modal('hide');
                        toastr.success(res.message, 'Thông báo');
                        $('#list_rolegroup_table').bootstrapTable('refresh', { silent: true });
                    }
                    else{
                        toastr.error(res.message, 'Thông báo');
                    }
                }
            });
        },
        updateRole: function () {
            $.ajax({
                url: '/update-role-groups',
                type: 'get',
                dataType: 'json',
                data: {
                    id: $('#rolegroup_id').val(),
                    role_name: $('#update_role_name').val()
                },
                success: function (res) {
                    if (res.success) {
                        $('#update_rolegroup_modal').modal('hide');
                        toastr.success(res.message, 'Thông báo');
                        $('#list_rolegroup_table').bootstrapTable('refresh', { silent: true });
                    }
                    else{
                        toastr.error(res.message, 'Thông báo');
                    }
                }
            });
        },
        getListRoleGroup: function (id) {
            $('#list_rolegroup_table').bootstrapTable('destroy');
            $('#list_rolegroup_table').bootstrapTable({
                url: '/get-list-role-groups',
                queryParams: function (p) {
                    var param = $.extend(true, {
                        offset: p.offset,
                        limit: p.limit
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
                        title: "Nhóm quyền",
                        field: 'role_name',
                        align: 'center',
                        valign: 'left'
                    },
                    {
                        title: "Chức năng",
                        align: 'center',
                        valign: 'left',
                        formatter: function (value, row, index) {
                            var html = '<a href="/list-roles?role_id=' + row.id + '" class="btn btn-sm btn-primary me-1"><i class="bx bx-info-square"></i></a>';
                            html += '<a href="javascript:void(0)" class="btn btn-sm btn-success btnUpdateData"><i class="bx bx-pencil"></i></a>';
                            return html;
                        },
                        events: {
                            'click .btnUpdateData': function (e, value, row, index) {
                                $('#rolegroup_id').val(row.id);
                                $('#update_role_name').val(row.role_name);
                                $('#update_rolegroup_modal').modal('show');
                            }
                        }
                    }
                ],
                formatNoMatches: function () {
                    return 'Chưa có dữ liệu';
                },
            });
        }
    }
});
$(document).ready(function () {
    list_rolegroup.init();
})
