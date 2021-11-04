<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */


if( ! defined( 'NV_IS_MOD_APPOINTMENT' ) ) die( 'Stop!!!' );
$mod = $nv_Request->get_string('mod', 'post, get', 0);

// https://suckhoethiennhan.com/booking/ajax?happybirthday=1

$happybirthday = $nv_Request->get_string('happybirthday', 'get', 0);
if($happybirthday == 1)
{
	// xử lý cập nhật luân chuyển chi nhánh tự động cho bác sĩ
	$list_doctor = $db->query('SELECT id FROM ' .  NV_PREFIXLANG . '_' . $module_data . '_history_branch_doctor WHERE DATE_FORMAT(FROM_UNIXTIME(date_change),"%m-%d") = DATE_FORMAT(NOW(),"%m-%d")')->fetchAll();
	
	
	
	foreach($list_doctor as $doctor)
	{
		update_brand_doctor($doctor['id']);
	}
	
	// lấy danh sách khách hàng có ngày sinh là ngày hôm nay
	$list_patient = $db->query('SELECT confess, full_name, phone FROM ' .  NV_PREFIXLANG . '_' . $module_data . '_patient WHERE DATE_FORMAT(FROM_UNIXTIME(birthday),"%m-%d") = DATE_FORMAT(NOW(),"%m-%d")')->fetchAll();
	
	foreach($list_patient as $patient)
	{
		sent_sms_happybirthday($patient);
	}
	
}


// https://suckhoethiennhan.com/booking/ajax?happynewyeah=1

$happynewyeah = $nv_Request->get_string('happynewyeah', 'get', 0);
if($happynewyeah == 1)
{
	
	// lấy danh sách khách hàng có ngày sinh là ngày hôm nay
	$list_patient = $db->query('SELECT confess, full_name, phone FROM ' .  NV_PREFIXLANG . '_' . $module_data . '_patient')->fetchAll();
	
	foreach($list_patient as $patient)
	{
		
		sent_sms_happynewyear($patient);
	}
	
}


// https://suckhoethiennhan.com/booking/ajax?sms_cron=1
$sms_cron = $nv_Request->get_string('sms_cron', 'get', 0);


if($sms_cron == 1)
{
	
	// lấy danh sách đặt lịch hẹn chưa gửi tin nhắn cách giờ hẹn của khách 1h = 60x60x1 = 5400
	$list_sms = $db->query('SELECT appointment_id, customer_date_booking FROM ' .  NV_PREFIXLANG . '_' . $module_data . '_appointment WHERE is_send_sms = 0 AND (customer_date_booking - ' . NV_CURRENTTIME .') < 5400')->fetchAll();
	//$list_sms = $db->query('SELECT appointment_id, customer_date_booking FROM ' .  NV_PREFIXLANG . '_' . $module_data . '_appointment WHERE appointment_id = 83')->fetchAll();
	
	
	foreach($list_sms as $sms)
	{
		// gửi tin nhắn cho khách hàng
		$appointment_id = $sms['appointment_id'];
		$sms = sent_sms_nhac_kham($appointment_id);
				
		if(empty($sms['error']))
		{
			$sql = 'UPDATE ' .  NV_PREFIXLANG . '_' . $module_data . '_appointment SET is_send_sms=1, sms_result=\'\' WHERE appointment_id=' . $appointment_id;
					
			$db->query($sql);
			
		}
		
		history_sms($appointment_id);
				
	}
	
	
	die(sms_cron_ok);
}


$json = array();

if( ACTION_METHOD == 'getDoctors' )
{
	
	// xử lý lấy danh sách bác sĩ

	$data['booking_date'] = $nv_Request->get_string( 'booking_date', 'post,get', '', '' );
	$data['booking_hour'] = $nv_Request->get_string( 'booking_hour', 'post,get', '', '' );
	$data['branch_id'] = $nv_Request->get_int( 'branch_id', 'post,get', '', 0 );
	
	$data['appointment_id'] = $nv_Request->get_int( 'appointment_id', 'post,get', '', 0 );
	 
		// lấy danh sách bác sĩ thuộc chi nhánh ra
		$list_doctor_branch = list_doctor_branch($data['branch_id']);
	
	
		$array_bs_work = array();
		
		$a_time_work = explode(':', $data['booking_hour']);
		$time_work['gio'] = $a_time_work[0];
		$time_work['phut'] = $a_time_work[1];
		
		$json = array();
		
		// time_work kiểm tra thời gian làm việc
		foreach($list_doctor_branch as $doctor)
		{
			// kiểm tra có đặt lịch thời gian với bs này không
			$check_work = check_work($doctor, $time_work, $data['booking_date'], $data['appointment_id']);
			
			if($check_work)
			{
				// lấy thông tin bác sĩ
				$bs = $db->query('SELECT last_name, first_name FROM ' . NV_USERS_GLOBALTABLE .' WHERE userid =' . $check_work)->fetch();
				$tenbs = $bs['last_name'] . ' ' .  $bs['first_name'];
				
				$json['data'][] = array( 'id' => $check_work, 'text' => $tenbs );
			}
		}
		
	
	
	// kết thúc
	
	if( !isset( $json['data'] ) )
	{
		$json['data'][] = array( 'id' => 0, 'text' => $lang_module['empty_doctors']);
	}
	
	nv_jsonOutput( $json );
}

if( ACTION_METHOD == 'getDoctors2' )
{

	$implode = array();
	
	
	if( !empty( $getSetting['default_group_doctors'] ) )
	{
		$implode[] = 'gu.group_id=' . intval( $getSetting['default_group_doctors'] );
	}

	$doctor = $nv_Request->get_int( 'doctor', 'post,get', 0);
	if($doctor)
	{
		if($user_info['group_id'] == $getSetting['default_group_doctors'])
		{
			$implode[] = 'u.userid=' . intval( $user_info['userid'] );
		}
	}
	
	
	
	$keyword = $nv_Request->get_string( 'search', 'post', '');

	if( !empty( $keyword ) )
	{
		$implode[]= "CONCAT(u.last_name,' ', u.first_name) LIKE '%" . $db->dblikeescape( $keyword ) . "%' OR username LIKE '%" . $db->dblikeescape( $keyword ) . "%'";
	}

	
	$sql= 'SELECT u.userid, u.username, CONCAT(u.last_name,\' \', u.first_name) AS full_name, u.username, u.email, u.address, u.regdate, u.active FROM 
	' . NV_USERS_GLOBALTABLE . ' u  RIGHT JOIN ' . NV_USERS_GLOBALTABLE . '_groups_users gu ON (u.userid = gu.userid)';

	if( !empty( $implode ) )
	{
		$sql .= ' WHERE ' . implode( ' AND ', $implode );
	}		
	
	$sql .= ' ORDER BY full_name ASC LIMIT 0, 200';

	$sth = $db->prepare( $sql );
	$sth->execute();
	while( $item = $sth->fetch( ) )
	{
		$json['data'][] = array( 'id' => $item['userid'], 'text' => nv_htmlspecialchars( $item['full_name'] ) );
	}


	nv_jsonOutput( $json );
}


// lấy danh sách bác sĩ theo phân quyền admin, bác sĩ
// admin được quyền xem tất cả, bác sĩ thì chỉ xem được mình bác sĩ thôi
if( ACTION_METHOD == 'getDoctors3' )
{

	if (!defined('NV_IS_USER'))
	{
		$json['data'][] = array( );
		die;
	}
	
	
	
	$implode = array();
	
	// là bác sĩ
	if($user_info['group_id'] == $getSetting['default_group_doctors'])
	{
		$implode[] = 'u.userid=' . intval( $user_info['userid'] );
	}
	
	
	
	if( !empty( $getSetting['default_group_doctors'] ) )
	{
		$implode[] = 'gu.group_id=' . intval( $getSetting['default_group_doctors'] );
	}

	$keyword = $nv_Request->get_string( 'search', 'post', '');
	$branch_id = $nv_Request->get_int( 'branch_id', 'post', 0);
	if($branch_id)
	{
		$implode[] = 'bu.branch_id =' . $branch_id;
	}

	if( !empty( $keyword ) )
	{
		$implode[]= "CONCAT(u.last_name,' ', u.first_name) LIKE '%" . $db->dblikeescape( $keyword ) . "%' OR username LIKE '%" . $db->dblikeescape( $keyword ) . "%'";
	}

	
	$sql= 'SELECT u.userid, u.username, CONCAT(u.last_name,\' \', u.first_name) AS full_name, u.username, u.email, u.address, u.regdate, u.active FROM 
	' . NV_USERS_GLOBALTABLE . ' u  RIGHT JOIN ' . NV_USERS_GLOBALTABLE . '_groups_users gu ON (u.userid = gu.userid) LEFT JOIN vidoco_vi_booking_branch_users bu ON (u.userid = bu.userid) ';

	if( !empty( $implode ) )
	{
		$sql .= ' WHERE ' . implode( ' AND ', $implode );
	}		
	
	$sql .= ' ORDER BY full_name ASC LIMIT 0, 200';

	$sth = $db->prepare( $sql );
	$sth->execute();
	while( $item = $sth->fetch( ) )
	{
		$json['data'][] = array( 'id' => $item['userid'], 'text' => nv_htmlspecialchars( $item['full_name'] ) );
	}


	nv_jsonOutput( $json );
}

if( ACTION_METHOD == 'getBranch' )
{

	$keyword = $nv_Request->get_string( 'q', 'post', '');
	if( !empty( $keyword ) )
	{
		$implode = array();
		$implode[] = 'title LIKE :title';
	}
	$sql = 'SELECT branch_id, title FROM ' . TABLE_APPOINTMENT_NAME . '_branch';

	if( !empty( $implode ) )
	{
		$sql .= ' WHERE ' . implode( ' AND ', $implode );
	}		
	
	$sql .= ' ORDER BY title ASC LIMIT 0, 200';

	$sth = $db->prepare( $sql );
	
	if( ! empty( $implode ) )
	{
		$sth->bindValue( ':title', '%'.$keyword.'%' );
		
	}
	$sth->execute();
	while( list( $branch_id, $title ) = $sth->fetch( 3 ) )
	{
		$json['data'][] = array( 'id' => $branch_id, 'text' => nv_htmlspecialchars( $title ) );
	}

	nv_jsonOutput( $json );
}


if( ACTION_METHOD == 'getcalendar' )
{

	$getday = $nv_Request->get_string( 'getday', 'post', '');
	$type = $nv_Request->get_int( 'type', 'post', 0);
	
	$getday = convertToTimeStamp( $getday );
	$current_day = date('d', $getday );
	$current_month = date('m', $getday );
	$current_year = date('Y', $getday );
	$last_day_month = date("t", $getday );
	
	
	
	if( $type == 0 )
	{
		
		$beginday = '01/'. $current_month .'/' . $current_year; 
		$endday = str_pad( $last_day_month, 2, "0", STR_PAD_LEFT) . '/'. $current_month .'/' . $current_year; 
		
		$endday = convertToTimeStamp( $endday, 0, 23, 59, 59 );
		$beginday = convertToTimeStamp( $beginday );
		


	}
	else 
	{
		$beginday = str_pad( $current_day, 2, "0", STR_PAD_LEFT) . '/'. $current_month .'/' . $current_year; 
		$endday = str_pad( $last_day_month, 2, "0", STR_PAD_LEFT) . '/'. $current_month .'/' . $current_year; 

		$endday = convertToTimeStamp( $beginday, 0, 23, 59, 59 );	
		$beginday = convertToTimeStamp( $beginday );


	}

	$result = $db->query( 'SELECT * FROM ' . TABLE_APPOINTMENT_NAME . '_calendar WHERE userid = '. $getUserid .' AND date_start BETWEEN ' . intval( $beginday ) . ' AND ' . intval( $endday ) . ' ORDER BY date_start ASC' );
	while( $data = $result->fetch() )
	{
		$data['date_start'] = date('d/m/Y', $data['date_start']);
		$data['day'] = intval( date('d', $data['date_start']) );
		$data['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $data['calendar_id'] );
		$data['disabled'] = ( ( convertToTimeStamp( $data['date_start'] ) + 86399 ) < NV_CURRENTTIME ) ? 'disabled="disabled"' : '';
		$json['data'][$data['date_start']] = $data;
		
	}

	nv_jsonOutput( $json );
}

if( ACTION_METHOD == 'savecalendar' )
{

	$data['shift'] = $nv_Request->get_int( 'key', 'post,get', 0 );
	$data['date_start'] = $nv_Request->get_int( 'time', 'post,get', 0 );
	$data['userid'] = isset( $user_info['userid'] ) ? $user_info['userid'] : 0;
	$data['date_added'] = NV_CURRENTTIME;
	$data['date_modified'] = 0;
	$error = array();
	$success = 0;
	$check_calender = $db->query('SELECT COUNT(*) FROM ' . TABLE_APPOINTMENT_NAME . '_calendar WHERE date_start=' . intval( $data['date_start'] ) . ' AND userid='. intval( $data['userid'] ) . ' AND shift = ' . $data['shift'])->fetchColumn();
	if($check_calender==0){
		$stmt = $db->prepare( 'INSERT INTO ' . TABLE_APPOINTMENT_NAME . '_calendar SET 
			date_start=' . intval( $data['date_start'] ) . ',
			userid=' . intval( $data['userid'] ) . ',
			shift=' . intval( $data['shift'] ) . ',
			date_added=' . intval( $data['date_added'] ) . ',
			date_modified=' . intval( $data['date_modified'] ) 
		);
		$stmt->execute();
		$json[] = ['status'=>'OK', 'text'=>'Đăng ký ca ' . $data['shift'] .' ngày ' . $data['date_start'] . ' thành công'];
	}else{
		$db->query('DELETE FROM ' . TABLE_APPOINTMENT_NAME . '_calendar WHERE date_start=' . intval( $data['date_start'] ) . ' AND userid='. intval( $data['userid'] ) . ' AND shift = ' . $data['shift']);
		$json[] = ['status'=>'OK', 'text'=>'Xóa ca ' . $data['shift'] .' ngày ' . $data['date_start'] . ' thành công'];
	}
	print_r(json_encode($json[0]));die();
}
if( ACTION_METHOD == 'register_all' )
{

	$data['time'] = $nv_Request->get_int( 'time', 'post,get', 0 );
	$lastdate = date("t",$data['time']); 
	$data['date_modified'] = 0;
	for( $day = 1; $day <= $lastdate; ++$day )
	{

		$date_start = str_pad($day, 2, "0", STR_PAD_LEFT) . '/'. date( 'm/Y', $data['time'] );
		$date_now = convertToTimeStamp( $date_start, 0, 0, 0, 0 );
		if($date_now>NV_CURRENTTIME){
			for( $i = 1; $i <= 3; ++$i )
			{
				$check_calender = $db->query('SELECT COUNT(*) FROM ' . TABLE_APPOINTMENT_NAME . '_calendar WHERE date_start=' . intval( $date_now ) . ' AND userid='. intval( $user_info['userid'] ) . ' AND shift = ' . $i)->fetchColumn();
				if($check_calender == 1){

				}else{
					$stmt = $db->prepare( 'INSERT INTO ' . TABLE_APPOINTMENT_NAME . '_calendar SET 
						date_start=' . intval( $date_now ) . ',
						userid=' . intval( $user_info['userid'] ) . ',
						shift=' . intval( $i ) . ',
						date_added=' . intval( NV_CURRENTTIME ) . ',
						date_modified=' . intval( $data['date_modified'] ) 
					);
					$stmt->execute();
				}
			}
		}

	}
	$json[] = ['status'=>'OK', 'text'=>'Đăng ký thành công'];
	print_r(json_encode($json[0]));die();
}
if( ACTION_METHOD == 'delete_register_all' )
{

	$data['time'] = $nv_Request->get_int( 'time', 'post,get', 0 );
	$lastdate = date("t",$data['time']); 
	$data['date_modified'] = 0;
	for( $day = 1; $day <= $lastdate; ++$day )
	{

		$date_start = str_pad($day, 2, "0", STR_PAD_LEFT) . '/'. date( 'm/Y', $data['time'] );
		$date_now = convertToTimeStamp( $date_start, 0, 0, 0, 0 );
		if($date_now>NV_CURRENTTIME){
			for( $i = 1; $i <= 3; ++$i )
			{
				$check_calender = $db->query('SELECT COUNT(*) FROM ' . TABLE_APPOINTMENT_NAME . '_calendar WHERE date_start=' . intval( $date_now ) . ' AND userid='. intval( $user_info['userid'] ) . ' AND shift = ' . $i)->fetchColumn();
				if($check_calender == 1){

				}else{
					$db->query('DELETE FROM ' . TABLE_APPOINTMENT_NAME . '_calendar WHERE date_start=' . intval( $date_now ) . ' AND userid='. intval( $user_info['userid'] ) . ' AND shift = ' . $i);
				}
			}
		}

	}
	$json[] = ['status'=>'OK', 'text'=>'Đăng ký thành công'];
	print_r(json_encode($json[0]));die();
}

if($mod=='register_medical'){

	$data['customer_full_name'] = nv_substr( $nv_Request->get_title( 'booking_name', 'post', '', '' ), 0, 250 );
	$data['customer_phone'] = nv_substr( $nv_Request->get_title( 'booking_phone', 'post', '', '' ), 0, 250 );
	$data['customer_email'] = nv_substr( $nv_Request->get_title( 'booking_email', 'post', '', '' ), 0, 250 );

	$data['customer_message'] = $nv_Request->get_textarea( 'booking_message', '', 'br', 1 );
	$data['service_id'] = $nv_Request->get_typed_array( 'booking_service', 'post', 'int', array() );
	$booking_date = $data['customer_date_booking'] = nv_substr( $nv_Request->get_title( 'booking_date', 'post', '', '' ), 0, 10 );

	$booking_hour = $data['customer_time_set'] = nv_substr( $nv_Request->get_title( 'booking_hour', 'post', '', '' ), 0, 10 );

	$branch_id = $nv_Request->get_array( 'branch_id', 'post', 0 );
	$data['branch_id'] = $branch_id[0];
	
	
	$data['password'] = $crypt->hash_password($data['customer_phone'], $global_config['hashprefix']);
	$data['md5username'] = nv_md5safe($data['customer_phone']);

	$data['sms_result'] ='';

	$data['patient_group'] = 6;
	if($data['customer_email'] == ''){
		$data['customer_email'] = $data['customer_phone'] . '@gmail.com';
	}
	$ii = 1;


	if( preg_match( '/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $data['customer_date_booking'], $m ) )
	{

		$time = array_map( 'trim', explode( ':', $data['customer_time_set'] ) );
		$hour = isset( $time[0] ) ? intval( $time[0] ) : 0;
		$minute = isset( $time[1] ) ? intval( $time[1] ) : 0;

		$data['customer_date_booking'] = mktime( $hour, $minute, 0, $m[2], $m[1], $m[3] );
	}
	else
	{
		$data['customer_date_booking'] = 0;
	}
	$error = array();
	
	
	
	if( empty( $data['branch_id'] ) ) $error['branch_id'] = $lang_module['appointment_error_branch_id'];
	
	
	//$error['branch_id'] = $data['branch_id'];
	
	if( empty( $data['customer_full_name'] ) ) $error['customer_full_name'] = $lang_module['appointment_error_customer_full_name'];
	if( empty( $data['customer_phone'] ) ) $error['customer_phone'] = $lang_module['appointment_error_customer_phone'];
	// if( empty( $data['customer_email'] ) ) $error['customer_email'] = $lang_module['appointment_error_customer_email'];
	if( empty( $data['customer_date_booking'] ) ) $error['customer_date_booking'] = $lang_module['appointment_error_customer_date_booking'];
	if( empty( $data['customer_time_set'] ) ) $error['customer_time_set'] = $lang_module['appointment_error_customer_time_set'];
	if( empty( $data['service_id'] ) ) $error['service_id'] = $lang_module['appointment_error_service'];


	if( preg_match( '/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $booking_date, $m ) )
	{

		$time = array_map( 'trim', explode( ':', $booking_hour ) );
		$hour = isset( $time[0] ) ? intval( $time[0] ) : 0;
		$minute = isset( $time[1] ) ? intval( $time[1] ) : 0;

		$booking_date = mktime( $hour, $minute, 0, $m[2], $m[1], $m[3] );

		$date_start_begin = mktime( 0, 0 , 0, $m[2], $m[1], $m[3] );
		$date_start_end = mktime( 23, 59, 59, $m[2], $m[1], $m[3] );


	}
	else
	{
		$booking_date = 0;
	}
	$shift = 0;

	if( $booking_date > 0 )
	{
		$time_morning_begin = mktime( 07, 0 , 0, $m[2], $m[1], $m[3] );
		$time_morning_end = mktime( 13, 59, 59, $m[2], $m[1], $m[3] );
		$time_afternoon_begin = mktime( 14, 0 , 0, $m[2], $m[1], $m[3] );
		$time_afternoon_end = mktime( 17, 59, 59, $m[2], $m[1], $m[3] );
		$time_night_begin = mktime( 18, 0 , 0, $m[2], $m[1], $m[3] );
		$time_night_end = mktime( 21, 59, 59, $m[2], $m[1], $m[3] );

		if( $booking_date >= $time_morning_begin && $booking_date <= $time_morning_end )
		{
			$shift = 1;

		}
		elseif( $booking_date >= $time_afternoon_begin && $booking_date <=  $time_afternoon_end )
		{

			$shift = 2;

		}
		elseif( $booking_date >= $time_night_begin && $booking_date <=  $time_night_end )
		{

			$shift = 3;

		}


	}



	$implode = array();


		// if( !empty( $getSetting['default_group_doctors'] ) )
		// {
			// $implode[] = 'gu.group_id=' . intval( $getSetting['default_group_doctors'] );
		// }
	
	/*

	if( $shift > 0 && $date_start_begin > 0 )
	{
		$keyword = $nv_Request->get_string( 'search', 'post', '');

		$implode[] = 'c.shift=' . intval( $shift );



		$implode[] = 'c.date_start BETWEEN ' . intval( $date_start_begin ) . ' AND ' . intval( $date_start_end );

		if( !empty( $branch_id ) )
		{
			$implode[] = 'bu.branch_id=' . intval( $branch_id );
		}

		if( !empty( $keyword ) )
		{
			$implode[]= "CONCAT(u.last_name,' ', u.first_name) LIKE '%" . $db->dblikeescape( $keyword ) . "%' OR username LIKE '%" . $db->dblikeescape( $keyword ) . "%'";
		}
		$sql= 'SELECT u.userid, u.username, CONCAT(u.last_name,\' \', u.first_name) AS full_name, u.username, u.email, u.address, u.regdate, u.active, bu.branch_id FROM 
		' . NV_USERS_GLOBALTABLE . ' u  RIGHT JOIN ' . TABLE_APPOINTMENT_NAME . '_branch_users bu ON (u.userid = bu.userid) 
		LEFT JOIN ' . TABLE_APPOINTMENT_NAME . '_calendar c ON (u.userid = c.userid)';

		if( !empty( $implode ) )
		{
			$sql .= ' WHERE ' . implode( ' AND ', $implode );
		}		

		$sql .= ' ORDER BY full_name ASC LIMIT 0, 200';

		$sth = $db->prepare( $sql );
		$sth->execute();
		$mang = array();
		while( $item = $sth->fetch( ) )
		{
			$check = $db->query('SELECT COUNT(*) FROM ' . TABLE_APPOINTMENT_NAME . '_appointment WHERE doctors_id = ' . $item['userid'] . ' AND customer_date_booking = ' . $data['customer_date_booking'])->fetchColumn();
			if($check == 0){
				$mang[] = $item;
			}
			// $json['data'][] = array( 'id' => $item['userid'], 'text' => nv_htmlspecialchars( $item['full_name'] . ''  ) );
		}
		$data['doctors_id'] = $mang[array_rand($mang)]['userid'];

	}

	*/


	$data['doctors_id'] = $nv_Request->get_int( 'doctors_id', 'post,get', 0 );




	if( empty( $error ) )
	{

		$service_id = ( ! empty( $data['service_id'] ) ) ? implode( ',', $data['service_id'] ) : '';
		try
		{		
	
			if(true){
				$check_user = $db->query('SELECT count(*) FROM ' . $db_config['prefix'] . '_users WHERE username = "' .$data['customer_phone'] . '"')->fetchColumn();
				$info_user_register = $db->query('SELECT * FROM ' . $db_config['prefix'] . '_users WHERE username = "' .$data['customer_phone'] . '"')->fetch();
				$check_email = $db->query('SELECT userid FROM ' . $db_config['prefix'] . '_users WHERE email = "' .$data['customer_email'] . '"')->fetchColumn();
				if($data['customer_email'] == ''){
					$data['customer_email'] = $data['customer_phone'] . '@gmail.com';
				}
				
				//$user_id = $getUserid;
				
				if($check_email)
				{
					$user_id = $check_email;
				}
				if($check_user == 1){

					$user_id = $info_user_register['userid'];

				}
				
				
				
				if(!$user_id){
				
					$data['sig'] = '';
					$data['in_groups_default'] = 4;
					$data['in_groups'] = 4;
					$data['view_mail']= 0;
					$data['is_email_verified']= -1;
					$data['address'] = '';
					$data['gender'] = 'M';
					$data['service_package_id'] = 0;
					$birthday = 0;

					$sql = "INSERT INTO " . $db_config['prefix'] . "_users (
					group_id, username, md5username, password, first_name, email, phone, sig, regdate,
					question, answer, passlostkey, view_mail,
					remember, in_groups, active, checknum, last_login, last_ip, last_agent, last_openid, email_verification_time,
					active_obj
					) VALUES (
					" . $data['in_groups_default'] . ",
					'" . $data['customer_phone'] . "',
					'" . $data['md5username'] . "',
					'" . $data['password'] . "',
					'" . $data['customer_full_name'] . "',
					'" . $data['customer_email'] . "', 
					'" . $data['customer_phone'] . "', 
					'" . $data['sig'] . "', 
					" . NV_CURRENTTIME . ",
					'" . $data['customer_phone'] . "', 
					'" . $data['customer_phone'] . "', 
					'',
					" . $data['view_mail'] . ",
					1,
					'" . implode(',', $data['in_groups']) . "', 1, '', 0, '', '', '',
					" . ($data['is_email_verified'] ? '-1' : '0') . ",
					'SYSTEM'
				)";
				$data_insert = [];
				$userid = $db->insert_id($sql, 'userid', $data_insert);

				$user_id = $userid;
				
			}
			
			// kiểm tra có tài khoản khách hàng chưa
			if($user_id)
			{
				$check_kh = $db->query('SELECT userid FROM ' . NV_PREFIXLANG . '_' . $module_data . '_patient WHERE userid =' .$user_id)->fetchColumn();
				
				if(!$check_kh)
				{
					// thêm khách hàng
					// lấy mã kh lớn nhất
					$max = $db->query("SELECT max(patient_code) as max FROM " . NV_PREFIXLANG . "_" . $module_name . "_patient")->fetchColumn();
					$patient_code = $max + 1;
					
					$data['patient_group'] = 6;
					$data['address'] = '';
					$data['gender'] = 'M';
					$data['service_package_id'] = 0;
					$birthday = 0;
					
					

					$db->query( 'INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_patient(userid,patient_group,full_name,confess,phone,service_package_id,gender,birthday,address,patient_code,date_added,branch) VALUES('.$user_id. ',' . $data['patient_group'] . ', "' . $data['customer_full_name'] . '", "Quý khách","' . $data['customer_phone'] . '", ' . $data['service_package_id'] . ', "' . $data['gender'] . '",' . $birthday . ', "' . $data['address'] . '", "' . $patient_code . '", ' . NV_CURRENTTIME . ', ' . $data['branch_id'] . ' )' );
					
				}
			}
		}

		$stmt = $db->prepare( 'INSERT INTO ' .  NV_PREFIXLANG . '_' . $module_data . '_appointment SET 
			sms_result=:sms_result,
			customer_full_name=:customer_full_name,
			customer_email=:customer_email,
			customer_phone=:customer_phone,
			customer_message=:customer_message,
			service_id=:service_id,
			branch_id=' . intval( $data['branch_id'] ) . ',
			doctors_id=' . intval( $data['doctors_id'] ) . ',
			userid=' . intval( $user_id ) . ',
			customer_date_booking=' . intval( $data['customer_date_booking'] ) . ',
			date_added=' . intval( NV_CURRENTTIME ) );

		$stmt->bindParam( ':sms_result', $data['sms_result'], PDO::PARAM_STR );
		$stmt->bindParam( ':customer_full_name', $data['customer_full_name'], PDO::PARAM_STR );
		$stmt->bindParam( ':customer_email', $data['customer_email'], PDO::PARAM_STR );
		$stmt->bindParam( ':customer_phone', $data['customer_phone'], PDO::PARAM_STR );
		$stmt->bindParam( ':customer_message', $data['customer_message'], PDO::PARAM_STR );
		$stmt->bindParam( ':service_id', $service_id, PDO::PARAM_STR );

		$stmt->execute();
		
		/*
		// gửi sms tới khách hàng
		// lấy $appointment_id
		$appointment_id = $db->query('SELECT max(appointment_id) as appointment_id FROM ' .  NV_PREFIXLANG . '_' . $module_data . '_appointment WHERE customer_phone = "'. $data['customer_phone'] .'"')->fetchColumn();
		
		$sms = sent_sms_nhac_kham($appointment_id);
		
		if(empty($sms['error']))
		{
			$sql = 'UPDATE ' .  NV_PREFIXLANG . '_' . $module_data . '_appointment SET is_send_sms=1, sms_result=\'\' WHERE appointment_id=' . $appointment_id;
			
			$db->query($sql);
		}
		
		*/
		
		// LẤY xưng hô $user_id
		$confess = $db->query('SELECT confess FROM ' .  NV_PREFIXLANG . '_' . $module_data . '_patient WHERE userid ='. $user_id)->fetchColumn();
		if(empty($confess))
			$confess = 'Quý khách';
		
		
		$arr_name = explode(' ', $data['customer_full_name']);
		$customer_name = end($arr_name);
		
		$data['customer_time_set'] = str_replace(' ','',$data['customer_time_set']);

		
		$json['success'] = 'Cảm ơn '. $confess .' ' . $customer_name . ' đã đặt lịch hẹn trị liệu tại Cơ Xương Khớp Thiện Nhân lúc ' . $data['customer_time_set'] . ', ngày ' . date('d/m/Y',$data['customer_date_booking']) . ', Vui lòng đến trước 10 phút để nghỉ ngơi, ổn định huyết áp trước khi trị liệu. Trân trọng cảm ơn!';
	
		$email_title = 'CƠ XƯƠNG KHỚP THIỆN NHÂN - [BÁO LỊCH HẸN]';
		
		$lienhe ='<br /><br />Thông tin liên hệ:<br />
CƠ SỞ TRỊ LIỆU CƠ XƯƠNG KHỚP THIỆN NHÂN<br />
Địa chỉ: 112/36 Tây Hòa , Phước Long A, Tp Thủ Đức, Tp Hồ Chí Minh<br />
Chi nhánh Tân Bình: 277 Hoàng Văn Thụ, Phường 2, Tân Bình, Thành phố Hồ Chí Minh<br />
Chi nhánh Quận 9: 112/36 Tây Hòa, Phước Long A, Quận 9, Thành phố Hồ Chí Minh<br />
Điện thoại: 028.9999 44 55 - 0336 044 055<br />
Email: <a href="mailto:thiennhan.tdcs@gmail.com" target="_blank">thiennhan.tdcs@gmail.com</a>';

		if($global_config['site_email'])
		{
			/*
			// lay ten bacsi
			$name_bs = '';
			if($data['doctors_id'])
			{
				$tenbacsi = $db->query('SELECT last_name, first_name FROM vidoco_users WHERE userid ='. $data['doctors_id'])->fetch();
				$name_bs = $tenbacsi['last_name'] . ' ' . $tenbacsi['first_name'];
			}
			*/
			
			if($data['branch_id'])
			{
				$chinhanh = $db->query('SELECT title FROM ' .  NV_PREFIXLANG . '_' . $module_data . '_branch WHERE branch_id ='. $data['branch_id'])->fetchColumn();
			}
			
			// LẤY MÃ KH $user_id
			$patient_code = $db->query('SELECT patient_code FROM ' .  NV_PREFIXLANG . '_' . $module_data . '_patient WHERE userid ='. $user_id)->fetchColumn();
			
			$content1 .= '<div>Thời gian trị liệu: ' . date('d/m/Y - H:i:s',$data['customer_date_booking']) . '</div>';
			$content1 .= '<div>Mã KH: ' . $patient_code . '</div>';
			$content1 .= '<div>Tên khách hàng: ' . $data['customer_full_name'] . '</div>';
			$content1 .= '<div>Số điện thoại: ' . $data['customer_phone'] . '</div>';
			$content1 .= '<div>Ghi chú: ' . $data['customer_message'] . '</div>';
			$content1 .= '<div>Chi nhánh: ' . $chinhanh . '</div>';
			
			$content1 .= $lienhe;
			
			
			
			
			$a = nv_sendmail(array($global_config['site_name'], $config_email['sender_email']['config_value']), $global_config['site_email'], sprintf($email_title, $module_info['custom_title'], $order_code), $content1);
		
		}
		
		// gui email cho kh
		
		
		if(!empty($data['customer_email']))
		{
			$content_customer = 'Cảm ơn '. $confess .' ' . $customer_name . ' đã đặt lịch hẹn trị liệu tại Cơ Xương Khớp Thiện Nhân lúc ' . $data['customer_time_set'] . ', ngày ' . date('d/m/Y',$data['customer_date_booking']) . ', Vui lòng đến trước 10 phút để nghỉ ngơi, ổn định huyết áp trước khi trị liệu. Trân trọng cảm ơn!';
			$content_customer .= $lienhe;
			
			$kh = nv_sendmail(array($global_config['site_name'], $config_email['sender_email']['config_value']), $data['customer_email'], sprintf($email_title, $module_info['custom_title'], $order_code), $content_customer);
		}
		



 

		if( $data['appointment_id'] = $db->lastInsertId() )
		{

			$json['success'] = 'Cảm ơn '. $confess .' ' . $customer_full_name . ' đã đặt lịch hẹn trị liệu tại Cơ Xương Khớp Thiện Nhân lúc ' . $data['customer_time_set'] . ', ngày ' . date('d/m/Y',$data['customer_date_booking']) . ', Vui lòng đến trước 10 phút để nghỉ ngơi, ổn định huyết áp trước khi trị liệu. Trân trọng cảm ơn!';
		}
	}
	catch ( PDOException $e )
	{
		$error['save'] = $lang_module['appointment_error_save'];
		// var_dump( $e ); die();
	}	
}

}

// xử lý lấy thời gian khám bệnh
// https://suckhoethiennhan.com/index.php?nv=booking&op=ajax&mod=load_calender_register_new&branch_id_i=1&booking_date_i=17/03/2021&timing_active_i=08:00


if($mod=='load_calender_register_new'){
	
	$data['booking_date'] = $nv_Request->get_string( 'booking_date_i', 'post,get', '', '' );
	$data['timing_active'] = $nv_Request->get_string( 'timing_active_i', 'post,get', '', '' );
	$data['branch_id'] = $nv_Request->get_int( 'branch_id_i', 'post,get', '', 0 );
	
	// bác sĩ đặt lịch
	// khóa lại. bác sĩ có thể đặt cho bs khác được
	//$data['doctor_id'] = $nv_Request->get_int( 'doctor_id_i', 'post,get', '', 0 );
	
	 
	// lấy danh sách bác sĩ thuộc chi nhánh ra
	if($data['doctor_id'])
	{
		$list_doctor_branch[] = $data['doctor_id'];
	}
	else
	{		
		// lấy danh sách bác sĩ cũ. lấy trong mặc định ra
		//$list_doctor_branch = list_doctor_branch($data['branch_id']);
		
		// lấy danh sách bác sĩ mới. lấy theo chi nhanh, theo thời gian. xem tại thời điểm đó bác sĩ làm việc ở chi nhánh nào. có còn làm việc hay không.
		$list_doctor_branch = list_doctor_branch_new($data['branch_id'],$data['booking_date']);
		
		
	}
	
	$list_all = array();
	
	
	
	$quakhu = false;
	
	$day_current = date('d/m/Y', NV_CURRENTTIME);
	if($day_current == $data['booking_date'])
	{
		$quakhu = true;
		$gio_current = date('H', NV_CURRENTTIME);
		$phu_current = date('i', NV_CURRENTTIME);
	}
	
	
	
	foreach($array_time_works as $time_work)
	{
		
		if($quakhu)
		{
			// kiểm tra giờ, phút hiện tại
			if( ($time_work['gio'] < $gio_current) or ($gio_current == $time_work['gio'] and $time_work['phut'] < $phu_current ))
			{	
				continue;
			}
		} 
		
		$array_bs_work = array();
		
		// time_work kiểm tra thời gian làm việc
		foreach($list_doctor_branch as $doctor)
		{
			// kiểm tra có đặt lịch thời gian với bs này không
			$check_work = check_work($doctor, $time_work, $data['booking_date']);
			
			if($check_work)
			{
				$array_bs_work[] = $check_work;
			}
		}
		
		if($array_bs_work)
		{
			$arr = array();
			$arr['time'] = $time_work;
			$arr['bs'] = $array_bs_work;
			
			$list_all[] = $arr;
		}
	}
	
	
	$content = list_time_order_doctor($list_all, $list_doctor_branch);
	if(empty($list_all))
		$content = 'Đã hết giờ trị liệu. Vui lòng chọn ngày khác!';
	print_r($content);die;
	
	
	
}


if($mod=='get_info_user'){
	
	$userid = $nv_Request->get_int( 'userid_kh', 'post,get', '', 0 );
	// lấy thông tin khách hàng
	
	$info = $db->query('SELECT phone, full_name as name FROM ' .  NV_PREFIXLANG . '_' . $module_data . '_patient WHERE userid ='. $userid)->fetch();
	
	$info['email'] = $db->query('SELECT email FROM vidoco_users WHERE userid ='. $userid)->fetchColumn();
	
	
	$info['doctor'] = 0;
	// kiểm tra tài khoản của thầy không
	if($user_info['group_id'] == $getSetting['default_group_doctors'])
	{
		$info['doctor'] = 1;
	}
	
	print_r(json_encode($info));die;
	
}

if($mod=='load_calender_register'){
	$data['time'] = $nv_Request->get_string( 'time', 'post', '', '' );
	$time_current = convertToTimeStamp( $data['time'], $default=0, $phour=0, $pmin=0, $second=0 );
	$booking_time = explode( '-', $getSetting['booking_time'] );
	$begintime = isset( $booking_time[0] ) ? intval( $booking_time[0] ) : 0;
	$endtime = isset( $booking_time[1] ) ? $booking_time[1] : 0;
	$list_time = array();

	if( !empty( $begintime ) && !empty( $endtime ) )
	{
		preg_match( '/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', date('d/m/Y', NV_CURRENTTIME), $m );
		$date_from = mktime( $begintime, 0, 0, $m[2], $m[1], $m[3] );
		$date_to = mktime( $endtime, 23, 59, $m[2], $m[1], $m[3] );
		while( $date_from <= $date_to )
		{
			$time = date( 'H:i', $date_from );

			if( !in_array( $time, array( '07:00', '12:00', '12:30', '13:00', '13:30', '14:00', '21:00'  ) ) )
			{
				$list_time[] = $time;
			}
			$date_from = $date_from + ( $getSetting['space_time'] * 60 );
		}
	}







	$current_day = date('d', $time_current );
	$current_month = date('m', $time_current );
	$current_year = date('Y', $time_current );
	$last_day_month = date("t", $time_current );



	$beginday1 = $current_day . '/'. $current_month .'/' . $current_year; 

	$endday1 = str_pad( $last_day_month, 2, "0", STR_PAD_LEFT) . '/'. $current_month .'/' . $current_year; 
	$endday = convertToTimeStamp( $beginday1, 0, 23, 59, 59 );
	$beginday = convertToTimeStamp( $beginday1, $default=0, $phour=0, $pmin=0, $second=0 );

	$result = $db->query('SELECT * FROM ' . NV_PREFIXLANG . '_booking' . '_calendar WHERE date_start BETWEEN ' . intval( $beginday ) . ' AND ' . intval( $endday ) . ' ORDER BY date_start ASC')->fetchAll();


	$array_config = $array_config;
	$time_space = $array_config['space_time']['config_value'] * 60;
//ca sáng
	$list_time = array();
	$list_time_begin = convertToTimeStamp($beginday1, 0, 8, 0, 0);
	for ($l=1; $l <= 6 ; $l++) {
		if($l==1){
			$list_time_begin = $list_time_begin;
		}else{
			$list_time_begin = $list_time_begin + $time_space;
		}
		$list_time[] = date('H:i',$list_time_begin);

	}
	$list_time_begin = convertToTimeStamp($beginday1, 0, 13, 30, 0);
	for ($ll=1; $ll <= 6 ; $ll++) {
		if($ll==1){
			$list_time_begin = $list_time_begin;
		}else{
			$list_time_begin = $list_time_begin + $time_space;
		}
		$list_time[] = date('H:i',$list_time_begin);
	}

	$list_time_begin = convertToTimeStamp($beginday1, 0, 17, 30, 0);
	for ($lll=1; $lll <= 4 ; $lll++) {
		if($lll==1){
			$list_time_begin = $list_time_begin;
		}else{
			$list_time_begin = $list_time_begin + $time_space;
		}
		$list_time[] = date('H:i',$list_time_begin);

	}
	if( $list_time )
	{
		$i = 0;
		$div = '<ul class="timing-list" id="timing-list">';
		foreach( $list_time as $key => $time )
		{

			$class = 'hidden';
			$time1 = explode(':', $time);
			$ii = 1;
			foreach ($time1 as $key => $value) {
				if($ii == 1){
					$hour = $value;
				}else{
					$minute = $value;
				}
				$ii++;
			}

			$time_begin = convertToTimeStamp($beginday1, 0, $hour, $minute, 0);
			$time_end = $time_begin + $time_space;
			$count_time_slot1 = 0;
			$count_time_slot2 = 0;
			$count_time_slot3 = 0;
			foreach ($result as $key1 => $value1) {
				if($value1['shift']==1){
					$count_time_slot1 = $count_time_slot1 + 1;
				}
				if($value1['shift']==2){
					$count_time_slot2 = $count_time_slot2 + 1;
				}
				if($value1['shift']==3){
					$count_time_slot3 = $count_time_slot2 + 1;
				}

				if($value1['shift'] == 1){
					$time_slot_begin = convertToTimeStamp($beginday1, 0, 8, 0, 0);
					$time_slot = $time_slot_begin;
					for ($i=1; $i <= $value1['shift'] + 5 ; $i++) {
						$time_slot = $time_slot + $time_space;
						$time_slot_end = $time_slot;
						$time_slot_begin = $time_slot - $time_space;
						$h_time_slot = date('H',$time_slot_end);
						$i_time_slot = date('i',$time_slot_end);
						if($hour==$h_time_slot){
							$class = 'active';
						}
					}
				}else if($value1['shift'] == 2){
					$time_slot_begin = convertToTimeStamp($beginday1, 0, 12, 30, 0);
					$time_slot = $time_slot_begin;
					for ($i=1; $i <= $value1['shift'] + 5 ; $i++) {
						$time_slot = $time_slot + $time_space;
						$time_slot_end = $time_slot;
						$time_slot_begin = $time_slot - $time_space;
						$h_time_slot = date('H',$time_slot_end);
						$i_time_slot = date('i',$time_slot_end);

						if($hour==$h_time_slot){
							$class = 'active';
						}
					}
				}else{
					$time_slot_begin = convertToTimeStamp($beginday1, 0, 17, 30, 0);
					$time_slot = $time_slot_begin;
					for ($i=1; $i <= $value1['shift'] + 3 ; $i++) {
						$time_slot = $time_slot + $time_space;
						$time_slot_end = $time_slot;
						$time_slot_begin = $time_slot - $time_space;
						$h_time_slot = date('H',$time_slot_end);
						$i_time_slot = date('i',$time_slot_end);
						if($hour==$h_time_slot){
							$class = 'active';
						}
					}
				}

			}
			$time_check = convertToTimeStamp($beginday1, 0, $hour, $minute, 0);
			$time_morning = convertToTimeStamp($beginday1, 0, 12, 0, 0);
			$time_afternoon = convertToTimeStamp($beginday1, 0, 17, 30, 0);
			$time_night = convertToTimeStamp($beginday1, 0, 22, 0, 0);

			$count_register = $db->query('SELECT count(*) FROM ' . NV_PREFIXLANG . '_booking_appointment where doctors_id = 1 AND customer_date_booking = ' . $time_check)->fetchColumn();

			if($hour >= 8 && $hour < 12){
				if($class == 'active' && $count_time_slot1 - $count_register <= 0){
					$class = 'hidden';
				}
			}
			else if($hour > 12 && $hour < 18){
				if($class == 'active' && $count_time_slot2 - $count_register <= 0){
					$class = 'hidden';
				}

			}else if($hour >= 18 && $hour < 22){
				if($class == 'active' && $count_time_slot3 - $count_register <= 0){
					$class = 'hidden';
				}

			}else{

			}


			if($current_day == date('d',NV_CURRENTTIME)){
				$time_crrent_check = date('H',NV_CURRENTTIME);

				if($time_crrent_check > ($hour - 1)){
					$class = 'hidden';
				}
			}



			++$i;
			$div .= '<li class="' .$class. '">' .$time. '</li>';
		}
		$div .= '</ul>';
	}
	$div .= "<script type='text/javascript'>$('ul.timing-list li').on('click', function(){
		$('ul.timing-list li').removeClass('timing-list-active');
		$(this).addClass('timing-list-active');
		});$('ul.timing-list li').on('click', function(){
			var bookingTime = $(this).text();
			$('#booking_hour').val(bookingTime);
			$('#doctors').val('').trigger('change');
		});</script>";


		$json=array("status"=>"OK", "text"=>$div);
		nv_jsonOutput($json);
	}

// $data['booking_location'] = nv_substr( $nv_Request->get_title( 'booking_location', 'post', '', '' ), 0, 250 );


	$json['error'] = $error;

	nv_jsonOutput( $json );