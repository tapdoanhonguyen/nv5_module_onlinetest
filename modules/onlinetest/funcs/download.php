<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */

if( ! defined( 'NV_IS_MOD_TEST' ) ) die( 'Stop!!!' );

if( ACTION_METHOD == 'download' )
{
	$file_name = $nv_Request->get_string( 'file', 'get', '' );

	$file_path = NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . $file_name;

	if( file_exists( $file_path ) )
	{
		header( 'Content-Description: File Transfer' );
		header( 'Content-Type:application/msword' );
		header( 'Content-Disposition: attachment; filename=' . $file_name );
		header( 'Content-Transfer-Encoding: binary' );
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate' );
		header( 'Pragma: public' );
		header( 'Content-Length: ' . filesize( $file_path ) );
		readfile( $file_path );
		// ob_clean();
		flush();
		nv_deletefile( $file_path );
		exit();
	}
	else
	{
		die( 'File not exists !' );
	}
}

if( ACTION_METHOD == 'analyzed' )
{

	$type_exam_id = $nv_Request->get_int( 'type_exam_id', 'get', 0 );
	$token = $nv_Request->get_string( 'token', 'get', '' );
	if( $token != md5( $nv_Request->session_id . $global_config['sitekey'] . $type_exam_id ) )
	{
		nv_jsonOutput( array( 'error' => $lang_module['error_security'] ) );
	}

	$dataContent = $db->query( 'SELECT analyzed FROM ' . TABLE_ONLINETEST_NAME . '_type_exam WHERE type_exam_id=' . intval( $type_exam_id ) )->fetch();

	if( ! empty( $dataContent['analyzed'] ) and file_exists( NV_UPLOADS_REAL_DIR . '/' . $module_upload . '/' . $dataContent['analyzed'] ) )
	{

		//Download file
		$download = new NukeViet\Files\Download( NV_UPLOADS_REAL_DIR . '/' . $module_upload . '/' . $dataContent['analyzed'], basename( $dataContent['analyzed'] ) );
		$download->download_file();
		exit();
	}
	else
	{
		echo 'file not exist !';
	}

	die();

}
if( ACTION_METHOD == 'download1' )
{

	$type_exam_id = $nv_Request->get_int( 'type_exam_id', 'get', 0 );
	$token = $nv_Request->get_string( 'token', 'get', '' );
	if( $token != md5( $nv_Request->session_id . $global_config['sitekey'] . $type_exam_id ) )
	{
		nv_jsonOutput( array( 'error' => $lang_module['error_security'] ) );
	}

	$dataContent = $db->query( 'SELECT pdf FROM ' . TABLE_ONLINETEST_NAME . '_type_exam WHERE type_exam_id=' . intval( $type_exam_id ) )->fetch();

	if( ! empty( $dataContent['pdf'] ) and file_exists( NV_UPLOADS_REAL_DIR . '/' . $module_upload . '/' . $dataContent['pdf'] ) )
	{

		//Download file
		$download = new NukeViet\Files\Download( NV_UPLOADS_REAL_DIR . '/' . $module_upload . '/' . $dataContent['pdf'], basename( $dataContent['pdf'] ) );
		$download->download_file();
		exit();
	}
	else
	{
		echo 'file not exist !';
	}

	die();

}
if( ACTION_METHOD == 'is_download' )
{

	$history_id = $nv_Request->get_int( 'history_id', 'post', 0 );
	$token = $nv_Request->get_string( 'token', 'post', '' );
	// if( $token != md5( $nv_Request->session_id . $global_config['sitekey'] . $history_id ) )
	// {
		// nv_jsonOutput( array( 'error' => $lang_module['error_security'] ) );
	// }

	$xtpl = new XTemplate( 'ThemeOnlineTestTemplate.tpl', NV_ROOTDIR . "/themes/" . $module_info['template'] . "/modules/" . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'MODFILE', $module_file );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );

	// if( ! defined( 'NV_IS_ADMIN' ) )
	// {
		// $download = ' tx.allow_download=1 AND h.is_deleted=0 AND h.userid = ' . intval( $user_info['userid'] ) . ' AND ';
	// }
	// else
	// {
		// $download = '';
	// }

	$result = $db->query( '
			SELECT h.*, u.username, tx.group_exam_id, tx.group_exam_list, tx.title, tx.code, tx.config, tx.allow_show_answer, tx.allow_download, tx.pdf, tx.date_added FROM ' . TABLE_ONLINETEST_NAME . '_history h 
			LEFT JOIN ' . TABLE_ONLINETEST_NAME . '_type_exam tx ON (h.type_exam_id = tx.type_exam_id) 
			LEFT JOIN ' . NV_USERS_GLOBALTABLE . ' u ON (h.userid = u.userid) 
			WHERE h.history_id=' . intval( $history_id ) );

	$dataContent = $result->fetch();

	$result->closeCursor();

	$xtpl->assign( 'DATA', $dataContent );
	$linkin = 1;
	if( $dataContent['type_id'] == 0 || $dataContent['type_id'] == 1 )
	{

		$list_question = unserialize( $dataContent['question'] );
		$listQuestion = array();
		foreach( $list_question as $question_id => $data )
		{
			$listQuestion[] = $question_id;
		}
						

		$dataQuestion = array();
		if( ! empty( $listQuestion ) )
		{
			$result = $db->query( 'SELECT * FROM ' . TABLE_ONLINETEST_NAME . '_question WHERE question_id IN (' . implode( ',', $listQuestion ) . ')' );
			while( $item = $result->fetch() )
			{
				$item['answers'] = @unserialize( $item['answers'] );
				$item['trueanswer'] = explode( ',', $item['trueanswer'] );
				$total = 0;
				$number = 1;
				foreach( $item['answers'] as $key => $name )
				{
					$namex = str_replace( array(
						'\(',
						'\)',
						'\[',
						'\]',
						'$$',
						'$',
						'\frac',
						'\left',
						'\frac',
						'\mathrm',
						'\over' ), //11
						array(
						'',
						'',
						'',
						'',
						'',
						'',
						'1',
						'1',
						'1',
						'1',
						'1' ), $name ); //11
					$total = $total + nv_strlen( trim( $namex ) );
					++$number;
				}
				$item['total'] = floor( $total / $number );
				$dataQuestion[$item['question_id']] = $item;
				unset( $total, $number );
			}
			$result->closeCursor();

		}
		
		
		$trueanswer = array();
		if( ! empty( $dataQuestion ) )
		{
			$number = 1;
			foreach( $list_question as $_question_id => $item )
			{

				$question = isset( $dataQuestion[$_question_id] ) ? $dataQuestion[$_question_id] : array();
				
				if( !empty( $question ) )
				{
					$question['number'] = $number;

					$question['question'] = str_replace( array( '\(', '\)' ), array(
						' $',
						'$ ',
						), $question['question'] );
					$question['question'] = strip_tags( $question['question'], '<img><br>' );

					preg_match_all( '/< *img[^>]*src *= *["\']?([^"\']*).*?>/i', $question['question'], $images );

					if( isset( $images[1] ) )
					{

						foreach( $images[1] as $key => $img )
						{

							if( preg_match( '/latex.codecogs.com.+latex\?(.*)/i', urldecode( htmlspecialchars_decode( $img ) ), $match ) )
							{
								$math = '';
								if( isset( $match[1] ) && ! empty( $match[1] ) )
								{
									$math = '$' . str_replace( '&plus;', '+', $match[1] ) . '$';
								}
								$question['question'] = str_replace( $images[0][$key], $math, $question['question'] );
							}
							else
							{
								if( nv_is_url( $img ) )
								{
									$imageData = base64_encode( file_get_contents( $img ) );
								}
								else
								{
									$imagepath = NV_ROOTDIR . $img;

									$imageData = base64_encode( file_get_contents( $imagepath ) );
								}

								$src = 'data:' . nv_get_mime_type( $imagepath ) . ';base64,' . $imageData;

								$newimage = str_replace( $img, $src, $images[0][$key] );

								$question['question'] = str_replace( $images[0][$key], $newimage, $question['question'] );

							}

						}

					}
					unset( $images );

					// $question['question'] = nv_unhtmlspecialchars( $question['question'] );
					$xtpl->assign( 'QUESTION', $question );
					$count = 0;
					$array_key_last = array_keys( $question['answers'] )[count( $question['answers'] ) - 1];
					$_trueanswer = array();
					foreach( $item['sys_answers'] as $key )
					{
						
						$sys_answers = $question['answers'];
						$name = isset( $sys_answers[$key] ) ? $sys_answers[$key] : '';
						$titleABC = ( $onlineTestTitleFirst[$count] ) ? $onlineTestTitleFirst[$count] : $count;

						/*
						$tabspace = '';
						if( $item['total'] < 6 )
						{
						
						$tabspace.= '<span style=\'mso-tab-count:1\'>	</span>';
						$tabspace.= '<span style=\'mso-tab-count:1\'>	</span>';
						if( ($count+1) % 2 == 0 )
						{
						$tabspace.= '<span style=\'mso-tab-count:1\'>	</span>';
						} 
						
						}
						elseif( $item['total'] < 8 )
						{
						
						$tabspace.= '<span style=\'mso-tab-count:1\'>	</span>';
						$tabspace.= '<span style=\'mso-tab-count:1\'>	</span>';
						
						
						}
						elseif( $item['total'] <= 10 )
						{
						$tabspace.= '<span style=\'mso-tab-count:1\'>	</span>';
						if( ($count+1) % 2 == 0 )
						{
						$tabspace.= '<span style=\'mso-tab-count:1\'>	</span>';
						}
						
						if( $number == 1)
						{
						// var_dump($tabspace);
						}
						
						// if( ($count+1) % 2 == 0 )
						// {
						// $tabspace.= '<br>';
						// }
						
						}
						elseif( $countName <= 40 )
						{
						if( $count  % 2 == 0 )
						{
						// $tabspace.= '<span style=\'mso-tab-count:1\'>   </span>';
						// $tabspace.= '<span style=\'mso-tab-count:1\'>   </span>';
						}
						if( ($count+1) % 2 == 0 )
						{
						$tabspace.= '<br>';
						}
						
						}
						elseif( $countName < 100 )
						{
						$tabspace = '<span style=\'mso-tab-count:1\'>   </span>';
						
						} */

						
						$name = strip_tags( $name, '<img><br>' );
						// $name = nv_unhtmlspecialchars( $name );

						preg_match_all( '/< *img[^>]*src *= *["\']?([^"\']*).*?>/i', $name, $images );

						if( isset( $images[1] ) )
						{

							foreach( $images[1] as $key => $img )
							{

								if( preg_match( '/latex.codecogs.com.+latex\?(.*)/i', urldecode( htmlspecialchars_decode( $img ) ), $match ) )
								{
									$math = '';
									if( isset( $match[1] ) && ! empty( $match[1] ) )
									{
										$math = '$' . str_replace( '&plus;', ' + ', $match[1] ) . '$';
									}
									$name = str_replace( $images[0][$key], $math, $name );
								}
								else
								{
									if( nv_is_url( $img ) )
									{
										$imageData = base64_encode( file_get_contents( $img ) );
									}
									else
									{
										$imagepath = NV_ROOTDIR . $img;

										$imageData = base64_encode( file_get_contents( $imagepath ) );
									}

									$src = 'data:' . nv_get_mime_type( $imagepath ) . ';base64,' . $imageData;

									$newimage = str_replace( $img, $src, $images[0][$key] );

									$name = str_replace( $images[0][$key], $newimage, $name );

								}

							}

						}
 
						if( in_array( $key, $question['trueanswer'] ) )
						{
							$_trueanswer[] = $titleABC;
						}
						$xtpl->assign( 'ANSWER', array(
							'key' => $key,
							'title' => $titleABC,
							'name' => str_replace( array( '\(', '\)' ), array(
								' $',
								'$ ',
								), $name ),
							'tabspace' => ( $array_key_last != $key ) ? $tabspace : '' ) );

						$xtpl->parse( 'main.question.answer' );
						++$count;
					}
					
				 
					
					if( $_trueanswer )
					{
						$trueanswer[$number] = implode( '', $_trueanswer );

					}
					else
					{
						$trueanswer[$number] = '';
		
					}
					unset( $count );

					$xtpl->parse( 'main.question' );
					++$number;
					
				}
				
				
			}

		}
	
		if( $dataContent['allow_show_answer'] == 1 )
		{
			if( $trueanswer )
			{
				foreach( $trueanswer as $number => $answer )
				{
					$xtpl->assign( 'NUMBER',$number);
					$xtpl->assign( 'ANSWER',$answer);
					$xtpl->parse( 'main.trueanswer.loop' );
				}
				$xtpl->parse( 'main.trueanswer' );
			}
			
		}
		
		
		$xtpl->parse( 'main' );
		$html = $xtpl->text( 'main' );

		require_once NV_ROOTDIR . '/modules/' . $module_file . '/html_to_doc.inc.php';

		$htmltodoc = new HTML_TO_DOC();

		$file_name = mb_strtolower( str_replace( '-', '_', change_alias( $dataContent['title'] ) ) ) . '.doc';

		$file_path = NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . $file_name;

		unlink( $file_path );

		$htmltodoc->createDoc( $html, $file_path );
		
		unset( $htmltodoc, $html, $dataContent, $dataQuestion );

		$link = NV_BASE_SITEURL . "index.php?" . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=download&action=download&file=' . $file_name;
		$linkin = 1;
	}
	else
	{
		if( ! nv_is_url( $dataContent['pdf'] ) )
		{
			$dataContent['type_exam_token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $dataContent['type_exam_id'] );

			$link = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=download', true ) . '?action=download1&type_exam_id=' . $dataContent['type_exam_id'] . '&token=' . $dataContent['type_exam_token'];
			$linkin = 1;
		}
		else
		{
			$link = $dataContent['pdf'];
			$linkin = 0;
		}
	}
	nv_jsonOutput( array( 'link' => $link, 'linkin' => $linkin ) );

}
die();
