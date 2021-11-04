<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */

if( ! defined( 'NV_IS_MOD_APPOINTMENT' ) ) die( 'Stop!!!' );

function ThemeViewCalendar( $dataContent, $time_current )
{
	global $getSetting, $nv_Request, $user_info, $arrayshift, $client_info, $lang_module, $lang_global, $module_info, $module_name, $module_file, $global_config, $user_info, $op;

	$xtpl = new XTemplate( 'ThemeViewCalendar.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'SELFURL', $client_info['selfurl'] );
	$xtpl->assign( 'CONFIG', $getSetting );
	$xtpl->assign( 'ARRAYSHIFT', json_encode( $arrayshift ) );
	$current = date('d/m/Y',$time_current);

	$xtpl->assign( 'CURRENT', $current);
	$xtpl->assign( 'CURRENTINT', $time_current);
	$xtpl->assign( 'LINK_MON', nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=calendar&month=',true));

	$xtpl->assign( 'TOKEN', md5( $nv_Request->session_id . $global_config['sitekey'] ));

	$lastdate = date("t", $time_current ); 

	$currentday = date("d", NV_CURRENTTIME ); 

	$xtpl->assign( 'CURRENT_MONTH', date('m/Y', NV_CURRENTTIME ) );
	$ii = 1;
	for( $day = 1; $day <= $lastdate; ++$day )
	{

		$date_start = str_pad($day, 2, "0", STR_PAD_LEFT) . '/'. date( 'm/Y', $time_current );
		$date_converted = convertToTimeStamp( $date_start, 0, 0, 0, 0 );


		$calendar_id = isset( $dataContent[$date_start] ) ? $dataContent[$date_start]['calendar_id'] : 0;
		$token = md5( $nv_Request->session_id . $global_config['sitekey'] .$calendar_id );
		$disabled = ( ( convertToTimeStamp( $date_start ) + 86399 ) < NV_CURRENTTIME ) ? 'disabled="disabled"' : '';
		$xtpl->assign( 'DATA', array('day'=> $day, 'date_start'=> $date_start, 'calendar_id'=> $calendar_id, 'token'=> $token, 'disabled'=> $disabled ) );

		foreach( $arrayshift as $key => $name )
		{
			$selected='';
			foreach ($dataContent[$date_start]['shift'] as $key1 => $value) {
				if($key == $value['shift']){
					$selected = 'checked';
				}
			}
			$shift = isset( $dataContent[$date_start] ) ? $dataContent[$date_start]['shift'] : 0;

			$list = array('key'=> $key, 'name'=> $name, 'selected'=> $selected, 'disabled'=> $disabled );
			$shift = array('key'=> $key, 'name'=> $name, 'selected'=> $selected, 'disabled'=> $disabled );
			$xtpl->assign( 'SHIFT', $shift);
			$xtpl->assign( 'SHIFT_TIME', $date_converted);
			$xtpl->parse( 'main.data.shift' );
		}
		$xtpl->parse( 'main.data' );
		$ii++;
	}


	/* foreach ( $dataContent as $key => $category ) 
	{
		if ( isset( $dataContent[$key]['content'] ) ) 
		{
			$xtpl->assign('CATEGORY', $category );
			if ( $category['subcatid'] != '' )
			{
				$_arr_subcat = explode( ',', $category['subcatid'] );
				$limit = 0;
				foreach ( $_arr_subcat as $category_id_i )
				{
					if ( $getCategory[$category_id_i]['status'] == 1 )
					{
						$xtpl->assign( 'SUBCAT', $getCategory[$category_id_i] );
						$xtpl->parse( 'main.category.subcatloop' );
						$limit++;
					}
					if ( $limit >= 3 )
					{
						$more = array( 'title' => $lang_module['more'], 'link' => $getCategory[$data['category_id']]['link'] );
						$xtpl->assign( 'MORE', $more );
						$xtpl->parse( 'main.category.subcatmore' );
						break;
					}
				}
			}

	    
			foreach ( $dataContent[$key]['content'] as $loop )
			{
				$loop['title_cut'] = nv_clean60( $loop['class_name'], 40 );
				$loop['type'] = isset( $getTypes[$loop['type_id']] ) ? $getTypes[$loop['type_id']]['title'] : '';
				$loop['teacher'] = isset( $teacherList[$loop['teacher_id']] ) ? $teacherList[$loop['teacher_id']] : '';
				$loop['price'] = priceFormat( $loop['price'] );
				
				$xtpl->assign( 'LOOP', $loop );

				$xtpl->parse( 'main.category.loop' );

			}
			$xtpl->parse( 'main.category' );
		}
	} */

	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );
}

function ThemeViewPatientPrint( $userPatient, $doctorsList, $dataContent )
{
	global $getSetting, $nv_Request, $category_id, $getCategory, $getTypes, $client_info, $lang_module, $lang_global, $module_info, $module_name, $module_file, $global_config, $user_info, $op;
	$xtpl = new XTemplate( 'ThemeViewPatientPrint.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'SELFURL', $client_info['selfurl'] );
	$xtpl->assign( 'CONFIG', $getSetting );

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



function ThemeViewPatient_Follow( $dataContent, $generate_page, $data)
{
	global $getSetting, $nv_Request, $category_id, $getCategory, $getTypes, $client_info, $lang_module, $lang_global, $module_info, $module_name, $module_file, $global_config, $user_info, $op;
	$xtpl = new XTemplate( 'ThemeViewPatientFollow.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'SELFURL', $client_info['selfurl'] );
	$xtpl->assign( 'CONFIG', $getSetting );
	$xtpl->assign( 'USER', $dataContent['user'] );
	$xtpl->assign( 'MODULE_NAME', $module_name );
	
	$xtpl->assign( 'DATA', $data );
	
	$xtpl->assign( 'doctor_id', $user_info['userid'] );
	// lấy chi nhanh bác sĩ này
	$branch_id = get_branch_id($user_info['userid']);
	$xtpl->assign( 'branch_id', $branch_id );
	
	$xtpl->assign( 'URLPATIENT', nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op, true ) );
	
	// lấy danh sách chi nhánh ra
	$get_chinhanh = getBranch();
	foreach($get_chinhanh as $chinhanh)
	{
		if($chinhanh['branch_id'] == $data['chinhanh'])
		{
			$chinhanh['selected'] = 'selected=selected';
		}
		else
		{
			$chinhanh['selected'] = '';
		}
		
		$xtpl->assign( 'chinhanh', $chinhanh );
		$xtpl->parse( 'main.chinhanh' );
	}

	
	if( ! empty( $dataContent ) )
	{

		foreach( $dataContent as $item )
		{
			
			// $item['date_added'] = nv_date( 'd/m/Y', $item['date_added'] );
			// $item['date_modified'] = nv_date( 'd/m/Y', $item['date_modified'] );
			// $item['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $item['question_id'] );
			
			$item['url_muagoidichvu'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=buy_service&userid=' . $item['userid'];
			
			$xtpl->assign( 'LOOP', $item );
			$xtpl->parse( 'main.data.loop' );
		}

		$xtpl->parse( 'main.data' );
	}
	else
	{
		$xtpl->parse( 'main.no_data' );
	}
	
	
	// kiểm tra có phải là bác sĩ
	if($user_info['group_id'] == $getSetting['default_group_doctors'])
	{
		$xtpl->parse( 'main.doctor' );
	}
	
	if( ! empty( $generate_page ) )
	{
		$xtpl->assign( 'GENERATE_PAGE', $generate_page );
		$xtpl->parse( 'main.generate_page' );
	}


	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );
}


function ThemeViewPatient( $dataContent, $generate_page, $keyword)
{
	global $getSetting, $nv_Request, $category_id, $getCategory, $getTypes, $client_info, $lang_module, $lang_global, $module_info, $module_name, $module_file, $global_config, $user_info, $op;
	$xtpl = new XTemplate( 'ThemeViewPatient.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'SELFURL', $client_info['selfurl'] );
	$xtpl->assign( 'CONFIG', $getSetting );
	$xtpl->assign( 'USER', $dataContent['user'] );
	$xtpl->assign( 'MODULE_NAME', $module_name );
	$xtpl->assign( 'keyword', $keyword );
	
	$xtpl->assign( 'doctor_id', $user_info['userid'] );
	// lấy chi nhanh bác sĩ này
	$branch_id = get_branch_id($user_info['userid']);
	$xtpl->assign( 'branch_id', $branch_id );
	
	$xtpl->assign( 'URLPATIENT', nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op, true ) );
	
	
	
	
	if( ! empty( $dataContent ) )
	{

		foreach( $dataContent as $item )
		{
			
			// $item['date_added'] = nv_date( 'd/m/Y', $item['date_added'] );
			// $item['date_modified'] = nv_date( 'd/m/Y', $item['date_modified'] );
			// $item['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $item['question_id'] );
			
			$item['url_muagoidichvu'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=buy_service&userid=' . $item['userid'];
			
			$xtpl->assign( 'LOOP', $item );
			$xtpl->parse( 'main.data.loop' );
		}

		$xtpl->parse( 'main.data' );
	}
	else
	{
		$xtpl->parse( 'main.no_data' );
	}
	
	// kiểm tra có phải là bác sĩ
	if($user_info['group_id'] == $getSetting['default_group_doctors'])
	{
		$xtpl->parse( 'main.doctor' );
	}
	
	if( ! empty( $generate_page ) )
	{
		$xtpl->assign( 'GENERATE_PAGE', $generate_page );
		$xtpl->parse( 'main.generate_page' );
	}


	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );
}


function ThemeViewPatientUser_User( $userPatient, $doctorsList, $dataContent, $generatePage, $doctors)
{
	global $getSetting, $nv_Request, $arrayGender, $category_id, $getCategory,  $client_info, $lang_module, $lang_global, $module_info, $module_name, $module_file, $global_config, $user_info, $op;

	$xtpl = new XTemplate( 'ThemeViewPatientUser_User.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'SELFURL', $client_info['selfurl'] );
	$xtpl->assign( 'CONFIG', $getSetting );
	$xtpl->assign( 'NV_ASSETS_DIR', NV_ASSETS_DIR );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );

	// $xtpl->assign( 'URLPATIENT', nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op, true ) );
	$xtpl->assign( 'URLPATIENT',NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op.'&action=print&token='.$userPatient['token'] . '&userid=' . $userPatient['userid'] );

	
	
	$xtpl->assign( 'USER', $userPatient );
	$xtpl->assign( 'DOCTORS', $user_info );
	$xtpl->assign( 'DATE_ADDED', date('d/m/Y', NV_CURRENTTIME) ); 
	
	// danh sách bác sĩ
	foreach($doctors as $doctor)
	{
		$xtpl->assign( 'doctor', $doctor );
		$xtpl->parse( 'main.doctor' );
	}
	
	
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
			$item['price'] = price_format( $item['price'] );
			$item['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $item['question_id'] );
			$xtpl->assign( 'LOOP', $item );
			$xtpl->parse( 'main.data.loop' );
		}
		
		$xtpl->parse( 'main.data' );
	}
	else
	{
		$xtpl->parse( 'main.no_data' );
	}
	if($userPatient['confess'] == 'Quý khách'){
		$xtpl->assign( 'SELECTED1', 'selected' );
	}else if($userPatient['confess'] == 'Cô'){
		$xtpl->assign( 'SELECTED2', 'selected' );
	}else if($userPatient['confess'] == 'Chú'){
		$xtpl->assign( 'SELECTED3', 'selected' );
	}else if($userPatient['confess'] == 'Anh'){
		$xtpl->assign( 'SELECTED4', 'selected' );
	}else if($userPatient['confess'] == 'Chị'){
		$xtpl->assign( 'SELECTED5', 'selected' );
	}

	$list_patient_group = get_group_patient();
	foreach( $list_patient_group as $key => $name )
	{
		if(!$userPatient['patient_group']){
			$userPatient['patient_group'] = 0;
		}
		$xtpl->assign( 'PATIENT_GROUP', array('key'=> $key, 'title'=> $name['title'], 'selected'=> ( $userPatient['patient_group'] == $key ) ? 'selected="selected"' : '') );
		$xtpl->parse( 'main.patient_group' );
	}

	$getServicePackage = getServicePackage();

	foreach( $getServicePackage as $key => $name )
	{
		$xtpl->assign( 'SERVICEPACKAGE', array('key'=> $key, 'name'=> $name['title'], 'selected'=> ( $userPatient['service_package_id'] == $key ) ? 'selected="selected"' : '') );
		$xtpl->parse( 'main.servicepackage' );
	}

	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );
}




function ThemeViewPatientUser( $userPatient, $doctorsList, $dataContent, $generate_page, $doctors)
{
	global $getSetting, $nv_Request, $arrayGender, $category_id, $getCategory,  $client_info, $lang_module, $lang_global, $module_info, $module_name, $module_file, $global_config, $user_info, $op;
	
	if($user_info['group_id'] == $getSetting['default_group_doctors'])
	{
		$xtpl = new XTemplate( 'ThemeViewPatientUser_doctor.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	}
	else
	{
		$xtpl = new XTemplate( 'ThemeViewPatientUser.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	}
	
	
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'SELFURL', $client_info['selfurl'] );
	$xtpl->assign( 'CONFIG', $getSetting );
	$xtpl->assign( 'NV_ASSETS_DIR', NV_ASSETS_DIR );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );


	// $xtpl->assign( 'URLPATIENT', nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op, true ) );
	$xtpl->assign( 'URLPATIENT',NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op.'&action=print&token='.$userPatient['token'] . '&userid=' . $userPatient['userid'] );

	// quyền xem thông tin của bác sĩ
	if($user_info['group_id'] == $getSetting['default_group_doctors'])
	{
		$userPatient['phone'] = '******';
		$userPatient['email'] = '******';
		$userPatient['phone'] = '******';
		$userPatient['phone'] = '******';
		$userPatient['address'] = '******';
		$userPatient['other_contact'] = '******';
		$userPatient['note'] = '******';
	}
	
	$xtpl->assign( 'USER', $userPatient );
	$xtpl->assign( 'DOCTORS', $user_info );
	$xtpl->assign( 'DATE_ADDED', date('d/m/Y', NV_CURRENTTIME) ); 
	
	// danh sách bác sĩ
	foreach($doctors as $doctor)
	{
		$xtpl->assign( 'doctor', $doctor );
		$xtpl->parse( 'main.doctor' );
	}
	
	//print_r( $userPatient);die;
	
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
			$item['price'] = price_format( $item['price'] );
			$item['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $item['question_id'] );
			$xtpl->assign( 'LOOP', $item );
			$xtpl->parse( 'main.data.loop' );
		}
		
		$xtpl->parse( 'main.data' );
	}
	else
	{
		$xtpl->parse( 'main.no_data' );
	}
	if($userPatient['confess'] == 'Quý khách'){
		$xtpl->assign( 'SELECTED1', 'selected' );
	}else if($userPatient['confess'] == 'Cô'){
		$xtpl->assign( 'SELECTED2', 'selected' );
	}else if($userPatient['confess'] == 'Chú'){
		$xtpl->assign( 'SELECTED3', 'selected' );
	}else if($userPatient['confess'] == 'Anh'){
		$xtpl->assign( 'SELECTED4', 'selected' );
	}else if($userPatient['confess'] == 'Chị'){
		$xtpl->assign( 'SELECTED5', 'selected' );
	}

	$list_patient_group = get_group_patient();
	foreach( $list_patient_group as $key => $name )
	{
		if(!$userPatient['patient_group']){
			$userPatient['patient_group'] = 0;
		}
		$xtpl->assign( 'PATIENT_GROUP', array('key'=> $key, 'title'=> $name['title'], 'selected'=> ( $userPatient['patient_group'] == $key ) ? 'selected="selected"' : '') );
		$xtpl->parse( 'main.patient_group' );
	}

	$getServicePackage = getServicePackage();

	foreach( $getServicePackage as $key => $name )
	{
		$xtpl->assign( 'SERVICEPACKAGE', array('key'=> $key, 'name'=> $name['title'], 'selected'=> ( $userPatient['service_package_id'] == $key ) ? 'selected="selected"' : '') );
		$xtpl->parse( 'main.servicepackage' );
	}
	
	if (!empty($generate_page)) {
        $xtpl->assign('NV_GENERATE_PAGE', $generate_page);
        $xtpl->parse('main.generate_page');
    }

	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );
}



function ThemeViewPatientUserSearch_User( $userPatient, $doctorsList, $dataContent, $generate_page )
{
	global $getSetting, $nv_Request, $category_id, $getCategory, $getTypes, $client_info, $lang_module, $lang_global, $module_info, $module_name, $module_file, $global_config, $user_info, $op;
	$xtpl = new XTemplate( 'ThemeViewPatientUserSearch_User.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'SELFURL', $client_info['selfurl'] );
	$xtpl->assign( 'CONFIG', $getSetting );
	$xtpl->assign( 'USER', $userPatient ); 
	$xtpl->assign( 'MODULE_NAME', $module_name ); 
	
	$xtpl->assign( 'doctor_id', $user_info['userid'] );
	// lấy chi nhanh bác sĩ này
	$branch_id = get_branch_id($user_info['userid']);
	$xtpl->assign( 'branch_id', $branch_id );
	
	
	$xtpl->assign( 'DATE_ADDED', date('d/m/Y', NV_CURRENTTIME) ); 

	if( ! empty( $dataContent ) )
	{

		foreach( $dataContent as $item )
		{
			$item['price'] = number_format($item['price'],0,",",".");
			$item['date_added'] = nv_date( 'd/m/Y', $item['date_added'] );
			// $item['date_modified'] = nv_date( 'd/m/Y', $item['date_modified'] );
			// $item['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $item['question_id'] );
			$item['doctors'] = isset( $doctorsList[$item['doctors_id']] ) ? $doctorsList[$item['doctors_id']]['full_name'] : 'N/A';
			$xtpl->assign( 'LOOP', $item );
			if($item['doctors_id'] == $user_info['userid'])
			{
				$xtpl->parse( 'main.data.loop.edit' );
			}
			$xtpl->parse( 'main.data.loop' );
		}
		
		$xtpl->parse( 'main.data' );
	}
	else
	{
		$xtpl->parse( 'main.no_data' );
	}

	
	if( ! empty( $generate_page ) )
	{
		$xtpl->assign( 'GENERATE_PAGE', $generate_page );
		$xtpl->parse( 'main.generate_page' );
	}

	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );
}



function ThemeViewPatientUserSearch( $userPatient, $doctorsList, $dataContent, $generate_page )
{
	global $getSetting, $nv_Request, $category_id, $getCategory, $getTypes, $client_info, $lang_module, $lang_global, $module_info, $module_name, $module_file, $global_config, $user_info, $op;
	$xtpl = new XTemplate( 'ThemeViewPatientUserSearch.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'SELFURL', $client_info['selfurl'] );
	$xtpl->assign( 'CONFIG', $getSetting );
	$xtpl->assign( 'USER', $userPatient ); 
	$xtpl->assign( 'MODULE_NAME', $module_name ); 
	
	$xtpl->assign( 'doctor_id', $user_info['userid'] );
	// lấy chi nhanh bác sĩ này
	$branch_id = get_branch_id($user_info['userid']);
	$xtpl->assign( 'branch_id', $branch_id );
	
	
	$xtpl->assign( 'DATE_ADDED', date('d/m/Y', NV_CURRENTTIME) ); 
	
	

	if( ! empty( $dataContent ) )
	{

		foreach( $dataContent as $item )
		{
			$item['price'] = number_format($item['price'],0,",",".");
			$item['date_added'] = nv_date( 'd/m/Y', $item['date_added'] );
			// $item['date_modified'] = nv_date( 'd/m/Y', $item['date_modified'] );
			// $item['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $item['question_id'] );
			$item['doctors'] = isset( $doctorsList[$item['doctors_id']] ) ? $doctorsList[$item['doctors_id']]['full_name'] : 'N/A';
			$xtpl->assign( 'LOOP', $item );
			if($item['doctors_id'] == $user_info['userid'])
			{
				$xtpl->parse( 'main.data.loop.edit' );
			}
			
			// quản trị được quyền xóa trị liệu
			if(defined('NV_IS_ADMIN') or $user_info['group_id'] == 1 or $user_info['group_id'] ==2 or $user_info['group_id'] ==3)
			{
				$xtpl->parse( 'main.data.loop.delete_trilieu' );
				$xtpl->parse( 'main.data.loop.delete_trilieu_title' );
			}
			
			$xtpl->parse( 'main.data.loop' );
		}
		
		$xtpl->parse( 'main.data' );
	}
	else
	{
		$xtpl->parse( 'main.no_data' );
	}

	
	if( ! empty( $generate_page ) )
	{
		$xtpl->assign( 'GENERATE_PAGE', $generate_page );
		$xtpl->parse( 'main.generate_page' );
	}

	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );
}

function ThemeViewPatientSearch( $dataContent, $generate_page )
{
	global $getSetting, $nv_Request, $category_id, $getCategory, $getTypes, $client_info, $lang_module, $lang_global, $module_info, $module_name, $module_file, $global_config, $user_info, $op;
	$xtpl = new XTemplate( 'ThemeViewPatientSearch.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'SELFURL', $client_info['selfurl'] );
	$xtpl->assign( 'CONFIG', $getSetting );
	
	$xtpl->assign( 'MODULE_NAME', $module_name ); 
	
	$xtpl->assign( 'doctor_id', $user_info['userid'] );
	// lấy chi nhanh bác sĩ này
	$branch_id = get_branch_id($user_info['userid']);
	$xtpl->assign( 'branch_id', $branch_id );
	
	// kiểm tra có phải là bác sĩ
	if($user_info['group_id'] == $getSetting['default_group_doctors'])
	{
		$xtpl->parse( 'main.doctor' );
	}

	
	if( ! empty( $dataContent ) )
	{

		foreach( $dataContent as $item )
		{
			
			// $item['date_added'] = nv_date( 'd/m/Y', $item['date_added'] );
			// $item['date_modified'] = nv_date( 'd/m/Y', $item['date_modified'] );
			// $item['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $item['question_id'] );
			
			$item['url_muagoidichvu'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=buy_service&userid=' . $item['userid'];
			
			$xtpl->assign( 'LOOP', $item );
			$xtpl->parse( 'main.data.loop' );
		}

		$xtpl->parse( 'main.data' );
	}
	else
	{
		$xtpl->parse( 'main.no_data' );
	}

	
	
	if( ! empty( $generate_page ) )
	{
		$xtpl->assign( 'GENERATE_PAGE', $generate_page );
		$xtpl->parse( 'main.generate_page' );
	}

	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );
}


function ThemeViewHistoryUser( $userPatient, $doctorsList, $dataContent, $generatePage )
{
	global $getSetting, $nv_Request, $category_id, $getCategory,  $client_info, $lang_module, $lang_global, $module_info, $module_name, $module_file, $global_config, $user_info, $op;
	$xtpl = new XTemplate( 'ThemeViewHistoryUser.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'SELFURL', $client_info['selfurl'] );
	$xtpl->assign( 'CONFIG', $getSetting );
	$xtpl->assign( 'USER', $userPatient );
	$xtpl->assign( 'DOCTORS', $user_info );
	$xtpl->assign( 'DATE_ADDED', date('d/m/Y', NV_CURRENTTIME) ); 
	
	if( ! empty( $dataContent ) )
	{

		foreach( $dataContent as $item )
		{
			$item['date_added'] = nv_date( 'd/m/Y', $item['date_added'] );
			$item['doctors'] = isset( $doctorsList[$item['doctors_id']] ) ? $doctorsList[$item['doctors_id']]['full_name'] : 'N/A';
			$item['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $item['question_id'] );
			$xtpl->assign( 'LOOP', $item );
			$xtpl->parse( 'main.data.loop' );
		}
		
		$xtpl->parse( 'main.data' );
	}
	else
	{
		$xtpl->parse( 'main.no_data' );
	}


	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );
}

function ThemeViewHistoryUserSearch( $userPatient, $doctorsList, $dataContent, $generate_page )
{
	global $getSetting, $nv_Request, $category_id, $getCategory, $getTypes, $client_info, $lang_module, $lang_global, $module_info, $module_name, $module_file, $global_config, $user_info, $op;
	$xtpl = new XTemplate( 'ThemeViewHistoryUserSearch.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'SELFURL', $client_info['selfurl'] );
	$xtpl->assign( 'CONFIG', $getSetting );
	$xtpl->assign( 'USER', $userPatient ); 
	$xtpl->assign( 'DATE_ADDED', date('d/m/Y', NV_CURRENTTIME) ); 

	if( ! empty( $dataContent ) )
	{

		foreach( $dataContent as $item )
		{
			
			$item['date_added'] = nv_date( 'd/m/Y', $item['date_added'] );
			// $item['date_modified'] = nv_date( 'd/m/Y', $item['date_modified'] );
			$item['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $item['question_id'] );
			$item['doctors'] = isset( $doctorsList[$item['doctors_id']] ) ? $doctorsList[$item['doctors_id']]['full_name'] : 'N/A';
			$xtpl->assign( 'LOOP', $item );
			$xtpl->parse( 'main.data.loop' );
		}
		
		$xtpl->parse( 'main.data' );
	}
	else
	{
		$xtpl->parse( 'main.no_data' );
	}

	
	if( ! empty( $generate_page ) )
	{
		$xtpl->assign( 'GENERATE_PAGE', $generate_page );
		$xtpl->parse( 'main.generate_page' );
	}

	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );
}
