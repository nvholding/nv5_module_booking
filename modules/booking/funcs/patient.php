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

// xử lý xóa thông tin trị liệu
if($mod == 'delete_patient_appointment')
{
	$id = $nv_Request->get_int('id_delete_patient_appointment', 'post,get', 0);
	
	// lấy thông tin trị liệu
	$where = '';
	
	if($user_info['group_id'] == $getSetting['default_group_doctors'] or defined('NV_IS_ADMIN') or $user_info['group_id'] == 1 or $user_info['group_id'] ==2 or $user_info['group_id'] ==3)
	{
		if($user_info['group_id'] == $getSetting['default_group_doctors'])
		{
			$where = ' AND doctors_id =' . $user_info['group_id'];
		}
	}
	else{
		$json[] = ['status'=>'ERROR', 'text'=>'Xóa trị liệu thất bại!'];
		print_r( json_encode( $json[0] ) );
		die();
	}
		
	
	
	
	$info = $db->query('SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_patient_appointment WHERE id ='. $id . $where)->fetch();
	
	if($info)
	{
		// xử lý xóa thông tin
		$db->query('DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_patient_appointment WHERE id ='. $id . $where);
	}
	
	$json[] = ['status'=>'OK', 'text'=>'Xóa trị liệu thành công!'];
	print_r( json_encode( $json[0] ) );
	die();


}

if ( $mod == 'update_benh_nhan' ) {

	$data['confess'] = $nv_Request->get_string('confess', 'post', '');
	$data['patient_group'] = $nv_Request->get_int('patient_group', 'post', 0);
	$data['full_name'] = $nv_Request->get_string('full_name', 'post', '');
	$data['gender'] = $nv_Request->get_string('gender', 'post', '');
	$data['birthday'] = $nv_Request->get_string('birthday', 'post', '');
	$data['phone'] = $nv_Request->get_string('phone', 'post', '');
	$data['email'] = $nv_Request->get_string('email', 'post', '');
	$data['patient_address'] = $nv_Request->get_string('patient_address', 'post', '');
	$data['work'] = $nv_Request->get_string('work', 'post', '');
	$data['other_contact'] = $nv_Request->get_string('other_contact', 'post', '');
	$data['history'] = $nv_Request->get_string('history', 'post', '');
	$data['expect'] = $nv_Request->get_string('expect', 'post', '');
	$data['note'] = $nv_Request->get_string('note', 'post', '');
	$data['userid'] = $nv_Request->get_int('userid', 'post', 0);
	
	$birthday = 0;
	if( preg_match( '/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $data['birthday'], $m ) )
	{

		$birthday = mktime( 0, 0, 1, $m[2], $m[1], $m[3] );
	}
	
	// cập nhật database
	
	$db->query( 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_patient SET confess = "'. $data['confess'] .'", gender ="'. $data['gender'] .'", work = "' . $data['work'] . '", history = "' . $data['history'] . '", expect = "' . $data['expect'] . '", note = "' . $data['note'] . '", address = "' . $data['patient_address'] . '", birthday = ' . $birthday . ', full_name = "' . $data['full_name'] . '", patient_group = ' . $data['patient_group'] . ',other_contact = "' . $data['other_contact'] . '", phone = "' . $data['phone'] . '"  WHERE userid ="' . $data['userid'] .'"');
					
					
	$stmt = $db->prepare( 'UPDATE ' . NV_USERS_GLOBALTABLE . '  SET 
						first_name="' . $value_user['name'] . '",
						last_name="",
						email="' . $data['email'] . '",
						phone="' . $data['phone'] . '",
						address="' . $data['patient_address'] . '",
						birthday="' . $birthday . '"
						WHERE userid=' . intval( $data['userid'] ) );
						
					$stmt->execute();
	
	// kết thúc cập nhật database

	$json[] = ['status'=>'OK', 'text'=>'Cập nhật thông tin khách hàng thành công!'];
	print_r( json_encode( $json[0] ) );
	die();

}
if( empty( $user_info ) )
{
	nv_redirect_location( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA, true );
}


if($user_info['group_id'] != $getSetting['default_group_doctors'] and !defined('NV_IS_ADMIN') and $user_info['group_id'] != 1 and $user_info['group_id'] !=2 and $user_info['group_id'] !=3)
{
	nv_redirect_location( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA, true );
}


if( ACTION_METHOD == 'search' )
{
	
	$data['keyword'] = $nv_Request->get_string( 'keyword', 'post', '');
	$page = $nv_Request->get_int( 'page', 'post',1);
	$perpage = 50;

	$implode = array();

	if( $data['keyword'] )
	{
		$implode[] = '(p.full_name LIKE \'%' . $db_slave->dblikeescape( $data['keyword'] ) . '%\' OR p.patient_code LIKE \'' . $db_slave->dblikeescape( $data['keyword'] ) . '\')';
		
	}


	$sql =  NV_USERS_GLOBALTABLE . ' u INNER JOIN ' . TABLE_APPOINTMENT_NAME . '_patient p ON u.userid = p.userid';

	
	if( $implode )
	{
		$sql .= ' WHERE '  . implode( ' AND ', $implode );
	}
	

	$num_items = $db->query( 'SELECT COUNT(*) FROM ' . $sql )->fetchColumn();

	$base_url = NV_BASE_SITEURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '='. $op .'&action=search&keyword=' . $data['keyword'] .'&per_page=' . $perpage;

	$db->sqlreset()->select( 'u.userid, u.username, p.full_name, u.email, p.gender, p.birthday, p.address, p.patient_code, p.confess' )->from( $sql )->limit( $perpage )->offset( ( $page - 1 ) * $perpage );
	
	

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
		$rows['link'] = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '/' . $rows['userid'], true );
		
		// số lần khám bệnh còn lại
		// lấy thông tin số lần khám còn lại của userid
		$get_parent_info = get_parent_info($rows['userid']);
		$rows['kham_conlai'] = $get_parent_info['kham_conlai'];
		
		
		$dataContent[] = $rows;
		++$stt;
	}
	$generate_page = nv_generate_page( $base_url, $num_items, $perpage, $page, 'true', 'false', 'nv_urldecode_ajax', 'showcontent');

	$json['template'] = ThemeViewPatientSearch( $dataContent, $generate_page );	
	

	nv_jsonOutput( $json );
}

if( ACTION_METHOD == 'insertpatient' )
{

	$json = array();
	
	$data['id'] = $nv_Request->get_int( 'id', 'post', 0 );
	
	$data['patient_id'] = $nv_Request->get_int( 'patient_id', 'post', 0 );
	$data['appointment_id'] = $nv_Request->get_int( 'appointment_id', 'post', 0 );
	$data['token'] = $nv_Request->get_title( 'token', 'post', '' );
	$data['price'] = $nv_Request->get_title( 'price', 'post', '' );
	$data['price'] = preg_replace("/[^0-9\/]*/", "", $data['price'] );

	$data['typemedicine'] = $nv_Request->get_title( 'typemedicine', 'post', '' );
	$data['blood_pressure'] = $nv_Request->get_title( 'blood_pressure', 'post', '' );
	$data['patient_result'] = $nv_Request->get_string( 'patient_result', 'post', '');
	$data['doctors_id'] = $nv_Request->get_string( 'doctors_id', 'post', 0);
	
	// lấy chi nhánh bác sĩ thuộc	
	//$data['branch_id'] = get_branch_id($data['doctors_id']);
	
	//cho date_added = 0 để phân biệt với các bệnh nhân có sẵn
	$date_added = $nv_Request->get_title('date_added', 'post', '');
    if (preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $date_added, $m)) {
        $phour = $nv_Request->get_int('phour', 'post', 0);
        $pmin = $nv_Request->get_int('pmin', 'post', 0);
       $data['date_added'] = mktime($phour, $pmin, 0, $m[2], $m[1], $m[3]);
    } else {
        $data['date_added'] = NV_CURRENTTIME;
    }
	
	
	$data['branch_id'] = get_branch_id_new($data['doctors_id'], $data['date_added'] );
	
	
	$data['date_modified'] = NV_CURRENTTIME;
	$data['value_record'] = $nv_Request->get_int( 'value_record', 'post,get', 0);
	
	if(!$data['doctors_id'])
	{
		$json['error'] = 'Chưa chọn bác sĩ điều trị';
			
		nv_jsonOutput( $json );
	}
	
	if(!$data['id'])
	{
		
		$khambenh = khambenh($data['appointment_id'], $data['patient_id']);
	
		if(!$khambenh)
		{
			$json['error'] = 'Thêm liệu trình trị liệu thất bại. Bạn vui lòng mua thêm gói dịch vụ';
			
			nv_jsonOutput( $json );
			
		}
		// kết thúc
		
		
		// insert
		$stmt = $db->prepare( 'INSERT INTO ' . TABLE_APPOINTMENT_NAME . '_patient_appointment SET 
						patient_id=' . intval( $data['patient_id'] ) . ',
						appointment_id=' . intval( $data['appointment_id'] ) . ',
						doctors_id=' . intval( $data['doctors_id'] ) . ',
						branch_id=' . intval( $data['branch_id'] ) . ',
						price=:price,
						typemedicine=:typemedicine,
						blood_pressure=:blood_pressure,
						patient_result=:patient_result,
						date_added=' . intval( $data['date_added'] ));
					$stmt->bindParam( ':price', $data['price'], PDO::PARAM_STR );
					$stmt->bindParam( ':typemedicine', $data['typemedicine'], PDO::PARAM_STR );
					$stmt->bindParam( ':blood_pressure', $data['blood_pressure'], PDO::PARAM_STR );
					$stmt->bindParam( ':patient_result', $data['patient_result'], PDO::PARAM_STR );
					
					if( !$stmt->execute() )
					{
						$json['error'] = $lang_module['patient_error_secutiry'];
					}
		
	}
	else
	{
		// cập nhật
		$stmt = $db->prepare( 'UPDATE ' . TABLE_APPOINTMENT_NAME . '_patient_appointment SET 
						price=:price,
						date_added=:date_added,
						doctors_id=:doctors_id,
						blood_pressure=:blood_pressure,
						typemedicine=:typemedicine,
						branch_id=:branch_id,
						patient_result=:patient_result
						WHERE id=' . $data['id'] );
					$stmt->bindParam( ':price', $data['price'], PDO::PARAM_STR );
					$stmt->bindParam( ':date_added', $data['date_added'], PDO::PARAM_INT );
					$stmt->bindParam( ':doctors_id', $data['doctors_id'], PDO::PARAM_INT );
					$stmt->bindParam( ':blood_pressure', $data['blood_pressure'], PDO::PARAM_STR );
					$stmt->bindParam( ':typemedicine', $data['typemedicine'], PDO::PARAM_STR );
					$stmt->bindParam( ':branch_id', $data['branch_id'], PDO::PARAM_INT );
					$stmt->bindParam( ':patient_result', $data['patient_result'], PDO::PARAM_STR );

					if( !$stmt->execute() )
					{
						$json['error'] = $lang_module['patient_error_secutiry'];
					}
		
	}
	
	if(empty($json['error']))
		$json['success'] = $lang_module['patient_update_success'];
	
	nv_jsonOutput( $json );
	
	
}

if( ACTION_METHOD == 'update' )
{
	$json = array();
	$data['userid'] = $nv_Request->get_title( 'userid', 'post', '' );
	$data['token'] = $nv_Request->get_title( 'token', 'post', '' );
	$full_name = trim( nv_substr( $nv_Request->get_title( 'full_name', 'post', '', '' ), 0, 250 ) );
	$data['phone'] =trim(  nv_substr( $nv_Request->get_title( 'phone', 'post', '', '' ), 0, 250 ));
	$data['md5username'] = nv_md5safe($data['phone']);
	$data['email'] = trim( nv_substr( $nv_Request->get_title( 'email', 'post', '', '' ), 0, 250 ));
	$data['address'] = trim( nv_substr( $nv_Request->get_title( 'address', 'post', '', '' ), 0, 250 ));
	$data['gender'] = $nv_Request->get_title( 'gender', 'post', '', '' );
	$data['birthday'] = $nv_Request->get_title( 'birthday', 'post', '' );
	$data['medical_history'] = $nv_Request->get_title( 'medical_history', 'post', '' );
	$data['aspirations_treatment'] = $nv_Request->get_title( 'aspirations_treatment', 'post', '' );
	$data['y_benh_tdcs'] = $nv_Request->get_title( 'y_benh_tdcs', 'post', '' );
	$success = 0;  
	if ( $data['token'] ==  md5( $nv_Request->session_id . $global_config['sitekey'] . $data['userid'] ) )
	{
		$full_name = array_map('trim', explode( ' ', $full_name ));
		$full_name = array_filter( $full_name );
		if( !empty( $full_name ) )
		{
			$data['first_name'] = $full_name[count( $full_name ) -1];
			unset( $full_name[count( $full_name ) -1] );
			$data['last_name'] = trim( implode( ' ', $full_name ) );
		}
		if( empty( $data['full_name'] ) ) $error['full_name'] = $lang_module['patient_error_full_name'];
		if( empty( $data['address'] ) ) $error['address'] = $lang_module['patient_error_address'];
		if( ( $error_username = nv_check_valid_login( $data['phone'], $global_config['nv_unickmax'], $global_config['nv_unickmin'] ) ) != '' )
		{
			$error['phone'] =  $error_username;
		}
		else if( "'" . $data['phone'] . "'" != $db->quote( $data['phone'] ) )
		{
			$json['error'] =  sprintf( $lang_module['patient_account_deny_name'], $data['phone'] );
		}
		else
		{
			
			// Thực hiện câu truy vấn để kiểm tra username đã tồn tại chưa.
			$stmt = $db->prepare( 'SELECT userid FROM ' . NV_USERS_GLOBALTABLE . ' WHERE md5username= :md5username AND userid != ' .intval( $data['userid'] ) );
			$stmt->bindParam( ':md5username', $data['md5username'], PDO::PARAM_STR );
			$stmt->execute();
			$query_error_username = $stmt->fetchColumn();
			if( $query_error_username )
			{
				$json['error'] = $lang_module['patient_error_phone_exist'];
			}
		}
		

		$error_xemail = nv_check_valid_email( $data['email'], true );
		if( $error_xemail[0] != '' )
		{
			$error['email'] = $error_xemail[0];
		}
		$data['email'] = $error_xemail[1];
		
		if( !isset( $error['email'] ) )
		{
			
			// Thực hiện câu truy vấn để kiểm tra email đã tồn tại chưa.
			$stmt = $db->prepare( 'SELECT userid FROM ' . NV_USERS_GLOBALTABLE . ' WHERE email= :email AND userid != ' .intval( $data['userid'] ) );
			$stmt->bindParam( ':email', $data['email'], PDO::PARAM_STR );
			$stmt->execute();
			$query_error_email = $stmt->fetchColumn();
			if( $query_error_email )
			{
				$json['error'] = $lang_module['patient_error_email_exist'];
			}
		}

		if( !isset( $error['email'] ) )
		{
			
			// Thực hiện câu truy vấn để kiểm tra email đã tồn tại trong nv4_users_reg  chưa.
			$stmt = $db->prepare( 'SELECT userid FROM ' . NV_USERS_GLOBALTABLE . '_reg WHERE email= :email' );
			$stmt->bindParam( ':email', $data['email'], PDO::PARAM_STR );
			$stmt->execute();
			$query_error_email_reg = $stmt->fetchColumn();
			if( $query_error_email_reg )
			{
				$json['error'] = $lang_module['patient_error_email_exist'];
			}
		}
		if( !isset( $error['email'] ) )
		{
			
			// Thực hiện câu truy vấn để kiểm tra email đã tồn tại trong nv3_users_openid chưa.
			$stmt = $db->prepare( 'SELECT userid FROM ' . NV_USERS_GLOBALTABLE . '_openid WHERE email= :email' );
			$stmt->bindParam( ':email', $data['email'], PDO::PARAM_STR );
			$stmt->execute();
			$query_error_email_openid = $stmt->fetchColumn();
			if( $query_error_email_openid )
			{
				$json['error'] = 'email: ' . $lang_module['patient_error_email_exist'];
			}
		}

		try
		{

			$stmt = $db->prepare( 'UPDATE ' . NV_USERS_GLOBALTABLE . '  SET 
				first_name=:first_name,
				last_name=:last_name,
				email=:email,
				username=:username,
				md5username=:md5username,
				address=:address,
				gender=:gender,
				aspirations_treatment=:aspirations_treatment,
				medical_history=:medical_history,
				y_benh_tdcs=:y_benh_tdcs
				WHERE userid=' . intval( $data['userid'] ) );

			$stmt->bindParam( ':first_name', $data['first_name'], PDO::PARAM_STR );
			$stmt->bindParam( ':last_name', $data['last_name'], PDO::PARAM_STR );
			$stmt->bindParam( ':email', $data['email'], PDO::PARAM_STR );
			$stmt->bindParam( ':username', $data['phone'], PDO::PARAM_STR );
			$stmt->bindParam( ':md5username', $data['md5username'], PDO::PARAM_STR );
			$stmt->bindParam( ':address', $data['address'], PDO::PARAM_STR );
			$stmt->bindParam( ':gender', $data['gender'], PDO::PARAM_STR );
			$stmt->bindParam( ':aspirations_treatment', $data['aspirations_treatment'], PDO::PARAM_STR );
			$stmt->bindParam( ':medical_history', $data['medical_history'], PDO::PARAM_STR );
			$stmt->bindParam( ':y_benh_tdcs', $data['y_benh_tdcs'], PDO::PARAM_STR );

			if( $stmt->execute() )
			{
				$json['success'] = $lang_module['patient_update_success'];
				
				// nv_insert_logs( NV_LANG_DATA, $module_name, $lang_module['patient_edit'], 'userid: ' . $data['userid'], $admin_info['userid'] );

				// $nv_Request->set_Session( $module_data . '_success', $lang_module['patient_edit_success'] );

				// $nv_Cache->delMod( $module_name );

			}
			else
			{
				$json['error'] = $lang_module['patient_error_save'];

			}

			$stmt->closeCursor();

		}
		catch ( PDOException $e )
		{
			$json['error'] = $lang_module['patient_error_save'];
			// var_dump( $e ); die();
		}
	}  
	else
	{
		$json['error'] = $lang_module['patient_error_secutiry'];
	}
	

	nv_jsonOutput( $json );
}
if( ACTION_METHOD == 'print' )
{

	$token = $nv_Request->get_title( 'token', 'get', '' );
	$userid = $nv_Request->get_int( 'userid', 'get', 0 );

	
	$userPatient = array();


	$userPatient = $db->query( 'SELECT u.userid, u.username, p.full_name, u.email, p.gender, p.birthday, p.address, p.history, p.expect, p.confess,p.other_contact,p.service_package_id, p.phone,p.note,p.work,p.patient_result,p.patient_code,p.branch FROM ' . NV_USERS_GLOBALTABLE . ' u INNER JOIN ' . TABLE_APPOINTMENT_NAME . '_patient p ON u.userid = p.userid WHERE u.userid=' . intval( $userid ) . ' AND p.mode = 0' )->fetch();


	if( empty( $userPatient ) )
	{
		nv_redirect_location( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op, true );
		
	}
	
	if($userPatient['branch']){
		$userPatient['title_branch'] = nv_unhtmlspecialchars( getBranch_id($userPatient['branch'])['title'] );
		
		$userPatient['address_branch'] = nv_unhtmlspecialchars( getBranch_id($userPatient['branch'])['address'] );
	}else{
		$userPatient['title_branch'] = nv_unhtmlspecialchars( 'Không xác định' );
		$userPatient['address_branch'] = '112/36 Tây Hòa , Phước Long A, Tp Thủ Đức';
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
	
	$sql .= ' ORDER BY date_added ASC';

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


$data['userid'] = $nv_Request->get_int( 'userid', 'post,get', 0 );

$data['appointment_id'] = $nv_Request->get_int( 'appointment_id', 'get', 0 );

$data['token'] = $nv_Request->get_title( 'token', 'post,get', '' );

$dataContent = array();
$generate_page = '';

if( isset( $array_op[1] ) OR $data['userid'] > 0 )
{
	
	$userPatient = array();
	if( isset( $array_op[1] ) )
	{

		$userPatient = $db->query( 'SELECT u.userid, u.username, p.full_name, u.email, p.gender, p.birthday, p.address, p.confess,p.expect,p.history,p.work,p.other_contact,p.note,p.service_package_id,p.patient_group,p.phone,p.patient_code,p.kham_conlai FROM ' . NV_USERS_GLOBALTABLE . ' u INNER JOIN ' . TABLE_APPOINTMENT_NAME . '_patient p on u.userid = p.userid WHERE p.userid=' . $db->quote( $array_op[1] ) . ' AND p.mode = 0')->fetch();
		if($userPatient['patient_group']){
			$userPatient['name_group_patient'] = get_name_patient_group($userPatient['patient_group'])['title'];	
		}
		
		

	} 
	elseif ( $data['token'] ==  md5( $nv_Request->session_id . $global_config['sitekey'] . $data['userid'] ) )
	{
		$userPatient = $db->query( 'SELECT userid, username, CONCAT(last_name,\' \', first_name) AS full_name, email, gender, birthday, address FROM ' . NV_USERS_GLOBALTABLE . ' WHERE userid=' . intval( $data['userid'] ) )->fetch();
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
	
	$data['date_from'] = $nv_Request->get_title( 'df', 'post,get', '' );
	$data['date_to'] = $nv_Request->get_title( 'dt', 'post,get', '' );
	$data['keyword'] = $nv_Request->get_string( 'keyword', 'post,get', '');
	$data['doctors_id'] = $nv_Request->get_int( 'doctors_id', 'post,get', 0 );
	$data['userid'] = $userPatient['userid'];
	
	$page = $nv_Request->get_int( 'page', 'post,get',1);
	
	$perpage = 20;
	
	$base_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '/' . $array_op[1];

	$base_url .= '&page_ajax=1';

	if( preg_match( '/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $data['date_from'], $m ) )
	{

		$date_from = mktime( 0, 0, 0, $m[2], $m[1], $m[3] );
		$base_url .= '&df=' . $data['date_from'];
	}
	else
	{
		$date_from = 0;
	}
	if( preg_match( '/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $data['date_to'], $m ) )
	{

		$date_to = mktime( 23, 59, 59, $m[2], $m[1], $m[3] );
		$base_url .= '&dt=' . $data['date_to'];
	}
	else
	{
		$date_to = 0;
	}

	$implode = array();

	
	// đang ở patient nào thì lấy userid của khách hàng đó thôi
	
	$implode[] = 'patient_id=' . intval( $userPatient['userid'] );
	$base_url .= '&userid=' . $userPatient['userid'];
	
	if( $data['doctors_id']  > 0)
	{
		$implode[] = 'doctors_id=' . intval( $data['doctors_id'] );
		$base_url .= '&doctors_id=' . $data['doctors_id'];
	}
	
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
	
	$sql .= ' ORDER BY date_added ASC';

	$num_items = $db->query( 'SELECT COUNT(*) FROM ' . $sql )->fetchColumn();

	
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

	$generate_page = nv_generate_page( $base_url, $num_items, $perpage, $page , 'true', 'false', 'nv_urldecode_ajax', 'showcontent'  );
	//$generate_page = nv_generate_page( $base_url, $num_items, $perpage, $page );

	
	
	$_SESSION[$data['token']] = nv_base64_encode( serialize( $data ) );
	
	if ($nv_Request->get_int('page_ajax', 'get') == 1)
	{
		$content = ThemeViewPatientUserSearch( $userPatient, $doctorsList, $dataContent, $generate_page );
		die($content);
	}
	elseif( $data['token'] == md5( $nv_Request->session_id . $global_config['sitekey'] . $data['userid'] ))
	{
		// search trong url https://suckhoethiennhan.com/booking/patient/0906192927/
		
		nv_jsonOutput( array( 'template'=> ThemeViewPatientUserSearch( $userPatient, $doctorsList, $dataContent, $generate_page ) ) );
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
		

		$contents = ThemeViewPatientUser( $userPatient, $doctorsList, $dataContent, $generate_page, $doctor);	
	}

	
}
else
{

	$data['keyword'] = $nv_Request->get_string( 'keyword', 'post,get', '');
	$page = $nv_Request->get_int( 'page', 'get',1);
	$perpage = 50;

	$implode = array();

	if( $data['keyword'] )
	{
		// note
		$implode[] = '(p.full_name LIKE \'%' . $db_slave->dblikeescape( $data['keyword'] ) . '%\' OR p.note LIKE \'%' . $db_slave->dblikeescape( $data['keyword'] ) . '%\' OR p.patient_code LIKE \'' . $db_slave->dblikeescape( $data['keyword'] ) . '\')';
		
	}


	$sql =  NV_USERS_GLOBALTABLE . ' u INNER JOIN ' . TABLE_APPOINTMENT_NAME . '_patient p ON u.userid = p.userid ';

	
	if( $implode )
	{
		$sql .= ' WHERE '  . implode( ' AND ', $implode );
	}
	

	$num_items = $db->query( 'SELECT COUNT(*) FROM ' . $sql )->fetchColumn();

	$base_url = NV_BASE_SITEURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '='. $op .'&keyword=' . $data['keyword'] .'&per_page=' . $perpage;

	$db->sqlreset()->select( 'u.userid, u.username, p.full_name, u.email, p.gender, p.birthday, p.address, p.patient_code, p.confess' )->from( $sql )->limit( $perpage )->order('p.patient_code ASC')->offset( ( $page - 1 ) * $perpage );

	$result = $db->query( $db->sql() );
//die($db->sql());
	$stt = 1;
	$dataContent = array();
	while( $rows = $result->fetch() )
	{
		$rows['job'] = '';
		$rows['age'] = $rows['birthday'] ? date('d/m/Y', $rows['birthday']) : 'N/A';
		

		$rows['gender'] = isset( $arrayGender[$rows['gender']] ) ? $arrayGender[$rows['gender']] : 'N/A';
		$rows['stt'] = $stt + ( ( $page - 1 ) * $perpage );
		$rows['link'] = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '/' . $rows['userid'], true );
		
		// số lần khám bệnh còn lại
		// lấy thông tin số lần khám còn lại của userid
		$get_parent_info = get_parent_info($rows['userid']);
		$rows['kham_conlai'] = $get_parent_info['kham_conlai'];
		
		$rows['appointment_id'] = $data['appointment_id'];
		
		
		$dataContent[] = $rows;
		++$stt;
	}
	
	$generate_page = nv_generate_page( $base_url, $num_items, $perpage, $page );

	$contents = ThemeViewPatient( $dataContent, $generate_page, $data['keyword']);	


}

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
