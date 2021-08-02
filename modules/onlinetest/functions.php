<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */

if( ! defined( 'NV_SYSTEM' ) ) die( 'Stop!!!' );

define( 'NV_IS_MOD_TEST', true );

define( 'TABLE_ONLINETEST_NAME', NV_PREFIXLANG . '_' . $module_data );

define( 'ACTION_METHOD', $nv_Request->get_string( 'action', 'get,post', '' ) );

require_once NV_ROOTDIR . '/modules/' . $module_file . '/global.functions.php';
 
$group_exam_id = 0;
$parent_id = 0;

$essay_group_exam_id = 0;
$essay_parent_id = 0;

$alias_cat_url = isset( $array_op[0] ) ? $array_op[0] : '';
$essay_alias_cat_url = isset( $array_op[1] ) ? $array_op[1] : '';
$array_mod_title = array();

if( ! empty( $onlineTestGroupExam ) )
{
	foreach( $onlineTestGroupExam as $key => $l )
	{
		$onlineTestGroupExam[$l['group_exam_id']] = $l;
		$onlineTestGroupExam[$l['group_exam_id']]['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $l['alias'];
		$onlineTestGroupExam[$l['group_exam_id']]['link_essay'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=essay/' . $l['alias'];
		if( $alias_cat_url == $l['alias'] )
		{
			$group_exam_id = $l['group_exam_id'];
			$parent_id = $l['parent_id'];
 	
		}
		if( $essay_alias_cat_url == $l['alias'] )
		{
			$essay_group_exam_id = $l['group_exam_id'];
			$essay_parent_id = $l['parent_id'];
 	
		}
	}
}

//Xac dinh RSS
if( $module_info['rss'] )
{
	$rss[] = array( 'title' => $module_info['custom_title'], 'src' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $module_info['alias']['rss'] );
}

foreach( $onlineTestGroupExam as $_group_exam_id => $array_cat_i )
{
	if( $_group_exam_id > 0 and $array_cat_i['parent_id'] == 0 )
	{
		$act = 0;
		$submenu = array();
		if( $_group_exam_id == $group_exam_id or $_group_exam_id == $parent_id )
		{
			$act = 1;
			if( ! empty( $onlineTestGroupExam[$_group_exam_id]['subcatid'] ) )
			{
				$array_catid = explode( ',', $onlineTestGroupExam[$_group_exam_id]['subcatid'] );
				foreach( $array_catid as $sub_catid_i )
				{
					$array_sub_cat_i = $onlineTestGroupExam[$sub_catid_i];
					$sub_act = 0;
					if( $sub_catid_i == $group_exam_id )
					{
						$sub_act = 1;
					}
					$submenu[] = array(
						$array_sub_cat_i['title'],
						$array_sub_cat_i['link'],
						$sub_act );
				}
			}
		}
		$nv_vertical_menu[] = array(
			$array_cat_i['title'],
			$array_cat_i['link'],
			$act,
			'submenu' => $submenu );
	}

	//Xac dinh RSS
	if( $_group_exam_id and $module_info['rss'] )
	{
		$rss[] = array( 'title' => $module_info['custom_title'] . ' - ' . $array_cat_i['title'], 'src' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $module_info['alias']['rss'] . '/' . $array_cat_i['alias'] );
	}
}
unset( $result, $_group_exam_id, $parent_id_i, $title_i, $alias_i );

$module_info['submenu'] = 0;

$page = 1;
$perpage = $onlineTestConfig['perpage'];
 
$count_op = sizeof( $array_op );
if( ! empty( $array_op ) and $op == 'main' )
{
	$op = 'main';
	if( $count_op == 1 or substr( $array_op[1], 0, 5 ) == 'page-' )
	{
		if( $count_op > 1 or $group_exam_id > 0 )
		{
			$op = 'viewgroupexam';
			if( isset( $array_op[1] ) and substr( $array_op[1], 0, 5 ) == 'page-' )
			{
				$page = intval( substr( $array_op[1], 5 ) );
			}
		}
		elseif( $group_exam_id == 0 )
		{
			$contents = $lang_module['nocatpage'] . $array_op[0];
			if( isset( $array_op[0] ) and substr( $array_op[0], 0, 5 ) == 'page-' )
			{
				$page = intval( substr( $array_op[0], 5 ) );
			}
		}
		
	}elseif( $count_op == 2 )
	{
		 
		$historyCode = $array_op[0];
		if( $historyCode )
		{
			$op = 'share';
		}

	}
 
	$parent_id = $group_exam_id;
	while( $parent_id > 0 )
	{
		if( isset( $onlineTestGroupExam[$parent_id] ) )
		{
			$array_cat_i = $onlineTestGroupExam[$parent_id];
			$array_mod_title[] = array(
				'catid' => $parent_id,
				'title' => $array_cat_i['title'],
				'link' => $array_cat_i['link'] );
			$parent_id = $array_cat_i['parent_id'];
		}
		
	}
	sort( $array_mod_title, SORT_NUMERIC );
}

function getListGroupExam( $group_exam_id )
{
	global $onlineTestGroupExam;
	
	if( nv_user_in_groups( $onlineTestGroupExam[$group_exam_id]['groups_view'] ) )
	{
		$array_group_exam[] = $group_exam_id;
	}
	$subcatid = explode( ',', $onlineTestGroupExam[$group_exam_id]['subcatid'] );
	if( ! empty( $subcatid ) )
	{
		foreach( $subcatid as $_group_exam_id )
		{
			if( $_group_exam_id > 0 && nv_user_in_groups( $onlineTestGroupExam[$_group_exam_id]['groups_view'] ) ) 
			{
				if( $onlineTestGroupExam[$_group_exam_id]['numsubcat'] == 0 )
				{
					$array_group_exam[] = intval( $_group_exam_id );
				}
				else
				{
					$array_temp = getListGroupExam( $_group_exam_id );
					foreach( $array_temp as $group_exam_id_i )
					{
						if( nv_user_in_groups( $onlineTestGroupExam[$group_exam_id_i]['groups_view'] ) )
						{
							$array_group_exam[] = intval( $group_exam_id_i );
						}
						
					}
				}
			}
		}
	}
	return array_unique( $array_group_exam );
}


$groupUsers = array();
if( !empty( $user_info ) )
{
	
	$result = $db->query( 'SELECT gu.group_user_id, gu.title FROM ' . TABLE_ONLINETEST_NAME . '_group_user_list gul INNER JOIN ' . TABLE_ONLINETEST_NAME . '_group_user gu ON( gul.group_user_id = gu.group_user_id ) WHERE gul.userid = ' . intval( $user_info['userid'] ) );
 	while( $group = $result->fetch() )
	{
		$groupUsers[$group['group_user_id']] = $group['title'];
		
	}
	$result->closeCursor();
	
}

$globalUserid = defined( 'NV_IS_USER' ) ? $user_info['userid'] : 0;