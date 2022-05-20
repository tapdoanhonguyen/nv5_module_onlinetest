<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );
require_once  NV_ROOTDIR . '/modules/' . $module_file . '/phpoffice/autoload.php';

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
if( ACTION_METHOD == 'download' )
{
	$file_name = $nv_Request->get_string( 'file_name', 'get', '' );
 
	$file_path = NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . $file_name;
	
	if( file_exists( $file_path ) )
	{
		header( 'Content-Description: File Transfer' );
		header( 'Content-Type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
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
	}else
	{
		die('File not exists !');
	}
}
 
elseif( ACTION_METHOD == 'is_download' )
{
	ini_set( 'memory_limit', '512M' );

	set_time_limit( 0 );
	
	$token = $nv_Request->get_title( 'token', 'post', '', '' );
	
	if( $token == md5( $nv_Request->session_id . $global_config['sitekey'] ) )
	{
		$data['title'] = $nv_Request->get_string( 'title', 'get', '' );
		$data['code'] = $nv_Request->get_string( 'code', 'get', '' );
		$data['username'] = $nv_Request->get_string( 'username', 'get', '' );
		$data['type'] = $nv_Request->get_int( 'type', 'get', 1 );
		$data['group'] = $nv_Request->get_int( 'group', 'get', 0 );

		$implode = array();

		if( $data['title'] )
		{
			$implode[] = 'tx.title LIKE \'%' . $db->dblikeescape( $data['title'] ) . '%\'';
		}
		if( $data['code'] )
		{
			$implode[] = 'tx.code LIKE \'%' . $db->dblikeescape( $data['code'] ) . '%\'';
		}
		if( $data['username'] )
		{
			if( $data['type'] == 1 )
			{
				$implode[] = 'u.username LIKE \'%' . $db->dblikeescape( $data['username'] ) . '%\'';
			}
			else
			{
				$implode[] = 'CONCAT(u.first_name,\' \', u.last_name) LIKE \'%' . $db->dblikeescape( $data['username'] ) . '%\'';
			}
		}
		 
		if( !empty( $data['group'] ) )
		{
			$implode[] = 'tx.group_user LIKE \'%' . $db->dblikeescape( ',' . $data['group'] .',' ) . '%\'';
		}
		$sql = TABLE_ONLINETEST_NAME . '_history h 
		LEFT JOIN ' . TABLE_ONLINETEST_NAME . '_type_exam tx ON (h.type_exam_id = tx.type_exam_id) 
		LEFT JOIN ' . NV_USERS_GLOBALTABLE . ' u ON (h.userid = u.userid)';
		if( $implode )
		{
			$sql .= ' WHERE ' . implode( ' AND ', $implode );
		}
		$sql .= ' ORDER BY h.test_time DESC';

		$db->sqlreset()->select( 'h.*, u.userid, u.username, u.email, CONCAT(u.last_name,\' \',u.first_name) AS full_name, tx.title, tx.code, tx.group_user' )->from( $sql );
 
		$result = $db->query( $db->sql() );
		
		if( $result->rowCount() )
		{
 
			$data_type_exam_id = array();
			$listUsers = array();
			$data_array = array();
			$dataContent = array();
			$stt = 0;
			while( $row = $result->fetch() )
			{
				 
				// $username = nv_show_name_user( $row['first_name'], $row['last_name'], $row['username'] );
				
				if( !empty( $row['group_user'] ) )
				{
					$row['group_user'] = array_map('trim', explode(',', $row['group_user']));
					$row['group_user'] = array_unique( array_filter( $row['group_user'] ) );
					$listUsers[] = $row['userid'];
				}
				else
				{
					$row['group_user'] = array();
				}
				$row['time_do_test'] = str_pad( ceil($row['time_do_test']/60), 2, '0', STR_PAD_LEFT );	
				$row['score'] =  round($row['score'], 1);
				
				$data_array['stt'] = ++$stt;
				$data_array['userid'] = nv_unhtmlspecialchars( $row['userid'] );
				$data_array['code'] = nv_unhtmlspecialchars( $row['code'] );
				$data_array['phone'] = nv_unhtmlspecialchars( $row['phone'] );
				$data_array['username'] = nv_unhtmlspecialchars( $row['username'] );
				$data_array['full_name'] = nv_unhtmlspecialchars( $row['full_name'] );
				$data_array['email'] = nv_unhtmlspecialchars( $row['email'] );
				$data_array['score'] = nv_unhtmlspecialchars( number_format((float)$row['score'], 1, '.', '') . '/' . $row['max_score'] );				 
				$data_array['test_time'] = nv_unhtmlspecialchars( date( 'd/m/Y H:i', $row['test_time'] ) );
				
				$timedotest = $row['time_do_test'] . '/'. $row['time'] . ' '. $lang_module['share_minutes'];
				
				$data_array['timedotest'] = $timedotest;
				$data_array['typeexam'] = '';
				$data_array['group_user'] = $row['group_user'] ;
				$data_array['group'] = '';
				$data_array['type_exam_id'] = $row['type_exam_id'];
				
				$data_type_exam_id[]= $row['type_exam_id'];

				$dataContent[] = $data_array;	
			}
			$result->closeCursor();
			
			$UsersGroups = array();
			if( !empty( $listUsers ) )
			{
				$listUsers = array_unique( array_filter( $listUsers ) );
				$result = $db->query( 'SELECT gu.group_user_id, gu.title , gul.userid FROM ' . TABLE_ONLINETEST_NAME . '_group_user gu LEFT JOIN ' . TABLE_ONLINETEST_NAME . '_group_user_list gul ON( gu.group_user_id = gul.group_user_id) WHERE gul.userid IN ('. implode(',', $listUsers ) .')');
				$group_user_id = array();		
				while( $item = $result->fetch() )
				{
					$UsersGroups[$item['userid']][] = $item;
					
				}
				$result->closeCursor();
			}
			
			$data_type_exam_id = array_unique( $data_type_exam_id );
			$typeExam = array();
			if( !empty( $data_type_exam_id ) )
			{
				$result = $db->query( 'SELECT type_exam_id, title FROM ' . TABLE_ONLINETEST_NAME . '_type_exam WHERE type_exam_id IN ('. implode(',', $data_type_exam_id ) .')');

				while( $item = $result->fetch() )
				{
					$typeExam[$item['type_exam_id']] = $item['title'];
					
				}
				$result->closeCursor();
			}
			 
			$page_title = 'DANH SÁCH BÀI THI';
			
			$Excel_Cell_Begin = 1; // Dong bat dau viet du lieu
			
			
	 
			$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(NV_ROOTDIR . '/modules/' . $module_file . '/template/mau3.xlsx');
	 
			$worksheet = $spreadsheet->getActiveSheet();
			
			$worksheet->setTitle( $page_title );

			// Set page orientation and size
			$worksheet->getPageSetup()->setOrientation( PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE );
			$worksheet->getPageSetup()->setPaperSize( PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4 );
			$worksheet->getPageSetup()->setHorizontalCentered( true );
	  

			$spreadsheet->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd( 1, $Excel_Cell_Begin );
			
			// Tieu de
			$array_title = array();
			$array_title[] = 'stt';
			$array_title[] = 'code';
			$array_title[] = 'typeexam';
			$array_title[] = 'username';
			$array_title[] = 'full_name';
			$array_title[] = 'email';
			$array_title[] = 'phone';
			$array_title[] = 'group';
			$array_title[] = 'score';
			$array_title[] = 'test_time';
			$array_title[] = 'timedotest';

			$columnIndex = 0;
			foreach( $array_title as $key_lang )
			{
				++$columnIndex;
				$TextColumnIndex = PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex( $columnIndex );
				$worksheet->getColumnDimension( $TextColumnIndex )->setAutoSize( false );
				$worksheet->setCellValue( $TextColumnIndex . $Excel_Cell_Begin, $lang_export[$key_lang] );
			}
			
			// Du lieu
			$array_key_data = array();
			$array_key_data[] = 'stt';
			$array_key_data[] = 'code';
			$array_key_data[] = 'typeexam';
			$array_key_data[] = 'username';
			$array_key_data[] = 'full_name';
			$array_key_data[] = 'email';
			$array_key_data[] = 'phone';
			$array_key_data[] = 'group';
			$array_key_data[] = 'score';
			$array_key_data[] = 'test_time';
			$array_key_data[] = 'timedotest';

			$pRow = $Excel_Cell_Begin;
			foreach( $dataContent as $row )
			{
				$row['typeexam'] = isset( $typeExam[$row['type_exam_id']] ) ? $typeExam[$row['type_exam_id']] : '';
				
				if( isset( $UsersGroups[$row['userid']] ) )
				{
					foreach( $UsersGroups[$row['userid']] as $gru )
					{
						if( in_array( $gru['group_user_id'], $row['group_user'] ) )
						{
							$row['group'] = $gru['title'];
							break;
						}
						
					}
				}
				
				$pRow++;
				$columnIndex2 = 0;
	 
				foreach( $array_key_data as $key_data )
				{
 
					++$columnIndex2;
					$TextColumnIndex = PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex( $columnIndex2 );
					$worksheet->setCellValue( $TextColumnIndex . $pRow, $row[$key_data] );
					 
				}
			}
	 

			$highestRow = $worksheet->getHighestRow(); // Tinh so dong du lieu
			$highestColumn = $TextColumnIndex; // Tinh so cot du lieu
			
			
			// $worksheet->setCellValue( 'G' . ( $highestRow + 1 ), '=SUM(G2:G11)' );
			
			
			
			//$objWorksheet->mergeCells('A1:' . $highestColumn . '1');
			// $objWorksheet->setCellValue( 'A1', $page_title );
			//$objWorksheet->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			//$objWorksheet->getStyle('A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

			$spreadsheet->getActiveSheet()->getStyle('A' . $Excel_Cell_Begin . ':' . $highestColumn . $highestRow)->getBorders()->applyFromArray( 
			[ 
				'bottom' => [ 'borderStyle' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => [ 'rgb' => '000000' ] ], 
				'top' => 	[ 'borderStyle' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => [ 'rgb' => '000000' ] ], 
				'left' => [ 'borderStyle' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => [ 'rgb' => '000000' ] ], 
				'right' => 	[ 'borderStyle' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => [ 'rgb' => '000000' ] ], 
			]);


	 
			
			$file_name = $module_name . '_danh_sach_bai_thi_' . date('d_m_Y', NV_CURRENTTIME ) . '.xlsx';
			
			$file_path = NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . $file_name;
			
			header( 'Content-Type: application/vnd.ms-excel' );
			header( 'Content-Disposition: attachment;filename="'. $file_name .'"' );
			header( 'Cache-Control: max-age=0' );

			$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
			$writer->save($file_path);
			
			$link = NV_BASE_ADMINURL . "index.php?" . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=type_exam&action=download&file_name=' . $file_name;  
			
			nv_jsonOutput( array('link'=> $link) );		
			
			
			
			//$objWriter->save( 'php://output' );
			//exit;
		}
		else
		{
			nv_jsonOutput( array('error'=> 'Không tìm thấy dữ liệu') );		
		}
	}
	else
	{
		$json['error'] = $lang_module['type_exam_error_security'];
	}	 
	nv_jsonOutput( $json );		
}

elseif( ACTION_METHOD == 'view' )
{
	$history_id = $nv_Request->get_int( 'history_id', 'get', 0 );

	$token = $nv_Request->get_title( 'token', 'get', '', 1 );

	
	$result = $db->query( '
		SELECT h.*, u.username, u.first_name, u.last_name, tx.* FROM ' . TABLE_ONLINETEST_NAME . '_history h 
		LEFT JOIN ' . TABLE_ONLINETEST_NAME . '_type_exam tx ON (h.type_exam_id = tx.type_exam_id) 
		LEFT JOIN ' . NV_USERS_GLOBALTABLE . ' u ON (h.userid = u.userid) 
		WHERE h.history_id=' . intval( $history_id ) );

	$dataContent = $result->fetch();
	
	if( empty( $dataContent ) )
	{
		Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
		die();
	}
	$dataContent['full_name'] = nv_show_name_user( $dataContent['first_name'], $dataContent['last_name'], $dataContent['username']);
	
	$dataContent['group'] = '';
	if( !empty( $dataContent['group_user'] ) )
	{
		$dataContent['group_user'] = array_map('trim', explode(',', $dataContent['group_user']));
		$dataContent['group_user'] = array_unique( array_filter( $dataContent['group_user'] ) );
		$result = $db->query( 'SELECT gu.group_user_id, gu.title FROM ' . TABLE_ONLINETEST_NAME . '_group_user gu LEFT JOIN ' . TABLE_ONLINETEST_NAME . '_group_user_list gul ON( gu.group_user_id = gul.group_user_id) WHERE gul.userid=' . intval( $dataContent['userid'] ) );
		$group_user_id = array();		
		while( $item = $result->fetch() )
		{
			if( in_array( $item['group_user_id'], $dataContent['group_user'] ) )
			{
				$dataContent['group'] = $item['title'];
				break;
			}
			
		}
		$result->closeCursor();
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
		
		if( $dataContent['group'] )
		{
			$xtpl->parse( 'main.group' );
		}
		
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
			$result = $db->query( 'SELECT * FROM ' . TABLE_ONLINETEST_NAME . '_question WHERE question_id IN ('.implode(',', $listQuestionId ).')');
			
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
		$dataContent['print'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&action=print&history_id=' . $dataContent['history_id'] . '&token=' . $dataContent['token'];
		
		$xtpl->assign( 'DATA', $dataContent );
		if( $dataContent['group'] )
		{
			$xtpl->parse( 'main.group' );
		}
		$xtpl->parse( 'main' );
		$contents = $xtpl->text( 'main' );

		include NV_ROOTDIR . '/includes/header.php';
		echo nv_admin_theme( $contents );
		include NV_ROOTDIR . '/includes/footer.php';
	}
	
}
elseif( ACTION_METHOD == 'print' )
{
	$history_id = $nv_Request->get_int( 'history_id', 'get', 0 );

	$token = $nv_Request->get_title( 'token', 'get', '', 1 );

	
	$result = $db->query( '
		SELECT h.*, u.username, u.first_name, u.last_name, tx.* FROM ' . TABLE_ONLINETEST_NAME . '_history h 
		LEFT JOIN ' . TABLE_ONLINETEST_NAME . '_type_exam tx ON (h.type_exam_id = tx.type_exam_id) 
		LEFT JOIN ' . NV_USERS_GLOBALTABLE . ' u ON (h.userid = u.userid) 
		WHERE h.history_id=' . intval( $history_id ) );

	$dataContent = $result->fetch();
	
	if( empty( $dataContent ) )
	{
		Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
		die();
	}
	$dataContent['full_name'] = nv_show_name_user( $dataContent['first_name'], $dataContent['last_name'], $dataContent['username']);
	
	$dataContent['group'] = '';
	if( !empty( $dataContent['group_user'] ) )
	{
		$dataContent['group_user'] = array_map('trim', explode(',', $dataContent['group_user']));
		$dataContent['group_user'] = array_unique( array_filter( $dataContent['group_user'] ) );
		$result = $db->query( 'SELECT gu.group_user_id, gu.title FROM ' . TABLE_ONLINETEST_NAME . '_group_user gu LEFT JOIN ' . TABLE_ONLINETEST_NAME . '_group_user_list gul ON( gu.group_user_id = gul.group_user_id) WHERE gul.userid=' . intval( $dataContent['userid'] ) );
		$group_user_id = array();		
		while( $item = $result->fetch() )
		{
			if( in_array( $item['group_user_id'], $dataContent['group_user'] ) )
			{
				$dataContent['group'] = $item['title'];
				break;
			}
			
		}
		$result->closeCursor();
	}
	

	
	if( $dataContent['type_id'] == 2 )
	{
		$xtpl = new XTemplate( 'history_print_pdf.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
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
		
		if( $dataContent['group'] )
		{
			$xtpl->parse( 'main.group' );
		}
		
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
		echo nv_admin_theme( $contents, false );
		include NV_ROOTDIR . '/includes/footer.php';
	}

	else
	{
		$xtpl = new XTemplate( 'history_print.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
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
		$xtpl->assign( 'TEMPLATE', $global_config['module_theme'] );
		$xtpl->assign( 'NV_ASSETS_DIR', NV_ASSETS_DIR );
		
		
		$xtpl->assign( 'CANCEL', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
		
		
		
		$questionTest = @unserialize( $dataContent['question'] );
	 
		$listQuestionId = array_keys( $questionTest );
		
		$getLevel = getLevel( $module_name );
		$dataQuestion = array();
		if( $listQuestionId )
		{
			$result = $db->query( 'SELECT * FROM ' . TABLE_ONLINETEST_NAME . '_question WHERE question_id IN ('.implode(',', $listQuestionId ).')');
			
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
		if( $dataContent['group'] )
		{
			$xtpl->parse( 'main.group' );
		}
		$xtpl->parse( 'main' );
		$contents = $xtpl->text( 'main' );

		include NV_ROOTDIR . '/includes/header.php';
		echo $contents;
		include NV_ROOTDIR . '/includes/footer.php';
	}
	
}

/*show list history*/

$page = $nv_Request->get_int( 'page', 'get', 1 );

$data['title'] = $nv_Request->get_string( 'title', 'get', '' );
$data['code'] = $nv_Request->get_string( 'code', 'get', '' );
$data['username'] = $nv_Request->get_string( 'username', 'get', '' );
$data['type'] = $nv_Request->get_int( 'type', 'get', 1 );
$data['group'] = $nv_Request->get_int( 'group', 'get', 0 );

$implode = array();

if( $data['title'] )
{
	$implode[] = 'tx.title LIKE \'%' . $db->dblikeescape( $data['title'] ) . '%\'';
}
if( $data['code'] )
{
	$implode[] = 'tx.code LIKE \'%' . $db->dblikeescape( $data['code'] ) . '%\'';
}
if( $data['username'] )
{
	if( $data['type'] == 1 )
	{
		$implode[] = 'u.username LIKE \'%' . $db->dblikeescape( $data['username'] ) . '%\'';
	}
	else
	{
		$implode[] = 'CONCAT(u.first_name,\' \', u.last_name) LIKE \'%' . $db->dblikeescape( $data['username'] ) . '%\'';
	}
}
 
if( !empty( $data['group'] ) )
{
	$implode[] = 'tx.group_user LIKE \'%' . $db->dblikeescape( ',' . $data['group'] .',' ) . '%\'';
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

$db->sqlreset()->select( 'h.*, u.username, CONCAT(u.last_name,\' \',u.first_name) AS full_name, tx.title, tx.code, tx.group_user' )->from( $sql )->limit( $onlineTestConfig['perpage'] )->offset( ( $page - 1 ) * $onlineTestConfig['perpage'] );
    

$result = $db->query( $db->sql() );
$listUsers = array();
$dataContent = array();
while( $rows = $result->fetch() )
{
	
	
	if( !empty( $rows['group_user'] ) )
	{
		$rows['group_user'] = array_map('trim', explode(',', $rows['group_user']));
		$rows['group_user'] = array_unique( array_filter( $rows['group_user'] ) );
		$listUsers[] = $rows['userid'];
	}
	else
	{
		$rows['group_user'] = array();
	}
	$dataContent[] = $rows;
}
$result->closeCursor();

$UsersGroups = array();
if( !empty( $listUsers ) )
{
	$listUsers = array_unique( array_filter( $listUsers ) );
	$result = $db->query( 'SELECT gu.group_user_id, gu.title , gul.userid FROM ' . TABLE_ONLINETEST_NAME . '_group_user gu LEFT JOIN ' . TABLE_ONLINETEST_NAME . '_group_user_list gul ON( gu.group_user_id = gul.group_user_id) WHERE gul.userid IN ('. implode(',', $listUsers ) .')');
	$group_user_id = array();		
	while( $item = $result->fetch() )
	{
		$UsersGroups[$item['userid']][] = $item;
		
	}
	$result->closeCursor();
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
 
$xtpl->assign( 'TYPE_CHECKED_1', ( $data['type'] == 1) ? 'checked="checked"' : '' );
$xtpl->assign( 'TYPE_CHECKED_2', ( $data['type'] == 2) ? 'checked="checked"' : '' );
 
 
if( $nv_Request->get_string( $module_data . '_success', 'session' ) )
{
	$xtpl->assign( 'SUCCESS', $nv_Request->get_string( $module_data . '_success', 'session' ) );

	$xtpl->parse( 'main.success' );

	$nv_Request->unset_request( $module_data . '_success', 'session' );

}
$getGroupUser = getGroupUser( $module_name );
foreach($getGroupUser as $key => $group )
{
	$xtitle_i = '';
	if( $group['lev'] > 0 )
	{
		$xtitle_i .= '&nbsp;';
		for( $i = 1; $i <= $group['lev']; $i++ )
		{
			$xtitle_i .= '&nbsp;&nbsp;';
		}
	}
	
	$xtitle_i .= $group['title'];
	$xtpl->assign( 'GROUP', array( 'key'=> $key, 'name'=> $xtitle_i, 'selected'=> ( $key == $data['group'] ) ? 'selected="selected"' : '' ));
	$xtpl->parse( 'main.group' );
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
		$item['userlink'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=users&' . NV_OP_VARIABLE . '=edit&userid=' . $item['userid'];
					
		$item['grouplink'] = '';
		if( isset( $UsersGroups[$item['userid']] ) )
		{
			foreach( $UsersGroups[$item['userid']] as $gru )
			{
				if( in_array( $gru['group_user_id'], $item['group_user'] ) )
				{
					$item['group'] = $gru['title'];
					$item['grouplink'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=group_user&action=userlist&group_user_id=' . $gru['group_user_id'];
					break;
				}
				
			}
		}
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
