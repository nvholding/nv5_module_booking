<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2013 Webdep24.com. All rights reserved
 * @Blog  http://dangdinhtu.com
 * @License GNU/GPL version 2 or any later version
 * @Createdate  Wed, 21 Jan 2015 14:00:59 GMT
 */
 
if( ! defined( 'NV_ADMIN' ) ) die( 'Stop!!!' );

$allow_func = array(
	'main',
	'service',
	'service_package',
	'buy_service',
	'appointment',
	'history_sms',
	'doctors',
	'branch',
	'patient',
	'patient_edit',
	'history_patient',
	'history_branch_doctor',
	'history_branch_doctor_add',
	'export',
	'group_user',
	'get_user',
	'setting',
	'statics',
	'salary',
	'grouppatient' );
 
$submenu['appointment'] = $lang_module['appointment'];
$submenu['patient'] = $lang_module['patient'];
$submenu['patient_edit'] = $lang_module['patient_update_new'];
$submenu['grouppatient'] = $lang_module['grouppatient'];
$submenu['doctors'] = $lang_module['doctors'];
$submenu['service'] = $lang_module['service'];
$submenu['service_package'] = $lang_module['service_package'];
$submenu['branch'] = $lang_module['branch'];
$submenu['salary'] = 'Lịch sử Thầy';
$submenu['statics'] = $lang_module['statics'];
$submenu['history_branch_doctor'] = $lang_module['history_branch_doctor'];
$submenu['history_patient'] = $lang_module['history_patient'];
$submenu['history_sms'] = $lang_module['history_sms'];
$submenu['setting'] = $lang_module['setting'];
 
