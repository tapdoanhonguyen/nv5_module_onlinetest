<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */

if( ! defined( 'NV_IS_MOD_TEST' ) ) die( 'Stop!!!' );

$json = array();
$dataContent = array();
$dataQuestions = array();
$dataDefaultQuestions = array();
$history_id = 0;
$globalUserid = defined( 'NV_IS_USER' ) ? $user_info['userid'] : 0;

if( $onlineTestConfig['open'] == 0 )
{
	nv_jsonOutput( array( 'error' => $lang_module['error_close'] ) );
}

if( ! $globalUserid )
{
	nv_jsonOutput( array( 'error' => $lang_module['error_login'] ) );
}

if( ACTION_METHOD == 'essay' )
{
	$essay_exam_id = $nv_Request->get_int( 'essay_exam_id', 'post', 0 );
	$token = $nv_Request->get_title( 'token', 'post', '', 1 );

	if( $token != md5( $nv_Request->session_id . $global_config['sitekey'] . $essay_exam_id ) )
	{
		nv_jsonOutput( array( 'error' => $lang_module['error_security'] ) );
	}

	$query = $db_slave->query( 'SELECT * FROM ' . TABLE_ONLINETEST_NAME . '_essay_exam WHERE status=1 AND essay_exam_id = ' . intval( $essay_exam_id ) );
	$dataContent = $query->fetch();

	if( empty( $dataContent ) )
	{
		nv_jsonOutput( array( 'error' => $lang_module['error_status_or_notexist'] ) );
	}

	if( $onlineTestConfig['test_limit'] > 0 )
	{
		$testLimit = $db->query( 'SELECT test_limit FROM ' . TABLE_ONLINETEST_NAME . '_limit_essay WHERE userid=' . intval( $globalUserid ) . ' AND essay_exam_id=' . intval( $dataContent['essay_exam_id'] ) )->fetchColumn();

		if( $testLimit >= $onlineTestConfig['test_limit'] )
		{
			nv_jsonOutput( array( 'error' => $lang_module['error_limit'] ) );
		}

	}

	$dataContent['alias'] = strtolower( change_alias( $dataContent['title'] ) );

	$checkHistoryTest = $nv_Request->get_int( 'history_essay_' . $essay_exam_id, 'session' );
	
	
	
	if( ! nv_function_exists( 'getEditor' ) and file_exists( NV_ROOTDIR . '/' . NV_EDITORSDIR . '/ckeditor/ckeditor.js' ) )
	{
		
		function getEditor( $textareaname, $val = '', $width = '100%', $height = '450px', $path, $currentpath  )
		{
			global $module_data, $user_info, $module_upload ;
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
			if( defined( 'NV_IS_USER' ) )
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

				if( ! empty( $user_info['allow_files_type'] ) )
				{
					$return .= "filebrowserUploadUrl: '" . NV_BASE_SITEURL . "index.php?" . NV_NAME_VARIABLE . "=upload&" . NV_OP_VARIABLE . "=upload&editor=ckeditor&path=" . $currentpath . "',";
				}

				if( in_array( 'images', $user_info['allow_files_type'] ) )
				{
					$return .= "filebrowserImageUploadUrl: '" . NV_BASE_SITEURL . "index.php?" . NV_NAME_VARIABLE . "=upload&" . NV_OP_VARIABLE . "=upload&editor=ckeditor&path=" . $currentpath . "&type=image',";
				}

				if( in_array( 'flash', $user_info['allow_files_type'] ) )
				{
					$return .= "filebrowserFlashUploadUrl: '" . NV_BASE_SITEURL  . "index.php?" . NV_NAME_VARIABLE . "=upload&" . NV_OP_VARIABLE . "=upload&editor=ckeditor&path=" . $currentpath . "&type=flash',";
				}
				$return .= "filebrowserBrowseUrl: '" . NV_BASE_SITEURL  . "index.php?" . NV_NAME_VARIABLE . "=upload&popup=1&path=" . $path . "&currentpath=" . $currentpath . "',";
				$return .= "filebrowserImageBrowseUrl: '" . NV_BASE_SITEURL  . "index.php?" . NV_NAME_VARIABLE . "=upload&popup=1&type=image&path=" . $path . "&currentpath=" . $currentpath . "',";
				$return .= "filebrowserFlashBrowseUrl: '" . NV_BASE_SITEURL  . "index.php?" . NV_NAME_VARIABLE . "=upload&popup=1&type=flash&path=" . $path . "&currentpath=" . $currentpath . "'";
 
			}		
			$return .= "	});";
			$return .= "</script>";
			return $return;

		}
	}
	
	
	if( ! empty( $checkHistoryTest ) )
	{
		$history = $db->query( 'SELECT * FROM ' . TABLE_ONLINETEST_NAME . '_history_essay WHERE history_essay_id = ' . intval( $checkHistoryTest ) . ' AND is_deleted=0 AND is_sended=0 AND userid = ' . intval( $globalUserid ) . ' AND essay_exam_id = ' . intval( $essay_exam_id ) )->fetch();

		if( $history && ( $history['test_time'] + ( $history['time'] * 60 ) ) > NV_CURRENTTIME )
		{
			$history_essay = array();
			$result = $db->query( 'SELECT essay_id, question, answer FROM ' . TABLE_ONLINETEST_NAME . '_history_essay_row WHERE history_essay_id = ' . intval( $history['history_essay_id'] ) . ' ORDER BY row_id ASC'  );
			while( $item = $result->fetch() )
			{
				$history_essay[$item['essay_id']] = $item;
			}

			$test_time = NV_CURRENTTIME - $history['time_do_test'];

		
			$i = 1;
			foreach( $history_essay as $essay_id => $data )
			{
				$json['question'][] = array( 'num' => str_pad( $i, 2, '0', STR_PAD_LEFT ), 'essay_id' => $essay_id );
				
				// if( nv_function_exists( 'getEditor' ) )
				// {
					// $data['answer'] = getEditor( 'answers['. $essay_id .'][answer]', $data['answer'], '100%', '400px', '', '' );
				// }
				// else
				// {
					// $data['answer'] = '<textarea style="width: 100%;height:400px" name="answers['. $essay_id .'][answer]" id="answer-'. $essay_id .'" cols="20" rows="10" class="form-control">' . $data['answer'] . '</textarea>';
				// }
				
				 
				$dataQuestions[$essay_id] = array(
					'stt' => $i,
					'essay_id' => $essay_id,
					'token' => md5( $nv_Request->session_id . $global_config['sitekey'] . $essay_id ),
					'question' => $data['question'],
					'answer' => $data['answer'] );
				++$i;
			}

			$json['time_test'] = ( $history['time'] * 60 ) - ( NV_CURRENTTIME - $history['test_time'] );
			$json['time_now'] = $test_time;

			$json['template'] = ThemeOnlineTestGetQuestionsEssay( $dataContent, $dataQuestions, $history['history_essay_id'] );

			 
		}
		else
		{
			$checkHistoryTest = 0;
			$nv_Request->set_Session( 'history_essay_' . $type_exam_id, 0 );
			$db->query( 'UPDATE ' . TABLE_ONLINETEST_NAME . '_history_essay SET is_sended=1 WHERE history_essay_id = ' . intval( $history['history_essay_id'] ) . ' AND userid = ' . intval( $user_info['userid'] ) );
		}
	}

	if( empty( $checkHistoryTest ) )
	{
		$list_answer = array();
		$QuestionAnswers = array();

		$configEexam = unserialize( $dataContent['config'] );

		if( ! empty( $configEexam ) )
		{
			

			$list_question = array();
			$result = $db->query( 'SELECT * FROM ' . TABLE_ONLINETEST_NAME . '_essay WHERE essay_id IN ( ' . implode( ',', $configEexam ) . ' )' );
			while( $item = $result->fetch() )
			{
				$list_question[$item['essay_id']] = $item;
			}


			$i = 1;
			foreach( $configEexam as $essay_id )
			{
				if( isset( $list_question[$essay_id] ) )
				{
					$data = $list_question[$essay_id];
					$data['answer'] = '';
					$json['question'][] = array( 'num' => str_pad( $i, 2, '0', STR_PAD_LEFT ), 'essay_id' => $data['essay_id'] );
				
					$dataQuestions[$data['essay_id']] = array(
						'stt' => $i,
						'essay_id' => $data['essay_id'],
						'token' => md5( $nv_Request->session_id . $global_config['sitekey'] . $data['essay_id'] ),
						'question' => $data['question'],
						'answer' => $data['answer'] );
 
					// $QuestionAnswers[$data['essay_id']] = array(
						// 'question' => $data['question'],
						// 'answer' => $data['answer']);

					++$i;

				}

			}
			
			unset( $list_question );

		}

		$nv_Request->set_Cookie( $module_data . '_test_timeout_essay_' . $dataContent['essay_exam_id'], NV_CURRENTTIME );

		$test_time = NV_CURRENTTIME + 2;

		$history_alias = getHistoryCode( getRandomString( 15 ) );

		$stmt = $db->prepare( 'INSERT INTO ' . TABLE_ONLINETEST_NAME . '_history_essay SET 
			history_alias = :history_alias, 
			num_question = ' . intval( $dataContent['num_question'] ) . ', 
			time = ' . intval( $dataContent['time'] ) . ', 
			userid = ' . intval( $globalUserid ) . ', 
			test_time = ' . intval( $test_time ) . ', 
			point = ' . intval( $dataContent['point'] ) . ', 
			max_score = ' . intval( $onlineTestConfig['max_score'] ) . ', 
			essay_exam_id=' . intval( $dataContent['essay_exam_id'] ) );

		$stmt->bindParam( ':history_alias', $history_alias, PDO::PARAM_STR );
		$stmt->execute();

		$history_essay_id = $db->lastInsertId();

		$stmt->closeCursor();

		if( $history_essay_id )
		{
			foreach( $dataQuestions as $essay_id => $item )
			{
				$stmt = $db->prepare( 'INSERT INTO ' . TABLE_ONLINETEST_NAME . '_history_essay_row SET 
					question = :question, 
					answer = :answer,
					essay_id=' . intval( $essay_id ) . ',
					history_essay_id=' . intval( $history_essay_id ) );

				$stmt->bindParam( ':question', $item['question'], PDO::PARAM_STR );
				$stmt->bindParam( ':answer', $item['answer'], PDO::PARAM_STR );
				$stmt->execute();
				$stmt->closeCursor();

			}

			
			$db_slave->query( 'INSERT INTO ' . TABLE_ONLINETEST_NAME . '_limit_essay (userid,essay_exam_id,test_limit) VALUES (' . intval( $globalUserid ) . ',' . intval( $dataContent['essay_exam_id'] ) . ', 1) ON DUPLICATE KEY UPDATE test_limit=test_limit+1' );

			$db_slave->query( 'UPDATE ' . TABLE_ONLINETEST_NAME . '_point SET point = ( point - ' . intval( $dataContent['point'] ) . ') WHERE userid=' . intval( $globalUserid ) );
			
			$db_slave->query( 'UPDATE ' . TABLE_ONLINETEST_NAME . '_essay_exam SET tested = ( SELECT COUNT(*) FROM ' . TABLE_ONLINETEST_NAME . '_history_essay WHERE essay_exam_id = ' . intval( $dataContent['essay_exam_id'] ) . ' ) WHERE essay_exam_id = ' . intval( $dataContent['essay_exam_id'] ) );

		}

		$json['time_test'] = $dataContent['time'] * 60;
		$json['time_now'] = $test_time;
		$json['template'] = ThemeOnlineTestGetQuestionsEssay( $dataContent, $dataQuestions, $history_essay_id );

		$nv_Request->set_Session( 'history_essay_' . $dataContent['essay_exam_id'], intval( $history_essay_id ) );
	}

}
else
{
	$type_exam_id = $nv_Request->get_int( 'type_exam_id', 'post', 0 );
	$token = $nv_Request->get_title( 'token', 'post', '', 1 );

	if( $token != md5( $nv_Request->session_id . $global_config['sitekey'] . $type_exam_id ) )
	{
		nv_jsonOutput( array( 'error' => $lang_module['error_security'] ) );
	}

	$query = $db_slave->query( 'SELECT * FROM ' . TABLE_ONLINETEST_NAME . '_type_exam WHERE status=1 AND type_exam_id = ' . intval( $type_exam_id ) );
	$dataContent = $query->fetch();

	if( empty( $dataContent ) )
	{
		nv_jsonOutput( array( 'error' => $lang_module['error_status_or_notexist'] ) );
	}

	if( $onlineTestConfig['test_limit'] > 0 )
	{
		$testLimit = $db->query( 'SELECT test_limit FROM ' . TABLE_ONLINETEST_NAME . '_limit WHERE userid=' . intval( $globalUserid ) . ' AND type_exam_id=' . intval( $dataContent['type_exam_id'] ) )->fetchColumn();

		if( $testLimit >= $onlineTestConfig['test_limit'] )
		{
			nv_jsonOutput( array( 'error' => $lang_module['error_limit'] ) );
		}

	}

	$dataContent['alias'] = strtolower( change_alias( $dataContent['title'] ) );

	$checkHistoryTest = $nv_Request->get_int( 'history_' . $type_exam_id, 'session' );

	if( ! empty( $checkHistoryTest ) )
	{
		$history = $db->query( 'SELECT * FROM ' . TABLE_ONLINETEST_NAME . '_history WHERE history_id = ' . intval( $checkHistoryTest ) . ' AND is_deleted=0 AND is_sended=0 AND userid = ' . intval( $globalUserid ) . ' AND type_exam_id = ' . intval( $type_exam_id ) )->fetch();

		if( $history && ( $history['test_time'] + ( $history['time'] * 60 ) ) > NV_CURRENTTIME )
		{
			$getLevel = getLevel( $module_name );

			$test_time = NV_CURRENTTIME - $history['time_do_test'];

			$list_question = unserialize( $history['question'] );

			$list_answer = unserialize( $history['list_answer'] );
			$i = 1;
			foreach( $list_question as $question_id => $data )
			{
				$sys_answers = $list_answer[$question_id];

				$json['question'][] = array( 'num' => str_pad( $i, 2, '0', STR_PAD_LEFT ), 'question_id' => $question_id );

				$dataQuestions[$question_id] = array(
					'stt' => $i,
					'question_id' => $question_id,
					'level_id' => $data['level_id'],
					'level' => isset( $getLevel[$data['level_id']] ) ? $getLevel[$data['level_id']]['title'] : '',
					'token' => md5( $nv_Request->session_id . $global_config['sitekey'] . $question_id ),
					'question' => $data['question'],
					'trueanswer' => $data['trueanswer'],
					'answers' => $sys_answers,
					'user_answers' => $data['user_answers'],
					'comment' => $data['comment'] );
				++$i;
			}

			$json['time_test'] = ( $history['time'] * 60 ) - ( NV_CURRENTTIME - $history['test_time'] );
			$json['time_now'] = $test_time;

			$json['template'] = ThemeOnlineTestGetQuestions( $dataContent, $dataQuestions, $history['history_id'] );

			if( $dataContent['type_id'] == 2 )
			{

				$json['answers_list'] = ThemeOnlineTestGetAnswers( $dataContent, $dataQuestions, $history['history_id'] );

			}
		}
		else
		{
			$checkHistoryTest = 0;
			$nv_Request->set_Session( 'history_' . $type_exam_id, 0 );
			$db->query( 'UPDATE ' . TABLE_ONLINETEST_NAME . '_history SET is_sended=1 WHERE history_id = ' . intval( $history['history_id'] ) . ' AND userid = ' . intval( $user_info['userid'] ) );
		}
	}

	if( empty( $checkHistoryTest ) )
	{
		$list_answer = array();
		$QuestionAnswers = array();

		$getLevel = getLevel( $module_name );

		if( $dataContent['type_id'] == 0 )
		{
			$configEexam = unserialize( $dataContent['config'] );
			$lastItem = end( $configEexam );

			$last_category_id = $lastItem['category_id'];
			$last_level_id = end( $lastItem['level_id'] );

			$i = 1;
			$check_num = 0;
			foreach( $configEexam as $category_id => $cat )
			{
				foreach( $cat['level_id'] as $level_id )
				{
					if( ( $category_id == $last_category_id ) and ( $level_id == $last_level_id ) )
					{
						$num_question = $dataContent['num_question'] - $check_num;
					}
					else
					{
						$num_question = intval( $cat['percent'][$level_id] * $dataContent['num_question'] / 100 ); // Tinh so cau hoi theo ti le
						$check_num += $num_question;
					}

					$sql = 'SELECT DISTINCT * FROM ' . TABLE_ONLINETEST_NAME . '_question WHERE status=1 AND category_id=' . intval( $category_id ) . ' AND level_id=' . intval( $level_id ) . ' ORDER BY RAND() LIMIT ' . $num_question;
					$result = $db->query( $sql );
					$check = $result->rowCount();

					if( $check != $num_question )
					{
						nv_jsonOutput( array( 'error' => $lang_module['error_question_not_enough'] ) );
					}
					else
					{
						while( $data = $result->fetch() )
						{

							$answers = @unserialize( $data['answers'] );

							shuffleAssoc( $answers );

							$json['question'][] = array( 'num' => str_pad( $i, 2, '0', STR_PAD_LEFT ), 'question_id' => $data['question_id'] );

							$dataQuestions[$data['question_id']] = array(
								'stt' => $i,
								'question_id' => $data['question_id'],
								'level_id' => $data['level_id'],
								'level' => isset( $getLevel[$data['level_id']] ) ? $getLevel[$data['level_id']]['title'] : '',
								'token' => md5( $nv_Request->session_id . $global_config['sitekey'] . $data['question_id'] ),
								'question' => $data['question'],
								'trueanswer' => $data['trueanswer'],
								'answers' => $answers,
								'comment' => $data['comment'] );

							$list_answer[$data['question_id']] = $answers;

							$QuestionAnswers[$data['question_id']] = array(
								'question' => $data['question'],
								'sys_answers' => array_keys( $answers ),
								'user_answers' => array(),
								'trueanswer' => $data['trueanswer'] );

							++$i;
						}

					}

				}

			}
		}
		elseif( $dataContent['type_id'] == 1 )
		{
			$configEexam = unserialize( $dataContent['config'] );

			if( ! empty( $configEexam ) )
			{

				if( $dataContent['random'] )
				{
					shuffle( $configEexam );
				}

				$list_question = array();
				$result = $db->query( 'SELECT * FROM ' . TABLE_ONLINETEST_NAME . '_question WHERE question_id IN ( ' . implode( ',', $configEexam ) . ' )' );
				while( $item = $result->fetch() )
				{
					$item['level'] = isset( $getLevel[$item['level_id']] ) ? $getLevel[$item['level_id']]['title'] : '';
					$list_question[$item['question_id']] = $item;
				}

				$i = 1;
				foreach( $configEexam as $_question_id )
				{
					if( isset( $list_question[$_question_id] ) )
					{
						$data = $list_question[$_question_id];

						$answers = @unserialize( $data['answers'] );

						shuffleAssoc( $answers );

						$json['question'][] = array( 'num' => str_pad( $i, 2, '0', STR_PAD_LEFT ), 'question_id' => $data['question_id'] );

						$dataQuestions[$data['question_id']] = array(
							'stt' => $i,
							'question_id' => $data['question_id'],
							'level_id' => $data['level_id'],
							'level' => isset( $getLevel[$data['level_id']] ) ? $getLevel[$data['level_id']]['title'] : '',
							'token' => md5( $nv_Request->session_id . $global_config['sitekey'] . $data['question_id'] ),
							'question' => $data['question'],
							'trueanswer' => $data['trueanswer'],
							'answers' => $answers,
							'comment' => $data['comment'] );

						$list_answer[$data['question_id']] = $answers;

						$QuestionAnswers[$data['question_id']] = array(
							'question' => $data['question'],
							'level_id' => $data['level_id'],
							'level' => isset( $getLevel[$data['level_id']] ) ? $getLevel[$data['level_id']]['title'] : '',
							'sys_answers' => array_keys( $answers ),
							'user_answers' => array(),
							'trueanswer' => $data['trueanswer'] );

						++$i;

					}

				}
				unset( $list_question );

			}

		}
		else
		{
			$configEexam = unserialize( $dataContent['config'] );

			if( ! empty( $configEexam ) )
			{

				foreach( $configEexam as $question_id => $item )
				{
					$list_question[$question_id] = array(
						'question_id' => $question_id,
						'answers' => $item['answers'],
						'trueanswer' => $item['trueanswer'] );
				}

				$i = 1;
				foreach( $configEexam as $_question_id => $item )
				{

					$dataQuestions[$_question_id] = array(
						'stt' => $i,
						'question_id' => $data['question_id'],
						'token' => md5( $nv_Request->session_id . $global_config['sitekey'] . $data['question_id'] ),
						'trueanswer' => $item['trueanswer'],
						'answers' => $item['answers'],
						'comment' => '' );

					$list_answer[$_question_id] = $item['answers'];

					$QuestionAnswers[$_question_id] = array(
						'sys_answers' => $item['answers'],
						'user_answers' => array(),
						'trueanswer' => $item['trueanswer'] );

					++$i;

				}
				unset( $list_question );

			}

		}

		$list_answer = serialize( $list_answer );
		$QuestionAnswers = serialize( $QuestionAnswers );

		$nv_Request->set_Cookie( $module_data . '_test_timeout_' . $dataContent['type_exam_id'], NV_CURRENTTIME );

		$test_time = NV_CURRENTTIME + 2;

		$history_alias = getHistoryCode( getRandomString( 15 ) );

		$stmt = $db->prepare( 'INSERT INTO ' . TABLE_ONLINETEST_NAME . '_history SET 
			history_alias = :history_alias, 
			list_answer = :list_answer, 
			question = :question, 
			num_question = ' . intval( $dataContent['num_question'] ) . ', 
			time = ' . intval( $dataContent['time'] ) . ', 
			userid = ' . intval( $globalUserid ) . ', 
			test_time = ' . intval( $test_time ) . ', 
			point = ' . intval( $dataContent['point'] ) . ', 
			max_score = ' . intval( $onlineTestConfig['max_score'] ) . ', 
			type_exam_id=' . intval( $dataContent['type_exam_id'] ) . ',
			type_id=' . intval( $dataContent['type_id'] ) );

		$stmt->bindParam( ':history_alias', $history_alias, PDO::PARAM_STR );
		$stmt->bindParam( ':list_answer', $list_answer, PDO::PARAM_STR );
		$stmt->bindParam( ':question', $QuestionAnswers, PDO::PARAM_STR );
		$stmt->execute();

		$history_id = $db->lastInsertId();

		$stmt->closeCursor();

		if( $history_id )
		{
			$db_slave->query( 'INSERT INTO ' . TABLE_ONLINETEST_NAME . '_limit (userid,type_exam_id,test_limit) VALUES (' . intval( $globalUserid ) . ',' . intval( $dataContent['type_exam_id'] ) . ', 1) ON DUPLICATE KEY UPDATE test_limit=test_limit+1' );

			$db_slave->query( 'UPDATE ' . TABLE_ONLINETEST_NAME . '_point SET point = ( point - ' . intval( $dataContent['point'] ) . ') WHERE userid=' . intval( $globalUserid ) );
			
			$db_slave->query( 'UPDATE ' . TABLE_ONLINETEST_NAME . '_type_exam SET tested = ( SELECT COUNT(*) FROM ' . TABLE_ONLINETEST_NAME . '_history WHERE type_exam_id = ' . intval( $dataContent['type_exam_id'] ) . ' ) WHERE type_exam_id = ' . intval( $dataContent['type_exam_id'] ) );

		}

		$json['time_test'] = $dataContent['time'] * 60;
		$json['time_now'] = $test_time;
		$json['template'] = ThemeOnlineTestGetQuestions( $dataContent, $dataQuestions, $history_id );

		if( $dataContent['type_id'] == 2 )
		{
			$json['answers_list'] = ThemeOnlineTestGetAnswers( $dataContent, $dataQuestions, $history_id );

		}

		$nv_Request->set_Session( 'history_' . $dataContent['type_exam_id'], intval( $history_id ) );
	}
}

nv_jsonOutput( $json );
