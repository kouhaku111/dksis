<?php
//session_start();
include_once('a.php');

// check xem lop co mo ki nay hay khong
// return true neu co mo, false neu khong mo
function checkClassTable($semester, $class_code) {
	$class = new classTable;
	return $class->check($semester, $class_code);
}
// da check


// check xem da dang ki lop day chua
// return true neu da dang ki, false neu chua
function checkRegisterTable($semester, $class_code) {
	$register = new registerTable;
	$result = $register->getAll($semester, $_SESSION['client_id']);
	while($row = pg_fetch_assoc($result)) {
		if($row['class_code'] == $class_code) {
			return true;
		}
	}
	return false;
}
// da check

// check xem co qua so tin chi hay khong
// return true neu da qua, false neu chua
function checkCredit($semester, $class_code) {
	$class = new classTable;
	$subject = new subjectTable;
	$sum = getSumCredit($semester, $_SESSION["client_id"]);
	$class_info = $class->get($semester, $class_code);
	$subject_info = $subject->get($class_info["subject_id"]);
	if($subject_info["credit"] + $sum > 24) {
		return true;
	} else {
		return false;
	}
}
// da check

// check xem da qua het cac mon yeu cau chua
// return true neu qua het, false neu chua qua
function checkRequire($semester, $class_code) {
	$class = new classTable;
	$require = new requireTable;
	$result = new resultTable;
	$subject = $class->get($semester, $class_code)['subject_id'];
	$table = $require->getAll($subject);
	while($row = pg_fetch_assoc($table)) {
		$require_subject = $row['require_subject'];
		$pass = $result->check($_SESSION['client_id'], $require_subject);
		if(!$pass) {	// chua qua mon
			return false;
		}
	}
	return true;
}
// da check

// check xem co trung gio voi lop nao da dang ki hay khong
// return true neu trung gio, false neu khong trung
function checkTimeConflict($semester, $class_code) {
	$class = new classTable;
	$register = new registerTable;
	
	// lay thon tin lop dinh dang ki
	$class_info = $class->get($semester, $class_code);

	// lay danh sach lop da dang ki
	$result = $register->getAll($semester, $_SESSION['client_id']);

	while($row = pg_fetch_assoc($result)) {
		// lay thong tin lop da dang ki	
		$register_class = $class->get($semester, $row['class_code']);

		if($class_info['week_day'] === $register_class['week_day']) {
			if(!($class_info['time_start'] >= $register_class['time_end']
				||  $class_info['time_end'] <= $register_class['time_start'])) {
				return true;
			}
		}
	}
	return false;
}
// da check

// check xem lop da dat so luong dang ki toi da hay chua
// return true neu da dat, false khi chua dat
function checkFull($semester, $class_code) {
	$register = new RegisterTable;
	$class = new classTable;
	$total = $register->getTotal($semester, $class_code);
	$class_info = $class->get($semester, $class_code);
	if($total == $class_info['max_number']) return true;
	return false;
}
// da check

function checkRegister($semester, $class_code) {
	if(!checkClassTable($semester, $class_code)) return 1;
	if(checkRegisterTable($semester, $class_code)) return 2;
	if(checkCredit($semester, $class_code)) return 3;
	if(!checkRequire($semester, $class_code)) return 4;
	if(checkTimeConflict($semester, $class_code)) return 5;
	if(checkFull($semester, $class_code)) return 6;
	return 0;
}
?>