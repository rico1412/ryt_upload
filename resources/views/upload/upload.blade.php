<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>

    <script src="/assets/jquery.min.js"></script>
    <script src="/assets/jQuery.upload.js"></script>
    <link rel="stylesheet" href="/assets/upload.css">
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
</body>
<script>
    $(function(){
        // $("#case1").upload();
        $("#case2").upload();
        // $("#case3").upload(
        //     function(_this,data){
        //         alert(data)
        //     }
        // )
    })
</script>
</html>