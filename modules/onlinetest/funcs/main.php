<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */

if( ! defined( 'NV_IS_MOD_TEST' ) ) die( 'Stop!!!' );

$page_title = $module_info['custom_title'];
$key_words = $module_info['keywords'];

$base_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name;
$base_url_rewrite = nv_url_rewrite( $base_url, true );
$page_url_rewrite = ( $page > 1 ) ? nv_url_rewrite( $base_url . '/page-' . $page, true ) : $base_url_rewrite;
$request_uri = $_SERVER['REQUEST_URI'];
if( ! ( $home or $request_uri == $base_url_rewrite or $request_uri == $page_url_rewrite or NV_MAIN_DOMAIN . $request_uri == $base_url_rewrite or NV_MAIN_DOMAIN . $request_uri == $page_url_rewrite ) )
{
	$redirect = '<meta http-equiv="Refresh" content="3;URL=' . $base_url_rewrite . '" />';
	nv_info_die( $lang_global['error_404_title'], $lang_global['error_404_title'], $lang_global['error_404_content'] . $redirect, 404 );
}

$setView = 'view_by_group_exam'; //view_by_all

$dataContent = array();
$dataContentEssay = array();

if( $setView == 'view_by_group_exam' )
{
	$key = 0;
	$key1 = 0;
	foreach( $onlineTestGroupExam as $_group_exam_id => $group_exam )
	{ 
	
		
		if( nv_user_in_groups( $group_exam['groups_view'] ) && $group_exam['inhome'] == 1 && $group_exam['parent_id'] == 0)
		{
 
			$dataContent[$key] = $group_exam;
			
			$getListGroupExam = getListGroupExam( $_group_exam_id ); 
		 
			$db->sqlreset()
				->select('type_exam_id, group_exam_id, title, point, images, thumb, introtext, description, keywords, num_question, time, status, date_added')
				->from( TABLE_ONLINETEST_NAME . '_type_exam' )
				->order( 'date_added DESC' )
				->limit( $group_exam['numlinks'] );
			
			$implode = array();
			$implode[]= 'status=1';
			if( $getListGroupExam )
			{
				$implode[]= 'group_exam_id IN ( ' . implode(',', $getListGroupExam ) . ' )';
			}
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
 
			$db->where( implode(' AND ', $implode ) );
 
			$result = $db->query( $db->sql() );
			
			while( $item = $result->fetch() )
			{
				$item['alias'] = strtolower( change_alias( $item['title'] ) );
				$item['link'] = ( $onlineTestConfig['open'] == 1 ) ? NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=dotest/' . $item['alias'] . '-' . $item['type_exam_id'] . $global_config['rewrite_exturl'] : 'javascript:void(0);';
				if( $item['thumb'] == 1 )
				{
					//image thumb
					$item['imghome'] = NV_BASE_SITEURL . NV_FILES_DIR . '/' . $module_upload . '/' . $item['images'];
				}
				elseif( $item['thumb'] == 2 )
				{
					//image file
					$item['imghome'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $item['images'];
				}
				elseif( $item['thumb'] == 3 )
				{
					//image url
					$item['imghome'] = $item['images'];
				}
				
				else
				{
					$item['imghome'] = '';
				}
				
				 
				$dataContent[$key]['content'][] = $item;
				 
				
			}
			$result->closeCursor();
			++$key;
		}
		
		
		if( nv_user_in_groups( $group_exam['groups_view'] ) && $group_exam['inhome'] == 1 && $group_exam['parent_id'] == 0)
		{
 
			$dataContentEssay[$key1] = $group_exam;
			
			$getListGroupExam = getListGroupExam( $_group_exam_id ); 
		 
			$db->sqlreset()
				->select('essay_exam_id, group_exam_id, title, point, images, thumb, introtext, description, keywords, num_question, time, status, date_added')
				->from( TABLE_ONLINETEST_NAME . '_essay_exam' )
				->order( 'date_added DESC' )
				->limit( $group_exam['numlinks'] );
			
			$implode = array();
			$implode[]= 'status=1';
			if( $getListGroupExam )
			{
				$implode[]= 'group_exam_id IN ( ' . implode(',', $getListGroupExam ) . ' )';
			}
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
 
			$db->where( implode(' AND ', $implode ) );
 
			$result = $db->query( $db->sql() );
			
			while( $item = $result->fetch() )
			{
				$item['alias'] = strtolower( change_alias( $item['title'] ) );
				$item['link'] = ( $onlineTestConfig['open'] == 1 ) ? NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=dotestessay/' . $item['alias'] . '-' . $item['essay_exam_id'] . $global_config['rewrite_exturl'] : 'javascript:void(0);';
				if( $item['thumb'] == 1 )
				{
					//image thumb
					$item['imghome'] = NV_BASE_SITEURL . NV_FILES_DIR . '/' . $module_upload . '/' . $item['images'];
				}
				elseif( $item['thumb'] == 2 )
				{
					//image file
					$item['imghome'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $item['images'];
				}
				elseif( $item['thumb'] == 3 )
				{
					//image url
					$item['imghome'] = $item['images'];
				}
				
				else
				{
					$item['imghome'] = '';
				}
				
				 
				$dataContentEssay[$key1]['content'][] = $item;
				 
				
			}
			$result->closeCursor();
			++$key1;
		}
	}
 
	$contents = ThemeOnlineTestViewByGroupExam( $dataContent, $dataContentEssay );
}
 



include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';
