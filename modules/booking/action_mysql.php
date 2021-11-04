<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2019 DANG DINH TU. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Wed, 04 Sep 2019 17:00:00 GMT
 */

if( ! defined( 'NV_IS_FILE_MODULES' ) ) die( 'Stop!!!' );

$sql_drop_module = array();

$sql_create_module[] = "INSERT INTO  " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_appointment";
$sql_create_module[] = "INSERT INTO  " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_history_send_sms";
$sql_create_module[] = "INSERT INTO  " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_calendar";
$sql_create_module[] = "INSERT INTO  " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_branch";
$sql_create_module[] = "INSERT INTO  " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_branch_users";
$sql_create_module[] = "INSERT INTO  " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_patient";
$sql_create_module[] = "INSERT INTO  " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_patient_appointment";
$sql_create_module[] = "INSERT INTO  " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_service";
$sql_create_module[] = "INSERT INTO  " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_buy_service";
$sql_create_module[] = "INSERT INTO  " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_history_branch_doctor";
$sql_create_module[] = "INSERT INTO  " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_setting";

$sql_create_module = $sql_drop_module;


$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_appointment (
	appointment_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	customer_full_name varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
	customer_email varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
	customer_phone varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
	customer_message text COLLATE utf8mb4_unicode_ci NOT NULL,
	customer_date_booking int(11) unsigned NOT NULL DEFAULT 0,
	service_id varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
	branch_id int(11) unsigned NOT NULL DEFAULT 0,
	doctors_id int(11) unsigned NOT NULL DEFAULT 0,
	userid int(11) unsigned NOT NULL DEFAULT 0,
	sms_result text COLLATE utf8mb4_unicode_ci NOT NULL,
	is_send_sms tinyint(1) unsigned NOT NULL DEFAULT 0,
	is_send_email tinyint(1) unsigned NOT NULL DEFAULT 0,
	bs_dakham tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Bác sĩ chưa khám 0, đã khám 1',
	date_added int(11) unsigned NOT NULL DEFAULT 0,
	date_modified int(11) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (appointment_id)
) ENGINE=MyISAM";



$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_history_send_sms (
	id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	appointment_id mediumint(8) unsigned NOT NULL,
	name_send varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
	phone_send varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
	status_sms int(11) unsigned NOT NULL DEFAULT 0,
	date_order int(11) unsigned NOT NULL DEFAULT 0,
	date_added int(11) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (id)
) ENGINE=MyISAM";



$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_branch (
	branch_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	title varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
	email varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
	phone varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
	address varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
	weight smallint(5) NOT NULL DEFAULT 0,
	status tinyint(1) unsigned NOT NULL DEFAULT 0,
	date_added int(11) unsigned NOT NULL DEFAULT 0,
	date_modified int(11) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (branch_id)
) ENGINE=MyISAM";


$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_branch_users (
	branch_users_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	userid int(11) NOT NULL DEFAULT 0,
	branch_id int(11) NOT NULL DEFAULT 0,
	time_from int(100) NOT NULL DEFAULT 0 COMMENT 'Thời gian hợp đồng từ',
	time_to int(100) NOT NULL DEFAULT 0 COMMENT 'Thời gian hợp đồng đến',
	PRIMARY KEY (branch_users_id),
	UNIQUE KEY userid_2 (userid),
	KEY branch_id (branch_id),
	KEY userid (userid)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_calendar (
	calendar_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	date_start int(11) unsigned NOT NULL DEFAULT 0,
	userid int(11) unsigned NOT NULL DEFAULT 0,
	shift tinyint(1) unsigned NOT NULL DEFAULT 0,
	date_added int(11) unsigned NOT NULL DEFAULT 0,
	date_modified int(11) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (calendar_id)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_patient (
	patient_id int(11) unsigned NOT NULL AUTO_INCREMENT,
	patient_code varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT ,
	userid int(11) unsigned NOT NULL DEFAULT 0,
	doctors_id int(11) unsigned NOT NULL DEFAULT 0,
	price varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
	blood_pressure varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
	patient_result text COLLATE utf8mb4_unicode_ci NOT NULL,
	typemedicine text COLLATE utf8mb4_unicode_ci NOT NULL,
	status tinyint(1) unsigned NOT NULL DEFAULT 0,
	date_added int(11) unsigned NOT NULL DEFAULT 0,
	date_modified int(11) unsigned NOT NULL DEFAULT 0,
	kham_conlai int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'Số lần khám còn lại',
	PRIMARY KEY (patient_id)
) ENGINE=MyISAM";


$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_patient_appointment(
	id int(11) unsigned NOT NULL AUTO_INCREMENT,
	patient_id int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'userid khách hàng',
	appointment_id int(11) unsigned NOT NULL DEFAULT 0,
	doctors_id int(11) unsigned NOT NULL DEFAULT 0,
	price varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
	blood_pressure varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
	patient_result text COLLATE utf8mb4_unicode_ci NOT NULL,
	typemedicine text COLLATE utf8mb4_unicode_ci NOT NULL,
	date_added int(11) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (id)
) ENGINE=MyISAM";



$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_service (
	service_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	service_name varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
	image varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
	weight smallint(5) NOT NULL DEFAULT 0,
	status tinyint(1) NOT NULL DEFAULT 0,
	date_added int(11) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (service_id),
	UNIQUE KEY service_name (service_name)
) ENGINE=MyISAM";




$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_history_branch_doctor (
	id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	userid_doctor mediumint(8) NOT NULL DEFAULT 0 COMMENT 'Thầy',
	id_branch int(11) NOT NULL DEFAULT 0 COMMENT 'Chi nhánh',
	date_change int(11) NOT NULL DEFAULT 0 COMMENT 'Ngày luân chuyển',
	date_added int(11) unsigned NOT NULL DEFAULT 0,
	active int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'Kích hoạt',
	PRIMARY KEY (id)
) ENGINE=MyISAM";



$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_buy_service (
	id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	service_id mediumint(8) NOT NULL DEFAULT 0 COMMENT 'Gói dịch vụ',
	userid int(11) NOT NULL DEFAULT 0 COMMENT 'Tài khoản khách hàng mua gói',
	userid_add int(11) NOT NULL DEFAULT 0 COMMENT 'Tài khoản đăng ký mua gói',
	num int(11) NOT NULL DEFAULT 0 COMMENT 'Số lần khám',
	date_added int(11) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (id)
) ENGINE=MyISAM";



$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_setting (
	config_name varchar(50) NOT NULL DEFAULT '',
	config_value text NOT NULL,
	UNIQUE KEY config_name (config_name)
) ENGINE=MyISAM";
 
$sql_create_module[] = "INSERT INTO  " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_setting (config_name, config_value) VALUES
('location', ''),
('booking_time', '08-17'),
('space_time', '30'),
('activesms', '1'),
('timesmsbegin', '1'),
('activeemail', '1'),
('timeemailbegin', '1'),
('emailhourstart', '9'),
('numberemail', '1'),
('infosms', 'Kính Mời Anh/Chị &#91;HOTEN&#93;, Tới khám &#91;SERVICE&#93; tại AnvH Lúc &#91;TIME&#93; ngày &#91;DATE&#93;'),
('infoemail', 'Kính Mời Anh/Chị &#91;HOTEN&#93;, Tới khám &#91;SERVICE&#93; tại AnvH Lúc &#91;TIME&#93; ngày &#91;DATE&#93;'),
('apikey', ''),
('secretkey', ''),
('brandname', ''),
('email', ''),
('emailminutestart', '17'),
('smshourstart', '7'),
('default_group_doctors', '10'),
('smsminutestart', '0');";

try
{
	$db->query("ALTER TABLE ". NV_USERS_GLOBALTABLE ." ADD address TEXT NOT NULL DEFAULT '' AFTER email_verification_time;");
}
catch ( PDOException $e )
{
	 
}

try
{
	$db->query("ALTER TABLE ". NV_USERS_GLOBALTABLE ." ADD medical_history TEXT NOT NULL DEFAULT '' AFTER address;";);
}
catch ( PDOException $e )
{
	 
}

try
{
	$db->query("ALTER TABLE ". NV_USERS_GLOBALTABLE ." ADD aspirations_treatment TEXT NOT NULL DEFAULT '' AFTER medical_history;");
}
catch ( PDOException $e )
{
	 
}

try
{
	$db->query("ALTER TABLE ". NV_USERS_GLOBALTABLE ." ADD y_benh_tdcs TEXT NOT NULL DEFAULT '' AFTER aspirations_treatment;");
}
catch ( PDOException $e )
{
	 
}


 
