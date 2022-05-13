define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'student/index' + location.search,
                    add_url: 'student/add',
                    edit_url: 'student/edit',
                    del_url: 'student/del',
                    multi_url: 'student/multi',
                    import_url: 'student/import',
                    table: 'student',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                search:false,   //搜索框
                showColumns: false,  //下拉显示哪些字段
                exportTypes: ['excel'], //导出EXCEL
                showToggle: false,   //显示格式排版
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'), visible:false},
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'sex', title: __('Sex'), searchList: {"男":__('男'),"女":__('女')}, formatter: Table.api.formatter.normal},
                        {field: 'school_id', title: __('School_id'), visible:false, operate:false},
                        {field: 'grade_id', title: __('Grade_id'), visible:false, operate:false},
                        {field: 'team_id', title: __('Team_id'), visible:false, operate:false},
                        {field: 'teacher_id', title: __('Teacher_id'), visible:false, operate:false},
                        { field: 'meet_id', title: __('Meet_id'), visible: false, operate:false },
                        {field: 'school.name', title: __('School.name'), operate: 'LIKE'},
                        {field: 'grade.name', title: __('Grade.name'), operate: 'LIKE'},
                        {field: 'team.name', title: __('Team.name'), operate: 'LIKE'},
                        {field: 'teacher.name', title: __('Teacher.name'), operate: 'LIKE'},
                        {field: 'meet.name', title: __('Meet.name'), operate: 'LIKE'},
                        {field: 'is_interest', title: __('Is_interest'), searchList: {"是":__('是'),"否":__('否')}, formatter: Table.api.formatter.normal},
                        { field: 'interest_id', title: __('Interest_id'), visible:false },
                        {field: 'interest.name', title: __('Interest.name'), operate: 'LIKE'},
                        { field: 'is_late', title: __('Is_late'), searchList: { "是": __('是'), "否": __('否') }, formatter: Table.api.formatter.normal},
                        {field: 'is_vip', title: __('Is_vip'), searchList: {"是":__('是'),"否":__('否')}, formatter: Table.api.formatter.normal},
                        {field: 'is_meet', title: __('Is_meet'), searchList: {"是":__('是'),"否":__('否')}, formatter: Table.api.formatter.normal},
                        {field: 'is_eat', title: __('Is_eat'), searchList: {"是":__('是'),"否":__('否')}, formatter: Table.api.formatter.normal},
                        {field: 'is_money', title: __('是否已交费'), searchList: { "是": __('是'), "否": __('否') }, formatter: Table.api.formatter.normal},
                        {field: 'mobile', title: __('Mobile'), operate: 'LIKE' },
                        {field: 'contact', title: __('Contact'), operate: false},
                        {field: 'remark', title: __('Remark'), operate: false, visible:false},
                        {
                            field: 'operate', title: __('Operate'),
                            buttons: [
                                {
                                    name: 'score_show',
                                    text: __('添加成绩'),
                                    title: __('添加成绩'),
                                    classname: 'btn btn-xs btn-primary btn-dialog score_show',
                                    icon: 'fa',
                                    extend: 'data-area=\'["90%", "75%"]\'',
                                    url: 'student/score_show',
                                },
                                {
                                    name: 'score_list',
                                    text: __('成绩记录'),
                                    title: __('成绩记录'),
                                    classname: 'btn btn-xs btn-primary btn-dialog score_list',
                                    icon: 'fa',
                                    extend: 'data-area=\'["90%", "75%"]\'',
                                    url: 'student/score_list',
                                },
                            ],
                            table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate
                        }
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
        score_show:function(){
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

$(".small_title").click(function () {
    $exam_log_id = $(this).attr("attr");
    $.ajax({
        url: "student/del_exam_log",
        type: 'post',
        data: {exam_log_id: $exam_log_id },
        success: function (ret) {
            window.location.reload();
        }, error: function (e) {
            Backend.api.toastr.error(e.message);
        }
    });
})

$(function () {
    $(".meet_div").css("display","1px solid red")
    var meet_value = $("#c-is_meet").val();
    if (meet_value == "否") {
        $("#c-meet_id").parent(".form-group").hide();
    } else {
        $("#c-meet_id").parent(".form-group").show();
    }

    $("#c-is_meet").on("change", function () {
        var this_value = $(this).val()
        if (this_value == "否") {
            alert("2")
            $("#c-meet_id").parent(".form-group").hide();
        } else {
            alert("3")
            $("#c-meet_id").parent(".form-group").show();
        }
    })
})
