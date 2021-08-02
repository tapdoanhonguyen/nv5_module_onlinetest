<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */

if( ! defined( 'NV_IS_MOD_TEST' ) ) die( 'Stop!!!' );


$globalUserid = defined( 'NV_IS_USER' ) ? $user_info['userid'] : 0;

if( ! $globalUserid )
{
	$link_redirect = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=users&' . NV_OP_VARIABLE . '=login&&nv_redirect=' . nv_redirect_encrypt( $client_info['selfurl'] );

	Header( 'Location: ' . $link_redirect );
	exit();
}

if( ! hasPermission( 'view' ) )
{
	$contents = ThemeOnlineTestErrorPermission( 'error_permission_view' );
	include NV_ROOTDIR . '/includes/header.php';
	echo nv_site_theme( $contents );
	include NV_ROOTDIR . '/includes/footer.php';
}

if( ! hasPermission( 'view' ) )
{
	$contents = ThemeOnlineTestErrorPermission( 'error_permission_view' );
	include NV_ROOTDIR . '/includes/header.php';
	echo nv_site_theme( $contents );
	include NV_ROOTDIR . '/includes/footer.php';
}

if( ACTION_METHOD == 'delete' )
{
	$json = array();

	if( ! hasPermission( 'delete' ) )
	{
		nv_jsonOutput( array( 'error' => $lang_module['error_permission_delete'] ) );
	}

	$question_id = $nv_Request->get_int( 'question_id', 'post', 0 );

	$token = $nv_Request->get_title( 'token', 'post', '', 1 );

	$listid = $nv_Request->get_string( 'listid', 'post', '' );

	if( $listid != '' and md5( $nv_Request->session_id . $global_config['sitekey'] ) == $token )
	{
		$del_array = array_map( 'intval', explode( ',', $listid ) );
	}
	elseif( $token == md5( $nv_Request->session_id . $global_config['sitekey'] . $question_id ) )
	{
		$del_array = array( $question_id );
	}

	if( ! empty( $del_array ) )
	{

		$_del_array = array();

		$a = 0;
		foreach( $del_array as $question_id )
		{
			$result = $db->query( 'DELETE FROM ' . TABLE_ONLINETEST_NAME . '_question WHERE question_id = ' . ( int )$question_id );
			if( $result->rowCount() )
			{
				$db->query( 'DELETE FROM ' . TABLE_ONLINETEST_NAME . '_comment WHERE question_id = ' . ( int )$question_id );

				$json['id'][$a] = $question_id;
				$_del_array[] = $question_id;
				++$a;
			}
		}
		$count = sizeof( $_del_array );

		if( $count )
		{
			nv_insert_logs( NV_LANG_DATA, $module_name, 'log_del_question', implode( ', ', $_del_array ), $user_info['userid'] );

			$nv_Cache->delMod( $module_name );

			$json['success'] = $lang_module['question_delete_success'];
		}

	}
	else
	{
		$json['error'] = $lang_module['question_error_security'];
	}

	nv_jsonOutput( $json );
}
elseif( ACTION_METHOD == 'add' || ACTION_METHOD == 'edit' )
{
	$page_title =  $lang_module['contribute'];
	
	if( ! hasPermission( 'create' ) )
	{
		$contents = ThemeOnlineTestErrorPermission( 'error_permission_create' );
		include NV_ROOTDIR . '/includes/header.php';
		echo nv_site_theme( $contents );
		include NV_ROOTDIR . '/includes/footer.php';
	}

	if( ! nv_function_exists( 'getEditor' ) and file_exists( NV_ROOTDIR . '/' . NV_EDITORSDIR . '/ckeditor/ckeditor.js' ) )
	{
 		$my_head .= '<script type="text/javascript" src="' . NV_BASE_SITEURL . NV_EDITORSDIR . '/ckeditor/ckeditor.js"></script>';

		function getEditor( $textareaname, $val = '', $width = '100%', $height = '450px'  )
		{
			global $module_data, $admin_info, $module_upload ;
			$return = '<textarea style="width: ' . $width . '; height:' . $height . ';" id="' . $module_data . '_' . $textareaname . '" name="' . $textareaname . '">' . $val . '</textarea>';
			$return .= "<script type=\"text/javascript\">
				CKEDITOR.replace( '" . $module_data . "_" . $textareaname . "',{ 
				width: '" . $width . "',
				height: '" . $height . "',
				toolbarGroups:[
					{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
					{ name: 'forms', groups: [ 'forms' ] },
					{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
					{ name: 'links', groups: [ 'links' ] },
					{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'paragraph' ] },
					{ name: 'insert', groups: [ 'insert' ] },
					{ name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
					{ name: 'styles', groups: [ 'styles' ] },
					{ name: 'colors', groups: [ 'colors' ] },
					{ name: 'tools', groups: [ 'tools' ] },
					{ name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
					{ name: 'others', groups: [ 'others' ] },
					{ name: 'about', groups: [ 'about' ] }
				],
				removePlugins: 'autosave,gg,switchbar',
				removeButtons: 'Templates,Googledocs,NewPage,Preview,Print,Save,Find,Replace,SelectAll,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Blockquote,Flash,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe,Maximize,About,Anchor,BidiRtl,CreateDiv,Indent,BulletedList,NumberedList,Outdent,ShowBlocks,Youtube,Video',"; 
			if( defined( 'NV_IS_ADMIN' ) )
			{
				if( empty( $path ) and empty( $currentpath ) )
				{
					$path = NV_UPLOADS_DIR;
					$currentpath = NV_UPLOADS_DIR;

					if( ! empty( $module_upload ) and file_exists( NV_UPLOADS_REAL_DIR . '/' . $module_upload . '/' . date( "Y_m" ) ) )
					{
						$currentpath = NV_UPLOADS_DIR . '/' . $module_upload . '/' . date( "Y_m" );
						$path = NV_UPLOADS_DIR . '/' . $module_upload;
					}
					elseif( ! empty( $module_upload ) and file_exists( NV_UPLOADS_REAL_DIR . '/' . $module_upload ) )
					{
						$currentpath = NV_UPLOADS_DIR . '/' . $module_upload;
					}
				}

				if( ! empty( $admin_info['allow_files_type'] ) )
				{
					$return .= "filebrowserUploadUrl: '" . NV_BASE_SITEURL . NV_ADMINDIR . "/index.php?" . NV_NAME_VARIABLE . "=upload&" . NV_OP_VARIABLE . "=upload&editor=ckeditor&path=" . $currentpath . "',";
				}

				if( in_array( 'images', $admin_info['allow_files_type'] ) )
				{
					$return .= "filebrowserImageUploadUrl: '" . NV_BASE_SITEURL . NV_ADMINDIR . "/index.php?" . NV_NAME_VARIABLE . "=upload&" . NV_OP_VARIABLE . "=upload&editor=ckeditor&path=" . $currentpath . "&type=image',";
				}

				if( in_array( 'flash', $admin_info['allow_files_type'] ) )
				{
					$return .= "filebrowserFlashUploadUrl: '" . NV_BASE_SITEURL . NV_ADMINDIR . "/index.php?" . NV_NAME_VARIABLE . "=upload&" . NV_OP_VARIABLE . "=upload&editor=ckeditor&path=" . $currentpath . "&type=flash',";
				}
				$return .= "filebrowserBrowseUrl: '" . NV_BASE_SITEURL . NV_ADMINDIR . "/index.php?" . NV_NAME_VARIABLE . "=upload&popup=1&path=" . $path . "&currentpath=" . $currentpath . "',";
				$return .= "filebrowserImageBrowseUrl: '" . NV_BASE_SITEURL . NV_ADMINDIR . "/index.php?" . NV_NAME_VARIABLE . "=upload&popup=1&type=image&path=" . $path . "&currentpath=" . $currentpath . "',";
				$return .= "filebrowserFlashBrowseUrl: '" . NV_BASE_SITEURL . NV_ADMINDIR . "/index.php?" . NV_NAME_VARIABLE . "=upload&popup=1&type=flash&path=" . $path . "&currentpath=" . $currentpath . "'";
 
			}		
			$return .= "	});";
			$return .= "</script>";
			return $return;

		}
	}

	$data = array(
		'question_id' => 0,
		'group_exam_id' => 0,
		'category_id' => 0,
		'level_id' => 0,
		'contribute' => 1,
		'user_id' => $user_info['userid'],
		'user_name' => $user_info['username'],
		'question' => '',
		'analyzes' => '',
		'answers' => array(),
		'trueanswer' => '',
		'date_added' => 0,
		'date_modified' => 0,
		'status' => 0,
		'addAnalyzes' => 0,
		'getcontent' => array(),
	);
	$dataContent = array();
	$error = array();

	$data['question_id'] = $nv_Request->get_int( 'question_id', 'get,post', 0 );
	if( ! hasPermission( 'modified' ) && $data['question_id'] > 0 )
	{
		$contents = ThemeOnlineTestErrorPermission( 'error_permission_modified' );
		include NV_ROOTDIR . '/includes/header.php';
		echo nv_site_theme( $contents );
		include NV_ROOTDIR . '/includes/footer.php';
	}
	if( $data['question_id'] > 0 )
	{
		$data = $db->query( 'SELECT *
		FROM ' . TABLE_ONLINETEST_NAME . '_question 
		WHERE question_id=' . $data['question_id'] )->fetch();

		$trueanswer = explode( ',', $data['trueanswer'] );

		$data['answers'] = unserialize( $data['answers'] );

		foreach( $data['answers'] as $key => $answer )
		{
			$dataContent[$key] = array(
				'answer' => $answer,
				'trueanswer' => ( in_array( $key, $trueanswer ) ) ? 1 : 0,
				);
			$data['getcontent'][$key] = 1;
		}
		$data['addAnalyzes'] = ( !empty( $data['analyzes'] ) ) ? 1 : 0;
		$caption = $lang_module['question_edit'];
	}
	else
	{
		$caption = $lang_module['question_add'];
	}

	if( $nv_Request->get_int( 'save', 'post' ) == 1 )
	{

		$data['category_id'] = $nv_Request->get_int( 'category_id', 'post', 0 );
		$data['group_exam_id'] = $nv_Request->get_int( 'group_exam_id', 'post', 0 );
		$data['level_id'] = $nv_Request->get_int( 'level_id', 'post', 0 );
		//$data['status'] = $nv_Request->get_int( 'status', 'post', 0 );
		$data['question'] = $nv_Request->get_editor( 'question', '', NV_ALLOWED_HTML_TAGS );
		$data['analyzes'] = $nv_Request->get_editor( 'analyzes', '', NV_ALLOWED_HTML_TAGS );
		$getcontent = $nv_Request->get_typed_array( 'getcontent', 'post', array() );
		$data['addAnalyzes'] = $nv_Request->get_int( 'addAnalyzes', 'post', 0 );
 		
		$trueanswer = array();
		$answers = array();

		$newkey = 0;

		foreach( $getcontent as $key => $item )
		{
			++$newkey;
			$_answer = $nv_Request->get_editor( 'answer_' . $key, '', NV_ALLOWED_HTML_TAGS );
			$_trueanswer = $nv_Request->get_int( 'trueanswer_' . $key, 'post', 0 );

			$dataContent[$newkey] = array( 'answer' => $_answer, 'trueanswer' => $_trueanswer );
			$answers[$newkey] = $_answer;
			if( $_trueanswer == 1 )
			{
				$trueanswer[] = $newkey;
			}
		}

		if( empty( $data['group_exam_id'] ) ) $error['group_exam'] = $lang_module['question_error_group_exam_id'];
		if( empty( $data['category_id'] ) ) $error['category'] = $lang_module['question_error_category_id'];
		if( empty( $data['level_id'] ) ) $error['level'] = $lang_module['question_error_level_id'];
		if( trim( strip_tags( $data['question'] ) ) == '' and ! preg_match( "/\<img[^\>]*alt=\"([^\"]+)\"[^\>]*\>/is", $data['question'] ) )
		{
			$error['question'] = $lang_module['question_error_question'];
		}
		if( ! empty( $error ) && ! isset( $error['warning'] ) )
		{
			$error['warning'] = $lang_module['question_error_warning'];
		}

		if( empty( $error ) )
		{

			$data['answers'] = serialize( $answers );

			$data['trueanswer'] = ( $trueanswer ) ? implode( ',', $trueanswer ) : '';

			if( $data['question_id'] == 0 )
			{

				try
				{
					$stmt = $db->prepare( 'INSERT INTO ' . TABLE_ONLINETEST_NAME . '_question SET 
						group_exam_id=' . intval( $data['group_exam_id'] ) . ',
						category_id=' . intval( $data['category_id'] ) . ',
						level_id=' . intval( $data['level_id'] ) . ',
						contribute=' . intval( $data['contribute'] ) . ',
						user_id=' . intval( $data['user_id'] ) . ',
						user_name=:user_name, 
						question=:question, 
						analyzes=:analyzes, 
						answers=:answers, 
						trueanswer=:trueanswer, 
						status=' . intval( $data['status'] ) . ',
						date_added=' . intval( NV_CURRENTTIME ) );

					$stmt->bindParam( ':user_name', $data['user_name'], PDO::PARAM_STR );
					$stmt->bindParam( ':question', $data['question'], PDO::PARAM_STR, strlen( $data['question'] ) );
					$stmt->bindParam( ':analyzes', $data['analyzes'], PDO::PARAM_STR, strlen( $data['analyzes'] ) );
					$stmt->bindParam( ':answers', $data['answers'], PDO::PARAM_STR, strlen( $data['answers'] ) );
					$stmt->bindParam( ':trueanswer', $data['trueanswer'], PDO::PARAM_STR );
					$stmt->execute();

					if( $data['question_id'] = $db->lastInsertId() )
					{
						nv_insert_logs( NV_LANG_DATA, $module_name, 'Add Question', 'question_id: ' . $data['question_id'], $user_info['userid'] );
						
						$bonus_score = vsprintf( $lang_module['bonus_score'], $onlineTestConfig['bonus_score']);
						
						$nv_Request->set_Session( $module_data . '_success', $bonus_score );
										
					}
					else
					{
						$error['warning'] = $lang_module['question_error_save'];

					}
					$stmt->closeCursor();

				}
				catch ( PDOException $e )
				{
					$error['warning'] = $lang_module['question_error_save'];
					//var_dump( $e ); die();
				}

			}
			else
			{
				try
				{

					$stmt = $db->prepare( 'UPDATE ' . TABLE_ONLINETEST_NAME . '_question SET 
							group_exam_id=' . intval( $data['group_exam_id'] ) . ',
							category_id=' . intval( $data['category_id'] ) . ',
							level_id=' . intval( $data['level_id'] ) . ',
							user_id=' . intval( $data['user_id'] ) . ',
							user_name=:user_name, 
							question=:question, 
							analyzes=:analyzes, 
							answers=:answers, 
							trueanswer=:trueanswer, 
							status=0,
							date_modified=' . intval( NV_CURRENTTIME ) . '
							WHERE question_id=' . $data['question_id'] );

					$stmt->bindParam( ':user_name', $data['user_name'], PDO::PARAM_STR );
					$stmt->bindParam( ':question', $data['question'], PDO::PARAM_STR, strlen( $data['question'] ) );
					$stmt->bindParam( ':analyzes', $data['analyzes'], PDO::PARAM_STR, strlen( $data['analyzes'] ) );
					$stmt->bindParam( ':answers', $data['answers'], PDO::PARAM_STR, strlen( $data['answers'] ) );
					$stmt->bindParam( ':trueanswer', $data['trueanswer'], PDO::PARAM_STR );
					$stmt->execute();

					if( $stmt->rowCount() )
					{
						nv_insert_logs( NV_LANG_DATA, $module_name, 'Edit Question: ' . $data['question_id'], $user_info['userid'] );

						$nv_Request->set_Session( $module_data . '_success', $lang_module['question_edit_success'] );

					}
					else
					{
						$error['warning'] = $lang_module['question_error_save'];

					}

					$stmt->closeCursor();

				}
				catch ( PDOException $e )
				{
					$error['warning'] = $lang_module['question_error_save'];
					//var_dump($e);die();
				}

			}

		}

		if( empty( $error ) )
		{

			Header( 'Location: ' . NV_BASE_SITEURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
			die();
		}

	}
	$_question = $data['question'];
	$data['question'] = htmlspecialchars( nv_editor_br2nl( $data['question'] ) );

	if( nv_function_exists( 'getEditor' ) && isHtml( $_question ) )
	{
		$data['question'] = getEditor( 'question', $data['question'], '100%', '100px' );
	}
	else
	{
		$data['question'] = '<textarea style="width: 100%;height:32px" name="question" id="question" cols="20" rows="2" style="height:100px" class="form-control">' . $data['question'] . '</textarea>';
	}
	
	 
	if( $data['addAnalyzes'] )
	{
		$data['analyzes'] = htmlspecialchars( nv_editor_br2nl( $data['analyzes'] ) );
		if( nv_function_exists( 'getEditor' ) )
		{
			$data['analyzes'] = getEditor( 'analyzes', $data['analyzes'], '100%', '100px' );
		}
		else
		{
			$data['analyzes'] = '<textarea style="width: 100%;height:32px" name="analyzes" id="analyzes" cols="20" rows="2" style="height:100px" class="form-control">' . $data['analyzes'] . '</textarea>';
		}
	}
	
	$data['css_hide'] = ( $data['addAnalyzes'] == 1 ) ? 'shows' : 'hides';


	$xtpl = new XTemplate( 'ThemeOnlineTestContributeAdd.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'NV_LANG_VARIABLE', NV_LANG_VARIABLE );
	$xtpl->assign( 'NV_LANG_DATA', NV_LANG_DATA );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
	$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
	$xtpl->assign( 'MODULE_FILE', $module_file );
	$xtpl->assign( 'MODULE_NAME', $module_name );
	$xtpl->assign( 'MODULE_DATA', $module_data );
	$xtpl->assign( 'OP', $op );
	$xtpl->assign( 'CAPTION', $caption );
	$xtpl->assign( 'DATA', $data );
	$xtpl->assign( 'CANCEL', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
	$xtpl->assign( 'TOKEN', md5( $nv_Request->session_id . $global_config['sitekey'] ) );

	
	if( $data['addAnalyzes'] == 1 )
	{
		
		$xtpl->assign( 'ANALYZESS', $data['analyzes'] );
		$xtpl->parse( 'main.analyzes' );
		
	}
	if( $onlineTestGroupExam )
	{
		foreach( $onlineTestGroupExam as $key => $item )
		{
			$xtpl->assign( 'GROUPEXAM', array(
				'key' => $key,
				'name' => $item['title'],
				'selected' => ( $key == $data['group_exam_id'] ) ? 'selected="selected"' : '' ) );
			$xtpl->parse( 'main.group_exam' );
		}
	}
	if( $onlineTestCategory )
	{
		foreach( $onlineTestCategory as $key => $item )
		{
			$xtpl->assign( 'CATEGORY', array(
				'key' => $key,
				'name' => $item['title'],
				'selected' => ( $key == $data['category_id'] ) ? 'selected="selected"' : '' ) );
			$xtpl->parse( 'main.category' );
		}
	}

	$onlineTestLevel = getLevel( $module_name );
	if( $onlineTestLevel )
	{
		foreach( $onlineTestLevel as $key => $item )
		{
			$xtpl->assign( 'LEVEL', array(
				'key' => $key,
				'name' => $item['title'],
				'selected' => ( $key == $data['level_id'] ) ? 'selected="selected"' : '' ) );
			$xtpl->parse( 'main.level' );
		}
	}
	if( $onlineTestStatus )
	{
		foreach( $onlineTestStatus as $key => $item )
		{
			$xtpl->assign( 'STATUS', array(
				'key' => $key,
				'name' => $item,
				'selected' => ( $key == $data['status'] ) ? 'selected="selected"' : '' ) );
			$xtpl->parse( 'main.status' );
		}
	}

	$countAnswers = sizeof( $data['getcontent'] );

	$countAnswers = ( $countAnswers ) ? $countAnswers : 4;
	$answer_row = 1;
	for( $key = 1; $key <= $countAnswers; ++$key )
	{
		$_answer = $answer = isset( $dataContent[$key]['answer'] ) ? $dataContent[$key]['answer'] : '';

		$answer = htmlspecialchars( nv_editor_br2nl( $answer ) );

		if( nv_function_exists( 'getEditor' ) && isHtml( $_answer ) )
		{
			$answer = getEditor( 'answer_' . $key, $answer, '100%', '100px' );

		}
		else
		{
			$answer = '<textarea class="form-control" style="width: 100%;height:32px" name="answer_' . $key . '" id="answer_' . $key . '" cols="20" rows="2" >' . $answer . '</textarea>';
		}

		$xtpl->assign( 'ANSWERS', array(
			'key' => $key,
			'answer' => $answer,
			'trueanswer' => ( isset( $dataContent[$key]['trueanswer'] ) && $dataContent[$key]['trueanswer'] == 1 ) ? 'checked="checked"' : '' ) );

		$xtpl->parse( 'main.loopAnswers' );
		++$answer_row;
	}
	$xtpl->assign( 'answer_row', $answer_row );

	if( isset( $error['warning'] ) )
	{
		$xtpl->assign( 'WARNING', $error['warning'] );
		$xtpl->parse( 'main.error_warning' );
		unset( $error['warning'] );
	}
	if( $error )
	{
		foreach( $error as $key => $_error )
		{
			$xtpl->assign( 'error_' . $key, $_error );
			$xtpl->parse( 'main.error_' . $key );
		}
	}

	$xtpl->parse( 'main' );
	$contents = $xtpl->text( 'main' );

	include NV_ROOTDIR . '/includes/header.php';
	echo nv_site_theme( $contents );
	include NV_ROOTDIR . '/includes/footer.php';
}

/*show list contribute*/
$page_title =  $lang_module['contribute_manager'];
 
$page = $nv_Request->get_int( 'page', 'get', 1 );

$data['question'] = $nv_Request->get_string( 'question', 'get', '' );
$data['category_id'] = $nv_Request->get_int( 'category_id', 'get', 0 );
$data['level_id'] = $nv_Request->get_int( 'level_id', 'get', 0 );
$data['user_name'] = $nv_Request->get_string( 'user_name', 'get' );
$data['status'] = $nv_Request->get_string( 'status', 'get', '' );
$data['date_from'] = $nv_Request->get_title( 'date_from', 'get', '' );
$data['date_to'] = $nv_Request->get_title( 'date_to', 'get', '' );

if( preg_match( '/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $data['date_from'], $m ) )
{

	$date_from = mktime( 0, 0, 0, $m[2], $m[1], $m[3] );
}
else
{
	$date_from = 0;
}
if( preg_match( '/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $data['date_to'], $m ) )
{

	$date_to = mktime( 23, 59, 59, $m[2], $m[1], $m[3] );
}
else
{
	$date_to = 0;
}

$implode = array();

if( $data['question'] )
{
	$implode[] = 'question LIKE \'%' . $db_slave->dblikeescape( $data['question'] ) . '%\'';
}

if( $data['category_id'] )
{
	$implode[] = 'category_id = ' . intval( $data['category_id'] );
}
if( $data['level_id'] )
{
	$implode[] = 'level_id = ' . intval( $data['level_id'] );
}
if( is_numeric( $data['status'] ) )
{
	$implode[] = 'status = ' . intval( $data['status'] );
}

$implode[] = 'user_id = ' . intval( $user_info['userid'] );

if( $date_from && $date_to )
{
	$implode[] = 'date_added BETWEEN ' . intval( $date_from ) . ' AND ' . intval( $date_to );
}

$sql = TABLE_ONLINETEST_NAME . '_question';

if( $implode )
{
	$sql .= ' WHERE ' . implode( ' AND ', $implode );
}

$sort = $nv_Request->get_string( 'sort', 'get', '' );

$order = $nv_Request->get_string( 'order', 'get' ) == 'asc' ? 'asc' : 'desc';

$sort_data = array(
	'question',
	'level_id',
	'date_added',
	'date_modified',
	'status' );

if( isset( $sort ) && in_array( $sort, $sort_data ) )
{

	$sql .= ' ORDER BY ' . $sort;
}
else
{
	$sql .= ' ORDER BY date_added';
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

$base_url = NV_BASE_SITEURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=contribute&amp;sort=' . $sort . '&amp;order=' . $order . '&amp;per_page=' . $onlineTestConfig['perpage'];

$db->sqlreset()->select( '*' )->from( $sql )->limit( $onlineTestConfig['perpage'] )->offset( ( $page - 1 ) * $onlineTestConfig['perpage'] );

$result = $db->query( $db->sql() );

$dataContent = array();
while( $rows = $result->fetch() )
{
	$dataContent[] = $rows;
}

$xtpl = new XTemplate( 'ThemeOnlineTestContribute.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
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
if( hasPermission( 'create' ) )
{
	$xtpl->assign( 'ADD_NEW', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=contribute&action=add' );

	$xtpl->parse( 'main.create' );
}
if( hasPermission( 'delete' ) )
{

	$xtpl->parse( 'main.delete' );
}

$xtpl->assign( 'DATA', $data );

$order2 = ( $order == 'asc' ) ? 'desc' : 'asc';

$xtpl->assign( 'URL_QUESTION', NV_BASE_SITEURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=question&amp;order=' . $order2 . '&amp;per_page=' . $onlineTestConfig['perpage'] );
$xtpl->assign( 'URL_LEVEL', NV_BASE_SITEURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=level_id&amp;order=' . $order2 . '&amp;per_page=' . $onlineTestConfig['perpage'] );
$xtpl->assign( 'URL_STATUS', NV_BASE_SITEURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=status&amp;order=' . $order2 . '&amp;per_page=' . $onlineTestConfig['perpage'] );
$xtpl->assign( 'URL_DATE_ADDED', NV_BASE_SITEURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=date_added&amp;order=' . $order2 . '&amp;per_page=' . $onlineTestConfig['perpage'] );
$xtpl->assign( 'URL_DATE_MODIFIED', NV_BASE_SITEURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=date_modified&amp;order=' . $order2 . '&amp;per_page=' . $onlineTestConfig['perpage'] );

$xtpl->assign( 'QUESTION_ORDER', ( $sort == 'question' ) ? 'class="' . $order2 . '"' : '' );
$xtpl->assign( 'LEVEL_ORDER', ( $sort == 'level_id' ) ? 'class="' . $order2 . '"' : '' );
$xtpl->assign( 'STATUS_ORDER', ( $sort == 'status' ) ? 'class="' . $order2 . '"' : '' );
$xtpl->assign( 'DATE_ADDED_ORDER', ( $sort == 'date_added' ) ? 'class="' . $order2 . '"' : '' );
$xtpl->assign( 'DATE_MODIFIED_ORDER', ( $sort == 'date_modified' ) ? 'class="' . $order2 . '"' : '' );

if( $nv_Request->get_string( $module_data . '_success', 'session' ) )
{
	$xtpl->assign( 'SUCCESS', $nv_Request->get_string( $module_data . '_success', 'session' ) );

	$xtpl->parse( 'main.success' );

	$nv_Request->unset_request( $module_data . '_success', 'session' );

}

if( $onlineTestCategory )
{
	foreach( $onlineTestCategory as $key => $item )
	{
		$xtpl->assign( 'CATEGORY', array(
			'key' => $key,
			'name' => $item['title'],
			'selected' => ( $key == $data['category_id'] ) ? 'selected="selected"' : '' ) );
		$xtpl->parse( 'main.category' );
	}
}

$onlineTestLevel = getLevel( $module_name );
if( $onlineTestLevel )
{
	foreach( $onlineTestLevel as $key => $item )
	{
		$xtpl->assign( 'LEVEL', array(
			'key' => $key,
			'name' => $item['title'],
			'selected' => ( $key == $data['level_id'] ) ? 'selected="selected"' : '' ) );
		$xtpl->parse( 'main.level' );
	}
}

if( $onlineTestStatus )
{
	foreach( $onlineTestStatus as $key => $item )
	{
		$xtpl->assign( 'STATUS', array(
			'key' => $key,
			'name' => $item,
			'selected' => ( $key == $data['status'] && is_numeric( $data['status'] ) ) ? 'selected="selected"' : '' ) );
		$xtpl->parse( 'main.status' );
	}
}

if( ! empty( $dataContent ) )
{

	foreach( $dataContent as $item )
	{
		$item['level_name'] = ( isset( $onlineTestLevel[$item['level_id']] ) ) ? $onlineTestLevel[$item['level_id']]['title'] : '';
		$item['status_checked'] = ( $item['status'] ) ? 'checked="checked"' : '';
		$item['date_added'] = nv_date( 'd/m/Y', $item['date_added'] );
		$item['date_modified'] = nv_date( 'd/m/Y', $item['date_modified'] );
		$item['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $item['question_id'] );
		$item['edit'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&action=edit&token=' . $item['token'] . '&question_id=' . $item['question_id'];
		
		$item['question'] = nv_clean60( strip_tags( $item['question'] ), 120 );
		
		$xtpl->assign( 'LOOP', $item );
		if( hasPermission( 'modified' ) )
		{
			$xtpl->parse( 'main.loop.modified' );
		}
		if( hasPermission( 'delete' ) )
		{
			$xtpl->parse( 'main.loop.delete' );
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
echo nv_site_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';
