<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */

if( ! defined( 'NV_MAINFILE' ) )
{
	die( 'Stop!!!' );
}

if( ! nv_function_exists( 'nukevn_block_register' ) )
{ 
	if($module_info['module_file'] != 'booking'){
		function convertToTimeStamp( $time, $default=0, $phour=0, $pmin=0, $second=0 )
		{
			if( preg_match( '/^([0-9]{1,2})[\/|-]([0-9]{1,2})[\/|-]([0-9]{4})$/', $time, $m ) )
			{

				$time = mktime( $phour, $pmin, $second, $m[2], $m[1], $m[3] );
			}
			else
			{
				if( $default )
				{
					$time = NV_CURRENTTIME;
				}
				else
				{
					$time = 0;
				}
			}

			return $time;

		}
	}



	

	function nukevn_block_register( $block_config, $module )
	{
		global  $module_info, $db, $nv_Cache, $user_info, $lang_module, $global_config, $site_mods,$db_config;
		
		
	
	
	// thời gian khám bệnh giờ phút
	$time_work1 = array(
		'gio' => 8,
		'phut' => 0 
	);

	$time_work2 = array(
		'gio' => 8,
		'phut' => 40 
	);

	$time_work3 = array(
		'gio' => 9,
		'phut' => 20 
	);

	$time_work4 = array(
		'gio' => 10,
		'phut' => 0 
	);

	$time_work5 = array(
		'gio' => 10,
		'phut' => 40 
	);

	$time_work6 = array(
		'gio' => 11,
		'phut' => 20 
	);

	$time_work7 = array(
		'gio' => 13,
		'phut' => 30 
	);

	$time_work8 = array(
		'gio' => 14,
		'phut' => 10 
	);


	$time_work9 = array(
		'gio' => 14,
		'phut' => 50 
	);

	$time_work10 = array(
		'gio' => 15,
		'phut' => 30 
	);

	$time_work11 = array(
		'gio' => 16,
		'phut' => 10 
	);

	$time_work12 = array(
		'gio' => 16,
		'phut' => 50 
	);

	$time_work13 = array(
		'gio' => 17,
		'phut' => 30 
	);

	$time_work14 = array(
		'gio' => 18,
		'phut' => 10 
	);

	$time_work15 = array(
		'gio' => 18,
		'phut' => 50 
	);

	$time_work16 = array(
		'gio' => 19,
		'phut' => 30 
	);


	$array_time_works = array(
		'1' => $time_work1,
		'2' => $time_work2,
		'3' => $time_work3,
		'4' => $time_work4,
		'5' => $time_work5,
		'6' => $time_work6,
		'7' => $time_work7,
		'8' => $time_work8,
		'9' => $time_work9,
		'10' => $time_work10,
		'11' => $time_work11,
		'12' => $time_work12,
		'13' => $time_work13,
		'14' => $time_work14,
		'15' => $time_work15,
		'16' => $time_work16
	);
		
		
	
		$module = $block_config['module'];
		
		if( file_exists( NV_ROOTDIR . '/modules/' . $site_mods[$module]['module_file'] . '/language/' . NV_LANG_DATA . '.php' ) )
		{
			include NV_ROOTDIR . '/modules/' . $site_mods[$module]['module_file'] . '/language/' . NV_LANG_DATA . '.php';
		}
		else
		{
			include NV_ROOTDIR . '/modules/' . $site_mods[$module]['module_file'] . '/language/vi.php';
		}
		
		if( file_exists( NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $site_mods[$module]['module_file'] . '/BlockRegister1.tpl' ) )
		{
			$block_theme = $global_config['module_theme'];
		}
		else
		{
			$block_theme = 'default';
		}

		$xtpl = new XTemplate( 'BlockRegister1.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/' . $site_mods[$module]['module_file'] );
		$xtpl->assign( 'LANG', $lang_module );
		$xtpl->assign( 'MODULE_NAME', $module );
		$xtpl->assign( 'TEMPLATE', $block_theme );
		$xtpl->assign( 'NV_LANG_DATA', NV_LANG_DATA );
		
		// kiểm tra tài khoản này có phải là bác sĩ không
		if($user_info['group_id'] == 10)
		$xtpl->assign( 'doctor_id', $user_info['userid'] );
		else
		$xtpl->assign( 'doctor_id', 0);	
	
		// quản trị viên được quyền chọn bác sĩ
		if(defined('NV_IS_ADMIN') or $user_info['group_id'] == 1 or $user_info['group_id'] == 2 or $user_info['group_id'] == 3)
		{
			// lấy danh sách bác sĩ
			$xtpl->parse( 'main.doctor' );
		}

		$services = $nv_Cache->db( 'SELECT service_id, service_name, image FROM ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_service WHERE status=1 ORDER BY weight ASC', '', $module );
		if( !empty( $services ) )
		{
			foreach( $services as $service )
			{	
				$service['image'] = NV_BASE_SITEURL .  NV_UPLOADS_DIR . '/' . $site_mods[$module]['module_upload'] . '/' . $service['image'];
				$xtpl->assign( 'SERVICE', $service );
				$xtpl->parse( 'main.service' );
			}
			
		}
		$dataContent = $nv_Cache->db( 'SELECT config_name, config_value FROM ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_setting', 'config_name', $module );

		$getSetting = array();
		foreach( $dataContent as $row )
		{
			$getSetting[$row['config_name']] = $row['config_value'];
		}
		unset($dataContent);
		$time_current = NV_CURRENTTIME;
		$xtpl->assign( 'CONFIG', $getSetting );
		$xtpl->assign( 'USER', $user_info );
		$xtpl->assign( 'TODAY', date('d/m/Y', $time_current ) );
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
		
		
		if( $list_time )
		{
			foreach($array_time_works as $time)
			{			
				$xtpl->assign( 'TIME', str_pad($time['gio'], 2, "0", STR_PAD_LEFT) . ':' . str_pad($time['phut'], 2, "0", STR_PAD_LEFT) );
				$xtpl->assign( 'CLASS', $class );
				$xtpl->parse( 'main.time1' );
				$xtpl->parse( 'main.time2' );
				++$i;
			}
			
		}
		
		
		
		$xtpl->parse( 'main' );
		return $xtpl->text( 'main' ); 
	}
	
}
if( defined( 'NV_SYSTEM' ) )
{
	global $site_mods, $module_name, $nv_Cache;
	$module = $block_config['module'];
	if( isset( $site_mods[$module] ) )
	{
		$content = nukevn_block_register( $block_config, $module );
	}
}
