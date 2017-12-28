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
  <title>Chương trình đào tạo</title>
  <link rel="stylesheet" href="../css/student.css">
</head>
<body>
    <h1>Danh sách các môn học</h1>
	<div>
		<form method="get" action="curriculum.php">
                    <input type="text" name="semester"  class="textbox" placeholder="Kỳ học">
                    <input type="text" name="subject_id" class="textbox" placeholder="Mã môn học">
                    <input type="text" name="subject_name" class="textbox" placeholder="Tên môn học">
                    <input type="submit" name="search" class="submit_button" value="Tìm kiếm">
		</form>
	</div>
	<table id="page-wrap">
		<tr>
			<th>Mã môn học</th>
			<th>Tên môn học</th>
			<th>Điểm giữa kỳ</th>
			<th>Điểm cuối kỳ</th>
			<th>Điểm số</th>
			<th>Điểm chữ</th>
		</tr>
		<?php
		$query = "SELECT * FROM curriculum c, subject s, client cl
				  WHERE cl.client_id = '{$_SESSION['client_id']}'
				  and  cl.management_class = c.class_id
				  and c.subject_id=s.subject_id ";
		if(isset($_GET['semester'])) 
			if($_GET['semester'] != '') $query .= "AND semester = '{$_GET['semester']}'";
		if(isset($_GET['subject_id'])) 
			if($_GET['subject_id'] != '') $query .= "AND c.subject_id = '{$_GET['subject_id']}'";
		if(isset($_GET['subject_name'])) 
			if($_GET['subject_name'] != '') $query .= "AND s.name like '%{$_GET['subject_name']}%'";
		if(!isset($_GET['page'])) $_SESSION['query'] = $query;
		// echo "search query: ".$query;
		// tinh total records
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
		//lay danh sach voi start va limit
		$query = $_SESSION['query'];
		//echo $current_page;
		if(isset($_GET['page']) || $current_page == 1) $query .= "ORDER BY semester, c.subject_id ASC LIMIT $limit OFFSET $start";
		//echo 'class get table query: '.$query;
		//echo "search query: ".$query;
		$result = pg_query(DB, $query);
		//hien thi danh sach
		while($row = pg_fetch_assoc($result)) {
			$subject = new subjectTable;
			$subject_info = $subject->get($row['subject_id']);
			$query_1 = "select * from result 
					where student_id = '{$_SESSION['client_id']}' and subject_id='{$subject_info['subject_id']}'
					and 
					gpa = (select max(gpa) from result 
							 where student_id = '{$_SESSION['client_id']}' 
							 and subject_id='{$subject_info['subject_id']}')";	
							 // lay thanh tich tot nhat cua sinh vien
			//echo $query_1;
			$best = pg_query(DB, $query_1);
			$best = pg_fetch_assoc($best);				 
			echo '<tr>';
			echo '<td>' . $subject_info['subject_id'] . '</td>';
			echo '<td>' . $subject_info['name'] . '</td>';
			echo '<td>' . $best['mid_term'] . '</td>';
			echo '<td>' . $best['final_term'] . '</td>';
			echo '<td>' . $best['total'] . '</td>';
			echo '<td>' . $best['grade'] . '</td>';
			echo '</tr>';
		}
		?>
	</table><br>

	<ul class="pagination index">
	<?php
		//hien thi day so trang, cac nut next, prev
		if($current_page > 1 && $total_page > 1)
                    ?>
                        <li><a class="prev" href="curriculum.php?page=<?= ($current_page-1) ?>">&laquo;</a></li>
            <?php
		for($i = 1; $i <= $total_page; $i++) {		// update: hien so ... 
			if($i == $current_page) {
                            ?>
                        <li><a href="#"><?= $i ?></a></li>
                    <?php
			} else {
                            ?>
                        <li><a href="curriculum.php?page=<?= $i ?>"><?= $i ?></a></li>
                    <?php
			}
		}
		if ($current_page < $total_page&& $total_page > 1)
                    ?>
                        <li><a class="next" href="curriculum.php?page=<?= ($current_page+1) ?>">&raquo;</a></li>
        </ul> 
        <h1>Thành tích học tập</h1>
	<table id="page-wrap">
            <tr>
                <th>Kỳ học</th>
                <th>Tổng số tín chỉ</th>
                <th>GPA</th>
                <th>Thứ hạng theo kỳ</th>
            </tr>
            <tr class="result">
		<?php
		//tong ket gpa, cpa
		$sr = new semesterResultTable($_SESSION['client_id']);
		$result = $sr->getRank();
		while($row = pg_fetch_assoc($result)) {
			//var_dump($row);
                    ?>
                    <td class="result"><?= $row['semester'] ?></td>
                    <td class="result"><?= $row['total_credit'] ?></td>
                    <td class="result"><?= $row['gpa'] ?></td>
                    <td class="result"><?= $row['rank'] ?></td>
            </tr>
		<?php } 
                    $rt = new rankTable($_SESSION['client_id']);
                ?>
            <tr>
                <td colspan="4" rowspan="3">
                    <li id="rank">Xếp loại trong lớp: <?= $rt->getByClass() ?></li>
                    <li id="rank">Xếp loại trong viện: <?= $rt->getByFaculty()?></li>
                    <li id="rank">Xếp loại trong trường: <?= $rt->getByAll()?></li>
                </td>
            </tr>
        </table>
	<div class="page">
            <input type="button" class="button" onclick="location.href='student.php';" value="Đăng ký">
            <input type="button" class="button" onclick="location.href='../logout.php';" value="Đăng xuất">
	</div>
</body>
</html>