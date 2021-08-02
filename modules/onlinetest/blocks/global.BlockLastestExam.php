<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */

if( ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );

if( ! nv_function_exists( 'nukevn_block_group_exam_lastest' ) )
{
	function nukevn_block_group_exam_lastest_config( $module, $data_block, $lang_block )
	{
		global $db, $language_array;

		$html = '';
 
		$html .= '<div class="form-group">';
		$html .= '	<label class="control-label col-sm-6">' . $lang_block['numrow'] . '</label>';
		$html .= '	<div class="col-sm-18">';
		$html .= '		<input class="form-control" name="config_numrow" type="text" value="' . $data_block['numrow'] . '">';
		$html .= '	</div>';
		$html .= '</div>';		
			
		$html .= '<div class="form-group">';
		$html .= '	<label class="control-label col-sm-6">' . $lang_block['title_length'] . '</label>';
		$html .= '	<div class="col-sm-18">';
		$html .= '		<input class="form-control" name="config_title_length" type="text" value="' . $data_block['title_length'] . '">';
		$html .= '	</div>';
		$html .= '</div>';		
		
		return $html;
	}

	function nukevn_block_group_exam_lastest_config_submit( $module, $lang_block )
	{
		global $nv_Request;
		$return = array();
		$return['error'] = array();
		$return['config'] = array();
		$return['config']['numrow'] = $nv_Request->get_int( 'config_numrow', 'post', 0 );
		$return['config']['title_length'] = $nv_Request->get_int( 'config_title_length', 'post', 0 );
		return $return;
	}
 
	
	function nukevn_block_group_exam_lastest( $block_config )
	{
		global $module_array_group_exam, $nv_Cache, $global_config, $module_info, $lang_module, $db_slave, $module_config, $site_mods;

		$module = $block_config['module'];
		$mod_upload = $site_mods[$module]['module_upload'];
		$mod_data = $site_mods[$module]['module_data'];
		$mod_file= $site_mods[$module]['module_file'];
		$content = '';
		
		$numrow = ( $block_config['numrow'] ) ? $block_config['numrow'] : 1;
		// $cache_file = NV_LANG_DATA . '_block_lastest_exam_' . $numrow . '_' . NV_CACHE_PREFIX . '.cache';
		// if( ( $cache = $nv_Cache->getItem( $module, $cache_file ) ) != false )
		// {
			// $content = unserialize( $cache );
		// }
		// else
		// {
			if( file_exists( NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $site_mods[$module]['module_file'] . '/BlockLastestExam.tpl' ) )
			{
				$block_theme = $module_info['template'];
			}
			else
			{
				$block_theme = 'default';
			}
			
			
			$listGroupExamId = array();
			foreach( $module_array_group_exam as $_group_exam_id => $group_exam )
			{
				if( nv_user_in_groups( $group_exam['groups_view'] ) )
				{
					$listGroupExamId[] = $_group_exam_id;
				}
			}
 
			
			global $user_info;
			
			$globalUserid = defined( 'NV_IS_USER' ) ? $user_info['userid'] : 0;


			$groupUsers = array();
			if( !empty( $user_info ) )
			{
				
				$result = $db_slave->query( 'SELECT gu.group_user_id, gu.title FROM ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_group_user_list gul INNER JOIN ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_group_user gu ON( gul.group_user_id = gu.group_user_id ) WHERE gul.userid = ' . intval( $globalUserid ) );
				while( $group = $result->fetch() )
				{
					$groupUsers[$group['group_user_id']] = $group['title'];
					
				}
				$result->closeCursor();
				
			}
			
			if( !empty( $getListGroupExam ) )
			{
				$implode[] = 'group_exam_id IN ( ' . implode( ',', $listGroupExamId ) . ' )';
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
			
			$dataContent = array();

			$db_slave->sqlreset()
				->select( 'type_exam_id, group_exam_id, title, point, images, thumb, introtext, description, keywords, num_question, time, status, date_added' )
				->from( NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_type_exam' )
				->where( implode( ' AND ', $implode ) )
				->limit( $numrow )
				->offset( 0 );
			 
			
			$result = $db_slave->query( $db_slave->sql() );

			while( $item = $result->fetch() )
			{

				$item['alias'] = strtolower( change_alias( $item['title'] ) );

				$item['title_cut'] = nv_clean60( $item['title'], $block_config['title_length'] );
				
				$item['groupexam'] = isset( $module_array_group_exam[$item['group_exam_id']] ) ? $module_array_group_exam[$item['group_exam_id']]['title'] : '';
				$item['groupexam_url'] = isset( $module_array_group_exam[$item['group_exam_id']] ) ? $module_array_group_exam[$item['group_exam_id']]['link'] : '';
				
				
				// $item['introtext'] = nv_clean60( $item['introtext'], 125 );

				/* if( $item['thumb'] == 1 )
				{
					//image thumb
					$item['imghome'] = NV_BASE_SITEURL . NV_FILES_DIR . '/' . $mod_upload . '/' . $item['images'];
				}
				elseif( $item['thumb'] == 2 )
				{
					//image file
					$item['imghome'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $mod_upload . '/' . $item['images'];
				}
				elseif( $item['thumb'] == 3 )
				{
					//image url
					$item['imghome'] = $item['images'];
				}
	 
				else
				{
					$item['imghome'] = '';
				} */

				$item['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=dotest/' . $item['alias'] . '-' . $item['type_exam_id'] . $global_config['rewrite_exturl'];
				$dataContent[] = $item;
			}

			$xtpl = new XTemplate( 'BlockLastestExam.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/' . $site_mods[$module]['module_file'] . '' );

			foreach( $dataContent as $loop )
			{
				$xtpl->assign( 'LOOP', $loop );

				$xtpl->parse( 'main.loop' );
			}
			
			$xtpl->parse( 'main' );
			$content = $xtpl->text( 'main' );
			
			// $cache = serialize( $content );
			// $nv_Cache->setItem( $module, $cache_file, $cache );
			
		// }
		return $content;
	}
}

if( defined( 'NV_SYSTEM' ) )
{
	global $site_mods, $onlineTestGroupExam, $module_name, $module_array_group_exam, $nv_Cache;
	$module = $block_config['module'];
	if( isset( $site_mods[$module] ) )
	{

		if( $module == $module_name )
		{
			$module_array_group_exam = $onlineTestGroupExam;
			unset( $module_array_group_exam[0] );
		}
		else
		{
			$module_array_group_exam = array();
			$sql = 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_group_exam ORDER BY sort ASC';
			$list = $nv_Cache->db( $sql, 'group_exam_id', $module );
			if( ! empty( $list ) )
			{
				foreach( $list as $l )
				{
					$module_array_group_exam[$l['group_exam_id']] = $l;
					$module_array_group_exam[$l['group_exam_id']]['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $l['alias'];
				}
			}
		}

		$content = nukevn_block_group_exam_lastest( $block_config );
	}
}
