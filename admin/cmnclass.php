<?php
session_start();
include_once('../a.php');

if(!isset($_SESSION['client_id']))
	header('location: ../index.php');

if(isset($_GET['id'])) {
	$_SESSION['class_id'] = $_GET['id'];
}
?>
<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>Lớp quản lý</title>
  <link rel="stylesheet" href="../css/cmnclass.css">
</head>
<body>
    <?php 
        $mn_class = new mnClassTable;
        $class_name = $mn_class->get($_SESSION['class_id'])['name'];

        $client = new clientTable;
        $result = $client->getAll($_SESSION['class_id']);
    ?>
    <h1><?= $class_name ?></h1>
    <table>
        <tr>
            <th>Mã số sinh viên</th>
            <th>Tên sinh viên</th>
            <th>Số tín chỉ nợ</th>
        </tr>
        <?php
            while($row = pg_fetch_assoc($result)) { ?>
        <tr>
            <td><?= $row['client_id'] ?></td>
            <td><?= $row['name'] ?></td>
            <td><?= getOwnCredit($row['client_id']) ?></td>
        </tr>
        <?php } ?>
    </table>
        <input type="button" class="button" onclick="location.href='cfaculty.php';" value="Thông tin viện">
        <input type="button" class="button" onclick="location.href='../logout.php';" value="Đăng xuất"><br>
    </table>
</body>
</html>