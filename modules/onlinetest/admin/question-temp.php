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
			$result = $db->query( 'DELETE FROM ' . TABLE_ONLINETEST_NAME . '_question_import WHERE question_id = ' . ( int )$question_id );
			if( $result->rowCount() )
			{
 			
				$json['id'][$a] = $question_id;
				$_del_array[] = $question_id;
				++$a;
			}
		}
		$count = sizeof( $_del_array );

		if( $count )
		{
			nv_insert_logs( NV_LANG_DATA, $module_name, 'log_del_question', implode( ', ', $_del_array ), $admin_info['userid'] );

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
elseif( ACTION_METHOD == 'move' )
{
	$json = array();

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
			
			$data = $db->query( 'SELECT * FROM ' . TABLE_ONLINETEST_NAME . '_question_import WHERE question_id= '. ( int )$question_id )->fetch();
			if( !empty( $data ) )
			{
				$stmt = $db->prepare( 'INSERT INTO ' . TABLE_ONLINETEST_NAME . '_question SET 
					category_id=' . intval( $data['category_id'] ) . ',
					level_id=' . intval( $data['level_id'] ) . ',
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
				$stmt->bindParam( ':answers', $data['answers'], PDO::PARAM_STR, strlen(  $data['answers'] ) );
				$stmt->bindParam( ':trueanswer', $data['trueanswer'], PDO::PARAM_STR );
				$stmt->execute();

				if( $_question_id = $db->lastInsertId() )
				{
					$result = $db->query( 'DELETE FROM ' . TABLE_ONLINETEST_NAME . '_question_import WHERE question_id = ' . ( int )$question_id );
					if( $result->rowCount() )
					{
					
						$json['id'][$a] = $question_id;
						$_del_array[] = $question_id;
						++$a;
					}
					
				}
				
			}
			
 	
		}
		$count = sizeof( $_del_array );

		if( $count )
		{
			nv_insert_logs( NV_LANG_DATA, $module_name, 'log_move_question', implode( ', ', $_del_array ), $admin_info['userid'] );

			$nv_Cache->delMod( $module_name );

			$json['success'] = $lang_module['question_move_success'];
		}

	}
	else
	{
		$json['error'] = $lang_module['question_error_security'];
	}

	nv_jsonOutput( $json );
}
elseif( ACTION_METHOD == 'status' )
{
	$json = array();

	$question_id = $nv_Request->get_int( 'question_id', 'post', 0 );
	$new_vid = $nv_Request->get_int( 'new_vid', 'post', 0 );
	$token = $nv_Request->get_title( 'token', 'post', '', 1 );

	if( $token == md5( $nv_Request->session_id . $global_config['sitekey'] . $question_id ) )
	{
		$data = $db->query( 'SELECT question_id, user_id, contribute, status FROM ' . TABLE_ONLINETEST_NAME . '_question_import WHERE question_id=' . $question_id )->fetch();
		if ( $data['status'] != $new_vid )
		{
			$sql = 'UPDATE ' . TABLE_ONLINETEST_NAME . '_question_import SET status=' . $new_vid . ' WHERE question_id=' . $question_id;
			if( $db->exec( $sql ) )
			{
 
				
				nv_insert_logs( NV_LANG_DATA, $module_name, 'log_change_status_question', 'question_id:' . $question_id, $admin_info['userid'] );

				$nv_Cache->delMod($module_name);

				$json['success'] = $lang_module['question_status_success'];

			}
			else
			{
				$json['error'] = $lang_module['question_error_status'];

			}
		}
		else
		{
			$json['error'] = $lang_module['question_error_status'];

		}
	}
	else
	{
		$json['error'] = $lang_module['question_error_security'];
	}

	nv_jsonOutput( $json );
}
elseif( ACTION_METHOD == 'import' )
{
 
	$data = array(
		'question_id' => 0,
		'category_id' => 0,
		'level_id' => 0,
		'user_id' => $admin_info['userid'],
		'user_name' => $admin_info['username'],
		'question' => '',
		'analyzes' => '',
		'answers' => array(),
		'trueanswer' => '',
		'date_added' => 0,
		'date_modified' => 0,
		'status' => 1,
		'duplicate' => 0,
		'addAnalyzes' => 0,
		'getcontent' => array(),	  
	);
	
	$dataImage = array();
	$dataQuestion = array();
	$dataContent = array();
	$upload_info = array();
	$header = array();
	$array_data = array();
	$insert_error = array(); 
	$insert_success = 0; 
	$update_success = 0; 
	$error = array();
	
	if( $nv_Request->get_int( 'save', 'post' ) == 1 )
	{

		$data['category_id'] = $nv_Request->get_int( 'category_id', 'post', 0 );
		$data['level_id'] = $nv_Request->get_int( 'level_id', 'post', 0 );
		$data['duplicate'] = $nv_Request->get_int( 'duplicate', 'post', 0 );
			
		if( empty( $data['category_id'] ) ) $error['category'] = $lang_module['question_error_category_id'];
		if( empty( $data['level_id'] ) ) $error['level'] = $lang_module['question_error_level_id'];
		if( empty( $_FILES['files']['name'] ) ) $error['files'] = $lang_module['question_error_files'];
 		// if ( trim( strip_tags( $data['question'] ) ) == '' and ! preg_match("/\<img[^\>]*alt=\"([^\"]+)\"[^\>]*\>/is", $data['question'] ) ) 
		// {
			// $error['question'] =  $lang_module['question_error_question'];
		// }
		if( ! empty( $error ) && ! isset( $error['warning'] ) )
		{
			$error['warning'] = $lang_module['question_error_warning'];
		} 
		
		if( isset( $_FILES['files'] ) )
		{
			
			//$upload = new NukeViet\Files\Upload( ['documents'], $global_config['forbid_extensions'], $global_config['forbid_mimes'], NV_UPLOAD_MAX_FILESIZE, NV_MAX_WIDTH, NV_MAX_HEIGHT );
			//$upload->setLanguage( $lang_global );
			$array_question = nv_test_read_msword( $data['category_id'],$_FILES['files']['tmp_name']);
			//$temp_file = NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . $module_data . '_import_' . $data['category_id'] . '.html';
			//require_once (NV_ROOTDIR . '/modules/onlinetest/simple_html_dom.php');
			//$phpWord = \PhpOffice\PhpWord\IOFactory::load($_FILES['files']['tmp_name']);
			//$htmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');
			//$htmlWriter->save($temp_file);
			//$html = file_get_html($temp_file);
			////var_dump($html);die;
			//$all_p_tags = $html->find('p');
			//var_dump($all_p_tags);die;
			
			if( isset( $_FILES['files']['tmp_name'] ) and is_uploaded_file( $_FILES['files']['tmp_name'] ) )
			{
				 
				$upload_info = $upload->save_file( $_FILES['files'], NV_ROOTDIR . '/' . NV_TEMP_DIR, false, $global_config['nv_auto_resize'] );
				
				if( empty( $upload_info['error'] ) )
				{
					$temp_file = NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . $module_data . '_import_' . $data['category_id'] . '.html';
					
					//var_dump($temp_file);die;
					require_once (NV_ROOTDIR . '/modules/onlinetest/simple_html_dom.php');
					$filename = $upload_info['name'];
					$phpWord = \PhpOffice\PhpWord\IOFactory::load($_FILES['files']['tmp_name']);
					$htmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');
					$htmlWriter->save($temp_file);
					
					$html = file_get_html($temp_file);
					
					
					$all_p_tags = $html->find('p');
					//var_dump($all_p_tags);die;
					//print_r($all_p_tags);
					$array_question = array(
						'data' => array(),
						'error' => 0
					);
					$images = array();
					$count_image = 1;
					$index_image = $count_elemt = 0;
					$str = '';
					foreach ($phpWord->getSections() as $section) {
						$arrays = $section->getElements();
						
						foreach ($arrays as $e) {
							if (get_class($e) === 'PhpOffice\PhpWord\Element\TextRun') {
								foreach ($e->getElements() as $text) {
									if (get_class($text) === 'PhpOffice\PhpWord\Element\Text') {
										$str .= $text->getText();
									} elseif (get_class($text) === 'PhpOffice\PhpWord\Element\Image') {
										$data = base64_decode($text->getImageStringData(true));
										$file_name = md5($text->getName() . time());
										file_put_contents(NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . $file_name . '.png', $data);
										array_push($images, NV_BASE_SITEURL . NV_TEMP_DIR . '/' . $file_name . '.png');
										$count_image++;
									}
								}
								$str .= "\n";
							}
							//print_r($str);
							if (get_class($e) === 'PhpOffice\PhpWord\Element\TextBreak') {
								$array = preg_split("/\r\n|\n|\r/", $str);
								//print_r($array);
								if (is_array($array) && count($array) > 0) {
									$index_image = 0;
									$question = array(
										'question' => '',
										'answer' => array(),
										'useguide' => '',
										'count_true' => 0,
										'error' => array()
									);
									if ($all_p_tags[$count_elemt]->find('img')) {
										$all_p_tags[$count_elemt]->find('img')[0]->src = $images[$index_image++];
									}
									
									$question['question'] = nv_test_content_format($all_p_tags[$count_elemt++]);
									
									for ($i = 1; $i < count($array); $i++) {
										// phát hiện lời giải
										$is_userguide = 0;
										if (substr(nv_unhtmlspecialchars($array[$i]), 0, 1) === '>') {
											$is_userguide = 1;
										} elseif (substr($array[$i], 0, 1) === '*') {
											$is_true = 1;
											$question['count_true']++;
										} else {
											$is_true = 0;
										}

										if (isset($all_p_tags[$count_elemt]) && $all_p_tags[$count_elemt]->find('img')) {
											$all_p_tags[$count_elemt]->find('img')[0]->src = $images[$index_image++];
										}

										// loại bỏ các ký tự đánh dấu
										if (isset($all_p_tags[$count_elemt])) {
											if (count($all_p_tags[$count_elemt]->find('span')) > 0) {
												$type = 'span';
												$answerText = nv_unhtmlspecialchars($all_p_tags[$count_elemt]->find('span')[0]->plaintext);
											} else {
												$type = 'p';
												$answerText = $all_p_tags[$count_elemt]->plaintext;
											}
											if (substr($answerText, 0, 1) === '*' || substr($answerText, 0, 1) === '>') {
												if ($type == 'span') {
													$all_p_tags[$count_elemt]->find('span')[0]->innertext = substr($answerText, 1);
												} else {
													$all_p_tags[$count_elemt]->innertext = substr($answerText, 1);
												}
											}
										}

										if ($is_userguide) {
											$question['useguide'] = nv_test_content_format($all_p_tags[$count_elemt++]);
										} elseif ($i < count($array) - 1) {
											$question['answer'][$i] = array(
												'id' => $i,
												'content' => nv_test_content_format($all_p_tags[$count_elemt++]),
												'is_true' => $is_true
											);
										} else {
											$count_elemt++;
										}
										
									}

									// kiểm tra lỗi
									if (count($question['answer']) < 2) {
										$question['error'][] = $lang_module['error_required_answer'];
									} elseif (empty($question['count_true'])) {
										$question['error'][] = $lang_module['error_required_answer_is_true'];
									}

									if (!empty($question['error'])) {
										$array_question['error'] = 1;
									}

									$array_question['data'][] = $question;
								}
								$str = '';
								$images = array();
							}
						}
					}

					// insert last question
					$array = preg_split("/\r\n|\n|\r/", $str);
					if ($str != '' && is_array($array) && count($array) > 0) {
						$index_image = 0;
						$question = array(
							'question' => '',
							'answer' => array(),
							'count_true' => 0,
							'error' => array()
						);
						if ($all_p_tags[$count_elemt]->find('img')) {
							$all_p_tags[$count_elemt]->find('img')[0]->src = $images[$index_image++];
						}
						$question['question'] = nv_test_content_format($all_p_tags[$count_elemt++]);
						for ($i = 1; $i < count($array); $i++) {

							// phát hiện lời giải
							$is_userguide = 0;
							if (substr(nv_unhtmlspecialchars($array[$i]), 0, 1) === '>') {
								$is_userguide = 1;
								$question['useguide'] = nv_test_content_format($all_p_tags[$count_elemt]);
							} elseif (substr($array[$i], 0, 1) === '*') {
								$is_true = 1;
								$question['count_true']++;
							} else {
								$is_true = 0;
							}

							if (isset($all_p_tags[$count_elemt]) && $all_p_tags[$count_elemt]->find('img')) {
								$all_p_tags[$count_elemt]->find('img')[0]->src = $images[$index_image++];
							}

							// loại bỏ các ký tự đánh dấu
							if (isset($all_p_tags[$count_elemt]) && count($all_p_tags[$count_elemt]->find('span')) > 0) {
								$answerText = nv_unhtmlspecialchars($all_p_tags[$count_elemt]->find('span')[0]->plaintext);
								if (substr($answerText, 0, 1) === '*' || substr($answerText, 0, 1) === '>') {
									$all_p_tags[$count_elemt]->find('span')[0]->innertext = substr($answerText, 1);
								}
							}

							if ($is_userguide) {
								$question['useguide'] = nv_test_content_format($all_p_tags[$count_elemt++]);
							} elseif ($i < count($array) - 1) {
								$question['answer'][$i] = array(
									'id' => $i,
									'content' => nv_test_content_format($all_p_tags[$count_elemt++]),
									'is_true' => $is_true
								);
							} else {
								$count_elemt++;
							}
						}

						// kiểm tra lỗi
						if (count($question['answer']) < 2) {
							$question['error'][] = $lang_module['error_required_answer'];
						} elseif (empty($question['count_true'])) {
							$question['error'][] = $lang_module['error_required_answer_is_true'];
						}

						if (!empty($question['error'])) {
							$array_question['error'] = 1;
						}

						$array_question['data'][] = $question;
					}
					print_r($question);die;
					/* $zip = new ZipArchive;

					if( true === $zip->open( $filename ) )
					{
						for( $i = 0; $i < $zip->numFiles; $i++ )
						{
							$obj = ( object )$zip->statIndex( $i );
							// var_dump($obj);
							//[Content_Types].xml
							//_rels/.rels
							//word/_rels/document.xml.rels
							//word/document.xml
							if( $obj->name == 'word/document.xml' )
							{
								$xml = $zip->getFromIndex( $i );
								// $xml = preg_replace (array("/pic:cnvpr/i", "/pic:nvpicpr/i", "/pic:cNvPicPr/i", "/wp:effectextent/i", "/wp:docpr/i", "/a:graphic/i"), array('image','pic:nvpicpr','pic:cnvpicpr','wp:effectextent','wp:docpr','a:graphic', ), $xml); 
								// $xml = preg_replace (array("/pic:cnvpr/i"), array('img'), $xml); 
								$xml = preg_replace (array("/wp:docpr/i"), array('wpimg'), $xml); 
								// echo $xml;
								libxml_use_internal_errors( true );
								$dom = new DOMDocument( '1.0', 'utf-8' );
								$dom->validateOnParse = false;
								$dom->recover = true;
								$dom->strictErrorChecking = false;
								$dom->loadXML( $xml );
								libxml_clear_errors();

								$xpath = new DOMXPath( $dom );
								$col = $xpath->query( '//w:body/w:p' );
								if( $col->length > 0 )
								{
									$index2 = 1;
									foreach( $col as $row => $node )
									{
										$wpcol = $xpath->query( 'w:r//w:drawing//wpimg', $node ); 
										$image = '';
										if( $wpcol->length > 0 )
										{
											 if( empty( $wpcol->item(0)->getAttribute('name') ) )
											 {
												 $name = strtolower( nv_genpass( 8 ) ).'.png';
											 }
											 else
											 {
												$name = $wpcol->item(0)->getAttribute('name'); 
												 
											 }
											 $image= '[image id="'.$index2.'"]'. $name .'[/image]';
											 $dataImage[$index2]['name'] = $name;
											 $dataImage[$index2]['replace'] = $image;
											 ++$index2;
										}
				 
										$dataQuestion[] = trim( $node->nodeValue . $image );
									}
									 
								}
								

							}
							else
							{
								
								if( preg_match( '([^\s]+(\.(?i)(jpg|jpeg|png|gif|bmp))$)', $obj->name ) )
								{	
									$index = preg_replace('/[^0-9]/', '',basename( $obj->name ) );
									$dataImage[$index]['ext'] = nv_getextension( $obj->name ) ;
									$dataImage[$index]['path'] = base64_encode( $zip->getFromIndex( $i ) );
				  
								}
							}
						}
					} */
 
					if( !empty( $dataImage ) )
					{
						$array_random_alt = array( $lang_module['image_alt1'], $lang_module['image_alt2'], $lang_module['image_alt3'] );
						shuffle($array_random_alt);

						
						MakeDir( $module_upload . '/' . date( 'Y_m' ) );
						$path = NV_UPLOADS_DIR . '/' . $module_upload;
						$currentpath = NV_UPLOADS_DIR . '/' . $module_upload . '/' . date( 'Y_m' );
						
						foreach( $dataImage as $key => $image )
						{
							$fileExt = nv_getextension( $image['name'] );
							if( empty( $fileExt ) )
							{
								$fileExt = $image['ext'];
							}
							$basename = strtolower( nv_genpass( 10 ) . '.' . $fileExt );
 
							$output_file = NV_ROOTDIR . '/' . $currentpath . '/' . $basename;

							if( is_file( $output_file ) )
							{
								$basename = preg_replace('/(.*)(\.[a-z]+)$/i', '\1_'.  strtolower( nv_genpass( 3  ) ) . '\2', basename( $output_file ) );
								$output_file = NV_ROOTDIR . '/' . $currentpath . '/' . $basename;
							}

							$file = fopen($output_file, "wb");

		 
							fwrite($file, base64_decode($image['path']));
							if( is_file( $output_file ) )
							{
								$lu = strlen( NV_ROOTDIR );
								$output_file = substr($output_file, $lu);
								
								$dataImage[$key]['filename'] = '<img alt="'.$array_random_alt[0] . ' ' . $basename.'" src="'. $output_file .'" style="vertical-align: middle;" />';
								
							}
							
							fclose($file); 
							
						}
						
					 
					}
	 
					$count = 0;
					$key = 0;
					$countAnswer = 0;
					foreach( $dataQuestion as $item )
					{
						if( !empty( $item ) )
						{
 
							preg_match_all( '/\\[(.*?)\\\]/', $item, $matchs );
							if(  isset( $matchs[1] ) )
							{
								foreach( $matchs[1] as $replace )
								{
									//$item = str_replace( '\['. $replace .'\]', '<img alt="'.$replace.'" src="http://latex.codecogs.com/gif.latex?'.rawurlencode( '' . $replace ).'" style="vertical-align: middle;" />', $item );
									$item = str_replace( '\['. $replace .'\]', '<span class="math-tex">\('. nv_htmlspecialchars ( $replace ) .'\)</span>', $item );
								}
								unset($matchs);
							}
							preg_match_all( '/\$(.*?)\$/', $item, $matchs );
							if(  isset( $matchs[1] ) )
							{
								foreach( $matchs[1] as $replace ) // \dpi{100} \fn_phv 
								{
									//$item = str_replace( '$'.$replace. '$', '<img alt="'.$replace.'" src="http://latex.codecogs.com/gif.latex?'.rawurlencode( '' . $replace ).'" style="vertical-align: middle;" />', $item );
									$item = str_replace( '$'.$replace. '$', '<span class="math-tex">\('. nv_htmlspecialchars ( $replace ) .'\)</span>', $item );
								}
								unset($matchs);
							}
	
						}
						foreach( $dataImage as $_key => $image )
						{
 
							if( preg_match( '/\[image.*\](.*?)\[\/image\]/ism', $item, $match ) )
							{
								if( $image['replace'] == $match[0] )
								{
									$item = str_replace( $image['replace'], $image['filename'], $item);
								}
 
							}
							
						}
 
						if( $count == 0 && !empty( $item ))
						{
							$dataContent[$key]['question'] = $item;
							
							++$count;
						}
						elseif( $count > 0 && $item != '' )
						{	
							
							if( substr($item, 0, 1) == '*' )
							{
								
								$item = trim( substr( $item, 1) );
								$dataContent[$key]['trueanswer'][] = $item;
								$dataContent[$key]['answer'][] = $item;
							}
							elseif( substr($item, 0, 1) != '>' && !isset( $dataContent[$key]['analyzes'] ) )
							{
								$item = trim( $item );
								$dataContent[$key]['answer'][] = $item;
							}
							elseif( substr($item, 0, 1) == '>' || isset( $dataContent[$key]['analyzes'] ) )
							{
								
								if( !isset( $dataContent[$key]['analyzes'] ) )
								{
									$item = trim( substr( $item, 1) );
								}
								$dataContent[$key]['analyzes'] = $dataContent[$key]['analyzes'] . '<br>' . $item;
							}
							
							
							++$countAnswer;
							++$count;
						}
						else 
						{
							$count = 0;	
							++$key;				
						}
					}
					
				}
			}
 
		 
			if( !empty( $dataContent ) && empty( $error ))
			{
			
				foreach( $dataContent as $key => $item )
				{
					
					
					$data['status'] = 1;
					$data['question'] = $item['question'];
					$data['analyzes'] = isset( $item['analyzes'] ) ? $item['analyzes'] : '';
					
					$answers = array();
					$trueanswer = array();
					$newkey = 0;
					foreach ( $item['answer'] as $answer )
					{
						++$newkey;			
						$answers[$newkey] = $answer;
						foreach( $item['trueanswer'] as $_trueanswer )
						{
							if( $_trueanswer == $answer )
							{
								$trueanswer[] = $newkey;
							}
							
						}
					}
	 
					$data['answers'] = serialize( $answers );

					$data['trueanswer'] = ( $trueanswer ) ? implode( ',', $trueanswer ) : '';
				
					$checkExist = $db->query( 'SELECT COUNT(*) FROM ' . TABLE_ONLINETEST_NAME . '_question WHERE question=' . $db->quote( $data['question'] ) )->fetchColumn();
				
					if( empty( $checkExist ) )
					{
		 
						try
						{
							$stmt = $db->prepare( 'INSERT INTO ' . TABLE_ONLINETEST_NAME . '_question_import SET 
								category_id=' . intval( $data['category_id'] ) . ',
								level_id=' . intval( $data['level_id'] ) . ',
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
							$stmt->bindParam( ':answers', $data['answers'], PDO::PARAM_STR, strlen(  $data['answers'] ) );
							$stmt->bindParam( ':trueanswer', $data['trueanswer'], PDO::PARAM_STR );
							$stmt->execute();

							if( $data['question_id'] = $db->lastInsertId() )
							{
								nv_insert_logs( NV_LANG_DATA, $module_name, 'Add Question', 'question_id: ' . $data['question_id'], $admin_info['userid'] );

								$nv_Request->set_Session( $module_data . '_success', $lang_module['question_add_success'] );

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
					else if( !empty( $checkExist ) && $data['duplicate'] == 0 )
					{
		 
						try
						{
							$stmt = $db->prepare( 'INSERT INTO ' . TABLE_ONLINETEST_NAME . '_question_import SET 
								category_id=' . intval( $data['category_id'] ) . ',
								level_id=' . intval( $data['level_id'] ) . ',
								user_id=' . intval( $data['user_id'] ) . ',
								user_name=:user_name, 
								question=:question, 
								analyzes=:analyzes, 
								answers=:answers, 
								trueanswer=:trueanswer, 
								status=' . intval( $data['status'] ) . ',
								duplicate=1,
								date_added=' . intval( NV_CURRENTTIME ) );


							$stmt->bindParam( ':user_name', $data['user_name'], PDO::PARAM_STR );
							$stmt->bindParam( ':question', $data['question'], PDO::PARAM_STR, strlen( $data['question'] ) );
							$stmt->bindParam( ':analyzes', $data['analyzes'], PDO::PARAM_STR, strlen( $data['analyzes'] ) );
							$stmt->bindParam( ':answers', $data['answers'], PDO::PARAM_STR, strlen(  $data['answers'] ) );
							$stmt->bindParam( ':trueanswer', $data['trueanswer'], PDO::PARAM_STR );
							$stmt->execute();

							if( $data['question_id'] = $db->lastInsertId() )
							{
								nv_insert_logs( NV_LANG_DATA, $module_name, 'Add Question', 'question_id: ' . $data['question_id'], $admin_info['userid'] );

								$nv_Request->set_Session( $module_data . '_success', $lang_module['question_add_success'] );

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
					 
	 
				}
				

				if( empty( $error ) )
				{

					Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
					die();
				}

			}
		 
		}
 	
	}	
	
	
	$xtpl = new XTemplate( 'question_import.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
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
	$xtpl->assign( 'DATA', $data );
	$xtpl->assign( 'CANCEL', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
	$xtpl->assign( 'TOKEN', md5( $client_info['session_id'] . $global_config['sitekey'] ) );
	$xtpl->assign( 'DEFAULT_FORM_IMPORT', $onlineTestConfig['default_form_import'] );
 
	$xtpl->assign( 'DUPLICATE_CHECKED', ( $data['duplicate'] == 1 ) ? 'checked="checked"': '' );
	 
	if( $onlineTestCategory )
	{
		foreach( $onlineTestCategory as $key => $item )
		{
			$xtitle_i = '';
			if( $item['lev'] > 0 )
			{
				$xtitle_i .= '&nbsp;';
				for( $i = 1; $i <= $item['lev']; $i++ )
				{
					$xtitle_i .= '&nbsp;&nbsp;';
				}
			}
			
			$xtitle_i .= $item['title'];
			
			$xtpl->assign( 'CATEGORY', array(
				'key' => $key,
				'name' => $xtitle_i,
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
		
 
	
	if( isset( $error['warning'] ) )
	{
		$xtpl->assign( 'WARNING', $error['warning'] );
		$xtpl->parse( 'main.error_warning' );
		unset($error['warning']);
	}
	if( $error )
	{
		foreach( $error as $key => $_error )
		{ 
			$xtpl->assign( 'error_' . $key, $_error );
			$xtpl->parse( 'main.error_' . $key );
		}
	}
	
	MakeDir( $module_upload . '/' . date( 'Y_m' ) );
	$path = NV_UPLOADS_DIR . '/' . $module_upload;
	$currentpath = NV_UPLOADS_DIR . '/' . $module_upload . '/' . date( 'Y_m' );
	
	$xtpl->assign( 'PATH', $path );
	$xtpl->assign( 'CURRENTPATH', $currentpath );
 
	
	$xtpl->parse( 'main' );
	$contents = $xtpl->text( 'main' );

	include NV_ROOTDIR . '/includes/header.php';
	echo nv_admin_theme( $contents );
	include NV_ROOTDIR . '/includes/footer.php';
}
 
elseif( ACTION_METHOD == 'add' || ACTION_METHOD == 'edit' )
{
	if( ! nv_function_exists( 'getEditor' ) and file_exists( NV_ROOTDIR . '/' . NV_EDITORSDIR . '/ckeditor/ckeditor.js' ) )
	{
 		$my_head .= '<script type="text/javascript" src="' . NV_BASE_SITEURL . NV_EDITORSDIR . '/ckeditor/ckeditor.js"></script>';

		function getEditor( $textareaname, $val = '', $width = '100%', $height = '450px', $path, $currentpath  )
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
		'category_id' => 0,
		'level_id' => 0,
		'user_id' => $admin_info['userid'],
		'user_name' => $admin_info['username'],
		'question' => '',
		'analyzes' => '',
		'answers' => array(),
		'trueanswer' => '',
		'date_added' => 0,
		'date_modified' => 0,
		'status' => 1,
		'addAnalyzesLang' => $lang_module['add_analyzes'],
		'addAnalyzes' => 0,
		'getcontent' => array(),	  
	);
	$dataContent = array();
	$error = array();

	$data['question_id'] = $nv_Request->get_int( 'question_id', 'get,post', 0 );
	
	if( $data['question_id'] > 0 )
	{
		$data = $db->query( 'SELECT *
		FROM ' . TABLE_ONLINETEST_NAME . '_question_import
		WHERE question_id=' . $data['question_id'] )->fetch();
		$data['old_status'] = $data['status'];
		$trueanswer = explode(',', $data['trueanswer']);
 
		$data['answers'] = unserialize( $data['answers'] );
		foreach( $data['answers'] as $key => $answer )
		{
			$dataContent[$key]= array(
				'answer'=> $answer,
				'trueanswer'=> ( in_array( $key, $trueanswer) ) ? 1 : 0,			
			);
			$data['getcontent'][$key] = 1;
		}
 
		$data['addAnalyzes'] = ( !empty( $data['analyzes'] ) ) ? 1 : 0;
		$data['addAnalyzesLang'] = ( !empty( $data['analyzes'] ) ) ? $lang_module['analyzes_delete'] : $lang_module['add_analyzes'] ;

		
		$caption = $lang_module['question_edit'];
	}
	else
	{
		$caption = $lang_module['question_add'];
	}
	
	
	
	if( $nv_Request->get_int( 'save', 'post' ) == 1 )
	{
 
		$data['category_id'] = $nv_Request->get_int( 'category_id', 'post', 0 );
 		$data['level_id'] = $nv_Request->get_int( 'level_id', 'post', 0 );
 		$data['status'] = $nv_Request->get_int( 'status', 'post', 0 );
 		$data['question'] = $nv_Request->get_editor('question', '', NV_ALLOWED_HTML_TAGS);
		$data['analyzes'] = $nv_Request->get_editor('analyzes', '', NV_ALLOWED_HTML_TAGS);
		$getcontent = $nv_Request->get_typed_array( 'getcontent', 'post', array() );
		$data['addAnalyzes'] = $nv_Request->get_int( 'addAnalyzes', 'post', 0 );
 		
		$trueanswer = array();
		$answers = array();
		
		$newkey = 0;
		
		foreach( $getcontent as $key => $item )
		{
			++$newkey;
			$_answer = $nv_Request->get_editor('answer_' . $key, '', NV_ALLOWED_HTML_TAGS);
			$_trueanswer =  $nv_Request->get_int( 'trueanswer_' . $key, 'post', 0 );
			
			$dataContent[$newkey]= array(
				'answer'=> $_answer,
				'trueanswer'=> $_trueanswer
				
			);
			$answers[$newkey] = $_answer;
			if( $_trueanswer == 1 )
			{
				$trueanswer[] = $newkey;
			}
		}
 
		if( empty( $data['category_id'] ) ) $error['category'] = $lang_module['question_error_category_id'];
		if( empty( $data['level_id'] ) ) $error['level'] = $lang_module['question_error_level_id'];
		if ( trim( strip_tags( $data['question'] ) ) == '' and ! preg_match("/\<img[^\>]*alt=\"([^\"]+)\"[^\>]*\>/is", $data['question'] ) ) 
		{
			$error['question'] =  $lang_module['question_error_question'];
	    }
		if( ! empty( $error ) && ! isset( $error['warning'] ) )
		{
			$error['warning'] = $lang_module['question_error_warning'];
		}

		if( empty( $error ) )
		{

			$data['answers'] = serialize( $answers );

			$data['trueanswer'] = ( $trueanswer ) ? implode( ',', $trueanswer ) : '';
			
			$send = '';
			$temp_question_id = $data['question_id'];
			if( $nv_Request->isset_request( 'send', 'post' ) )
			{
				$send.='';
				$data['question_id'] = 0;
			}
			else
			{
				$send.='_import';
			}
			if( $data['question_id'] == 0 )
			{
 
				try
				{
					$stmt = $db->prepare( 'INSERT INTO ' . TABLE_ONLINETEST_NAME . '_question' . $send .' SET 
						category_id=' . intval( $data['category_id'] ) . ',
						level_id=' . intval( $data['level_id'] ) . ',
						user_id=' . intval( $data['user_id'] ) . ',
						user_name=:user_name, 
						question=:question, 
						analyzes=:analyzes, 
						answers=:answers, 
						trueanswer=:trueanswer, 
						status=' . intval( $data['status'] ) . ',
						date_added=' . intval( NV_CURRENTTIME ) );


					$stmt->bindParam( ':user_name', $data['user_name'], PDO::PARAM_STR );
					$stmt->bindParam( ':question', $data['question'], PDO::PARAM_STR, strlen(  $data['question'] ) );
					$stmt->bindParam( ':analyzes', $data['analyzes'], PDO::PARAM_STR, strlen(  $data['analyzes'] ) );
					$stmt->bindParam( ':answers', $data['answers'], PDO::PARAM_STR, strlen(  $data['answers'] ) );
					$stmt->bindParam( ':trueanswer', $data['trueanswer'], PDO::PARAM_STR );
					$stmt->execute();

					if( $data['question_id'] = $db->lastInsertId() )
					{
						
						if( $nv_Request->isset_request( 'send', 'post' ) && $temp_question_id > 0)
						{
							 $db->query( 'DELETE FROM ' . TABLE_ONLINETEST_NAME . '_question_import WHERE question_id = ' . ( int )$temp_question_id );
			
						}
						
						
						nv_insert_logs( NV_LANG_DATA, $module_name, 'Add Question', 'question_id: ' . $data['question_id'], $admin_info['userid'] );
						
						if( $nv_Request->isset_request( 'send', 'post' ) )
						{
							$nv_Request->set_Session( $module_data . '_success', $lang_module['question_move_success'] );
						}
						else
						{
							$nv_Request->set_Session( $module_data . '_success', $lang_module['question_add_success'] );
						}
 	

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

					$stmt = $db->prepare( 'UPDATE ' . TABLE_ONLINETEST_NAME . '_question_import SET 
							category_id=' . intval( $data['category_id'] ) . ',
							level_id=' . intval( $data['level_id'] ) . ',
							user_id=' . intval( $data['user_id'] ) . ',
							user_name=:user_name, 
							question=:question, 
							analyzes=:analyzes, 
							answers=:answers, 
							trueanswer=:trueanswer, 
							status=' . intval( $data['status'] ) . ',
							date_modified=' . intval( NV_CURRENTTIME ) . '
							WHERE question_id=' . $data['question_id'] );
					
					$stmt->bindParam( ':user_name', $data['user_name'], PDO::PARAM_STR );
					$stmt->bindParam( ':question', $data['question'], PDO::PARAM_STR, strlen(  $data['question'] ) );
					$stmt->bindParam( ':analyzes', $data['analyzes'], PDO::PARAM_STR, strlen(  $data['analyzes'] ) );
					$stmt->bindParam( ':answers', $data['answers'], PDO::PARAM_STR, strlen(  $data['answers'] ) );
					$stmt->bindParam( ':trueanswer', $data['trueanswer'], PDO::PARAM_STR );
					$stmt->execute();
					
					if( $stmt->rowCount() )
					{
						
						if( $data['contribute']== 1 && $data['old_status'] == 0 )
						{
							$db->query( 'INSERT INTO ' . TABLE_ONLINETEST_NAME . '_point (userid, point) VALUES ('. intval( $data['user_id'] ) .', '. intval( $onlineTestConfig['bonus_score'] ) .') ON DUPLICATE KEY UPDATE point = point + '. intval( $onlineTestConfig['bonus_score'] ) );
							
							$db->query( 'UPDATE ' . TABLE_ONLINETEST_NAME . '_question_import SET contribute=2 WHERE question_id=' . $data['question_id'] );
		 
						}
						
						nv_insert_logs( NV_LANG_DATA, $module_name, 'Edit Question: ' . $data['question_id'], $admin_info['userid'] );

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
			
			Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
			die();
		}

	}
	$_question = $data['question'];
	$data['question'] = htmlspecialchars( nv_editor_br2nl( $data['question'] ) );

	if( nv_function_exists( 'getEditor' ) && isHtml( $_question ) )
	{
		$data['question'] = getEditor( 'question', $data['question'], '100%', '100px', '', '' );
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
			$data['analyzes'] = getEditor( 'analyzes', $data['analyzes'], '100%', '100px', '', '' );
		}
		else
		{
			$data['analyzes'] = '<textarea style="width: 100%;height:32px" name="analyzes" id="analyzes" cols="20" rows="2" style="height:100px" class="form-control">' . $data['analyzes'] . '</textarea>';
		}
	}
	
	$data['css_hide'] = ( $data['addAnalyzes'] == 1 ) ? 'shows' : 'hides';

	
	$xtpl = new XTemplate( 'question_temp_add.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
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
	$xtpl->assign( 'CAPTION', $caption );
	$xtpl->assign( 'DATA', $data );
	$xtpl->assign( 'CANCEL', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
	$xtpl->assign( 'TOKEN', md5( $client_info['session_id'] . $global_config['sitekey'] ) );
	
	if( $data['addAnalyzes'] == 1 )
	{
		
		$xtpl->assign( 'ANALYZESS', $data['analyzes'] );
		$xtpl->parse( 'main.analyzes' );
		
	}
	 
	if( $onlineTestCategory )
	{
		foreach( $onlineTestCategory as $key => $item )
		{
			$xtitle_i = '';
			if( $item['lev'] > 0 )
			{
				$xtitle_i .= '&nbsp;';
				for( $i = 1; $i <= $item['lev']; $i++ )
				{
					$xtitle_i .= '&nbsp;&nbsp;';
				}
			}
			
			$xtitle_i .= $item['title'];
			
			$xtpl->assign( 'CATEGORY', array(
				'key' => $key,
				'name' => $xtitle_i,
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
	for( $key=1; $key <= $countAnswers; ++$key )
	{
		$_answer = $answer = isset( $dataContent[$key]['answer'] ) ? $dataContent[$key]['answer'] : '';

		$answer = htmlspecialchars( nv_editor_br2nl( $answer ) );

		if( nv_function_exists( 'getEditor' ) && isHtml( $_answer ) )
		{
			$answer = getEditor( 'answer_' . $key, $answer, '100%', '100px', '', '' );

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
		unset($error['warning']);
	}
	if( $error )
	{
		foreach( $error as $key => $_error )
		{ 
			$xtpl->assign( 'error_' . $key, $_error );
			$xtpl->parse( 'main.error_' . $key );
		}
	}
	
	MakeDir( $module_upload . '/' . date( 'Y_m' ) );
	$path = NV_UPLOADS_DIR . '/' . $module_upload;
	$currentpath = NV_UPLOADS_DIR . '/' . $module_upload . '/' . date( 'Y_m' );
	
	$xtpl->assign( 'PATH', $path );
	$xtpl->assign( 'CURRENTPATH', $currentpath );
	
	if( defined( 'NV_IS_ADMIN' ) )
	{		
		if( empty( $path ) and empty( $currentpath ) )
		{
			$path = NV_UPLOADS_DIR;
			$currentpath = NV_UPLOADS_DIR;

			if( ! empty( $module_upload ) and file_exists( NV_UPLOADS_REAL_DIR . '/' . $module_upload . '/' . date( 'Y_m' ) ) )
			{
				$currentpath = NV_UPLOADS_DIR . '/' . $module_upload . '/' . date( 'Y_m' );
				$path = NV_UPLOADS_DIR . '/' . $module_upload;
			}
			elseif( ! empty( $module_upload ) and file_exists( NV_UPLOADS_REAL_DIR . '/' . $module_upload ) )
			{
				$currentpath = NV_UPLOADS_DIR . '/' . $module_upload;
			}
		}

		if( ! empty( $admin_info['allow_files_type'] ) )
		{
			$xtpl->assign( 'filebrowserUploadUrl', "filebrowserUploadUrl: '" . NV_BASE_SITEURL . NV_ADMINDIR . "/index.php?" . NV_NAME_VARIABLE . "=upload&" . NV_OP_VARIABLE . "=upload&editor=ckeditor&path=" . $currentpath . "'," );
			$xtpl->parse( 'main.filebrowserUploadUrl' );
		}

		if( in_array( 'images', $admin_info['allow_files_type'] ) )
		{
 
			$xtpl->assign( 'filebrowserImageUploadUrl', "filebrowserImageUploadUrl: '" . NV_BASE_SITEURL . NV_ADMINDIR . "/index.php?" . NV_NAME_VARIABLE . "=upload&" . NV_OP_VARIABLE . "=upload&editor=ckeditor&path=" . $currentpath . "&type=image'," );
			$xtpl->parse( 'main.filebrowserImageUploadUrl' );
		
		}

		if( in_array( 'flash', $admin_info['allow_files_type'] ) )
		{
			$xtpl->assign( 'filebrowserFlashUploadUrl', "filebrowserFlashUploadUrl: '" . NV_BASE_SITEURL . NV_ADMINDIR . "/index.php?" . NV_NAME_VARIABLE . "=upload&" . NV_OP_VARIABLE . "=upload&editor=ckeditor&path=" . $currentpath . "&type=flash'," );
			$xtpl->parse( 'main.filebrowserFlashUploadUrl' );
		}
		
		$xtpl->assign( 'filebrowserBrowseUrl', "filebrowserBrowseUrl: '" . NV_BASE_SITEURL . NV_ADMINDIR . "/index.php?" . NV_NAME_VARIABLE . "=upload&popup=1&path=" . $path . "&currentpath=" . $currentpath . "'," );
		$xtpl->assign( 'filebrowserImageBrowseUrl', "filebrowserImageBrowseUrl: '" . NV_BASE_SITEURL . NV_ADMINDIR . "/index.php?" . NV_NAME_VARIABLE . "=upload&popup=1&type=image&path=" . $path . "&currentpath=" . $currentpath . "'," );
		$xtpl->assign( 'filebrowserFlashBrowseUrl', "filebrowserFlashBrowseUrl: '" . NV_BASE_SITEURL . NV_ADMINDIR . "/index.php?" . NV_NAME_VARIABLE . "=upload&popup=1&type=flash&path=" . $path . "&currentpath=" . $currentpath . "'," );

	}
	
	
	$xtpl->parse( 'main' );
	$contents = $xtpl->text( 'main' );

	include NV_ROOTDIR . '/includes/header.php';
	echo nv_admin_theme( $contents );
	include NV_ROOTDIR . '/includes/footer.php';
}

/*show list question*/

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
if( $data['user_name'] )
{
	$implode[] = 'user_name LIKE \'%' . $db_slave->dblikeescape( $data['user_name'] ) . '%\'';
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

if( $date_from && $date_to )
{
	$implode[] = 'date_added BETWEEN ' . intval( $date_from ) . ' AND ' . intval( $date_to );
}

$sql = TABLE_ONLINETEST_NAME . '_question_import';

if( $implode )
{
	$sql .= ' WHERE '  . implode( ' AND ', $implode );
}

$sort = $nv_Request->get_string( 'sort', 'get', '' );

$order = $nv_Request->get_string( 'order', 'get' ) == 'asc' ? 'asc' : 'desc';

$sort_data = array(
	'question',
	'category_id',
	'level_id',
	'user_name',
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

$base_url = NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=' . $sort . '&amp;order=' . $order . '&amp;per_page=' . $onlineTestConfig['perpage'];

$db->sqlreset()->select( '*' )->from( $sql )->limit( $onlineTestConfig['perpage'] )->offset( ( $page - 1 ) * $onlineTestConfig['perpage'] );

$result = $db->query( $db->sql() );

$dataContent = array();
while( $rows = $result->fetch() )
{
	$dataContent[] = $rows;
}

$xtpl = new XTemplate( 'question_temp.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
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
$xtpl->assign( 'ADD_NEW', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&amp;action=add' );
$xtpl->assign( 'IMPORT', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&amp;action=import' );

$xtpl->assign( 'DATA', $data );

$order2 = ( $order == 'asc' ) ? 'desc' : 'asc';

$xtpl->assign( 'URL_QUESTION', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=question&amp;order=' . $order2 . '&amp;per_page=' . $onlineTestConfig['perpage'] );
$xtpl->assign( 'URL_CATEGORY', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=category_id&amp;order=' . $order2 . '&amp;per_page=' . $onlineTestConfig['perpage'] );
$xtpl->assign( 'URL_LEVEL', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=level_id&amp;order=' . $order2 . '&amp;per_page=' . $onlineTestConfig['perpage'] );
$xtpl->assign( 'URL_STATUS', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=status&amp;order=' . $order2 . '&amp;per_page=' . $onlineTestConfig['perpage'] );
$xtpl->assign( 'URL_DATE_ADDED', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=date_added&amp;order=' . $order2 . '&amp;per_page=' . $onlineTestConfig['perpage'] );
$xtpl->assign( 'URL_DATE_MODIFIED', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=date_modified&amp;order=' . $order2 . '&amp;per_page=' . $onlineTestConfig['perpage'] );
$xtpl->assign( 'URL_USER_NAME', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=user_name&amp;order=' . $order2 . '&amp;per_page=' . $onlineTestConfig['perpage'] );

$xtpl->assign( 'QUESTION_ORDER', ( $sort == 'question' ) ? 'class="' . $order2 . '"' : '' );
$xtpl->assign( 'CATEGORY_ORDER', ( $sort == 'category_id' ) ? 'class="' . $order2 . '"' : '' );
$xtpl->assign( 'LEVEL_ORDER', ( $sort == 'level_id' ) ? 'class="' . $order2 . '"' : '' );
$xtpl->assign( 'STATUS_ORDER', ( $sort == 'status' ) ? 'class="' . $order2 . '"' : '' );
$xtpl->assign( 'DATE_ADDED_ORDER', ( $sort == 'date_added' ) ? 'class="' . $order2 . '"' : '' );
$xtpl->assign( 'DATE_MODIFIED_ORDER', ( $sort == 'date_modified' ) ? 'class="' . $order2 . '"' : '' );
$xtpl->assign( 'USER_NAME_ORDER', ( $sort == 'user_name' ) ? 'class="' . $order2 . '"' : '' );

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
		$xtitle_i = '';
		if( $item['lev'] > 0 )
		{
			$xtitle_i .= '&nbsp;';
			for( $i = 1; $i <= $item['lev']; $i++ )
			{
				$xtitle_i .= '&nbsp;&nbsp;';
			}
		}
		
		$xtitle_i .= $item['title'];
		$xtpl->assign( 'CATEGORY', array(
			'key' => $key,
			'name' => $xtitle_i,
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
		$item['category'] = ( isset( $onlineTestCategory[$item['category_id']] ) ) ? $onlineTestCategory[$item['category_id']]['title'] : '';
		$item['level_name'] = ( isset( $onlineTestLevel[$item['level_id']] ) ) ? $onlineTestLevel[$item['level_id']]['title'] : '';
		$item['duplicate_checked'] = ( $item['duplicate'] ) ? 'checked="checked"' : '';
		$item['status_checked'] = ( $item['status'] ) ? 'checked="checked"' : '';
		$item['date_added'] = nv_date( 'd/m/Y', $item['date_added'] );
		$item['date_modified'] = nv_date( 'd/m/Y', $item['date_modified'] );
		$item['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $item['question_id'] );
		$item['edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&amp;action=edit&token=' . $item['token'] . '&question_id=' . $item['question_id'];
		$xtpl->assign( 'LOOP', $item );
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
