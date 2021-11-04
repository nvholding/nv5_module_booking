<?php

/**
 * @Project NUKEVIET 4.x
 * @Author ĐẶNG ĐÌNH TỨ (dlinhvan@gmail.com)
 * @Website https://nuke.vn - http://dangdinhtu.com
 * @Copyright (C) 2014 https://nuke.vn All rights reserved
 * @License GNU/GPL version 3 or any later version
 * @Createdate Fri, 30 Sep 2016 15:00:00 GMT
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

$page_title = $lang_module['setting'];
if( defined( 'NV_EDITOR' ) )
{
	require_once NV_ROOTDIR . '/' . NV_EDITORSDIR . '/' . NV_EDITOR . '/nv.php';
}
$currentpath = $module_upload . '/' . date( 'Y_m' );

if( file_exists( NV_UPLOADS_REAL_DIR . '/' . $currentpath ) )
{
	$upload_real_dir_page = NV_UPLOADS_REAL_DIR . '/' . $currentpath;
}
else
{
	$upload_real_dir_page = NV_UPLOADS_REAL_DIR . '/' . $module_upload;
	$e = explode( '/', $currentpath );
	if( ! empty( $e ) )
	{
		$cp = '';
		foreach( $e as $p )
		{
			if( ! empty( $p ) and ! is_dir( NV_UPLOADS_REAL_DIR . '/' . $cp . $p ) )
			{
				$mk = nv_mkdir( NV_UPLOADS_REAL_DIR . '/' . $cp, $p );
				if( $mk[0] > 0 )
				{
					$upload_real_dir_page = $mk[2];
					try
					{
						$db->query( "INSERT INTO " . NV_UPLOAD_GLOBALTABLE . "_dir (dirname, time) VALUES ('" . NV_UPLOADS_DIR . "/" . $cp . $p . "', 0)" );
					}
					catch ( PDOException $e )
					{
						trigger_error( $e->getMessage() );
					}
				}
			}
			elseif( ! empty( $p ) )
			{
				$upload_real_dir_page = NV_UPLOADS_REAL_DIR . '/' . $cp . $p;
			}
			$cp .= $p . '/';
		}
	}
	$upload_real_dir_page = str_replace( '\\', '/', $upload_real_dir_page );
}

$currentpath = str_replace( NV_ROOTDIR . '/', '', $upload_real_dir_page );
$moudulepath = NV_UPLOADS_DIR . '/' . $module_upload ;
$savesetting = $nv_Request->get_int( 'savesetting', 'post', 0 );
if( ! empty( $savesetting ) )
{

	$getSetting = array();
	$getSetting['default_group_doctors'] = $nv_Request->get_int( 'default_group_doctors', 'post', 0 );
	$getSetting['booking_time'] = $nv_Request->get_title( 'booking_time', 'post', '' );
	$getSetting['activesms'] = $nv_Request->get_int( 'activesms', 'post', 0 );
	$getSetting['clientno'] = $nv_Request->get_title( 'clientno', 'post', '' );
	$getSetting['space_time'] = $nv_Request->get_title( 'space_time', 'post', '' );
	
	$getSetting['clientpass'] = $nv_Request->get_title( 'clientpass', 'post', '' );
	$getSetting['timesmsbegin'] = $nv_Request->get_int( 'timesmsbegin', 'post', 0 );
	$getSetting['smshourstart'] = $nv_Request->get_int( 'smshourstart', 'post', 0 );
	$getSetting['smsminutestart'] = $nv_Request->get_int( 'smsminutestart', 'post', 0 );
	$getSetting['activeemail'] = $nv_Request->get_int( 'activeemail', 'post', 0 );
	$getSetting['timeemailbegin'] = $nv_Request->get_int( 'timeemailbegin', 'post', 0 );
	
	$getSetting['emailhourstart'] = $nv_Request->get_int( 'emailhourstart', 'post', 0 );
	$getSetting['emailminutestart'] = $nv_Request->get_int( 'emailminutestart', 'post', 0 );
	
	$getSetting['numberemail'] = $nv_Request->get_int( 'numberemail', 'post', 0 );
	$getSetting['infosms'] = $nv_Request->get_string( 'infosms', 'post', '' );
	$getSetting['infoemail'] = $nv_Request->get_editor( 'infoemail', '', NV_ALLOWED_HTML_TAGS );

	$getSetting['location'] = $nv_Request->get_textarea( 'location', '', 'br', 1 );
	$getSetting['apikey'] = $nv_Request->get_string( 'apikey', 'post', '' );
	$getSetting['secretkey'] = $nv_Request->get_string( 'secretkey', 'post', '' );
	$getSetting['brandname'] = $nv_Request->get_string( 'brandname', 'post', '' );
	$getSetting['email'] = $nv_Request->get_string( 'email', 'post', '' );
	$getSetting['titleemail'] = $nv_Request->get_string( 'titleemail', 'post', '' );
	$sth = $db->prepare( 'UPDATE ' . TABLE_APPOINTMENT_NAME . '_setting SET config_value = :config_value WHERE config_name = :config_name');
	foreach( $getSetting as $config_name => $config_value )
	{
		$sth->bindParam( ':config_name', $config_name, PDO::PARAM_STR );
		$sth->bindParam( ':config_value', $config_value, PDO::PARAM_STR );
		$sth->execute();
	}
	$sth->closeCursor();
	
	$nv_Request->set_Session( $module_data . '_success', $lang_module['setting_update_success'] );

	$nv_Cache->delMod( $module_name );
	Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&rand=' . nv_genpass() );
	die();
}

$getSetting['infoemail'] = htmlspecialchars( nv_editor_br2nl($getSetting['infoemail'] ) );

if( defined( 'NV_EDITOR' ) and nv_function_exists( 'nv_aleditor' ) )
{
	$getSetting['infoemail'] = nv_aleditor( 'infoemail', '100%', '300px', $getSetting['infoemail'], '', $moudulepath, $currentpath );
}
else
{
	$getSetting['infoemail'] = "<textarea class=\"form-control\" style=\"width: 100%\" name=\"infoemail\" id=\"' . $module_data . '_infoemail\" rows=\"10\">" . $data['infoemail'] . "</textarea>";
} 
$getSetting['activesms'] = ( $getSetting['activesms'] == 1 ) ? 'checked="checked"' : ''; 
$getSetting['activeemail'] = ( $getSetting['activeemail'] == 1 ) ? 'checked="checked"' : ''; 
$getSetting['location'] = nv_htmlspecialchars( nv_br2nl( $getSetting['location'] ) );
$groups_list = nv_groups_list();


$getSetting['default_group_doctors_title'] = isset( $groups_list[$getSetting['default_group_doctors']] ) ? $groups_list[$getSetting['default_group_doctors']] : '';

$xtpl = new XTemplate( 'setting.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
$xtpl->assign( 'LANG', $lang_module );
$xtpl->assign( 'GLANG', $lang_global );
$xtpl->assign( 'NV_BASE_ADMINURL', NV_BASE_ADMINURL );
$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
$xtpl->assign( 'MODULE_NAME', $module_name );
$xtpl->assign( 'OP', $op );
$xtpl->assign( 'DATA', $getSetting  );




$xtpl->assign( 'SHOW1', ( $getSetting['default_group_doctors_title'] == '' ) ? '' : 'showx' );


for( $hour = 1; $hour <= 24 ; ++$hour )
{
	$xtpl->assign( 'SMSHOURSTART', array('key'=> $hour, 'name' => str_pad( $hour, 2, "0", STR_PAD_LEFT ), 'selected'=> ( $hour == $getSetting['smshourstart'] ) ? 'selected="selected"' : '' ) );
	$xtpl->parse( 'main.smshourstart' );
}

for( $minute = 0; $minute <= 59 ; ++$minute )
{
	$xtpl->assign( 'SMSMINUTESTART', array('key'=> $minute, 'name' => str_pad( $minute, 2, "0", STR_PAD_LEFT ), 'selected'=> ( $minute == $getSetting['smsminutestart'] ) ? 'selected="selected"' : '' ) );
	$xtpl->parse( 'main.smsminutestart' );
}

unset( $hour, $minute );

for( $hour = 1; $hour <= 24 ; ++$hour )
{
	$xtpl->assign( 'EMAILHOURSTART', array('key'=> $hour, 'name' => str_pad( $hour, 2, "0", STR_PAD_LEFT ), 'selected'=> ( $hour == $getSetting['emailhourstart'] ) ? 'selected="selected"' : '' ) );
	$xtpl->parse( 'main.emailhourstart' );
}

for( $minute = 0; $minute <= 59 ; ++$minute )
{
	$xtpl->assign( 'EMAILMINUTESTART', array('key'=> $minute, 'name' => str_pad( $minute, 2, "0", STR_PAD_LEFT ), 'selected'=> ( $minute == $getSetting['emailminutestart'] ) ? 'selected="selected"' : '' ) );
	$xtpl->parse( 'main.emailminutestart' );
}

if( $nv_Request->get_string( $module_data . '_success', 'session' ) )
{
	$xtpl->assign( 'SUCCESS', $nv_Request->get_string( $module_data . '_success', 'session' ) );

	$xtpl->parse( 'main.success' );

	$nv_Request->unset_request( $module_data . '_success', 'session' );

}

$xtpl->parse( 'main' );
$contents = $xtpl->text( 'main' );
include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';