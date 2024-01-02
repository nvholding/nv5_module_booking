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
if($mod=='get_list_doctor'){
	$q=$nv_Request->get_string('q', 'get','');
	$list = get_list_doctor_select2($q);
	foreach($list as $result){
		$json[] = ['id'=>$result['userid'], 'text'=> $result['first_name'] . ' ' . $result['last_name']];
	}
	print_r(json_encode($json));die(); 
}

if($mod=='load_salary'){
	$doctor_id=$nv_Request->get_int('doctor_id', 'get,post',0);
	$month=$nv_Request->get_string('month', 'get,post','');
	$month_start = '1/' . $month;
	$info_doctor = get_info_doctor($doctor_id);
	$month_start = convertToTimeStamp( $month_start, 0, 0, 0, 0 );
	$lastdate = date("t", $month_start ); 
	$month_end = $lastdate . '/' . $month;
	$month_end = convertToTimeStamp( $month_end, 0, 23, 23, 23 );

	$list_appointment = $db->query('SELECT t2.*, t1.date_added FROM ' . TABLE_APPOINTMENT_NAME . '_patient_appointment t1 INNER JOIN ' . TABLE_APPOINTMENT_NAME . '_patient t2 ON t1.patient_id = t2.userid WHERE t1.doctors_id = ' . $doctor_id . ' AND t1.date_added >= ' . $month_start . ' GROUP BY t1.patient_id')->fetchAll();

	
	$xtpl = new XTemplate( 'salary_load.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );

	if($list_appointment){
		foreach ($list_appointment as $key => $value) {
			$value['customer_date_booking'] = date('d/m/Y - H:i:s',$value['date_added']);
			$xtpl->assign( 'ROW', $value);
			$xtpl->parse( 'main.loop' );
		}
	}else{
		$xtpl->parse( 'main.no_info' );
	}
	

	$xtpl->assign( 'DATA', $info_doctor);
	$xtpl->assign( 'MONTH', $month);
	$xtpl->assign( 'COUNT', count($list_appointment));
	$xtpl->parse( 'main' );
	$contents = $xtpl->text( 'main' );
	$json[] = ['status'=>'OK', 'contents'=>$contents];
	print_r(json_encode($json[0]));die(); 
}

$page_title = $lang_module['salary'];
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
if( ACTION_METHOD == 'delete' )
{
	$json = array();

	$userid = $nv_Request->get_int( 'userid', 'post', 0 );

	$token = $nv_Request->get_title( 'token', 'post', '', 1 );

	$listid = $nv_Request->get_string( 'listid', 'post', '' );

	if( $listid != '' and md5( $nv_Request->session_id . $global_config['sitekey'] ) == $token )
	{
		$del_array = array_map( 'intval', explode( ',', $listid ) );
	}
	elseif( $token == md5( $nv_Request->session_id . $global_config['sitekey'] . $userid ) )
	{
		$del_array = array( $userid );
	}

	if( ! empty( $del_array ) )
	{

		$_del_array = array();

		$a = 0;
		foreach( $del_array as $userid )
		{
			$result = $db->query( 'DELETE FROM ' . NV_USERS_GLOBALTABLE . '_groups_users WHERE group_id = '. intval( $getSetting['default_group_doctors'] ) .' AND userid = ' . ( int )$userid );
			if( $result->rowCount() )
			{
				$json['id'][$a] = $userid;
				$_del_array[] = $userid;
				++$a;
			}
		}
		$count = sizeof( $_del_array );

		if( $count )
		{
			nv_insert_logs( NV_LANG_DATA, $module_name, 'log_del_doctors', implode( ', ', $_del_array ), $admin_info['userid'] );

			$nv_Cache->delMod( $module_name );

			$json['success'] = $lang_module['doctors_delete_success'];
		}

	}
	else
	{
		$json['error'] = $lang_module['doctors_error_security'];
	}

	nv_jsonOutput( $json );
}
elseif( ACTION_METHOD == 'add' || ACTION_METHOD == 'edit' )
{

	$data = array(
		'userid' => 0,
		'first_name' => '',
		'birthday' => '',
		'gender' => '',
		'last_name' => '',
		'full_name' => '',
		'phone' => '',
		'email' => '',
		'address' => '',
		'active' => 1,
		'branch_id' => 0 );

	$error = array();

	$data['userid'] = $nv_Request->get_int( 'userid', 'get,post', 0 );
	if( $data['userid'] > 0 )
	{
		$data = $db->query( 'SELECT u.gender,u.birthday,u.userid, u.username, CONCAT(u.last_name,\'\', u.first_name) AS full_name, u.email, u.address, u.regdate, u.active, bu.branch_id FROM 
			' . NV_USERS_GLOBALTABLE . ' u  RIGHT JOIN ' . NV_USERS_GLOBALTABLE . '_groups_users gu ON (u.userid = gu.userid) 
			LEFT JOIN ' . TABLE_APPOINTMENT_NAME . '_branch_users bu ON (u.userid = bu.userid) WHERE u.userid=' . $data['userid'] )->fetch();

		$caption = $lang_module['doctors_update'];
	}
	else
	{
		$caption = $lang_module['doctors_add'];
	}

	if( $nv_Request->get_int( 'save', 'post' ) == 1 )
	{
		$full_name = trim( nv_substr( $nv_Request->get_title( 'full_name', 'post', '', '' ), 0, 250 ) );
		$data['phone'] =trim(  nv_substr( $nv_Request->get_title( 'phone', 'post', '', '' ), 0, 250 ));
		$data['email'] = trim( nv_substr( $nv_Request->get_title( 'email', 'post', '', '' ), 0, 250 ));
		$data['address'] = trim( nv_substr( $nv_Request->get_title( 'address', 'post', '', '' ), 0, 250 ));
		$data['active'] = $nv_Request->get_int( 'active', 'post', 1 );
		$data['branch_id'] = $nv_Request->get_int( 'branch_id', 'post', 0 );
		$data['password'] = $crypt->hash_password($data['phone'], $global_config['hashprefix']);
		$data['gender'] = $nv_Request->get_title( 'gender', 'post', '', '' );
		$data['birthday'] = $nv_Request->get_title( 'birthday', 'post', '' );
		if( preg_match( '/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $data['birthday'], $m ) )
		{

			$birthday = mktime( 0, 0, 1, $m[2], $m[1], $m[3] );
		}

		$full_name = explode(' ', $full_name);
		$ii = 1;

		foreach ($full_name as $key => $value) {
			if($ii<(count($full_name))){
				$data['last_name'] .= $value . ' ';
			}
			else{
				$data['first_name'] .= $value;
			}
			$ii ++;
		}

		if( empty( $full_name ) ) $error['full_name'] = $lang_module['doctors_error_full_name'];
		if( empty( $data['phone'] ) ) $error['phone'] = $lang_module['doctors_error_phone'];
		if( empty( $data['email'] ) ) $error['email'] = $lang_module['doctors_error_email'];
		if( empty( $data['address'] ) ) $error['address'] = $lang_module['doctors_error_address'];
		if( empty( $data['branch_id'] ) ) $error['branch_id'] = $lang_module['doctors_error_branch'];

		if( ! empty( $error ) && ! isset( $error['warning'] ) )
		{
			$error['warning'] = $lang_module['doctors_error_warning'];
		}

		if( empty( $error ) )
		{
			$data['md5username'] = nv_md5safe($data['phone']);
			
			try
			{
				if( $data['userid'] == 0 )
				{

					$data['sig'] = '';
					$data['in_groups_default'] = 10;
					$data['in_groups'] = 10;
					$data['view_mail']= 0;
					$data['is_email_verified']= -1;

					$sql = "INSERT INTO " . NV_USERS_GLOBALTABLE . " (
					group_id, username,service_package_id, md5username, password, email, phone, address, first_name, last_name, gender, birthday, sig, regdate,
					question, answer, passlostkey, view_mail,
					remember, in_groups, active, checknum, last_login, last_ip, last_agent, last_openid, email_verification_time,
					active_obj
					) VALUES (
					" . $data['in_groups_default'] . ",
					:username,
					" . intval( $data['service_package_id'] ) . ",
					:md5_username,
					:password,
					:email, 
					:phone, 
					:address,
					:first_name,
					:last_name,
					:gender,
					" . intval($birthday) . ",
					:sig,
					" . NV_CURRENTTIME . ",
					:question,
					:answer,
					'',
					" . $data['view_mail'] . ",
					1,
					'" . $data['in_groups'] . "', 1, '', 0, '', '', '',
					" . ($data['is_email_verified'] ? '-1' : '0') . ",
					'SYSTEM'
				)";
				$data_insert = [];
				$data_insert['username'] = $data['phone'];
				$data_insert['md5_username'] = $data['md5username'];
				$data_insert['password'] = $data['password'];
				$data_insert['email'] = $data['email'];  
				$data_insert['phone'] = $data['phone'];  
				$data_insert['address'] = $data['address'];
				$data_insert['first_name'] = $data['first_name'];
				$data_insert['last_name'] = $data['last_name'];
				$data_insert['gender'] = $data['gender'];
				$data_insert['sig'] = $data['sig'];
				$data_insert['question'] = $data['phone'];
				$data_insert['answer'] = $data['phone'];
				$userid = $db->insert_id($sql, 'userid', $data_insert);
				$db->query( 'INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_branch_users(userid, branch_id) VALUES('.$userid. ',' . $data['branch_id'] . ')' );
				$db->query( 'INSERT INTO ' . $db_config['prefix'] . '_users_groups_users(group_id,userid, is_leader,approved,data,time_requested,time_approved) VALUES(10,'.$userid. ',0,1,0,' . NV_CURRENTTIME . ',' . NV_CURRENTTIME . ')');
				nv_insert_logs( NV_LANG_DATA, $module_name, $lang_module['patient_add'], 'userid: ' . $userid, $admin_info['userid'] );
				$nv_Request->set_Session( $module_data . '_success', $lang_module['patient_add_success'] );

				$nv_Cache->delMod( $module_name );
			}
			else
			{
				$stmt = $db->prepare( 'UPDATE ' . NV_USERS_GLOBALTABLE . '  SET 
					first_name=:first_name,
					last_name=:last_name,
					phone="' .$data['phone'].'",
					email=:email,
					birthday=' .intval($birthday). ',
					gender="' .$data['gender']. '",
					password=:password,
					username=:username,
					md5username=:md5username,
					address=:address,
					active=' . intval( $data['active'] ) .'
					WHERE userid=' . intval( $data['userid'] ) );
				$stmt->bindParam( ':password', $data['password'], PDO::PARAM_STR );
				$stmt->bindParam( ':first_name', $data['first_name'], PDO::PARAM_STR );
				$stmt->bindParam( ':last_name', $data['last_name'], PDO::PARAM_STR );
				$stmt->bindParam( ':email', $data['email'], PDO::PARAM_STR );
				$stmt->bindParam( ':username', $data['phone'], PDO::PARAM_STR );
				$stmt->bindParam( ':md5username', $data['md5username'], PDO::PARAM_STR );
				$stmt->bindParam( ':address', $data['address'], PDO::PARAM_STR );

				if( $stmt->execute() )
				{
					nv_insert_logs( NV_LANG_DATA, $module_name, $lang_module['doctors_edit'], 'userid: ' . $data['userid'], $admin_info['userid'] );

					$nv_Request->set_Session( $module_data . '_success', $lang_module['doctors_edit_success'] );

					$nv_Cache->delMod( $module_name );

				}
				else
				{
					$error['warning'] = $lang_module['doctors_error_save'];

				}

				$stmt->closeCursor();


			}
		}
		catch ( PDOException $e )
		{
			$error['warning'] = $lang_module['doctors_error_save'];
				// var_dump( $e ); die();
		}
	}

	if( empty( $error ) )
	{

		Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
		die();
	}

}

$xtpl = new XTemplate( 'salary_add.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
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

$data['birthday'] = nv_date('d/m/Y', $data['birthday']);

$xtpl->assign( 'DATA', $data );
$xtpl->assign( 'CANCEL', NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=" . $op );
$xtpl->assign( 'TOKEN', md5( $client_info['session_id'] . $global_config['sitekey'] ) );

	// $xtpl->assign( 'UPLOADDIR', NV_UPLOADS_DIR . '/' . $module_upload );
	// $xtpl->assign( 'CURRENTPATH', NV_UPLOADS_DIR . '/' . $module_upload );
$xtpl->assign( 'BUTTON_SUBMIT', ( $data['userid'] == 0 ) ? $lang_module['doctors_create'] : $lang_module['doctors_update'] );
foreach( $arrayGender as $key => $name )
{
	$xtpl->assign( 'GENDER', array('key'=> $key, 'name'=> $name, 'selected'=> ( $data['gender'] == $key ) ? 'selected="selected"' : '') );
	$xtpl->parse( 'main.gender' );
}

$getBranch = getBranch( );

if( $getBranch )
{
	foreach( $getBranch as $key => $item )
	{
		$xtpl->assign( 'BRANCH', array(
			'key' => $key,
			'name' => $item['title'],
			'selected' => (  $key == $data['branch_id'] ) ? 'selected="selected"' : '' ) );
		$xtpl->parse( 'main.branch' );

	}
}

foreach( $array_active as $key => $name )
{
	$xtpl->assign( 'ACTIVE', array('key'=> $key, 'name'=> $name, 'selected'=> ( is_numeric( $data['active'] ) && $data['active']== $key ) ? 'selected="selected"' : '') );
	$xtpl->parse( 'main.active' );
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

/*show list doctors*/

$base_url_order = $base_url = NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '='. $op;

$per_page = 20;

$page = $nv_Request->get_int( 'page', 'get', 1 );

$data['full_name'] = trim( $nv_Request->get_string( 'full_name', 'get', '' ) );
$data['email'] = trim( $nv_Request->get_string( 'email', 'get', '' ) );
$data['phone'] = trim( $nv_Request->get_string( 'phone', 'get' ) );
$data['date_from'] = trim( $nv_Request->get_title( 'date_from', 'get', '' ) );
$data['date_to'] = trim( $nv_Request->get_title( 'date_to', 'get', '' ) );
$data['active'] = $nv_Request->get_title( 'active', 'get', '' );


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

$sql = 	NV_USERS_GLOBALTABLE . ' u 
RIGHT JOIN ' . NV_USERS_GLOBALTABLE . '_groups_users gu ON (u.userid = gu.userid) 
LEFT JOIN ' . TABLE_APPOINTMENT_NAME . '_branch_users bu ON (u.userid = bu.userid)';


$implode = array();

$implode[]= "gu.group_id=" . intval( $getSetting['default_group_doctors'] );

if( $data['full_name'] )
{
	$implode[]= "CONCAT(u.last_name,' ', u.first_name) LIKE '%" . $db->dblikeescape( $data['full_name'] ) . "%'";
	$base_url.= '&amp;full_name=' . $data['full_name'];
	$base_url_order.= '&amp;full_name=' . $data['full_name'];
}
if( $data['phone'] )
{
	$implode[]= "username LIKE '%" . $db->dblikeescape( $data['phone'] ) . "%'";
	$base_url.= '&amp;phone=' . $data['phone'];
	$base_url_order.= '&amp;phone=' . $data['phone'];
}
if( $data['email'] )
{
	$implode[]= "email LIKE '%" . $db->dblikeescape( $data['email'] ) . "%'";
	$base_url.= '&amp;email=' . $data['email'];
	$base_url_order.= '&amp;email=' . $data['email'];
}

if( is_numeric( $data['active'] ) )
{
	$implode[]= "u.active=" .  intval( $data['active'] );
	$base_url.= '&amp;active=' . $data['active'];
	$base_url_order.= '&amp;active=' . $data['active'];
}
if( $date_from && $date_to )
{
	$implode[] = "(u.regdate BETWEEN " . intval( $date_from ) . " AND " . intval( $date_to ) . ")";
}

if( $implode )
{
	$sql .= ' WHERE ' . implode( ' AND ', $implode );
}

$sort = $nv_Request->get_string( 'sort', 'get', '' );

$order = $nv_Request->get_string( 'order', 'get' ) == 'desc' ? 'desc' : 'asc';

$sort_data = array(
	'full_name',
	'email',
	'username',
	'active',
	'regdate' );

if( isset( $sort ) && in_array( $sort, $sort_data ) )
{

	$sql .= ' ORDER BY ' . $sort;
}
else
{
	$sql .= ' ORDER BY regdate';
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

$db->sqlreset()->select( 'u.userid, u.username, CONCAT(u.last_name,\' \', u.first_name) AS full_name, u.email,  u.regdate, u.active, bu.branch_id' )->from( $sql )->limit( $per_page )->offset( ( $page - 1 ) * $per_page );


$result = $db->query( $db->sql() );

$array = array();
while( $rows = $result->fetch() )
{
	$array[] = $rows;
}


$xtpl = new XTemplate( 'salary.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
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
$xtpl->assign( 'ADD_NEW', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=doctors&action=add' );

$xtpl->assign( 'DATA', $data );

$order2 = ( $order == 'asc' ) ? 'desc' : 'asc';

$xtpl->assign( 'URL_FULL_NAME', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=full_name&amp;order=' . $order2 . '&amp;per_page=' . $per_page );
$xtpl->assign( 'URL_EMAIL', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=email&amp;order=' . $order2 . '&amp;per_page=' . $per_page );
$xtpl->assign( 'URL_PHONE', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=phone&amp;order=' . $order2 . '&amp;per_page=' . $per_page );
$xtpl->assign( 'URL_DATE_BOOKING', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=date_booking&amp;order=' . $order2 . '&amp;per_page=' . $per_page );
$xtpl->assign( 'URL_STATUS', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=status&amp;order=' . $order2 . '&amp;per_page=' . $per_page );

$xtpl->assign( 'FULL_NAME_ORDER', ( $sort == 'full_name' ) ? 'class="' . $order2 . '"' : '' );
$xtpl->assign( 'EMAIL_ORDER', ( $sort == 'email' ) ? 'class="' . $order2 . '"' : '' );
$xtpl->assign( 'PHONE_ORDER', ( $sort == 'phone' ) ? 'class="' . $order2 . '"' : '' );
$xtpl->assign( 'DATE_BOOKING_ORDER', ( $sort == 'date_booking' ) ? 'class="' . $order2 . '"' : '' );
$xtpl->assign( 'STATUS_ORDER', ( $sort == 'status' ) ? 'class="' . $order2 . '"' : '' );

if( $nv_Request->get_string( $module_data . '_success', 'session' ) )
{
	$xtpl->assign( 'SUCCESS', $nv_Request->get_string( $module_data . '_success', 'session' ) );

	$xtpl->parse( 'main.success' );

	$nv_Request->unset_request( $module_data . '_success', 'session' );

}


foreach( $array_active as $key => $name )
{
	$xtpl->assign( 'ACTIVE', array('key'=> $key, 'name'=> $name, 'selected'=> ( is_numeric( $data['active'] ) && $data['active']== $key ) ? 'selected="selected"' : '') );
	$xtpl->parse( 'main.active' );
}

if( ! empty( $array ) )
{
	$getBranch = getBranch();

	foreach( $array as $item )
	{
		
		// $item['full_name'] = nv_show_name_user( $item['first_name'], $item['last_name'], '' );

		
		$item['branch'] = isset( $getBranch[$item['branch_id']] ) ? $getBranch[$item['branch_id']]['title'] : '';
		$item['regdate'] = !empty( $item['regdate'] ) ? date('H:i d/m/Y', $item['regdate']) : '';
		$item['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $item['userid'] );
		$item['edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&action=edit&token=' . $item['token'] . '&userid=' . $item['userid'];

		$xtpl->assign( 'LOOP', $item );
		
		
		foreach( $array_active as $key => $name )
		{
			$xtpl->assign( 'ACTIVE', array('key'=> $key, 'name'=> $name, 'selected'=> ( is_numeric( $item['active'] ) && $item['active']== $key ) ? 'selected="selected"' : '') );
			$xtpl->parse( 'main.loop.active' );
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
echo nv_admin_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';
