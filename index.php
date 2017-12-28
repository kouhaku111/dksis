<?php
session_start();
include_once('a.php');

$warning = '';
$client = new clientTable;

if(isset($_SESSION['client_id'])) {
	$privilege = $client->get($_SESSION['client_id'])['privilege'];
	if($privilege == '1') {
		header('location: student/student.php');
	} else if($privilege == '2') {
		header('location: admin/admin.php');
	}
}

$schedule = new scheduleTable;

if(isset($_GET['login'])) {
	$authenticate = $client->check($_GET['id'], $_GET['password']);

	if($authenticate) {
		$privilege = $client->get($_GET['id'])['privilege'];

		if($privilege == '1') {
			if($schedule->check($_GET['id'])) {
				$_SESSION['client_id'] = $_GET['id'];
				header('location: student/student.php');
			} else {
				$warning = 'not your time to login';
			}
		} else if($privilege == '2') {
			$_SESSION['client_id'] = $_GET['id'];
			header('location: admin/admin.php');
		}
	}
	else {
		$warning = "Wrong username or password";
	}
}
?>

<!DOCTYPE html>
<html lang="en" >

<head>
  <meta charset="UTF-8">
  <title>DK-SIS</title>
  <link rel="stylesheet" href="css/index.css">
</head>

<body>
  <hgroup>
  <h1>TRANG ĐĂNG KÝ TÍN CHỈ</h1>
  <h3>TRƯỜNG ĐẠI HỌC BÁCH KHOA HÀ NỘI</h3>
</hgroup>
<form action="index.php" method="get"> 
  <div class="group">
    <input type="text" name="id" placeholder="Mã sinh viên"required><span class="highlight"></span><span class="bar"></span>
  </div>
  <div class="group">
      <input type="password" name="password" placeholder="Mật khẩu" required><span class="highlight"></span><span class="bar"></span>
    
  </div>
  <button type="submit" name="login" class="button buttonBlue" value="login">Đăng nhập
    <div class="ripples buttonRipples"><span class="ripplesCircle"></span></div>
  </button>
</form>
    <?php
        echo "<div id='warning'><strong>".$warning."</strong></div>";
    ?>
    <footer><a href="https://hust.edu.vn/" target=""><img src="images/bklogo.jpg" width="50px" height="80px"></a>
    </footer>
  <script src='js/jquery.min.js'></script>
  <script  src="js/index.js"></script>

</body>
</html>

