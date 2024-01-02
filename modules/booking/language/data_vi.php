<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2024 VINADES.,JSC. All rights reserved
 * @License: Not free read more http://nukeviet.vn/vi/store/modules/nvtools/
 * @Createdate Tue, 02 Jan 2024 06:23:56 GMT
 */

if (!defined('NV_ADMIN'))
    die('Stop!!!');

try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_appointment (appointment_id, customer_full_name, customer_email, customer_phone, customer_message, customer_date_booking, service_id, branch_id, doctors_id, userid, sms_result, is_send_sms, is_send_email, bs_dakham, date_added, date_modified) VALUES('34', 'Nguyễn Thanh Hoàng', 'adminwmt@gmail.com', '0988455066', '', '1704198000', '1', '1', '0', '6', '', '0', '0', '0', '1704175930', '0')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}
try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_appointment (appointment_id, customer_full_name, customer_email, customer_phone, customer_message, customer_date_booking, service_id, branch_id, doctors_id, userid, sms_result, is_send_sms, is_send_email, bs_dakham, date_added, date_modified) VALUES('33', 'Nguyễn Thanh Hoàng', 'honguyentapdoan8@gmail.com', '0988455068', '', '1704198000', '1', '1', '0', '14', '', '0', '0', '0', '1704175717', '0')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}
try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_appointment (appointment_id, customer_full_name, customer_email, customer_phone, customer_message, customer_date_booking, service_id, branch_id, doctors_id, userid, sms_result, is_send_sms, is_send_email, bs_dakham, date_added, date_modified) VALUES('31', 'bk', '0923998879@gmail.com', '0923998879', '', '1704171600', '1', '1', '0', '7', '', '0', '0', '0', '1704164383', '0')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}

try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_branch (branch_id, title, email, phone, address, weight, status, date_added, date_modified) VALUES('1', 'Trụ sở chính', 'thienphatvltl@gmail.com', '0923889979', '2/14 Tăng Bạt Hổ, Phường 11', '1', '1', '1703889654', '0')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}

try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_branch_users (branch_users_id, userid, branch_id, time_from, time_to) VALUES('5', '5', '1', '1546102801', '1798563601')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}
try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_branch_users (branch_users_id, userid, branch_id, time_from, time_to) VALUES('6', '12', '1', '1704042001', '1864486801')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}
try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_branch_users (branch_users_id, userid, branch_id, time_from, time_to) VALUES('7', '13', '1', '1704042001', '1864486801')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}

try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_group_patient (id, title, time_add, time_edit, logo, description, status, weight) VALUES('3', 'VIP', '0', '1612752162', '', 'Khách hàng VIP', '1', '1')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}
try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_group_patient (id, title, time_add, time_edit, logo, description, status, weight) VALUES('4', 'Khách hàng', '1610438320', '1704168607', '', 'Đang điều trị', '1', '2')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}
try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_group_patient (id, title, time_add, time_edit, logo, description, status, weight) VALUES('6', 'Tiềm năng', '1610789302', '1612752227', '', 'Đã được tư vấn, đã trải nghiệm, có vấn đề sức khỏe', '1', '3')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}
try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_group_patient (id, title, time_add, time_edit, logo, description, status, weight) VALUES('7', 'Người nhà KH', '1611818776', '1612752253', '', 'Đưa khách hàng đến trị liệu', '1', '4')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}
try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_group_patient (id, title, time_add, time_edit, logo, description, status, weight) VALUES('8', 'Data', '1611818800', '1612752333', '', 'Chưa sử dụng dịch vụ / chưa được tư vấn / chưa biết', '1', '5')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}
try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_group_patient (id, title, time_add, time_edit, logo, description, status, weight) VALUES('9', 'Blacklist', '1634612336', '', '', 'Không phục vụ', '1', '6')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}
try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_group_patient (id, title, time_add, time_edit, logo, description, status, weight) VALUES('10', 'Foreign', '1634612601', '', '', 'Khách nước ngoài', '1', '7')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}

try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_patient (patient_id, userid, doctors_id, price, blood_pressure, patient_result, typemedicine, status, date_added, date_modified, patient_group, expect, history, work, confess, other_contact, full_name, service_package_id, phone, gender, birthday, address, note, mode, patient_code, kham_conlai, branch) VALUES('7', '14', '0', '', '', '', '', '0', '0', '0', '6', '', '', '', 'Anh', '', 'Nguyễn Thanh Hoàng', '0', '0988455068', 'M', '560624401', '12/3D Đường 06, P.Linh Xuân, Q.Thủ Đức', '', '0', '1', '0', '1')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}

try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_service (service_id, service_name, image, weight, status, date_added) VALUES('1', 'Chăm sóc sức khỏe', '', '1', '1', '1703911169')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}
try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_service (service_id, service_name, image, weight, status, date_added) VALUES('2', 'Cổ vai gáy tiền đình', '', '2', '1', '1703925356')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}
try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_service (service_id, service_name, image, weight, status, date_added) VALUES('3', 'Trị liệu đau mỏi lưng', '', '3', '1', '1703925412')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}
try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_service (service_id, service_name, image, weight, status, date_added) VALUES('4', 'Mắt đại bàng', '', '4', '1', '1703925478')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}
try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_service (service_id, service_name, image, weight, status, date_added) VALUES('5', 'Ngăn ngừa tai biến', '', '5', '1', '1703925491')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}
try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_service (service_id, service_name, image, weight, status, date_added) VALUES('6', 'Detox thải độc cơ thể', '', '6', '1', '1703925526')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}

try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_service_package (service_package_id, title, number, price, weight, status, date_added) VALUES('1', 'Liệu trình xông hơi', '1', '250000', '1', '1', '1609261847')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}
try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_service_package (service_package_id, title, number, price, weight, status, date_added) VALUES('2', 'Combo Massage Trị liệu vùng chấn thương giảm 5%', '5', '1200000', '4', '1', '1609261948')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}
try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_service_package (service_package_id, title, number, price, weight, status, date_added) VALUES('3', 'Combo Massage Trị liệu vùng chấn thương giảm 15%', '10', '2300000', '5', '1', '1609261961')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}
try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_service_package (service_package_id, title, number, price, weight, status, date_added) VALUES('4', 'Massage Trị liệu vùng chấn thương', '1', '250000', '3', '1', '1614000458')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}
try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_service_package (service_package_id, title, number, price, weight, status, date_added) VALUES('5', 'Liệu trình tắm thảo dược', '1', '180000', '2', '1', '1623313701')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}
try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_service_package (service_package_id, title, number, price, weight, status, date_added) VALUES('6', 'Massage trị liệu đầu vai gáy', '1', '280000', '6', '1', '1704169898')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}

try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_setting (config_name, config_value) VALUES('location', 'Thông tin liên hệ:<br />THIỆN PHÁT<br />Địa chỉ: 2/14 Tăng Bạt Hổ, P11, Q.Bình Thạnh, Tp Hồ Chí Minh<br />Chi nhánh Tân Bình: 277 Hoàng Văn Thụ, Phường 2, Tân Bình, Thành phố Hồ Chí Minh<br />Chi nhánh Quận 9: 112/36 Tây Hòa, Phước Long A, Quận 9, Thành phố Hồ Chí Minh<br />Điện thoại: 0923998879<br /><br />Email: thienphat.sg@gmail.com')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}
try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_setting (config_name, config_value) VALUES('booking_time', '08-20')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}
try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_setting (config_name, config_value) VALUES('space_time', '20')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}
try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_setting (config_name, config_value) VALUES('activesms', '1')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}
try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_setting (config_name, config_value) VALUES('timesmsbegin', '1')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}
try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_setting (config_name, config_value) VALUES('activeemail', '1')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}
try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_setting (config_name, config_value) VALUES('timeemailbegin', '1')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}
try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_setting (config_name, config_value) VALUES('emailhourstart', '9')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}
try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_setting (config_name, config_value) VALUES('numberemail', '1')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}
try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_setting (config_name, config_value) VALUES('infosms', 'Kính Mời Anh/Chị &#91;HOTEN&#93;, Tới khám &#91;SERVICE&#93; tại AnvH Lúc &#91;TIME&#93; ngày &#91;DATE&#93;')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}
try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_setting (config_name, config_value) VALUES('infoemail', 'Kính Mời Anh/Chị &#91;HOTEN&#93;, Tới khám &#91;SERVICE&#93; tại Thiên Phát Lúc &#91;TIME&#93; ngày &#91;DATE&#93;')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}
try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_setting (config_name, config_value) VALUES('apikey', '')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}
try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_setting (config_name, config_value) VALUES('secretkey', '')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}
try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_setting (config_name, config_value) VALUES('brandname', '')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}
try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_setting (config_name, config_value) VALUES('email', 'thienphat.sg@gmail.com')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}
try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_setting (config_name, config_value) VALUES('emailminutestart', '17')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}
try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_setting (config_name, config_value) VALUES('smshourstart', '7')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}
try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_setting (config_name, config_value) VALUES('default_group_doctors', '10')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}
try {
    $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_setting (config_name, config_value) VALUES('smsminutestart', '0')");
} catch (PDOException $e) {
    trigger_error($e->getMessage());
}
