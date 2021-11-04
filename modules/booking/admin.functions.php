<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2013 Webdep24.com. All rights reserved
 * @Blog  http://dangdinhtu.com
 * @License GNU/GPL version 2 or any later version
 * @Createdate  Wed, 21 Jan 2015 14:00:59 GMT
 */

if( ! defined( 'NV_ADMIN' ) or ! defined( 'NV_MAINFILE' ) or ! defined( 'NV_IS_MODADMIN' ) ) die( 'Stop!!!' );


define( 'NV_IS_FILE_ADMIN', true );

define( 'TABLE_APPOINTMENT_NAME', NV_PREFIXLANG . '_' . $module_data ); 

define( 'ACTION_METHOD', $nv_Request->get_string( 'action', 'get,post', '' ) ); 


$array_status = array( '0' => $lang_module['disabled'], '1' => $lang_module['enable'] );

$array_active = array(
	'0'=> $lang_module['doctors_active0'],
	'1'=> $lang_module['doctors_active1']
);


require_once NV_ROOTDIR . '/modules/' . $module_file . '/global.functions.php';



function ThemeViewPatientPrint( $userPatient, $doctorsList, $dataContent, $logo )
{
	global $getSetting, $nv_Request, $client_info, $lang_module, $lang_global, $module_info, $module_name, $module_file, $global_config, $user_info, $op;
	

	$xtpl = new XTemplate( 'ThemeViewPatientPrint.tpl', NV_ROOTDIR . '/themes/' . $global_config['site_theme'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'SELFURL', $client_info['selfurl'] );
	$xtpl->assign( 'CONFIG', $getSetting );
	$xtpl->assign( 'LOGO', $logo );

	$xtpl->assign( 'USER', $userPatient );
	foreach( $arrayGender as $key => $name )
	{

		$xtpl->assign( 'GENDER', array('key'=> $key, 'name'=> $name, 'selected'=> ( $userPatient['gender'] == $key ) ? 'selected="selected"' : '') );
		$xtpl->parse( 'main.gender' );
	}
	if( ! empty( $dataContent ) )
	{

		foreach( $dataContent as $item )
		{
			
			$item['date_added'] = nv_date( 'd/m/Y', $item['date_added'] );
			$item['doctors'] = isset( $doctorsList[$item['doctors_id']] ) ? $doctorsList[$item['doctors_id']]['full_name'] : 'N/A';
			$item['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $item['question_id'] );
			$xtpl->assign( 'LOOP', $item );
			$xtpl->parse( 'main.loop' );
		}
		
	}

	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );
}
