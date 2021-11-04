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

if( empty( $user_info ) )
{
	nv_redirect_location( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA, true );
}


// bác sĩ và admin mới được quyền vào xem danh sách lịch hẹn
if($user_info['group_id'] != $getSetting['default_group_doctors'] and !defined('NV_IS_ADMIN') and $user_info['group_id'] != 1 and $user_info['group_id'] !=2 and $user_info['group_id'] !=3)
{
	nv_redirect_location(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=history');
}
       

if( ACTION_METHOD == 'search' )
{
	$data['date_from'] = $nv_Request->get_title( 'date_from', 'post', '' );
	$data['date_to'] = $nv_Request->get_title( 'date_to', 'post', '' );
	$data['keyword'] = $nv_Request->get_string( 'keyword', 'post', '');
	$data['chinhanh'] = $nv_Request->get_int( 'chinhanh', 'post,get',0);
	
	$page = $nv_Request->get_int( 'page', 'post',1);
	$perpage = 5;

	
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
		// note
		$implode[] = '(p.full_name LIKE \'%' . $db_slave->dblikeescape( $data['keyword'] ) . '%\' OR p.note LIKE \'%' . $db_slave->dblikeescape( $data['keyword'] ) . '%\' OR p.patient_code LIKE \'' . $db_slave->dblikeescape( $data['keyword'] ) . '\')';
	}

	if($user_info['group_id'] == $getSetting['default_group_doctors'])
	{
		$implode[] = 'u.doctors_id = '. $user_info['userid'];
	}
	
	if( $data['chinhanh'] )
	{
		// lấy danh sách bác sĩ trong chi nhánh này
		$get_array_doctor = list_doctor_branch($data['chinhanh']);
		if(!empty($get_array_doctor))
		{
			$implode[] = 'u.doctors_id IN('. implode(',',$get_array_doctor).')';
		}
	}

	// _patient_appointment
	
	$sql =  TABLE_APPOINTMENT_NAME . '_patient_appointment u LEFT JOIN ' . TABLE_APPOINTMENT_NAME . '_patient p ON (u.patient_id = p.userid)';

	if( $date_from && $date_to )
	{
		$implode[] = 'u.date_added BETWEEN ' . intval( $date_from ) . ' AND ' . intval( $date_to );
	}

	if( $implode )
	{
		$sql .= ' WHERE '  . implode( ' AND ', $implode );
	}
	
	$sql .= ' GROUP BY p.userid ORDER BY u.date_added DESC';

	$num_items = $db->query( 'SELECT COUNT(*) FROM ' . $sql )->fetchAll();
	$num_items = count($num_items);
	
	$base_url = NV_BASE_SITEURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '='. $op .'&date_from=' . $data['date_from'] .'&date_to=' . $data['date_to'] .'&keyword=' . $data['keyword'] .'&per_page=' . $perpage;

	$db->sqlreset()->select( 'p.userid, p.full_name, p.gender, p.birthday, p.address, p.patient_code, p.confess' )->from( $sql )->limit( $perpage )->offset( ( $page - 1 ) * $perpage );
	
	

	$result = $db->query( $db->sql() );
	
	
	$dataContent = array();
	while( $rows = $result->fetch() )
	{
		$rows['job'] = '';
		$rows['age'] = $rows['birthday'] ? date('d/m/Y', $rows['birthday']) : 'N/A';
		

		$rows['gender'] = isset( $arrayGender[$rows['gender']] ) ? $arrayGender[$rows['gender']] : 'N/A';
		
		$rows['link'] = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=patient/' . $rows['userid'], true );
		
		$get_parent_info = get_parent_info($rows['userid']);
		$rows['kham_conlai'] = $get_parent_info['kham_conlai'];
		
		$dataContent[] = $rows;
		
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
	
	$data['chinhanh'] = $nv_Request->get_int( 'chinhanh', 'post,get',0);
	
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


	$implode[] = 'userid=' . intval( $userPatient['userid'] );
	if( $data['doctors_id']  > 0)
	{
		$implode[] = 'doctors_id=' . intval( $data['doctors_id'] );
	}

	$sql = TABLE_APPOINTMENT_NAME . '_patient';
	
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
$data['token'] = $nv_Request->get_title( 'token', 'post', '' );

$dataContent = array();
$generate_page = '';

if(true)
{
	$page = $nv_Request->get_int( 'page', 'post,get',1);
	$perpage = 5;
	
	$base_url = NV_BASE_SITEURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '='. $op;
	
	$data['keyword'] = $nv_Request->get_string( 'keyword', 'post,get', '');
	$data['date_from'] = $nv_Request->get_title( 'date_from', 'post', '' );
	
	$data['date_to'] = $nv_Request->get_title( 'date_to', 'post,get', '' );
	$data['keyword'] = $nv_Request->get_string( 'keyword', 'post,get', '');
	$data['chinhanh'] = $nv_Request->get_int( 'chinhanh', 'post,get',0);
	//print_r($data['chinhanh']);die;
	
	$page = $nv_Request->get_int( 'page', 'post,get',1);
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
		$implode[] = '(p.full_name LIKE \'%' . $db_slave->dblikeescape( $data['keyword'] ) . '%\' OR p.patient_code LIKE \'' . $db_slave->dblikeescape( $data['keyword'] ) . '\')';
		
		$base_url .= '&keyword=' . $data['keyword'];
	}

	// _patient_appointment KH đã trị liệu
	
	$sql =  TABLE_APPOINTMENT_NAME . '_patient_appointment u INNER JOIN ' . TABLE_APPOINTMENT_NAME . '_patient p ON u.patient_id = p.userid ';

	if( $date_from )
	{
		$implode[] = 'u.date_added >= ' . intval( $date_from );
	}
	
	if( $date_to )
	{
		$implode[] = 'u.date_added <= ' . intval( $date_to );
	}
	
	
	if($user_info['group_id'] == $getSetting['default_group_doctors'])
	{
		$implode[] = 'u.doctors_id = '. $user_info['userid'];
	}
	
	if( $data['chinhanh'] )
	{
		// lấy danh sách bác sĩ trong chi nhánh này
		$get_array_doctor = list_doctor_branch($data['chinhanh']);
		if(!empty($get_array_doctor))
		{
			$implode[] = 'u.doctors_id IN('. implode(',',$get_array_doctor).')';
		}
		$base_url .= '&chinhanh=' . $data['chinhanh'];
	}
	
	
	if( $implode )
	{
		$sql .= ' WHERE '  . implode( ' AND ', $implode );
	}
	
	$sql .= ' GROUP BY p.userid ORDER BY u.date_added DESC';

	$num_items = $db->query( 'SELECT COUNT(*) FROM ' . $sql )->fetchAll();
	$num_items = count($num_items);
	
	

	$db->sqlreset()->select( '*' )->from( $sql )->limit( $perpage )->offset( ( $page - 1 ) * $perpage );

	$result = $db->query( $db->sql() );
	
	//die($db->sql());

	$dataContent = array();
	while( $rows = $result->fetch() )
	{
		$rows['job'] = '';
		$rows['age'] = $rows['birthday'] ? date('d/m/Y', $rows['birthday']) : 'N/A';
		

		$rows['gender'] = isset( $arrayGender[$rows['gender']] ) ? $arrayGender[$rows['gender']] : 'N/A';
		
		$rows['link'] = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=patient/' . $rows['userid'], true );
		
		$get_parent_info = get_parent_info($rows['userid']);
		$rows['kham_conlai'] = $get_parent_info['kham_conlai'];
		
		
		$dataContent[] = $rows;
		
	}
	
	$generate_page = nv_generate_page( $base_url, $num_items, $perpage, $page );

	$contents = ThemeViewPatient_Follow( $dataContent, $generate_page, $data);	


}

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
