define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'interest_student/index' + location.search,
                    add_url: 'interest_student/add',
                    edit_url: 'interest_student/edit',
                    del_url: 'interest_student/del',
                    multi_url: 'interest_student/multi',
                    import_url: 'interest_student/import',
                    table: 'interest_student',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'),visible:false},
                        {field: 'student_id', title: __('Student_id'),visible:false},
                        { field: 'interest_id', title: __('Interest_id'), operate: 'LIKE', visible: false },
                        {field: 'student.name', title: __('Student.name'), operate: 'LIKE'},
                        {field: 'interest.name', title: __('Interest.name'), operate: 'LIKE'},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});