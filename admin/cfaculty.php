<?php
session_start();
include_once('../a.php');
if(!isset($_SESSION['client_id']))
	header('location: ../index.php');

if(isset($_GET['id'])) {
	$_SESSION['faculty_id'] = $_GET['id'];
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Title of the document</title>
<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
 <link rel="stylesheet" href="../css/cfaculty.css">
</head>
 <body>

<div class="navbar">
		  <div class="dropdown">
			  <button class="dropbtn">Danh sách lớp
			      <i class="fa fa-caret-down"></i>
			  </button>
			  <div class="dropdown-content">
			      <?php
			      	$mn_class = new mnClassTable;
					$result = $mn_class->getByFaculty($_SESSION['faculty_id']);
					while($row = pg_fetch_assoc($result)) {
						echo '<a href="cmnclass.php?id='.$row['id'].'">'. $row['name'] .'</a>'.' ';
					}
			      ?>
			  </div>
			</div>

		<div class="dropdown">
			  <button class="dropbtn">Danh sách giảng viên
			      <i class="fa fa-caret-down"></i>
			  </button>
			  <div class="dropdown-content">
			      <?php
			      	$teacher = new teacherTable;
					$result = $teacher->getByFaculty($_SESSION['faculty_id']);
					while($row = pg_fetch_assoc($result)) {
						echo '<a href="cteacher.php?id='.$row['teacher_id'].'">'. $row['name'] .'</a>'.' ';
					}
			      ?>
			  </div>
		</div>

		<a href="admin.php">Quản lý</a>
		<a href="../logout.php">Đăng xuất</a>
</div> 
<?php
$query =  "with tmp as (
select t.subject_id,
round(cast(sum(rate) / (select count(*) from teach where subject_id = t.subject_id) as numeric), 2) as rate
from teach t
group by t.subject_id
)
select t.subject_id, t.rate
from faculty f, subject s, tmp t
where t.subject_id = s.subject_id
and s.faculty_id = f.faculty_id
and f.faculty_id = '{$_SESSION['faculty_id']}'";

//echo $query;
$result = pg_query(DB, $query);
$i = 0;
while($row = pg_fetch_assoc($result)) {
    if($row['rate'] != NULL) {
        //echo $row['subject_id'];
        $dataPoints[$i]["y"] = $row['rate'];
        $dataPoints[$i]["label"] = $row['subject_id'];
        $i += 1;
    }
}
?>
<?php
if(isset($_GET['semester']) && isset($_GET['ay'])) {
	$query = "
	with tmp as (
	select f.faculty_id, client_id
	from client c, faculty f, management_class m
	where f.faculty_id = '{$_SESSION['faculty_id']}'
	and academic_year = '{$_GET['ay']}'
	and c.management_class = m.id
	),
	tmp_2 as (
	select * from tmp join semester_result on (client_id = student_id)
	where semester = '{$_GET['semester']}'
	),
	tmp_excellent as (
	select round(cast(count(*) * 100 / (select count(*) from tmp) as numeric), 2) as excellent
	from tmp_2
	where final_result >= 4),
	tmp_good as (
	select round(cast(count(*) * 100 / (select count(*) from tmp) as numeric), 2) as good
	from tmp_2
	where final_result >= 3 and final_result < 4),
	tmp_average as (
	select round(cast(count(*) * 100 / (select count(*) from tmp) as numeric), 2) as average
	from tmp_2
	where final_result >= 2 and final_result < 3),
	tmp_below_average as (
	select round(cast(count(*) * 100 / (select count(*) from tmp) as numeric), 2) as below_average
	from tmp_2
	where final_result >= 1 and final_result < 2),
	tmp_bad as (
	select round(cast(count(*) * 100 / (select count(*) from tmp) as numeric), 2) as bad
	from tmp_2
	where final_result < 1)
	select excellent, good, average, below_average, bad 
	from tmp_excellent, tmp_good, tmp_average, tmp_below_average, tmp_bad
	";

	$result = pg_query(DB, $query);
	$row = pg_fetch_assoc($result);


	$dataPoints_1[0]['y'] = $row['excellent'];
	$dataPoints_1[0]['name'] = 'xuất sắc';

	$dataPoints_1[1]['y'] = $row['good'];
	$dataPoints_1[1]['name'] = 'giỏi';

	$dataPoints_1[2]['y'] = $row['average'];
	$dataPoints_1[2]['name'] = 'khá';

	$dataPoints_1[3]['y'] = $row['below_average'];
	$dataPoints_1[3]['name'] = 'trung bình';

	$dataPoints_1[4]['y'] = $row['bad'];
	$dataPoints_1[4]['name'] = 'yếu';
}
?>  

<div id="chartContainer1" style="width: 100%; height: 300px; display: inline-block;"></div><br/>
<div id="chartContainer2" style="width: 50%; height: 300px; display: inline-block;"></div>
<div id="top_list" style="float: right; width: 50%;">
	<table border="1" cellspacing="0">
		<tr>
			<th>Thứ hạng</th>
			<th>MSSV</th>
			<th>Tên sinh viên</th>
		</tr>
	<?php
		$query = "select student_id as mssv, c.name as hoten
				from semester_result s, client c, management_class m, faculty f
				where f.faculty_id = '{$_SESSION['faculty_id']}'
				and semester = '{$_GET['semester']}' 
				and s.student_id = c.client_id
				and academic_year = '{$_GET['ay']}'
				and c.management_class = m.id
				and m.faculty_id = f.faculty_id
				order by final_result desc
				limit 20 offset 0";
		$result = pg_query(DB, $query);
		$i = 1;
		while($row = pg_fetch_assoc($result)) {
			echo '<tr>';
			echo '<td>'. $i .'</td>';
			echo '<td>'. $row['mssv'] .'</td>';
			echo '<td>'. $row['hoten'] .'</td>';
			echo '</tr>';
			$i += 1;
		}
	?>
	</table>
</div>
<script type="text/javascript">
 
$(function () {
var chart1 = new CanvasJS.Chart("chartContainer1", {
	animationEnabled: true,
	title: {
		text: "Tỉ lệ sinh viên qua môn"
	},
	axisX: {
		title: "Môn học",
	},
	axisY: {
		title: "Tỉ lệ qua môn",
		maximum: 100,
		interval: 20
	},
	data: [
	{
		type: "column",                
		dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
	}
	]
});
chart1.render();

var chart2 = new CanvasJS.Chart("chartContainer2", {
	theme: "theme2",
	title:{
		text: "Cấu trúc điểm số sinh viên"
	},
	exportFileName: "point_percentage",
	exportEnabled: true,
	animationEnabled: true,		
	data: [
	{       
		type: "pie",
		showInLegend: true,
		toolTipContent: "{name}: <strong>{y}%</strong>",
		indexLabel: "{name} {y}%",
		dataPoints: <?php echo json_encode($dataPoints_1, JSON_NUMERIC_CHECK); ?>
	}]
});
chart2.render();
});
</script>

<form method="get" action="cfaculty.php">
	<input type="text" name="semester" placeholder="Kỳ học">
	<select name="ay">
		<option value="K59">K59</option>
    	<option value="K60">K60</option>
    	<option value="K61">K61</option>
    	<option value="K62">K62</option>
  	</select>
	<input type="submit" name="submit" value="Gửi">
</form>
</body> 
</html>