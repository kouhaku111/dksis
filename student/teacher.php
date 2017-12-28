<?php
session_start();
if(!isset($_SESSION['client_id']))
	header('location: ../index.php');
?>

<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>Giáo viên</title>
  <link rel="stylesheet" href="../css/teacher.css">
</head>
<body>
    <h1>Thông tin giảng viên</h1>
	<?php
	include_once('../a.php');
	if(isset($_GET['id'])) {
		$teacher = new teacherTable;
		$teacher_info = $teacher->get($_GET['id']);
                $faculty = new facultyTable;
                $faculty_info = $faculty->get($teacher_info['faculty_id']);
                ?>
    <table id="page-wrap">
        <tr>
            <td>
                <img src="../images/headshot.jpg" alt="teacher image" width="120" height="150">
            </td>
            <td colspan="2">
                <ul>
                    <li><b>Họ và tên: </b><?= $teacher_info['name'] ?></li>
                    <li><b>Khoa/Viện: </b><?= $faculty_info['name'] ?></li>
                </ul>
            </td>
        </tr>
            <?php
                $teach = new teachTable;
		$result = $teach->getAll($teacher_info['teacher_id']);
		while($row = pg_fetch_assoc($result)) {
			$subject = new subjectTable;
			$subject_info = $subject->get($row['subject_id']);
            ?>
        <tr>
            <td>
                <?= $subject_info['subject_id'] ?> 
            </td>
            <td>
                <?= $subject_info['name'] ?>
            </td>
            <?php $teacher_rate = $teach->getRate($teacher_info['teacher_id'], $subject_info['subject_id']); ?>
            <td>
                <?= $teacher_rate.'%' ?>
            </td>
        </tr>
	<?php }
        } ?>
    </table>
	<div>
		<input type="button" class="button" onclick="location.href='student.php';" value="Đăng ký">
		<input type="button" class="button" onclick="location.href='list.php';" value="Danh sách lớp">
		<input type="button" class="button" onclick="location.href='../logout.php';" value="Đăng xuất">
	</div>
</body>
</html>