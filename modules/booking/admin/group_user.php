<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );


if( ACTION_METHOD == 'get_user' )
{
	$username = $nv_Request->get_string( 'username', 'get', '' );
 
	// $group_user_id = $nv_Request->get_int( 'group_user_id', 'post', 0 );
 
	// $userlist = $nv_Request->get_typed_array( 'userlist', 'post', 'int', array() );
	
	// $userlist = array_unique( array_filter( $userlist ) );
	
	
	$json = array();

	$and = '';
	if( ! empty( $username ) )
	{
		$and .= ' AND username LIKE :username';
	}
	$sql = 'SELECT userid, username FROM ' . NV_USERS_GLOBALTABLE . '  
	WHERE active=1 ' . $and . '
	ORDER BY username ASC LIMIT 0, 50';
	$sth = $db->prepare( $sql );
	if( ! empty( $username ) )
	{
		$sth->bindValue( ':username', '%' . $username . '%' );
	}
	$sth->execute();
	while( list( $userid, $username ) = $sth->fetch( 3 ) )
	{
		$json[] = array( 'userid' => $userid, 'username' => nv_htmlspecialchars( $username ) );
	}
	 
	nv_jsonOutput( $json );

}
elseif( ACTION_METHOD == 'get_group' )
{ 
	$groups_list = nv_groups_list();

	foreach( $groups_list as $group_id => $item )
	{
		if( $group_id > 7 )
		{
			$json[] = array( 'group_user_id' => $group_id, 'title' => nv_htmlspecialchars( $item  ) );
		}
		
	}
	 
	nv_jsonOutput( $json );

}
 
