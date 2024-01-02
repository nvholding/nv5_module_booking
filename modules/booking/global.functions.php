<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2013 Webdep24.com. All rights reserved
 * @Blog  http://dangdinhtu.com
 * @License GNU/GPL version 2 or any later version
 * @Createdate  Wed, 21 Jan 2015 14:00:59 GMT
 */

if( ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );

$arrayGender = array('M'=> 'Nam', 'F'=> 'Nữ');


// thời gian khám bệnh giờ phút
$time_work1 = array(
	'gio' => 8,
	'phut' => 0 
);

$time_work2 = array(
	'gio' => 8,
	'phut' => 40 
);

$time_work3 = array(
	'gio' => 9,
	'phut' => 20 
);

$time_work4 = array(
	'gio' => 10,
	'phut' => 0 
);

$time_work5 = array(
	'gio' => 10,
	'phut' => 40 
);

$time_work6 = array(
	'gio' => 11,
	'phut' => 20 
);

$time_work7 = array(
	'gio' => 13,
	'phut' => 30 
);

$time_work8 = array(
	'gio' => 14,
	'phut' => 10 
);


$time_work9 = array(
	'gio' => 14,
	'phut' => 50 
);

$time_work10 = array(
	'gio' => 15,
	'phut' => 30 
);

$time_work11 = array(
	'gio' => 16,
	'phut' => 10 
);

$time_work12 = array(
	'gio' => 16,
	'phut' => 50 
);

$time_work13 = array(
	'gio' => 17,
	'phut' => 30 
);

$time_work14 = array(
	'gio' => 18,
	'phut' => 10 
);

$time_work15 = array(
	'gio' => 18,
	'phut' => 50 
);

$time_work16 = array(
	'gio' => 19,
	'phut' => 30 
);


$array_time_works = array(
	'1' => $time_work1,
	'2' => $time_work2,
	'3' => $time_work3,
	'4' => $time_work4,
	'5' => $time_work5,
	'6' => $time_work6,
	'7' => $time_work7,
	'8' => $time_work8,
	'9' => $time_work9,
	'10' => $time_work10,
	'11' => $time_work11,
	'12' => $time_work12,
	'13' => $time_work13,
	'14' => $time_work14,
	'15' => $time_work15,
	'16' => $time_work16
);


// thời gian buổi trong ngày

$buoi_sang = array(
	'from' => 8,
	'to' => 12
);

$buoi_chieu = array(
	'from' => 12,
	'to' => 17
);

$buoi_toi = array(
	'from' => 17,
	'to' => 21
);


$array_buoi = array(
	'1' => $buoi_sang,
	'2' => $buoi_chieu,
	'3' => $buoi_toi,
);



function nv_get_week_from_time($time)
{
	$week = 1;
	$year = date('Y', $time);
	$real_week = array($week, $year);
	$time_per_week = 86400 * 7;
	$time_start_year = mktime(0, 0, 0, 1, 1, $year);
	$time_first_week = $time_start_year - (86400 * (date('N', $time_start_year) - 1));
	
	$addYear = true;
	$num_week_loop = nv_get_max_week_of_year($year) - 1;
	
	for ($i = 0; $i <= $num_week_loop; $i++) {
		$week_begin = $time_first_week + $i * $time_per_week;
		$week_next = $week_begin + $time_per_week;
		
		if ($week_begin <= $time and $week_next > $time) {
			$real_week[0] = $i + 1;
			$addYear = false;
			break;
		}
	}
	if ($addYear) {
		$real_week[1] = $real_week[1] + 1;
	}
	
	return $real_week;
}


/**
 * nv_get_max_week_of_year()
 * 
 * @param mixed $year
 * @return
 */
function nv_get_max_week_of_year($year)
{
	$time_per_week = 86400 * 7;
	$time_start_year = mktime(0, 0, 0, 1, 1, $year);
	$time_first_week = $time_start_year - (86400 * (date('N', $time_start_year) - 1));
	
	if (date('Y', $time_first_week + ($time_per_week * 53) - 1) == $year) {
		return 53;
	} else {
		return 52;
	}
}


// khám bệnh cho kh
function khambenh($appointment_id, $patient_id)
{
	global $db, $db_config, $module_data;
	
		
		// lấy số lần khám của bệnh nhân này ra patient_id.
		$kham_conlai = $db->query('SELECT kham_conlai FROM ' . TABLE_APPOINTMENT_NAME . '_patient WHERE userid='. $patient_id)->fetchColumn();
		
	
		
		if(!$kham_conlai)
		{
			return false;
		}
		else
		{
			// trừ số lần khám đi
			$so_lan_update = -1;
			update_num_kham($patient_id, $so_lan_update);
			
			// cập nhật trạng thái lịch hẹn đã khám bệnh cho KH
			update_khambenh_kh($patient_id, $appointment_id);
			
		}
		
		
		return true;	
		
}

// cập nhật số lần khám bệnh cho Kh
function update_num_kham($userid, $so_lan_update)
{
	global $db, $db_config, $module_data;
	
	if(!$userid or !$so_lan_update)
		return;
	
	// cập nhật
	$db->query('UPDATE ' . TABLE_APPOINTMENT_NAME . '_patient
 SET kham_conlai = kham_conlai +' . $so_lan_update . ' WHERE userid =' . $userid);
 
	// thấp nhất = 0
	$db->query('UPDATE ' . TABLE_APPOINTMENT_NAME . '_patient
 SET kham_conlai = 0 WHERE userid =' . $userid .' AND kham_conlai < 0');
 
}

// cập nhật đã khám bệnh cho khách hàng đặt lịch
function update_khambenh_kh($userid, $appointment_id)
{
	global $db, $db_config, $module_data;
	
	if(!$userid or !$appointment_id)
		return false ;
	
	$db->query('UPDATE ' . TABLE_APPOINTMENT_NAME . '_appointment
 SET bs_dakham = 1 WHERE userid =' . $userid . ' AND appointment_id ='. $appointment_id);
 
 return true ;
	
}

// lấy số lần khám cũ trước đó
function get_num_byservice($id)
{
	global $db, $db_config, $module_data;
	
	if(!$id)
		return;
	
	return $db->query('SELECT num FROM ' . NV_PREFIXLANG . '_' . $module_data . '_buy_service WHERE id =' . $id)->fetchColumn();
	
}

// lấy thông tin khách hàng có lần khám còn lại
// lấy số lần khám cũ trước đó
function get_parent_info($userid)
{
	global $db, $db_config, $module_data;
	
	if(!$userid)
		return;
	
	
	return $db->query('SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_patient WHERE userid =' . $userid)->fetch();
	
}


// lấy userid 
function get_userid_byservice($id)
{
	global $db, $db_config, $module_data;
	
	if(!$id)
		return;
	
	return $db->query('SELECT userid FROM ' . NV_PREFIXLANG . '_' . $module_data . '_buy_service WHERE id =' . $id)->fetchColumn();
	
}

// convert 0 -> 84
function convert_phone($phone)
{

	$thaythe = substr($phone,1,9);
	$thaythe = '84'. $thaythe;
	
	return $thaythe;

}


// gửi sms cho chúc mừng sinh nhật
function sent_sms_happybirthday($info)
{
	global $db, $db_config, $module_data;
	
	if(empty($info['phone']) or empty($info['full_name']))
		return;
	
	if(empty($info['confess']))
		$info['confess'] = 'quý khách';
	
	$text = 'Co Xuong Khop THIỆN NHÂN chúc '. $info['confess'] .' '. $info['full_name'].' sinh nhật vui, nhiều phước lành, THÂN KHỎE-TÂM AN. Cảm ơn '. $info['confess'] .' tin chọn Thiện Nhân. www.suckhoethiennhan.com. Tran trong';
	
	$text = urlencode($text);
	
	$to = convert_phone($info['phone']);
	
	return sms_pushing($to, $text);
	
	
}

// gửi sms cho chúc mừng sinh nhật
function sent_sms_happynewyear($info)
{
	global $db, $db_config, $module_data;
	
	if(empty($info['phone']) or empty($info['full_name']))
		return;
	
	if(empty($info['confess']))
		$info['confess'] = 'quý khách';
	
	
	$text = 'Cơ Xương Khớp THIỆN NHÂN chúc '. $info['confess'] .' '. $info['full_name'].' năm mới 2021 hạnh phúc, nhiều phước lành, THÂN KHOẺ-TÂM AN. Cảm ơn '. $info['confess'] .' tin chọn www.suckhoethiennhan.com .Tran trong';
	
	$text = urlencode($text);
	
	$to = convert_phone($info['phone']);
	
	return sms_pushing($to, $text);
	
	
}

function history_sms($appointment_id)
{
	global $db, $db_config, $module_data;
	
	if(!$appointment_id)
		return;
	
	// lưu lại lịch sử gửi sms thành công
	
	$row = $db->query('SELECT * FROM ' . TABLE_APPOINTMENT_NAME . '_appointment WHERE appointment_id ='. $appointment_id)->fetch();
	
	$row['date_added'] = NV_CURRENTTIME;
	
	
	$stmt = $db->prepare('INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_history_send_sms (appointment_id, name_send, phone_send, status_sms, date_order, date_added) VALUES (:appointment_id, :name_send, :phone_send, :status_sms, :date_order, :date_added)');
	
	$stmt->bindParam(':appointment_id', $row['appointment_id'], PDO::PARAM_INT);
    $stmt->bindParam(':name_send', $row['customer_full_name'], PDO::PARAM_STR);
    $stmt->bindParam(':phone_send', $row['customer_phone'], PDO::PARAM_STR);
	$stmt->bindParam(':status_sms', $row['is_send_sms'], PDO::PARAM_INT);
    $stmt->bindParam(':date_order', $row['customer_date_booking'], PDO::PARAM_INT);
    $stmt->bindParam(':date_added', $row['date_added'], PDO::PARAM_INT);

    $exc = $stmt->execute();
	
}

function get_thu_vietnam($weekday)
{

	$weekday = strtolower($weekday);
	switch($weekday) {
		case 'monday':
			$weekday = 'thu hai';
			break;
		case 'tuesday':
			$weekday = 'thu ba';
			break;
		case 'wednesday':
			$weekday = 'thu tu';
			break;
		case 'thursday':
			$weekday = 'thu nam';
			break;
		case 'friday':
			$weekday = 'thu sau';
			break;
		case 'saturday':
			$weekday = 'thu bay';
			break;
		default:
			$weekday = 'chu nhat';
			break;
	}
	
	return $weekday;

}

// gửi sms cho hẹn lịch khám
function sent_sms_nhac_kham($appointment_id)
{
	global $db, $db_config, $module_data;
	
	if(!$appointment_id)
		return;
	
	$info = $db->query('SELECT * FROM ' . TABLE_APPOINTMENT_NAME . '_appointment WHERE appointment_id ='. $appointment_id)->fetch();
	
	$info['gio'] = date('H', $info['customer_date_booking'] );
	$info['phut'] = date('i', $info['customer_date_booking'] );
	$info['thu'] = date('l', $info['customer_date_booking'] );
	$info['thu'] = get_thu_vietnam($info['thu']);
	$info['ngay'] = date('d.m.Y', $info['customer_date_booking'] );
	
	
	// lấy thông tin xưng hô
	if($info['userid'])
	{
		$xungho = $db->query('SELECT confess FROM ' . NV_PREFIXLANG . '_' . $module_data . '_patient WHERE userid =' . $info['userid'])->fetchColumn();
	}
	
	if(empty($xungho))
		$xungho = 'Quý khách';
	
	$arr_name = explode(' ', $info['customer_full_name']);
	$info['customer_full_name'] = end($arr_name);
	
	$text = 'Co Xuong Khop THIEN NHAN xin nhac lich hen '. $xungho .' '. $info['customer_full_name'].' den tri lieu luc '. $info['gio'] .':'. $info['phut'] .' '. $info['thu'] .', ngay '. $info['ngay'] .'. LH 0336044055 , www.suckhoethiennhan.com . Xin cam on'; 
	
	
	$text = urlencode($text);
	
	$to = convert_phone($info['customer_phone']);
	
	return sms_pushing($to, $text);
	
	
}


function sms_pushing($to, $text)
{        

		$sms_push_querystring = 'http://www.etracker.cc/bulksms/mesapi.aspx?user=THIEN_NHAN&pass=WW8nSqm%2b&type=0&to='. $to .'&from=Thien+Nhan&text='. $text .'&servid=MES01';
		
		
		$curl = curl_init();
		
        curl_setopt_array($curl, array(
			  CURLOPT_URL => $sms_push_querystring,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "GET"
			));

			$response = curl_exec($curl);

			$data = json_decode($response,true);
		
        return $data;
}


function check_work($doctor, $time_work, $booking_date, $appointment_id = 0)
{
	global $db, $db_config, $module_data, $array_buoi;
	
	// kiểm tra hôm nay bác sĩ có xin nghỉ giờ nào không
	$date_from_to = date_from_to($booking_date);
	
	//print_r(date('d/m/Y - H:i',1616000399));die;

	$list_nghilam = $db->query('SELECT shift FROM ' . TABLE_APPOINTMENT_NAME . '_calendar WHERE userid ='. $doctor .' AND date_start >=' . $date_from_to['from'] . ' AND date_start <= '. $date_from_to['to'] )->fetchAll();
	
	// kiểm tra thời gian nghỉ có nằm trong khung giờ đặt lịch khám không
	$flag = $doctor;
	
	foreach($list_nghilam as $nghilam)
	{
		//print_r($time_work);die;
		if($array_buoi[$nghilam['shift']])
		{
			if( ($array_buoi[$nghilam['shift']]['from'] <= $time_work['gio']) AND ($array_buoi[$nghilam['shift']]['to'] > $time_work['gio']) )
			{
				// trúng giờ nghỉ làm việc của bác sĩ rồi
				$flag = 0;
			}
		}
	}
	
	
	// kiểm tra có lịch hẹn nào ngày này giờ này chưa
	$num_ngay_gio = 0;
	
	$publ_date = $booking_date;
    if (preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $publ_date, $m)) {
        $phour = $time_work['gio'];
        $pmin = $time_work['phut'];
        $num_ngay_gio = mktime($phour, $pmin, 0, $m[2], $m[1], $m[3]);
    }
	
	$where = '';
	
	if($appointment_id)
	{
		$where = ' AND appointment_id !=' . $appointment_id;
	}
	$check_appointment = $db->query('SELECT appointment_id FROM ' . TABLE_APPOINTMENT_NAME . '_appointment WHERE doctors_id ='. $doctor .' AND customer_date_booking =' . $num_ngay_gio . $where)->fetchColumn();
	
	
	if($check_appointment)
	{
		$flag = 0;
	}
	
	return $flag;
}

// function ngày từ ngày đến
function date_from_to($time)
{
	$array = array();
	
	if( preg_match( '/^([0-9]{1,2})[\/|-]([0-9]{1,2})[\/|-]([0-9]{4})$/', $time, $m ) )
	{
		$array['from'] = mktime( 0, 0, 0, $m[2], $m[1], $m[3] );
	}
	
	if( preg_match( '/^([0-9]{1,2})[\/|-]([0-9]{1,2})[\/|-]([0-9]{4})$/', $time, $m ) )
	{
		$array['to'] = mktime( 23, 59, 59, $m[2], $m[1], $m[3] );
	}
	
	return $array;
}


function list_doctor_branch($branch_id)
{
	global $db, $module_name, $module_data,$db_config;
	if(!$branch_id)
		return array();
	
	$list = $db->query('SELECT u.userid FROM ' . $db_config['prefix'] . '_users u RIGHT JOIN ' . $db_config['prefix'] . '_users_groups_users gu ON (u.userid = gu.userid) LEFT JOIN ' . NV_PREFIXLANG . '_booking_branch_users bu ON (u.userid = bu.userid) WHERE u.active = 1 AND gu.group_id=10 AND branch_id =' . $branch_id)->fetchAll();
	$array_doctor = array();
	
	foreach($list as $u)
	{
		$array_doctor[] = $u['userid'];
	}
	
	return $array_doctor;
	
}


function list_doctor_branch_new($branch_id, $time)
{
	global $db, $module_name, $module_data,$db_config;
	if(!$branch_id or !$time)
		return array();
	
	if( preg_match( '/^([0-9]{1,2})[\/|-]([0-9]{1,2})[\/|-]([0-9]{4})$/', $time, $m ) )
	{
		$time = mktime( 0, 0, 0, $m[2], $m[1], $m[3] );
	}
	
	// lấy danh sách bác sĩ thuộc chi nhánh $branch_id trong ngày $time
	
	// lấy tất cả bác sĩ trong bệnh viện ra
	$list_doctor = $db->query('SELECT u.userid, bu.time_from, bu.time_to  FROM ' . $db_config['prefix'] . '_users u RIGHT JOIN ' . $db_config['prefix'] . '_users_groups_users gu ON (u.userid = gu.userid) LEFT JOIN ' . NV_PREFIXLANG . '_booking_branch_users bu ON (u.userid = bu.userid) WHERE u.active = 1 AND gu.group_id=10 ORDER BY u.userid DESC')->fetchAll();
	
	$arr = array();
	
	// kiểm tra từng bác sĩ ngày @time có thuộc chi nhánh $branch_id không
	foreach($list_doctor as $doctor)
	{
		
		// kiểm tra bác sĩ ngày @time có còn hợp đồng làm việc không
		if(($time >= $doctor['time_from']  and $time <= $doctor['time_to']) or ($doctor['time_from'] == 0 and $doctor['time_to'] == 0) or ($time >= $doctor['time_from']  and $doctor['time_to'] == 0))
		{
			// tiếp tục kiểm tra @time này bác sĩ này đang công tác ở chi nhánh nào
			$get_info_chinhanh_time = $db->query('SELECT id_branch FROM ' . NV_PREFIXLANG . '_booking_history_branch_doctor WHERE date_change <=' . $time .'  AND userid_doctor ='. $doctor['userid'])->fetchColumn();
			
			// tại thời điểm này bác sĩ đang công tác ở chi nhánh khác rồi
			
			if($get_info_chinhanh_time > 0)
			{
				if($get_info_chinhanh_time != $branch_id)
				{
					//continue;
				}
				else
				{
					$arr[] = $doctor['userid'];
				}
			}
			else
			{
			
				// tiếp tục kiểm tra chi nhánh hiện tại bác sĩ đang làm có cùng chi nhánh yêu cầu không
				$get_info_chinhanh_current = $db->query('SELECT userid FROM ' . NV_PREFIXLANG . '_booking_branch_users WHERE branch_id =' . $branch_id .'  AND userid ='. $doctor['userid'])->fetchColumn();
				
			
				
				if($get_info_chinhanh_current)
				{
					$arr[] = $doctor['userid'];
				}
			
			}
		}
		
	}
	
	//print_r($arr);die;
	return $arr;
}


// cập nhật chi nhánh luân chuyển bác sĩ
function update_brand_doctor($id_luanchuyen)
{
	global $db, $module_name, $module_data;
	if(!$id_luanchuyen)
		return 0;
	
	// lấy thông tin 
	$info = $db->query('SELECT id, id_branch, userid_doctor FROM ' . TABLE_APPOINTMENT_NAME . '_history_branch_doctor WHERE id ='. $id_luanchuyen)->fetch();
	
	// cập nhật chi nhánh bác sĩ đó
	$db->query('UPDATE ' . TABLE_APPOINTMENT_NAME . '_history_branch_doctor SET active = 1 WHERE id ='. $id_luanchuyen);
	
	$db->query('UPDATE ' . TABLE_APPOINTMENT_NAME . '_branch_users SET branch_id ='. $info['id_branch'] .' WHERE userid ='. $info['userid_doctor']);
	
	return true;
	
}


// lấy chi nhánh bác sĩ làm việc trong quá khứ
function get_branch_id_new($userid, $date)
{
	global $db, $module_name, $module_data;
	if(!$userid)
		return 0;
	
	// lấy chi nhánh trị liệu của bác sĩ trong lịch sử nếu có
	$branch_id = $db->query('SELECT id_branch FROM ' . TABLE_APPOINTMENT_NAME . '_history_branch_doctor WHERE userid_doctor ='. $userid .' AND date_change <= '. $date .' AND active = 1 ORDER BY date_change DESC LIMIT 0,1')->fetchColumn();
	
	if(!$branch_id)
		return get_branch_id($userid);
	else
		return $branch_id;
	
}


// lấy chi nhánh bác sĩ làm việc
function get_branch_id($userid)
{
	global $db, $module_name, $module_data;
	if(!$userid)
		return 0;
	
	
	return $db->query('SELECT branch_id FROM ' . TABLE_APPOINTMENT_NAME . '_branch_users WHERE userid ='. $userid)->fetchColumn();
	
}

// lấy danh sách bác sĩ trong chi nhánh ra
function get_doctor_branch($branch_id)
{
	global $db, $module_name, $module_data;
	if(!$branch_id)
		return 0;
	
	
	$list_user = $db->query('SELECT userid FROM ' . TABLE_APPOINTMENT_NAME . '_branch_users WHERE branch_id ='. $branch_id)->fetchAll();
	
	$arr = array();
	foreach($list_user as $user)
	{
		if($user['userid'])
			$arr[] = $user['userid'];
	}
	
	return $arr;
	
}

function list_time_order_doctor($list_all, $list_doctor_branch)
{
	global $module_name, $module_file, $global_config, $op, $module_info;

	$xtpl = new XTemplate( 'html_order_doctor.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	
	$array_get_doctor = $list_doctor_branch;
	
	
	
	foreach($list_all as $time)
	{
		
	
			// random bs
		
		if(empty($array_get_doctor))
		{
			$array_get_doctor = $list_doctor_branch;
		}
		
		
		
		if(count($time['bs']) == 1)
		{
			$random_bs = $time['bs'][0];
		}
		else
		{
			$arr = random_bs($array_get_doctor, $time['bs']);
			$array_get_doctor = $arr['list_doctor'];
			$random_bs = $arr['bs'];
		}
	
		
		$xtpl->assign( 'random_bs', $random_bs);
		
		$time['time']['gio'] = str_pad($time['time']['gio'], 2, "0", STR_PAD_LEFT);
		$time['time']['phut'] = str_pad($time['time']['phut'], 2, "0", STR_PAD_LEFT);
		$xtpl->assign( 'time', $time['time']);
		
		
		$xtpl->parse( 'main.time' );
	}
	
	
	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );
	
}

// random bs
function random_bs($array_get_doctor, $array_bs)
{
	$arr = array();
	
	$arr['bs'] = $array_bs[0];
	
	foreach($array_get_doctor as $key => $value)
	{
		if(in_array($value,$array_bs))
		{
			$arr['bs'] = $value;
			array_splice($array_get_doctor, $key, 1);
			$arr['list_doctor'] = $array_get_doctor;
			break;
		}
	}
	
	return $arr ;
}

function convertToTimeStamp( $time, $default=0, $phour=0, $pmin=0, $second=0 )
{
	if( preg_match( '/^([0-9]{1,2})[\/|-]([0-9]{1,2})[\/|-]([0-9]{4})$/', $time, $m ) )
	{
		
		$time = mktime( $phour, $pmin, $second, $m[2], $m[1], $m[3] );
	}
	else
	{
		if( $default )
		{
			$time = NV_CURRENTTIME;
		}
		else
		{
			$time = 0;
		}
	}
	
	return $time;
	
}

// $arrayshift = array('0'=> 'Chọn ca', 1=>'Sáng', '2'=> 'Chiều', '3'=> 'Tối' );
$arrayshift = array(1=>'Sáng', '2'=> 'Chiều', '3'=> 'Tối' );

$getUserid = isset( $user_info['userid'] ) ? intval( $user_info['userid'] ) : 0;

$config_email = $nv_Cache->db( 'SELECT config_name, config_value FROM ' . NV_CONFIG_GLOBALTABLE . '', 'config_name', 'site');

$dataContent = $nv_Cache->db( 'SELECT config_name, config_value FROM ' . TABLE_APPOINTMENT_NAME . '_setting', 'config_name', $module_name);
$array_config = $nv_Cache->db( 'SELECT config_name, config_value FROM ' . TABLE_APPOINTMENT_NAME . '_setting', 'config_name', $module_name);


$getSetting = array();

foreach( $dataContent as $row )
{
	$getSetting[$row['config_name']] = $row['config_value'];
}

unset($dataContent);

function getService()
{
	global $nv_Cache, $module_name;

	$sql = 'SELECT service_id, service_name FROM ' . TABLE_APPOINTMENT_NAME . '_service WHERE status=1 ORDER BY weight ASC';
	
	return $nv_Cache->db($sql, 'service_id', $module_name);
}

function get_list_doctor_select2($q){
	global $db, $db_config,$module_data,$db_config;

	$list_doctor = $db->query('SELECT t1.* FROM ' . $db_config['prefix'] . '_users t1 RIGHT JOIN ' . $db_config['prefix'] . '_users_groups_users t2 ON t1.userid = t2.userid LEFT JOIN ' . TABLE_APPOINTMENT_NAME . '_branch_users t3 ON t1.userid = t3.userid WHERE t1.last_name LIKE "%'.$q.'%" AND t2.group_id=10')->fetchAll();
	return $list_doctor;
}

function get_info_doctor($id){
	global $db, $db_config,$module_data,$db_config;
	
	if(!$id)
		return array();
	
	$info_doctor = $db->query('SELECT * FROM ' . $db_config['prefix'] . '_users t1 RIGHT JOIN ' . $db_config['prefix'] . '_users_groups_users t2 ON t1.userid = t2.userid LEFT JOIN ' . TABLE_APPOINTMENT_NAME . '_branch_users t3 ON t1.userid = t3.userid WHERE t1.userid = ' . $id . ' AND t2.group_id=10')->fetch();
	return $info_doctor;
}


function getBranch()
{
	global $nv_Cache, $module_name;

	$sql = 'SELECT branch_id, title  FROM ' . TABLE_APPOINTMENT_NAME . '_branch WHERE status=1 ORDER BY weight ASC';
	
	return $nv_Cache->db($sql, 'branch_id', $module_name);
}

 
function service_package()
{
	global $nv_Cache, $module_name;

	$sql = 'SELECT service_package_id, title  FROM ' . TABLE_APPOINTMENT_NAME . '_service_package WHERE status=1 ORDER BY weight ASC';
	
	return $nv_Cache->db($sql, 'service_package_id', $module_name);
}

function getBranch_id($branch_id)
{
	global $nv_Cache, $module_name, $db;

	$info = $db->query('SELECT  *  FROM ' . TABLE_APPOINTMENT_NAME . '_branch WHERE branch_id = ' . $branch_id)->fetch();
	
	return $info;
}


function get_name_patient_group($id)
{
	global $nv_Cache, $module_name, $db;

	$info = $db->query('SELECT  *  FROM ' . TABLE_APPOINTMENT_NAME . '_group_patient WHERE id = ' . $id)->fetch();
	
	return $info;
}
function get_name_ServicePackage($id)
{
	global $nv_Cache, $module_name, $db;

	$info = $db->query('SELECT  *  FROM ' . TABLE_APPOINTMENT_NAME . '_service_package WHERE service_package_id = ' . $id)->fetch();
	
	return $info;
}

function getServicePackage()
{
	global $nv_Cache, $module_name;

	$sql = 'SELECT service_package_id, title  FROM ' . TABLE_APPOINTMENT_NAME . '_service_package WHERE status=1 ORDER BY weight ASC';
	
	return $nv_Cache->db($sql, 'service_package_id', $module_name);
}
function get_group_patient()
{
	global $nv_Cache, $module_name;

	$sql = 'SELECT id, title  FROM ' . TABLE_APPOINTMENT_NAME . '_group_patient WHERE status=1 ORDER BY weight ASC';
	
	return $nv_Cache->db($sql, 'id', $module_name);
}


function getDataPage( $base_url, $num_items, $per_page, $on_page, $add_prevnext_text = true, $onclick = false, $js_func_name = 'nv_urldecode_ajax', $containerid = 'generate_page', $full_theme = true )
{
	global $lang_global;
	
	
	// Round up total page
	$total_pages = ceil( $num_items / $per_page );

	if( $total_pages < 2 )
	{
		return '';
	}

	if( ! is_array( $base_url ) )
	{
		$amp = preg_match( '/\?/', $base_url ) ? '&amp;' : '?';
		$amp .= 'page=';
	}
	else
	{
		$amp = $base_url['amp'];
		$base_url = $base_url['link'];
	}
	
	
	$parts = parse_url( $base_url );
	parse_str($parts['query'], $parameters);
	
	$page_string = '';

	if( $total_pages > 10 )
	{
		$init_page_max = ( $total_pages > 3 ) ? 3 : $total_pages;

		for( $i = 1; $i <= $init_page_max; ++$i )
		{
			$href = ( $i > 1 ) ? $base_url . $amp . $i : $base_url;
			$href = ! $onclick ? "href=\"" . $href . "\"" : "href=\"javascript:void(0)\" data-param='". json_encode( $parameters ) ."' onclick=\"" . $js_func_name . "(this, '". $i ."','" . $containerid . "')\"";
			$page_string .= '<li' . ( $i == $on_page ? ' class="active"' : '' ) . '><a' . ( $i == $on_page ? ' href="#"' : ' ' . $href ) . '>' . $i . '</a></li>';
		}

		if( $total_pages > 3 )
		{
			if( $on_page > 1 and $on_page < $total_pages )
			{
				if( $on_page > 5 )
				{
					$page_string .= '<li class="disabled"><span>...</span></li>';
				}

				$init_page_min = ( $on_page > 4 ) ? $on_page : 5;
				$init_page_max = ( $on_page < $total_pages - 4 ) ? $on_page : $total_pages - 4;

				for( $i = $init_page_min - 1; $i < $init_page_max + 2; ++$i )
				{
					$href = ( $i > 1 ) ? $base_url . $amp . $i : $base_url;
					$href = ! $onclick ? "href=\"" . $href . "\"" : "href=\"javascript:void(0)\" data-param='". json_encode( $parameters ) ."' onclick=\"" . $js_func_name . "(this, '". $i ."','" . $containerid . "')\"";
					$page_string .= '<li' . ( $i == $on_page ? ' class="active"' : '' ) . '><a' . ( $i == $on_page ? ' href="#"' : ' ' . $href ) . '>' . $i . '</a></li>';
				}

				if( $on_page < $total_pages - 4 )
				{
					$page_string .= '<li class="disabled"><span>...</span></li>';
				}
			}
			else
			{
				$page_string .= '<li class="disabled"><span>...</span></li>';
			}

			for( $i = $total_pages - 2; $i < $total_pages + 1; ++$i )
			{
				$href = ( $i > 1 ) ? $base_url . $amp . $i : $base_url;
				$href = ! $onclick ? "href=\"" . $href . "\"" : "href=\"javascript:void(0)\" data-param='". json_encode( $parameters ) ."' onclick=\"" . $js_func_name . "(this, '". $i ."','" . $containerid . "')\"";
				$page_string .= '<li' . ( $i == $on_page ? ' class="active"' : '' ) . '><a' . ( $i == $on_page ? ' href="#"' : ' ' . $href ) . '>' . $i . '</a></li>';
			}
		}
	}
	else
	{
		for( $i = 1; $i < $total_pages + 1; ++$i )
		{
			$href = ( $i > 1 ) ? $base_url . $amp . $i : $base_url;
			$href = ! $onclick ? "href=\"" . $href . "\"" : "href=\"javascript:void(0)\" data-param='". json_encode( $parameters ) ."' onclick=\"" . $js_func_name . "(this, '". $i ."','" . $containerid . "')\"";
			$page_string .= '<li' . ( $i == $on_page ? ' class="active"' : '' ) . '><a' . ( $i == $on_page ? ' href="#"' : ' ' . $href ) . '>' . $i . '</a></li>';
		}
	}

	if( $add_prevnext_text )
	{
		if( $on_page > 1 )
		{
			$href = ( $on_page > 2 ) ? $base_url . $amp . ( $on_page - 1 ) : $base_url;
			$href = ! $onclick ? "href=\"" . $href . "\"" : "href=\"javascript:void(0)\" data-param='". json_encode( $parameters ) ."' onclick=\"" . $js_func_name . "(this, '". $i ."','" . $containerid . "')\"";
			$page_string = "<li><a " . $href . " title=\"" . $lang_global['pageprev'] . "\">&laquo;</a></li>" . $page_string;
		}
		else
		{
			$page_string = '<li class="disabled"><a href="#">&laquo;</a></li>' . $page_string;
		}

		if( $on_page < $total_pages )
		{
			$href = ( $on_page ) ? $base_url . $amp . ( $on_page + 1 ) : $base_url;
			$href = ! $onclick ? "href=\"" . $href . "\"" : "href=\"javascript:void(0)\" data-param='". json_encode( $parameters ) ."' onclick=\"" . $js_func_name . "(this, '". $i ."','" . $containerid . "')\"";
			$page_string .= '<li><a ' . $href . ' title="' . $lang_global['pagenext'] . '">&raquo;</a></li>';
		}
		else
		{
			$page_string .= '<li class="disabled"><a href="#">&raquo;</a></li>';
		}
	}

	if( $full_theme !== true )
	{
		return $page_string;
	}

	return '<ul class="pagination">' . $page_string . '</ul>';
}


function is_decimal( $val )
{
	return is_numeric( $val ) && floor( $val ) != $val;
}

function price_format( $price, $round = 0 )
{
	if( ! is_numeric( $price ) ) return $price;
	if(  $round ) //is_decimal( $price ) &&
	{
		// $_round = strlen( substr( strrchr( $price, "." ), 1 ) );
		// if( $_round > 2 ) 
		
		$_round = 2;

		return ( is_numeric( $price ) && $price > 0 ) ? number_format( $price, $_round, ",", "." ) : 0;
	}
	else
	{
		return ( is_numeric( $price ) && $price > 0 ) ? number_format( $price, 0, ",", "," ) : 0;
	}
}

function price_round( $price )
{
	if( ! is_numeric( $price ) ) return $price;
	return ( is_numeric( $price ) && $price > 0 ) ? round( $price, 0, PHP_ROUND_HALF_UP ) : 0;
}

