define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'account/index' + location.search,
                    add_url: 'account/add',
                    edit_url: 'account/edit',
                    del_url: 'account/del',
                    multi_url: 'account/multi',
                    import_url: 'account/import',
                    table: 'account',
                }
            });

            var table = $("#table");
            table.on('load-success.bs.table', function (e, data) {
                $(".in_sum").html(" " + data.extend.in_sum + " ");
                $(".out_sum").html(" " + data.extend.out_sum + " ");
                $(".sum").html(" " + data.extend.sum + " ")
            });
            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'accounttype_id', title: __('Accounttype_id'), visible:false, operate:false},
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'accountuser_id', title: __('Accountuser_id'), operate: false, visible:false},
                        {field: 'in_out', title: __('In_out'), searchList: {"入账":__('入账'),"出账":__('出账')}, formatter: Table.api.formatter.normal},
                        {field: 'money', title: __('Money'), operate: 'LIKE' },
                        {field: 'accounttype.name', title: __('Accounttype.name'), operate: 'LIKE'},
                        {field: 'accountuser.name', title: __('Accountuser.name'), operate: 'LIKE'},
                        {field: 'date_time', title: __('Date_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'images', title: __('Images'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.images},
                        {field: 'create_time', title: __('Create_time'), visible:false, operate:false, addclass:'datetimerange', autocomplete:false,},
                        {field: 'remark', title: __('Remark'), operate: false},
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