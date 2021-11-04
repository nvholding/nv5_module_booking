<?php

/**  
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */

if( ! defined( 'NV_SYSTEM' ) ) die( 'Stop!!!' );

define( 'NV_IS_MOD_APPOINTMENT', true );
 
define( 'TABLE_APPOINTMENT_NAME', NV_PREFIXLANG . '_' . $module_data );

define( 'ACTION_METHOD', $nv_Request->get_string( 'action', 'get,post', '' ) );



require_once NV_ROOTDIR . '/modules/' . $module_file . '/global.functions.php';


