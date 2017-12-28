<?php
session_start();
include_once('../a.php');
if(!isset($_SESSION['client_id']))
	header('location: ../index.php');
?>
<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>Danh sách các lớp</title>
  <link rel="stylesheet" href="../css/student.css">
</head>
<body>
    <h1>Danh sách lớp hiện tại</h1>
	<div>
		<form method="get" action="list.php">
			<input type="text" name="id" class="textbox" placeholder="Mã lớp">
			<input type="text" name="name" class="textbox" placeholder="Tên lớp">
			<input type="text" name="subject" class="textbox" placeholder="Mã môn học">
			<input type="submit" name="search" class="submit_button" value="Tìm kiếm">
		</form>
	</div>
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
		$query = "SELECT c.* FROM Class c, Subject s WHERE c.subject_id = s.subject_id AND semester='20172' ";
		
		if(isset($_GET['id'])) if($_GET['id'] != '') 
			$query .= "AND c.class_code = '{$_GET['id']}'";

		if(isset($_GET['name'])) if($_GET['name'] != '') 
			$query .= "AND name like '%{$_GET['name']}%'";

		if(isset($_GET['subject'])) if($_GET['subject'] != '') 
			$query .= "AND c.subject_id = '{$_GET['subject']}'";

		if(!isset($_GET['page'])) $_SESSION['query'] = $query;

		//tinh total records
		$result = pg_query(DB, $_SESSION['query']);

		$total_record = (pg_num_rows($result) == 0) ? 1 : pg_num_rows($result);

		//tinh current page va limit
		$current_page = (isset($_GET['page'])) ? $_GET['page'] : 1;
		$limit = 10;

		//tinh total page
		$total_page = ceil($total_record/$limit);

		//gioi han current page
		if($current_page > $total_page) $current_page = $total_page;
		else if($current_page < 1) $current_page = 1;

		//tim start
		$start = ($current_page - 1)*$limit;
		//echo $start;
		//lay danh sach voi start va limit
		$query = $_SESSION['query'];
		//echo $current_page;
		if(isset($_GET['page']) || $current_page == 1) 
			$query .= "ORDER BY c.subject_id, c.class_code ASC LIMIT $limit OFFSET $start";
		//echo 'class get table query: '.$query;
		$result = pg_query(DB, $query);
	
		// tinh toan ti le qua mon
		$totalCredit = getSumCredit('20172', $_SESSION['client_id']);

		//hien thi danh sach
		while($class = pg_fetch_assoc($result)) {
			$register = new registerTable;
			$total = $register->getTotal('20172', $class['class_code']);
			echo '<tr>';
			echo "<td>". $class['class_code'] ."</td>";
			$subject = new subjectTable;
			$subject_info = $subject->get($class['subject_id']);

			echo "<td>". $subject_info['name'] ."</td>";

			$subject_id = $subject_info['subject_id'];
			echo "<td>". $subject_id ."</td>";

			$credit = $subject_info['credit'];
			echo "<td>". $credit ."</td>";

			echo "<td>". $class['place'] ."</td>";
			echo "<td>". $class['week_day'] ."</td>";
			echo "<td>". $class['time_start'] ."</td>";
			echo "<td>". $class['time_end'] ."</td>";
			echo "<td>".$total."/".$class['max_number']."</td>";

			$faculty = $subject_info['faculty_id'];
			$student_rate = studentRate($_SESSION['client_id'], $faculty);

			$teacher = new teacherTable;
			$teacher_id = $class['teacher_id'];
			$teacher_name = $teacher->get($teacher_id)['name'];

			echo '<td><a href="teacher.php?id='.$teacher_id.'">'.$teacher_name.'</a></td>';

			$teach = new teachTable;
			$teacher_rate = $teach->getRate($teacher_id, $subject_id);

			$rate = $student_rate * 0.5 + $teacher_rate * 0.5 - ($credit + $totalCredit) / 24 * 20; 
			echo "<td>". number_format((float)$rate, 2, '.', '') . '%' ."</td>";
			echo "</tr>";
		}
		?>
	</table><br>
	<ul class="pagination index">
	<?php
		//hien thi day so trang, cac nut next, prev
		if($current_page > 1 && $total_page > 1)
                    ?>
                        <li><a class="prev" href="list.php?page=<?= ($current_page-1) ?>">&laquo;</a></li>
            <?php
		for($i = 1; $i <= $total_page; $i++) {		// update: hien so ... 
			if($i == $current_page) {
                            ?>
                        <li><a href="#"><?= $i ?></a></li>
                    <?php
			} else {
                            ?>
                        <li><a href="list.php?page=<?= $i ?>"><?= $i ?></a></li>
                    <?php
			}
		}
		if ($current_page < $total_page&& $total_page > 1)
                    ?>
                        <li><a class="next" href="list.php?page=<?= ($current_page+1) ?>">&raquo;</a></li>
        </ul> 
	<div>
		<input type="button" class="button" onclick="location.href='student.php';" value="Đăng ký">
		<input type="button" class="button" onclick="location.href='../logout.php';" value="Đăng xuất">
	</div>
</body>
</html>