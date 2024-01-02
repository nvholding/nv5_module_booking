<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2024 VINADES.,JSC. All rights reserved
 * @License: Not free read more http://nukeviet.vn/vi/store/modules/nvtools/
 * @Createdate Tue, 02 Jan 2024 06:23:56 GMT
 */

if (!defined('NV_IS_FILE_MODULES'))
    die('Stop!!!');

$sql_drop_module = array();
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_appointment";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_branch";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_branch_users";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_buy_service";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_calendar";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_group_patient";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_history_branch_doctor";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_history_send_sms";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_patient";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_patient_appointment";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_patient_edit";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_service";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_service_package";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_setting";

$sql_create_module = $sql_drop_module;
$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_appointment(
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

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_branch(
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

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_branch_users(
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

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_buy_service(
  id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  service_id mediumint(8) NOT NULL DEFAULT 0 COMMENT 'Gói dịch vụ',
  userid int(11) NOT NULL DEFAULT 0 COMMENT 'Tài khoản khách hàng mua gói',
  userid_add int(11) NOT NULL DEFAULT 0 COMMENT 'Tài khoản đăng ký mua gói',
  num int(11) NOT NULL DEFAULT 0 COMMENT 'Số lần khám',
  date_added int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_calendar(
  calendar_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  date_start int(11) unsigned NOT NULL DEFAULT 0,
  userid int(11) unsigned NOT NULL DEFAULT 0,
  shift tinyint(1) unsigned NOT NULL DEFAULT 0,
  date_added int(11) unsigned NOT NULL DEFAULT 0,
  date_modified int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (calendar_id)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_group_patient(
  id int(11) NOT NULL AUTO_INCREMENT,
  title varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  time_add int(11) NOT NULL,
  time_edit int(11) DEFAULT NULL,
  logo varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  description varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  status int(11) NOT NULL,
  weight int(11) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_history_branch_doctor(
  id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  userid_doctor mediumint(8) NOT NULL DEFAULT 0 COMMENT 'Thầy',
  id_branch int(11) NOT NULL DEFAULT 0 COMMENT 'Chi nhánh',
  date_change int(11) NOT NULL DEFAULT 0 COMMENT 'Ngày luân chuyển',
  date_added int(11) unsigned NOT NULL DEFAULT 0,
  active int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'Kích hoạt',
  PRIMARY KEY (id)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_history_send_sms(
  id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  appointment_id mediumint(8) unsigned NOT NULL,
  name_send varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  phone_send varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  status_sms int(11) unsigned NOT NULL DEFAULT 0,
  date_order int(11) unsigned NOT NULL DEFAULT 0,
  date_added int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_patient(
  patient_id int(11) unsigned NOT NULL AUTO_INCREMENT,
  userid int(11) unsigned NOT NULL DEFAULT 0,
  doctors_id int(11) unsigned NOT NULL DEFAULT 0,
  price varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  blood_pressure varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  patient_result text COLLATE utf8mb4_unicode_ci NOT NULL,
  typemedicine text COLLATE utf8mb4_unicode_ci NOT NULL,
  status tinyint(1) unsigned NOT NULL DEFAULT 0,
  date_added int(11) unsigned NOT NULL DEFAULT 0,
  date_modified int(11) unsigned NOT NULL DEFAULT 0,
  patient_group int(11) NOT NULL,
  expect varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  history varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  work varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  confess varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  other_contact text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  full_name varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  service_package_id int(11) NOT NULL DEFAULT 0,
  phone text COLLATE utf8mb4_unicode_ci NOT NULL,
  gender varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  birthday int(11) DEFAULT NULL,
  address varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  note varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  mode int(11) NOT NULL DEFAULT 0,
  patient_code int(100) DEFAULT NULL,
  kham_conlai int(11) NOT NULL DEFAULT 0 COMMENT 'Số lần khám còn lại',
  branch int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (patient_id)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_patient_appointment(
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

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_patient_edit(
  id int(11) NOT NULL AUTO_INCREMENT,
  patient_id int(11) NOT NULL,
  userid int(11) NOT NULL DEFAULT 0,
  doctors_id int(11) NOT NULL DEFAULT 0,
  price varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  blood_pressure varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  patient_result text COLLATE utf8mb4_unicode_ci NOT NULL,
  status int(11) NOT NULL DEFAULT 0,
  date_added int(11) NOT NULL DEFAULT 0,
  date_modified int(11) NOT NULL DEFAULT 0,
  patient_group int(11) NOT NULL,
  expect varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  history varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  work varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  confess varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  other_contact text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  full_name varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  service_package_id int(11) NOT NULL DEFAULT 0,
  phone text COLLATE utf8mb4_unicode_ci NOT NULL,
  gender varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  birthday int(11) DEFAULT NULL,
  address varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  note varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  user_require int(11) NOT NULL,
  time_require int(11) NOT NULL,
  email varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  using_patient int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_service(
  service_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  service_name varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  image varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  weight smallint(5) NOT NULL DEFAULT 0,
  status tinyint(1) NOT NULL DEFAULT 0,
  date_added int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (service_id),
  UNIQUE KEY service_name (service_name)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_service_package(
  service_package_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  title varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  number int(10) NOT NULL DEFAULT 1,
  price varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  weight smallint(5) NOT NULL DEFAULT 0,
  status tinyint(1) NOT NULL DEFAULT 0,
  date_added int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (service_package_id),
  UNIQUE KEY service_name (title)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_setting(
  config_name varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  config_value text COLLATE utf8mb4_unicode_ci NOT NULL,
  UNIQUE KEY config_name (config_name)
) ENGINE=MyISAM";
