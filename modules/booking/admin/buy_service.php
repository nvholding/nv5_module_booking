<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2021 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Tue, 23 Mar 2021 03:28:11 GMT
 */

if (!defined('NV_IS_FILE_ADMIN'))
    die('Stop!!!');

if ($nv_Request->isset_request('delete_id', 'get') and $nv_Request->isset_request('delete_checkss', 'get')) {
    $id = $nv_Request->get_int('delete_id', 'get');
    $delete_checkss = $nv_Request->get_string('delete_checkss', 'get');
    if ($id > 0 and $delete_checkss == md5($id . NV_CACHE_PREFIX . $client_info['session_id'])) {
		
		// cập nhật thông tin số lần khám còn lại cho KH mua gói dịch vụ
		// lấy số lần khám cũ trước đó
		$num_old = get_num_byservice($id);
		$userid = get_userid_byservice($id);
				
		$so_lan_update = 0 - $num_old;
		update_num_kham($userid, $so_lan_update);
				
        $db->query('DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_buy_service  WHERE id = ' . $db->quote($id));
        $nv_Cache->delMod($module_name);
        nv_insert_logs(NV_LANG_DATA, $module_name, 'Delete Buy_service', 'ID: ' . $id, $admin_info['userid']);
        nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
    }
}

$row = array();
$error = array();

$row['id'] = $nv_Request->get_int('id', 'post,get', 0);

if ($nv_Request->isset_request('submit', 'post')) {
    $row['service_id'] = $nv_Request->get_int('service_id', 'post', 0);
	
    $row['userid'] = $nv_Request->get_int('userid', 'post', 0);
	
	$row['date_added'] = nv_substr( $nv_Request->get_title( 'date_added', 'post', '', '' ), 0, 10 );
	
	if( preg_match( '/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $row['date_added'], $m ) )
	{
		$row['date_added'] = mktime( 12, 59 , 59 , $m[2], $m[1], $m[3] );
	}
	else
	{
		$row['date_added'] = NV_CURRENTTIME;
	}

    if (empty($row['service_id'])) {
        $error[] = $lang_module['error_required_service_id'];
    } elseif (empty($row['userid'])) {
        $error[] = $lang_module['error_required_userid'];
    }

    if (empty($error)) {
        try {
			
			$row['num'] = $db->query('SELECT number FROM ' . NV_PREFIXLANG . '_' . $module_data . '_service_package WHERE service_package_id = ' . $row['service_id'])->fetchColumn();
			
            if (empty($row['id'])) {
				
                $row['userid_add'] = $admin_info['userid'];
                
                $stmt = $db->prepare('INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_buy_service (service_id, userid, userid_add, num, date_added) VALUES (:service_id, :userid, :userid_add, :num, :date_added)');

                $stmt->bindParam(':userid_add', $row['userid_add'], PDO::PARAM_INT);
                
                $stmt->bindParam(':date_added', $row['date_added'], PDO::PARAM_INT);
				
				// cập nhật thông tin số lần khám còn lại cho KH mua gói dịch vụ
				
				$so_lan_update = $row['num'];
				update_num_kham($row['userid'], $so_lan_update);
				

            } else {
                $stmt = $db->prepare('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_buy_service SET service_id = :service_id, userid = :userid, num = :num, date_added =:date_added WHERE id=' . $row['id']);
				
				// cập nhật thông tin số lần khám còn lại cho KH mua gói dịch vụ
				// lấy số lần khám cũ trước đó
				$num_old = get_num_byservice($row['id']);
				
				$so_lan_update = $row['num'] - $num_old;
				update_num_kham($row['userid'], $so_lan_update);
            }
            $stmt->bindParam(':service_id', $row['service_id'], PDO::PARAM_INT);
            $stmt->bindParam(':userid', $row['userid'], PDO::PARAM_INT);
			$stmt->bindParam(':num', $row['num'], PDO::PARAM_INT);
			$stmt->bindParam(':date_added', $row['date_added'], PDO::PARAM_INT);

            $exc = $stmt->execute();
            if ($exc) {
                $nv_Cache->delMod($module_name);
                if (empty($row['id'])) {
                    nv_insert_logs(NV_LANG_DATA, $module_name, 'Add Buy_service', ' ', $admin_info['userid']);
                } else {
                    nv_insert_logs(NV_LANG_DATA, $module_name, 'Edit Buy_service', 'ID: ' . $row['id'], $admin_info['userid']);
                }
                nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op .'&userid='. $row['userid']);
            }
        } catch(PDOException $e) {
            trigger_error($e->getMessage());
            die($e->getMessage()); //Remove this line after checks finished
        }
    }
} elseif ($row['id'] > 0) {
    $row = $db->query('SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_buy_service WHERE id=' . $row['id'])->fetch();
    if (empty($row)) {
        nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
    }
} else {
    $row['id'] = 0;
    $row['service_id'] = 0;
    $row['userid'] = $nv_Request->get_int('userid', 'post,get', 0);
}
$array_service_id_booking = array();
$_sql = 'SELECT service_package_id,title FROM ' . NV_PREFIXLANG . '_' . $module_data . '_service_package WHERE status = 1 ORDER BY weight ASC';
$_query = $db->query($_sql);
while ($_row = $_query->fetch()) {
    $array_service_id_booking[$_row['service_package_id']] = $_row;
}

$array_userid_booking = array();
$_sql = 'SELECT userid,full_name,phone FROM ' . NV_PREFIXLANG . '_' . $module_data . '_patient';
$_query = $db->query($_sql);
while ($_row = $_query->fetch()) {
    $array_userid_booking[$_row['userid']] = $_row;
}

$where = '';

$q = $nv_Request->get_title('q', 'post,get');

if($row['userid'])
{
	$where .= ' AND userid ='. $row['userid'] ;
}


// Fetch Limit
$show_view = false;
if (!$nv_Request->isset_request('id', 'post,get')) {
    $show_view = true;
    $per_page = 20;
    $page = $nv_Request->get_int('page', 'post,get', 1);
    $db->sqlreset()
        ->select('COUNT(*)')
        ->from('' . NV_PREFIXLANG . '_' . $module_data . '_buy_service')
		->where('1=1 '. $where);

   
    $sth = $db->prepare($db->sql());

   
    $sth->execute();
    $num_items = $sth->fetchColumn();

    $db->select('*')
        ->order('date_added ASC')
        ->limit($per_page)
        ->offset(($page - 1) * $per_page);
    $sth = $db->prepare($db->sql());

   
    $sth->execute();
}

$xtpl = new XTemplate('buy_service.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
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
if( !empty ($row['date_added'])){
	$row['date_added'] = nv_date('d/m/Y', $row['date_added']);
}
$xtpl->assign('ROW', $row);

foreach ($array_service_id_booking as $value) {
    $xtpl->assign('OPTION', array(
        'key' => $value['service_package_id'],
        'title' => $value['title'],
        'selected' => ($value['service_package_id'] == $row['service_id']) ? ' selected="selected"' : ''
    ));
    $xtpl->parse('main.select_service_id');
}
foreach ($array_userid_booking as $value) {
    $xtpl->assign('OPTION', array(
        'key' => $value['userid'],
        'title' => $value['full_name'] . '-' . $value['phone'],
        'selected' => ($value['userid'] == $row['userid']) ? ' selected="selected"' : ''
    ));
    $xtpl->parse('main.select_userid');
}
$xtpl->assign('Q', $q);

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
        $view['service_id'] = $array_service_id_booking[$view['service_id']]['title'];
        $view['userid'] = $array_userid_booking[$view['userid']]['full_name'];
        $view['link_edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;id=' . $view['id'];
        $view['link_delete'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;delete_id=' . $view['id'] . '&amp;delete_checkss=' . md5($view['id'] . NV_CACHE_PREFIX . $client_info['session_id']);
		$view['date_added'] = date('d/m/Y', $view['date_added'] );
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

$page_title = $lang_module['buy_service'];

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
