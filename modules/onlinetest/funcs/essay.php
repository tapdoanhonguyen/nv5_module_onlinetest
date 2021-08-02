<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */

if( ! defined( 'NV_IS_MOD_TEST' ) ) die( 'Stop!!!' );
 
 

if( isset( $array_op[2] ) and substr( $array_op[2], 0, 5 ) == 'page-' )
{
	$page = intval( substr( $array_op[2], 5 ) );
}


$parent_id = $essay_group_exam_id;
while( $parent_id > 0 )
{
	if( isset( $onlineTestGroupExam[$parent_id] ) )
	{
		$array_cat_i = $onlineTestGroupExam[$parent_id];
		$array_mod_title[] = array(
			'catid' => $parent_id,
			'title' => $array_cat_i['title'],
			'link' => $array_cat_i['link_essay'] );
		$parent_id = $array_cat_i['parent_id'];
	}
	
}
sort( $array_mod_title, SORT_NUMERIC ); 

$base_url_rewrite = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=essay/' . $onlineTestGroupExam[$essay_group_exam_id]['alias'];
if( $page > 1 )
{
	$base_url_rewrite .= '/page-' . $page;
}
$base_url_rewrite = nv_url_rewrite( $base_url_rewrite, true );
if( $_SERVER['REQUEST_URI'] != $base_url_rewrite and NV_MAIN_DOMAIN . $_SERVER['REQUEST_URI'] != $base_url_rewrite )
{
	Header( 'Location: ' . $base_url_rewrite );
	die();
}

$page_title = !empty( $onlineTestGroupExam[$essay_group_exam_id]['meta_title'] ) ? $onlineTestGroupExam[$essay_group_exam_id]['meta_title'] : $onlineTestGroupExam[$essay_group_exam_id]['title'];

$key_words = !empty( $onlineTestGroupExam[$essay_group_exam_id]['meta_keyword'] ) ? $onlineTestGroupExam[$essay_group_exam_id]['meta_keyword'] : '';

$description = !empty( $onlineTestGroupExam[$essay_group_exam_id]['meta_description'] ) ? $onlineTestGroupExam[$essay_group_exam_id]['meta_description'] : '';

$base_url = $onlineTestGroupExam[$essay_group_exam_id]['link_essay'];

$dataContent = array();
 
$getListGroupExam = getListGroupExam( $essay_group_exam_id );

$implode = array();
$implode[] = 'status=1';
if( $getListGroupExam )
{
	$implode[] = 'group_exam_id IN ( ' . implode( ',', $getListGroupExam ) . ' )';
	
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
	
	$db_slave->sqlreset()
			->select('COUNT(*)')
			->from( TABLE_ONLINETEST_NAME . '_essay_exam' );
	 
	$db_slave->where( implode( ' AND ', $implode ) );
	
	$num_items = $db_slave->query($db_slave->sql())->fetchColumn();
 
	$db_slave->select( 'essay_exam_id, group_exam_id, title, point, images, thumb, introtext, description, keywords, num_question, time, status, date_added' )
			->order( 'date_added DESC' )
			->limit( $perpage )
            ->offset( ( $page - 1 ) * $perpage );
 
	$result = $db_slave->query( $db_slave->sql() );

	while( $item = $result->fetch() )
	{
		$item['alias'] = strtolower( change_alias( $item['title'] ) );
		$item['link'] = ( $onlineTestConfig['open'] == 1 ) ? NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=dotestessay/' . $item['alias'] . '-' . $item['essay_exam_id'] . $global_config['rewrite_exturl'] : 'javascript:void(0);';
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
	
	$contents = ThemeOnlineTestViewByEssayGroupExamGrid( $dataContent, $generatePage );
	
}else
{
	$error['permission'] = $lang_module['error_group_exam_permission'];
	
	$contents = ThemeOnlineTestError( $error ); 
		
}	 

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';
