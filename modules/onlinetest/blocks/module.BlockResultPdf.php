<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */

if( ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );

if( ! nv_function_exists( 'nukevn_block_result_pdf' ) )
{
 
	function nukevn_block_result_pdf( $block_config )
	{
		global $module_array_group_exam, $onlineTestTitleFirst, $lang_module, $dataContent, $dataQuestion, $nv_Cache, $global_config, $module_info, $lang_module, $db, $module_config, $site_mods;

		$module = $block_config['module'];
		$mod_upload = $site_mods[$module]['module_upload'];
		$mod_data = $site_mods[$module]['module_data'];
		$mod_file = $site_mods[$module]['module_file'];

		if( $dataContent['type_id'] ==  2 )
		{ 
			if( file_exists( NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/'. $site_mods[$module]['module_file'] .'/BlockResultPdf.tpl' ) )
			{
				$block_theme = $module_info['template'];
			}
			else
			{
				$block_theme = 'default';
			}
	 
			$xtpl = new XTemplate( 'BlockResultPdf.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/'. $site_mods[$module]['module_file'] .'' );
			$xtpl->assign( 'DATA', $dataContent ); 
			$xtpl->assign( 'LANG', $lang_module ); 
			
			if( isset( $dataContent['question'] ) )
			{
				$questionTest = $dataContent['question'];
				$stt = 1;
				foreach( $questionTest as $question_id => $item )
				{ 
					
		 
					$item['question_id'] = $question_id;
					
					$item['stt'] = $stt;
					$count = 0;
					$_checked = 0;
					$notenough = 0;
					$checkWrong = 0;
					$listTrue = array();
					foreach( $item['sys_answers'] as $key  )
					{
						$sys_answers = $dataQuestion[$question_id]['answers'];
						$sys_trueanswer = isset( $dataQuestion[$question_id]['trueanswer'] ) ? $dataQuestion[$question_id]['trueanswer'] : array();
						
						if( isset( $item['user_answers'] ) && in_array( $key, $item['user_answers'] ) )
						{
							$checked_class = 'checked';
							$checked = 'checked="checked"';
							++$_checked;
							 
						}else{
							$checked_class = '';
							$checked = '';
							
						}
						$titleABC = ( $onlineTestTitleFirst[$count] ) ? $onlineTestTitleFirst[$count] : $count;
						
						if( in_array( $key, $sys_trueanswer ) )
						{
							$listTrue[] = $titleABC;
						}
						
						
						if( !empty( $checked ) && ! in_array( $key, $sys_trueanswer ) ) 
						{
							++$checkWrong;
						}
						if( in_array( $key, $sys_trueanswer ) ) 
						{
							if( empty( $checked ) )
							{
								++$checkWrong;
							}
							
						}
						$xtpl->assign( 'ANSWERS',array(
							'key'=> $key,
							'title'=> $titleABC,	
							'checked'=> $checked, 
							'checked_class'=> $checked_class,
							'trueanswer'=> ( in_array( $key, $sys_trueanswer ) && ( $dataContent['allow_show_answer'] == 1 || $checked ) ) ? 'trueanswer' : 'wrong' 
						) );
	 
						$xtpl->parse( 'main.loop.answers' );
						++$count;
					}

					if( !empty( $checkWrong ) || empty( $_checked ) )
					{
						$error =  empty( $_checked ) ? $lang_module['empty_ans'] : $lang_module['failed'];
						$xtpl->assign( 'LISTTRUE', array( 'error'=> $error, 'ans'=> implode(', ', $listTrue)));
						if( $dataContent['allow_show_answer'] == 1 )
						{
							$xtpl->parse( 'main.loop.show_wrong.allow_answer' );
						}
						$xtpl->parse( 'main.loop.show_wrong' );
					}
		 
					if( !empty( $checkWrong ) && ! empty( $_checked ) )
					{
						$class='aw_not_correct';
					}
					elseif( empty( $checkWrong )  )
					{
						$class='aw_correct';
					}
					elseif( empty( $_checked )  )
					{
						$class='';
					}
					elseif( empty( $_checked )  )
					{
						$class='';
					}
					else
					{
						$class='';
					}
	 
					$xtpl->assign( 'LOOP', $item );

					$xtpl->assign( 'NUM', str_pad( $stt, 2, '0', STR_PAD_LEFT ) ); 
					$xtpl->assign( 'QUESTION_ID',  $question_id );
					$xtpl->assign( 'CLASS',  $class );
					$xtpl->parse( 'main.loop_num_question' );
					
					$xtpl->parse( 'main.loop' );
					++$stt;
				}	
			}
			
		
		 
			$xtpl->parse( 'main' );
			return $xtpl->text( 'main' );
		}
		else return '';
	}
}

if( defined( 'NV_SYSTEM' ) )
{
	global $site_mods, $onlineTestGroupExam, $module_name, $module_array_group_exam, $nv_Cache;
	$module = $block_config['module'];
	if( isset( $site_mods[$module] ) )
	{
		$content = nukevn_block_result_pdf( $block_config );
	}
}
