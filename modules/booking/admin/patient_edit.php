<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2013 Webdep24.com. All rights reserved
 * @Blog  http://dangdinhtu.com
 * @License GNU/GPL version 2 or any later version
 * @Createdate  Wed, 21 Jan 2015 14:00:59 GMT
 */

$error = array();

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

$page_title = $lang_module['patient'];
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

/*
// kiem tra kh trung ma
$result = $db->query('SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_patient')->fetchAll();

$arr = array();

foreach($result as $pa)
{
	$count = $db->query('SELECT COUNT(*) as count FROM ' . NV_PREFIXLANG . '_' . $module_data . '_patient WHERE patient_id ='. $pa['patient_id'])->fetchColumn();
	
	if($count > 1)
		$arr[] = $pa;
	
}
PRINT_R($arr);DIE;

*/



/*
	$code = 'KH_%06s';	
	$patient_code = 1;
	
// lay id lon nhat ra
	$list_p = $db->query('SELECT patient_id FROM ' . NV_PREFIXLANG . '_' . $module_data . '_patient WHERE 1=1 ORDER BY patient_id ASC')->fetchAll();
	
	foreach($list_p as $patient)
	{
		//$patient_code = sprintf($code, $patient['patient_id']);
		
		$db->query('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_patient SET patient_code ="'. $patient_code .'" WHERE patient_id =' . $patient['patient_id'] );
		
		$patient_code++;
	}
				

*/

$base_url_order = $base_url = NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '='. $op;

$per_page = 20;

$page = $nv_Request->get_int( 'page', 'get', 1 );

$data['full_name'] = trim( $nv_Request->get_string( 'full_name', 'get', '' ) );
$data['email'] = trim( $nv_Request->get_string( 'email', 'get', '' ) );
$data['phone'] = trim( $nv_Request->get_string( 'phone', 'get' ) );
$data['date_from'] = trim( $nv_Request->get_title( 'date_from', 'get', '' ) );
$data['patient_code'] = trim( $nv_Request->get_title( 'patient_code', 'get', '' ) );
$data['date_to'] = trim( $nv_Request->get_title( 'date_to', 'get', '' ) );
$data['active'] = $nv_Request->get_title( 'active', 'get', '' );
$data['patient_group'] = $nv_Request->get_int( 'patient_group', 'get', 0 );




if( empty( $data['date_to'] ) )
{
	$data['date_to'] = $data['date_from'];
}
if( empty( $data['date_from'] ) )
{
	$data['date_from'] = $data['date_to'];
}


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



$sql = 	NV_USERS_GLOBALTABLE . ' u INNER JOIN ' . TABLE_APPOINTMENT_NAME . '_patient p ON (u.userid = p.userid ) INNER JOIN ' . TABLE_APPOINTMENT_NAME . '_patient_edit e ON (e.userid = p.userid )';


$implode = array();

if( $data['full_name'] )
{
	$implode[]= " p.full_name LIKE '%" . $db->dblikeescape( $data['full_name'] ) . "%'";
	$base_url.= '&amp;full_name=' . $data['full_name'];
	$base_url_order.= '&amp;full_name=' . $data['full_name'];
}

if( $data['patient_code'] )
{
	$implode[]= " p.patient_code LIKE '%" . $db->dblikeescape( $data['patient_code'] ) . "%'";
	$base_url.= '&amp;patient_code=' . $data['patient_code'];
	$base_url_order.= '&amp;patient_code=' . $data['patient_code'];
}


if( $data['phone'] )
{
	$implode[]= " p.phone LIKE '%" . $db->dblikeescape( $data['phone'] ) . "%'";
	$base_url.= '&amp;phone=' . $data['phone'];
	$base_url_order.= '&amp;phone=' . $data['phone'];
}
if( $data['email'] )
{
	$implode[]= " u.email LIKE '%" . $db->dblikeescape( $data['email'] ) . "%'";
	$base_url.= '&amp;email=' . $data['email'];
	$base_url_order.= '&amp;email=' . $data['email'];
}
if( $data['patient_group'] )
{
	$implode[] = "(p.patient_group = " . $data['patient_group'] . ")";
	$base_url.= '&amp;patient_group=' . $data['patient_group'];
	$base_url_order.= '&amp;patient_group=' . $data['patient_group'];
}


if( $date_from && $date_to )
{
	$implode[] = "(p.date_added BETWEEN " . intval( $date_from ) . " AND " . intval( $date_to ) . ")";
}

$implode[] = 'e.using_patient = 0';

if( $implode )
{
	$sql .= ' WHERE p.mode = 0 AND ' . implode( ' AND ', $implode );
}



$num_items = $db->query( 'SELECT COUNT(*) FROM ' . $sql )->fetchColumn();
$sql .= ' GROUP BY p.userid';
$sort = $nv_Request->get_string( 'sort', 'get', '' );

$order = $nv_Request->get_string( 'order', 'get' ) == 'desc' ? 'desc' : 'asc';



$sort_data = array(
	'full_name',
	'email',
	'username',
	'active',
	'date_added',
	'regdate' );

if( isset( $sort ) && in_array( $sort, $sort_data ) )
{

	$sql .= ' ORDER BY ' . $sort;
}
else
{
	$sql .= ' ORDER BY patient_code';
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




$db->sqlreset()->select( 'u.userid, u.username, p.full_name, u.email, p.gender, p.address, p.birthday, u.regdate, p.date_added, p.patient_group, p.confess, p.other_contact, p.service_package_id, p.blood_pressure, p.patient_result, p.work, p.history, p.expect,p.note,p.phone,p.patient_code,p.kham_conlai' )->from( $sql )->limit( $per_page )->offset( ( $page - 1 ) * $per_page );

$result = $db->query( $db->sql() );

$array = array();
while( $rows = $result->fetch() )
{
	$array[] = $rows;
}


$xtpl = new XTemplate( 'patient_update.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
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
$xtpl->assign( 'ADD_NEW', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=patient&action=add' );

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
$list_patient_group = get_group_patient();

foreach( $list_patient_group as $key => $name )
{
	$xtpl->assign( 'PATIENT_GROUP', array('key'=> $key, 'title'=> $name['title'], 'selected'=> ( $data['patient_group'] == $key ) ? 'selected="selected"' : '') );
	$xtpl->parse( 'main.patient_group' );
}
if( ! empty( $array ) )
{

	foreach( $array as $item )
	{

		$item['patient_group_name'] = get_name_patient_group($item['patient_group'])['title'];
		$item['service_package'] = get_name_ServicePackage($item['service_package_id'])['title'];

		
		$item['birthday'] = !empty( $item['birthday'] ) ? date('d/m/Y', $item['birthday']) : 'N/A';
		
		
		$item['gender'] = isset( $arrayGender[$item['gender']] ) ? $arrayGender[$item['gender']] : 'N/A';

		$item['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $item['userid'] );
		$item['edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=patient&action=edit&token=' . $item['token'] . '&userid=' . $item['userid'];
		$item['view'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&action=view&token=' . $item['token'] . '&userid=' . $item['userid'];
		
		$item['url_appointment'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=appointment&action=add&userid=' . $item['userid'];
		
		$item['url_by_service'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=buy_service&action=by_service&token=' . $item['token'] . '&userid=' . $item['userid'];
		
		

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

if( ! empty( $error ) )
{
	foreach($error as $e)
	{
		$xtpl->assign( 'ERROR', $e );
		$xtpl->parse( 'main.error' );
	}
}



$xtpl->parse( 'main' );
$contents = $xtpl->text( 'main' );

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';
