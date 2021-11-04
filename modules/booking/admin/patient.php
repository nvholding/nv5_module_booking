<?php
	
	/**
		* @Project NUKEVIET 4.x
		* @Author DANGDINHTU (dlinhvan@gmail.com)
		* @Copyright (C) 2013 Webdep24.com. All rights reserved
		* @Blog  http://dangdinhtu.com
		* @License GNU/GPL version 2 or any later version
		* @Createdate  Wed, 21 Jan 2015 14:00:59 GMT
	*/
	
	/*
	// check mã khách hàng trùng
	
	$list_patient = $db->query('SELECT patient_code, phone FROM ' . NV_PREFIXLANG . '_' . $module_data . '_patient')->fetchAll();
	
	
	$arr = array();
	
	foreach($list_patient as $patient)
	{
		
		
		$check_code_exits = $db->query('SELECT patient_id FROM ' . NV_PREFIXLANG . '_' . $module_data . '_patient WHERE patient_code ="'. $patient['patient_code'] .'" AND phone != "' . $patient['phone'] . '"')->fetchColumn();
		
		if($check_code_exits)
		{
			$arr[] = $patient;
		}
			
	
	}
	
	print_r($arr);die;
	
	
	*/

	
	/*
		
		// xử lý đồng bộ tài khoản và khách hàng
		
		$arr = array();
		
		$list_user = $db->query('SELECT * FROM vidoco_users WHERE group_id = 4')->fetchAll();
		
		foreach($list_user as $user)
		{
		// kiểm tra tài khoản này có nằm trong khách hàng không
		$check_is_kh = $db->query('SELECT userid FROM ' . NV_PREFIXLANG . '_' . $module_data . '_patient WHERE userid ='. $user['userid'])->fetchColumn();
		
		if(!$check_is_kh)
		{
		$arr[] = $user;
		
		// xóa thông tin tài khoản user luôn
		$result = $db->exec('DELETE FROM vidoco_users WHERE userid=' . $user['userid']);
		$db->query('DELETE FROM vidoco_users_groups_users WHERE userid=' . $user['userid']);
		$db->query('DELETE FROM vidoco_users_openid WHERE userid=' . $user['userid']);
		$db->query('DELETE FROM vidoco_users_info WHERE userid=' . $user['userid']);
		
		}
		}
		
		print_r($arr);die;
		
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
	
	$mod= $nv_Request->get_string('mod', 'get', '');
	
	if($mod=='no_accept'){
		
		$id_edit = $nv_Request->get_int('id_edit', 'post,get', 0);
		
		if($id_edit)
		{
			$db->query('DELETE FROM ' . TABLE_APPOINTMENT_NAME . '_patient_edit WHERE id = ' . $id_edit);
		}
		
		$json[] = ['status'=>'OK', 'text'=>'Đã từ chối!'];
		print_r(json_encode($json[0]));die(); 
		
	}
	
	if($mod=='accept'){
		$id_edit=$nv_Request->get_int('id_edit', 'post,get', 0);
		$data = $db->query('SELECT * FROM ' . TABLE_APPOINTMENT_NAME . '_patient_edit WHERE id = ' . $id_edit)->fetch();
		
		$db->query( 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_patient_edit SET using_patient = 1 WHERE id = ' . $id_edit);
		
		$db->query( 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_patient_edit SET using_patient = 0 WHERE id != ' . $id_edit . ' AND userid = ' . $data['userid']);
		if($data){
			$db->query( 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_patient SET work = "' . $data['work'] . '", expect = "' . $data['expect'] . '", address = "' . $data['address'] . '", birthday = ' . $data['birthday'] . ', full_name = "' . $data['full_name'] . '", phone = "' . $data['phone'] . '",gender = "' . $data['gender'] . '"  WHERE userid =' . $data['userid']);
			
			$db->query( 'UPDATE ' . $db_config['prefix'] . '_users SET email = "' . $data['email'] . '" WHERE userid = ' . $data['userid']);
			
			$stmt = $db->prepare( 'UPDATE ' . NV_USERS_GLOBALTABLE . '  SET 
			first_name="' . $data['full_name'] . '",
			last_name="",
			email="' . $data['email'] . '",
			phone="' . $data['phone'] . '",
			address="' . $data['address'] . '",
			birthday="' . $data['birthday'] . '"
			WHERE userid=' . intval( $data['userid'] ) );
			$stmt->execute();
			
		}
		
		$json[] = ['status'=>'OK', 'text'=>'Đã duyệt!'];
		print_r(json_encode($json[0]));die(); 
	}
	if($mod=='send_email'){
		$email=$nv_Request->get_string('email', 'post', '');
		
		
		$content_file = NV_ROOTDIR . '/' . NV_DATADIR . '/' . NV_LANG_DATA . '_' . $module_data . '_order_content.txt';
		if (file_exists($content_file)) {
			$content = file_get_contents($content_file);
			$content = nv_editor_br2nl($content);
			} else {
			$content = 'Bạn nhận được 1 email';
		}
		
		
		
		$email_contents = call_user_func($content, $data_order, $data_pro);
		$email_title = 'Tiêu đề email';
		
		nv_sendmail(array($global_config['site_name'], $config_email['sender_email']['config_value']), $email, sprintf($email_title, $module_info['custom_title'], $order_code), $content);
		$json[] = ['status'=>'OK', 'text'=>'Gửi email thành công!'];
		print_r(json_encode($json[0]));die(); 
	}
	
	
	
	
	if(isset($_POST['import']))
	{
		
		$allowedExts = array("xlsx");
		$temp = explode(".", $_FILES["excel"]["name"]);
		
		$extension = end($temp);
		if (($_FILES["excel"]["size"] < 200000000000) && in_array($extension, $allowedExts)) {
			
			if ($_FILES["excel"]["error"] > 0)
			echo "Return Code: " . $_FILES["excel"]["error"] . "<br>";
			else{
				// ki?m tra forder user dã t?n t?i chua
				$filename = NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/' . $module_upload . '/patient';
				
				
				if  (!file_exists($filename)) {
					mkdir(NV_UPLOADS_DIR . '/' . $module_upload .  '/patient', 0777);
				} 
				
				
				if (file_exists($filename . '/' . $_FILES["excel"]["name"]))
				unlink($filename .'/'. $_FILES["excel"]["name"]);
				
				
				move_uploaded_file($_FILES["excel"]["tmp_name"],$filename .'/'. $_FILES["excel"]["name"]); 
				$file = $filename .'/'. $_FILES["excel"]["name"]; // file du  
				
				require_once NV_ROOTDIR . '/modules/'. $module_file .'/Classes/PHPExcel.php';
				
				$objFile = PHPExcel_IOFactory::identify($file);
				$objData = PHPExcel_IOFactory::createReader($objFile);
				$objData->setReadDataOnly(true);
				$objPHPExcel = $objData->load($file);
				$sheet  = $objPHPExcel->setActiveSheetIndex(0);
				$Totalrow = $sheet->getHighestRow();
				$LastColumn = $sheet->getHighestColumn();
				$TotalCol = PHPExcel_Cell::columnIndexFromString($LastColumn);
				$data = [];
				$row = array();
				
				for ($i = 3; $i <= $Totalrow; $i++)
				{
					
					
					$data[$i]=array(
            		"stt"=>ltrim($sheet->getCellByColumnAndRow(0, $i)->getValue(),0),
            		"confess"=>$sheet->getCellByColumnAndRow(1, $i)->getValue(),
            		"name"=>$sheet->getCellByColumnAndRow(2, $i)->getValue(),
            		"gender"=>$sheet->getCellByColumnAndRow(3, $i)->getValue(),
            		"birthday"=>$sheet->getCellByColumnAndRow(4, $i)->getValue(),
            		"email"=>$sheet->getCellByColumnAndRow(5, $i)->getValue(),
            		"phone"=>$sheet->getCellByColumnAndRow(6, $i)->getValue(),
            		"other_phone"=>$sheet->getCellByColumnAndRow(7, $i)->getValue(),
            		"address"=>$sheet->getCellByColumnAndRow(8, $i)->getValue(),
            		"work"=>$sheet->getCellByColumnAndRow(9, $i)->getValue(),
            		"history"=>$sheet->getCellByColumnAndRow(10, $i)->getValue(),
            		"result"=>$sheet->getCellByColumnAndRow(11, $i)->getValue(),
            		"expect"=>$sheet->getCellByColumnAndRow(12, $i)->getValue(),
            		"note"=>$sheet->getCellByColumnAndRow(13, $i)->getValue(),
            		"patient_group"=>$sheet->getCellByColumnAndRow(14, $i)->getValue()
            		
					);
					
				}
				
				
				function super_unique($array,$key)
				{
					
					$temp_array = [];
					foreach ($array as $key2 => &$v) {
						if($v['phone']){
							$temp_array[$v[$key]][$key2] =& $v;
						}
						
					}
					$data_loi=array();
					foreach( $temp_array as $value){
						if(count($value)>1){
							$data_loi=$value;
						}
					}
					
					return $data_loi;
				}
				$check_phonenumber=super_unique($data,'phone');
				//  if(count($check_phonenumber)>0){
				if(false){
					
					$error[]='File excel dòng ';
					$i=0;
					foreach($check_phonenumber as $key=>$value){
						if($i==0){
							$error[]=$key . ' Với số điện thoại ' . $value['phone'] . ';';
							
							}else{
							if($value['phone']){
								$error[]=$key . ' với số điện thoại ' . $value['phone'] . ';';
							}
						}
						$i++;
					}
					
					//$array_data['redirect'] = nv_base_siteurl + 'index.php?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=booking&op=patient';
					$error[]='bị trùng dữ liệu cột số điện thoại nên không thể import';
					echo "<script type='text/javascript'>alert('".implode(',',$error)."');location.href='". nv_url_rewrite($array_data['redirect'], true )."'</script>";
					}else{
					
					$h = 1;
					
					$add = 0;
					$edit = 0;
					
					
					
					foreach ($data as $key => $value_user) {
						
						if(empty($value_user['name']) and empty($value_user['phone']))
						{
							continue;
						}
						
						if(empty($value_user['confess']))
						$value_user['confess'] = 'Quý khách';
						
						$value_user['service_package_id'] = 0;
						
						// xử lý dữ liệu email và phone cho chuẩn database
						$value_user['phone'] = str_replace(' ', '', $value_user['phone']);
						
						if($value_user['email']==''){
							$value_user['email'] = $value_user['phone'] . '@gmail.com';
						}
						
						$value_user['email'] = str_replace(' ', '', $value_user['email']);
						
						
						
						//	print_r($value_user);die;
						
						if((!empty($value_user['phone'])) and (!empty($value_user['name'])))
						{
							
							
							
							
							if(!$value_user['patient_group']){
								$value_user['patient_group'] = 6;
							}
							if($value_user['birthday']){
								$ngaysinh = explode('/', $value_user['birthday']);
								
								if(count($ngaysinh)==1){
									$value_user['birthday'] = '1/1/' . $value_user['birthday'];
									}else if(count($ngaysinh)==2){
									$value_user['birthday'] = '1/' . $value_user['birthday'];
									}else{
									$value_user['birthday'] = $value_user['birthday'];
								}
								
								$value_user['birthday'] = convertToTimeStamp( ($value_user['birthday']), 0, 0, 0, 0 );
								
								}else{
								$value_user['birthday'] = 0;
							}
							
							
							if($value_user['gender']=='Nam'){
								$value_user['gender'] = 'M';
								}else if($value_user['gender']=='Nữ'){
								
								$value_user['gender'] = 'F';
								}else{
								$value_user['gender'] = '';
							}
							
							
							
							
							// xử lý khi mã số KH trống
							if(empty($value_user['stt']))
							{
								// lấy số thứ tự lớn nhất ra
								$max = $db->query("SELECT max(patient_code) as max FROM " . NV_PREFIXLANG . "_" . $module_name . "_patient")->fetchColumn();
								
								$value_user['stt'] = $max + 1;
								
								
							}
							
							$userid = $db->query("SELECT userid FROM " . NV_PREFIXLANG . "_" . $module_name . "_patient WHERE phone ='" . $value_user['phone'] . "'")->fetchColumn();
							$username = $db->query("SELECT count(*) FROM " . $db_config['prefix'] . "_users WHERE username ='" . $value_user['phone'] . "'")->fetchColumn();
							
							
							
							
							
							if($userid == 0 && $username == 0){
								
								// thêm mới. kiểm tra patient_code có tồn tại chưa
								$check_patient_code = $db->query("SELECT patient_id FROM " . NV_PREFIXLANG . "_" . $module_name . "_patient WHERE patient_code ='" . $value_user['stt'] . "'")->fetchColumn();
								
								
								if($check_patient_code)
								{
									// đã tồn tại. lấy mã code mới tăng dần
									// lấy số thứ tự lớn nhất ra
									$max = $db->query("SELECT max(patient_code) as max FROM " . NV_PREFIXLANG . "_" . $module_name . "_patient")->fetchColumn();
									
									$value_user['stt'] = $max + 1;
								}
								
								$value_user['md5username'] = nv_md5safe($value_user['phone']);
								$value_user['password'] = $crypt->hash_password($value_user['phone'], $global_config['hashprefix']);
								$value_user['sig'] = '';
								$value_user['in_groups_default'] = 4;
								$value_user['in_groups'] = 4;
								$value_user['view_mail']= 0;
								$value_user['is_email_verified']= -1;
								
								
								$ii = 1;
								
								
								try{
									
									$sql = "INSERT INTO " . $db_config['prefix'] . "_users (
									group_id, username, md5username, password, first_name, phone, address, gender, email, sig, birthday, regdate,
									question, answer, passlostkey, view_mail,
									remember, in_groups, active, checknum, last_login, last_ip, last_agent, last_openid, email_verification_time,
									active_obj
									) VALUES (
									" . $value_user['in_groups_default'] . ",
									'" .$value_user['phone']."',
									'" .$value_user['md5username']."',
									'" .$value_user['password']."',
									'" .$value_user['name']."',
									'" .$value_user['phone']."',
									'" .$value_user['address']."',
									'" .$value_user['gender']."',
									'" .$value_user['email']."', 
									'" .$value_user['sig']."',
									'" .$value_user['birthday']."',
									" . NV_CURRENTTIME . ",
									'" .$value_user['phone']."',
									'" .$value_user['phone']."',
									'',
									" . $value_user['view_mail'] . ",
									1,
									'" . $value_user['in_groups_default'] . "', 1, '', 0, '', '', '',
									" . ($value_user['is_email_verified'] ? '-1' : '0') . ",
									'SYSTEM'
									)";
									$data_insert = [];
									$userid = $db->insert_id($sql, 'userid', $data_insert);
									
									
									if($userid){
										
										if(empty($value_user['confess']))
										$value_user['confess'] = 'Quý khách';
										
										
										$db->query( 'INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_patient(confess,userid,patient_group,patient_result,note,expect,work,history,other_contact,gender,birthday,service_package_id,address,full_name,phone,patient_code) VALUES("'. $value_user['confess'] . '",' .$userid. ',' . $value_user['patient_group'] .',"' . $value_user['result'] .'","' . $value_user['note'] .'","' . $value_user['expect'] .'","' . $value_user['work'] .'","' . $value_user['history'] . '", "' . $value_user['other_phone'] . '", "' . $value_user['gender'] . '", ' . $value_user['birthday'] . ', ' . $value_user['service_package_id'] . ', "' . $value_user['address'] . '", "' . $value_user['name'] . '", "' . $value_user['phone'] . '", "' . $value_user['stt'] . '")' );
									}
									
									$add++;
									
								}
								catch ( PDOException $e )
								{
									$error[]= 'Thêm dong lỗi' . $add;
									
									print_r($error);
									print_r($value_user);die;    
									
								}
								
								
								
								
								/*
									print_r($value_user);
									die('INSERT');
								*/
								
								
								}else{
								
								
								
								// kiểm tra mã số patient_code đã có tài khoản khác sử dụng chưa.
								$check_code_exits = $db->query('SELECT patient_id FROM ' . NV_PREFIXLANG . '_' . $module_data . '_patient WHERE patient_code ="'. $value_user['stt'] .'" AND phone != "' . $value_user['phone'] . '"')->fetchColumn();
								
								// cập nhật mã khách hàng này bị trùng với khách hàng đã tồn tại trên hệ thống.
								
								
								if($check_code_exits)
								{
									$edit++;
									$error[]= 'Cập nhật dòng' . $edit;
									continue;
								}
								
								
								
								
								try{
									
									
									// cập nhật thông tin
									
									$db->query( 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_patient SET confess = "'. $value_user['confess'] .'", patient_code ="'. $value_user['stt'] .'", gender ="'. $value_user['gender'] .'", work = "' . $value_user['work'] . '", history = "' . $value_user['history'] . '", patient_result = "' . $value_user['result'] . '", expect = "' . $value_user['expect'] . '", note = "' . $value_user['note'] . '", address = "' . $value_user['address'] . '", birthday = ' . $value_user['birthday'] . ', full_name = "' . $value_user['name'] . '", patient_group = ' . $value_user['patient_group'] . ',other_contact = "' . $value_user['other_phone'] . '",service_package_id = ' . $value_user['service_package_id'] . ',phone = "' . $value_user['phone'] . '"  WHERE phone ="' . $value_user['phone'] .'"');
									
									
									$stmt = $db->prepare( 'UPDATE ' . NV_USERS_GLOBALTABLE . '  SET 
									first_name="' . $value_user['name'] . '",
									last_name="",
									email="' . $value_user['email'] . '",
									phone="' . $value_user['phone'] . '",
									address="' . $value_user['address'] . '",
									gender="' . $value_user['gender'] . '",
									birthday="' . $value_user['birthday'] . '"
									WHERE userid=' . intval( $userid ) );
									
									$stmt->execute();
									
									$edit++;
									
								} 
								catch ( PDOException $e )
								{
									$error[]= 'Cập nhật dòng ' . $edit;
									
									//print_r($error);
									//print_r($value_user);die;    
									
								}
								
								
								
								
								
							}	
							
						}
						else
						{
							$error[]= "Lỗi dữ liệu tại dòng " . $key;
						}
						
					}
					
					$error[]= "Số dòng thêm ". $add;
					$error[]= "Số dòng chỉnh sửa ". $edit;
					
					//print_r($error);die;
					// nv_redirect_location( NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=patient', true );
				}
				
				
			}
		}
	}
	
	
	
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
				// kiểm tra tài khoản này có khám bệnh chưa. nếu chưa mới được xóa
				
				$check_kham = $db->query( 'SELECT * FROM ' . TABLE_APPOINTMENT_NAME . '_patient_appointment WHERE patient_id = ' . ( int )$userid )->fetch();
				if($check_kham)
				{
					$json['error'] = 'Khách hàng đã trị liệu tại đây. Xóa thất bại!';
					nv_jsonOutput( $json );
				}
				
				$result = $db->query( 'DELETE FROM ' . TABLE_APPOINTMENT_NAME . '_patient WHERE userid = ' . ( int )$userid );
				if( $result->rowCount() )
				{
					// xóa thông tin tài khoản user luôn
					$result = $db->exec('DELETE FROM vidoco_users WHERE userid=' . $userid);
					$db->query('DELETE FROM vidoco_users_groups_users WHERE userid=' . $userid);
					$db->query('DELETE FROM vidoco_users_openid WHERE userid=' . $userid);
					$db->query('DELETE FROM vidoco_users_info WHERE userid=' . $userid);
					
					
					$json['id'][$a] = $userid;
					$_del_array[] = $userid;
					++$a;
				}
			}
			$count = sizeof( $_del_array );
			
			if( $count )
			{
				nv_insert_logs( NV_LANG_DATA, $module_name, 'log_del_patient', implode( ', ', $_del_array ), $admin_info['userid'] );
				
				$nv_Cache->delMod( $module_name );
				
				$json['success'] = $lang_module['patient_delete_success'];
			}
			
		}
		else
		{
			$json['error'] = $lang_module['patient_error_security'];
		}
		
		nv_jsonOutput( $json );
	}
	
	if( ACTION_METHOD == 'getDoctors' )
	{
		
		$branch_id =  $nv_Request->get_int( 'branch_id', 'post', 0);
		$booking_date =  $nv_Request->get_string( 'booking_date', 'post', '');
		$booking_hour =  $nv_Request->get_string( 'booking_hour', 'post', '');
		$date_start_begin = $date_start_end = 0;
		if( $branch_id > 0 )
		{
			
			if( preg_match( '/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $booking_date, $m ) )
			{
				
				$time = array_map( 'trim', explode( ':', $booking_hour ) );
				$hour = isset( $time[0] ) ? intval( $time[0] ) : 0;
				$minute = isset( $time[1] ) ? intval( $time[1] ) : 0;
				
				$booking_date = mktime( $hour, $minute, 0, $m[2], $m[1], $m[3] );
				
				$date_start_begin = mktime( 0, 0 , 0, $m[2], $m[1], $m[3] );
				$date_start_end = mktime( 23, 59, 59, $m[2], $m[1], $m[3] );
				
				
			}
			else
			{
				$booking_date = 0;
			}
			$shift = 0;
			if( $booking_date > 0 )
			{
				$time_morning_begin = mktime( 07, 0 , 0, $m[2], $m[1], $m[3] );
				$time_morning_end = mktime( 13, 59, 59, $m[2], $m[1], $m[3] );
				$time_afternoon_begin = mktime( 14, 0 , 0, $m[2], $m[1], $m[3] );
				$time_afternoon_end = mktime( 17, 59, 59, $m[2], $m[1], $m[3] );
				$time_night_begin = mktime( 18, 0 , 0, $m[2], $m[1], $m[3] );
				$time_night_end = mktime( 21, 59, 59, $m[2], $m[1], $m[3] );
				
				if( $booking_date >= $time_morning_begin && $booking_date <= $time_morning_end )
				{
					
					$shift = 1;
					
				}
				elseif( $booking_date > $time_afternoon_begin && $booking_date <  $time_afternoon_end )
				{
					
					$shift = 2;
					
				}
				elseif( $booking_date > $time_night_begin && $booking_date <  $time_night_end )
				{
					
					$shift = 3;
					
				}
				
			}
			
			
			
			$implode = array();
			
			
			// if( !empty( $getSetting['default_group_doctors'] ) )
			// {
			// $implode[] = 'gu.group_id=' . intval( $getSetting['default_group_doctors'] );
			// }
			if( $shift > 0 && $date_start_begin > 0 )
			{
				$keyword = $nv_Request->get_string( 'search', 'post', '');
				
				$implode[] = 'c.shift=' . intval( $shift );
				
				
				
				$implode[] = 'c.date_start BETWEEN ' . intval( $date_start_begin ) . ' AND ' . intval( $date_start_end );
				
				if( !empty( $branch_id ) )
				{
					$implode[] = 'bu.branch_id=' . intval( $branch_id );
				}
				
				if( !empty( $keyword ) )
				{
					$implode[]= "CONCAT(u.last_name,' ', u.first_name) LIKE '%" . $db->dblikeescape( $keyword ) . "%' OR username LIKE '%" . $db->dblikeescape( $keyword ) . "%'";
				}
				$sql= 'SELECT u.userid, u.username, CONCAT(u.last_name,\'\', u.first_name) AS full_name, u.username, u.email, u.address, u.regdate, u.active, bu.branch_id FROM 
				' . NV_USERS_GLOBALTABLE . ' u  RIGHT JOIN ' . TABLE_APPOINTMENT_NAME . '_branch_users bu ON (u.userid = bu.userid) 
				LEFT JOIN ' . TABLE_APPOINTMENT_NAME . '_calendar c ON (u.userid = c.userid)';
				
				if( !empty( $implode ) )
				{
					$sql .= ' WHERE ' . implode( ' AND ', $implode );
				}		
				
				$sql .= ' ORDER BY full_name ASC LIMIT 0, 200';
				
				$sth = $db->prepare( $sql );
				$sth->execute();
				while( $item = $sth->fetch( ) )
				{
					$json['data'][] = array( 'id' => $item['userid'], 'text' => nv_htmlspecialchars( $item['full_name'] . ' - ' . $item['username'] ) );
				}
			}	
			
		}
		if( !isset( $json['data'] ) )
		{
			$json['data'][] = array( 'id' => 0, 'text' => $lang_module['empty_doctors']);
		}
		
		nv_jsonOutput( $json );
	}
	
	if( ACTION_METHOD == 'getDoctors2' )
	{
		
		$implode = array();
		
		
		if( !empty( $getSetting['default_group_doctors'] ) )
		{
			$implode[] = 'gu.group_id=' . intval( $getSetting['default_group_doctors'] );
		}
		
		$keyword = $nv_Request->get_string( 'search', 'post', '');
		
		if( !empty( $keyword ) )
		{
			$implode[]= "CONCAT(u.last_name,' ', u.first_name) LIKE '%" . $db->dblikeescape( $keyword ) . "%' OR username LIKE '%" . $db->dblikeescape( $keyword ) . "%'";
		}
		
		
		$sql= 'SELECT u.userid, u.username, CONCAT(u.last_name,\'\', u.first_name) AS full_name, u.username, u.email, u.address, u.regdate, u.active FROM 
		' . NV_USERS_GLOBALTABLE . ' u  RIGHT JOIN ' . NV_USERS_GLOBALTABLE . '_groups_users gu ON (u.userid = gu.userid)';
		
		if( !empty( $implode ) )
		{
			$sql .= ' WHERE ' . implode( ' AND ', $implode );
		}		
		
		$sql .= ' ORDER BY full_name ASC LIMIT 0, 200';
		
		$sth = $db->prepare( $sql );
		$sth->execute();
		while( $item = $sth->fetch( ) )
		{
			$json['data'][] = array( 'id' => $item['userid'], 'text' => nv_htmlspecialchars( $item['full_name'] . ' - ' . $item['username'] ) );
		}
		
		
		nv_jsonOutput( $json );
	}
	
	if( ACTION_METHOD == 'getBranch' )
	{
		
		$keyword = $nv_Request->get_string( 'q', 'post', '');
		if( !empty( $keyword ) )
		{
			$implode = array();
			$implode[] = 'title LIKE :title';
		}
		$sql = 'SELECT branch_id, title FROM ' . TABLE_APPOINTMENT_NAME . '_branch';
		
		if( !empty( $implode ) )
		{
			$sql .= ' WHERE ' . implode( ' AND ', $implode );
		}		
		
		$sql .= ' ORDER BY title ASC LIMIT 0, 200';
		
		$sth = $db->prepare( $sql );
		
		if( ! empty( $implode ) )
		{
			$sth->bindValue( ':title', '%'.$keyword.'%' );
			
		}
		$sth->execute();
		while( list( $branch_id, $title ) = $sth->fetch( 3 ) )
		{
			$json['data'][] = array( 'id' => $branch_id, 'text' => nv_htmlspecialchars( $title ) );
		}
		
		nv_jsonOutput( $json );
	}
	
	elseif( ACTION_METHOD == 'search' )
	{
		
		
		
		$userPatient['token'] = $nv_Request->get_title( 'token', 'get,post', '' );
		$userPatient['userid'] = $nv_Request->get_int( 'userid', 'get,post', 0 );
		$data['date_from'] = $nv_Request->get_title( 'df', 'get,post','' );
		$data['date_to'] = $nv_Request->get_title( 'dt', 'get,post', '' );
		$data['token'] = $userPatient['token'];
		$data['userid'] = $userPatient['userid'];
		
		
		$_SESSION[$data['token']] = nv_base64_encode( serialize( $data ) );
		
		if( $userPatient['token'] != md5( $nv_Request->session_id . $global_config['sitekey'] . $userPatient['userid'] ) )
		{
			nv_jsonOutput( array( 'error'=> $lang_module['patient_error_security'] ));
		}
		
		$xtpl = new XTemplate( 'patient_search.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
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
		$data['doctors_id'] = $nv_Request->get_int( 'doctors_id', 'get,post', 0 );
		
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
		
		if( ! empty( $dataContent ) )
		{
			
			foreach( $dataContent as $item )
			{
				
				$item['date_added'] = nv_date( 'd/m/Y', $item['date_added'] );
				$item['price'] = price_format( $item['price'] );
				// $item['date_modified'] = nv_date( 'd/m/Y', $item['date_modified'] );
				$item['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $item['patient_id'] );
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
		
		
		$base_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op;
		
		if( $userPatient['userid'] )
		{
			$base_url.= '&userid=' . $userPatient['userid'];
		}
		if( $userPatient['token'] )
		{
			$base_url.= '&token=' . $userPatient['token'];
		}
		if( $data['date_from'] )
		{
			$base_url.= '&df=' . $data['date_from'];
		}
		if( $data['date_to'] )
		{
			$base_url.= '&dt=' . $data['date_to'];
		}
		if( $data['doctors_id'] )
		{
			$base_url.= '&doctors_id=' . $data['doctors_id'];
		}
		
		$generate_page = getDataPage( $base_url, $num_items, $perpage, $page, true, true, 'getDataPage', 'showcontent', true  );
		if( ! empty( $generate_page ) )
		{
			$xtpl->assign( 'GENERATE_PAGE', $generate_page );
			$xtpl->parse( 'main.generate_page' );
		}
		
		$xtpl->parse( 'main' );
		$contents = $xtpl->text( 'main' );
		
		nv_jsonOutput( array( 'template'=> $contents ));
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
				var_dump( $e ); die();
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
		
		$data = isset( $_SESSION[$token] ) ? unserialize( nv_base64_decode( $_SESSION[$token] ) ) : array();
		
		
		$userPatient = array();
		if ( $data['token'] ==  md5( $nv_Request->session_id . $global_config['sitekey'] . $data['userid'] ) )
		{
			
			$userPatient = $db->query( 'SELECT u.userid, u.username, p.full_name, u.email, p.gender, p.birthday, p.address, p.history, p.expect, p.confess,p.other_contact,p.service_package_id, p.phone,p.note,p.work,p.patient_result,p.patient_code FROM ' . NV_USERS_GLOBALTABLE . ' u INNER JOIN ' . TABLE_APPOINTMENT_NAME . '_patient p ON u.userid = p.userid WHERE u.userid=' . intval( $data['userid'] ) . ' AND p.mode = 0' )->fetch();
		}
		
		if( empty( $userPatient ) )
		{
			nv_redirect_location( NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op, true );
			
		}
		
		if($userPatient['birthday']){
			$userPatient['birthday'] = date('d/m/Y',$userPatient['birthday']);
			}else{
			$userPatient['birthday'] = '...........';
		}
		
		$userPatient['service_package'] = get_name_ServicePackage($userPatient['service_package_id'])['title'];
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
		$logo = $db->query('SELECT config_value FROM ' . $db_config['prefix'] . '_config WHERE config_name = "site_logo"')->fetchColumn();
		$logo = NV_BASE_SITEURL . $logo;
		
		$contents = ThemeViewPatientPrint( $userPatient, $doctorsList, $dataContent,$logo );	
		
		include NV_ROOTDIR . '/includes/header.php';
		echo $contents;
		include NV_ROOTDIR . '/includes/footer.php';
		
	}
	
	elseif( ACTION_METHOD == 'view' )
	{
		
		
		
		$data['token'] = $nv_Request->get_title( 'token', 'get,post', '' );
		$data['userid'] = $nv_Request->get_int( 'userid', 'get,post', 0 );
		
		$userPatient = $db->query( 'SELECT u.userid, u.username, p.full_name, u.email, p.address, p.birthday, u.active, p.gender, p.history, p.expect, p.patient_group, p.work, p.other_contact, p.confess, p.service_package_id, p.phone, p.note,p.patient_result,p.patient_code FROM ' . NV_USERS_GLOBALTABLE . ' u INNER JOIN ' . TABLE_APPOINTMENT_NAME . '_patient p ON u.userid = p.userid WHERE u.userid=' . $data['userid'] . ' AND p.mode = 0')->fetch();
		
		
		$data['date_from'] = $nv_Request->get_title( 'df', 'post', '' );
		$data['date_to'] = $nv_Request->get_title( 'dt', 'post', '' );
		$data['keyword'] = $nv_Request->get_string( 'keyword', 'post', '');
		$data['doctors_id'] = $nv_Request->get_int( 'doctors_id', 'post', 0 );
		
		
		
		$_SESSION[$data['token']] = nv_base64_encode( serialize( $data ) );
		$userPatient['job'] = '';
		$userPatient['age'] = ( $userPatient['birthday'] ) ? floor((time() - $userPatient['birthday'] ) / 31556926) : 'N/A';
		// $userPatient['gender'] = isset( $arrayGender[$userPatient['gender']] ) ? $arrayGender[$userPatient['gender']] : 'N/A';
		$userPatient['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $userPatient['userid'] );
		$userPatient['birthday'] = ( $userPatient['birthday'] ) ? date('d/m/Y', $userPatient['birthday']) : '';
		
		
		$xtpl = new XTemplate( 'patient_view.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
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
		$xtpl->assign( 'DATA', $data );
		$userPatient['patient_group_name'] = $db->query('SELECT t1.title FROM ' . TABLE_APPOINTMENT_NAME . '_group_patient t1 INNER JOIN ' . TABLE_APPOINTMENT_NAME . '_patient t2 ON t2.patient_group = t1.id WHERE t2.userid = ' . $userPatient['userid'])->fetchColumn();
		$userPatient['service_package'] = get_name_ServicePackage($userPatient['service_package_id'])['title'];
		
		if($userPatient['gender']){
			if($userPatient['gender'] == 'M'){
				$userPatient['gender'] = 'Nam';
				}else{
				$userPatient['gender'] = 'Nữ';
			}
			}else{
			$userPatient['gender'] = 'Không xác định';
		}
		if($userPatient['birthday']){
			
			}else{
			$userPatient['birthday'] = 'Không xác định';
		}
		
		
		// số lần khám bệnh còn lại
		// lấy thông tin số lần khám còn lại của userid
		$get_parent_info = get_parent_info($userPatient['userid']);
		
		$userPatient['kham_conlai'] = $get_parent_info['kham_conlai'];
		
		
		$xtpl->assign( 'USER', $userPatient );
		
		
		
		$xtpl->assign( 'CANCEL', NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=" . $op );
		$xtpl->assign( 'URLPATIENT', NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=" . $op );
		
		foreach( $arrayGender as $key => $name )
		{
			$xtpl->assign( 'GENDER', array('key'=> $key, 'name'=> $name, 'selected'=> ( $userPatient['gender'] == $key ) ? 'selected="selected"' : '') );
			$xtpl->parse( 'main.gender' );
		}
		
		
		$base_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op;
		
		
		if( $userPatient['userid'] )
		{
			$base_url.= '&userid=' . $userPatient['userid'];
		}
		if( $userPatient['token'] )
		{
			$base_url.= '&token=' . $userPatient['token'];
		}
		if( $data['date_from'] )
		{
			$base_url.= '&df=' . $data['date_from'];
		}
		if( $data['date_to'] )
		{
			$base_url.= '&dt=' . $data['date_to'];
		}
		if( $data['doctors_id'] )
		{
			$base_url.= '&doctors_id=' . $data['doctors_id'];
		}
		
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
		
		if( ! empty( $dataContent ) )
		{
			
			foreach( $dataContent as $item )
			{
				
				$item['date_added'] = nv_date( 'd/m/Y', $item['date_added'] );
				$item['price'] = price_format( $item['price'] );
				// $item['date_modified'] = nv_date( 'd/m/Y', $item['date_modified'] );
				$item['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $item['patient_id'] );
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
		
		$generate_page = getDataPage( $base_url, $num_items, $perpage, $page, true, true, 'getDataPage', 'showcontent', true  );
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
	}
	
	elseif( ACTION_METHOD == 'add' || ACTION_METHOD == 'edit' )
	{
		
		// lay id lon nhat ra
		$max = $db->query("SELECT max(patient_code) as max FROM " . NV_PREFIXLANG . "_" . $module_name . "_patient")->fetchColumn();
		
		$patient_code = $max + 1;
		
		$data = array(
		'userid' => 0,
		'first_name' => '',
		'last_name' => '',
		'full_name' => '',
		'patient_code' => $patient_code,
		'phone' => '',
		'email' => '',
		'address' => '',
		'gender' => '',
		'confess' => '',
		'patient_group' => 0,
		'branch' => 0,
		'service_package_id' => 0 );
		
		$error = array();
		
		$data['userid'] = $nv_Request->get_int( 'userid', 'get,post', 0 );
		if( $data['userid'] > 0 )
		{
			$data = $db->query( 'SELECT u.userid, u.username, bu.full_name, u.email, bu.address, bu.birthday, u.active, bu.gender, u.password, bu.service_package_id, bu.patient_group, bu.confess, bu.other_contact, bu.work, bu.expect, bu.note, bu.patient_result, bu.history, bu.patient_code, bu.branch FROM 
			' . NV_USERS_GLOBALTABLE . ' u
			LEFT JOIN ' . TABLE_APPOINTMENT_NAME . '_patient bu ON (u.userid = bu.userid) WHERE u.userid=' . $data['userid'] . ' AND mode = 0')->fetch();
			
			$data['birthday'] = ( $data['birthday'] ) ? date('d/m/Y', $data['birthday']) : '';
			
			$caption = $lang_module['patient_update'];
		}
		else
		{
			$caption = $lang_module['patient_add'];
		}
		
		if( $nv_Request->get_int( 'save', 'post' ) == 1 )
		{
			
			$data['full_name']  = trim( nv_substr( $nv_Request->get_title( 'full_name', 'post', '', '' ), 0, 250 ) );
			
			$data['patient_code']  = trim( nv_substr( $nv_Request->get_title( 'patient_code', 'post', '', '' ), 0, 250 ) );
			
			$data['phone'] =trim(  nv_substr( $nv_Request->get_title( 'phone', 'post', '', '' ), 0, 250 ));
			
			$data['md5username'] = nv_md5safe($data['phone']);
			$data['email'] = trim( nv_substr( $nv_Request->get_title( 'email', 'post', '', '' ), 0, 250 ));
			$data['address'] = trim( nv_substr( $nv_Request->get_title( 'address', 'post', '', '' ), 0, 250 ));
			$data['gender'] = $nv_Request->get_title( 'gender', 'post', '', '' );
			$data['password1'] = $data['phone'];
			$data['password2'] = $data['phone'];
			$data['birthday'] = $nv_Request->get_title( 'birthday', 'post', '' );
			$data['other_contact'] = $nv_Request->get_title( 'other_contact', 'post', '' );
			$data['service_package_id'] = $nv_Request->get_int( 'service_package_id', 'post',0);
			$data['patient_group'] = $nv_Request->get_int( 'patient_group', 'post',0);
			$data['branch'] = $nv_Request->get_int( 'branch', 'post',0);
			$data['confess'] = $nv_Request->get_title( 'confess', 'post','');
			$data['work'] = $nv_Request->get_title( 'work', 'post','');
			$data['history'] = $nv_Request->get_title( 'history', 'post','');
			$data['patient_result'] = $nv_Request->get_title( 'patient_result', 'post','');
			$data['expect'] = $nv_Request->get_title( 'expect', 'post','');
			$data['note'] = $nv_Request->get_title( 'note', 'post','');
			$data['check_admin'] = $nv_Request->get_int( 'check_admin', 'post',1);
			
			
			
			if( empty( $data['full_name'] ) ) $error['full_name'] = $lang_module['patient_error_full_name'];
			
			if( empty( $data['patient_code'] ) ) $error['patient_code'] = $lang_module['patient_error_patient_code'];
			else
			{
				
				// kiểm tra code có tồn tại hay không
				$where_code = '';
				if( $data['userid'] > 0 )
				{
					$where_code = ' AND userid !=' . $data['userid'] ;
				}
				
				// kiểm tra
				$exits_code = $db->query('SELECT patient_code FROM ' . NV_PREFIXLANG . '_' . $module_data . '_patient WHERE  patient_code != "" AND patient_code ="'. $data['patient_code'] .'"' . $where_code)->fetchColumn();
				
				
				
				if( $exits_code ) $error['patient_code'] = $lang_module['patient_error_patient_code_exits'];
				
			}
			
			
			
			// if( empty( $data['address'] ) ) $error['address'] = $lang_module['patient_error_address'];
			
			if( ( $error_username = nv_check_valid_login( $data['phone'], $global_config['nv_unickmax'], $global_config['nv_unickmin'] ) ) != '' )
			{
				$error['phone'] =  $error_username;
			}
			else if( "'" . $data['phone'] . "'" != $db->quote( $data['phone'] ) )
			{
				$error['phone'] =  sprintf( $lang_module['patient_account_deny_name'], $data['phone'] );
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
					$error['phone'] = $lang_module['patient_error_phone_exist'];
				}
				
				
			}
			
			$check_phone = $db->query('SELECT count(*) FROM ' . NV_PREFIXLANG . '_' . $module_data . '_patient WHERE phone = "' . $data['phone'] . '" AND userid != ' . $data['userid'])->fetchColumn();
			if($check_phone != 0){
				$error['phone'] = 'Số điện thoại này đã tồn tại';
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
					$error['email'] = $lang_module['patient_error_email_exist'];
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
					$error['email'] = $lang_module['patient_error_email_exist'];
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
					$error['email'] = 'email: ' . $lang_module['patient_error_email_exist'];
				}
			}
			
			
			
			if( $data['userid'] > 0 )
			{
				if( $data['password1'] != $data['password2'] )
				{
					if( ( $check_pass = nv_check_valid_pass( $data['password1'], $global_config['nv_upassmax'], $global_config['nv_upassmin'] ) ) != ''  )
					{
						$error['password'] = $check_pass;
					}
				}
				if( $data['password1'] != $data['password2'] && !empty( $data['password1'] ) && !empty( $data['password2'] ) )
				{
					$error['password'] = $lang_module['patient_error_password'];
				}
				
			}
			else
			{
				if( ( $check_pass = nv_check_valid_pass( $data['password1'], $global_config['nv_upassmax'], $global_config['nv_upassmin'] ) ) != ''  )
				{
					$error['password'] = $check_pass;
				}
				
				if( $data['password1'] != $data['password2'] )
				{
					$error['password'] = $lang_module['patient_error_password'];
				}
				
			}
			
			if( ! empty( $error ) && ! isset( $error['warning'] ) )
			{
				$error['warning'] = $lang_module['patient_error_warning'];
			}
			
			if( empty( $error ) )
			{
				
				$password = !empty($data['password1']) ? $crypt->hash_password($data['password1'], $global_config['hashprefix']) : $data['password'];
				$birthday = 0;
				if( preg_match( '/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $data['birthday'], $m ) )
				{
					
					$birthday = mktime( 0, 0, 1, $m[2], $m[1], $m[3] );
				}
				
				try
				{
					if( $data['userid'] == 0 )
					{
						
						$data['sig'] = '';
						$data['in_groups_default'] = 4;
						$data['in_groups'] = 4;
						$data['view_mail']= 0;
						$data['is_email_verified']= -1;
						
						
						$sql = "INSERT INTO " . NV_USERS_GLOBALTABLE . " (
						group_id, username, md5username, password, email, sig, regdate,
						question, answer, passlostkey, view_mail,
						remember, in_groups, active, checknum, last_login, last_ip, last_agent, last_openid, email_verification_time,
						active_obj
						) VALUES (
						
						" . $data['in_groups_default'] . ",
						'" . $data['phone'] . "',
						'" . $data['md5username'] . "',
						'" . $password . "',
						'" . $data['email'] . "',
						:sig,
						" . NV_CURRENTTIME . ",
						'" . $data['phone'] . "',
						'" . $data['phone'] . "',
						'',
						" . $data['view_mail'] . ",
						1,
						'" . implode(',', $data['in_groups']) . "', 1, '', 0, '', '', '',
						" . ($data['is_email_verified'] ? '-1' : '0') . ",
						'SYSTEM'
						)";
						$data_insert = [];
						
						
						
						
						$data_insert['sig'] = $data['sig'];
						$userid = $db->insert_id($sql, 'userid', $data_insert);
						
						
						// $data['patient_code']
						// lấy số thứ tự lớn nhất ra
						$max = $db->query("SELECT max(patient_code) as max FROM " . NV_PREFIXLANG . "_" . $module_name . "_patient")->fetchColumn();
						
						$data['patient_code'] = $max + 1;
						
						$db->query( 'INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_patient(userid,patient_group,branch,confess,other_contact,service_package_id,phone,gender,full_name,patient_code,birthday,address,work,history,patient_result,expect,note) VALUES('.$userid. ',' . $data['patient_group'] . ',' . $data['branch'] . ',"' . $data['confess'] . '","' . $data['other_contact'] . '",' . $data['service_package_id'] . ',"' . $data['phone'] . '","' . $data['gender'] . '","' . $data['full_name'] . '","' . $data['patient_code'] . '", ' . $birthday . ', "' . $data['address'] . '", "' . $data['work'] . '", "' . $data['history'] . '", "' . $data['patient_result'] . '", "' . $data['expect'] . '", "' . $data['note'] . '")' );
						
						
						nv_insert_logs( NV_LANG_DATA, $module_name, $lang_module['patient_add'], 'userid: ' . $userid, $admin_info['userid'] );
						$nv_Request->set_Session( $module_data . '_success', $lang_module['patient_add_success'] );
						
						
						
						$nv_Cache->delMod( $module_name );
					}
					else
					{
						if($data['check_admin'] == 1){
							$db->query( 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_patient SET work = "' . $data['work'] . '", patient_code = "'. $data['patient_code'] .'", history = "' . $data['history'] . '", patient_result = "' . $data['patient_result'] . '", expect = "' . $data['expect'] . '", note = "' . $data['note'] . '", address = "' . $data['address'] . '", birthday = ' . $birthday . ', full_name = "' . $data['full_name'] . '", confess = "' . $data['confess'] . '", patient_group = ' . $data['patient_group'] . ', branch = ' . $data['branch'] . ', other_contact = "' . $data['other_contact'] . '",service_package_id = ' . $data['service_package_id'] . ',phone = "' . $data['phone'] . '",gender = "' . $data['gender'] . '"  WHERE userid =' . $data['userid']);
							$stmt = $db->prepare( 'UPDATE ' . NV_USERS_GLOBALTABLE . '  SET 
							first_name="' . $data['first_name'] . '",
							last_name="' . $data['last_name'] . '",
							email="' . $data['email'] . '",
							username="' . $data['phone'] . '",
							md5username="' . $data['md5username'] . '",
							password="' . $password . '"
							WHERE userid=' . intval( $data['userid'] ) );
							if( $stmt->execute() )
							{
								nv_insert_logs( NV_LANG_DATA, $module_name, $lang_module['patient_edit'], 'userid: ' . $data['userid'], $admin_info['userid'] );
								
								$nv_Request->set_Session( $module_data . '_success', $lang_module['patient_edit_success'] );
								
								$nv_Cache->delMod( $module_name );
								
							}
							else
							{
								$error['warning'] = $lang_module['patient_error_save'];
								
							}
							
							$stmt->closeCursor();
							}else{
							
							$db->query( 'INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_patient_edit(userid,patient_group,confess,other_contact,service_package_id,phone,gender,full_name,birthday,address,work,history,patient_result,expect,note,user_require,time_require,email) VALUES('.$data['userid']. ',' . $data['patient_group'] . ',"' . $data['confess'] . '","' . $data['other_contact'] . '",' . $data['service_package_id'] . ',"' . $data['phone'] . '","' . $data['gender'] . '","' . $data['full_name'] . '", ' . $birthday . ', "' . $data['address'] . '", "' . $data['work'] . '", "' . $data['history'] . '", "' . $data['patient_result'] . '", "' . $data['expect'] . '", "' . $data['note'] . '", ' . $admin_info['userid'] . ', ' . NV_CURRENTTIME . ', "' . $data['email'] . '")' );
							
							$content1 .= '<div>Yêu cầu cập nhật hồ sơ bệnh án lúc: ' . date('d/m/Y - H:i:s',NV_CURRENTTIME) . '</div>';
							$content1 .= '<div>Người gửi yêu cầu: ' . $admin_info['username'] . '</div>';
							$content1 .= '<div>Bệnh nhân: ' . $data['full_name'] . '</div>';
							$content1 .= '<div>Số điện thoại: ' . $data['phone'] . '</div>';
							$email_title = 'Thông báo cập nhật hồ sơ bệnh án';
							// $global_config['site_email'] = 'binhbo661@gmail.com';
							$a = nv_sendmail(array($global_config['site_name'], $config_email['sender_email']['config_value']), $global_config['site_email'], sprintf($email_title, $module_info['custom_title'], $order_code), $content1);
						}
						
					}
				}
				catch ( PDOException $e )
				{
					$error['warning'] = $lang_module['patient_error_save'];
					// var_dump( $e ); die();
				}
			}
			
			if( empty( $error ) )
			{
				
				Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
				die();
			}
			
		}
		
		$xtpl = new XTemplate( 'patient_add.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
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
		if($data['confess'] == 'Quý khách'){
			$xtpl->assign( 'SELECTED1', 'selected' );
			}else if($data['confess'] == 'Cô'){
			$xtpl->assign( 'SELECTED2', 'selected' );
			}else if($data['confess'] == 'Chú'){
			$xtpl->assign( 'SELECTED3', 'selected' );
			}else if($data['confess'] == 'Anh'){
			$xtpl->assign( 'SELECTED4', 'selected' );
			}else if($data['confess'] == 'Chị'){
			$xtpl->assign( 'SELECTED5', 'selected' );
		}
		
		$xtpl->assign( 'DATA', $data );
		$xtpl->assign( 'CANCEL', NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=" . $op );
		$xtpl->assign( 'TOKEN', md5( $client_info['session_id'] . $global_config['sitekey'] ) );
		
		if(true){
			$xtpl->assign( 'BUTTON_SUBMIT', ( $data['userid'] == 0 ) ? $lang_module['patient_create'] : $lang_module['patient_update'] );
			$list_require = $db->query('SELECT * FROM ' . TABLE_APPOINTMENT_NAME . '_patient_edit WHERE userid = ' . $data['userid'])->fetchAll();
			
			foreach ($list_require as $key => $value) {
				$check_patient = $db->query('SELECT * FROM ' . TABLE_APPOINTMENT_NAME . '_patient WHERE userid = ' . $value['userid'])->fetch();
				if($check_patient){
					$value['user_require'] = $check_patient['full_name'];
					}else{
					$info_user_require = $db->query('SELECT * FROM ' . NV_USERS_GLOBALTABLE . ' WHERE userid = ' . $value['user_require'])->fetch();
					
					$value['user_require'] = $info_user_require['first_name'] . $info_user_require['last_name'];
				}
				
				
				$value['time_require'] = date('d/m/Y - H:i:s',$value['time_require']);
				if($value['gender'] == 'M'){
					$value['gender'] = 'Nam';
					}else{
					$value['gender'] = 'Nữ';
				}
				$value['birthday'] = date('d/m/Y',$value['birthday']);
				$value['patient_group'] = get_name_patient_group($value['patient_group'])['title'];
				$value['service_package_id'] = get_name_ServicePackage($value['service_package_id'])['title'];
				if($value['using_patient'] == 1){
					
					$xtpl->parse( 'main.list_require.loop.using' );
					}else{
					
					$xtpl->assign( 'LOOP', $value);
					$xtpl->parse( 'main.list_require.loop.no_using' );
				}
				
				$xtpl->assign( 'LOOP', $value);
				$xtpl->parse( 'main.list_require.loop' );
			}
			$xtpl->parse( 'main.admin' );
			$xtpl->parse( 'main.list_require' );
			}else{
			$xtpl->parse( 'main.no_admin' );
		}
		
		if( $data['userid'] > 0 )
		{
			
			$xtpl->parse( 'main.userlog' );
			
		}
		
		
		foreach( $arrayGender as $key => $name )
		{
			$xtpl->assign( 'GENDER', array('key'=> $key, 'name'=> $name, 'selected'=> ( $data['gender'] == $key ) ? 'selected="selected"' : '') );
			$xtpl->parse( 'main.gender' );
		}
		
		$list_patient_group = get_group_patient();
		foreach( $list_patient_group as $key => $name )
		{
			if(!$data['patient_group']){
				$data['patient_group'] = 0;
			}
			$xtpl->assign( 'PATIENT_GROUP', array('key'=> $key, 'title'=> $name['title'], 'selected'=> ( $data['patient_group'] == $key ) ? 'selected="selected"' : '') );
			$xtpl->parse( 'main.patient_group' );
		}
		
		
		// chi nhánh
		$list_Branch = getBranch();
		foreach( $list_Branch as $key => $name )
		{
			if(!$data['branch']){
				$data['branch'] = 0;
			}
			$xtpl->assign( 'branch', array('key'=> $key, 'title'=> $name['title'], 'selected'=> ( $data['branch'] == $key ) ? 'selected="selected"' : '') );
			$xtpl->parse( 'main.branch' );
		}
		
		
		
		$getServicePackage = getServicePackage();
		
		foreach( $getServicePackage as $key => $name )
		{
			$xtpl->assign( 'SERVICEPACKAGE', array('key'=> $key, 'name'=> $name['title'], 'selected'=> ( $data['service_package_id'] == $key ) ? 'selected="selected"' : '') );
			$xtpl->parse( 'main.servicepackage' );
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
	
	/*show list patient*/
	
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
	
	
	
	$sql = 	NV_USERS_GLOBALTABLE . ' u INNER JOIN ' . TABLE_APPOINTMENT_NAME . '_patient p ON (u.userid = p.userid )';
	
	
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
	
	
	$xtpl = new XTemplate( 'patient.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
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
			$item['edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&action=edit&token=' . $item['token'] . '&userid=' . $item['userid'];
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
