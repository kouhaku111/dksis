<?php 
include_once('config.php');

class facultyTable{
	function getAll() {
		$query = "select * from faculty";
		return pg_query(DB, $query);
	}

	function get($faculty_id) {
		$query = "select * from faculty where faculty_id = '{$faculty_id}'";
		$result = pg_query(DB, $query);
		return pg_fetch_assoc($result);
	}
}

class mnClassTable{
	function get($class_id) {
		$query = "select * from management_class where id = '{$class_id}'";
		$result = pg_query(DB, $query);
		return pg_fetch_assoc($result);
	}

	function getAll() {
		$query = "select * from management_class";
		return pg_query(DB, $query);
	}

	function getByFaculty($faculty_id) {
		$query = "select * from management_class where faculty_id = '{$faculty_id}'";
		return pg_query(DB, $query);
	}
}

class clientTable{
	function check($client_id, $password) {
		$query = "SELECT count(*) FROM client WHERE client_id='{$client_id}' AND password='{$password}'";
		$result = pg_query(DB, $query);
		$count = pg_fetch_assoc($result)['count'];
		if($count == 1) return true;
		else return false;
	}

	function get($client_id) {
		$query = "SELECT * FROM client WHERE client_id = '{$client_id}'";
		//echo 'client.get: '.$query;
		$result = pg_query(DB, $query);
		return pg_fetch_assoc($result);
	}

	function delete($client_id) {
		$query = "DELETE FROM client WHERE client_id = '{$client_id}'";
		return pg_query(DB, $query);
	}

	function getAll($class_id) {
		$query = "select * from client where management_class = '{$class_id}'";
		return pg_query(DB, $query);
	}
}

class classTable{
	function check($semester, $class_code) {
		$query = "SELECT count(*) FROM class WHERE semester='{$semester}' and class_code='{$class_code}'";
		//echo 'class.check: '. $query;
		$result = pg_query(DB, $query);
		$count = pg_fetch_assoc($result)['count'];
		if($count == '1') return true;
		else return false;
	}
	
	function get($semester, $class_code) {
		$query = "SELECT * FROM class WHERE semester='{$semester}' and class_code='{$class_code}'";
		// echo 'class.get: '.$query;
		$result = pg_query(DB, $query);
		return pg_fetch_assoc($result);
	}

	function getAll($semester, $class_code) {
		$query = "select r.student_id, cl.name, mid_term, final_term
				from class c, result r, client cl
				where cl.client_id = r.student_id
				and c.semester = '{$semester}'
				and c.class_code = '{$class_code}'
				and c.semester = r.semester
				and c.subject_id = r.subject_id";

		return pg_query(DB, $query);
	}

	// function delete($id) {
	// 	$query = "DELETE FROM class WHERE id = '{$id}'";
	// 	return pg_query(DB, $query);
	// }
	//}
}

class subjectTable{
	function check($subject_id) {
		$query = "SELECT count(*) FROM subject WHERE subject_id='{$subject_id}'";
		$result = pg_query(DB, $query);
		$count = pg_fetch_assoc($result)['count'];
		if($count == 1) return true;
		else return false;
	}

	function get($subject_id) {
		$query = "SELECT * FROM subject WHERE subject_id = '{$subject_id}'";
		// echo 'subject.get: '.$query;
		$result = pg_query(DB, $query);
		return pg_fetch_assoc($result);
	}

	function delete($subject_id) {
		$query = "DELETE FROM subject WHERE subject_id = '{$subject_id}'";
		return pg_query(DB, $query);
	}
}

class registerTable{
	function add($semester, $client, $class) {
		$query = "INSERT INTO register(semester, class_code, student_id) VALUES($semester, $class, $client)";
		// echo 'register.add: '.$query;
		pg_query(DB, $query);
	}

	function delete($semester, $client, $class) {
		$query = "DELETE FROM register 
		WHERE student_id='{$client}' and class_code='{$class}' and semester='{$semester}'";
		pg_query(DB, $query);
	}

	// lay danh sach lop da dang ki cua sinh vien hoac
	// danh sach sinh vien da dang ki 1 lop
	function getAll($semester, $name) {
		$query = "SELECT * FROM register WHERE 
		(student_id='{$name}' OR class_code='{$name}') AND (semester='{$semester}')";
		//echo 'register.getall: '.$query;
		return pg_query(DB, $query);
	}

	// lay tong so lop da dang ki trong 1 ki cua sinh vien
	function getTotal($semester, $class) {
		$query = "SELECT count(*) as total FROM register 
		WHERE class_code='{$class}' and semester='{$semester}'";
		//echo 'register.gettotal: '.$query;
		$result = pg_query(DB, $query);
		return pg_fetch_assoc($result)['total'];
	}


}

class teacherTable{
	function get($teacher_id) {
		$query = "select * from teacher where upper(teacher_id)='{$teacher_id}'";
		$result = pg_query(DB, $query);
		return pg_fetch_assoc($result);
	}

	function getByFaculty($faculty_id) {
		$query = "select * from teacher where faculty_id = '{$faculty_id}'";
		return pg_query(DB, $query);
	}
}

class teachTable{
	function getAll($id) {
		$query = "select * from teach where teacher_id='{$id}' or subject_id='{id}'";
		return pg_query(DB, $query);
	}

	function getRate($teacher_id, $subject_id) {
		$query = "select rate from teach 
				where teacher_id='{$teacher_id}'
				and subject_id='{$subject_id}'";
		// echo 'teach.getRate: '.$query;
		$result = pg_query(DB, $query);
		return pg_fetch_assoc($result)['rate'];
	}
}

class scheduleTable{
	function check($client_id) {
		$query = "select * from schedule";
		$result = pg_query(DB, $query);
		while($row = pg_fetch_assoc($result)) {
			if(date("Y-m-d") === $row['day']) {
				if(strtotime($row['time_start']) <= time() && time() <= strtotime($row['time_end'])) {
					if($row['student_start'] <= $client_id && $client_id <= $row['student_end']) {
						return true;
					}
				}
			}
		}
	return false;
	}

	function getAll() {
		$query = "select * from schedule";
		return pg_query(DB, $query);
	}
}

class requireTable{
	function getAll($subject_id) {
		$query = "select * from require where subject = '{$subject_id}'";
		//echo 'require.getAll: '. $query;
		return pg_query(DB, $query);
	}
}

class resultTable{
	//check xem sinh vien da qua mon hay chua
	function check($student_id, $subject_id) {
		$query = "select count(*) from result where student_id = '{$student_id}'
				and subject_id = '{$subject_id}' and pass = true";
		//echo 'result.check: '. $query;
		$result = pg_query(DB, $query);
		$row = pg_fetch_assoc($result)['count'];
		if($row != 0) return true;
		else return false;
	}
}

class semesterResultTable{
	private $student_info;
	private $mnclass_info;
	private $faculty_info;

	function __construct($student_id) {
		$student = new clientTable;
		$mnclass = new mnClassTable;
		$faculty = new facultyTable;
		$this->student_info = $student->get($student_id);
		$this->mnclass_info = $mnclass->get($this->student_info['management_class']);
		$this->faculty_info = $faculty->get($this->mnclass_info['faculty_id']);
	}

	function getAll() {
		$query = "select * from semester_result
				where student_id = '{$this->student_info['client_id']}'";
		return pg_query(DB, $query);
	}

	function getRank() {
		$query = "with tmp as (
				select semester, student_id, total_credit, gpa, rank() over (order by final_result desc)
				from semester_result s, client c, management_class m, faculty f
				where f.faculty_id = '{$this->faculty_info['faculty_id']}'
				and academic_year = '{$this->student_info['academic_year']}' 
				and s.student_id = c.client_id
				and c.management_class = m.id
				and m.faculty_id = f.faculty_id
				)
				select semester, total_credit, gpa, rank from tmp
				where student_id = '{$this->student_info['client_id']}'
				order by semester asc";
				//echo $query;
		return pg_query(DB, $query);
	}
}

class rankTable{
	private $student_info;
	private $mnclass_info;
	private $faculty_info;

	function __construct($student_id) {
		$student = new clientTable;
		$mnclass = new mnClassTable;
		$faculty = new facultyTable;
		$this->student_info = $student->get($student_id);
		$this->mnclass_info = $mnclass->get($this->student_info['management_class']);
		$this->faculty_info = $faculty->get($this->mnclass_info['faculty_id']);
	}

	function getByClass() {
		//var_dump($this->student_info);
		$query = "with tmp as
				(
				select r.student_id, rank() over (order by cpa desc)
				from rank r, client c, management_class m
				where m.id = '{$this->student_info['management_class']}'
				and academic_year = '{$this->student_info['academic_year']}'
				and r.student_id = c.client_id
				and c.management_class = m.id
				)
				select rank from tmp
				where student_id = '{$this->student_info['client_id']}'";
		//echo 'getbyclass: '.$query;
		$result = pg_query(DB, $query);
		return pg_fetch_assoc($result)['rank'];
	}

	function getByFaculty() {
		$query = "with tmp as
				(
				select r.student_id, rank() over (order by cpa desc)
				from rank r, client c, management_class m, faculty f
				where f.faculty_id = m.faculty_id 
				and academic_year = '{$this->student_info['academic_year']}'
				and f.faculty_id = '{$this->faculty_info['faculty_id']}'
				and r.student_id = c.client_id
				and c.management_class = m.id
				)
				select rank from tmp
				where student_id = '{$this->student_info['client_id']}'";
		$result = pg_query(DB, $query);
		return pg_fetch_assoc($result)['rank'];
	}

	function getByAll() {
		$query = "with tmp as
				(
				select r.student_id, rank() over (order by cpa desc)
				from rank r, client c
				where academic_year = '{$this->student_info['academic_year']}' 
				and r.student_id = c.client_id
				)
				select rank from tmp
				where student_id = '{$this->student_info['client_id']}'";
		$result = pg_query(DB, $query);
		return pg_fetch_assoc($result)['rank'];
	}
}

function getSumCredit($semester, $student_id) {
	$query = "select SUM(s.credit) as total from register r, class c, subject s
			  WHERE r.student_id = '{$student_id}'
			  and r.semester = '{$semester}'
			  and r.class_code = c.class_code
			  and c.subject_id = s.subject_id";
	//echo 'getSumCredit: '. $query;
	$result = pg_query(DB, $query);
	return pg_fetch_assoc($result)['total'];
}

function studentRate($student_id, $faculty_id) {
	$query = "with total as 
			(select * from result inner join subject using(subject_id) 
			where student_id = '{$student_id}'
			and faculty_id = '{$faculty_id}')
			select count(pass) * 100 / (select count(*) + 1 from total) as rate
			from result inner join subject using(subject_id) 
			where student_id = '{$student_id}'
			and faculty_id = '{$faculty_id}' and pass=true";
	//echo $query;
	$result = pg_query(DB, $query);
	return pg_fetch_assoc($result)['rate'];
}

function getOwnCredit($student_id) {
	$query = "with tmp as 
	(
	select distinct(subject_id), credit, pass 
	from result natural inner join subject t
	where student_id = '{$student_id}'
	and gpa = (select max(gpa) from result 
			where student_id = '{$student_id}' 
			and subject_id = t.subject_id)
	)
	select sum(credit) as total from tmp
	where pass = false";

	//echo $query;
	$result = pg_query(DB, $query);
	return pg_fetch_assoc($result)['total'];
}

?>