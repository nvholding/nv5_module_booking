<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2013 Webdep24.com. All rights reserved
 * @Blog  http://dangdinhtu.com
 * @License GNU/GPL version 2 or any later version
 * @Createdate  Wed, 21 Jan 2015 14:00:59 GMT
 */

 
if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );
$mod= $nv_Request->get_string('mod', 'get', '');
if($mod=='send_email'){
	$email=$nv_Request->get_string('email', 'post', '');
	$id=$nv_Request->get_string('id', 'post', '');

	$info = $db->query('SELECT * FROM ' . TABLE_APPOINTMENT_NAME . '_appointment WHERE appointment_id = ' . $id)->fetch();
	$info_bacsi = $db->query('SELECT * FROM ' . NV_USERS_GLOBALTABLE . ' WHERE userid = ' . $info['doctors_id'])->fetch();
	$info['customer_date_booking'] = date('d/m/Y - H:i:s',$info['customer_date_booking']);
	


	$content_file = NV_ROOTDIR . '/' . NV_DATADIR . '/' . NV_LANG_DATA . '_' . $module_data . '_order_content.txt';
	if (file_exists($content_file)) {
		$content = file_get_contents($content_file);
		$content = nv_editor_br2nl($content);
	} else {

		$content .= '<div>Thời gian trị liệu: ' . $info['customer_date_booking'] . '</div>';
		$content .= '<div>Tên khách hàng: ' . $info['customer_full_name'] . '</div>';
		$content .= '<div>Ghi chú: ' . $info['customer_message'] . '</div>';
		$content .= '<div>Số điện thoại: ' . $info['customer_phone'] . '</div>';
		$content .= '<div>Thầy trị liệu: ' . $info_bacsi['first_name'] . ' ' . $info_bacsi['last_name'] . '</div>';
	}

	$email_contents = call_user_func($content, $data_order, $data_pro);
	$email_title = 'Thông báo lịch trị liêu';
	nv_sendmail(array($global_config['site_name'], $config_email['sender_email']['config_value']), $email, sprintf($email_title, $module_info['custom_title'], $order_code), $content);
	$json[] = ['status'=>'OK', 'text'=>'Gửi email thành công!'];
	print_r(json_encode($json[0]));die(); 
}

$page_title = $lang_module['appointment'];
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
if( ACTION_METHOD == 'show_appointment' )
{
	$json = array();

	$appointment_id = $nv_Request->get_int( 'appointment_id', 'post', 0 );

	$token = $nv_Request->get_title( 'token', 'post', '' );
	$json['info'] = '';
	$json['hoten'] = '';
	if( $token == md5( $nv_Request->session_id . $global_config['sitekey'] . $appointment_id ) )
	{
		$xtpl = new XTemplate( 'appointment_info.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
		$xtpl->assign( 'LANG', $lang_module );
		$xtpl->assign( 'NV_LANG_VARIABLE', NV_LANG_VARIABLE );
		$xtpl->assign( 'NV_LANG_DATA', NV_LANG_DATA );
		$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
		$xtpl->assign( 'THEME', $global_config['site_theme'] );
		$xtpl->assign( 'NV_BASE_ADMINURL', NV_BASE_ADMINURL );
		$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
		$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
		$xtpl->assign( 'OP', $op );
		$xtpl->assign( 'MODULE_FILE', $module_file );
		$xtpl->assign( 'MODULE_NAME', $module_name );
		$xtpl->assign( 'TOKEN', md5( $nv_Request->session_id . $global_config['sitekey'] ) );

		$dataContent = $db->query( 'SELECT * FROM ' . TABLE_APPOINTMENT_NAME . '_appointment WHERE appointment_id=' . intval( $appointment_id ) )->fetch();
		
		if( ! nv_is_url ( $dataContent['avatar'] ) )
		{
			$dataContent['avatar'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $dataContent['avatar'];		
		}
		
		$dataContent['customer_date_booking'] = !empty( $dataContent['customer_date_booking'] ) ? date( 'd/m/Y H:i', $dataContent['customer_date_booking'] ) : '';


		
		$dataContent['service_id'] = ( !empty( $dataContent['service_id'] ) ) ? array_map('intval', explode( ',', $dataContent['service_id'] ) ) : array( );
		$dataContent['service_name'] = '';
		if( !empty(  $dataContent['service_id'] ) )
		{
			$getService = getService( $module_name );
			$service = array();
			foreach( $dataContent['service_id'] as $service_id )
			{
				if( isset( $getService[$service_id] ) )
				{
					$service[] = $getService[$service_id]['service_name'];
				}
			}

			$dataContent['service_name'] = implode( '<br>', $service );

		}

		$xtpl->assign( 'DATA', $dataContent );



		$xtpl->parse( 'main' );
		$json['info'] = $xtpl->text( 'main' );
		$json['customer_full_name'] = $dataContent['customer_full_name'];

	}
	else
	{
		$json['error'] = $lang_module['appointment_error_security'];
	}

	nv_jsonOutput( $json );
}
else if( ACTION_METHOD == 'delete' )
{
	$json = array();

	$appointment_id = $nv_Request->get_int( 'appointment_id', 'post', 0 );

	$token = $nv_Request->get_title( 'token', 'post', '', 1 );

	$listid = $nv_Request->get_string( 'listid', 'post', '' );

	if( $listid != '' and md5( $nv_Request->session_id . $global_config['sitekey'] ) == $token )
	{
		$del_array = array_map( 'intval', explode( ',', $listid ) );
	}
	elseif( $token == md5( $nv_Request->session_id . $global_config['sitekey'] . $appointment_id ) )
	{
		$del_array = array( $appointment_id );
	}

	if( ! empty( $del_array ) )
	{

		$_del_array = array();

		$a = 0;
		foreach( $del_array as $appointment_id )
		{
			$result = $db->query( 'DELETE FROM ' . TABLE_APPOINTMENT_NAME . '_appointment WHERE appointment_id = ' . ( int )$appointment_id );
			if( $result->rowCount() )
			{
				$json['id'][$a] = $appointment_id;
				$_del_array[] = $appointment_id;
				++$a;
			}
		}
		$count = sizeof( $_del_array );

		if( $count )
		{
			nv_insert_logs( NV_LANG_DATA, $module_name, 'log_del_appointment', implode( ', ', $_del_array ), $admin_info['userid'] );

			$nv_Cache->delMod( $module_name );

			$json['success'] = $lang_module['appointment_delete_success'];
		}

	}
	else
	{
		$json['error'] = $lang_module['appointment_error_security'];
	}

	nv_jsonOutput( $json );
}
else if( ACTION_METHOD == 'email' )
{
	$json = array();

	$appointment_id = $nv_Request->get_int( 'appointment_id', 'post', 0 );
	$new_vid = $nv_Request->get_int( 'new_vid', 'post', 0 );

	$token = $nv_Request->get_title( 'token', 'post', '', 1 ); 


	if( $token == md5( $nv_Request->session_id . $global_config['sitekey'] . $appointment_id ) )
	{
		$sql = 'UPDATE ' . TABLE_APPOINTMENT_NAME . '_appointment SET is_send_email=' . $new_vid . ' WHERE appointment_id=' . $appointment_id;
		if( $db->exec( $sql ) )
		{
			nv_insert_logs( NV_LANG_DATA, $module_name, 'log_change_email_appointment', 'appointment_id:' . $appointment_id, $admin_info['userid'] );

			$nv_Cache->delMod($module_name);

			$json['success'] = $lang_module['appointment_change_success'];

		}
		else
		{
			$json['error'] = $lang_module['appointment_error_email'];

		}
	}
	else
	{
		$json['error'] = $lang_module['appointment_error_security'];
	}

	nv_jsonOutput( $json );
}
else if( ACTION_METHOD == 'sms' )
{
	$json = array();

	$appointment_id = $nv_Request->get_int( 'appointment_id', 'post', 0 );
	$new_vid = $nv_Request->get_int( 'new_vid', 'post', 0 );

	$token = $nv_Request->get_title( 'token', 'post', '', 1 );


	if( $token == md5( $nv_Request->session_id . $global_config['sitekey'] . $appointment_id ) )
	{
		
		// gửi sms
		$sms = sent_sms_nhac_kham($appointment_id);
		
		if(empty($sms['error']))
		{
			$sql = 'UPDATE ' . TABLE_APPOINTMENT_NAME . '_appointment SET is_send_sms=' . $new_vid . ', sms_result=\'\' WHERE appointment_id=' . $appointment_id;
			if( $db->exec( $sql ) )
			{
				nv_insert_logs( NV_LANG_DATA, $module_name, 'log_change_sms_appointment', 'appointment_id:' . $appointment_id, $admin_info['userid'] );

				$nv_Cache->delMod($module_name);

				$json['success'] = $lang_module['appointment_sms_success'];

			}
		}
		else
		{
			$json['error'] = $lang_module['appointment_error_sms'];

		}
		
		history_sms($appointment_id);
	}
	else
	{
		$json['error'] = $lang_module['appointment_error_security'];
	}

	nv_jsonOutput( $json );
}
elseif( ACTION_METHOD == 'add' || ACTION_METHOD == 'edit' )
{

	
	
	$data = array(
		'appointment_id' => 0,
		'sms_result' => '',
		'customer_full_name' => '',
		'customer_phone' => '',
		'customer_email' => '',
		'customer_message' => '',
		'customer_time_set' => '',
		'customer_date_booking' => NV_CURRENTTIME,
		'service_id' => array(),
		'branch_id' => 0,
		'doctors_id' => 0,
		'userid' => 0,
		'date_added' => 0,
		'date_modified' => 0 );

	$error = array();

	$data['appointment_id'] = $nv_Request->get_int( 'appointment_id', 'get,post', 0 );
	
	$data['userid'] = $nv_Request->get_int( 'userid', 'get,post', 0 );
	
	if($data['userid'])
	{
		$data_user = $db->query( 'SELECT p.full_name, p.phone, u.email
			FROM ' . TABLE_APPOINTMENT_NAME . '_patient p, ' . NV_USERS_GLOBALTABLE .' u WHERE p.userid = u.userid AND p.userid=' . $data['userid'] )->fetch();
			
		$data['customer_full_name'] = $data_user['full_name'];
		$data['customer_phone'] = $data_user['phone'];
		$data['customer_email'] = $data_user['email'];
		
	}
	
	if( $data['appointment_id'] > 0 )
	{
		$data = $db->query( 'SELECT *
			FROM ' . TABLE_APPOINTMENT_NAME . '_appointment  
			WHERE appointment_id=' . $data['appointment_id'] )->fetch();
		
		
		$data['service_id'] = ( ! empty( $data['service_id'] ) ) ? explode( ',', $data['service_id'] ) : array();
		
		$data['customer_time_set'] = date('H:i', $data['customer_date_booking'] );
		
		$caption = $lang_module['appointment_edit'];
	}
	else
	{
		$caption = $lang_module['appointment_add'];
	}

	if( $nv_Request->get_int( 'save', 'post' ) == 1 )
	{
		$data['send_mail_patient'] = $nv_Request->get_int( 'send_mail_patient', 'post', 0 );
		$data['send_mail_doctor'] = $nv_Request->get_int( 'send_mail_doctor', 'post', 0 );
		
		
		
		$data['branch_id'] = $nv_Request->get_int( 'branch_id', 'post', 0 );
		$data['doctors_id'] = $nv_Request->get_int( 'doctors_id', 'post', 0 );
		
		$data['customer_full_name'] = nv_substr( $nv_Request->get_title( 'customer_full_name', 'post', '', '' ), 0, 250 );
		$data['customer_phone'] = trim( nv_substr( $nv_Request->get_title( 'customer_phone', 'post', '', '' ), 0, 250 ) );
		$data['customer_email'] = nv_substr( $nv_Request->get_title( 'customer_email', 'post', '', '' ), 0, 250 );
		
		if($data['customer_email'] == ''){
			$data['customer_email'] = $data['customer_phone'] . '@gmail.com';
		}
		
		$data['userid'] = $db->query( 'SELECT userid FROM ' . NV_USERS_GLOBALTABLE . ' WHERE username=' . $db->quote( $data['customer_phone'] ) )->fetchColumn();
		
		$check_email = $db->query('SELECT userid FROM ' . $db_config['prefix'] . '_users WHERE email = "' .$data['customer_email'] . '"')->fetchColumn();
		
		
		if($check_email)
		{
			$data['userid'] = $check_email;
		}
		
				
		
		if(!$data['userid']){
					
				$data['password'] = $crypt->hash_password($data['customer_phone'], $global_config['hashprefix']);
				$data['md5username'] = nv_md5safe($data['customer_phone']);

				
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

				$data['userid'] = $userid;
		}
		
		// kiểm tra có tài khoản khách hàng chưa
		if($data['userid'])
		{
				$check_kh = $db->query('SELECT userid FROM ' . NV_PREFIXLANG . '_' . $module_data . '_patient WHERE userid =' .$data['userid'])->fetchColumn();
				
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
					
					

					$db->query( 'INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_patient(userid,patient_group,full_name,confess,phone,service_package_id,gender,birthday,address,patient_code,date_added,branch) VALUES('.$data['userid']. ',' . $data['patient_group'] . ', "' . $data['customer_full_name'] . '", "Quý khách","' . $data['customer_phone'] . '", ' . $data['service_package_id'] . ', "' . $data['gender'] . '",' . $birthday . ', "' . $data['address'] . '", "' . $patient_code . '", ' . NV_CURRENTTIME . ', ' . $data['branch_id'] . ' )' );
					
				}
		}
		
		
		$data['customer_message'] = $nv_Request->get_textarea( 'customer_message', '', 'br', 1 );
		$data['service_id'] = $nv_Request->get_typed_array( 'service_id', 'post', 'int', array() );
		$data['customer_date_booking'] = nv_substr( $nv_Request->get_title( 'customer_date_booking', 'post', '', '' ), 0, 10 );
		$data['customer_time_set'] = nv_substr( $nv_Request->get_title( 'customer_time_set', 'post', '', '' ), 0, 10 );
		
		
		
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
		
		//print_r(intval( $data['customer_date_booking'] ));die;

		if( empty( $data['customer_full_name'] ) ) $error['customer_full_name'] = $lang_module['appointment_error_customer_full_name'];
		if( empty( $data['customer_phone'] ) ) $error['customer_phone'] = $lang_module['appointment_error_customer_phone'];
		if( empty( $data['customer_email'] ) ) $error['customer_email'] = $lang_module['appointment_error_customer_email'];
		if( empty( $data['customer_date_booking'] ) ) $error['customer_date_booking'] = $lang_module['appointment_error_customer_date_booking'];
		if( empty( $data['customer_time_set'] ) ) $error['customer_time_set'] = $lang_module['appointment_error_customer_time_set'];
		if( empty( $data['service_id'] ) ) $error['service_id'] = $lang_module['appointment_error_service'];
		if( empty( $data['userid'] ) ) $error['userid'] = $lang_module['appointment_error_userid'];
		if( empty( $data['branch_id'] ) ) $error['branch_id'] = $lang_module['appointment_error_branch_id'];
		//if( empty( $data['doctors_id'] ) ) $error['doctors_id'] = $lang_module['appointment_error_doctors_id'];

		if( ! empty( $error ) && ! isset( $error['warning'] ) )
		{
			$error['warning'] = $lang_module['appointment_error_warning'];
		}

		if( empty( $error ) )
		{
			$service_id = ( ! empty( $data['service_id'] ) ) ? implode( ',', $data['service_id'] ) : '';
			try
			{
				if( $data['appointment_id'] == 0 )
				{

					$stmt = $db->prepare( 'INSERT INTO ' . TABLE_APPOINTMENT_NAME . '_appointment SET 
						sms_result=:sms_result,
						customer_full_name=:customer_full_name,
						customer_email=:customer_email,
						customer_phone=:customer_phone,
						customer_message=:customer_message,
						service_id=:service_id,
						customer_date_booking=' . intval( $data['customer_date_booking'] ) . ',
						branch_id=' . intval( $data['branch_id'] ) . ',
						doctors_id=' . intval( $data['doctors_id'] ) . ',
						userid=' . intval( $data['userid'] ) . ',
						date_added=' . intval( NV_CURRENTTIME ) );

					$stmt->bindParam( ':sms_result', $data['sms_result'], PDO::PARAM_STR );
					$stmt->bindParam( ':customer_full_name', $data['customer_full_name'], PDO::PARAM_STR );
					$stmt->bindParam( ':customer_email', $data['customer_email'], PDO::PARAM_STR );
					$stmt->bindParam( ':customer_phone', $data['customer_phone'], PDO::PARAM_STR );
					$stmt->bindParam( ':customer_message', $data['customer_message'], PDO::PARAM_STR );
					$stmt->bindParam( ':service_id', $service_id, PDO::PARAM_STR );

					$stmt->execute();

					if( $data['appointment_id'] = $db->lastInsertId() )
					{
						nv_insert_logs( NV_LANG_DATA, $module_name, $lang_module['appointment_add'], 'appointment_id: ' . $data['appointment_id'], $admin_info['userid'] );

						
						$nv_Request->set_Session( $module_data . '_success', $lang_module['appointment_add_success'] );

						$nv_Cache->delMod( $module_name );
					}
					else
					{
						$error['warning'] = $lang_module['appointment_error_save'];

					}
					$stmt->closeCursor();



				}
				else
				{


					$stmt = $db->prepare( 'UPDATE ' . TABLE_APPOINTMENT_NAME . '_appointment SET 
						sms_result=:sms_result,
						customer_full_name=:customer_full_name,
						customer_email=:customer_email,
						customer_phone=:customer_phone,
						customer_message=:customer_message,
						service_id=:service_id,
						customer_date_booking=' . intval( $data['customer_date_booking'] ) . ',
						branch_id=' . intval( $data['branch_id'] ) . ',
						doctors_id=' . intval( $data['doctors_id'] ) . ',
						userid=' . intval( $data['userid'] ) . ',				
						date_modified=' . intval( NV_CURRENTTIME ) . '
						WHERE appointment_id=' . $data['appointment_id'] );

					$stmt->bindParam( ':sms_result', $data['sms_result'], PDO::PARAM_STR );
					$stmt->bindParam( ':customer_full_name', $data['customer_full_name'], PDO::PARAM_STR );
					$stmt->bindParam( ':customer_email', $data['customer_email'], PDO::PARAM_STR );
					$stmt->bindParam( ':customer_phone', $data['customer_phone'], PDO::PARAM_STR );
					$stmt->bindParam( ':customer_message', $data['customer_message'], PDO::PARAM_STR );
					$stmt->bindParam( ':service_id', $service_id, PDO::PARAM_STR );

					if( $stmt->execute() )
					{
						nv_insert_logs( NV_LANG_DATA, $module_name, $lang_module['appointment_edit'], 'appointment_id: ' . $data['appointment_id'], $admin_info['userid'] );

						$nv_Request->set_Session( $module_data . '_success', $lang_module['appointment_edit_success'] );

						$nv_Cache->delMod( $module_name );

					}
					else
					{
						$error['warning'] = $lang_module['appointment_error_save'];

					}

					$stmt->closeCursor();


				}
				
				
				
						$info_doctor = $db->query('SELECT * FROM ' . NV_USERS_GLOBALTABLE . ' WHERE userid = ' . $data['doctors_id'])->fetch();
						
						// LẤY xưng hô $user_id
						$confess = $db->query('SELECT confess FROM ' .  NV_PREFIXLANG . '_' . $module_data . '_patient WHERE userid ='. $data['userid'])->fetchColumn();
						if(empty($confess))
							$confess = 'Quý khách';
						
						
						$arr_name = explode(' ', $data['customer_full_name']);
						$customer_name = end($arr_name);
						
						$data['customer_time_set'] = str_replace(' ','',$data['customer_time_set']);

						if(true){
							
							$lienhe ='<br />Thông tin liên hệ:<br />
CƠ SỞ TRỊ LIỆU CƠ XƯƠNG KHỚP THIỆN NHÂN<br />
Địa chỉ: 112/36 Tây Hòa , Phước Long A, Tp Thủ Đức, Tp Hồ Chí Minh<br />
Chi nhánh Tân Bình: 277 Hoàng Văn Thụ, Phường 2, Tân Bình, Thành phố Hồ Chí Minh<br />
Chi nhánh Quận 9: 112/36 Tây Hòa, Phước Long A, Quận 9, Thành phố Hồ Chí Minh<br />
Điện thoại: 028.9999 44 55 - 0336 044 055<br />
Email: <a href="mailto:thiennhan.tdcs@gmail.com" target="_blank">thiennhan.tdcs@gmail.com</a>';

$email_title = 'CƠ XƯƠNG KHỚP THIỆN NHÂN - [BÁO LỊCH HẸN]';


							$content_customer = 'Cảm ơn '. $confess .' ' . $customer_name . ' đã đặt lịch hẹn trị liệu tại Cơ Xương Khớp Thiện Nhân lúc ' . $data['customer_time_set'] . ', ngày ' . date('d/m/Y',$data['customer_date_booking']) . ', Vui lòng đến trước 10 phút để nghỉ ngơi, ổn định huyết áp trước khi trị liệu. Trân trọng cảm ơn!';
							$content_customer .= $lienhe;
			
							
							
							$a = nv_sendmail(array($global_config['site_name'], $config_email['sender_email']['config_value']), $data['customer_email'], sprintf($email_title, $module_info['custom_title'], $order_code), $content_customer);
						}

						if(true){
							
							if($data['branch_id'])
							{
								$chinhanh = $db->query('SELECT title FROM ' .  NV_PREFIXLANG . '_' . $module_data . '_branch WHERE branch_id ='. $data['branch_id'])->fetchColumn();
							}
							
							// LẤY MÃ KH $user_id
							$patient_code = $db->query('SELECT patient_code FROM ' .  NV_PREFIXLANG . '_' . $module_data . '_patient WHERE userid ='. $data['userid'])->fetchColumn();
							
							$content1 .= '<div>Thời gian trị liệu: ' . date('d/m/Y - H:i:s',$data['customer_date_booking']) . '</div>';
							$content1 .= '<div>Tên khách hàng: ' . $data['customer_full_name'] . '</div>';
							$content1 .= '<div>Mã KH: ' . $patient_code . '</div>';
							$content1 .= '<div>Số điện thoại: ' . $data['customer_phone'] . '</div>';
							$content1 .= '<div>Ghi chú: ' . $data['customer_message'] . '</div>';
							$content1 .= '<div>Chi nhánh: ' . $chinhanh . '</div>';
							
							$content1 .= $lienhe;
							
							$a = nv_sendmail(array($global_config['site_name'], $config_email['sender_email']['config_value']), $global_config['site_email'], sprintf($email_title, $module_info['custom_title'], $order_code), $content1);
						}
						
						
			}
			catch ( PDOException $e )
			{
				$error['warning'] = $lang_module['appointment_error_save'];
				// var_dump( $e ); die();
			}
		}

		if( empty( $error ) )
		{

			Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
			die();
		}

	}
	
	
	// $data['customer_time_set'] = !empty( $data['customer_date_booking'] ) ? date('H:i', $data['customer_date_booking']) : '';
	$data['customer_date_booking'] = !empty( $data['customer_date_booking'] ) ? date('d/m/Y', $data['customer_date_booking']) : '';

	$xtpl = new XTemplate( 'appointment_add.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'NV_LANG_VARIABLE', NV_LANG_VARIABLE );
	$xtpl->assign( 'NV_LANG_DATA', NV_LANG_DATA );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'THEME', $global_config['site_theme'] );
	$xtpl->assign( 'NV_BASE_ADMINURL', NV_BASE_ADMINURL );
	$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
	$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
	$xtpl->assign( 'MODULE_FILE', $module_file );
	$xtpl->assign( 'MODULE_NAME', $module_name );
	$xtpl->assign( 'OP', $op );
	$xtpl->assign( 'CAPTION', $caption );
	$xtpl->assign( 'DATA', $data );
	$xtpl->assign( 'CANCEL', NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=" . $op );
	$xtpl->assign( 'TOKEN', md5( $client_info['session_id'] . $global_config['sitekey'] ) );

	$xtpl->assign( 'UPLOADDIR', NV_UPLOADS_DIR . '/' . $module_upload );
	$xtpl->assign( 'CURRENTPATH', NV_UPLOADS_DIR . '/' . $module_upload );
	$xtpl->assign( 'BUTTON_SUBMIT', ( $data['appointment_id'] == 0 ) ? $lang_module['appointment_create'] : $lang_module['appointment_update'] );
	
	
	if( $data['branch_id'] > 0 )
	{
		$getBranch = getBranch( $module_name );

		if( $getBranch)
		{
			foreach( $getBranch as $key => $item )
			{
				$xtpl->assign( 'BRANCH', array(
					'key' => $key,
					'name' => $item['title'],
					'selected' => ( $key == $data['branch_id'] ) ? 'selected="selected"' : ''));
				$xtpl->parse( 'main.branch' );

			}
		}
		
	
		if($data['doctors_id']){
			$doctors= $db->query( 'SELECT userid, CONCAT(last_name,\' \', first_name) AS full_name, username FROM ' . NV_USERS_GLOBALTABLE . ' WHERE userid = '. intval( $data['doctors_id'] ) )->fetch();

			$xtpl->assign( 'DOCTORS', $doctors );
			$xtpl->parse( 'main.doctors' );
		}
		

	}
	
	
	
	$getService = getService( $module_name );

	if( $getService )
	{
		foreach( $getService as $key => $item )
		{
			$xtpl->assign( 'SERVICE', array(
				'key' => $key,
				'name' => $item['service_name'],
				'checked' => ( in_array( $key, $data['service_id'] ) ) ? 'checked="checked"' : '' ) );
			$xtpl->parse( 'main.service' );

		}
	}
	
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
			$list_time[] = date( 'H:i', $date_from );
			
			$date_from = $date_from + ( $getSetting['space_time'] * 60 );

		}
		
	}

	if( $list_time )
	{
		foreach( $list_time as $key => $time )
		{
			$xtpl->assign( 'TIME', $time );
			$xtpl->parse( 'main.time' );
		}
	}
	

	if( $error )
	{
		foreach( $error as $key => $_error )
		{
			$xtpl->assign( 'error_' . $key, $_error );
			$xtpl->parse( 'main.error_' . $key );
		}
	}

	$xtpl->parse( 'main' );
	$contents = $xtpl->text( 'main' );

	include NV_ROOTDIR . '/includes/header.php';
	echo nv_admin_theme( $contents );
	include NV_ROOTDIR . '/includes/footer.php';
}

/*show list appointment*/

$base_url_order = $base_url = NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '='. $op;

$per_page = 100;

$page = $nv_Request->get_int( 'page', 'get', 1 );

$data['customer_full_name'] = trim( $nv_Request->get_string( 'customer_full_name', 'get', '' ) );
$data['customer_email'] = trim( $nv_Request->get_string( 'customer_email', 'get', '' ) );
$data['customer_phone'] = trim( $nv_Request->get_string( 'customer_phone', 'get' ) );
$data['date_from'] = trim( $nv_Request->get_title( 'date_from', 'get', date('d/m/Y') ) );
$data['date_to'] = trim( $nv_Request->get_title( 'date_to', 'get', date('d/m/Y') ) );
$data['service_id'] = $nv_Request->get_int( 'service_id', 'get', 0 );
$data['branch_id'] = $nv_Request->get_int( 'branch_id', 'get', 0 );



if( preg_match( '/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $data['date_from'], $m ) )
{

	$date_from = mktime( 0, 0, 0, $m[2], $m[1], $m[3] );
}
else
{
	$date_from = 0;
}
if( preg_match( '/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $data['date_to'], $m ) )
{

	$date_to = mktime( 23, 59, 59, $m[2], $m[1], $m[3] );
}
else
{
	$date_to = 0;
}

$sql = TABLE_APPOINTMENT_NAME . '_appointment';


$implode = array();

if( $data['customer_full_name'] )
{
	$implode[]= "customer_full_name LIKE '%" . $db->dblikeescape( $data['customer_full_name'] ) . "%'";
	$base_url.= '&amp;customer_full_name=' . $data['customer_full_name'];
	$base_url_order.= '&amp;customer_full_name=' . $data['customer_full_name'];
}
if( $data['customer_phone'] )
{
	$implode[]= "customer_phone LIKE '%" . $db->dblikeescape( $data['customer_phone'] ) . "%'";
	$base_url.= '&amp;customer_phone=' . $data['customer_phone'];
	$base_url_order.= '&amp;customer_phone=' . $data['customer_phone'];
}
if( $data['customer_email'] )
{
	$implode[]= "customer_email LIKE '%" . $db->dblikeescape( $data['customer_email'] ) . "%'";
	$base_url.= '&amp;customer_email=' . $data['customer_email'];
	$base_url_order.= '&amp;customer_email=' . $data['customer_email'];
}

if( $data['branch_id'] )
{
	$implode[]= "branch_id=" .  intval( $data['branch_id'] );
	$base_url.= '&amp;branch_id=' . $data['branch_id'];
	$base_url_order.= '&amp;branch_id=' . $data['branch_id'];
}

if( $data['service_id'] )
{
	$implode[]= "service_id=" .  intval( $data['service_id'] );
	$base_url.= '&amp;service_id=' . $data['service_id'];
	$base_url_order.= '&amp;service_id=' . $data['service_id'];
}
if( $date_from && $date_to )
{
	$implode[] = "(customer_date_booking BETWEEN " . intval( $date_from ) . " AND " . intval( $date_to ) . ")";
}

if( $implode )
{
	$sql .= ' WHERE ' . implode( ' AND ', $implode );
}

$sort = $nv_Request->get_string( 'sort', 'get', '' );

$order = $nv_Request->get_string( 'order', 'get' ) == 'desc' ? 'desc' : 'asc';

$sort_data = array(
	'customer_full_name',
	'customer_email',
	'customer_phone',
	'customer_date_booking',
	'service_id',
	'branch_id',
	'is_send_sms',
	'date_added' );

if( isset( $sort ) && in_array( $sort, $sort_data ) )
{

	$sql .= ' ORDER BY ' . $sort;
}
else
{
	$sql .= ' ORDER BY date_added';
}

if( isset( $order ) && ( $order == 'desc' ) )
{
	$sql .= ' DESC';
}
else
{
	$sql .= ' ASC';
}

$base_url.= '&amp;sort=' . $sort . '&amp;order=' . $order . '&amp;per_page=' . $per_page;


$num_items = $db->query( 'SELECT COUNT(*) FROM ' . $sql )->fetchColumn();

$db->sqlreset()->select( '*' )->from( $sql )->limit( $per_page )->offset( ( $page - 1 ) * $per_page );

$result = $db->query( $db->sql() );

$array = array();
while( $rows = $result->fetch() )
{
	if($rows['doctors_id']){
		$rows['info_doctor'] = $db->query('SELECT * FROM ' . $db_config['prefix'] . '_users WHERE userid = ' . $rows['userid'] )->fetch();
	}else{
		$rows['info_doctor']['last_name'] = 'Chưa chỉ định';
		$rows['info_doctor']['first_name'] = '';
	}
	$rows['info_doctor']['name_doctor'] = $rows['info_doctor']['last_name'] . ' ' . $rows['info_doctor']['first_name'];
	$array[] = $rows;
}


$xtpl = new XTemplate( 'appointment.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
$xtpl->assign( 'LANG', $lang_module );
$xtpl->assign( 'NV_LANG_VARIABLE', NV_LANG_VARIABLE );
$xtpl->assign( 'NV_LANG_DATA', NV_LANG_DATA );
$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
$xtpl->assign( 'THEME', $global_config['site_theme'] );
$xtpl->assign( 'NV_BASE_ADMINURL', NV_BASE_ADMINURL );
$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
$xtpl->assign( 'OP', $op );
$xtpl->assign( 'MODULE_FILE', $module_file );
$xtpl->assign( 'MODULE_NAME', $module_name );
$xtpl->assign( 'TOKEN', md5( $nv_Request->session_id . $global_config['sitekey'] ) );
$xtpl->assign( 'ADD_NEW', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=appointment&action=add' );

$xtpl->assign( 'DATA', $data );

$order2 = ( $order == 'asc' ) ? 'desc' : 'asc';

$xtpl->assign( 'URL_CUSTOMER_FULL_NAME', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=customer_full_name&amp;order=' . $order2 . '&amp;per_page=' . $per_page );
$xtpl->assign( 'URL_CUSTOMER_EMAIL', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=customer_email&amp;order=' . $order2 . '&amp;per_page=' . $per_page );
$xtpl->assign( 'URL_CUSTOMER_PHONE', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=customer_phone&amp;order=' . $order2 . '&amp;per_page=' . $per_page );
$xtpl->assign( 'URL_CUSTOMER_DATE_BOOKING', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=customer_date_booking&amp;order=' . $order2 . '&amp;per_page=' . $per_page );
$xtpl->assign( 'URL_SMS', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=is_send_sms&amp;order=' . $order2 . '&amp;per_page=' . $per_page );
$xtpl->assign( 'URL_STATUS', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=status&amp;order=' . $order2 . '&amp;per_page=' . $per_page );

$xtpl->assign( 'CUSTOMER_FULL_NAME_ORDER', ( $sort == 'customer_full_name' ) ? 'class="' . $order2 . '"' : '' );
$xtpl->assign( 'CUSTOMER_EMAIL_ORDER', ( $sort == 'customer_email' ) ? 'class="' . $order2 . '"' : '' );
$xtpl->assign( 'CUSTOMER_PHONE_ORDER', ( $sort == 'customer_phone' ) ? 'class="' . $order2 . '"' : '' );
$xtpl->assign( 'CUSTOMER_DATE_BOOKING_ORDER', ( $sort == 'customer_date_booking' ) ? 'class="' . $order2 . '"' : '' );
$xtpl->assign( 'SMS_ORDER', ( $sort == 'is_send_sms' ) ? 'class="' . $order2 . '"' : '' );
$xtpl->assign( 'STATUS_ORDER', ( $sort == 'status' ) ? 'class="' . $order2 . '"' : '' );

if( $nv_Request->get_string( $module_data . '_success', 'session' ) )
{
	$xtpl->assign( 'SUCCESS', $nv_Request->get_string( $module_data . '_success', 'session' ) );

	$xtpl->parse( 'main.success' );

	$nv_Request->unset_request( $module_data . '_success', 'session' );

}


$getService = getService();

foreach( $getService as $_lop_id => $item)
{
	$xtpl->assign( 'SERVICE', array( 'key'=> $item['service_id'], 'name'=> $item['service_name'], 'selected'=> ( $item['service_id'] == $data['service_id'] ) ? 'selected="selected"': '' ) );
	$xtpl->parse( 'main.service' );
}

	$getBranch = getBranch( $module_name );
	//print_r($getBranch);die;
	if( $getBranch )
	{
		foreach( $getBranch as $key => $item )
		{
			$xtpl->assign( 'BRANCH', array(
				'key' => $key,
				'name' => $item['title'],
				'selected' => ( $key == $data['branch_id'] ) ? 'selected="selected"' : '' ) );
			$xtpl->parse( 'main.branch' );

		}
	}
	

if( ! empty( $array ) )
{


	foreach( $array as $item )
	{
		$item['info_doctor'] = $db->query('SELECT * FROM ' . NV_USERS_GLOBALTABLE . ' WHERE userid = ' . $item['doctors_id'])->fetch();
		$item['customer_date_booking'] = !empty( $item['customer_date_booking'] ) ? date('H:i d/m/Y l', $item['customer_date_booking']) : '';
		
		
		$item['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $item['appointment_id'] );
		$item['edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&action=edit&token=' . $item['token'] . '&appointment_id=' . $item['appointment_id'];
		$item['is_send_sms_checked'] = ( $item['is_send_sms'] == 1 ) ? 'checked="checked"': '';
		$item['is_send_email_checked'] = ( $item['is_send_email'] == 1 ) ? 'checked="checked"': '';

		$xtpl->assign( 'LOOP', $item );
		
		$xtpl->parse( 'main.loop' );

	}

}

$generate_page = nv_generate_page( $base_url, $num_items, $per_page, $page );
if( ! empty( $generate_page ) )
{
	$xtpl->assign( 'GENERATE_PAGE', $generate_page );
	$xtpl->parse( 'main.generate_page' );
}

$xtpl->parse( 'main' );
$contents = $xtpl->text( 'main' );

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';
