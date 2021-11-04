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

$row = array();
$error = array();
$row['id'] = $nv_Request->get_int('id', 'post,get', 0);
if ($nv_Request->isset_request('submit', 'post')) {
    $row['userid_doctor'] = $nv_Request->get_int('userid_doctor', 'post', 0);
    $row['id_branch'] = $nv_Request->get_int('id_branch', 'post', 0);
    if (preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $nv_Request->get_string('date_change', 'post'), $m))     {
        $_hour = 0;
        $_min = 0;
        $row['date_change'] = mktime($_hour, $_min, 0, $m[2], $m[1], $m[3]);
    }
    else
    {
        $row['date_change'] = 0;
    }

    if (empty($row['userid_doctor'])) {
        $error[] = $lang_module['error_required_userid_doctor'];
    } elseif (empty($row['id_branch'])) {
        $error[] = $lang_module['error_required_id_branch'];
    } elseif (empty($row['date_change'])) {
        $error[] = $lang_module['error_required_date_change'];
    }

    if (empty($error)) {
        try {
            if (empty($row['id'])) {
                $row['date_added'] = 0;

                $stmt = $db->prepare('INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_history_branch_doctor (userid_doctor, id_branch, date_change, date_added) VALUES (:userid_doctor, :id_branch, :date_change, :date_added)');

                $stmt->bindParam(':date_added', $row['date_added'], PDO::PARAM_INT);

            } else {
                $stmt = $db->prepare('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_history_branch_doctor SET userid_doctor = :userid_doctor, id_branch = :id_branch, date_change = :date_change WHERE id=' . $row['id']);
            }
            $stmt->bindParam(':userid_doctor', $row['userid_doctor'], PDO::PARAM_INT);
            $stmt->bindParam(':id_branch', $row['id_branch'], PDO::PARAM_INT);
            $stmt->bindParam(':date_change', $row['date_change'], PDO::PARAM_INT);

            $exc = $stmt->execute();
            if ($exc) {
                $nv_Cache->delMod($module_name);
				
				// kiểm tra có phải là ngày hiện tại không
				$ngayhientai = date('d/m/Y', NV_CURRENTTIME);
				$ngaychuyen = date('d/m/Y', $row['date_change']);
				
				if($ngayhientai == $ngaychuyen)
				{
					// xử lý cập nhật thông tin tại đây
					$id_luanchuyen = 0;
					
					if($row['id'])
					{
						$id_luanchuyen = $row['id'];
					}
					else
					{
						// lấy id mới insert vào
						$id_luanchuyen = $db->query('SELECT max(id) as max FROM ' . NV_PREFIXLANG . '_' . $module_data . '_history_branch_doctor')->fetchColumn();
					}
					
					// cập nhật chi nhánh bác sĩ
					update_brand_doctor($id_luanchuyen);
				}
				
                if (empty($row['id'])) {
                    nv_insert_logs(NV_LANG_DATA, $module_name, 'Add History_branch_doctor', ' ', $admin_info['userid']);
                } else {
                    nv_insert_logs(NV_LANG_DATA, $module_name, 'Edit History_branch_doctor', 'ID: ' . $row['id'], $admin_info['userid']);
                }
                nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=history_branch_doctor');
            }
        } catch(PDOException $e) {
            trigger_error($e->getMessage());
            die($e->getMessage()); //Remove this line after checks finished
        }
    }
} elseif ($row['id'] > 0) {
    $row = $db->query('SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_history_branch_doctor WHERE id=' . $row['id'])->fetch();
    if (empty($row)) {
        nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=history_branch_doctor');
    }
} else {
    $row['id'] = 0;
    $row['userid_doctor'] = 0;
    $row['id_branch'] = 0;
    $row['date_change'] = 0;
}

if (empty($row['date_change'])) {
    $row['date_change'] = '';
}
else
{
    $row['date_change'] = date('d/m/Y', $row['date_change']);
}
$array_userid_doctor_users = array();
$_sql = 'SELECT t1.* FROM ' . $db_config['prefix'] . '_users t1 RIGHT JOIN ' . $db_config['prefix'] . '_users_groups_users t2 ON t1.userid = t2.userid LEFT JOIN ' . TABLE_APPOINTMENT_NAME . '_branch_users t3 ON t1.userid = t3.userid WHERE t2.group_id=10';
$_query = $db->query($_sql);
while ($_row = $_query->fetch()) {
    $array_userid_doctor_users[$_row['userid']] = $_row;
}

$array_id_branch_booking = array();
$_sql = 'SELECT branch_id,title FROM vidoco_vi_booking_branch';
$_query = $db->query($_sql);
while ($_row = $_query->fetch()) {
    $array_id_branch_booking[$_row['branch_id']] = $_row;
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

foreach ($array_userid_doctor_users as $value) {
    $xtpl->assign('OPTION', array(
        'key' => $value['userid'],
        'title' => $value['last_name'] . ' ' . $value['first_name'],
        'selected' => ($value['userid'] == $row['userid_doctor']) ? ' selected="selected"' : ''
    ));
    $xtpl->parse('main.select_userid_doctor');
}
foreach ($array_id_branch_booking as $value) {
    $xtpl->assign('OPTION', array(
        'key' => $value['branch_id'],
        'title' => $value['title'],
        'selected' => ($value['branch_id'] == $row['id_branch']) ? ' selected="selected"' : ''
    ));
    $xtpl->parse('main.select_id_branch');
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
