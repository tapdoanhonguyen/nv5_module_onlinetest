<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */

if ( ! defined( 'NV_IS_MOD_TEST' ) ) die( 'Stop!!!' );


 
if( $onlineTestConfig['open'] == 0 || $count_op != 2 )
{
	Header( 'Location: ' . nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name, true ) );
	exit();
}

if ( ! $globalUserid )
{
	$link_redirect = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=users&' . NV_OP_VARIABLE . '=login&&nv_redirect=' . nv_redirect_encrypt( $client_info['selfurl'] );
	
	Header( 'Location: ' . $link_redirect );
	exit();
}

$array_page = explode( '-', $array_op[1] );
$essay_exam_id = intval( end( $array_page ) );
$number = strlen( $essay_exam_id ) + 1;
$alias_url = substr( $array_op[1], 0, -$number );


if( $essay_exam_id == 0 || $alias_url == '' )
{
	Header( 'Location: ' . nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name, true ) );
	exit();
}
 

$checkHistoryTest = $nv_Request->get_int( 'history_essay_' . $essay_exam_id,  'session' );
 
if( empty( $checkHistoryTest ) )
{
	$timeout = $nv_Request->get_int( $module_data . '_test_timeout_essay_'. $essay_exam_id, 'cookie', 0 );

	if( $onlineTestConfig['test_timeout'] > 0 && empty( $checkHistoryTest ) )
	{
		if( ( NV_CURRENTTIME - $timeout ) < ( $onlineTestConfig['test_timeout'] * 60 ) )
		{
			$error['timeout'] = $lang_module['dotest_error_timeout'];
			
			$contents = ThemeOnlineTestError( $error ); 
			
			include NV_ROOTDIR . '/includes/header.php';
			echo nv_site_theme( $contents );
			include NV_ROOTDIR . '/includes/footer.php';
			
		}
	}
}
 
 
 
$implode = array(); 
$implode[] = 'status=1';
$implode[] = 'essay_exam_id = ' . intval( $essay_exam_id );
	
if( empty( $groupUsers ) )
{
	$implode[]= '( user_create_id = '. intval( $globalUserid ) .' OR group_user = \'\' )';
}
else 
{
	$string = " ( user_create_id = ". intval( $globalUserid ) ." OR group_user = '' OR ";
	$strarr= array();
	foreach( $groupUsers as $_group_user_id => $group )
	{
		$like = ',' . $_group_user_id . ',';
		$strarr[] = "group_user LIKE '%" . $like . "%'";
	}
	$string.= implode( ' OR ', $strarr );
	$string.= " ) ";
	$implode[]= $string;
}
$where = ''; 
if( !empty ( $implode ) )
{
	$where.= ( implode( ' AND ', $implode ) );
}


$query = $db->query('SELECT * FROM ' . TABLE_ONLINETEST_NAME . '_essay_exam WHERE ' . $where );
$dataContent = $query->fetch();
 
$showTesting = 0;

if( !empty ( $checkHistoryTest ) )
{
	$history = $db->query( 'SELECT * FROM ' . TABLE_ONLINETEST_NAME . '_history_essay WHERE history_essay_id = '. intval( $checkHistoryTest ) .' AND is_deleted=0 AND is_sended=0 AND userid = '. intval( $globalUserid ) .' AND essay_exam_id = ' . intval( $essay_exam_id))->fetch();

	if ( $history && ( $history['test_time'] + ( $history['time'] * 60 ) ) > NV_CURRENTTIME )
	{
		$showTesting = 1;
 
	}
	else
	{
		$nv_Request->set_Session( 'history_essay_' . $dataContent['essay_exam_id'], 0 );	
	}
	

	
}
 
if( ! nv_user_in_groups( $onlineTestGroupExam[$dataContent['group_exam_id']]['groups_view'] ) )
{
	Header( 'Location: ' . nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name, true ) );
	exit();
}
 
$dataContent['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $dataContent['essay_exam_id'] );
$dataContent['alias'] = strtolower( change_alias( $dataContent['title'] ) );
 
$meta_property['og:type'] = 'article';

$meta_property['article:published_time'] = date( 'Y-m-dTH:i:s', $dataContent['date_added'] );

$date_modified = ( $dataContent['date_modified'] == 0 ) ? $dataContent['date_added'] : $dataContent['date_modified'];

$meta_property['article:modified_time'] = date( 'Y-m-dTH:i:s', $date_modified );

$meta_property['article:section'] = $onlineTestGroupExam[$dataContent['group_exam_id']]['title'];

$show_no_image = 1;		

if( ! empty( $dataContent['images'] ) )
{
	$meta_property['og:image'] = ( preg_match( '/^(http|https|ftp|gopher)\:\/\//', $dataContent['images'] ) ) ? $dataContent['images'] : NV_MY_DOMAIN . NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $dataContent['images'];
}
 
$time_set = $nv_Request->get_int($module_data . '_' . $op . '_' . $essay_exam_id, 'session');
if ( empty( $time_set ) ) 
{
	$nv_Request->set_Session( $module_data . '_' . $op . '_' . $essay_exam_id, NV_CURRENTTIME);
	$db->query( 'UPDATE ' . TABLE_ONLINETEST_NAME . '_essay_exam SET viewed=viewed+1 WHERE essay_exam_id=' . intval( $essay_exam_id ) );
}            



if( ! empty( $dataContent ) && $dataContent['alias'] != $alias_url )
{
	$url_Permanently = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=dotestessay/' . $dataContent['alias'] . '-' . $dataContent['essay_exam_id'] . $global_config['rewrite_exturl'], true );
	header( "HTTP/1.1 301 Moved Permanently" );
	header( 'Location:' . $url_Permanently );
	exit();
}

$page_title =  $dataContent['title'];
$description =  $dataContent['description'];
$key_words = '';

 
$point = $db->query( 'SELECT point FROM '. TABLE_ONLINETEST_NAME .'_point WHERE userid='. intval( $globalUserid ) )->fetchColumn();

$dataContent['user_point'] = $point;
		
$contents = ThemeOnlineTestDoTestEssay( $dataContent, $showTesting ); 
 
$parent_id = $dataContent['group_exam_id'];
while( $parent_id > 0 )
{
	$array_cat_i = $onlineTestGroupExam[$parent_id];
	$array_mod_title[] = array(
		'catid' => $parent_id,
		'title' => $array_cat_i['title'],
		'link' => $array_cat_i['link'] );
	$parent_id = $array_cat_i['parent_id'];
}
sort( $array_mod_title, SORT_NUMERIC );
 
$my_head .= '<script type="text/javascript" src="' . NV_BASE_SITEURL . NV_EDITORSDIR . '/ckeditor/ckeditor.js"></script>';
  
 
include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';