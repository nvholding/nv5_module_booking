<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */


if( ! defined( 'NV_IS_MOD_APPOINTMENT' ) ) die( 'Stop!!!' );
$mon = $nv_Request->get_title( 'month', 'get', '' );
$mon = preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($mon));

if($mon){
	$time_current = convertToTimeStamp( $mon, $default=0, $phour=0, $pmin=0, $second=0 );
}else{
	$time_current = NV_CURRENTTIME;
}

if( empty( $user_info ) )
{
	nv_redirect_location( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA, true );
}

if( ! in_array( $getSetting['default_group_doctors'], $user_info['in_groups'] ))
{
	nv_redirect_location( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA, true );
}

$current_month = date('m', $time_current );
$current_year = date('Y', $time_current );
$last_day_month = date("t", $time_current ); 


$beginday = '01/'. $current_month .'/' . $current_year; 
$endday = str_pad( $last_day_month, 2, "0", STR_PAD_LEFT) . '/'. $current_month .'/' . $current_year; 


$beginday = convertToTimeStamp( $beginday, $default=0, $phour=0, $pmin=0, $second=0 );


$endday = convertToTimeStamp( $endday, 0, 23, 59, 59 );


$dataContent = array();

$result = $db->query( 'SELECT * FROM ' . TABLE_APPOINTMENT_NAME . '_calendar WHERE userid = '. $getUserid .' AND date_start BETWEEN ' . intval( $beginday ) . ' AND ' . intval( $endday ) . ' ORDER BY date_start ASC' );

while( $data = $result->fetch() )
{
	$time = $data['date_start'];
	$data['date_start'] = date('d/m/Y', $data['date_start']);
	$dataContent[$data['date_start']] = $data;
	$dataContent[$data['date_start']]['shift'] = $db->query('SELECT shift FROM ' . TABLE_APPOINTMENT_NAME . '_calendar WHERE userid = '. $getUserid .' AND date_start = '. $time)->fetchAll();
}


$contents =  ThemeViewCalendar( $dataContent,$time_current );
include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
