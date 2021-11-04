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

$json = array();

if( ACTION_METHOD == 'getDoctors' )
{

	$branch_id =  $nv_Request->get_int( 'branch_id', 'post', 0);
	$booking_date =  $nv_Request->get_string( 'booking_date', 'post', '');
	$booking_hour =  $nv_Request->get_string( 'booking_hour', 'post', '');
	$date_start_begin = $date_start_end = 0;
	if( $branch_id > 0 )
	{
		
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
			elseif( $booking_date > $time_afternoon_begin && $booking_date <  $time_afternoon_end )
			{
				
				$shift = 2;
				
			}
			elseif( $booking_date > $time_night_begin && $booking_date <  $time_night_end )
			{
				
				$shift = 3;
				
			}
			
		}
		

		
		$implode = array();
		
		
		// if( !empty( $getSetting['default_group_doctors'] ) )
		// {
			// $implode[] = 'gu.group_id=' . intval( $getSetting['default_group_doctors'] );
		// }
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
			while( $item = $sth->fetch( ) )
			{
				$json['data'][] = array( 'id' => $item['userid'], 'text' => nv_htmlspecialchars( $item['full_name'] . ' - ' . $item['username'] ) );
			}
		}	
		
	}
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
		$json['data'][] = array( 'id' => $item['userid'], 'text' => nv_htmlspecialchars( $item['full_name'] . ' - ' . $item['username'] ) );
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
	$getCalendar = $nv_Request->get_typed_array( 'getCalendar', 'post', 'array', array() );
	$error = array();
	$success = 0;
	foreach( $getCalendar as $day => $data )
	{
		$data['token'] = isset( $data['token'] ) ? $data['token'] : '';
		$data['calendar_id'] = isset( $data['calendar_id'] ) ? intval( $data['calendar_id'] ) : 0;
		$data['date_start'] = isset( $data['date_start'] ) ? (string)$data['date_start']: '';
		$data['date_start'] = convertToTimeStamp( $data['date_start'] );
		$data['userid'] = isset( $user_info['userid'] ) ? $user_info['userid'] : 0;
		$data['shift'] = isset( $data['shift'] ) ? intval( $data['shift'] ) : 0;
		$data['date_added'] = NV_CURRENTTIME;
		$data['date_modified'] = 0;
		
		if( $data['token'] == md5( $nv_Request->session_id . $global_config['sitekey'] . $data['calendar_id'] ) )
		{
			try
			{

				$calendar_id = $db->query('SELECT calendar_id FROM ' . TABLE_APPOINTMENT_NAME . '_calendar WHERE date_start=' . intval( $data['date_start'] ) . ' AND userid='. intval( $data['userid'] ) )->fetchColumn();

				$check_calender = $db->query('SELECT COUNT(*) FROM ' . TABLE_APPOINTMENT_NAME . '_calendar WHERE date_start=' . intval( $data['date_start'] ) . ' AND userid='. intval( $data['userid'] ) . ' AND shift = ' . $data['shift'])->fetchColumn();

				
				
				



				
				if( $data['calendar_id'] == 0 && intval( $calendar_id ) == 0 )
				{
					$stmt = $db->prepare( 'INSERT INTO ' . TABLE_APPOINTMENT_NAME . '_calendar SET 
						date_start=' . intval( $data['date_start'] ) . ',
						userid=' . intval( $data['userid'] ) . ',
						shift=' . intval( $data['shift'] ) . ',
						date_added=' . intval( $data['date_added'] ) . ',
						date_modified=' . intval( $data['date_modified'] ) );

					$stmt->execute();
					$json[] = ['status'=>'OK', 'text'=>'Đăng ký ca ' . $data['shift'] .' ngày ' . $data['date_start'] . ' thành công'];
					if( $data['calendar_id'] = $db->lastInsertId() )
					{
						nv_insert_logs( NV_LANG_DATA, $module_name, $lang_module['calendar_insert'], 'calendar_id: ' . $data['calendar_id'], $data['userid'] );
						
						++$success;
					}
					else
					{
						$error['warning'] = $lang_module['calendar_error_save'];

					}
					
					
				}
				else
				{
					if( $calendar_id > 0)
					{
						$data['calendar_id'] = intval( $calendar_id );
					}
					if($data['shift']!=0){
						if($check_calender == 1){
							$db->query('DELETE FROM ' . TABLE_APPOINTMENT_NAME . '_calendar WHERE date_start=' . intval( $data['date_start'] ) . ' AND userid='. intval( $data['userid'] ) . ' AND shift = ' . $data['shift']);
							$json[] = ['status'=>'OK', 'text'=>'Xóa ca ' . $data['shift'] .' ngày ' . $data['date_start'] . ' thành công'];
						}else{
							$data['date_modified'] = NV_CURRENTTIME;
							$stmt=$db->prepare( 'INSERT INTO ' . TABLE_APPOINTMENT_NAME . '_calendar SET 
								date_start=' . intval( $data['date_start'] ) . ',
								userid=' . intval( $data['userid'] ) . ',
								shift=' . intval( $data['shift'] ) . ',
								date_added=' . intval( $data['date_added'] ) . ',
								date_modified=' . intval($data['date_modified']));
							$stmt->execute();
							$stmt->closeCursor();
							$json[] = ['status'=>'OK', 'text'=>'Đăng ký ca ' . $data['shift'] .' ngày ' . $data['date_start'] . ' thành công'];
						// if( $stmt->execute() )
						// {
						// 	nv_insert_logs( NV_LANG_DATA, $module_name, $lang_module['calendar_update'], 'calendar_id: ' . $data['calendar_id'], $data['userid'] );

						// 	++$success;
						// }
						// else
						// {
						// 	$error['warning'] = $lang_module['calendar_error_save'];

						// }
						}
					}	
				}
			}
			catch ( PDOException $e )
			{
				var_dump($e);
				die();
				$error['warning'] = $lang_module['calendar_error_save'];
				//var_dump($e);
			}

		}

	}


	$json['getCalendar'] = $getCalendar;
	$json['error'] = $error;
	$json['success'] = $success;

	nv_jsonOutput( $json );
}

if($mod=='register_medical'){

	$data['customer_full_name'] = nv_substr( $nv_Request->get_title( 'booking_name', 'post', '', '' ), 0, 250 );
	$data['customer_phone'] = nv_substr( $nv_Request->get_title( 'booking_phone', 'post', '', '' ), 0, 250 );
	$data['customer_email'] = nv_substr( $nv_Request->get_title( 'booking_email', 'post', '', '' ), 0, 250 );

	$data['customer_message'] = $nv_Request->get_textarea( 'booking_message', '', 'br', 1 );
	$data['service_id'] = $nv_Request->get_typed_array( 'booking_service', 'post', 'int', array() );
	$data['customer_date_booking'] = nv_substr( $nv_Request->get_title( 'booking_date', 'post', '', '' ), 0, 10 );
	$data['customer_time_set'] = nv_substr( $nv_Request->get_title( 'booking_hour', 'post', '', '' ), 0, 10 );

	$data['branch_id'] = 1;
	$data['doctors_id'] = 1;
	$data['password'] = $crypt->hash_password($data['customer_phone'], $global_config['hashprefix']);
	$data['md5username'] = nv_md5safe($data['customer_phone']);

	$data['sms_result'] ='';
	$full_name = explode(' ', $data['customer_full_name']);
	$data['patient_group'] = 6;
	if($data['customer_email'] == ''){
		$data['customer_email'] = $data['customer_phone'] . '@gmail.com';
	}

	$ii = 1;

	foreach ($full_name as $key => $value) {
		if($ii<count($full_name)){
			$data['last_name'] .= $value . ' ';
		}else{
			$data['first_name'] .= $value;
		}
		$ii ++;
	}

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
	if( empty( $data['customer_full_name'] ) ) $error['customer_full_name'] = $lang_module['appointment_error_customer_full_name'];
	if( empty( $data['customer_phone'] ) ) $error['customer_phone'] = $lang_module['appointment_error_customer_phone'];
	// if( empty( $data['customer_email'] ) ) $error['customer_email'] = $lang_module['appointment_error_customer_email'];
	if( empty( $data['customer_date_booking'] ) ) $error['customer_date_booking'] = $lang_module['appointment_error_customer_date_booking'];
	if( empty( $data['customer_time_set'] ) ) $error['customer_time_set'] = $lang_module['appointment_error_customer_time_set'];
	if( empty( $data['service_id'] ) ) $error['service_id'] = $lang_module['appointment_error_service'];


	if( empty( $error ) )
	{
		
		$service_id = ( ! empty( $data['service_id'] ) ) ? implode( ',', $data['service_id'] ) : '';
		try
		{
			$user_id = $getUserid;

			if($getUserid == 0){
				$check_user = $db->query('SELECT count(*) FROM ' . $db_config['prefix'] . '_users WHERE phone = "' .$data['customer_phone'] . '"')->fetchColumn();
				$info_user_register = $db->query('SELECT * FROM ' . $db_config['prefix'] . '_users WHERE phone = "' .$data['customer_phone'] . '"')->fetch();
				if($check_user == 1){

					$user_id = $info_user_register['userid'];

				}else{
					$data['sig'] = '';
					$data['in_groups_default'] = 4;
					$data['in_groups'] = 4;
					$data['view_mail']= 0;
					$data['is_email_verified']= -1;
					$data['address'] = '';
					$data['gender'] = 'M';
					$data['service_package_id'] = 0;
					$birthday = NV_CURRENTTIME;

					$sql = "INSERT INTO " . $db_config['prefix'] . "_users (
					group_id, username,service_package_id, md5username, password, email, phone, address, first_name, last_name, gender, birthday, sig, regdate,
					question, answer, passlostkey, view_mail,
					remember, in_groups, active, checknum, last_login, last_ip, last_agent, last_openid, email_verification_time,
					active_obj
					) VALUES (
					" . $data['in_groups_default'] . ",
					'" . $data['customer_phone'] . "',
					" . intval( $data['service_package_id'] ) . ",
					'" . $data['md5username'] . "',
					'" . $data['password'] . "',
					'" . $data['customer_email'] . "', 
					'" . $data['customer_phone'] . "', 
					'" . $data['address'] . "', 
					'" . $data['first_name'] . "', 
					'" . $data['last_name'] . "', 
					'" . $data['gender'] . "', 
					" . intval($birthday) . ",
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

				$db->query( 'INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_patient(userid,patient_group) VALUES('.$userid. ',' . $data['patient_group'] . ')' );
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
		
		$content1 .= '<div>Thời gian khám bệnh: ' . date('d/m/Y - H:i:s',$data['customer_date_booking']) . '</div>';
		$content1 .= '<div>Tên bệnh nhân: ' . $data['customer_full_name'] . '</div>';
		$content1 .= '<div>Ghi chú: ' . $data['customer_message'] . '</div>';
		$content1 .= '<div>Số điện thoại: ' . $data['customer_phone'] . '</div>';
		$email_title = 'Thông báo bệnh nhân đăng ký khám bệnh';
		$a = nv_sendmail(array($global_config['site_name'], $config_email['sender_email']['config_value']), $global_config['site_email'], sprintf($email_title, $module_info['custom_title'], $order_code), $content1);






		if( $data['appointment_id'] = $db->lastInsertId() )
		{


			$json['success'] = $lang_module['thankyou'];
		}
	}
	catch ( PDOException $e )
	{
		$error['save'] = $lang_module['appointment_error_save'];
		// var_dump( $e ); die();
	}	
}

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
	for ($l=1; $l <= 5 ; $l++) {
		if($l==1){
			$list_time_begin = $list_time_begin;
		}else{
			$list_time_begin = $list_time_begin + $time_space;
		}
		$list_time[] = date('H:i',$list_time_begin);

	}
	$list_time_begin = convertToTimeStamp($beginday1, 0, 14, 0, 0);
	for ($ll=1; $ll <= 5 ; $ll++) {
		if($ll==1){
			$list_time_begin = $list_time_begin;
		}else{
			$list_time_begin = $list_time_begin + $time_space;
		}
		$list_time[] = date('H:i',$list_time_begin);
	}

	$list_time_begin = convertToTimeStamp($beginday1, 0, 18, 0, 0);
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
					for ($i=1; $i <= $value1['shift'] + 4 ; $i++) {
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
					$time_slot_begin = convertToTimeStamp($beginday1, 0, 14, 0, 0);
					$time_slot = $time_slot_begin;
					for ($i=1; $i <= $value1['shift'] + 4 ; $i++) {
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
					$time_slot_begin = convertToTimeStamp($beginday1, 0, 18, 0, 0);
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
			$time_afternoon = convertToTimeStamp($beginday1, 0, 18, 0, 0);
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