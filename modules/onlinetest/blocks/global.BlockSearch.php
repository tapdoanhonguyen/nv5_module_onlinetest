<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */

if( ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );

if( ! nv_function_exists( 'nukevn_block_search' ) )
{
 
	function nukevn_block_search( $block_config )
	{
		global $nv_Cache, $nv_Request, $global_config, $module_info, $lang_module, $db, $module_config, $site_mods;

		$module = $block_config['module'];
		$mod_upload = $site_mods[$module]['module_upload'];
		$mod_data = $site_mods[$module]['module_data'];
		$mod_file= $site_mods[$module]['module_file'];
		$content = '';
		
		$q = $nv_Request->get_title('q', 'get', '' );
		$type = $nv_Request->get_int('t', 'get', 1 );
		
		if( file_exists( NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $site_mods[$module]['module_file'] . '/BlockSearch.tpl' ) )
		{
			$block_theme = $module_info['template'];
		}
		else
		{
			$block_theme = 'default';
		}

		$xtpl = new XTemplate( 'BlockSearch.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/' . $site_mods[$module]['module_file'] . '' );
		$xtpl->assign( 'URLSEARCH', nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=search', true ));
		$xtpl->assign( 'Q',  $q );
		$arrayType = array('1'=> 'Trắc Nghiệm', '2'=> 'Tự Luận');
		foreach( $arrayType as $key => $name )
		{
			$xtpl->assign( 'TYPE', array('key'=> $key, 'name'=> $name, 'checked'=> ($key == $type ) ? 'checked="checked"' : '' ) );

			$xtpl->parse( 'main.type' );
		}
		
		$xtpl->parse( 'main' );
		$content = $xtpl->text( 'main' );
			
			 
		return $content;
	}
}

if( defined( 'NV_SYSTEM' ) )
{
	global $site_mods, $module_name, $nv_Cache;
	$module = $block_config['module'];
	if( isset( $site_mods[$module] ) )
	{
		$content = nukevn_block_search( $block_config );
	}
}
