<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
    <form action="/upload" method="post" enctype="multipart/form-data">
        <div>
            <select name="bank_code" id="">
                <option value="gfbank">广州广发手机银行</option>
                <option value="gfzse">广发掌上E</option>
                <option value="xybank">广州兴业银行</option>
                <option value="xyds">广州兴业电商</option>
                <option value="nsbank">深圳南商项目组</option>
                <option value="public">广州办公室</option>
                <option value="bjryt">北京融易通</option>
                <option value="shdy">上海电银</option>
                <option value="jhby">交行北研项目组（现）</option>
                <option value="jhxmz">交行项目组</option>
                <option value="qdbank">山东青岛银行项目组</option>
                <option value="hxbank">广州华兴银行</option>
                <option value="hxyh">华夏银行项目组</option>
                <option value="shjh">上海交行</option>
                <option value="dbbank">东北</option>
                <option value="dyyh">广州德阳银行</option>
                <option value="hfyh">恒丰项目组</option>
                <option value="zsbank">浙商银行</option>
                <option value="snsbank">上海农商</option>
                <option value="jhbank">交行卡中心</option>
                <option value="bjyhxmz">北京银行项目组</option>
                <option value="hbbank">北京河北银行</option>
            </select>&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="file" name="excel">
            <input type="submit" value="上传">
        </div>
        <!-- <div>
            上班时间：<input type="text" value="">
            下班时间：<input type="text" value="">
        </div> -->
    </form>
</body>
</html>