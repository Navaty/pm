<?include_once "statusage.php"; //by almaz - usage control
?><!DOCTYPE html>
<html>
<body>

<form action="filesupload.php" method="post" enctype="multipart/form-data">
   Выбери файл для загрузки
    <input type="file" name="fileToUpload" id="fileToUpload">
    <input type="submit" value="Загрузить файл" name="submit">
</form>

</body>
</html>