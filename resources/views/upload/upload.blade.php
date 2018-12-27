<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>

    <script src="/assets/jquery.min.js"></script>
    <script src="/assets/jQuery.upload.js"></script>
    <link rel="stylesheet" href="/assets/upload.css">
    <link rel="stylesheet" href="/assets/layui/css/layui.css"  media="all">
    <style>
        html,body,h1,h2,h3,h4,h5,h6,div,dl,dt,dd,ul,ol,li,p,blockquote,pre,hr,figure,table,caption,th,td,form,fieldset,legend,input,button,textarea,menu{margin:0;padding:0;}

        body{padding:100px;font-size: 14px;}
        h1{font-size: 56px;}
        p{font-size: 14px; margin-top: 10px;}
        pre{background:#eee;border:1px solid #ddd;border-left:4px solid #f60;padding:15px;margin-top: 15px;}
        h2{font-size: 20px;margin-top: 20px;}
        .case{margin-top: 15px;width:100%;}
        td{padding:5px;}
        table{margin-top: 20px;}
        .title{text-align: center;margin: 0 auto;}
        .upload{margin-top: 50px;}
        .upload:hover{cursor: pointer;}

    </style>

</head>
<body>
<div class="case">
    <div class="title"><h1>{{ $title }}</h1></div>
    <div class="upload" action='/export' data-num='1' id='case2'></div>
</div>

<div style="height: 300px"></div>

<div style="width:762px;margin: 0 auto;">
    <table class="layui-table" lay-data="{width: 762, url:'/bank/info/list', id:'idTest'}" lay-filter="demo">
        <thead>
        <tr>
            {{--<th lay-data="{type:'checkbox', fixed: 'left'}"></th>--}}
            <th lay-data="{type:'numbers', width:80, align:'center'}">序号</th>
            <th lay-data="{field:'bank_code', width:120}">项目别名</th>
            <th lay-data="{field:'project_name', width:200}">项目名</th>
            <th lay-data="{field:'on_duty_time_str', width:120, align:'center'}">上班时间</th>
            <th lay-data="{field:'off_duty_time_str', width:120, align:'center'}">下班时间</th>
            <th lay-data="{width:120, align:'center', toolbar: '#barDemo'}">操作</th>
        </tr>
        </thead>
    </table>
</div>

<script type="text/html" id="barDemo">
    {{--<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="detail">查看</a>--}}
    <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
    <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
</script>
</body>


<script src="/assets/layui/layui.js" charset="utf-8"></script>
<script>
    $(function(){
        // $("#case1").upload();
        $("#case2").upload();
        // $("#case3").upload(
        //     function(_this,data){
        //         alert(data)
        //     }
        // )

        function doDel(obj)
        {
            var data = obj.data;

            layer.confirm('确定删除【' + data.project_name + '】吗？', function(index)
            {
                $.ajax({
                    url: "{{ config('domain.common') }}bank/info/del?id=" + data.id,
                    success: function(res) {
                        if (res.code === 0) {
                            obj.del();
                            layer.close(index);
                            layer.msg('【' + data.project_name + '】删除成功！');
                        } else {
                            layer.msg(res.msg);
                        }
                    },
                    error: function (err) {
                        data = err.responseJSON;
                        layer.alert(data.message);
                    }
                });

            });
        }

        layui.use('table', function(){
            var table = layui.table;
            //监听表格复选框选择
            // table.on('checkbox(demo)', function(obj){
            //     console.log(obj)
            // });
            //监听工具条
            table.on('tool(demo)', function(obj)
            {
                var data = obj.data;

                if(obj.event === 'detail')
                {
                    layer.msg('ID：'+ data.id + ' 的查看操作');

                } else if(obj.event === 'del')
                {
                    doDel(obj);

                } else if(obj.event === 'edit')
                {
                    layer.alert('编辑行：<br>'+ JSON.stringify(data))
                }
            });

            // var $ = layui.$, active = {
            //     getCheckData: function(){ //获取选中数据
            //         var checkStatus = table.checkStatus('idTest')
            //             ,data = checkStatus.data;
            //         layer.alert(JSON.stringify(data));
            //     }
            //     ,getCheckLength: function(){ //获取选中数目
            //         var checkStatus = table.checkStatus('idTest')
            //             ,data = checkStatus.data;
            //         layer.msg('选中了：'+ data.length + ' 个');
            //     }
            //     ,isAll: function(){ //验证是否全选
            //         var checkStatus = table.checkStatus('idTest');
            //         layer.msg(checkStatus.isAll ? '全选': '未全选')
            //     }
            // };

            $('.demoTable .layui-btn').on('click', function()
            {
                var type = $(this).data('type');
                active[type] ? active[type].call(this) : '';
            });
        });
    })
</script>
</html>