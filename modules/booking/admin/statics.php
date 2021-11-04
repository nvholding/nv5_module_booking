<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2021 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 26 Apr 2021 03:50:11 GMT
 */

if (!defined('NV_IS_FILE_ADMIN'))
    die('Stop!!!');

/*
// tool cập nhật chi nhánh bác sĩ khám bệnh
$array_list = $db->query('SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_patient_appointment')->fetchAll();
 
foreach($array_list as $kham)
{
	$branch_id = get_branch_id($kham['doctors_id']);
	
	$db->query('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_patient_appointment SET branch_id = '. $branch_id .' WHERE id = '. $kham['id']);
}
*/

if ($nv_Request->isset_request('brand_id_ajax', 'post,get')) {
	
	$brand_id = $nv_Request->get_int('brand_id_ajax', 'post,get',0);
	
	$where = '';
	
	if($brand_id)
	$where = ' AND branch_id =' . $brand_id;

	
	$list = $db->query('SELECT u.userid, u.last_name, u.first_name FROM vidoco_users u RIGHT JOIN vidoco_users_groups_users gu ON (u.userid = gu.userid) LEFT JOIN vidoco_vi_booking_branch_users bu ON (u.userid = bu.userid) WHERE u.active = 1 AND gu.group_id=10 AND 1=1' . $where)->fetchAll();
	
	$html = '<option value="0">-- Chọn bác sĩ --</option>';
	
	foreach($list as $doctor)
	{
		$html .= '<option value="'. $doctor['userid'] .'">'. $doctor['last_name'] .' '. $doctor['first_name'] .'</option>';
	}

	die($html);

}



$where = '';

$search = array();

$search['brand'] = $nv_Request->get_int('brand', 'post,get',0);
$search['doctor'] = $nv_Request->get_int('doctor', 'post,get',0);
$date_from = $nv_Request->get_title( 'date_from', 'post,get', '' );
$date_to = $nv_Request->get_title( 'date_to', 'post,get', '' );

if( preg_match( '/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $date_from, $m ) )
{

	$search['date_from'] = mktime( 0, 0, 0, $m[2], $m[1], $m[3] );
}
else
{
	$search['date_from'] = 0;
}


	
if( preg_match( '/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $date_to, $m ) )
{

	$search['date_to'] = mktime( 23, 59, 59, $m[2], $m[1], $m[3] );
}
else
{
	$search['date_to'] = 0;
}


// SEARCH
$base_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op;


if($search['brand'])
{
	$where .= ' AND t1.branch_id='. $search['brand'] ;
	$base_url .= '&brand='. $search['brand'];
}

if($search['doctor'])
{
	$where .= ' AND t1.doctors_id='. $search['doctor'] ;
	$base_url .= '&doctor='. $search['doctor'];
}

if($search['date_from'])
{
	$where .= ' AND t1.date_added >='. $search['date_from'] ;
	$base_url .= '&date_from='. $date_from;
}

if($search['date_to'])
{
	$where .= ' AND t1.date_added <='. $search['date_to'] ;
	$base_url .= '&date_to='. $date_to;
}

// Fetch Limit
$show_view = false;
if (!$nv_Request->isset_request('id', 'post,get')) {
    $show_view = true;
    $per_page = 20;
    $page = $nv_Request->get_int('page', 'post,get', 1);
    $db->sqlreset()
        ->select('count(DISTINCT t1.patient_id) as count')
        ->from(NV_PREFIXLANG . '_' . $module_data . '_patient_appointment t1')
		->join('INNER JOIN ' . NV_PREFIXLANG . '_' . $module_data . '_patient t2 ON t1.patient_id = t2.patient_id')
		->where('1=1' . $where);
	
    $sth = $db->prepare($db->sql());
    $sth->execute();
	
	$num_items = $sth->fetchColumn();
	 

    $db->select('t1.*, count(t1.id) as count, sum(t1.price) as total')
		->where('1=1' . $where . ' GROUP BY t1.patient_id')
        ->order('t1.date_added DESC')
        ->limit($per_page)
        ->offset(($page - 1) * $per_page);
		
    $sth = $db->prepare($db->sql());
	//die($db->sql());
    $sth->execute();
}


// XUẤT RA FILE EXCEL
$export_excel = $nv_Request->get_int('export_excel', 'get', 0);
if($export_excel == 1)
{	
	$db->sqlreset();
	// DANH SÁCH THÔNG TIN USER
	$db->select('t1.*, count(t1.id) as count, sum(t1.price) as total')
		->from(NV_PREFIXLANG . '_' . $module_data . '_patient_appointment t1')
		->join('INNER JOIN ' . NV_PREFIXLANG . '_' . $module_data . '_patient t2 ON t1.patient_id = t2.patient_id')
		->where('1=1' . $where . ' GROUP BY t1.patient_id')
        ->order('t1.date_added DESC');
	$data = $db->query($db->sql())->fetchAll(); 
	
	require_once NV_ROOTDIR . '/modules/'. $module_file .'/Classes/PHPExcel.php';

		//Khởi tạo đối tượng
	$excel = new PHPExcel();
		//Chọn trang cần ghi (là số từ 0->n)
	$excel->setActiveSheetIndex(0);
		//Tạo tiêu đề cho trang. (có thể không cần)
	$excel->getActiveSheet()->setTitle('Báo cáo đơn đặt hàng');

		//Xét chiều rộng cho từng, nếu muốn set height thì dùng setRowHeight()
		
	
	
	$excel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
	$excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
	$excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
	$excel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
	$excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
	$excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
	
	$excel->getActiveSheet()->mergeCells('A1:F1');
	
	// thông tin thầy
	$ten_thay = ', Tất cả thầy';
	if($search['doctor'])
	{
		$doctor_excel = get_info_doctor($search['doctor']);
		$ten_thay = ', Thầy ' . $doctor_excel['last_name'] . ' ' . $doctor_excel['first_name'];
	}
	      
	// thông tin chi nhánh
	if($search['brand'])
	{
		$brand = getBranch_id($search['brand']);
		$ten_thay .= ' - Chi nhánh '. $brand['title'];
	}
 
	$ngaytu_excel = '';
	if($search['date_from'])
	{
		$ngaytu_excel = ', từ ngày '. date('d/m/Y',$search['date_from']);
	}

	$ngayden_excel = '';
	if($search['date_to'] and $search['date_from'])
	{
		$ngayden_excel = ' - '. date('d/m/Y',$search['date_to']);
	}
	elseif($search['date_to'])
	{
		$ngayden_excel = ', Đến ngày '. date('d/m/Y',$search['date_to']);
	}
	
	
	
	$excel->getActiveSheet()->setCellValue('A1', '"Báo cáo doanh thu"'. $ten_thay .' '. $ngaytu_excel . $ngayden_excel .'.');
	$excel->getActiveSheet()->getStyle('A1:F1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$excel->getActiveSheet()->setCellValue('A3', 'STT');
		$excel->getActiveSheet()->setCellValue('B3', 'Mã KH');
		$excel->getActiveSheet()->setCellValue('C3', 'Tên KH');
		$excel->getActiveSheet()->setCellValue('D3', 'Số lần TL còn lại');
		$excel->getActiveSheet()->setCellValue('E3', 'Số lần TL đã dùng');
		$excel->getActiveSheet()->setCellValue('F3', 'Doanh thu');
		
		
		
		$numRow = 5;
		
		$stt = 1;
		$total_count = 0;
		foreach($data as $row){
		

		//print_r($row['email']);die;
		
		// lấy thông tin khách hàng
		$patient = get_parent_info($row['patient_id']);
		
		$doctor = get_info_doctor($row['doctors_id']);
		
		$total_count = $total_count + $row['count'];
		
		$row['total'] = price_format( $row['total'] );
		
			$excel->getActiveSheet()->setCellValue('A'.$numRow, $stt);
			$excel->getActiveSheet()->setCellValue('B'.$numRow, $patient['patient_code']);
			$excel->getActiveSheet()->setCellValue('C'.$numRow, $patient['full_name']);
			$excel->getActiveSheet()->setCellValue('D'.$numRow, $patient['kham_conlai']);
			$excel->getActiveSheet()->setCellValue('E'.$numRow, $row['count']);
			$excel->getActiveSheet()->setCellValue('F'.$numRow, $row['total']);
			
			$numRow++;
			$stt++;
		}
		
		$numRow = $numRow + 2;
		
		$tongtien = $db->query('SELECT sum(t1.price) FROM ' . TABLE_APPOINTMENT_NAME . '_patient_appointment t1, ' . TABLE_APPOINTMENT_NAME . '_patient t2 WHERE t1.patient_id = t2.patient_id ' . $where)->fetchColumn();
		
		$tongtien = price_format( $tongtien );
		
		$excel->getActiveSheet()->setCellValue('A'.$numRow, 'TỔNG TIỀN');
		$excel->getActiveSheet()->setCellValue('E'.$numRow, $total_count);
		$excel->getActiveSheet()->setCellValue('F'.$numRow, $tongtien);
		
		// Khởi tạo đối tượng PHPExcel_IOFactory để thực hiện ghi file
		// ở đây mình lưu file dưới dạng excel2007 và cho người dùng download luôn
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename="data.xls"');
		PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
		die();
	
}


$xtpl = new XTemplate('statics.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('NV_LANG_VARIABLE', NV_LANG_VARIABLE);
$xtpl->assign('NV_LANG_DATA', NV_LANG_DATA);
$xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('MODULE_UPLOAD', $module_upload);
$xtpl->assign('NV_ASSETS_DIR', NV_ASSETS_DIR);
$xtpl->assign('OP', $op);
$xtpl->assign('ROW', $row);


$real_week = nv_get_week_from_time( NV_CURRENTTIME );
$week = $real_week[0];
$year = $real_week[1];
$this_year = $real_week[1];
$time_per_week = 86400 * 7;
$time_start_year = mktime( 0, 0, 0, 1, 1, $year );
$time_first_week = $time_start_year - ( 86400 * ( date( 'N', $time_start_year ) - 1 ) );
	
$tuannay = array(
    'from' => nv_date( 'd/m/Y', $time_first_week + ( $week - 1 ) * $time_per_week ),
    'to' => nv_date( 'd/m/Y', $time_first_week + ( $week - 1 ) * $time_per_week + $time_per_week - 1 ),
);
$tuantruoc = array(
    'from' => nv_date( 'd/m/Y', $time_first_week + ( $week - 2 ) * $time_per_week ),
    'to' => nv_date( 'd/m/Y', $time_first_week + ( $week - 2 ) * $time_per_week + $time_per_week - 2 ),
);
$tuankia = array(
    'from' => nv_date( 'd/m/Y', $time_first_week + ( $week - 3 ) * $time_per_week ),
    'to' => nv_date( 'd/m/Y', $time_first_week + ( $week - 3 ) * $time_per_week + $time_per_week - 3 ),
);

$thangnay = array(
    'from' => date( 'd/m/Y', strtotime( 'first day of this month' ) ),
    'to' => date( 'd/m/Y', strtotime( 'last day of this month' ) ),
);
$thangtruoc = array(
    'from' => date( 'd/m/Y', strtotime( 'first day of last month' ) ),
    'to' => date( 'd/m/Y', strtotime( 'last day of last month' ) ),
);
$namnay = array(
    'from' => date( 'd/m/Y', strtotime( 'first day of january this year' ) ),
    'to' => date( 'd/m/Y', strtotime( 'last day of december this year' ) ),
);
$namtruoc = array(
    'from' => date( 'd/m/Y', strtotime( 'first day of january last year' ) ),
    'to' => date( 'd/m/Y', strtotime( 'last day of december last year' ) ),
);
$xtpl->assign( 'TUANNAY', $tuannay );

$xtpl->assign( 'TUANTRUOC', $tuantruoc );

$xtpl->assign( 'TUANKIA', $tuankia );

$xtpl->assign( 'HOMNAY', date( 'd/m/Y', NV_CURRENTTIME ) );
$xtpl->assign( 'HOMQUA', date( 'd/m/Y', strtotime( 'yesterday' ) ) );
$xtpl->assign( 'THANGNAY', $thangnay );

$xtpl->assign( 'THANGTRUOC', $thangtruoc );

$xtpl->assign( 'NAMNAY', $namnay );

$xtpl->assign( 'NAMTRUOC', $namtruoc );


if($search['date_from'])
{
	$search['date_from'] = date('d/m/Y',$search['date_from']);
}
else
{
	$search['date_from'] = '';
}


if($search['date_to'])
{
	$search['date_to'] = date('d/m/Y',$search['date_to']);
}
else
{
	$search['date_to'] = '';
}

$xtpl->assign('search', $search);




$list_brand = getBranch();

foreach($list_brand as $brand)
{
	if($brand['branch_id'] == $search['brand'])
	{
		$xtpl->assign('brand_selected', 'selected=selected');
	}
	else
	{
		$xtpl->assign('brand_selected', '');
	}
	
	$xtpl->assign('brand', $brand);
    $xtpl->parse('main.view.brand');
}

$list_doctor = $db->query('SELECT t1.* FROM ' . $db_config['prefix'] . '_users t1 RIGHT JOIN ' . $db_config['prefix'] . '_users_groups_users t2 ON t1.userid = t2.userid LEFT JOIN ' . TABLE_APPOINTMENT_NAME . '_branch_users t3 ON t1.userid = t3.userid WHERE t2.group_id=10')->fetchAll();

foreach($list_doctor as $doctor)
{
	if($doctor['userid'] == $search['doctor'])
	{
		$xtpl->assign('doctor_selected', 'selected=selected');
	}
	else
	{
		$xtpl->assign('doctor_selected', '');
	}
	$xtpl->assign('doctor', $doctor);
    $xtpl->parse('main.view.doctor');
}

if ($show_view) {
    
  
    $generate_page = nv_generate_page($base_url, $num_items, $per_page, $page);
    if (!empty($generate_page)) {
        $xtpl->assign('NV_GENERATE_PAGE', $generate_page);
        $xtpl->parse('main.view.generate_page');
    }
    $number = $page > 1 ? ($per_page * ($page - 1)) + 1 : 1;
	
	$tongtien = $db->query('SELECT sum(t1.price) FROM ' . TABLE_APPOINTMENT_NAME . '_patient_appointment t1, ' . TABLE_APPOINTMENT_NAME . '_patient t2 WHERE t1.patient_id = t2.patient_id ' . $where)->fetchColumn();
	
	$tongtien = price_format( $tongtien );
	$xtpl->assign('tongtien', $tongtien);
	
	$total_count = 0;
	
    while ($view = $sth->fetch()) {
        $view['number'] = $number++;
        
		$total_count = $total_count + $view['count'];
		
		// lấy thông tin khách hàng
		$patient = get_parent_info($view['patient_id']);
		$xtpl->assign('patient', $patient);
		
		$doctor = get_info_doctor($view['doctors_id']);
		$xtpl->assign('doctor', $doctor);
       
		$view['price'] = price_format( $view['price'] );
		$view['total'] = price_format( $view['total'] );
		$view['date_added'] = date( 'd/m/Y : H:i',$view['date_added'] );
		
		$view['link'] = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=patient/' . $patient['userid'], true );
		
        $xtpl->assign('VIEW', $view);
        $xtpl->parse('main.view.loop');
    }
	
	$total_count = price_format( $total_count );
	$xtpl->assign('total_count', $total_count);
	
	$total_count = price_format( $num_items );
	$xtpl->assign('num_items', $num_items);

	
    $xtpl->parse('main.view');
}


if (!empty($error)) {
    $xtpl->assign('ERROR', implode('<br />', $error));
    $xtpl->parse('main.error');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

$page_title = $lang_module['statics'];

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
