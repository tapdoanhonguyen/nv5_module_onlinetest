<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */

if( ! defined( 'NV_IS_MOD_TEST' ) ) die( 'Stop!!!' );

$key = $nv_Request->get_title( 'q', 'get', '' );
$key = str_replace( '+', ' ', $key );
$key = trim( nv_substr( $key, 0, NV_MAX_SEARCH_LENGTH ) );
$keyhtml = nv_htmlspecialchars( $key );

$base_url_rewrite = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op;
if( ! empty( $key ) )
{
	$base_url_rewrite .= '&q=' . urlencode( $key );
}

$type = $nv_Request->get_int( 't', 'get', 1 );
if( ! empty( $type ) )
{
	$base_url_rewrite .= '&t=' . $type;
}

$page = $nv_Request->get_int( 'page', 'get', 1 );
$page = ( $page == 0 ) ? 1 : $page;

$base_url_rewrite = nv_url_rewrite( $base_url_rewrite, true );

$request_uri = $_SERVER['REQUEST_URI'];
if( $request_uri != $base_url_rewrite and NV_MAIN_DOMAIN . $request_uri != $base_url_rewrite )
{
	header( 'Location: ' . $base_url_rewrite );
	die();
}
 
$dataContent = array();

$getListGroupExam = array();
foreach( $onlineTestGroupExam as $_group_exam_id => $group_exam )
{
	if( nv_user_in_groups( $group_exam['groups_view'] ) )
	{
		$getListGroupExam[] = $_group_exam_id;
	}
}
$implode = array();
$implode[] = 'status=1';

if( !empty( $getListGroupExam ) )
{
	$implode[] = 'group_exam_id IN ( ' . implode( ',', $getListGroupExam ) . ' )';
}


if( empty( $groupUsers ) )
{
	$implode[] = '( user_create_id = ' . intval( $globalUserid ) . ' OR group_user = \'\' )';
}
else
{
	$string = " ( user_create_id = " . intval( $globalUserid ) . " OR group_user = '' OR ";
	$strarr = array();
	foreach( $groupUsers as $_group_user_id => $group )
	{
		$like = ',' . $_group_user_id . ',';
		$strarr[] = "group_user LIKE '%" . $like . "%'";
	}
	$string .= implode( ' OR ', $strarr );
	$string .= " ) ";
	$implode[] = $string;
}

$dbkey = $db_slave->dblikeescape( $key );

if( !empty( $key ) )
{
	$implode[] = '( title LIKE \'%'. $dbkey .'%\' OR introtext LIKE \'%'. $dbkey .'%\' OR description LIKE \'%'. $dbkey .'%\' )';
}


if( $type ==1  )
{
	$sql = TABLE_ONLINETEST_NAME . '_type_exam';
}
else
{
	$sql = TABLE_ONLINETEST_NAME . '_essay_exam';
}
$db_slave->sqlreset()->select( 'COUNT(*)' )->from( $sql );

$db_slave->where( implode( ' AND ', $implode ) );

$num_items = $db_slave->query( $db_slave->sql() )->fetchColumn();

if( $type ==1  )
{
	$db_slave->select( 'type_exam_id, group_exam_id, title, point, images, thumb, introtext, description, keywords, num_question, time, status, date_added' )->order( 'date_added DESC' )->limit( $perpage )->offset( ( $page - 1 ) * $perpage );

}
else
{
	$db_slave->select( 'essay_exam_id, group_exam_id, title, point, images, thumb, introtext, description, keywords, num_question, time, status, date_added' )->order( 'date_added DESC' )->limit( $perpage )->offset( ( $page - 1 ) * $perpage );

}

$result = $db_slave->query( $db_slave->sql() );

while( $item = $result->fetch() )
{
	$item['alias'] = strtolower( change_alias( $item['title'] ) );
	if( $type ==1  )
	{
		$item['link'] = ( $onlineTestConfig['open'] == 1 ) ? NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=dotest/' . $item['alias'] . '-' . $item['type_exam_id'] . $global_config['rewrite_exturl'] : 'javascript:void(0);';
	
	}
	else
	{
		$item['link'] = ( $onlineTestConfig['open'] == 1 ) ? NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=dotestessay/' . $item['alias'] . '-' . $item['essay_exam_id'] . $global_config['rewrite_exturl'] : 'javascript:void(0);';
	
	}
	
	if( $item['thumb'] == 1 )
	{

		$item['imghome'] = NV_BASE_SITEURL . NV_FILES_DIR . '/' . $module_upload . '/' . $item['images'];
	}
	elseif( $item['thumb'] == 2 )
	{

		$item['imghome'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $item['images'];
	}
	elseif( $item['thumb'] == 3 )
	{

		$item['imghome'] = $item['images'];
	}
	elseif( ! empty( $show_no_image ) )
	{
		//no image
		$item['imghome'] = NV_BASE_SITEURL . $show_no_image;
	}
	else
	{
		$item['imghome'] = '';
	}

	$dataContent[] = $item;
}
$result->closeCursor();

$generatePage = nv_alias_page( $page_title, $base_url, $num_items, $perpage, $page );

$contents = ThemeOnlineTestViewSearch( $dataContent, $generatePage );

 

if( empty( $key ) )
{
	$page_title = $lang_module['search_title'] . NV_TITLEBAR_DEFIS . $module_info['custom_title'];
}
else
{
	$page_title = $key . NV_TITLEBAR_DEFIS . $lang_module['search_title'];
	if( $page > 2 )
	{
		$page_title .= NV_TITLEBAR_DEFIS . $lang_global['page'] . ' ' . $page;
	}
	$page_title .= NV_TITLEBAR_DEFIS . $module_info['custom_title'];
}

$key_words = $description = 'no';
$mod_title = isset( $lang_module['main_title'] ) ? $lang_module['main_title'] : $module_info['custom_title'];

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';
