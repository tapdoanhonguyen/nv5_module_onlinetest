<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */

if( ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );

if( ! nv_function_exists( 'nukevn_block_do_test' ) )
{
 
	function nukevn_block_do_test( $block_config )
	{
		global $module_array_group_exam, $op, $dataContent, $nv_Cache, $global_config, $module_info, $lang_module, $db, $module_config, $site_mods;

		$module = $block_config['module'];
		$mod_upload = $site_mods[$module]['module_upload'];
		$mod_data = $site_mods[$module]['module_data'];
		$mod_file = $site_mods[$module]['module_file'];
		
		 
		if( file_exists( NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/'. $site_mods[$module]['module_file'] .'/BlockDoTest.tpl' ) )
		{
			$block_theme = $module_info['template'];
		}
		else
		{
			$block_theme = 'default';
		}
	
		$xtpl = new XTemplate( 'BlockDoTest.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/'. $site_mods[$module]['module_file'] .'' );
		$xtpl->assign( 'DATA', $dataContent ); 
		
		if( $op == 'dotest' )
		{
			if( $dataContent['type_id'] == 0 || $dataContent['type_id'] == 1 )
			{
				if( !empty( $dataContent['num_question'] ) )
				{
					for( $num = 1; $num <= $dataContent['num_question']; ++$num )
					{
						
						$xtpl->assign( 'NUM', str_pad( $num, 2, '0', STR_PAD_LEFT ) ); 
						
						$xtpl->parse( 'main.type.loop' );
					}
				}
				$xtpl->parse( 'main.type' );
			}
			else
			{
				for( $num = 1; $num <= $dataContent['num_question']; ++$num )
				{
	 
					$xtpl->assign( 'NUM', str_pad( $num, 2, '0', STR_PAD_LEFT ) ); 
					
					$xtpl->parse( 'main.type2.loop_question' );
				}
				
				$xtpl->parse( 'main.type2' );
			}
			
			$xtpl->parse( 'main' );
			return $xtpl->text( 'main' );
		}
		elseif( $op == 'dotestessay' )
		{
			 
			if( !empty( $dataContent['num_question'] ) )
			{
				for( $num = 1; $num <= $dataContent['num_question']; ++$num )
				{
					
					$xtpl->assign( 'NUM', str_pad( $num, 2, '0', STR_PAD_LEFT ) ); 
					
					$xtpl->parse( 'dotestessay.loop' );
				}
			}
			
			$xtpl->parse( 'dotestessay' );
			return $xtpl->text( 'dotestessay' );
		}
 	 
	}
}

if( defined( 'NV_SYSTEM' ) )
{
	global $site_mods, $onlineTestGroupExam, $module_name, $module_array_group_exam, $nv_Cache;
	$module = $block_config['module'];
	if( isset( $site_mods[$module] ) )
	{
		$content = nukevn_block_do_test( $block_config );
	}
}
