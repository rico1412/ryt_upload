<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>RYTong</title>
    <link href='/favicon.ico' rel='shortcut icon' type='image/x-icon'>
</head>
<body>
    <form action="/export" method="post" enctype="multipart/form-data">
        <div>
            <select name="bank_code" id="">
                @foreach($projectList as $projectInfo)
                <option value="{{ $projectInfo->bank_code }}">{{ $projectInfo->project_name }}</option>
                @endforeach
            </select>
            <input type="file" name="excel">
            <input type="submit" value="上传">
        </div>
    </form>
</body>
</html>