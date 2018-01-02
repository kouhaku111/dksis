<?php
session_start();
include_once('../a.php');
include_once('../cregister.php');

if(!isset($_SESSION['client_id']))
	header('location: ../index.php');

$warning = "";

$register = new registerTable;
if(isset($_GET['register'])) {
    if(!empty($_GET['class_id'])) {
	$error = checkRegister('20172', $_GET['class_id']);
	if($error == 1)	$warning = "Lớp không tồn tại";
	if($error == 2)	$warning = "Lớp đã đăng ký";
	if($error == 3)	$warning = "Vượt qua số tín chỉ cho phép";
	if($error == 4)	$warning = "Không đủ điều kiện đăng ký";
	if($error == 5)	$warning = "Trùng giờ với lớp đã đăng ký";
	if($error == 6) $warning = "Lớp đã đầy";	
	if($error == 0) {
		$register->add('20172', $_SESSION['client_id'], $_GET['class_id']);
	}
    }
}
if(isset($_GET['delete'])) {
    if(isset($_GET['check'])) {
       foreach($_GET['check'] as $class_code) {
        //echo $class_code;		
        $register->delete('20172', $_SESSION['client_id'], $class_code);
	} 
    }
}
?>
<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>Thông tin đăng ký</title>
  <link rel="stylesheet" href="../css/student.css">
</head>
<body>
	<h1>Thông tin đăng ký của sinh viên</h1>
        <form method="get" action="student.php">
	<table id="page-wrap">
		<tr>
			<th>Mã lớp</th>
			<th>Tên lớp</th>
			<th>Mã môn học</th>
			<th>Số tín chỉ</th>
			<th>Địa điểm</th>
			<th>Ngày trong tuần</th>
			<th>Bắt đầu</th>
			<th>Kết thúc</th>
			<th>Số lượng</th>
			<th>Giáo viên</th>
			<th>Tỉ lệ qua môn</th>
		</tr>
		<?php
			$register = new registerTable;
			$result = $register->getAll('20172', $_SESSION['client_id']);

			// tinh ti le qua mon
			$totalCredit = getSumCredit('20172', $_SESSION['client_id']);
			
			while($row = pg_fetch_assoc($result)) {
				$class = new classTable;
				$class_info = $class->get('20172', $row['class_code']);
				echo '<tr>';
				echo "<td>". $class_info['class_code'] ."</td>";

				$subject = new subjectTable;
				$subject_info = $subject->get($class_info['subject_id']);
				$subject_id = $subject_info['subject_id'];

				echo "<td>". $subject_info['name'] ."</td>";
				echo "<td>". $subject_id ."</td>";

				$credit = $subject_info['credit'];
				echo "<td>". $credit. "</td>";

				echo "<td>". $class_info['place'] ."</td>";
				echo "<td>". $class_info['week_day'] ."</td>";
				echo "<td>". $class_info['time_start'] ."</td>";
				echo "<td>". $class_info['time_end'] ."</td>";

				$faculty = $subject_info['faculty_id'];
				$student_rate = studentRate($_SESSION['client_id'], $faculty);

				$total = $register->getTotal('20172', $class_info['class_code']);
				echo "<td> " . $total . "/" . $class_info['max_number'] . "</td>";

				$teacher = new teacherTable;
				$teacher_id = $class_info['teacher_id'];
				$teacher_name = $teacher->get($teacher_id)['name'];
				echo '<td><a href="teacher.php?id='.$teacher_id.'">'.$teacher_name.'</a></td>';
				//echo '<td>'. $teacher_id .'</td>';

				$teach = new teachTable;
				$teacher_rate = $teach->getRate($teacher_id, $subject_id);

				$rate = $student_rate * 0.5 + $teacher_rate * 0.5 - ($credit + $totalCredit) / 24 * 20; 
				$rate = ($rate < 0) ? 0 : $rate;
				echo "<td>". number_format((float)$rate, 2, '.', '') . '%';
                                echo '<input type="checkbox" name="check[]" value="'.$row['class_code'].'">' ."</td>";
				echo "</tr>";
			}
		?>
	</table>
            <input type="submit" id="delButton" class="submit_button" name="delete" value="Xoá"><br>
		</form>
	<div id ="warning">
		<?php echo $warning ?>
	</div>
	<div>
		<form method="get" action="student.php">
                    <input type="text" class="textbox" name="class_id" placeholder="Mã lớp">
                    <input type="submit" class="submit_button" name="register" value="Đăng ký">
		</form>
		
	</div>
	<div>
		<input type="button" class="button" onclick="location.href='curriculum.php';" value="Chương trình đào tạo">
		<input type="button" class="button" onclick="location.href='list.php';" value="Danh sách lớp học">
		<input type="button" class="button" onclick="location.href='../logout.php';" value="Đăng xuất">
	</div>
</body>
</html>