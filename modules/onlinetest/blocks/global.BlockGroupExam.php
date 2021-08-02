<?php
 
/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */

if( ! defined( 'NV_MAINFILE' ) )
{
	die( 'Stop!!!' );
}

if( ! nv_function_exists( 'nukevn_block_group_exam' ) )
{
	function nukevn_block_group_exam_config( $module, $data_block, $lang_block )
	{
		global $nv_Cache, $site_mods;

		$html_input = '';
		
		$sql = 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_group_exam ORDER BY sort ASC';
		$list = $nv_Cache->db( $sql, '', $module );
		
		$html .= '<div class="form-group">';
		$html .= '	<label class="control-label col-sm-6">' . $lang_block['group_exam_id'] . '</label>';
		$html .= '	<div class="col-sm-18">';
		$html .= '		<select name="config_group_exam_id" class="form-control w200">';
		
		foreach( $list as $l )
		{
			$xtitle_i = '';

			if( $l['lev'] > 0 )
			{
				for( $i = 1; $i <= $l['lev']; ++$i )
				{
					$xtitle_i .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				}
			}
			$html .= '	<option value="' . $l['group_exam_id'] . '" ' . ( ( $data_block['group_exam_id'] == $l['group_exam_id'] ) ? ' selected="selected"' : '' ) . '>' . $xtitle_i . $l['title'] . '</option>';
		}
		 
		$html .= '		</select>';
		$html .= '	</div>';
		$html .= '</div>';		
		
		$html .= '<div class="form-group">';
		$html .= '	<label class="control-label col-sm-6">' . $lang_block['title_length'] . '</label>';
		$html .= '	<div class="col-sm-18">';
		$html .= '		<input class="form-control" name="config_numrow" type="text" value="' . $data_block['title_length'] . '">';
		$html .= '	</div>';
		$html .= '</div>';		
 
		return $html;
	}

	function nukevn_block_group_exam_config_submit( $module, $lang_block )
	{
		global $nv_Request;
		$return = array();
		$return['error'] = array();
		$return['config'] = array();
		$return['config']['group_exam_id'] = $nv_Request->get_int( 'config_group_exam_id', 'post', 0 );
		$return['config']['title_length'] = $nv_Request->get_int( 'config_title_length', 'post', 80 );
		return $return;
	}

	function nukevn_block_group_exam( $block_config )
	{
		global $module_array_group_exam, $module_info, $lang_module, $global_config, $site_mods;
		
		$module = $block_config['module'];
		 
		if( file_exists( NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $site_mods[$module]['module_file'] . '/BlockGroupExam.tpl' ) )
		{
			$block_theme = $global_config['module_theme'];
		}
		else
		{
			$block_theme = 'default';
		}

		$xtpl = new XTemplate( 'BlockGroupExam.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/' . $site_mods[$module]['module_file'] );

		if( ! empty( $module_array_group_exam ) )
		{
			$title_length = $block_config['title_length'];

			$xtpl->assign( 'LANG', $lang_module );
			$xtpl->assign( 'BLOCK_ID', $block_config['bid'] );
			$xtpl->assign( 'TEMPLATE', $block_theme );
			$html = '';
			foreach( $module_array_group_exam as $cat )
			{
				if( $block_config['group_exam_id'] == 0 && $cat['parent_id'] == 0 || ( $block_config['group_exam_id'] > 0 && $cat['parent_id'] == $block_config['group_exam_id'] ) )
				{
					$cat['title_cut'] = nv_clean60( $cat['title'], $title_length );

					$xtpl->assign( 'CAT', $cat );

					// if( ! empty( $cat['subcatid'] ) )
					// {
						// $xtpl->assign( 'SUBCAT', nukevn_block_group_exam_sub( $cat['subcatid'], $title_length, $block_theme, $module ) );
						// $xtpl->parse( 'main.cat.subcat' );
					// }
					$xtpl->parse( 'main.cat' );
				}
			}
			$xtpl->assign( 'MENUID', $block_config['bid'] );

			$xtpl->parse( 'main' );
			return $xtpl->text( 'main' );
		}
	}

	function nukevn_block_group_exam_sub( $list_sub, $title_length, $block_theme, $module )
	{
		global $module_array_group_exam, $site_mods;

		if( empty( $list_sub ) )
		{
			return '';
		}
		else
		{
			$xtpl = new XTemplate( 'BlockGroupExam.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/' . $site_mods[$module]['module_file'] );

			$list = explode( ',', $list_sub );
			foreach( $list as $group_exam_id )
			{
				$subcat = $module_array_group_exam[$group_exam_id];

				$subcat['title_cut'] = nv_clean60( $subcat['title'], $title_length );

				$xtpl->assign( 'SUBCAT', $subcat );

				if( ! empty( $subcat['subcatid'] ) )
				{
					$xtpl->assign( 'SUB', nukevn_block_group_exam_sub( $subcat['subcatid'], $title_length, $block_theme, $module ) );
					$xtpl->parse( 'subcat.loop.sub' );
				}
				$xtpl->parse( 'subcat.loop' );
			}
			$xtpl->parse( 'subcat' );
			return $xtpl->text( 'subcat' );
		}
	}
}

if( defined( 'NV_SYSTEM' ) )
{
	global $site_mods, $module_name, $onlineTestGroupExam, $module_array_group_exam, $nv_Cache;
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
		$content = nukevn_block_group_exam( $block_config );
	}
}
