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

$globalUserid = defined( 'NV_IS_USER' ) ? $user_info['userid'] : 0;

if( $onlineTestConfig['open'] == 0 )
{
	nv_jsonOutput( array( 'error' => $lang_module['error_close']) );
}

if( ! $globalUserid )
{
	nv_jsonOutput( array( 'error' => $lang_module['error_login'] ) );
}
if( ACTION_METHOD == 'essay' )
{
	$history_essay_id = $nv_Request->get_int( 'history_essay_id', 'post', 0 );
	$essay_exam_id = $nv_Request->get_int( 'essay_exam_id', 'post', 0 );
	$token = $nv_Request->get_title( 'token', 'post', '', 1 );


	$is_sended = $nv_Request->get_int( 'is_sended', 'post', 0 );


	if( $token != md5( $nv_Request->session_id . $global_config['sitekey'] . $essay_exam_id . $history_essay_id ) )
	{
		nv_jsonOutput( array( 'error' => $lang_module['error_security'] ) );
	}

	$query = $db_slave->query( 'SELECT * FROM ' . TABLE_ONLINETEST_NAME . '_essay_exam WHERE status=1 AND essay_exam_id = ' . intval( $essay_exam_id ) );
	$dataContent = $query->fetch();

	if( empty( $dataContent ) )
	{
		nv_jsonOutput( array( 'error' => $lang_module['error_status_or_notexist'] ) );
	}
	 
	$query = $db_slave->query( 'SELECT * FROM ' . TABLE_ONLINETEST_NAME . '_history_essay WHERE userid='. intval( $globalUserid ) .' AND essay_exam_id= '. intval( $essay_exam_id ) .' AND history_essay_id = ' . intval( $history_essay_id ) );
	$historyContent = $query->fetch();

	if( empty( $historyContent ) )
	{
		nv_jsonOutput( array( 'error' => $lang_module['error_limit'] ) );
	}
	 
	$getAnswers = $nv_Request->get_typed_array( 'answers', 'post', 'array', array() );

	$listQuestion = array();

	foreach( $getAnswers as $essay_id => $answers ) 
	{
		if( $answers['token'] == md5( $nv_Request->session_id . $global_config['sitekey'] . $essay_id ) )
		{
			$listQuestion[] = $essay_id;
		}
	 
	}

	if( sizeOf( $listQuestion ) != $historyContent['num_question'] )
	{
		nv_jsonOutput( array( 'error' => $lang_module['error_security'] ) );
	}
	 
 
 	$getQuestion = array();
	$listQuestion2 = array();
	$result = $db->query( 'SELECT * FROM ' . TABLE_ONLINETEST_NAME . '_essay WHERE essay_id IN ('. implode(',', $listQuestion ) .')');
	while( $data = $result->fetch() )
	{
 
		$listQuestion2[] = $data['essay_id'];
		$getQuestion[$data['essay_id']] = $data;
	 
	}
	if( sizeOf( $listQuestion2 ) != $historyContent['num_question'] )
	{
		nv_jsonOutput( array( 'error' => $lang_module['error_not_exist_question'] ) );
	}
 
	$QuestionAnswers = array();
 	 
	$time_second = $dataContent['time'] * 60;
	$time_do_test = NV_CURRENTTIME - $historyContent['test_time'];
	$time_do_test = ( $time_do_test > $time_second ) ? $time_second : $time_do_test;

	if( ( $historyContent['test_time'] + ( $historyContent['time'] * 60 ) ) <= NV_CURRENTTIME && $historyContent['is_sended'] == 0 ) 
	{
		if( $historyContent['time_do_test'] > ( $historyContent['time'] * 60 ) )
		{
			$historyContent['time_do_test'] = ( $historyContent['time'] * 60 );
		}
		$is_sended = 1;	 
	}

	if( $is_sended == 1 )
	{
		$sys_answers = unserialize( $historyContent['list_answer'] );
			
		foreach( $getAnswers as $question_id => $answers ) 
		{

			$question = $getQuestion[$question_id]['question'];
			$QuestionAnswers[$essay_id] = array(
				'question'=> $question,
 				'answer'=> isset( $answers['answer'] ) ? $answers['answer'] :'',
 			);
		}
 
		$score = round( $historyContent['max_score'] * $number_success / $historyContent['num_question'], 2 );
		
 		$stmt = $db->prepare( 'UPDATE ' . TABLE_ONLINETEST_NAME . '_history_essay SET 
			is_sended = ' . intval( $is_sended ) . ',
			time_do_test = ' . intval( $time_do_test ) . ',
			score =:score
			WHERE history_essay_id=' . intval( $history_essay_id ) );
 		$stmt->bindParam( ':score', $score, PDO::PARAM_STR );
		if( $stmt->execute() )
		{
			foreach( $QuestionAnswers as $essay_id => $item )
			{
				$stmt = $db->prepare( 'UPDATE ' . TABLE_ONLINETEST_NAME . '_history_essay_row SET 
					question = :question, 
					answer = :answer WHERE essay_id = '. intval( $essay_id )  .' AND  history_essay_id=' . intval( $history_essay_id ) );

				$stmt->bindParam( ':question', $item['question'], PDO::PARAM_STR );
				$stmt->bindParam( ':answer', $item['answer'], PDO::PARAM_STR );
				$stmt->execute();
				$stmt->closeCursor();

			}
		}
		
	}
	else
	{
 		
		
		foreach( $getAnswers as $essay_id => $answers ) 
		{
			$question = $getQuestion[$essay_id]['question'];
			$QuestionAnswers[$essay_id] = array(
				'question'=> $question,
 				'answer'=> isset( $answers['answer'] ) ? $answers['answer'] :'',
 			);
 		}
	 
		$score = round( $historyContent['max_score'] * $number_success / $historyContent['num_question'], 2 );
	 
		$stmt = $db->prepare( 'UPDATE ' . TABLE_ONLINETEST_NAME . '_history_essay SET 
			is_sended = ' . intval( $is_sended ) . ',
			time_do_test = ' . intval( $time_do_test ) . ',
			score =:score
			WHERE history_essay_id=' . intval( $history_essay_id ) );
		$stmt->bindParam( ':score', $score, PDO::PARAM_STR );
		if( $stmt->execute() )
		{
			foreach( $QuestionAnswers as $essay_id => $item )
			{
				$stmt = $db->prepare( 'UPDATE ' . TABLE_ONLINETEST_NAME . '_history_essay_row SET 
					question = :question, 
					answer = :answer WHERE essay_id = '. intval( $essay_id )  .' AND  history_essay_id=' . intval( $history_essay_id ) );

				$stmt->bindParam( ':question', $item['question'], PDO::PARAM_STR );
				$stmt->bindParam( ':answer', $item['answer'], PDO::PARAM_STR );
				$stmt->execute();
				$stmt->closeCursor();

			}
		}
 
		
	}


	// $json['time_do_test'] = $time_do_test; 
	if( $is_sended == 1 )
	{
		//$json['ranking'] = $db->query('SELECT title FROM ' . TABLE_ONLINETEST_NAME . '_ranking WHERE min_score >= '. floatval( $score ) .' AND '. floatval( $score ) .' < max_score ORDER BY max_score ASC LIMIT 1')->fetchColumn();
		$json['number_total'] = $score;
		$json['answers'] = true;
 		$json['shareLink'] = NV_MAIN_DOMAIN . nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $historyContent['history_alias'] . '/' .  strtolower( change_alias( $dataContent['title'] ) ) . $global_config['rewrite_exturl'], true );
		$json['print'] =  NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=history-essay&action=print&history_id='. $history_id .'&token='. md5( $nv_Request->session_id . $global_config['sitekey'] . $history_id );
		
		$nv_Request->set_Session( 'history_essay_' . $dataContent['essay_exam_id'], 0 );
	 
	}
}
else{
	$history_id = $nv_Request->get_int( 'history_id', 'post', 0 );
	$type_exam_id = $nv_Request->get_int( 'type_exam_id', 'post', 0 );
	$token = $nv_Request->get_title( 'token', 'post', '', 1 );


	$is_sended = $nv_Request->get_int( 'is_sended', 'post', 0 );


	if( $token != md5( $nv_Request->session_id . $global_config['sitekey'] . $type_exam_id . $history_id ) )
	{
		nv_jsonOutput( array( 'error' => $lang_module['error_security'] ) );
	}

	$query = $db_slave->query( 'SELECT * FROM ' . TABLE_ONLINETEST_NAME . '_type_exam WHERE status=1 AND type_exam_id = ' . intval( $type_exam_id ) );
	$dataContent = $query->fetch();

	if( empty( $dataContent ) )
	{
		nv_jsonOutput( array( 'error' => $lang_module['error_status_or_notexist'] ) );
	}
	 
	$query = $db_slave->query( 'SELECT * FROM ' . TABLE_ONLINETEST_NAME . '_history WHERE userid='. intval( $globalUserid ) .' AND type_exam_id= '. intval( $type_exam_id ) .' AND history_id = ' . intval( $history_id ) );
	$historyContent = $query->fetch();

	if( empty( $historyContent ) )
	{
		nv_jsonOutput( array( 'error' => $lang_module['error_limit'] ) );
	}
	 
	$getAnswers = $nv_Request->get_typed_array( 'answers', 'post', 'array', array() );

	$listQuestion = array();

	foreach( $getAnswers as $question_id => $answers ) 
	{
		if( $answers['token'] == md5( $nv_Request->session_id . $global_config['sitekey'] . $question_id ) )
		{
			$listQuestion[] = $question_id;
		}
	 
	}

	if( sizeOf( $listQuestion ) != $historyContent['num_question'] )
	{
		nv_jsonOutput( array( 'error' => $lang_module['error_security'] ) );
	}
	 
	if( $historyContent['type_id'] == 0 || $historyContent['type_id'] == 1 )
	{
	 
		$getLevel = getLevel( $module_name );
		$getQuestion = array();
		$listQuestion2 = array();
		$result = $db->query( 'SELECT * FROM ' . TABLE_ONLINETEST_NAME . '_question WHERE question_id IN ('. implode(',', $listQuestion ) .')');
		while( $data = $result->fetch() )
		{
		 
			$data['level'] = isset( $getLevel[$data['level_id']] ) ? $getLevel[$data['level_id']]['title'] : '';
			
			$listQuestion2[] = $data['question_id'];
			$dataQuestions[$data['question_id']] = array( 'trueanswer' => array_map( 'intval', explode(',', $data['trueanswer'] ) ) );
			$getQuestion[$data['question_id']] = $data;
		 
		}
		if( sizeOf( $listQuestion2 ) != $historyContent['num_question'] )
		{
			nv_jsonOutput( array( 'error' => $lang_module['error_not_exist_question'] ) );
		}

	}
	else
	{
		$configEexam = unserialize( $dataContent['config'] );
	 
		if( !empty( $configEexam ) )
		{	
			$getQuestion = array();
			$listQuestion2 = array();
			foreach ( $configEexam as $_question_id => $item )	
			{
				$listQuestion2[] = $_question_id;
				$dataQuestions[$_question_id]['trueanswer'] = $item['trueanswer'];
				$getQuestion[$_question_id] = array('question'=> '', 'analyzes'=> '', );
				
			}

		}
	}

	$number_error = 0;
	$number_success = 0;
	$number_total = 0;
	 
	$QuestionAnswers = array();
	$QuestionAnalyzes = array();
	 
	$time_second = $dataContent['time'] * 60;
	$time_do_test = NV_CURRENTTIME - $historyContent['test_time'];
	$time_do_test = ( $time_do_test > $time_second ) ? $time_second : $time_do_test;

	if( ( $historyContent['test_time'] + ( $historyContent['time'] * 60 ) ) <= NV_CURRENTTIME && $historyContent['is_sended'] == 0 ) 
	{
		if( $historyContent['time_do_test'] > ( $historyContent['time'] * 60 ) )
		{
			$historyContent['time_do_test'] = ( $historyContent['time'] * 60 );
		}
		$is_sended = 1;	 
	}

	if( $is_sended == 1 )
	{
		$sys_answers = unserialize( $historyContent['list_answer'] );
			
		foreach( $getAnswers as $question_id => $answers ) 
		{
			$trueanswer = $dataQuestions[$question_id]['trueanswer'];
			if( isset( $answers['answers'] ) && arrayEqual($trueanswer, $answers['answers']) )
			{
				++$number_success; 
			}else{
				++$number_error;
			}
			$question = $getQuestion[$question_id]['question'];
			$QuestionAnswers[$question_id] = array(
				'sys_answers'=> ( $historyContent['type_id'] == 2) ? $sys_answers[$question_id] : array_keys( $sys_answers[$question_id] ),
				'user_answers'=> isset( $answers['answers'] ) ? $answers['answers'] : array()
			);
			$QuestionAnalyzes[$question_id] = isset( $getQuestion[$question_id]['analyzes'] ) ? $getQuestion[$question_id]['analyzes'] : '';
		}

		$QuestionAnswers = serialize( $QuestionAnswers );
		
		$score = round( $historyContent['max_score'] * $number_success / $historyContent['num_question'], 2 );
		
		$list_answer = '';
		$stmt = $db->prepare( 'UPDATE ' . TABLE_ONLINETEST_NAME . '_history SET 
			is_sended = ' . intval( $is_sended ) . ',
			time_do_test = ' . intval( $time_do_test ) . ',
			list_answer =:list_answer,
			score =:score,
			question =:question
			WHERE history_id=' . intval( $history_id ) );
		$stmt->bindParam( ':list_answer', $list_answer, PDO::PARAM_STR );
		$stmt->bindParam( ':score', $score, PDO::PARAM_STR );
		$stmt->bindParam( ':question', $QuestionAnswers, PDO::PARAM_STR, strlen( $QuestionAnswers ) );
		$stmt->execute();
		
	}
	else
	{
		$sys_answers = unserialize( $historyContent['list_answer'] );
		
		
		foreach( $getAnswers as $question_id => $answers ) 
		{
			$trueanswer = $dataQuestions[$question_id]['trueanswer'];
			if( isset( $answers['answers'] ) && arrayEqual($trueanswer, $answers['answers']) )
			{
				++$number_success; 
			}else{
				++$number_error;
			}
			$question = $getQuestion[$question_id]['question'];
			$QuestionAnswers[$question_id] = array(
				'question'=> $question,
				'sys_answers'=> ( $historyContent['type_id'] == 2) ? $sys_answers[$question_id] : array_keys( $sys_answers[$question_id] ),
				'user_answers'=> isset( $answers['answers'] ) ? $answers['answers'] : array(),
				'trueanswer'=> $trueanswer 
			);
			$QuestionAnalyzes[$question_id] = isset( $getQuestion[$question_id]['analyzes'] ) ? $getQuestion[$question_id]['analyzes'] : '';
		}

		$QuestionAnswers = serialize( $QuestionAnswers );
	 
		$score = round( $historyContent['max_score'] * $number_success / $historyContent['num_question'], 2 );
	 
		$stmt = $db->prepare( 'UPDATE ' . TABLE_ONLINETEST_NAME . '_history SET 
			is_sended = ' . intval( $is_sended ) . ',
			time_do_test = ' . intval( $time_do_test ) . ',
			score =:score,
			question =:question
			WHERE history_id=' . intval( $history_id ) );
		$stmt->bindParam( ':score', $score, PDO::PARAM_STR );
		$stmt->bindParam( ':question', $QuestionAnswers, PDO::PARAM_STR, strlen( $QuestionAnswers ) );
		$stmt->execute();
	}


	// $json['time_do_test'] = $time_do_test; 
	if( $is_sended == 1 )
	{
		//$json['ranking'] = $db->query('SELECT title FROM ' . TABLE_ONLINETEST_NAME . '_ranking WHERE min_score >= '. floatval( $score ) .' AND '. floatval( $score ) .' < max_score ORDER BY max_score ASC LIMIT 1')->fetchColumn();
		$json['number_success'] = $number_success;
		$json['number_error'] = $number_error;
		$json['number_total'] = $score;
		$json['answers'] = $dataQuestions;
		$json['analyzes'] = $QuestionAnalyzes; 
		$json['shareLink'] = NV_MAIN_DOMAIN . nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $historyContent['history_alias'] . '/' .  strtolower( change_alias( $dataContent['title'] ) ) . $global_config['rewrite_exturl'], true );
		$json['print'] =  NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=history&action=print&history_id='. $history_id .'&token='. md5( $nv_Request->session_id . $global_config['sitekey'] . $history_id );
		
		$nv_Request->set_Session( 'history_' . $dataContent['type_exam_id'], 0 );
	 
	}
	
	
}

nv_jsonOutput( $json );
