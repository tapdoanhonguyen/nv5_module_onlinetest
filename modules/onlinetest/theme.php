<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */

if ( ! defined( 'NV_IS_MOD_TEST' ) ) die( 'Stop!!!' );
 
function ThemeOnlineTestViewByGroupExam ( $dataContent, $dataContentEssay )
{
    global $onlineTestConfig, $client_info, $onlineTestGroupExam, $group_exam_id, $lang_module, $lang_global, $module_info, $module_name, $module_file, $global_config, $user_info, $op;
    $xtpl = new XTemplate( 'ThemeOnlineTestViewByGroupExam.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
    $xtpl->assign( 'LANG', $lang_module );
    $xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'SELFURL', $client_info['selfurl'] );
	$xtpl->assign( 'CONFIG', $onlineTestConfig );
	foreach ( $dataContent as $key => $data ) 
	{
        if ( isset( $dataContent[$key]['content'] ) ) 
		{
			
			
			$xtpl->assign('GROUP_EXAM', $data );
			
			if ( $data['subcatid'] != '' )
			{
				$_arr_subcat = explode( ',', $data['subcatid'] );
				$limit = 0;
				foreach ( $_arr_subcat as $group_exam_id_i )
				{
					if ( $onlineTestGroupExam[$group_exam_id_i]['status'] == 1 )
					{
						$xtpl->assign( 'SUBGROUP', $onlineTestGroupExam[$group_exam_id_i] );
						$xtpl->parse( 'main.group_exam.subgrouploop' );
						$limit++;
					}
					if ( $limit >= 3 )
					{
						$more = array( 'title' => $lang_module['more'], 'link' => $onlineTestGroupExam[$data['group_exam_id']]['link'] );
						$xtpl->assign( 'MORE', $more );
						$xtpl->parse( 'main.group_exam.subcatmore' );
						break;
					}
				}
			}

			
			foreach ( $dataContent[$key]['content'] as $loop )
			{
				$loop['title_cut'] = nv_clean60( $loop['title'], 40 );
				$loop['point'] = ( $loop['point']  > 0 ) ? $loop['point'] . ' Vcoin ': $lang_module['free'];
				
				$xtpl->assign( 'LOOP', $loop );
				
				if( $onlineTestConfig['open'] == 1 )
				{
					$xtpl->assign( 'CHECKOPEN', 'isopen' );
					$xtpl->parse( 'main.group_exam.loop.open' );
					
				}else{
					$xtpl->assign( 'CHECKOPEN', 'isclose' );
					$xtpl->parse( 'main.group_exam.loop.close' );
				}
				
				$xtpl->parse( 'main.group_exam.loop' );
			
			}
 
			$xtpl->parse( 'main.group_exam' );
			
		}
		
	}
	

	foreach ( $dataContentEssay as $key => $data ) 
	{	
        if ( isset( $dataContentEssay[$key]['content'] ) ) 
		{
			 
			$data['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=essay/' . $data['alias'];
			$xtpl->assign('ESSAY_EXAM', $data );
			
			if ( $data['subcatid'] != '' )
			{
				$_arr_subcat = explode( ',', $data['subcatid'] );
				$limit = 0;
				foreach ( $_arr_subcat as $group_exam_id_i )
				{
					if ( $onlineTestGroupExam[$group_exam_id_i]['status'] == 1 )
					{
						$xtpl->assign( 'SUBGROUP', $onlineTestGroupExam[$group_exam_id_i] );
						$xtpl->parse( 'main.essay_exam.subgrouploop' );
						$limit++;
					}
					if ( $limit >= 3 )
					{
						$more = array( 'title' => $lang_module['more'], 'link' => $onlineTestGroupExam[$data['group_exam_id']]['link'] );
						$xtpl->assign( 'MORE', $more );
						$xtpl->parse( 'main.essay_exam.subcatmore' );
						break;
					}
				}
			}

			
			foreach ( $dataContentEssay[$key]['content'] as $loop )
			{
				$loop['title_cut'] = nv_clean60( $loop['title'], 40 );
				$loop['point'] = ( $loop['point']  > 0 ) ? $loop['point'] . ' Vcoin ': $lang_module['free'];
				
				$xtpl->assign( 'LOOP', $loop );
				
				if( $onlineTestConfig['open'] == 1 )
				{
					$xtpl->assign( 'CHECKOPEN', 'isopen' );
					$xtpl->parse( 'main.essay_exam.loop.open' );
					
				}else{
					$xtpl->assign( 'CHECKOPEN', 'isclose' );
					$xtpl->parse( 'main.essay_exam.loop.close' );
				}
				
				$xtpl->parse( 'main.essay_exam.loop' );
			
			}
 
			$xtpl->parse( 'main.essay_exam' );
			
		}
		
	}
	
    $xtpl->parse( 'main' );
    return $xtpl->text( 'main' );
}
 
function ThemeOnlineTestViewByEssayGroupExamGrid ( $dataContent, $generatePage )
{
    global $onlineTestConfig, $onlineTestGroupExam, $essay_group_exam_id, $client_info, $lang_module, $lang_global, $module_info, $module_name, $module_file, $global_config, $user_info, $op;
    $xtpl = new XTemplate( 'ThemeOnlineTestViewByEssayGroupExamGrid.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
    $xtpl->assign( 'LANG', $lang_module );
    $xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'SELFURL', $client_info['selfurl'] );
	$xtpl->assign( 'CONFIG', $onlineTestConfig );

	$group_exam = $onlineTestGroupExam[$essay_group_exam_id];
	$xtpl->assign('GROUP_EXAM', $group_exam  );
	
	if ( $group_exam['subcatid'] != '' )
	{
		$_arr_subcat = explode( ',', $group_exam['subcatid'] );
		$limit = 0;
		foreach ( $_arr_subcat as $group_exam_id_i )
		{
			if ( $onlineTestGroupExam[$group_exam_id_i]['status'] == 1 )
			{
				$xtpl->assign( 'SUBGROUP', $onlineTestGroupExam[$group_exam_id_i] );
				$xtpl->parse( 'main.group_exam.subgrouploop' );
				$limit++;
			}
			if ( $limit >= 3 )
			{
				$more = array( 'title' => $lang_module['more'], 'link' => $onlineTestGroupExam[$group_exam['group_exam_id']]['link'] );
				$xtpl->assign( 'MORE', $more );
				$xtpl->parse( 'main.group_exam.subcatmore' );
				break;
			}
		}
	}

	
	if( $dataContent )
	{
 
		foreach( $dataContent as $loop )
		{
			$loop['title_cut'] = nv_clean60( $loop['title'], 40 );
			$loop['point'] = ( $loop['point']  > 0 ) ? $loop['point'] . ' Vcoin ': $lang_module['free'];
				
			$xtpl->assign( 'LOOP', $loop );

			if( $onlineTestConfig['open'] == 1 )
			{
				$xtpl->assign( 'CHECKOPEN', 'isopen' );
				$xtpl->parse( 'main.group_exam.loop.open' );

			}
			else
			{
				$xtpl->assign( 'CHECKOPEN', 'isclose' );
				$xtpl->parse( 'main.group_exam.loop.close' );
			}

			$xtpl->parse( 'main.group_exam.loop' );

		}
		$xtpl->parse( 'main.group_exam' );
	}
	if( $generatePage )
	{
		$xtpl->assign( 'GENERATE_PAGE', $generatePage );
		$xtpl->parse( 'main.generatePage' );
	}
    $xtpl->parse( 'main' );
    return $xtpl->text( 'main' );
}

function ThemeOnlineTestViewByGroupExamGrid ( $dataContent, $generatePage )
{
    global $onlineTestConfig, $onlineTestGroupExam, $group_exam_id, $client_info, $lang_module, $lang_global, $module_info, $module_name, $module_file, $global_config, $user_info, $op;
    $xtpl = new XTemplate( 'ThemeOnlineTestViewByGroupExamGrid.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
    $xtpl->assign( 'LANG', $lang_module );
    $xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'SELFURL', $client_info['selfurl'] );
	$xtpl->assign( 'CONFIG', $onlineTestConfig );

	$group_exam = $onlineTestGroupExam[$group_exam_id];
	$xtpl->assign('GROUP_EXAM', $group_exam  );
	
	if ( $group_exam['subcatid'] != '' )
	{
		$_arr_subcat = explode( ',', $group_exam['subcatid'] );
		$limit = 0;
		foreach ( $_arr_subcat as $group_exam_id_i )
		{
			if ( $onlineTestGroupExam[$group_exam_id_i]['status'] == 1 )
			{
				$xtpl->assign( 'SUBGROUP', $onlineTestGroupExam[$group_exam_id_i] );
				$xtpl->parse( 'main.group_exam.subgrouploop' );
				$limit++;
			}
			if ( $limit >= 3 )
			{
				$more = array( 'title' => $lang_module['more'], 'link' => $onlineTestGroupExam[$group_exam['group_exam_id']]['link'] );
				$xtpl->assign( 'MORE', $more );
				$xtpl->parse( 'main.group_exam.subcatmore' );
				break;
			}
		}
	}

	
	if( $dataContent )
	{
 
		foreach( $dataContent as $loop )
		{
			$loop['title_cut'] = nv_clean60( $loop['title'], 40 );
			$loop['point'] = ( $loop['point']  > 0 ) ? $loop['point'] . ' Vcoin ': $lang_module['free'];
				
			$xtpl->assign( 'LOOP', $loop );

			if( $onlineTestConfig['open'] == 1 )
			{
				$xtpl->assign( 'CHECKOPEN', 'isopen' );
				$xtpl->parse( 'main.group_exam.loop.open' );

			}
			else
			{
				$xtpl->assign( 'CHECKOPEN', 'isclose' );
				$xtpl->parse( 'main.group_exam.loop.close' );
			}

			$xtpl->parse( 'main.group_exam.loop' );

		}
		$xtpl->parse( 'main.group_exam' );
	}
	if( $generatePage )
	{
		$xtpl->assign( 'GENERATE_PAGE', $generatePage );
		$xtpl->parse( 'main.generatePage' );
	}
    $xtpl->parse( 'main' );
    return $xtpl->text( 'main' );
}
 
function ThemeOnlineTestViewSearch ( $dataContent, $generatePage )
{
    global $onlineTestConfig, $onlineTestGroupExam, $num_items, $key, $client_info, $lang_module, $lang_global, $module_info, $module_name, $module_file, $global_config, $user_info, $op;
    $xtpl = new XTemplate( 'ThemeOnlineTestViewSearch.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
    $xtpl->assign( 'LANG', $lang_module );
    $xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'SELFURL', $client_info['selfurl'] );
	$xtpl->assign( 'CONFIG', $onlineTestConfig );

 	$xtpl->assign('NUM_ITEMS', $num_items  );
 	$xtpl->assign('KEY', $key  );
 
	
	if( $dataContent )
	{
 
		foreach( $dataContent as $loop )
		{
			$loop['title_cut'] = nv_clean60( $loop['title'], 40 );
			$loop['point'] = ( $loop['point']  > 0 ) ? $loop['point'] . ' Vcoin ': $lang_module['free'];
				
			$xtpl->assign( 'LOOP', $loop );

			if( $onlineTestConfig['open'] == 1 )
			{
				$xtpl->assign( 'CHECKOPEN', 'isopen' );
				$xtpl->parse( 'main.group_exam.loop.open' );

			}
			else
			{
				$xtpl->assign( 'CHECKOPEN', 'isclose' );
				$xtpl->parse( 'main.group_exam.loop.close' );
			}

			$xtpl->parse( 'main.group_exam.loop' );

		}
		$xtpl->parse( 'main.group_exam' );
	}
	if( $generatePage )
	{
		$xtpl->assign( 'GENERATE_PAGE', $generatePage );
		$xtpl->parse( 'main.generatePage' );
	}
    $xtpl->parse( 'main' );
    return $xtpl->text( 'main' );
}
 
 
function ThemeOnlineTestDoTestEssay ( $dataContent, $showTesting )
{
    global $onlineTestConfig, $client_info, $lang_module, $lang_global, $module_info, $module_name, $module_file, $global_config, $user_info, $op;
    $xtpl = new XTemplate( 'ThemeOnlineTestDoTestEssay.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
    $xtpl->assign( 'TEMPLATE', $module_info['template'] );
    $xtpl->assign( 'LANG', $lang_module );
    $xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'SELFURL', $client_info['selfurl'] );
	$xtpl->assign( 'CONFIG', $onlineTestConfig );
	$xtpl->assign( 'USER', $user_info );
	
	
	$dataContent['date_added'] = nv_date('d/m/Y H:i', $dataContent['date_added']);
	
	$xtpl->assign( 'DATA', $dataContent );
 
	if( $dataContent['user_point'] < $dataContent['point'] && $showTesting == 0 )
	{
		$xtpl->assign( 'RECHARGE', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=recharge' );
	
		$xtpl->parse( 'main.close' );
	}else{
		 
		$xtpl->parse( 'main.open' );

		if( $showTesting == 1 )
		{
			$xtpl->parse( 'main.trigger_open' );
		}
		
	}
	
	
	
	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );  
}
 
function ThemeOnlineTestDoTest ( $dataContent, $showTesting )
{
    global $onlineTestConfig, $client_info, $lang_module, $lang_global, $module_info, $module_name, $module_file, $global_config, $user_info, $op;
    $xtpl = new XTemplate( 'ThemeOnlineTestDoTest.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
    $xtpl->assign( 'TEMPLATE', $module_info['template'] );
    $xtpl->assign( 'LANG', $lang_module );
    $xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'SELFURL', $client_info['selfurl'] );
	$xtpl->assign( 'CONFIG', $onlineTestConfig );
	$xtpl->assign( 'USER', $user_info );
	
	
	$dataContent['date_added'] = nv_date('d/m/Y H:i', $dataContent['date_added']);
	
	$xtpl->assign( 'DATA', $dataContent );
 
	if( $dataContent['user_point'] < $dataContent['point'] && $showTesting == 0 )
	{
		$xtpl->assign( 'RECHARGE', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=recharge' );
	
		$xtpl->parse( 'main.close' );
	}else{
		
		$xtpl->parse( 'main.open' );
	
		if( $showTesting == 1 )
		{
			$xtpl->parse( 'main.trigger_open' );
		}
		
	}
	
	
	
	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );  
}
 
function ThemeOnlineTestGetQuestionsEssay ( $dataContent, $dataQuestions, $history_essay_id )
{
    global $onlineTestConfig, $nv_Request, $onlineTestTitleFirst, $module_upload, $lang_module, $lang_global, $module_info, $module_name, $module_file, $global_config, $user_info, $client_info, $op;
    $xtpl = new XTemplate( 'ThemeOnlineTestGetQuestionsEssay.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'LANG', $lang_module );
    $xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'DATA', $dataContent );
	$xtpl->assign( 'HISTORY_ID', $history_essay_id );
	$xtpl->assign( 'TOKEN', md5( $nv_Request->session_id . $global_config['sitekey'] . $dataContent['essay_exam_id'] . $history_essay_id ) );

	if( $dataQuestions )
	{
		
		foreach( $dataQuestions as $essay_id => $loop )
		{
			if( nv_function_exists( 'getEditor' ) )
			{
				$loop['answer'] = getEditor( 'answers['. $essay_id .'][answer]', $data['answer'], '100%', '400px', '', '' );
			}
			else
			{
				$loop['answer'] = '<textarea style="width: 100%;height:400px" name="answers['. $essay_id .'][answer]" id="answer-'. $essay_id .'" cols="20" rows="10" class="form-control">' . $data['answer'] . '</textarea>';
			}
			$xtpl->assign( 'LOOP', $loop );
			if( defined( 'NV_IS_USER' ) )
			{
				
				$xtpl->assign( 'LOGIN',  '' );
			}else{
				$xtpl->assign( 'LOGIN',  $lang_module['error_login_comment'] );
			}
			$xtpl->parse( 'main.loop' );
			
		}
	}
	
	
	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );  
}
 
function ThemeOnlineTestGetQuestions ( $dataContent, $dataQuestions, $history_id )
{
    global $onlineTestConfig, $nv_Request, $onlineTestTitleFirst, $module_upload, $lang_module, $lang_global, $module_info, $module_name, $module_file, $global_config, $user_info, $client_info, $op;
    $xtpl = new XTemplate( 'ThemeOnlineTestGetQuestions.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'LANG', $lang_module );
    $xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'DATA', $dataContent );
	$xtpl->assign( 'HISTORY_ID', $history_id );
	$xtpl->assign( 'TOKEN', md5( $nv_Request->session_id . $global_config['sitekey'] . $dataContent['type_exam_id'] . $history_id ) );
	
	if( $dataContent['type_id'] == 0 || $dataContent['type_id'] == 1 )
	{

		if( $dataQuestions )
		{
			
			foreach( $dataQuestions as $_key => $loop )
			{
				$xtpl->assign( 'LOOP', $loop );
				$count = 0;
				foreach( $loop['answers'] as $key => $ans )
				{
					$xtpl->assign( 'ANS', array( 'key'=> $key, 'checked'=> ( in_array( $key, $loop['user_answers'] ) ) ? 'checked="checked"' : '', 'class_checked'=> ( in_array( $key, $loop['user_answers'] ) ) ? 'checked' : '', 'title'=> ( $onlineTestTitleFirst[$count] ) ? $onlineTestTitleFirst[$count] : $count, 'name'=> $ans) );
					$xtpl->parse( 'main.type.loop.answers' );
					++$count;
				}
				if( defined( 'NV_IS_USER' ) )
				{
					
					$xtpl->assign( 'LOGIN',  '' );
				}else{
					$xtpl->assign( 'LOGIN',  $lang_module['error_login_comment'] );
				}
				$xtpl->parse( 'main.type.loop' );
				
			}
		}
		$xtpl->parse( 'main.type' );	
	}
	else
	{
		if( !nv_is_url( $dataContent['pdf'] ) )
		{
			$dataContent['pdf'] =  NV_MY_DOMAIN . NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $dataContent['pdf'];
		}
		
		if( ! preg_match('/drive.google.com/siu', $dataContent['pdf'] ) )
		{
			$pdfview = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=viewfile', true ) . '?url=' . nv_redirect_encrypt( $dataContent['pdf'] ) ;

		}
		else
		{
			$pdfview = $dataContent['pdf'];
		}
		
		$xtpl->assign( 'PDFVIEW',  $pdfview);
		$xtpl->parse( 'main.type2' );	
			
	}
	
	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );  
}

function ThemeOnlineTestGetAnswers ( $dataContent, $dataQuestions, $history_id )
{
    global $onlineTestConfig, $onlineTestTitleFirst, $nv_Request, $module_upload, $lang_module, $lang_global, $module_info, $module_name, $module_file, $global_config, $user_info, $client_info, $op;
    $xtpl = new XTemplate( 'ThemeOnlineTestGetAnswers.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'LANG', $lang_module );
    $xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'DATA', $dataContent );
	$xtpl->assign( 'HISTORY_ID', $history_id );
	$xtpl->assign( 'TOKEN', md5( $nv_Request->session_id . $global_config['sitekey'] . $dataContent['type_exam_id'] . $history_id ) );

	foreach( $dataQuestions as $question_id => $item )
	{
		$answers = $item['answers'];
		
		$xtpl->assign( 'QUESTION_ID', $question_id );
		$xtpl->assign( 'QUESTION_TOKEN', md5( $nv_Request->session_id . $global_config['sitekey'] . $question_id ) );
		foreach( $answers as $ans )
		{
			 			
			$xtpl->assign( 'ANS', array('key'=> $ans, 'title'=> $onlineTestTitleFirst[$ans-1]) );
			$xtpl->parse( 'main.loop_question.answers' );
			 
		}
		$xtpl->parse( 'main.loop_question' );
	} 
	
	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );  
}

function ThemeOnlineTestCommentBox ( $dataContent, $dataComment, $page, $total_page )
{
    global $onlineTestConfig, $lang_module, $nv_Request, $lang_global, $module_info, $module_name, $module_file, $global_config, $user_info, $client_info, $op;
    $xtpl = new XTemplate( 'ThemeOnlineTestComment.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
    $xtpl->assign( 'LANG', $lang_module );
    $xtpl->assign( 'GLANG', $lang_global );
    $xtpl->assign( 'DATA', $dataContent );
    $xtpl->assign( 'USER', $user_info );
	$xtpl->assign( 'PAGE', $page + 1 );

	$xtpl->assign( 'LAST_TIME', NV_CURRENTTIME . ',' . md5( $nv_Request->session_id . $global_config['sitekey'] . NV_CURRENTTIME ) );
	if( $dataComment )
	{
		foreach( $dataComment as $key => $loop )
		{
			$loop['date_added'] = nv_date('H:i, d/m/Y');
			$loop['post_name'] = nv_show_name_user( $loop['first_name'], $loop['last_name'], $loop['username'] );
			$loop['comment_id_token'] =  $loop['comment_id'] . ',' . md5( $nv_Request->session_id . $global_config['sitekey'] . $loop['comment_id'] );
			if( ! empty( $loop['photo'] ) && file_exists( NV_ROOTDIR . '/' . $loop['photo'] ) )
			{
				$loop['photo'] = NV_BASE_SITEURL . $loop['photo'];
			}
			elseif( is_file( NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/images/users/no_avatar.png' ) )
			{
				$loop['photo'] = NV_BASE_SITEURL . 'themes/' . $global_config['module_theme'] . '/images/users/no_avatar.png';
			}
			else
			{
				$loop['photo'] = NV_BASE_SITEURL . 'themes/default/images/users/no_avatar.png';
			}
			$xtpl->assign( 'LOOP', $loop );		 
			$xtpl->parse( 'main.loop' );
		}
		
		if( $total_page > $onlineTestConfig['number_comment'])
		{
			$xtpl->parse( 'main.loadmore' );
		}
 
	}
 
	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );  
}

function ThemeOnlineTestComment ( $dataContent, $dataComment )
{
    global $onlineTestConfig, $lang_module, $lang_global, $nv_Request, $module_info, $module_name, $module_file, $global_config, $user_info, $client_info, $op;
    $xtpl = new XTemplate( 'ThemeOnlineTestComment.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
    $xtpl->assign( 'LANG', $lang_module );
    $xtpl->assign( 'GLANG', $lang_global );
    $xtpl->assign( 'DATA', $dataContent );
    $xtpl->assign( 'USER', $user_info );
 
	if( $dataComment )
	{
		foreach( $dataComment as $key => $loop )
		{
			$loop['date_added'] = nv_date('H:i, d/m/Y');
			$loop['post_name'] = nv_show_name_user( $loop['first_name'], $loop['last_name'], $loop['username'] );
			$loop['comment_id_token'] =  $loop['comment_id'] . ',' . md5( $nv_Request->session_id . $global_config['sitekey'] . $loop['comment_id'] );
			if( ! empty( $loop['photo'] ) && file_exists( NV_ROOTDIR . '/' . $loop['photo'] ) )
			{
				$loop['photo'] = NV_BASE_SITEURL . $loop['photo'];
			}
			elseif( is_file( NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/images/users/no_avatar.png' ) )
			{
				$loop['photo'] = NV_BASE_SITEURL . 'themes/' . $global_config['module_theme'] . '/images/users/no_avatar.png';
			}
			else
			{
				$loop['photo'] = NV_BASE_SITEURL . 'themes/default/images/users/no_avatar.png';
			}
			$xtpl->assign( 'LOOP', $loop );		 
			$xtpl->parse( 'load_comment.loop' );
		}
	}
	$xtpl->parse( 'load_comment' );
	return $xtpl->text( 'load_comment' );  
}

function ThemeOnlineTestRecharge( $dataContent )
{
	global $global_config, $module_name, $module_file, $lang_module, $module_config, $module_info;
	$xtpl = new XTemplate( 'ThemeOnlineTestRecharge.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'LINKSITE', NV_BASE_SITEURL . 'themes/' . $module_info['template'] );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'MODULE_FILE', $module_file );
	$xtpl->assign( 'TOKEN', md5( session_id() . $global_config['sitekey'] ) );
	
	if( ! empty( $dataContent ) )
	{
		$stt = 1;
		foreach( $dataContent as $key => $item )
		{
			
			$item['stt'] = $stt;
			$item['date_added'] = date('d/m/Y', $item['date_added'] );
 
			$xtpl->assign( 'LOOP', $item );
			$xtpl->parse( 'main.data.loop' );
			++$stt;

		}
		$xtpl->parse( 'main.data' ); 
	}

	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );

}

function ThemeOnlineTestHistory( $dataContent, $data, $generate_page  )
{
	global $global_config, $op, $nv_Request, $onlineTestConfig, $module_name, $module_data,  $module_file, $lang_module, $module_config, $module_info;
 
	$xtpl = new XTemplate( 'ThemeOnlineTestHistory.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'NV_LANG_VARIABLE', NV_LANG_VARIABLE );
	$xtpl->assign( 'NV_LANG_DATA', NV_LANG_DATA );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
	$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
	$xtpl->assign( 'OP', $op );
	$xtpl->assign( 'MODULE_FILE', $module_file );
	$xtpl->assign( 'MODULE_NAME', $module_name );
	$xtpl->assign( 'TOKEN', md5( $nv_Request->session_id . $global_config['sitekey'] ) );

	$xtpl->assign( 'DATA', $data );

	$order2 = ( $data['order'] == 'asc' ) ? 'desc' : 'asc';

	$xtpl->assign( 'URL_TITLE', NV_BASE_SITEURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=tx.title&amp;order=' . $order2 . '&amp;per_page=' . $onlineTestConfig['perpage'] );
	$xtpl->assign( 'URL_CODE', NV_BASE_SITEURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=tx.code&amp;order=' . $order2 . '&amp;per_page=' . $onlineTestConfig['perpage'] );
	$xtpl->assign( 'URL_USERNAME', NV_BASE_SITEURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=u.username&amp;order=' . $order2 . '&amp;per_page=' . $onlineTestConfig['perpage'] );
	$xtpl->assign( 'URL_SCORE', NV_BASE_SITEURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=h.score&amp;order=' . $order2 . '&amp;per_page=' . $onlineTestConfig['perpage'] );
	$xtpl->assign( 'URL_TEST_TIME', NV_BASE_SITEURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=h.test_time&amp;order=' . $order2 . '&amp;per_page=' . $onlineTestConfig['perpage'] );
	 
	$xtpl->assign( 'TITLE_ORDER', ( $data['sort'] == 'title' ) ? 'class="' . $order2 . '"' : '' );
	$xtpl->assign( 'CODE_ORDER', ( $data['sort'] == 'code' ) ? 'class="' . $order2 . '"' : '' );
	$xtpl->assign( 'USERNAME_ORDER', ( $data['sort'] == 'username' ) ? 'class="' . $order2 . '"' : '' );
	$xtpl->assign( 'SCORE_ORDER', ( $data['sort'] == 'score' ) ? 'class="' . $order2 . '"' : '' );
	$xtpl->assign( 'TEST_TIME', ( $data['sort'] == 'test_time' ) ? 'class="' . $order2 . '"' : '' );
	 
	if( $nv_Request->get_string( $module_data . '_success', 'session' ) )
	{
		$xtpl->assign( 'SUCCESS', $nv_Request->get_string( $module_data . '_success', 'session' ) );

		$xtpl->parse( 'main.success' );

		$nv_Request->unset_request( $module_data . '_success', 'session' );

	}
 

	if( ! empty( $dataContent ) )
	{
 
		foreach( $dataContent as $item )
		{
	 
			 
			$item['testtime'] = nv_date('d/m/Y H:i:s', $item['test_time']);
			$item['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $item['history_id'] );
			$item['view'] = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $item['history_alias'] . '/' . strtolower( change_alias( $item['title'] ) ) . $global_config['rewrite_exturl'], true );
			$item['continue'] = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . strtolower( change_alias( $item['title'] ) ) . '-' . $item['type_exam_id'] . $global_config['rewrite_exturl'], true );
			
			$item['score'] = number_format((float)$item['score'], 1, '.', '') . '/' . $item['max_score'];
			$xtpl->assign( 'LOOP', $item );
 
			if( $item['is_sended'] == 1 )
			{
				$xtpl->parse( 'main.loop.view' );
				$xtpl->parse( 'main.loop.score' );
			}
			else
			{
				
				if( ( $item['test_time'] + ( $item['time'] * 60 ) ) > NV_CURRENTTIME ) 
				{
					$xtpl->parse( 'main.loop.testing1' );
				}
				else
				{
					$xtpl->parse( 'main.loop.score' );
				}
				
				$xtpl->parse( 'main.loop.testing' );
			}
			
			$xtpl->parse( 'main.loop' );
			 
		}

	}

	if( ! empty( $generate_page ) )
	{
		$xtpl->assign( 'GENERATE_PAGE', $generate_page );
		$xtpl->parse( 'main.generate_page' );
	}
 
	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );

}

function ThemeOnlineTestHistoryEssay( $dataContent, $data, $generate_page  )
{
	global $global_config, $op, $nv_Request, $onlineTestConfig, $module_name, $module_data,  $module_file, $lang_module, $module_config, $module_info;
 
	$xtpl = new XTemplate( 'ThemeOnlineTestHistoryEssay.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'NV_LANG_VARIABLE', NV_LANG_VARIABLE );
	$xtpl->assign( 'NV_LANG_DATA', NV_LANG_DATA );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
	$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
	$xtpl->assign( 'OP', $op );
	$xtpl->assign( 'MODULE_FILE', $module_file );
	$xtpl->assign( 'MODULE_NAME', $module_name );
	$xtpl->assign( 'TOKEN', md5( $nv_Request->session_id . $global_config['sitekey'] ) );

	$xtpl->assign( 'DATA', $data );

	$order2 = ( $data['order'] == 'asc' ) ? 'desc' : 'asc';

	$xtpl->assign( 'URL_TITLE', NV_BASE_SITEURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=tx.title&amp;order=' . $order2 . '&amp;per_page=' . $onlineTestConfig['perpage'] );
	$xtpl->assign( 'URL_CODE', NV_BASE_SITEURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=tx.code&amp;order=' . $order2 . '&amp;per_page=' . $onlineTestConfig['perpage'] );
	$xtpl->assign( 'URL_USERNAME', NV_BASE_SITEURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=u.username&amp;order=' . $order2 . '&amp;per_page=' . $onlineTestConfig['perpage'] );
	$xtpl->assign( 'URL_SCORE', NV_BASE_SITEURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=h.score&amp;order=' . $order2 . '&amp;per_page=' . $onlineTestConfig['perpage'] );
	$xtpl->assign( 'URL_TEST_TIME', NV_BASE_SITEURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=h.test_time&amp;order=' . $order2 . '&amp;per_page=' . $onlineTestConfig['perpage'] );
	 
	$xtpl->assign( 'TITLE_ORDER', ( $data['sort'] == 'title' ) ? 'class="' . $order2 . '"' : '' );
	$xtpl->assign( 'CODE_ORDER', ( $data['sort'] == 'code' ) ? 'class="' . $order2 . '"' : '' );
	$xtpl->assign( 'USERNAME_ORDER', ( $data['sort'] == 'username' ) ? 'class="' . $order2 . '"' : '' );
	$xtpl->assign( 'SCORE_ORDER', ( $data['sort'] == 'score' ) ? 'class="' . $order2 . '"' : '' );
	$xtpl->assign( 'TEST_TIME', ( $data['sort'] == 'test_time' ) ? 'class="' . $order2 . '"' : '' );
	 
	if( $nv_Request->get_string( $module_data . '_success', 'session' ) )
	{
		$xtpl->assign( 'SUCCESS', $nv_Request->get_string( $module_data . '_success', 'session' ) );

		$xtpl->parse( 'main.success' );

		$nv_Request->unset_request( $module_data . '_success', 'session' );

	}
 

	if( ! empty( $dataContent ) )
	{
 
		foreach( $dataContent as $item )
		{
	 
			 
			$item['testtime'] = nv_date('d/m/Y H:i:s', $item['test_time']);
			$item['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $item['history_id'] );
			$item['view'] = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $item['history_alias'] . '/' . strtolower( change_alias( $item['title'] ) ) . $global_config['rewrite_exturl'], true );
			$item['continue'] = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . strtolower( change_alias( $item['title'] ) ) . '-' . $item['type_exam_id'] . $global_config['rewrite_exturl'], true );
			
			$item['score'] = number_format((float)$item['score'], 1, '.', '') . '/' . $item['max_score'];
			$xtpl->assign( 'LOOP', $item );
 
			if( $item['is_sended'] == 1 )
			{
				$xtpl->parse( 'main.loop.view' );
				$xtpl->parse( 'main.loop.score' );
			}
			else
			{
				
				if( ( $item['test_time'] + ( $item['time'] * 60 ) ) > NV_CURRENTTIME ) 
				{
					$xtpl->parse( 'main.loop.testing1' );
				}
				else
				{
					$xtpl->parse( 'main.loop.score' );
				}
				
				$xtpl->parse( 'main.loop.testing' );
			}
			
			$xtpl->parse( 'main.loop' );
			 
		}

	}

	if( ! empty( $generate_page ) )
	{
		$xtpl->assign( 'GENERATE_PAGE', $generate_page );
		$xtpl->parse( 'main.generate_page' );
	}
 
	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );

}

function ThemeOnlineTestHistoryView( $dataContent, $dataQuestion )
{
	global $global_config, $op, $nv_Request, $onlineTestConfig, $module_name, $module_data,  $module_file, $lang_module, $module_config, $module_info;
 
	$xtpl = new XTemplate( 'ThemeOnlineTestHistoryView.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'NV_LANG_VARIABLE', NV_LANG_VARIABLE );
	$xtpl->assign( 'NV_LANG_DATA', NV_LANG_DATA );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
	$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
	$xtpl->assign( 'OP', $op );
	$xtpl->assign( 'MODULE_FILE', $module_file );
	$xtpl->assign( 'MODULE_NAME', $module_name );
	$xtpl->assign( 'TOKEN', md5( $nv_Request->session_id . $global_config['sitekey'] ) );
	
	$dataContent['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] .$dataContent['history_id'] );
	$xtpl->assign( 'PRINT', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&action=print&history_id=' . $dataContent['history_id'] . '&token=' . $dataContent['token']);
			
	$xtpl->assign( 'BACK', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
	
	
	
	$questionTest = $dataContent['question'];
	$number_error = 0;
	$number_success = 0;
	if( $questionTest )
	{
		$stt = 1;
		foreach( $questionTest as $question_id => $item )
		{ 
			$item['question_id'] = $question_id;
			$item['token'] =  md5( $nv_Request->session_id . $global_config['sitekey'] . $question_id );
			$item['stt'] = $stt;
			foreach( $item['sys_answers'] as $key => $sys_answers )
			{
				if( isset( $item['user_answers'] ) && in_array( $key, $item['user_answers'] ) )
				{
					$checked_class = 'checked';
					$checked = 'checked="checked"';
					 
				}else{
					$checked_class = '';
					$checked = '';
				}
 
				$xtpl->assign( 'ANSWERS', 
				array(
					'key'=> $key, 
					'name'=> $sys_answers, 
					'checked'=> $checked, 
					'checked_class'=> $checked_class,
					'trueanswer'=> in_array( $key, $item['trueanswer'] ) ? 'trueanswer' : 'wrong' 
				) );
				$xtpl->parse( 'main.loop.answers' );
				
			}
			
			$item['comment'] = isset( $dataQuestion[$question_id] ) ? $dataQuestion[$question_id]['comment'] : 0;
			$item['analyzes'] = isset( $dataQuestion[$question_id] ) ? $dataQuestion[$question_id]['analyzes'] : '';
			
			if( isset( $item['user_answers'] ) && arrayEqual( $item['trueanswer'], $item['user_answers'] ) )
			{
				++$number_success; 
			}else{
				++$number_error;
			}	
			
			if( defined( 'NV_IS_USER' ) )
			{
				
				$xtpl->assign( 'LOGIN',  '' );
				
				$xtpl->assign( 'ANALYZES', isset( $dataQuestion[$question_id] ) ? $dataQuestion[$question_id]['analyzes'] : '' );
				$xtpl->parse( 'main.loop.view_analyzes2' );
			}else{
				$xtpl->assign( 'LOGIN',  $lang_module['error_login_comment'] );
			}
			
			$xtpl->assign( 'LOOP', $item );
			$xtpl->parse( 'main.loop' );
			++$stt;
		}
	}
	
	$dataContent['date_added'] = nv_date('d/m/Y H:i:s', $dataContent['date_added']);	
	$dataContent['time_do_test'] = gmdate('H:i:s', $dataContent['time_do_test']);	
	$dataContent['number_success'] = $number_success;	
	$dataContent['number_error'] = $number_error;	
	$dataContent['token'] =  md5( $nv_Request->session_id . $global_config['sitekey'] . $dataContent['history_id'] );
	
	$xtpl->assign( 'DATA', $dataContent );
 
 
	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );

}

function ThemeOnlineTestHistoryPrint( $dataContent, $dataQuestion )
{
	global $global_config, $op, $nv_Request, $onlineTestConfig, $module_name, $module_data,  $module_file, $lang_module, $module_config, $module_info;
 
	$xtpl = new XTemplate( 'ThemeOnlineTestHistoryPrint.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'NV_LANG_VARIABLE', NV_LANG_VARIABLE );
	$xtpl->assign( 'NV_LANG_DATA', NV_LANG_DATA );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
	$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
	$xtpl->assign( 'OP', $op );
	$xtpl->assign( 'MODULE_FILE', $module_file );
	$xtpl->assign( 'MODULE_NAME', $module_name );
	$xtpl->assign( 'TOKEN', md5( $nv_Request->session_id . $global_config['sitekey'] ) );
	
	$xtpl->assign( 'BACK', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );

	$questionTest = $dataContent['question'];
	$number_error = 0;
	$number_success = 0;
	if( $questionTest )
	{
		$stt = 1;
		foreach( $questionTest as $question_id => $item )
		{ 
			$item['question_id'] = $question_id;
			$item['token'] =  md5( $nv_Request->session_id . $global_config['sitekey'] . $question_id );
			$item['stt'] = $stt;
			foreach( $item['sys_answers'] as $key => $sys_answers )
			{
				if( isset( $item['user_answers'] ) && in_array( $key, $item['user_answers'] ) )
				{
					$checked_class = 'checked';
					$checked = 'checked="checked"';
					 
				}elseif( in_array( $key, $item['trueanswer'] ) )
				{
					$checked_class = 'checked';
					$checked = 'checked="checked"';
				}else{
					$checked_class = '';
					$checked = '';
				}
 
				$xtpl->assign( 'ANSWERS', 
				array(
					'key'=> $key, 
					'name'=> $sys_answers, 
					'checked'=> $checked, 
					'checked_class'=> $checked_class,
					'trueanswer'=> in_array( $key, $item['trueanswer'] ) ? 'trueanswer' : 'wrong' 
				) );
				$xtpl->parse( 'main.loop.answers' );
				
			}
			
			$item['comment'] = isset( $dataQuestion[$question_id] ) ? $dataQuestion[$question_id]['comment'] : 0;
			$item['analyzes'] = isset( $dataQuestion[$question_id] ) ? $dataQuestion[$question_id]['analyzes'] : '';
			
			if( isset( $item['user_answers'] ) && arrayEqual( $item['trueanswer'], $item['user_answers'] ) )
			{
				++$number_success; 
			}else{
				++$number_error;
			}	
			
			if( defined( 'NV_IS_USER' ) )
			{
				
				$xtpl->assign( 'LOGIN',  '' );
				
				$xtpl->assign( 'ANALYZES', isset( $dataQuestion[$question_id] ) ? $dataQuestion[$question_id]['analyzes'] : '' );
				$xtpl->parse( 'main.loop.view_analyzes2' );
			}else{
				$xtpl->assign( 'LOGIN',  $lang_module['error_login_comment'] );
			}
			
			$xtpl->assign( 'LOOP', $item );
			$xtpl->parse( 'main.loop' );
			++$stt;
		}
	}
	
	$dataContent['date_added'] = nv_date('d/m/Y H:i:s', $dataContent['date_added']);	
	$dataContent['time_do_test'] = gmdate('H:i:s', $dataContent['time_do_test']);	
	$dataContent['number_success'] = $number_success;	
	$dataContent['number_error'] = $number_error;	
	$dataContent['token'] =  md5( $nv_Request->session_id . $global_config['sitekey'] . $dataContent['history_id'] );
	
	$xtpl->assign( 'DATA', $dataContent );
 
 
	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );

}

function ThemeOnlineTestShareEssay( $dataContent, $dataQuestion )
{
	global $global_config, $op, $client_info, $onlineTestTitleFirst, $module_upload, $nv_Request, $page_title, $description, $onlineTestConfig, $module_name, $module_data,  $module_file, $lang_module, $module_config, $module_info;
 
	$xtpl = new XTemplate( 'ThemeOnlineTestShareEssay.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'NV_LANG_VARIABLE', NV_LANG_VARIABLE );
	$xtpl->assign( 'NV_LANG_DATA', NV_LANG_DATA );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
	$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
	$xtpl->assign( 'OP', $op );
	$xtpl->assign( 'MODULE_FILE', $module_file );
	$xtpl->assign( 'MODULE_NAME', $module_name );
	$xtpl->assign( 'SELFURL', $client_info['selfurl'] );
	$xtpl->assign( 'TOKEN', md5( $nv_Request->session_id . $global_config['sitekey'] ) );
	
	$xtpl->assign( 'BACK', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
 
	$dataContent['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $dataContent['history_essay_id'] );
	$dataContent['essay_exam_token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $dataContent['essay_exam_id'] );
	$dataContent['time_do_test'] = str_pad( ceil($dataContent['time_do_test']/60), 2, '0', STR_PAD_LEFT );	
	$dataContent['score'] =  round($dataContent['score'], 1);
 	
	$xtpl->assign( 'DATA', $dataContent );
	 
	if( !empty( $dataQuestion )	)
	{
		$stt = 1;
		foreach( $dataQuestion as $essay_id => $loop  )
		{
			$loop['stt'] = $stt;
			$loop['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $essay_id );

			$xtpl->assign( 'LOOP', $loop );
			$xtpl->parse( 'main.loop' );
			++$stt;

		}
	} 
	
	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );

}

function ThemeOnlineTestShare( $dataContent, $dataQuestion, $is_print = false)
{
	global $global_config, $op, $client_info, $onlineTestTitleFirst, $module_upload, $nv_Request, $page_title, $description, $onlineTestConfig, $module_name, $module_data,  $module_file, $lang_module, $module_config, $module_info;
	
	// if($is_print){
		// $xtpl = new XTemplate( 'ThemeOnlineTestShare.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	// }
	// else{
		$xtpl = new XTemplate( 'ThemeOnlineTestShare.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	// }
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'NV_LANG_VARIABLE', NV_LANG_VARIABLE );
	$xtpl->assign( 'NV_LANG_DATA', NV_LANG_DATA );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
	$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
	$xtpl->assign( 'OP', $op );
	$xtpl->assign( 'MODULE_FILE', $module_file );
	$xtpl->assign( 'MODULE_NAME', $module_name );
	$xtpl->assign( 'SELFURL', $client_info['selfurl'] );
	$xtpl->assign( 'TOKEN', md5( $nv_Request->session_id . $global_config['sitekey'] ) );
	
	$xtpl->assign( 'BACK', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
	
	if($is_print){
		$xtpl->parse( 'main.print' );
	}
	
	$questionTest = $dataContent['question'];

	$number_error = 0;
	$number_success = 0;
	$number_notans = 0;
	if( $questionTest )
	{
		if( $dataContent['type_id'] == 0 ||  $dataContent['type_id'] == 1 )
		{
			
			$stt = 1;
			foreach( $questionTest as $question_id => $item )
			{ 
	 
				$item['question'] = $dataQuestion[$question_id]['question'];
				$item['question_id'] = $question_id;
				$item['level_id'] = $dataQuestion[$question_id]['level_id'];
				$item['level'] = $dataQuestion[$question_id]['level'];
				
				$item['token'] =  md5( $nv_Request->session_id . $global_config['sitekey'] . $question_id );
				$item['stt'] = $stt;
				$count = 0;
				$_checked = 0;
				$notenough = 0;
				$checkWrong = 0;
				$listTrue = array();
				foreach( $item['sys_answers'] as $key  )
				{
					$sys_answers = $dataQuestion[$question_id]['answers'];
					
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
					
					if( in_array( $key, $dataQuestion[$question_id]['trueanswer'] ) )
					{
						$listTrue[] = $titleABC;
					}
					
					
					if( !empty( $checked ) && ! in_array( $key, $dataQuestion[$question_id]['trueanswer'] ) ) 
					{
						++$checkWrong;
					}
					if( in_array( $key, $dataQuestion[$question_id]['trueanswer'] ) ) 
					{
						if( empty( $checked ) )
						{
							++$checkWrong;
						}
						
					}
		 
					$xtpl->assign( 'ANSWERS',array(
						'key'=> $key,
						'title'=> $titleABC,	
						'name'=> $sys_answers[$key], 
						'checked'=> $checked, 
						'checked_class'=> $checked_class,
						'trueanswer'=> ( in_array( $key, $dataQuestion[$question_id]['trueanswer'] ) && ( $dataContent['allow_show_answer'] == 1 || $checked ) ) ? 'trueanswer' : 'wrong' 
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
				
				
				
				$item['comment'] = isset( $dataQuestion[$question_id] ) ? $dataQuestion[$question_id]['comment'] : 0;
	 
				if( isset( $item['user_answers'] ) && arrayEqual( $dataQuestion[$question_id]['trueanswer'], $item['user_answers'] ) )
				{
					++$number_success; 
				}else{
					
					if( !empty( $_checked ) )
					{
						++$number_error;
					}
					
				}	
				if( empty( $_checked ) )
				{
					++$number_notans;
				}
				$xtpl->assign( 'LOOP', $item );
				
				if( defined( 'NV_IS_USER' ) )
				{
					
					$xtpl->assign( 'LOGIN',  '' );
					
					$xtpl->assign( 'ANALYZES', isset( $dataQuestion[$question_id] ) ? $dataQuestion[$question_id]['analyzes'] : '' );
					$xtpl->parse( 'main.loop.view_analyzes2' );
				}else{
					$xtpl->assign( 'LOGIN',  $lang_module['error_login_comment'] );
				}
				
				
				
				$xtpl->assign( 'NUM', str_pad( $stt, 2, '0', STR_PAD_LEFT ) ); 
				$xtpl->assign( 'QUESTION_ID',  $question_id );
				$xtpl->assign( 'CLASS',  $class );
				$xtpl->parse( 'main.type.loop_num_question' );
				
				
				$xtpl->parse( 'main.loop' );
				++$stt;
			}
			$xtpl->parse( 'main.type' );
		}
		else 
		{
			
 			foreach( $questionTest as $question_id => $item )
			{ 
 
 				$_checked = 0;
 				foreach( $item['sys_answers'] as $key  )
				{
					
					if( isset( $item['user_answers'] ) && in_array( $key, $item['user_answers'] ) )
					{
						++$_checked;
					} 
 
				}
 
				if( isset( $item['user_answers'] ) && arrayEqual( $dataQuestion[$question_id]['trueanswer'], $item['user_answers'] ) )
				{
					++$number_success; 
				}else
				{
					
					if( !empty( $_checked ) )
					{
						++$number_error;
					}
					
				}	
				if( empty( $_checked ) )
				{
					++$number_notans;
				}
  
 
			}
			
			if( !nv_is_url( $dataContent['pdf'] ) )
			{
				$dataContent['pdf'] =  NV_MY_DOMAIN . NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $dataContent['pdf'];
			}
			
			if( ! preg_match('/drive.google.com/siu', $dataContent['pdf'] ) )
			{
				$pdfview = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=viewfile', true ) . '?url=' . nv_redirect_encrypt( $dataContent['pdf'] ) ;
				
			}
			else
			{
				$pdfview = $dataContent['pdf'];
			}
			$xtpl->assign( 'PDFVIEW',  $pdfview);
			$xtpl->parse( 'main.pdf' );
			
		}
		
		
	}
	
	// $dataContent['date_added'] = nv_date('d/m/Y H:i:s', $dataContent['date_added']);	
	// $dataContent['time_do_test'] = gmdate('H:i:s', $dataContent['time_do_test']);	
	$dataContent['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $dataContent['history_id'] );
	$dataContent['type_exam_token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $dataContent['type_exam_id'] );
	$dataContent['time_do_test'] = str_pad( ceil($dataContent['time_do_test']/60), 2, '0', STR_PAD_LEFT );	
	$dataContent['number_success'] = $number_success;	
	$dataContent['number_error'] = $number_error;	
	$dataContent['number_notans'] = $number_notans;	
	$dataContent['number_work'] = $number_success + $number_error;	
	$dataContent['score'] =  round($dataContent['score'], 1);
	
	//print_r($dataContent); die;
	
	if($dataContent['score'] > 50){
		
		$thilai = 	$xtpl->assign( 'thilai', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '/dotest/'. $dataContent['alias'] .'-' . $dataContent['type_exam_id'] );
		
		 $xtpl->assign('URL_PRINT', $dataContent['url_print']);

		$xtpl->parse( 'main.hoanthanh' );
	}else{
		
		$thilai = 	$xtpl->assign( 'thilai', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '/dotest/'. $dataContent['alias'] .'-' . $dataContent['type_exam_id'] );
		
		$xtpl->parse( 'main.thilai' );
	}
 	
	$xtpl->assign( 'DATA', $dataContent );
	

	if( !empty( $dataContent['video'] ) || !empty( $dataContent['analyzed'] ) || $dataContent['allow_download'] == 1)
	{
		
		if( !empty( $dataContent['video'] ) && $dataContent['allow_video'] == 1 && $dataContent['is_sended'] == 1 )
		{
			
			$getYoutubeId = getYoutubeId($dataContent['video']);
			if( $getYoutubeId )
			{
				$dataContent['video'] = $getYoutubeId;
				$dataContent['video_type'] = 'youtube';
			}
			else
			{
				if( ! empty( $dataContent['video'] ) and file_exists( NV_UPLOADS_REAL_DIR . '/' . $module_upload . '/' . $dataContent['video'] ) )
				{
					$dataContent['video'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $dataContent['video'];
				}
				$dataContent['video_type'] = '';
			}
			
			if( ! empty( $dataContent['images'] ) and file_exists( NV_UPLOADS_REAL_DIR . '/' . $module_upload . '/' . $dataContent['images'] ) )
			{
				$dataContent['images'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $dataContent['images'];
			}
			$xtpl->assign( 'IMAGES',  $dataContent['images']);
			$xtpl->assign( 'VIDEO',  $dataContent['video']);
			$xtpl->assign( 'VIDEO_TYPE',  $dataContent['video_type']);
			$xtpl->parse( 'main.config.allow_video' );
		}
		
		if( !empty( $dataContent['analyzed'] ) && $dataContent['allow_show_answer'] == 1 && $dataContent['is_sended'] == 1)
		{
			
			if( ! nv_is_url( $dataContent['analyzed'] ) )
			{
				$analyzed = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=download', true ) . '?action=analyzed&type_exam_id=' . $dataContent['type_exam_id'] . '&token=' . $dataContent['type_exam_token'];
			
			}
			else
			{
				$analyzed = $dataContent['analyzed'];
			}
			$xtpl->assign( 'ANALYZED',  $analyzed);

			$xtpl->parse( 'main.config.allow_show_answer' );
		}

		if( $dataContent['allow_download'] == 1 && $dataContent['is_sended'] == 1 )
		{
			$xtpl->parse( 'main.config.allow_download' );
		}
		
		$xtpl->parse( 'main.config' );
 
		
	}
 
	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );

}

function ThemeOnlineTestRechargeHistory( $dataContent, $generate_page )
{
	global $global_config, $module_name, $module_file, $lang_module, $module_config, $module_info;
	$xtpl = new XTemplate( 'ThemeOnlineTestRechargeHistory.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'LINKSITE', NV_BASE_SITEURL . 'themes/' . $module_info['template'] );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'MODULE_FILE', $module_file );
	$xtpl->assign( 'TOKEN', md5( session_id() . $global_config['sitekey'] ) );
	
	if( ! empty( $dataContent ) )
	{
		$stt = 1;
		foreach( $dataContent as $key => $item )
		{
			
			$item['stt'] = $stt;
			$item['date_added'] = date('d/m/Y', $item['date_added'] );
 
			$xtpl->assign( 'LOOP', $item );
			$xtpl->parse( 'main.data.loop' );
			++$stt;

		}
		
		if( $generate_page )
		{
			$xtpl->assign( 'GENERATE_PAGE', $generate_page );
			$xtpl->parse( 'main.data.generate_page' );
		}
		
		$xtpl->parse( 'main.data' ); 
	}else
	{
		$xtpl->parse( 'main.no_data' ); 
	}

	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );

}

function ThemeOnlineTestError ( $error )
{
    global $onlineTestConfig, $lang_module, $lang_global, $module_info, $module_name, $module_file, $global_config, $user_info, $client_info, $op;
    $xtpl = new XTemplate( 'ThemeOnlineTestError.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
    $xtpl->assign( 'LANG', $lang_module );
    $xtpl->assign( 'GLANG', $lang_global );
	
	foreach( $error as $key => $_error )
	{
		$xtpl->assign( 'ERROR', $_error );
		$xtpl->parse( 'main.error' );
	}
	
  
	
	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );  
}

function ThemeOnlineTestErrorPermission ( $permissionKey )
{
    global $onlineTestConfig, $lang_module, $lang_global, $module_info, $module_name, $module_file, $global_config, $user_info, $client_info, $op;
    $xtpl = new XTemplate( 'ThemeOnlineTestErrorPermission.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
    $xtpl->assign( 'LANG', $lang_module );
    $xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'ERROR', $lang_module[$permissionKey] );
	
	 
	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );  
}
 