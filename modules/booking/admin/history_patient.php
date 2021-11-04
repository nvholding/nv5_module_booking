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


$where = '';

$search = array();

$search['brand'] = $nv_Request->get_int('brand', 'post,get',0);
$search['package'] = $nv_Request->get_int('package', 'post,get',0);
$search['keyword'] = $nv_Request->get_title('keyword', 'post,get','');

// SEARCH
$base_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op;



if($search['keyword'])
{
	$where .= ' AND (t1.patient_code ="'. $search['keyword'] .'" OR t1.full_name like "%'.  $search['keyword'] .'%" OR t1.phone = "' . $search['keyword'] .'")' ;
	$base_url .= '&keyword='. $search['keyword'];
}

if($search['brand'])
{
	$where .= ' AND t1.branch='. $search['brand'] ;
	$base_url .= '&brand='. $search['brand'];
}

if($search['package'])
{
	$where .= ' AND t2.service_id='. $search['package'] ;
	$base_url .= '&package='. $search['package'];
}


// Fetch Limit
$show_view = false;
if (!$nv_Request->isset_request('id', 'post,get')) {
    $show_view = true;
    $per_page = 20;
    $page = $nv_Request->get_int('page', 'post,get', 1);
    $db->sqlreset()
        ->select('count(DISTINCT(t1.userid))')
        ->from(NV_PREFIXLANG . '_' . $module_data . '_patient t1')
		->join('INNER JOIN ' . NV_PREFIXLANG . '_' . $module_data . '_buy_service t2 ON t1.userid = t2.userid')
		->where('1=1' . $where);
	
    $sth = $db->prepare($db->sql());
    $sth->execute();
	
	$num_items = $sth->fetchColumn();
	 

    $db->select('DISTINCT(t1.userid), t1.*, (select count(id) as count from ' . NV_PREFIXLANG . '_' . $module_data . '_patient_appointment WHERE patient_id = t1.userid) as count')
        ->order('t1.patient_code ASC')
        ->limit($per_page)
        ->offset(($page - 1) * $per_page);
		
    $sth = $db->prepare($db->sql());
	//die($db->sql());
    $sth->execute();
}



$xtpl = new XTemplate('history_patient.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
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



$service_package = service_package();

foreach($service_package as $package)
{
	if($package['service_package_id'] == $search['package'])
	{
		$xtpl->assign('package_selected', 'selected=selected');
	}
	else
	{
		$xtpl->assign('package_selected', '');
	}
	
	$xtpl->assign('package', $package);
    $xtpl->parse('main.view.package');
}



if ($show_view) {
    
  
    $generate_page = nv_generate_page($base_url, $num_items, $per_page, $page);
    if (!empty($generate_page)) {
        $xtpl->assign('NV_GENERATE_PAGE', $generate_page);
        $xtpl->parse('main.view.generate_page');
    }
    $number = $page > 1 ? ($per_page * ($page - 1)) + 1 : 1;
	

    while ($view = $sth->fetch()) {
        $view['number'] = $number++;
       // PRINT_R( $view);DIE;
        $xtpl->assign('VIEW', $view);
        $xtpl->parse('main.view.loop');
    }
	
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
