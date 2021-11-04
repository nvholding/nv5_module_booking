<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */


if( ! defined( 'NV_IS_MOD_APPOINTMENT' ) ) die( 'Stop!!!' );

$mod= $nv_Request->get_string('mod', 'get', '');

if ( $mod == 'update_benh_nhan' ) {

	$data['full_name'] = $nv_Request->get_string('full_name', 'post', '');
	$data['gender'] = $nv_Request->get_string('gender', 'post', '');
	$data['birthday'] = $nv_Request->get_string('birthday', 'post', '');
	$data['phone'] = $nv_Request->get_string('phone', 'post', '');
	$data['email'] = $nv_Request->get_string('email', 'post', '');
	$data['patient_address'] = $nv_Request->get_string('patient_address', 'post', '');
	$data['work'] = $nv_Request->get_string('work', 'post', '');
	$data['expect'] = $nv_Request->get_string('expect', 'post', '');
	
	
	
	$data['confess'] = $nv_Request->get_string('confess', 'post', '');
	$data['patient_group'] = $nv_Request->get_int('patient_group', 'post', 0);
	$data['service_package_id'] = $nv_Request->get_int('service_package_id', 'post', 0);
	

	$birthday = 0;
	if( preg_match( '/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $data['birthday'], $m ) )
	{

		$birthday = mktime( 0, 0, 1, $m[2], $m[1], $m[3] );
	}
	

	$db->query( 'INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_patient_edit(userid,gender,full_name,birthday,address,work,expect,time_require,email,phone) VALUES('. $user_info['userid']. ',"' . $data['gender'] . '","' . $data['full_name'] . '", ' . $birthday . ', "' . $data['patient_address'] . '", "' . $data['work'] . '", "' . $data['expect'] . '", ' . NV_CURRENTTIME . ', "' . $data['email'] . '", "' . $data['phone'] . '")' );

	$content1 .= '<div>Yêu cầu cập nhật hồ sơ trị liệu lúc: ' . date('d/m/Y - H:i:s',NV_CURRENTTIME) . '</div>';
	$content1 .= '<div>Người gửi yêu cầu: ' . $user_info['username'] . '</div>';
	$content1 .= '<div>Khách hàng: ' . $data['full_name'] . '</div>';
	$email_title = 'Thông báo cập nhật hồ sơ trị liệu';
	//$global_config['site_email'] = 'trungnghiack@gmail.com';
	$a = nv_sendmail(array($global_config['site_name'], $config_email['sender_email']['config_value']), $global_config['site_email'], sprintf($email_title, $module_info['custom_title'], $order_code), $content1);





	$json[] = ['status'=>'OK', 'text'=>'Gửi yêu cầu thành công, quản trị viên sẽ kiểm duyệt!'];
	print_r( json_encode( $json[0] ) );
	die();

}


if( ACTION_METHOD == 'search' )
{
	
	
	$data['date_from'] = $nv_Request->get_title( 'date_from', 'post', '' );
	$data['date_to'] = $nv_Request->get_title( 'date_to', 'post', '' );
	$data['keyword'] = $nv_Request->get_string( 'keyword', 'post', '');
	$page = $nv_Request->get_int( 'page', 'post',1);
	$perpage = 50;

	
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

	$implode = array();

	if( $data['keyword'] )
	{
		$implode[] = '(p.full_name LIKE \'%' . $db_slave->dblikeescape( $data['keyword'] ) . '%\' OR p.patient_code LIKE \'%' . $db_slave->dblikeescape( $data['keyword'] ) . '%\')';
		
	}


	$sql =  NV_USERS_GLOBALTABLE . ' u INNER JOIN ' . TABLE_APPOINTMENT_NAME . '_patient p ON u.userid = p.userid LEFT JOIN ' . TABLE_APPOINTMENT_NAME . '_patient_appointment pa ON pa.patient_id = p.userid';

	if( $date_from && $date_to )
	{
		$implode[] = 'pa.date_added BETWEEN ' . intval( $date_from ) . ' AND ' . intval( $date_to );
	}

	if( $implode )
	{
		$sql .= ' WHERE '  . implode( ' AND ', $implode );
	}
	
	$sql .= ' GROUP BY p.userid ORDER BY p.patient_code ASC';

	$num_items = $db->query( 'SELECT COUNT(*) FROM ' . $sql )->fetchColumn();

	$base_url = NV_BASE_SITEURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '='. $op .'&date_from=' . $data['date_from'] .'&date_to=' . $data['date_to'] .'&keyword=' . $data['keyword'] .'&per_page=' . $perpage;

	$db->sqlreset()->select( 'u.userid, u.username, p.full_name, u.email, p.gender, p.birthday, p.address, p.patient_code' )->from( $sql )->limit( $perpage )->offset( ( $page - 1 ) * $perpage );
	
	

	$result = $db->query( $db->sql() );
	
	//$json['template'] = $db->sql();	
	//nv_jsonOutput( $json );
	
	$stt = 1;
	$dataContent = array();
	while( $rows = $result->fetch() )
	{
		$rows['job'] = '';
		$rows['age'] = $rows['birthday'] ? date('d/m/Y', $rows['birthday']) : 'N/A';
		

		$rows['gender'] = isset( $arrayGender[$rows['gender']] ) ? $arrayGender[$rows['gender']] : 'N/A';
		$rows['stt'] = $stt + ( ( $page - 1 ) * $perpage );
		$rows['link'] = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '/' . $rows['username'], true );
		
		// số lần khám bệnh còn lại
		// lấy thông tin số lần khám còn lại của userid
		$get_parent_info = get_parent_info($rows['userid']);
		$rows['kham_conlai'] = $get_parent_info['kham_conlai'];
		
		
		$dataContent[] = $rows;
		++$stt;
	}
	
	$generate_page = nv_generate_page( $base_url, $num_items, $perpage, $page );

	$json['template'] = ThemeViewPatientSearch( $dataContent, $generate_page );	
	

	nv_jsonOutput( $json );
}


if( ACTION_METHOD == 'print' )
{

	$token = $nv_Request->get_title( 'token', 'get', '' );
	$userid = $nv_Request->get_int( 'userid', 'get', 0 );

	
	$userPatient = array();


	$userPatient = $db->query( 'SELECT u.userid, u.username, p.full_name, u.email, p.gender, p.birthday, p.address, p.history, p.expect, p.confess,p.other_contact,p.service_package_id, p.phone,p.note,p.work,p.patient_result,p.patient_code FROM ' . NV_USERS_GLOBALTABLE . ' u INNER JOIN ' . TABLE_APPOINTMENT_NAME . '_patient p ON u.userid = p.userid WHERE u.userid=' . intval( $userid ) . ' AND p.mode = 0' )->fetch();


	if( empty( $userPatient ) )
	{
		nv_redirect_location( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op, true );
		
	}
	
	$userPatient['job'] = '';
	$userPatient['age'] = ( $userPatient['birthday'] ) ? floor((time() - $userPatient['birthday'] ) / 31556926) : 'N/A';
	$userPatient['birthday'] = $userPatient['birthday'] ? date('d/m/Y', $userPatient['birthday']) : '';
	$userPatient['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $userPatient['userid'] );
	
	$data['date_from'] = $nv_Request->get_title( 'df', 'post', '' );
	$data['date_to'] = $nv_Request->get_title( 'dt', 'post', '' );
	$data['keyword'] = $nv_Request->get_string( 'keyword', 'post', '');
	$data['doctors_id'] = $nv_Request->get_int( 'doctors_id', 'post', 0 );
	$data['userid'] = $userPatient['userid'];
	
	$page = $nv_Request->get_int( 'page', 'post',1);
	$perpage = 50;

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

	$implode = array();


	$implode[] = 'patient_id=' . intval( $userPatient['userid'] );
	if( $data['doctors_id']  > 0)
	{
		$implode[] = 'doctors_id=' . intval( $data['doctors_id'] );
	}

	$sql = TABLE_APPOINTMENT_NAME . '_patient_appointment';
	
	if( $date_from && $date_to )
	{
		$implode[] = 'date_added BETWEEN ' . intval( $date_from ) . ' AND ' . intval( $date_to );
	}

	if( $implode )
	{
		$sql .= ' WHERE '  . implode( ' AND ', $implode );
	}
	
	$sql .= ' ORDER BY date_added DESC';

	$perpage = $db->query( 'SELECT COUNT(*) FROM ' . $sql )->fetchColumn();


	$db->sqlreset()->select( '*' )->from( $sql )->limit( $perpage )->offset( ( $page - 1 ) * $perpage );
	$result = $db->query( $db->sql() );
	$doctorsArray = array();
	$stt = 1;
	$dataContent = array();

	while( $rows = $result->fetch() )
	{
		$rows['stt'] = $stt + ( ( $page - 1 ) * $perpage );
		$doctorsArray[] = $rows['doctors_id'];
		$dataContent[] = $rows;
		++$stt;
	}
	$result->closeCursor();
	$doctorsList = array();
	$doctorsArray = array_unique( $doctorsArray );
	if( $doctorsArray )
	{
		
		$result = $db->query( 'SELECT userid, username, CONCAT(last_name,\' \', first_name) AS full_name, email, gender, birthday, address FROM ' . NV_USERS_GLOBALTABLE . ' WHERE userid IN ('. implode( ',', $doctorsArray ) .')' );

		while( $user = $result->fetch() )
		{

			$doctorsList[$user['userid']] = $user;
		}
		$result->closeCursor();
	}
	
	$contents = ThemeViewPatientPrint( $userPatient, $doctorsList, $dataContent );	

	include NV_ROOTDIR . '/includes/header.php';
	echo $contents;
	include NV_ROOTDIR . '/includes/footer.php';

}


$data['userid'] = $nv_Request->get_int( 'userid', 'post', 0 );

$data['appointment_id'] = $nv_Request->get_int( 'appointment_id', 'get', 0 );

$data['token'] = $nv_Request->get_title( 'token', 'post', '' );

$dataContent = array();
$generate_page = '';

if( $user_info['userid'] )
{
	
	$userPatient = array();
	
	$userPatient = $db->query( 'SELECT u.userid, u.username, p.full_name, u.email, p.gender, p.birthday, p.address, p.confess,p.expect,p.history,p.work,p.other_contact,p.note,p.service_package_id,p.patient_group,p.phone,p.patient_code,p.kham_conlai FROM ' . NV_USERS_GLOBALTABLE . ' u INNER JOIN ' . TABLE_APPOINTMENT_NAME . '_patient p on u.userid = p.userid WHERE u.userid=' . $user_info['userid'] . ' AND p.mode = 0')->fetch();
		if($userPatient['patient_group']){
			$userPatient['name_group_patient'] = get_name_patient_group($userPatient['patient_group'])['title'];	
		}
		if($userPatient['gender'] == 'M'){
			$userPatient['gender'] = 'Nam';
		}else{
			$userPatient['gender'] = 'Nữ';
		}
	
	
	if( empty( $userPatient ) )
	{
		nv_redirect_location( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op, true );
		
	}
	
	
	
	$userPatient['job'] = '';
	$userPatient['age'] = ( $userPatient['birthday'] ) ? floor((time() - $userPatient['birthday'] ) / 31556926) : 'N/A';
	$userPatient['birthday'] = $userPatient['birthday'] ? date('d/m/Y', $userPatient['birthday']) : '';
	$userPatient['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $userPatient['userid'] );
	
	$userPatient['appointment_id'] = $data['appointment_id'];
	
	$data['date_from'] = $nv_Request->get_title( 'df', 'post', '' );
	$data['date_to'] = $nv_Request->get_title( 'dt', 'post', '' );
	$data['keyword'] = $nv_Request->get_string( 'keyword', 'post', '');
	$data['doctors_id'] = $nv_Request->get_int( 'doctors_id', 'post', 0 );
	$data['userid'] = $userPatient['userid'];
	
	$page = $nv_Request->get_int( 'page', 'post',1);
	$perpage = 50;

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

	$implode = array();

	
	// đang ở patient nào thì lấy userid của khách hàng đó thôi
	
	$implode[] = 'patient_id=' . intval( $userPatient['userid'] );
	
	// bác sĩ nào đang login thì lấy userid của bác sĩ đó
	if($data['doctors_id'])
	$implode[] = 'doctors_id=' . intval( $data['doctors_id'] );
	
	
	if( $data['appointment_id']  > 0)
	{
		$implode[] = 'appointment_id=' . intval( $data['appointment_id'] );
	}
	
	

	$sql = TABLE_APPOINTMENT_NAME . '_patient_appointment';
	
	if( $date_from && $date_to )
	{
		$implode[] = 'date_added BETWEEN ' . intval( $date_from ) . ' AND ' . intval( $date_to );
	}

	if( $implode )
	{
		$sql .= ' WHERE '  . implode( ' AND ', $implode );
	}
	
	$sql .= ' ORDER BY date_added DESC';

	$num_items = $db->query( 'SELECT COUNT(*) FROM ' . $sql )->fetchColumn();

	$base_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '/' . $rows['username'] .'?df=' . $data['date_from'] .'&dt=' . $data['date_to'];

	$db->sqlreset()->select( '*' )->from( $sql )->limit( $perpage )->offset( ( $page - 1 ) * $perpage );

	$result = $db->query( $db->sql() );
	
	
	$doctorsArray = array();
	$stt = 1;
	$dataContent = array();
	while( $rows = $result->fetch() )
	{
		$rows['stt'] = $stt + ( ( $page - 1 ) * $perpage );
		$doctorsArray[] = $rows['doctors_id'];
		$dataContent[] = $rows;
		++$stt;
	}
	$result->closeCursor();
	$doctorsList = array();
	if( $doctorsArray )
	{
		$result = $db->query( 'SELECT userid, username, CONCAT(last_name,\' \', first_name) AS full_name, email, gender, birthday, address FROM ' . NV_USERS_GLOBALTABLE . ' WHERE userid IN ('. implode( ',', $doctorsArray ) .')' );

		while( $user = $result->fetch() )
		{

			$doctorsList[$user['userid']] = $user;
		}
		$result->closeCursor();
	}

	$generate_page = nv_generate_page( $base_url, $num_items, $perpage, $page );

	
	$_SESSION[$data['token']] = nv_base64_encode( serialize( $data ) );
	
	if( $data['token'] == md5( $nv_Request->session_id . $global_config['sitekey'] . $data['userid'] ) )
	{
		// search trong url https://suckhoethiennhan.com/booking/patient/0906192927/
		
		nv_jsonOutput( array( 'template'=> ThemeViewPatientUserSearch_User( $userPatient, $doctorsList, $dataContent, $generate_page ) ) );
	}
	else
	{
		
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

		$sql= 'SELECT u.userid, u.username, CONCAT(u.last_name,\' \', u.first_name) AS full_name, u.username, u.email, u.address, u.regdate, u.active FROM 
		' . NV_USERS_GLOBALTABLE . ' u  RIGHT JOIN ' . NV_USERS_GLOBALTABLE . '_groups_users gu ON (u.userid = gu.userid)';

		if( !empty( $implode ) )
		{
			$sql .= ' WHERE ' . implode( ' AND ', $implode );
		}		
		
		$sql .= ' ORDER BY full_name ASC LIMIT 0, 200';

		$sth = $db->prepare( $sql );
		$sth->execute();
		$doctor = $sth->fetchAll();
		
		
		$contents = ThemeViewPatientUser_User( $userPatient, $doctorsList, $dataContent, $generate_page, $doctor);	
	}

	
}

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
