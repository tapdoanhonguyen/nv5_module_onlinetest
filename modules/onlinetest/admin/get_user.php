<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2013 Webdep24.com. All rights reserved
 * @Blog  http://dangdinhtu.com
 * @License GNU/GPL version 2 or any later version
 * @Createdate  Wed, 21 Jan 2015 14:00:59 GMT
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

$_username = $nv_Request->get_string( 'username', 'get', '' );
$json = array();

$and = '';
if( ! empty( $_username ) )
{
	$and .= ' AND username LIKE :username';
}
$sql = 'SELECT userid, username FROM ' . NV_USERS_GLOBALTABLE . '  
WHERE active=1 ' . $and . '
ORDER BY username ASC LIMIT 0, 100';
$sth = $db->prepare( $sql );
if( ! empty( $_username ) )
{
	$sth->bindValue( ':username', '%' . $_username . '%' );
}
$sth->execute();

while( list( $userid, $username ) = $sth->fetch( 3 ) )
{
	$json[] = array( 'userid' => $userid, 'username' => nv_htmlspecialchars( $username ) );
}
 
nv_jsonOutput( $json );
