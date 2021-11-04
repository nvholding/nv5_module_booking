<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2013 Webdep24.com. All rights reserved
 * @Blog  http://dangdinhtu.com
 * @License GNU/GPL version 2 or any later version
 * @Createdate  Wed, 21 Jan 2015 14:00:59 GMT
 */


// bác sĩ và admin mới được quyền vào xem danh sách lịch hẹn
if($user_info['group_id'] != $getSetting['default_group_doctors'] and !defined('NV_IS_ADMIN') and $user_info['group_id'] != 1 and $user_info['group_id'] !=2 and $user_info['group_id'] !=3)
{
	nv_redirect_location(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=history');
}
 
if (!defined('NV_IS_USER') or !$global_config['allowuserlogin']) {
        nv_redirect_location(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=home');
}

if( ! defined( 'NV_IS_MOD_APPOINTMENT' ) ) die( 'Stop!!!' );

$page_title = $lang_module['appointment'];
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);



if( $nv_Request->isset_request( 'checkdakham', 'post, get' ) )
{
	$userid = $nv_Request->get_int( 'userid', 'post,get', 0 );
	$appointment_id = $nv_Request->get_int( 'appointment_id', 'post,get', 0 );
	
	$json = array();
	
	

	if( $userid and $appointment_id )
	{
		// kiểm tra userid còn bao nhiêu lượt khám còn lại
		$parent_info = get_parent_info($userid);
		
		$json['parent_info'] = $parent_info;
		
		if($parent_info['kham_conlai'])
		{
			// trừ số lần khám đi
			$so_lan_update = -1;
			update_num_kham($userid, $so_lan_update);
			
			// cập nhật trạng thái lịch hẹn đã khám bệnh cho KH
			if(update_khambenh_kh($userid, $appointment_id))
			$json['success'] = 'ok';	
		}
		else
		{
			$json['error'] = 'Thất bại';
		}
		
	}
	else
	{
		$json['error'] = 'Thất bại';
	}
	
	nv_jsonOutput( $json );die;

}



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
		$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
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
			nv_insert_logs( NV_LANG_DATA, $module_name, 'log_del_appointment', implode( ', ', $_del_array ), $user_info['userid'] );

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
		$sql = 'UPDATE ' . TABLE_APPOINTMENT_NAME . '_appointment SET is_send_sms=' . $new_vid . ', sms_result=\'\' WHERE appointment_id=' . $appointment_id;
		if( $db->exec( $sql ) )
		{
			nv_insert_logs( NV_LANG_DATA, $module_name, 'log_change_sms_appointment', 'appointment_id:' . $appointment_id, $admin_info['userid'] );

			$nv_Cache->delMod($module_name);

			$json['success'] = $lang_module['appointment_sms_success'];

		}
		else
		{
			$json['error'] = $lang_module['appointment_error_sms'];

		}
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

		$data['branch_id'] = $nv_Request->get_int( 'branch_id', 'post', 0 );
		$data['doctors_id'] = $nv_Request->get_int( 'doctors_id', 'post', 0 );
		
		$data['customer_full_name'] = nv_substr( $nv_Request->get_title( 'customer_full_name', 'post', '', '' ), 0, 250 );
		$data['customer_phone'] = trim( nv_substr( $nv_Request->get_title( 'customer_phone', 'post', '', '' ), 0, 250 ) );
		
		$data['userid'] = $db->query( 'SELECT userid FROM ' . NV_USERS_GLOBALTABLE . ' WHERE username=' . $db->quote( $data['customer_phone'] ) )->fetchColumn();
		
		$data['customer_email'] = nv_substr( $nv_Request->get_title( 'customer_email', 'post', '', '' ), 0, 250 );
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
		
		if( empty( $data['customer_full_name'] ) ) $error['customer_full_name'] = $lang_module['appointment_error_customer_full_name'];
		if( empty( $data['customer_phone'] ) ) $error['customer_phone'] = $lang_module['appointment_error_customer_phone'];
		if( empty( $data['customer_email'] ) ) $error['customer_email'] = $lang_module['appointment_error_customer_email'];
		if( empty( $data['customer_date_booking'] ) ) $error['customer_date_booking'] = $lang_module['appointment_error_customer_date_booking'];
		if( empty( $data['customer_time_set'] ) ) $error['customer_time_set'] = $lang_module['appointment_error_customer_time_set'];
		if( empty( $data['service_id'] ) ) $error['service_id'] = $lang_module['appointment_error_service'];
		// if( empty( $data['userid'] ) ) $error['userid'] = $lang_module['appointment_error_userid'];
		if( empty( $data['branch_id'] ) ) $error['branch_id'] = $lang_module['appointment_error_branch_id'];
		if( empty( $data['doctors_id'] ) ) $error['doctors_id'] = $lang_module['appointment_error_doctors_id'];
		
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
			}
			catch ( PDOException $e )
			{
				$error['warning'] = $lang_module['appointment_error_save'];
				// var_dump( $e ); die();
			}
		}

		if( empty( $error ) )
		{

			Header( 'Location: ' . NV_BASE_SITEURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
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
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
	$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
	$xtpl->assign( 'MODULE_FILE', $module_file );
	$xtpl->assign( 'MODULE_NAME', $module_name );
	$xtpl->assign( 'OP', $op );
	$xtpl->assign( 'CAPTION', $caption );
	$xtpl->assign( 'DATA', $data );
	$xtpl->assign( 'CANCEL', NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=" . $op );
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
		
		$doctors= $db->query( 'SELECT userid, CONCAT(last_name,\' \', first_name) AS full_name, username FROM ' . NV_USERS_GLOBALTABLE . ' WHERE userid = '. intval( $data['doctors_id'] ) )->fetch();
		$xtpl->assign( 'DOCTORS', $doctors );
		$xtpl->parse( 'main.doctors' );

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

$base_url_order = $base_url = NV_BASE_SITEURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '='. $op;

$per_page = 50;

$page = $nv_Request->get_int( 'page', 'get', 1 );

$data['customer_full_name'] = trim( $nv_Request->get_string( 'customer_full_name', 'get,post', '' ) );
$data['customer_email'] = trim( $nv_Request->get_string( 'customer_email', 'get,post', '' ) );
$data['customer_phone'] = trim( $nv_Request->get_string( 'customer_phone', 'get,post' ) );

$data['date_from'] = trim( $nv_Request->get_title( 'date_from', 'get,post', date('d/m/Y') ) );
$data['date_to'] = trim( $nv_Request->get_title( 'date_to', 'get,post', date('d/m/Y') ) );

$data['service_id'] = $nv_Request->get_int( 'service_id', 'get,post', 0 );


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

$sql = TABLE_APPOINTMENT_NAME . '_appointment a, ' . TABLE_APPOINTMENT_NAME . '_patient t';


$implode = array();

$implode[] = 'a.userid = t.userid ';

if( $data['customer_full_name'] )
{
	$implode[]= "(customer_full_name LIKE '%" . $db->dblikeescape( $data['customer_full_name'] ) . "%' OR t.note LIKE '%" . $db->dblikeescape( $data['customer_full_name'] ) . "%' OR t.patient_code LIKE '". $db->dblikeescape( $data['customer_full_name'] ) ."')";
	$base_url.= '&amp;customer_full_name=' . $data['customer_full_name'];
}

if($date_from)
{
	$implode[] = "customer_date_booking >= " . intval( $date_from );
	$base_url.= '&amp;date_from=' . $data['date_from'];
}

if($date_to)
{
	$implode[] = "customer_date_booking <= " . intval( $date_to );
	$base_url.= '&amp;date_to=' . $data['date_to'];
}


if($user_info['group_id'] == $getSetting['default_group_doctors'])
{
	// lấy chi nhánh bác sĩ này ra.
	$branch_id = get_branch_id($user_info['userid']);
	
	if($branch_id)
	$implode[] = 'a.branch_id = ' . $branch_id;
}

if( $implode )
{
	$sql .= ' WHERE ' . implode( ' AND ', $implode );
}


$num_items = $db->query( 'SELECT COUNT(*) FROM ' . $sql )->fetchColumn();

$db->sqlreset()->select( '*' )->from( $sql )->limit( $per_page )->offset( ( $page - 1 ) * $per_page );

$result = $db->query( $db->sql() );
//print_r($db->sql());
$array = array();
while( $rows = $result->fetch() )
{
	if($rows['doctors_id']){
		$rows['birthday'] = date('d/m/Y',$db->query('SELECT birthday FROM ' . $db_config['prefix'] . '_users WHERE userid = ' . $rows['doctors_id'] )->fetchColumn());
	}
	
	// lay thong tin ma kham
	$info_kh = get_parent_info($rows['userid']);
	$rows['patient_code'] = $info_kh['patient_code'];
	$array[] = $rows;
}


// kiểm tra bác sĩ hay khách hàng
if($user_info['group_id'] == $getSetting['default_group_doctors'])
{
	$xtpl = new XTemplate( 'appointment.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
}
else
{
	$xtpl = new XTemplate( 'appointment.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
}


$xtpl->assign( 'LANG', $lang_module );
$xtpl->assign( 'NV_LANG_VARIABLE', NV_LANG_VARIABLE );
$xtpl->assign( 'NV_LANG_DATA', NV_LANG_DATA );
$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
$xtpl->assign( 'THEME', $global_config['site_theme'] );
$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
$xtpl->assign( 'OP', $op );
$xtpl->assign( 'MODULE_FILE', $module_file );
$xtpl->assign( 'MODULE_NAME', $module_name );
$xtpl->assign( 'TOKEN', md5( $nv_Request->session_id . $global_config['sitekey'] ) );
$xtpl->assign( 'ADD_NEW', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=appointment&action=add' );

$xtpl->assign( 'DATA', $data );

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

if( ! empty( $array ) )
{
	

	foreach( $array as $item )
	{

		$item['customer_date_booking'] = !empty( $item['customer_date_booking'] ) ? date('H:i d/m/Y', $item['customer_date_booking']) : '';
		$item['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $item['appointment_id'] );
		$item['edit'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&action=edit&token=' . $item['token'] . '&appointment_id=' . $item['appointment_id'];
		
		$item['url_muagoidichvu'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=buy_service&userid=' . $item['userid'];
		
		if($item['userid'])
		$item['username'] = $db->query('SELECT username FROM vidoco_users WHERE userid ='. $item['userid'])->fetchColumn();
	
		$item['link'] = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=appointment-patient/' . $item['appointment_id'], true );
		$item['link'] = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=patient/' . $item['userid'] , true );
		
		
		$item['is_send_sms_checked'] = ( $item['is_send_sms'] == 1 ) ? 'checked="checked"': '';
		$item['is_send_email_checked'] = ( $item['is_send_email'] == 1 ) ? 'checked="checked"': '';
		
		
		if($item['bs_dakham'])
		{
			$item['bs_dakham_title'] = 'Đã khám';
		}
		else
		{
			$item['bs_dakham_title'] = 'Chưa khám';
		}
		
		// lấy thông tin số lần khám còn lại của userid
		$get_parent_info = get_parent_info($item['userid']);
		$item['kham_conlai'] = $get_parent_info['kham_conlai'];
		
		
		
		if(!$item['kham_conlai'])
		{
			$item['kham_conlai'] = 0;
		}
		
		$xtpl->assign( 'LOOP', $item );
		
		
		if(!$item['kham_conlai'])
		{
			$xtpl->parse( 'main.loop.muagoidichvu' );
		}
		elseif(!$item['bs_dakham'])
		{
			$xtpl->parse( 'main.loop.check_dakham' );
		}
		
		if(!$item['bs_dakham'])
		{
			$xtpl->parse( 'main.loop.delete_hen' );
		}
		
		
		
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
echo nv_site_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';
