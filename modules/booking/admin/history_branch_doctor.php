<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2021 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Tue, 22 Jun 2021 03:11:06 GMT
 */

if (!defined('NV_IS_FILE_ADMIN'))
    die('Stop!!!');

if ($nv_Request->isset_request('delete_id', 'get') and $nv_Request->isset_request('delete_checkss', 'get')) {
    $id = $nv_Request->get_int('delete_id', 'get');
    $delete_checkss = $nv_Request->get_string('delete_checkss', 'get');
    if ($id > 0 and $delete_checkss == md5($id . NV_CACHE_PREFIX . $client_info['session_id'])) {
        $db->query('DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_history_branch_doctor  WHERE id = ' . $db->quote($id));
        $nv_Cache->delMod($module_name);
        nv_insert_logs(NV_LANG_DATA, $module_name, 'Delete History_branch_doctor', 'ID: ' . $id, $admin_info['userid']);
        nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
    }
}

$row = array();
$error = array();
$row['id'] = $nv_Request->get_int('id', 'post,get', 0);


$array_userid_doctor_users = array();
$_sql = 'SELECT t1.* FROM ' . $db_config['prefix'] . '_users t1 RIGHT JOIN ' . $db_config['prefix'] . '_users_groups_users t2 ON t1.userid = t2.userid LEFT JOIN ' . TABLE_APPOINTMENT_NAME . '_branch_users t3 ON t1.userid = t3.userid WHERE t2.group_id=10';
$_query = $db->query($_sql);
while ($_row = $_query->fetch()) {
    $array_userid_doctor_users[$_row['userid']] = $_row;
}

$array_id_branch_booking = array();
$_sql = 'SELECT branch_id,title FROM ' . NV_PREFIXLANG . '_booking_branch';
$_query = $db->query($_sql);
while ($_row = $_query->fetch()) {
    $array_id_branch_booking[$_row['branch_id']] = $_row;
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
	$where .= ' AND id_branch='. $search['brand'] ;
	$base_url .= '&brand='. $search['brand'];
}

if($search['doctor'])
{
	$where .= ' AND userid_doctor='. $search['doctor'] ;
	$base_url .= '&doctor='. $search['doctor'];
}

if($search['date_from'])
{
	$where .= ' AND date_change >='. $search['date_from'] ;
	$base_url .= '&date_from='. $date_from;
}

if($search['date_to'])
{
	$where .= ' AND date_change <='. $search['date_to'] ;
	$base_url .= '&date_to='. $date_to;
}


// Fetch Limit
$show_view = false;
if (!$nv_Request->isset_request('id', 'post,get')) {
    $show_view = true;
    $per_page = 20;
    $page = $nv_Request->get_int('page', 'post,get', 1);
    $db->sqlreset()
        ->select('COUNT(*)')
        ->from('' . NV_PREFIXLANG . '_' . $module_data . '_history_branch_doctor')
		->where('1=1' . $where);

    $sth = $db->prepare($db->sql());

   
    $sth->execute();
    $num_items = $sth->fetchColumn();

    $db->select('*')
        ->order('date_change DESC')
        ->limit($per_page)
        ->offset(($page - 1) * $per_page);
    $sth = $db->prepare($db->sql());

    $sth->execute();
}

$xtpl = new XTemplate( $op . '.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
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
$xtpl->assign('add', $base_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=history_branch_doctor_add');



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



foreach ($array_userid_doctor_users as $value) {
    $xtpl->assign('OPTION', array(
        'key' => $value['userid'],
        'title' => $value['last_name'] . ' ' . $value['first_name'],
        'selected' => ($value['userid'] == $search['doctor']) ? ' selected="selected"' : ''
    ));
    $xtpl->parse('main.view.select_userid_doctor');
}
foreach ($array_id_branch_booking as $value) {
    $xtpl->assign('OPTION', array(
        'key' => $value['branch_id'],
        'title' => $value['title'],
        'selected' => ($value['branch_id'] == $search['brand']) ? ' selected="selected"' : ''
    ));
    $xtpl->parse('main.view.select_id_branch');
}


if ($show_view) {
    $base_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op;
    if (!empty($q)) {
        $base_url .= '&q=' . $q;
    }
    $generate_page = nv_generate_page($base_url, $num_items, $per_page, $page);
    if (!empty($generate_page)) {
        $xtpl->assign('NV_GENERATE_PAGE', $generate_page);
        $xtpl->parse('main.view.generate_page');
    }
    $number = $page > 1 ? ($per_page * ($page - 1)) + 1 : 1;
    while ($view = $sth->fetch()) {
        $view['number'] = $number++;
		
		$view['date_change_tam'] = $view['date_change'];
		
        $view['date_change'] = (empty($view['date_change'])) ? '' : nv_date('d/m/Y', $view['date_change']);
        $view['userid_doctor'] = $array_userid_doctor_users[$view['userid_doctor']]['last_name'] . ' ' . $array_userid_doctor_users[$view['userid_doctor']]['first_name'];
        $view['id_branch'] = $array_id_branch_booking[$view['id_branch']]['title'];
        $view['link_edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=history_branch_doctor_add&amp;id=' . $view['id'];
        $view['link_delete'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;delete_id=' . $view['id'] . '&amp;delete_checkss=' . md5($view['id'] . NV_CACHE_PREFIX . $client_info['session_id']);
        $xtpl->assign('VIEW', $view);
		
		if($view['active'])
		{
			$xtpl->assign('checked', 'checked=checked');
		}
		else
		{
			$xtpl->assign('checked', '');
		}
		
		if($view['date_change_tam'] > NV_CURRENTTIME)
		{
			$xtpl->parse('main.view.loop.edit');
		}
		
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

$page_title = $lang_module['history_branch_doctor'];

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
