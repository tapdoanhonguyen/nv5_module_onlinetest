<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) )
{
	die( 'Stop!!!' );
}

$sql = 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $mod_data . '_group_exam ORDER BY sort ASC';
$result = $db->query( $sql );
while( $row = $result->fetch() )
{
	$array_item[$row['group_exam_id']] = array(
		'parentid' => $row['parent_id'],
		'groups_view' => $row['groups_view'],
		'key' => $row['group_exam_id'],
		'title' => $row['title'],
		'alias' => $row['alias'] );
}
