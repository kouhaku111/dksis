<?php
session_start();
include_once('../a.php');

if(!isset($_SESSION['client_id']))
	header('location: ../index.php');

if(isset($_GET['schedule'])) {
	$query = "insert into schedule(day, time_start, time_end, student_start, student_end)
	values('{$_GET['day']}', '{$_GET['time_start']}', '{$_GET['time_end']}'
	, '{$_GET['student_start']}', '{$_GET['student_end']}')";

	pg_query(DB, $query);
}

if(isset($_GET['delete'])) {
	foreach($_GET['check'] as $id) {
		$query = "delete from schedule where id = '{$id}'";
		pg_query(DB, $query);
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset='UTF-8'>			
        <title>Trang Admin</title>
        <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
	<h1>ADMIN</h1>
        <div class="navbar">
	 		<a href="class.php">Danh sách lớp</a>
                    <div class="dropdown">
			  <button class="dropbtn">Danh sách viện
			      <i class="fa fa-caret-down"></i>
			  </button>
			  <div class="dropdown-content">
			      <?php
			      	$faculty = new facultyTable;
					$result = $faculty->getAll();
					while($row = pg_fetch_assoc($result)) {
						echo '<a href="cfaculty.php?id='.$row['faculty_id'].'">'. $row['name'] .'</a>'.' ';
					}
			      ?>
			  </div>

                    </div>
                        <a href="../logout.php">Đăng xuất</a>
	</div>
        <h2>Lịch đăng ký học tập</h2>
	<?php
            $schedule = new scheduleTable;
            $result = $schedule->getAll();
        ?>
	<form method="get" action="admin.php">
            <table>
            <?php while($row = pg_fetch_assoc($result)) { ?>
                <tr>
                    <td><?= $row['day'] ?></td>
                    <td><?= $row['time_start'] ?></td>
                    <td><?= $row['time_end'] ?></td>
                    <td><?= $row['student_start'] ?></td>
                    <td><?= $row['student_end'] ?>
                    <input type="checkbox" name="check[]" value=<?= $row['id'] ?>></td>
                </tr> 
            <?php } ?>
            </table>
            <input type="submit" id="delete" class="submit_button" name="delete" value="Xoá"><br><br><br>
	<form>
	<div>
		<form method="get" action="admin.php">
            <input type="date" class="textbox" name="day">
			<input type="time" class="textbox" name="time_start">
			<input type="time" class="textbox" name="time_end">
			<input type="text" class="textbox" name="student_start" placeholder="MSSV bắt đầu">
			<input type="text" class="textbox" name="student_end" placeholder="MSSV kết thúc">
 			<input type="submit" class="submit_button" name="schedule" value="Đặt lịch">
                        
		</form>
	</div>
</body>
</html> 