<?php

/**
 * @Project NUKEVIET 3.4
 * @Author ĐẶNG ĐÌNH TỨ (dlinhvan@gmail.com)
 * @Copyright (C) 2010 webdep24.com All rights reserved
 * @Createdate 10/08/2012 08:00
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

if( ACTION_METHOD == 'download' )
{

	$file_name = $nv_Request->get_string( 'file_name', 'get', '' );

	$file_path = NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . $file_name;

	if( file_exists( $file_path ) )
	{
		header( 'Content-Description: File Transfer' );
		header( 'Content-Type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
		header( 'Content-Disposition: attachment; filename=' . $file_name );
		header( 'Content-Transfer-Encoding: binary' );
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate' );
		header( 'Pragma: public' );
		header( 'Content-Length: ' . filesize( $file_path ) );
		readfile( $file_path );
		// ob_clean();
		flush();
		nv_deletefile( $file_path );
		exit();
	}
	else
	{
		die( 'File not exists !' );
	}
}


if( ACTION_METHOD == 'export_appointment' )
{

	ini_set( 'memory_limit', '512M' );

	set_time_limit( 0 );

	$data['customer_full_name'] = trim( $nv_Request->get_string( 'customer_full_name', 'get', '' ) );
	$data['customer_email'] = trim( $nv_Request->get_string( 'customer_email', 'get', '' ) );
	$data['customer_phone'] = trim( $nv_Request->get_string( 'customer_phone', 'get' ) );
	$data['date_from'] = trim( $nv_Request->get_title( 'date_from', 'get', '' ) );
	$data['date_to'] = trim( $nv_Request->get_title( 'date_to', 'get', '' ) );
	$data['service_id'] = $nv_Request->get_int( 'service_id', 'get', 0 );


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

	$sql = TABLE_APPOINTMENT_NAME . '_appointment';


	$implode = array();

	if( $data['customer_full_name'] )
	{
		$implode[]= "customer_full_name LIKE '%" . $db->dblikeescape( $data['customer_full_name'] ) . "%'";
		$base_url.= '&amp;customer_full_name=' . $data['customer_full_name'];
		$base_url_order.= '&amp;customer_full_name=' . $data['customer_full_name'];
	}
	if( $data['customer_phone'] )
	{
		$implode[]= "customer_phone LIKE '%" . $db->dblikeescape( $data['customer_phone'] ) . "%'";
		$base_url.= '&amp;customer_phone=' . $data['customer_phone'];
		$base_url_order.= '&amp;customer_phone=' . $data['customer_phone'];
	}
	if( $data['customer_email'] )
	{
		$implode[]= "customer_email LIKE '%" . $db->dblikeescape( $data['customer_email'] ) . "%'";
		$base_url.= '&amp;customer_email=' . $data['customer_email'];
		$base_url_order.= '&amp;customer_email=' . $data['customer_email'];
	}

	if( $data['service_id'] )
	{
		$implode[]= "service_id=" .  intval( $data['service_id'] );
		$base_url.= '&amp;service_id=' . $data['service_id'];
		$base_url_order.= '&amp;service_id=' . $data['service_id'];
	}
	if( $date_from && $date_to )
	{
		$implode[] = "(customer_date_booking BETWEEN " . intval( $date_from ) . " AND " . intval( $date_to ) . ")";
	}

	if( $implode )
	{
		$sql .= ' WHERE ' . implode( ' AND ', $implode );
	}

	$sort = $nv_Request->get_string( 'sort', 'get', '' );

	$order = $nv_Request->get_string( 'order', 'get' ) == 'desc' ? 'desc' : 'asc';

	$sort_data = array(
		'customer_full_name',
		'customer_email',
		'customer_phone',
		'customer_date_booking',
		'service_id',
		'is_send_sms',
		'date_added' );

	if( isset( $sort ) && in_array( $sort, $sort_data ) )
	{

		$sql .= ' ORDER BY ' . $sort;
	}
	else
	{
		$sql .= ' ORDER BY date_added';
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

	$db->sqlreset()->select( '*' )->from( $sql )->limit( $per_page )->offset( ( $page - 1 ) * $per_page );

	$result = $db->query( $db->sql() );
	if( $result->rowCount() )
	{

		$data_array = array();
		$dataContent = array();
		$i = 0;
		while( $row = $result->fetch() )
		{


			
			if($row['doctors_id']){
				$row['info_doctor'] = $db->query('SELECT * FROM ' . $db_config['prefix'] . '_users WHERE userid = ' . $row['userid'] )->fetch();
			}else{
				$row['info_doctor']['last_name'] = 'Chưa chỉ định';
				$row['info_doctor']['first_name'] = '';
			}

			$row['info_doctor']['name_doctor'] = $row['info_doctor']['last_name'] . ' ' . $row['info_doctor']['first_name'];

			if($row['gender'] == F){
				$row['gender'] = "Nữ";
			}else{
				$row['gender'] = "Nam";
			}
			$data_array['stt'] = $data_array['patient_code'];
			$data_array['customer_full_name'] = nv_unhtmlspecialchars( $row['customer_full_name'] );
			$data_array['customer_email'] = nv_unhtmlspecialchars( $row['customer_email']);
			$data_array['customer_phone'] = nv_unhtmlspecialchars( $row['customer_phone'] );
			$data_array['customer_message'] = nv_unhtmlspecialchars( $row['customer_message'] );
			$data_array['customer_date_booking'] = ' ' . nv_unhtmlspecialchars( date('d/m/Y - H:i:s',$row['customer_date_booking']) );
			$data_array['service_id'] = ' ' . nv_unhtmlspecialchars(getService($row['service_id'])[1]['service_name']);
			$data_array['branch_id'] = ' ' . nv_unhtmlspecialchars(getBranch($row['branch_id'])[1]['title']);
			
			$data_array['name_doctor'] = ' ' . nv_unhtmlspecialchars($row['info_doctor']['name_doctor']);

			
			$dataContent[] = $data_array;
		}

		/** Include PHPExcel */
		require_once ( NV_ROOTDIR . "/includes/PHPExcel.php" );

		$Excel_Cell_Begin = 1; // Dong bat dau viet du lieu

		$objReader = PHPExcel_IOFactory::createReader( 'Excel2007' );
		$objPHPExcel = $objReader->load( NV_ROOTDIR . "/modules/" . $module_file . "/template_excel/template.xlsx" );

		$objWorksheet = $objPHPExcel->getActiveSheet();

		$page_title = 'DANH SÁCH BỆNH NHÂN';
		$objWorksheet->setTitle( 'danh_sach_appointment' );

		// Set page orientation and size
		$objWorksheet->getPageSetup()->setOrientation( PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE );
		$objWorksheet->getPageSetup()->setPaperSize( PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4 );
		$objWorksheet->getPageSetup()->setHorizontalCentered( true );

		$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd( 1, $Excel_Cell_Begin );

		// Tieu de
		$array_title = array();
		$array_title[] = 'Mã KH';
		$array_title[] = 'customer_full_name';
		$array_title[] = 'customer_email';
		$array_title[] = 'customer_phone';
		$array_title[] = 'customer_message';
		$array_title[] = 'customer_date_booking';
		$array_title[] = 'service_id';
		$array_title[] = 'branch_id';
		
		$array_title[] = 'name_doctor';
		
		$columnIndex = 0;
		foreach( $array_title as $key_lang )
		{
			$TextColumnIndex = PHPExcel_Cell::stringFromColumnIndex( $columnIndex );
			$objWorksheet->getColumnDimension( $TextColumnIndex )->setAutoSize( true );
			$objWorksheet->setCellValue( $TextColumnIndex . $Excel_Cell_Begin, $lang_module[$key_lang] );
			$columnIndex++;
		}

		// Du lieu
		$array_key_data = array();
		$array_key_data[] = 'patient_code';
		$array_key_data[] = 'customer_full_name';
		$array_key_data[] = 'customer_email';
		$array_key_data[] = 'customer_phone';
		$array_key_data[] = 'customer_message';
		$array_key_data[] = 'customer_date_booking';
		$array_key_data[] = 'service_id';
		$array_key_data[] = 'branch_id';
		
		$array_key_data[] = 'name_doctor'; 
		
		$pRow = $Excel_Cell_Begin;
		foreach( $dataContent as $row )
		{
			$pRow++;
			$columnIndex = 0;

			foreach( $array_key_data as $key_data )
			{

				$TextColumnIndex = PHPExcel_Cell::stringFromColumnIndex( $columnIndex );
				$objWorksheet->setCellValue( $TextColumnIndex . $pRow, $row[$key_data] );
				$columnIndex++;
			}
		}

		$highestRow = $objWorksheet->getHighestRow(); // Tinh so dong du lieu
		$highestColumn = $objWorksheet->getHighestColumn(); // Tinh so cot du lieu

		//$objWorksheet->mergeCells('A1:' . $highestColumn . '1');
		// $objWorksheet->setCellValue( 'A1', $page_title );
		//$objWorksheet->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//$objWorksheet->getStyle('A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

		//$styleArray = array('borders' => array('outline' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('argb' => 'FF000000'))));

		//$objWorksheet->getStyle('A' . $Excel_Cell_Begin . ':' . $highestColumn . $highestRow)->applyFromArray($styleArray); // Tao duong bao

		//Redirect output to a client's web browser (Excel5)

		$file_name = 'danh_sach_lich_hen.xlsx';

		$file_path = NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . $file_name;

		header( 'Content-Type: application/vnd.ms-excel' );
		header( 'Content-Disposition: attachment;filename="' . $file_name . '"' );
		header( 'Cache-Control: max-age=0' );

		$objWriter = PHPExcel_IOFactory::createWriter( $objPHPExcel, 'Excel2007' );

		$objWriter->save( $file_path );

		$link = NV_BASE_ADMINURL . "index.php?" . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=export&action=download&file_name=' . $file_name;

		nv_jsonOutput( array( 'link' => $link ) );

		//$objWriter->save( 'php://output' );
		//exit;

	}
	else
	{
		nv_jsonOutput( array( 'error' => 'Không tìm thấy dữ liệu' ) );
	}

}



if( ACTION_METHOD == 'is_download' )
{

	ini_set( 'memory_limit', '512M' );

	set_time_limit( 0 );

	$base_url_order = $base_url = NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '='. $op;

	$per_page = 100;

	$page = $nv_Request->get_int( 'page', 'get', 1 );

	$data['full_name'] = trim( $nv_Request->get_string( 'full_name', 'get', '' ) );
	$data['email'] = trim( $nv_Request->get_string( 'email', 'get', '' ) );
	$data['phone'] = trim( $nv_Request->get_string( 'phone', 'get' ) );
	$data['date_from'] = trim( $nv_Request->get_title( 'date_from', 'get', '' ) );
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



	$sql = 	NV_USERS_GLOBALTABLE . ' u INNER JOIN ' . TABLE_APPOINTMENT_NAME . '_patient p ON (u.userid = p.userid)';


	$implode = array();

	if( $data['full_name'] )
	{
		$implode[]= "CONCAT(u.first_name,' ', u.last_name) LIKE '%" . $db->dblikeescape( $data['full_name'] ) . "%'";
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
		$sql .= ' WHERE ' . implode( ' AND ', $implode );
	}

	$sql .= ' GROUP BY p.userid';
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
		$sql .= ' ORDER BY date_added';
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

	$db->sqlreset()->select( 'u.userid, u.username, u.phone, CONCAT(u.first_name,\' \', u.last_name) AS full_name, u.email, u.gender, u.address, u.birthday, u.regdate, p.date_added, p.patient_group,p.confess,p.work,p.history,p.expect,p.patient_result,p.typemedicine' )->from( $sql )->limit( $per_page )->offset( ( $page - 1 ) * $per_page );

	$result = $db->query( $db->sql() );


	if( $result->rowCount() )
	{

		$data_array = array();
		$dataContent = array();
		$i = 0;
		while( $row = $result->fetch() )
		{
			if($row['gender'] == F){
				$row['gender'] = "Nữ";
			}else{
				$row['gender'] = "Nam";
			}

			$data_array['stt'] = ++$i;
			$data_array['confess'] = nv_unhtmlspecialchars( $row['confess'] );
			$data_array['gender'] = nv_unhtmlspecialchars( $row['gender'] );
			if($row['birthday']){
				$data_array['birthday'] = nv_unhtmlspecialchars( date('d/m/Y',$row['birthday']));
			}else{
				$data_array['birthday'] = '';
			}
			
			$data_array['full_name'] = nv_unhtmlspecialchars( $row['full_name'] );
			$data_array['phone'] = nv_unhtmlspecialchars( $row['phone'] );
			$data_array['address'] = nv_unhtmlspecialchars( $row['address'] );
			$data_array['email'] = ' ' . nv_unhtmlspecialchars( $row['email'] );
			$data_array['work'] = ' ' . nv_unhtmlspecialchars( $row['work'] );
			$data_array['history'] = ' ' . nv_unhtmlspecialchars( $row['history'] );
			$data_array['patient_result'] = ' ' . nv_unhtmlspecialchars( $row['patient_result'] );
			$data_array['expect'] = ' ' . nv_unhtmlspecialchars( $row['expect'] );
			if($row['patient_group']){
				$data_array['patient_group'] = nv_unhtmlspecialchars( get_name_patient_group($row['patient_group'])['title'] );
			}else{
				$data_array['patient_group'] = nv_unhtmlspecialchars( 'Không xác định' );
			}
			$data_array['typemedicine'] = ' ' . nv_unhtmlspecialchars( $row['typemedicine'] );
			
			
			$dataContent[] = $data_array;
		}

		/** Include PHPExcel */
		require_once ( NV_ROOTDIR . "/includes/PHPExcel.php" );

		$Excel_Cell_Begin = 1; // Dong bat dau viet du lieu

		$objReader = PHPExcel_IOFactory::createReader( 'Excel2007' );
		$objPHPExcel = $objReader->load( NV_ROOTDIR . "/modules/" . $module_file . "/template_excel/template.xlsx" );

		$objWorksheet = $objPHPExcel->getActiveSheet();

		$page_title = 'DANH SÁCH KHÁCH HÀNG';
		$objWorksheet->setTitle( 'danh_sach_appointment' );

		// Set page orientation and size
		$objWorksheet->getPageSetup()->setOrientation( PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE );
		$objWorksheet->getPageSetup()->setPaperSize( PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4 );
		$objWorksheet->getPageSetup()->setHorizontalCentered( true );

		$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd( 1, $Excel_Cell_Begin );

		// Tieu de
		$array_title = array();
		$array_title[] = 'patient_code'; 
		$array_title[] = 'confess';
		$array_title[] = 'full_name';
		$array_title[] = 'gender';
		$array_title[] = 'birthday';
		
		$array_title[] = 'email';
		$array_title[] = 'phone';
		$array_title[] = 'other_contact';
		
		$array_title[] = 'address';
		$array_title[] = 'work';
		$array_title[] = 'history';
		$array_title[] = 'patient_result';
		$array_title[] = 'expect';
		$array_title[] = 'typemedicine';
		
		
		
		
		$array_title[] = 'patient_group';
		
		$columnIndex = 0;
		foreach( $array_title as $key_lang )
		{
			$TextColumnIndex = PHPExcel_Cell::stringFromColumnIndex( $columnIndex );
			$objWorksheet->getColumnDimension( $TextColumnIndex )->setAutoSize( true );
			$objWorksheet->setCellValue( $TextColumnIndex . $Excel_Cell_Begin, $lang_module[$key_lang] );
			$columnIndex++;
		}

		// Du lieu
		$array_key_data = array();
		$array_key_data[] = 'stt';
		$array_key_data[] = 'confess';
		$array_key_data[] = 'full_name';
		$array_key_data[] = 'gender';
		$array_key_data[] = 'birthday';
		
		$array_key_data[] = 'email';
		$array_key_data[] = 'phone';
		$array_key_data[] = 'other_contact';
		$array_key_data[] = 'address';
		$array_key_data[] = 'work';
		$array_key_data[] = 'history';
		$array_key_data[] = 'patient_result';
		$array_key_data[] = 'expect';
		$array_key_data[] = 'typemedicine';
		
		
		
		$array_key_data[] = 'patient_group';
		
		$pRow = $Excel_Cell_Begin;
		foreach( $dataContent as $row )
		{
			$pRow++;
			$columnIndex = 0;

			foreach( $array_key_data as $key_data )
			{

				$TextColumnIndex = PHPExcel_Cell::stringFromColumnIndex( $columnIndex );
				$objWorksheet->setCellValue( $TextColumnIndex . $pRow, $row[$key_data] );
				$columnIndex++;
			}
		}

		$highestRow = $objWorksheet->getHighestRow(); // Tinh so dong du lieu
		$highestColumn = $objWorksheet->getHighestColumn(); // Tinh so cot du lieu

		//$objWorksheet->mergeCells('A1:' . $highestColumn . '1');
		// $objWorksheet->setCellValue( 'A1', $page_title );
		//$objWorksheet->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//$objWorksheet->getStyle('A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

		//$styleArray = array('borders' => array('outline' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('argb' => 'FF000000'))));

		//$objWorksheet->getStyle('A' . $Excel_Cell_Begin . ':' . $highestColumn . $highestRow)->applyFromArray($styleArray); // Tao duong bao

		//Redirect output to a client's web browser (Excel5)

		$file_name = 'danh_sach_benh_nhan.xlsx';

		$file_path = NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . $file_name;

		header( 'Content-Type: application/vnd.ms-excel' );
		header( 'Content-Disposition: attachment;filename="' . $file_name . '"' );
		header( 'Cache-Control: max-age=0' );

		$objWriter = PHPExcel_IOFactory::createWriter( $objPHPExcel, 'Excel2007' );

		$objWriter->save( $file_path );

		$link = NV_BASE_ADMINURL . "index.php?" . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=export&action=download&file_name=' . $file_name;

		nv_jsonOutput( array( 'link' => $link ) );

		//$objWriter->save( 'php://output' );
		//exit;

	}
	else
	{
		nv_jsonOutput( array( 'error' => 'Không tìm thấy dữ liệu' ) );
	}

}








































if( ACTION_METHOD == 'patient' )
{

	ini_set( 'memory_limit', '512M' );

	set_time_limit( 0 );

	$base_url_order = $base_url = NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '='. $op;

	$per_page = 100;

	$page = $nv_Request->get_int( 'page', 'get', 1 );

	$data['full_name'] = trim( $nv_Request->get_string( 'full_name', 'get', '' ) );
	$data['email'] = trim( $nv_Request->get_string( 'email', 'get', '' ) );
	$data['phone'] = trim( $nv_Request->get_string( 'phone', 'get' ) );
	$data['date_from'] = trim( $nv_Request->get_title( 'date_from', 'get', '' ) );
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



	$sql = 	NV_USERS_GLOBALTABLE . ' u INNER JOIN ' . TABLE_APPOINTMENT_NAME . '_patient p ON (u.userid = p.userid)';


	$implode = array();

	if( $data['full_name'] )
	{
		$implode[]= "CONCAT(u.first_name,' ', u.last_name) LIKE '%" . $db->dblikeescape( $data['full_name'] ) . "%'";
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
		$sql .= ' WHERE ' . implode( ' AND ', $implode );
	}

	$sql .= ' GROUP BY p.userid';
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


	$num_items = $db->query( 'SELECT COUNT(*) FROM ' . $sql )->fetchColumn();
	$limit = 10000;
	$db->sqlreset()->select( 'u.userid,p.phone, u.username, p.full_name, u.email, p.gender, p.address, p.birthday, u.regdate, p.date_added,p.patient_group,p.branch,p.confess,p.work,p.history,p.expect,p.patient_result,p.note,p.other_contact,p.patient_code' )->from( $sql )->limit( $limit )->offset( ( $page - 1 ) * $per_page );
	

	$result = $db->query( $db->sql() );
	
	

	if( $result->rowCount() )
	{

		$data_array = array();
		$dataContent = array();
		$i = 0;
		while( $row = $result->fetch() )
		{
			if($row['gender'] == F){
				$row['gender'] = "Nữ";
			}else{
				$row['gender'] = "Nam";
			}

			$data_array['stt'] = nv_unhtmlspecialchars( $row['patient_code'] );
			$data_array['confess'] = nv_unhtmlspecialchars( $row['confess'] );
			$data_array['gender'] = nv_unhtmlspecialchars( $row['gender'] );
			if($row['birthday']){
				$data_array['birthday'] = nv_unhtmlspecialchars( date('d/m/Y',$row['birthday']));
			}else{
				$data_array['birthday'] = '';
			}
			
			$data_array['full_name'] = nv_unhtmlspecialchars( $row['full_name'] );
			$data_array['phone'] = nv_unhtmlspecialchars( $row['phone'] );
			$data_array['address'] = nv_unhtmlspecialchars( $row['address'] );
			$data_array['email'] = ' ' . nv_unhtmlspecialchars( $row['email'] );
			$data_array['work'] = ' ' . nv_unhtmlspecialchars( $row['work'] );
			$data_array['history'] = ' ' . nv_unhtmlspecialchars( $row['history'] );
			$data_array['patient_result'] = ' ' . nv_unhtmlspecialchars( $row['patient_result'] );
			$data_array['expect'] = ' ' . nv_unhtmlspecialchars( $row['expect'] );
			$data_array['other_contact'] = ' ' . nv_unhtmlspecialchars( $row['other_contact'] );
			$data_array['patient_group_id'] = ' ' . nv_unhtmlspecialchars( $row['patient_group'] );
			if($row['patient_group']){
				$data_array['patient_group'] = nv_unhtmlspecialchars( get_name_patient_group($row['patient_group'])['title'] );
			}else{
				$data_array['patient_group'] = nv_unhtmlspecialchars( 'Không xác định' );
			}
			
			if($row['branch']){
				$data_array['branch'] = nv_unhtmlspecialchars( getBranch_id($row['branch'])['title'] );
			}else{
				$data_array['branch'] = nv_unhtmlspecialchars( 'Không xác định' );
			}
			
			
			$data_array['note'] = ' ' . nv_unhtmlspecialchars( $row['note'] );
			
			
			$dataContent[] = $data_array;
		}

//print_r($dataContent);die;
		/** Include PHPExcel */
		require_once ( NV_ROOTDIR . "/includes/PHPExcel.php" );

		$Excel_Cell_Begin = 1; // Dong bat dau viet du lieu

		$objReader = PHPExcel_IOFactory::createReader( 'Excel2007' );
		$objPHPExcel = $objReader->load( NV_ROOTDIR . "/modules/" . $module_file . "/template_excel/patient.xlsx" );

		$objWorksheet = $objPHPExcel->getActiveSheet();

		$page_title = 'DANH SÁCH KHÁCH HÀNG';
		$objWorksheet->setTitle( 'danh_sach_appointment' );

		// Set page orientation and size
		$objWorksheet->getPageSetup()->setOrientation( PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE );
		$objWorksheet->getPageSetup()->setPaperSize( PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4 );
		$objWorksheet->getPageSetup()->setHorizontalCentered( true );

		$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd( 1, $Excel_Cell_Begin );

		// Tieu de
		$array_title = array();
		$array_title[] = 'patient_code'; 
		$array_title[] = 'confess';
		$array_title[] = 'full_name';
		$array_title[] = 'gender';
		$array_title[] = 'birthday';
		
		$array_title[] = 'email';
		$array_title[] = 'phone';
		$array_title[] = 'other_contact';
		
		$array_title[] = 'address';
		$array_title[] = 'work';
		$array_title[] = 'history';
		$array_title[] = 'patient_result';
		$array_title[] = 'expect';
		$array_title[] = 'note';
		
		$array_title[] = 'patient_group_id';
		$array_title[] = 'patient_group';
		$array_title[] = 'branch_title';
		
		$columnIndex = 0;
		foreach( $array_title as $key_lang )
		{
			$TextColumnIndex = PHPExcel_Cell::stringFromColumnIndex( $columnIndex );
			$objWorksheet->getColumnDimension( $TextColumnIndex )->setAutoSize( true );
			$objWorksheet->setCellValue( $TextColumnIndex . $Excel_Cell_Begin, $lang_module[$key_lang] );
			$columnIndex++;
		}

		// Du lieu
		$array_key_data = array();
		$array_key_data[] = 'stt';
		$array_key_data[] = 'confess';
		$array_key_data[] = 'full_name';
		$array_key_data[] = 'gender';
		$array_key_data[] = 'birthday';
		
		$array_key_data[] = 'email';
		$array_key_data[] = 'phone';
		$array_key_data[] = 'other_contact';
		$array_key_data[] = 'address';
		$array_key_data[] = 'work';
		$array_key_data[] = 'history';
		$array_key_data[] = 'patient_result';
		$array_key_data[] = 'expect';
		$array_key_data[] = 'note';
		
		$array_key_data[] = 'patient_group_id';
		$array_key_data[] = 'patient_group';
		$array_key_data[] = 'branch';
		$pRow = $Excel_Cell_Begin;
		foreach( $dataContent as $row )
		{
			$pRow++;
			$columnIndex = 0;

			foreach( $array_key_data as $key_data )
			{

				$TextColumnIndex = PHPExcel_Cell::stringFromColumnIndex( $columnIndex );
				$objWorksheet->setCellValue( $TextColumnIndex . $pRow, $row[$key_data] );
				$columnIndex++;
			}
		}

		$highestRow = $objWorksheet->getHighestRow(); // Tinh so dong du lieu
		$highestColumn = $objWorksheet->getHighestColumn(); // Tinh so cot du lieu

		//$objWorksheet->mergeCells('A1:' . $highestColumn . '1');
		// $objWorksheet->setCellValue( 'A1', $page_title );
		//$objWorksheet->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//$objWorksheet->getStyle('A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

		//$styleArray = array('borders' => array('outline' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('argb' => 'FF000000'))));

		//$objWorksheet->getStyle('A' . $Excel_Cell_Begin . ':' . $highestColumn . $highestRow)->applyFromArray($styleArray); // Tao duong bao

		//Redirect output to a client's web browser (Excel5)

		$file_name = 'danh_sach_benh_nhan.xlsx';

		$file_path = NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . $file_name;

		header( 'Content-Type: application/vnd.ms-excel' );
		header( 'Content-Disposition: attachment;filename="' . $file_name . '"' );
		header( 'Cache-Control: max-age=0' );

		$objWriter = PHPExcel_IOFactory::createWriter( $objPHPExcel, 'Excel2007' );

		$objWriter->save( $file_path );

		$link = NV_BASE_ADMINURL . "index.php?" . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=export&action=download&file_name=' . $file_name;

		nv_jsonOutput( array( 'link' => $link ) );

		//$objWriter->save( 'php://output' );
		//exit;
	}
	else
	{
		nv_jsonOutput( array( 'error' => 'Không tìm thấy dữ liệu' ) );
	}

}

















if( ACTION_METHOD == 'export_salary' )
{
	ini_set( 'memory_limit', '512M' );

	set_time_limit( 0 );

	$doctor_id=$nv_Request->get_int('doctor_id', 'get,post',0);
	$month=$nv_Request->get_string('month', 'get,post','');
	$month_start = '1/' . $month;
	$info_doctor = get_info_doctor($doctor_id);
	$month_start = convertToTimeStamp( $month_start, 0, 0, 0, 0 );
	$lastdate = date("t", $month_start ); 
	$month_end = $lastdate . '/' . $month;
	$month_end = convertToTimeStamp( $month_end, 0, 23, 23, 23 );
	
	$list_appointment = $db->query('SELECT t2.*, t1.date_added FROM ' . TABLE_APPOINTMENT_NAME . '_patient_appointment t1 INNER JOIN ' . TABLE_APPOINTMENT_NAME . '_patient t2 ON t1.patient_id = t2.userid WHERE t1.doctors_id = ' . $doctor_id . ' AND t1.date_added >= ' . $month_start . ' GROUP BY t1.patient_id');

	if( $list_appointment->rowCount() )
	{

		$data_array = array();
		$dataContent = array();
		$i = 0;
		while( $row = $list_appointment->fetch() )
		{
			if($row['gender'] == F){
				$row['gender'] = "Nữ";
			}else{
				$row['gender'] = "Nam";
			}

			$data_array['stt'] = $row['patient_code'];
			$data_array['confess'] = nv_unhtmlspecialchars( $row['confess'] );
			$data_array['gender'] = nv_unhtmlspecialchars( $row['gender'] );
			if($row['birthday']){
				$data_array['birthday'] = nv_unhtmlspecialchars( date('d/m/Y',$row['birthday']));
			}else{
				$data_array['birthday'] = '';
			}
			
			$data_array['customer_full_name'] = nv_unhtmlspecialchars( $row['full_name'] );
			$data_array['customer_phone'] = nv_unhtmlspecialchars( $row['phone'] );
			$data_array['customer_message'] = nv_unhtmlspecialchars( $row['other_contact'] );
			
			$data_array['customer_email'] = $db->query('SELECT email FROM vidoco_users WHERE userid =' . $row['userid'])->fetchColumn();
			
			$data_array['work'] = ' ' . nv_unhtmlspecialchars( $row['work'] );
			$data_array['history'] = ' ' . nv_unhtmlspecialchars( $row['history'] );
			$data_array['patient_result'] = ' ' . nv_unhtmlspecialchars( $row['patient_result'] );
			$data_array['expect'] = ' ' . nv_unhtmlspecialchars( $row['expect'] );
			if($row['patient_group']){
				$data_array['patient_group'] = nv_unhtmlspecialchars( get_name_patient_group($row['patient_group'])['title'] );
			}else{
				$data_array['patient_group'] = nv_unhtmlspecialchars( 'Không xác định' );
			}
			$data_array['typemedicine'] = ' ' . nv_unhtmlspecialchars( $row['typemedicine'] );
			
			
			$dataContent[] = $data_array;
		}

		/** Include PHPExcel */
		require_once ( NV_ROOTDIR . "/includes/PHPExcel.php" );

		$Excel_Cell_Begin = 1; // Dong bat dau viet du lieu

		$objReader = PHPExcel_IOFactory::createReader( 'Excel2007' );
		$objPHPExcel = $objReader->load( NV_ROOTDIR . "/modules/" . $module_file . "/template_excel/salary.xlsx" );

		$objWorksheet = $objPHPExcel->getActiveSheet();

		$page_title = 'DANH SÁCH BỆNH NHÂN THÁNG ' . $month . ' của ' . $info_doctor['first_name'] . ' ' . $info_doctor['last_name'];
		$objWorksheet->setTitle( 'danh_sach_appointment' );

		// Set page orientation and size
		$objWorksheet->getPageSetup()->setOrientation( PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE );
		$objWorksheet->getPageSetup()->setPaperSize( PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4 );
		$objWorksheet->getPageSetup()->setHorizontalCentered( true );

		$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd( 1, $Excel_Cell_Begin );

		// Tieu de
		$array_title = array();
		$array_title[] = 'Mã KH';
		$array_title[] = 'full_name';
		$array_title[] = 'gender';
		$array_title[] = 'birthday';
		$array_title[] = 'email';
		$array_title[] = 'phone';
		$array_title[] = 'other_contact';
		$array_title[] = 'thong_tin_can_tu_van';
		$array_title[] = 'work';
		$array_title[] = 'history';
		$array_title[] = 'patient_result';
		$array_title[] = 'expect';
		$array_title[] = 'typemedicine';
		$array_title[] = 'patient_group';
		
		$columnIndex = 1;
		foreach( $array_title as $key_lang )
		{
			$TextColumnIndex = PHPExcel_Cell::stringFromColumnIndex( $columnIndex );
			$objWorksheet->getColumnDimension( $TextColumnIndex )->setAutoSize( true );
			$objWorksheet->setCellValue( $TextColumnIndex . $Excel_Cell_Begin, $lang_module[$key_lang] );
			$columnIndex++;
		}

		// Du lieu
		$array_key_data = array();
		$array_key_data[] = 'stt';
		$array_key_data[] = 'customer_full_name';
		$array_key_data[] = 'gender';
		$array_key_data[] = 'birthday';
		
		$array_key_data[] = 'customer_email';
		$array_key_data[] = 'customer_phone';
		$array_key_data[] = 'other_contact';
		$array_key_data[] = 'customer_message';
		$array_key_data[] = 'work';
		$array_key_data[] = 'history';
		$array_key_data[] = 'patient_result';
		$array_key_data[] = 'expect';
		$array_key_data[] = 'typemedicine';
		$array_key_data[] = 'patient_group';
		$total = 'Tổng số: ' . count($list_appointment) . ' ca khám bệnh';
		$TextColumnIndex = PHPExcel_Cell::stringFromColumnIndex( 0 );
		$objWorksheet->setCellValue( $TextColumnIndex . 1, $page_title );
		$objWorksheet->setCellValue( $TextColumnIndex . 2, $total );
		$pRow = 3;
		

		foreach( $dataContent as $row )
		{
			$pRow++;
			$columnIndex = 0;

			foreach( $array_key_data as $key_data )
			{

				$TextColumnIndex = PHPExcel_Cell::stringFromColumnIndex( $columnIndex );
				$objWorksheet->setCellValue( $TextColumnIndex . $pRow, $row[$key_data] );
				$columnIndex++;
			}
		}

		$highestRow = $objWorksheet->getHighestRow(); // Tinh so dong du lieu

		$highestColumn = $objWorksheet->getHighestColumn(); // Tinh so cot du lieu

		//$objWorksheet->mergeCells('A1:' . $highestColumn . '1');
		// $objWorksheet->setCellValue( 'A1', $page_title );
		//$objWorksheet->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//$objWorksheet->getStyle('A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

		//$styleArray = array('borders' => array('outline' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('argb' => 'FF000000'))));

		//$objWorksheet->getStyle('A' . $Excel_Cell_Begin . ':' . $highestColumn . $highestRow)->applyFromArray($styleArray); // Tao duong bao

		//Redirect output to a client's web browser (Excel5)

		$file_name = 'danh_sach_benh_nhan_' . str_replace('/', '_', $month). '.xlsx';
		$file_path = NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . $file_name;

		header( 'Content-Type: application/vnd.ms-excel' );
		header( 'Content-Disposition: attachment;filename="' . $file_name . '"' );
		header( 'Cache-Control: max-age=0' );

		$objWriter = PHPExcel_IOFactory::createWriter( $objPHPExcel, 'Excel2007' );

		$objWriter->save( $file_path );

		$link = NV_BASE_ADMINURL . "index.php?" . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=export&action=download&file_name=' . $file_name;

		nv_jsonOutput( array( 'link' => $link ) );

		//$objWriter->save( 'php://output' );
		//exit;

	}
	else
	{
		nv_jsonOutput( array( 'error' => 'Không tìm thấy dữ liệu' ) );
	}

}