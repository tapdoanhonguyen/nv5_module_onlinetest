<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */

if( ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );

if( ! nv_function_exists( 'nukevn_block_max_score_user' ) )
{
	function nukevn_block_max_score_user_config( $module, $data_block, $lang_block )
	{
		global $db, $language_array;

		$html = '';
		$html .= '<div class="form-group">';
		$html .= '	<label class="control-label col-sm-6">' . $lang_block['numrow'] . '</label>';
		$html .= '	<div class="col-sm-18">';
		$html .= '		<input class="form-control" name="config_numrow" type="text" value="' . $data_block['numrow'] . '">';
		$html .= '	</div>';
		$html .= '</div>';		
		return $html;
	}

	function nukevn_block_max_score_user_config_submit( $module, $lang_block )
	{
		global $nv_Request;
		$return = array();
		$return['error'] = array();
		$return['config'] = array();
		$return['config']['numrow'] = $nv_Request->get_int( 'config_numrow', 'post', 0 );
		return $return;
	}

	function nukevn_block_max_score_user( $block_config )
	{
		global $module_array_group_exam, $nv_Cache, $global_config, $module_info, $lang_module, $db, $module_config, $site_mods;

		$module = $block_config['module'];
		$mod_upload = $site_mods[$module]['module_upload'];
		$mod_data = $site_mods[$module]['module_data'];
		$mod_file = $site_mods[$module]['module_file'];
		$content = '';

		$numrow = ( $block_config['numrow'] ) ? $block_config['numrow'] : 5;
		// $cache_file = NV_LANG_DATA . '_block_max_score_user_' . $numrow . '_' . NV_CACHE_PREFIX . '.cache';
		// if( ( $cache = $nv_Cache->getItem( $module, $cache_file ) ) != false )
		// {
			// $content = unserialize( $cache );
		// }
		// else
		// {
			if( file_exists( NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/onlinetest/BlockMaxScoreUser.tpl' ) )
			{
				$block_theme = $module_info['template'];
			}
			else
			{
				$block_theme = 'default';
			}

			$dataContent = array();

			$sql = 'SELECT h.userid, h.history_id, u.username, u.first_name, u.last_name, u.photo, COUNT(h.userid) exam_number, sum(h.score) total_score 
				FROM ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_history h 	 
				LEFT JOIN ' . NV_USERS_GLOBALTABLE . ' u ON ( h.userid = u.userid ) GROUP BY h.userid ORDER BY total_score DESC LIMIT 0, ' . $numrow;

			$result = $db->query( $sql );

			while( $item = $result->fetch() )
			{
				$item['full_name'] = nv_show_name_user( $item['first_name'], $item['last_name'], $item['username'] );
				if( $item['total_score'] > 0 && $item['exam_number'] > 0)
				{
					$item['level'] = floor( $item['total_score'] / $item['exam_number'] );
				
				}else{
					$item['level'] =  0;
				}
				$dataContent[] = $item;
			}

			$xtpl = new XTemplate( 'BlockMaxScoreUser.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/onlinetest' );
			$stt = 1;
			foreach( $dataContent as $loop )
			{

				$loop['stt'] = $stt;
				if( ! empty( $loop['photo'] ) && file_exists( NV_ROOTDIR . '/' . $loop['photo'] ) )
				{
					$loop['photo'] = NV_BASE_SITEURL . $loop['photo'];
				}
				else
				{
					$loop['photo'] = NV_BASE_SITEURL . 'themes/' . $global_config['module_theme'] . '/images/users/no_avatar.png';
				}
				$xtpl->assign( 'LOOP', $loop );

				$xtpl->parse( 'main.loop' );
				++$stt;
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

		$content = nukevn_block_max_score_user( $block_config );
	}
}
