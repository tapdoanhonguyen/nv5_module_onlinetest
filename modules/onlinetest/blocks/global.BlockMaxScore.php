<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */

if( ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );

if( ! nv_function_exists( 'nukevn_block_max_score' ) )
{
	function nukevn_block_max_score_config( $module, $data_block, $lang_block )
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

	function nukevn_block_max_score_config_submit( $module, $lang_block )
	{
		global $nv_Request;
		$return = array();
		$return['error'] = array();
		$return['config'] = array();
		$return['config']['numrow'] = $nv_Request->get_int( 'config_numrow', 'post', 0 );
		$return['config']['title_length'] = $nv_Request->get_int( 'config_title_length', 'post', 0 );
		return $return;
	}

	function nukevn_block_max_score( $block_config )
	{
		global $module_array_group_exam, $user_info, $nv_Cache, $global_config, $module_info, $lang_module, $db, $module_config, $site_mods;

		$module = $block_config['module'];
		$mod_upload = $site_mods[$module]['module_upload'];
		$mod_data = $site_mods[$module]['module_data'];
		$mod_file = $site_mods[$module]['module_file'];
		$dataContent = array();

		$numrow = ( $block_config['numrow'] ) ? $block_config['numrow'] : 5;
		// $cache_file = NV_LANG_DATA . '_block_max_score_' . $numrow . '_' . NV_CACHE_PREFIX . '.cache';
		// if( ( $cache = $nv_Cache->getItem( $module, $cache_file ) ) != false )
		// {
			// $dataContent = unserialize( $cache );
		// }
		// else
		// {
 
			$sql = 'SELECT h.userid, h.type_exam_id, h.history_id, h.score, h.test_time, h.history_alias, u.username, u.first_name, u.last_name, te.title FROM ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_history h 
				LEFT JOIN ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_type_exam te ON ( h.type_exam_id = te.type_exam_id )
				LEFT JOIN ' . NV_USERS_GLOBALTABLE . ' u ON ( h.userid = u.userid ) ORDER BY h.score DESC, ABS( h.score / te.num_question ) DESC LIMIT 0, ' . $numrow;

			$result = $db->query( $sql );

			while( $item = $result->fetch() )
			{
				$item['full_name'] = nv_show_name_user( $item['first_name'], $item['last_name'], $item['username'] );

				$item['test_time'] = nv_date( 'd/m/Y H:i:s', $item['test_time'] );

				$item['alias'] = strtolower( change_alias( $item['title'] ) );

				$item['title'] = nv_clean60( $item['title'], $block_config['title_length'] );

				$item['link'] = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $item['history_alias'] . '/' . strtolower( change_alias( $item['title'] ) ) . $global_config['rewrite_exturl'], true );
				
				
				$dataContent[] = $item;
			}
			
			// $cache = serialize( $dataContent );
			// $nv_Cache->setItem( $module, $cache_file, $cache );
		// }
		
		if( file_exists( NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $site_mods[$module]['module_file'] . '/BlockMaxScore.tpl' ) )
		{
			$block_theme = $module_info['template'];
		}
		else
		{
			$block_theme = 'default';
		}
		$xtpl = new XTemplate( 'BlockMaxScore.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/' . $site_mods[$module]['module_file'] . '' );
		if( !empty( $dataContent ) )
		{
			foreach( $dataContent as $loop )
			{
				$xtpl->assign( 'LOOP', $loop );
				if( $loop['userid'] == $user_info['userid'] ) 
				{
					$xtpl->parse( 'main.loop.show_url' );
				}
				else 
				{
					$xtpl->parse( 'main.loop.hide_url' );
				}
 
				$xtpl->parse( 'main.loop' );
			}
		}
 
		$xtpl->parse( 'main' );
		$content = $xtpl->text( 'main' );
 
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

		$content = nukevn_block_max_score( $block_config );
	}
}
