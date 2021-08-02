<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

if( ACTION_METHOD == 'delete' )
{
	$json = array();
	
	$redirect = $nv_Request->get_int( 'redirect', 'post', 0 );
	
	$history_id = $nv_Request->get_int( 'history_id', 'post', 0 );

	$token = $nv_Request->get_title( 'token', 'post', '', 1 );

	$listid = $nv_Request->get_string( 'listid', 'post', '' );

	if( $listid != '' and md5( $nv_Request->session_id . $global_config['sitekey'] ) == $token )
	{
		$del_array = array_map( 'intval', explode( ',', $listid ) );
	}
	elseif( $token == md5( $nv_Request->session_id . $global_config['sitekey'] . $history_id ) )
	{
		$del_array = array( $history_id );
	}

	if( ! empty( $del_array ) )
	{

		$_del_array = array();

		$a = 0;
		foreach( $del_array as $history_id )
		{
			$result = $db->query( 'DELETE FROM ' . TABLE_ONLINETEST_NAME . '_history WHERE history_id = ' . ( int )$history_id );
			if( $result->rowCount() )
			{
				$json['id'][$a] = $history_id;
				$_del_array[] = $history_id;
				++$a;
			}
		}
		$count = sizeof( $_del_array );

		if( $count )
		{
			nv_insert_logs( NV_LANG_DATA, $module_name, 'log_del_history', implode( ', ', $_del_array ), $admin_info['userid'] );

			$nv_Cache->delMod( $module_name );
			
			$json['success'] = $lang_module['history_delete_success'];			
			if( $redirect )
			{
				$nv_Request->set_Session( $module_data . '_success', $lang_module['history_delete_success'] );

				$json['link'] = NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=history';
			}
		}

	}
	else
	{
		$json['error'] = $lang_module['history_error_security'];
	}

	nv_jsonOutput( $json );
}
elseif( ACTION_METHOD == 'view' )
{
	$history_id = $nv_Request->get_int( 'history_id', 'get', 0 );

	$token = $nv_Request->get_title( 'token', 'get', '', 1 );

	
	$result = $db->query( '
		SELECT h.*, u.username, tx.* FROM ' . TABLE_ONLINETEST_NAME . '_history h 
		LEFT JOIN ' . TABLE_ONLINETEST_NAME . '_type_exam tx ON (h.type_exam_id = tx.type_exam_id) 
		LEFT JOIN ' . NV_USERS_GLOBALTABLE . ' u ON (h.userid = u.userid) 
		WHERE h.history_id=' . intval( $history_id ) );

	$dataContent = $result->fetch();
	
	if( empty( $dataContent ) )
	{
		Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
		die();
	}

	if( $dataContent['type_id'] == 2 )
	{
		$xtpl = new XTemplate( 'history_view_pdf.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
		$xtpl->assign( 'LANG', $lang_module );
		$xtpl->assign( 'GLANG', $lang_global );
		$xtpl->assign( 'NV_LANG_VARIABLE', NV_LANG_VARIABLE );
		$xtpl->assign( 'NV_LANG_DATA', NV_LANG_DATA );
		$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
		$xtpl->assign( 'THEME', $global_config['site_theme'] );
		$xtpl->assign( 'NV_BASE_ADMINURL', NV_BASE_ADMINURL );
		$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
		$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
		$xtpl->assign( 'MODULE_FILE', $module_file );
		$xtpl->assign( 'MODULE_NAME', $module_name );
		$xtpl->assign( 'MODULE_DATA', $module_data );
		$xtpl->assign( 'OP', $op );
	 
		
		$xtpl->assign( 'CANCEL', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
	 
		$questionTest = @unserialize( $dataContent['question'] );
		
		$dataQuestion = @unserialize( $dataContent['config'] );
		
		$listQuestionId = array_keys( $questionTest );
		
		$number_error = 0;
		$number_success = 0;
		$number_notans = 0;
		if( $questionTest )
		{
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
						'trueanswer'=> in_array( $key, $sys_trueanswer ) ? 'trueanswer' : 'wrong' 
					) );
 
					$xtpl->parse( 'main.loop.answers' );
					++$count;
				}

				if( !empty( $checkWrong ) || empty( $_checked ) )
				{
					$error =  empty( $_checked ) ? $lang_module['empty_ans'] : $lang_module['failed'];
					$xtpl->assign( 'LISTTRUE', array( 'error'=> $error, 'ans'=> implode(', ', $listTrue)));
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
				
				$xtpl->parse( 'main.loop' );
				++$stt;
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
		$dataContent['token'] =  md5( $nv_Request->session_id . $global_config['sitekey'] . $dataContent['history_id'] );
		
		$xtpl->assign( 'DATA', $dataContent );
		
		
		if( !nv_is_url( $dataContent['pdf'] ) )
		{
			$dataContent['pdf'] =  NV_MY_DOMAIN . NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $dataContent['pdf'];
		}
		$pdfview = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=viewfile', true ) . '?url=' . nv_redirect_encrypt( $dataContent['pdf'] ) ;

		$xtpl->assign( 'PDFVIEW',  $pdfview);
		$xtpl->parse( 'main.pdf' );
		
		
		
		if( !empty( $dataContent['video'] ) || !empty( $dataContent['analyzed'] ) )
		{
			
			if( !empty( $dataContent['video'] ) )
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
			
			if( !empty( $dataContent['analyzed'] ) )
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
 
			
			$xtpl->parse( 'main.config' );
	 
			
		}
		
		$xtpl->parse( 'main' );
		$contents = $xtpl->text( 'main' );

		include NV_ROOTDIR . '/includes/header.php';
		echo nv_admin_theme( $contents );
		include NV_ROOTDIR . '/includes/footer.php';
	}

	else
	{
		$xtpl = new XTemplate( 'history_view.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
		$xtpl->assign( 'LANG', $lang_module );
		$xtpl->assign( 'GLANG', $lang_global );
		$xtpl->assign( 'NV_LANG_VARIABLE', NV_LANG_VARIABLE );
		$xtpl->assign( 'NV_LANG_DATA', NV_LANG_DATA );
		$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
		$xtpl->assign( 'THEME', $global_config['site_theme'] );
		$xtpl->assign( 'NV_BASE_ADMINURL', NV_BASE_ADMINURL );
		$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
		$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
		$xtpl->assign( 'MODULE_FILE', $module_file );
		$xtpl->assign( 'MODULE_NAME', $module_name );
		$xtpl->assign( 'MODULE_DATA', $module_data );
		$xtpl->assign( 'OP', $op );
	 
		
		$xtpl->assign( 'CANCEL', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
	 
		$questionTest = @unserialize( $dataContent['question'] );
	 
		$listQuestionId = array_keys( $questionTest );
		
		$getLevel = getLevel( $module_name );
		$dataQuestion = array();
		if( $listQuestionId )
		{
			$result = $db_slave->query( 'SELECT * FROM ' . TABLE_ONLINETEST_NAME . '_question WHERE question_id IN ('.implode(',', $listQuestionId ).')');
			
			while( $item = $result->fetch() )
			{
				$item['trueanswer'] = array_map('intval', explode( ',', $item['trueanswer'] ));
				$item['answers'] = unserialize( $item['answers'] );
				$item['level'] = isset( $getLevel[$item['level_id']] ) ? $getLevel[$item['level_id']]['title'] : '';
				$dataQuestion[$item['question_id']] = $item;
			}
			$result->closeCursor();
		}
	 
		$number_error = 0;
		$number_success = 0;
		$number_notans = 0;
		if( $questionTest )
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
			
				foreach( $item['sys_answers'] as $key )
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
						'trueanswer'=> in_array( $key, $dataQuestion[$question_id]['trueanswer'] ) ? 'trueanswer' : 'wrong' 
					) );
				 
					
				 
					
					$xtpl->parse( 'main.loop.answers' );
					++$count;
				}

				if( !empty( $checkWrong ) || empty( $_checked ) )
				{
					$error =  empty( $_checked ) ? $lang_module['empty_ans'] : $lang_module['failed'];
					$xtpl->assign( 'LISTTRUE', array( 'error'=> $error, 'ans'=> implode(', ', $listTrue)));
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
	 
				$xtpl->assign( 'NUM', str_pad( $stt, 2, '0', STR_PAD_LEFT ) ); 
				$xtpl->assign( 'QUESTION_ID',  $question_id );
				$xtpl->assign( 'CLASS',  $class );
				$xtpl->parse( 'main.loop_num_question' );
				
				$xtpl->parse( 'main.loop' );
				++$stt;
			}
			
			
			 
		}
		
		// $dataContent['date_added'] = nv_date('d/m/Y H:i:s', $dataContent['date_added']);	
		// $dataContent['time_do_test'] = gmdate('H:i:s', $dataContent['time_do_test']);	
		$dataContent['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $dataContent['history_id'] );
		$dataContent['time_do_test'] = str_pad( ceil($dataContent['time_do_test']/60), 2, '0', STR_PAD_LEFT );	
		$dataContent['number_success'] = $number_success;	
		$dataContent['number_error'] = $number_error;	
		$dataContent['number_notans'] = $number_notans;	
		$dataContent['number_work'] = $number_success + $number_error;	
		$dataContent['score'] =  round($dataContent['score'], 1);
		$dataContent['token'] =  md5( $nv_Request->session_id . $global_config['sitekey'] . $dataContent['history_id'] );
		
		$xtpl->assign( 'DATA', $dataContent );
	 
		$xtpl->parse( 'main' );
		$contents = $xtpl->text( 'main' );

		include NV_ROOTDIR . '/includes/header.php';
		echo nv_admin_theme( $contents );
		include NV_ROOTDIR . '/includes/footer.php';
	}
	
}

/*show list history*/

$page = $nv_Request->get_int( 'page', 'get', 1 );

$data['title'] = $nv_Request->get_string( 'title', 'get', '' );
$data['code'] = $nv_Request->get_string( 'code', 'get', '' );
$data['username'] = $nv_Request->get_string( 'username', 'get', '' );

$implode = array();

if( $data['title'] )
{
	$implode[] = 'tx.title LIKE \'%' . $db_slave->dblikeescape( $data['title'] ) . '%\'';
}
if( $data['code'] )
{
	$implode[] = 'tx.code LIKE \'%' . $db_slave->dblikeescape( $data['code'] ) . '%\'';
}
if( $data['username'] )
{
	$implode[] = 'u.username LIKE \'%' . $db_slave->dblikeescape( $data['username'] ) . '%\'';
}
 

$sql = TABLE_ONLINETEST_NAME . '_history h 
LEFT JOIN ' . TABLE_ONLINETEST_NAME . '_type_exam tx ON (h.type_exam_id = tx.type_exam_id) 
LEFT JOIN ' . NV_USERS_GLOBALTABLE . ' u ON (h.userid = u.userid)';

if( $implode )
{
	$sql .= ' WHERE ' . implode( ' AND ', $implode );
}

$sort = $nv_Request->get_string( 'sort', 'get', '' );

$order = $nv_Request->get_string( 'order', 'get' ) == 'asc' ? 'asc' : 'desc';

$sort_data = array(
	'u.username',
	'tx.title',
	'tx.code',
	'h.score',
	'h.test_time' );

if( isset( $sort ) && in_array( $sort, $sort_data ) )
{

	$sql .= ' ORDER BY ' . $sort;
}
else
{
	$sql .= ' ORDER BY h.test_time';
}

if( isset( $order ) && ( $order == 'desc' ) )
{
	$sql .= ' DESC';
}
else
{
	$sql .= ' ASC';
}

$num_items = $db->query( 'SELECT COUNT(*) FROM ' . $sql )->fetchColumn();

$base_url = NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=history&amp;sort=' . $sort . '&amp;order=' . $order . '&amp;per_page=' . $onlineTestConfig['perpage'];

$db->sqlreset()->select( 'h.*, u.username, tx.title, tx.code' )->from( $sql )->limit( $onlineTestConfig['perpage'] )->offset( ( $page - 1 ) * $onlineTestConfig['perpage'] );

$result = $db->query( $db->sql() );

$dataContent = array();
while( $rows = $result->fetch() )
{
	$dataContent[] = $rows;
}

$xtpl = new XTemplate( 'history.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
$xtpl->assign( 'LANG', $lang_module );
$xtpl->assign( 'NV_LANG_VARIABLE', NV_LANG_VARIABLE );
$xtpl->assign( 'NV_LANG_DATA', NV_LANG_DATA );
$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
$xtpl->assign( 'THEME', $global_config['site_theme'] );
$xtpl->assign( 'NV_BASE_ADMINURL', NV_BASE_ADMINURL );
$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
$xtpl->assign( 'OP', $op );
$xtpl->assign( 'MODULE_FILE', $module_file );
$xtpl->assign( 'MODULE_NAME', $module_name );
$xtpl->assign( 'TOKEN', md5( $nv_Request->session_id . $global_config['sitekey'] ) );
$xtpl->assign( 'ADD_NEW', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=history&action=add' );

$xtpl->assign( 'DATA', $data );

$order2 = ( $order == 'asc' ) ? 'desc' : 'asc';

$xtpl->assign( 'URL_TITLE', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=tx.title&amp;order=' . $order2 . '&amp;per_page=' . $onlineTestConfig['perpage'] );
$xtpl->assign( 'URL_CODE', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=tx.code&amp;order=' . $order2 . '&amp;per_page=' . $onlineTestConfig['perpage'] );
$xtpl->assign( 'URL_USERNAME', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=u.username&amp;order=' . $order2 . '&amp;per_page=' . $onlineTestConfig['perpage'] );
$xtpl->assign( 'URL_SCORE', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=h.score&amp;order=' . $order2 . '&amp;per_page=' . $onlineTestConfig['perpage'] );
$xtpl->assign( 'URL_TEST_TIME', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=h.test_time&amp;order=' . $order2 . '&amp;per_page=' . $onlineTestConfig['perpage'] );
 
$xtpl->assign( 'TITLE_ORDER', ( $sort == 'title' ) ? 'class="' . $order2 . '"' : '' );
$xtpl->assign( 'CODE_ORDER', ( $sort == 'code' ) ? 'class="' . $order2 . '"' : '' );
$xtpl->assign( 'USERNAME_ORDER', ( $sort == 'username' ) ? 'class="' . $order2 . '"' : '' );
$xtpl->assign( 'SCORE_ORDER', ( $sort == 'score' ) ? 'class="' . $order2 . '"' : '' );
$xtpl->assign( 'TEST_TIME', ( $sort == 'test_time' ) ? 'class="' . $order2 . '"' : '' );
 
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
		
		if( ( $item['test_time'] + ( $item['time'] * 60 ) ) <= NV_CURRENTTIME && $item['is_sended'] == 0 ) 
		{
			$item['is_sended'] = 1;	
			if( $item['time_do_test'] > ( $item['time'] * 60 ) )
			{
				$item['time_do_test'] = ( $item['time'] * 60 );
			}
			$db->exec( 'UPDATE ' . TABLE_ONLINETEST_NAME . '_history SET is_sended=1, time_do_test=' . intval( $item['time_do_test'] )  . ' WHERE history_id=' . intval( $item['history_id'] ) );
	 
			 
		}
		
 
		$item['test_time'] = nv_date('d/m/Y H:i:s', $item['test_time']);
		$item['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $item['history_id'] );
		$item['view'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&action=view&history_id=' . $item['history_id'] . '&token=' . $item['token'];
		
		
		
		$xtpl->assign( 'LOOP', $item );
		
		if( $item['title'] )
		{
			$xtpl->parse( 'main.loop.title' );
		
		}else
		{
			$xtpl->parse( 'main.loop.notitle' );
		}
		
		
		if( $item['is_deleted'] == 0 && $item['is_sended'] == 0 )
		{
			$xtpl->parse( 'main.loop.is_sended0' );
		
		}
		elseif( $item['is_deleted'] == 0 )
		{
			$xtpl->parse( 'main.loop.is_deleted0' );
		
		}
		else
		{
			$xtpl->parse( 'main.loop.is_deleted1' );
		}
		
		if( $item['code'] )
		{
			$xtpl->parse( 'main.loop.code' );
		
		}else
		{
			$xtpl->parse( 'main.loop.nocode' );
		}
		
		if( $item['username'] )
		{
			$xtpl->parse( 'main.loop.username' );
		
		}else
		{
			$xtpl->parse( 'main.loop.nousername' );
		}	
		if( $item['title'] && $item['code'] && $item['username'] )
		{
			$xtpl->parse( 'main.loop.view' );
		}
		else
		{
			$xtpl->parse( 'main.loop.noview' );
		}

		$xtpl->parse( 'main.loop' );
	}

}

$generate_page = nv_generate_page( $base_url, $num_items, $onlineTestConfig['perpage'], $page );
if( ! empty( $generate_page ) )
{
	$xtpl->assign( 'GENERATE_PAGE', $generate_page );
	$xtpl->parse( 'main.generate_page' );
}

$xtpl->parse( 'main' );
$contents = $xtpl->text( 'main' );

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';
