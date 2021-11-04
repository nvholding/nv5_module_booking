<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2019 DANGDINHTU. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Wed, 06 May 2020 10:30:00 GMT
 */

if ( ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );

$module_version = array(  
	'name' => 'BookingCare', 
	'modfuncs' => 'main,calendar,patient,history,appointment,buy_service,patientfollow,appointment-patient,statics', 
	'submenu' => 'calendar', 
	'change_alias' => '',
	'is_sysmod' => 0, 
	'virtual' => 1,  
	'version' => '4.4.02',  
	'date' => 'Thu, 26 Nov 2020 19:30:00 GMT',  
	'author' => 'DANGDINHTU (dlinhvan@gmail.com)',  
	'uploads_dir' => array(
		$module_name
	),  
	'note' => '' 
);