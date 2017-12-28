<?php
session_start();
include_once('../a.php');
if(isset($_GET['check'])) {
	$_SESSION['class_code'] = $_GET['class_code'];
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset='UTF-8'>		
	<meta name="viewport" content="width=device-width, initial-scale=1.0">	
        <title>Chỉnh sửa thông tin lớp hiện tại</title>
        <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <h1>Chỉnh sửa thông tin lớp</h1>
	<?php
	$register = new registerTable;
	if(isset($_GET['add'])) {
		$register->add('20172', $_GET['client_id'], $_SESSION['class_code']);
	}
	if(isset($_GET['delete'])) {
		$register->delete('20172', $_GET['client_id'], $_SESSION['class_code']);
	}
	
	$class = new classTable;
	$row = $class->get('20172', $_SESSION['class_code']);
	$subject = new subjectTable;
	$subject_info = $subject->get($row['subject_id']);
            ?>
            <div style="margin-bottom: -10px">
                <ul class="cclass">
                    <li>Mã lớp: <b><?= $row['class_code'] ?></b></li>
                    <li>Tên lớp: <b><?= $subject_info['name'] ?></b></li>
                    <li>Mã môn: <b><?= $row['subject_id'] ?></b></li>
                    <li>Địa điểm: <b><?= $row['place'] ?></b></li>
                    <li>Thời gian bắt đầu: <b><?= $row['time_start'] ?></b></li>
                    <li>Thời gian kết thúc: <b><?= $row['time_end'] ?></b></li>
                <?php
                    $total = $register->getTotal('20172', $row['class_code']);
                ?>
                <li>Số lượng đăng ký: <b><?= $total . "/" . $row['max_number'] ?></b></li>
                </ul>
            </div>

	<br>
        <table>
		<tr>
			<th>MSSV</th>
			<th>Họ và tên</th>
			<th>Điểm giữa kỳ</th>
			<th>Điểm cuối kỳ</th>
		</tr>
	<?php
	if(isset($_GET['update'])) {
		$query = "update result set mid_term = '{$_GET['mid_term']}', final_term = '{$_GET['final_term']}'
				where semester = '20172' and student_id = '{$_GET['client_id']}'
				and subject_id = '{$subject_info['subject_id']}'";
		pg_query(DB, $query);
	}

	$result = $class->getAll('20172', $_SESSION['class_code']);
	while($row = pg_fetch_assoc($result)) {
            ?>
		<tr>
		<td><?= $row['student_id'] ?></td>
		<td><?= $row['name'] ?></td>
		<td><?= $row['mid_term'] ?></td>
		<td><?= $row['final_term'] ?></td>
		</tr>
	<?php } ?>
        </table><br><br>
	<div>
		<form method="get" action="cclass.php">
                        <input type="text" class="textbox" name="client_id" placeholder="MSSV">
			<input type="text" class="textbox" name="mid_term" placeholder="Điểm giữa kỳ">
			<input type="text" class="textbox" name="final_term" placeholder="Điểm cuối kỳ">
			<input type="submit" class="submit_button" name="update" value="Cập nhật">
		</form>
<!--		 <form method="get" action="cclass.php">
            <input type="text" class="textbox" name="client_id" placeholder="MSSV">
            <input type="submit" class="submit_button" name="add" value="Add">
		</form>
		<form method="get" action="cclass.php">
            <input type="text" class="textbox" name="client_id" placeholder="MSSV">
            <input type="submit" class="submit_button" name="delete" value="Delete">
		</form> -->
	</div>
	<div>
            <input type="button" class="button" onclick="location.href='class.php';" value="Danh sách lớp">
            <input type="button" class="button" onclick="location.href='admin.php';" value="Quản lý">
            <input type="button" class="button" onclick="location.href='../logout.php';" value="Đăng xuất">
	</div>
</body>
</html>