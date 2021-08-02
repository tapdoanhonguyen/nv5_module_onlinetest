<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
if(function_exists('xdebug_disable')) 
{ 
   xdebug_disable(); 
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
	$json = array();
	
	ini_set( 'memory_limit', '512M' );

	set_time_limit( 0 );
 
	$essay_exam_id = $nv_Request->get_int( 'essay_exam_id', 'post', 0 );

	$token = $nv_Request->get_title( 'token', 'post', '', 1 );
 
	if( $token == md5( $nv_Request->session_id . $global_config['sitekey'] . $essay_exam_id ) )
	{
		$essayExam = $db->query('SELECT * FROM ' . TABLE_ONLINETEST_NAME . '_essay_exam WHERE essay_exam_id=' . intval( $essay_exam_id ) )->fetch();
 
		if( !empty( $essayExam ) )
		{
			$result = $db->query( 'SELECT h.*, u.username, u.first_name, u.last_name, u.email FROM ' . TABLE_ONLINETEST_NAME . '_history_essay h LEFT JOIN ' . NV_USERS_GLOBALTABLE . ' u ON (h.userid = u.userid) WHERE h.is_deleted = 0 AND h.essay_exam_id=' . intval(  $essay_exam_id ) );
 
			if( $result->rowCount() )
			{
				$data_array = array();
				$dataContent = array();
				$stt = 0;
				while( $row = $result->fetch() )
				{
					 
					// $username = nv_show_name_user( $row['first_name'], $row['last_name'], $row['username'] );
					
					$data_array['stt'] = ++$stt;
					$data_array['username'] = nv_unhtmlspecialchars( $row['username'] );
					$data_array['first_name'] = nv_unhtmlspecialchars( $row['first_name'] );
					$data_array['last_name'] = nv_unhtmlspecialchars( $row['last_name'] );
					$data_array['email'] = nv_unhtmlspecialchars( $row['email'] );
					$data_array['score'] = nv_unhtmlspecialchars( number_format((float)$row['score'], 1, '.', '') . '/' . $row['max_score'] );	 
					$data_array['test_time'] = nv_unhtmlspecialchars( date( 'd/m/Y', $row['test_time'] ) );
					$data_array['essayexam'] = nv_unhtmlspecialchars( $essayExam['title'] );
 
					$dataContent[] = $data_array;	
				}
				
				
				$page_title = 'DANH SÁCH ĐỀ THI';
				
				$Excel_Cell_Begin = 1; // Dong bat dau viet du lieu

		
				$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(NV_ROOTDIR . '/modules/' . $module_file . '/template/mau1.xlsx');
		
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
				$array_title[] = 'username';
				$array_title[] = 'last_name';
				$array_title[] = 'first_name';
				$array_title[] = 'email';
				$array_title[] = 'score';
				$array_title[] = 'test_time';
				$array_title[] = 'essayexam';
 
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
				$array_key_data[] = 'username';
				$array_key_data[] = 'last_name';
				$array_key_data[] = 'first_name';
				$array_key_data[] = 'email';
				$array_key_data[] = 'score';
				$array_key_data[] = 'test_time';
				$array_key_data[] = 'essayexam';

				$pRow = $Excel_Cell_Begin;
				foreach( $dataContent as $row )
				{
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


		 
				
				$file_name =  $module_name . '_' . date('d_m_Y', NV_CURRENTTIME ) . '.xlsx';
				
				$file_path = NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . $file_name;
				
				header( 'Content-Type: application/vnd.ms-excel' );
				header( 'Content-Disposition: attachment;filename="'. $file_name .'"' );
				header( 'Cache-Control: max-age=0' );

				$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
				$writer->save($file_path);
				
				$link = NV_BASE_ADMINURL . "index.php?" . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=essay_exam&action=download&file_name=' . $file_name;  
				
				nv_jsonOutput( array('link'=> $link) );		
				
				
				
				//$objWriter->save( 'php://output' );
				//exit;
			}
			else
			{
				nv_jsonOutput( array('error'=> 'Không tìm thấy dữ liệu thi') );		
			}
		}else
		{
			nv_jsonOutput( array('error'=> 'Không tìm thấy dữ liệu') );		
		}
	}
	else
	{
		$json['error'] = $lang_module['essay_exam_error_security'];
	}
	nv_jsonOutput( $json );	
}

elseif( ACTION_METHOD == 'is_download2' )
{
	ini_set( 'memory_limit', '512M' );

	set_time_limit( 0 );
	
	$token = $nv_Request->get_title( 'token', 'post', '', '' );
	
	if( $token == md5( $nv_Request->session_id . $global_config['sitekey'] ) )
	{
 
		$result = $db->query( 'SELECT h.*, u.username, u.first_name, u.last_name, u.email FROM ' . TABLE_ONLINETEST_NAME . '_history_essay h LEFT JOIN ' . NV_USERS_GLOBALTABLE . ' u ON (h.userid = u.userid) WHERE h.is_deleted = 0 ORDER BY h.test_time DESC');

		if( $result->rowCount() )
		{
			$data_essay_exam_id = array();
			$data_array = array();
			$dataContent = array();
			$stt = 0;
			while( $row = $result->fetch() )
			{
				 
				// $username = nv_show_name_user( $row['first_name'], $row['last_name'], $row['username'] );
				
				$data_array['stt'] = ++$stt;
				$data_array['username'] = nv_unhtmlspecialchars( $row['username'] );
				$data_array['first_name'] = nv_unhtmlspecialchars( $row['first_name'] );
				$data_array['last_name'] = nv_unhtmlspecialchars( $row['last_name'] );
				$data_array['email'] = nv_unhtmlspecialchars( $row['email'] );
				$data_array['score'] = nv_unhtmlspecialchars( number_format((float)$row['score'], 1, '.', '') . '/' . $row['max_score'] );	 
				$data_array['test_time'] = nv_unhtmlspecialchars( date( 'd/m/Y', $row['test_time'] ) );
				$data_array['essay_exam_id'] = $row['essay_exam_id'];
				
				$data_essay_exam_id[]= $row['essay_exam_id'];

				$dataContent[] = $data_array;	
			}
			$result->closeCursor();
			$data_essay_exam_id = array_unique( $data_essay_exam_id );
			$essayExam = array();
			if( !empty( $data_essay_exam_id ) )
			{
				$result = $db->query( 'SELECT essay_exam_id, title FROM ' . TABLE_ONLINETEST_NAME . '_essay_exam WHERE essay_exam_id IN ('. implode(',', $data_essay_exam_id ) .')');

				while( $item = $result->fetch() )
				{ 
					$essayExam[$item['essay_exam_id']] = $item['title'];
					
				}
				$result->closeCursor();
			}

			$page_title = 'DANH SÁCH ĐỀ THI';
			
			$Excel_Cell_Begin = 1; // Dong bat dau viet du lieu

	 
			$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(NV_ROOTDIR . '/modules/' . $module_file . '/template/mau1.xlsx');
	 
			$worksheet = $spreadsheet->getActiveSheet();
			
			$worksheet->setTitle( $page_title );

			// Set page orientation and size
			$worksheet->getPageSetup()->setOrientation( PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE );
			$worksheet->getPageSetup()->setPaperSize( PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4 );
			$worksheet->getPageSetup()->setHorizontalCentered( true );
	  

			$spreadsheet->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd( 1, $Excel_Cell_Begin );
			
			// Tieu de
			$array_title = array();
			$array_title = array();
			$array_title[] = 'stt';
			$array_title[] = 'username';
			$array_title[] = 'last_name';
			$array_title[] = 'first_name';
			$array_title[] = 'email';
			$array_title[] = 'score';
			$array_title[] = 'test_time';
			$array_title[] = 'essayexam';

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
			$array_key_data[] = 'username';
			$array_key_data[] = 'last_name';
			$array_key_data[] = 'first_name';
			$array_key_data[] = 'email';
			$array_key_data[] = 'score';
			$array_key_data[] = 'test_time';
			$array_key_data[] = 'essayexam';

			$pRow = $Excel_Cell_Begin;
			foreach( $dataContent as $row )
			{
				$row['essayexam'] = isset( $essayExam[$row['essay_exam_id']] ) ? $essayExam[$row['essay_exam_id']] : '';
				
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


	 
			
			$file_name = $module_name . '_all_' . date('d_m_Y', NV_CURRENTTIME ) . '.xlsx';
			
			$file_path = NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . $file_name;
			
			header( 'Content-Type: application/vnd.ms-excel' );
			header( 'Content-Disposition: attachment;filename="'. $file_name .'"' );
			header( 'Cache-Control: max-age=0' );

			$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
			$writer->save($file_path);
			
			$link = NV_BASE_ADMINURL . "index.php?" . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=essay_exam&action=download&file_name=' . $file_name;  
			
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
		$json['error'] = $lang_module['essay_exam_error_security'];
	}	 
	nv_jsonOutput( $json );		
}

elseif( ACTION_METHOD == 'delete' )
{
	$json = array();

	$essay_exam_id = $nv_Request->get_int( 'essay_exam_id', 'post', 0 );

	$token = $nv_Request->get_title( 'token', 'post', '', 1 );

	$listid = $nv_Request->get_string( 'listid', 'post', '' );

	if( $listid != '' and md5( $nv_Request->session_id . $global_config['sitekey'] ) == $token )
	{
		$del_array = array_map( 'intval', explode( ',', $listid ) );
	}
	elseif( $token == md5( $nv_Request->session_id . $global_config['sitekey'] . $essay_exam_id ) )
	{
		$del_array = array( $essay_exam_id );
	}

	if( ! empty( $del_array ) )
	{

		$_del_array = array();

		$a = 0;
		foreach( $del_array as $essay_exam_id )
		{
			$result = $db->query( 'DELETE FROM ' . TABLE_ONLINETEST_NAME . '_essay_exam WHERE essay_exam_id = ' . ( int )$essay_exam_id );
			if( $result->rowCount() )
			{
				$json['id'][$a] = $essay_exam_id;
				$_del_array[] = $essay_exam_id;
				++$a;
			}
		}
		$count = sizeof( $_del_array );

		if( $count )
		{
			nv_insert_logs( NV_LANG_DATA, $module_name, 'log_del_essay_exam', implode( ', ', $_del_array ), $admin_info['userid'] );

			$nv_Cache->delMod( $module_name );

			$json['success'] = $lang_module['essay_exam_delete_success'];
		}

	}
	else
	{
		$json['error'] = $lang_module['essay_exam_error_security'];
	}

	nv_jsonOutput( $json );
}
elseif( ACTION_METHOD == 'group_user' )
{
	$title = $nv_Request->get_string( 'title', 'get', '' );
	$json = array();

	$and = '';
	if( ! empty( $title ) )
	{
		$and .= ' AND title LIKE :title';
	}
	$sql = 'SELECT group_user_id, title FROM ' . TABLE_ONLINETEST_NAME . '_group_user  
	WHERE status=1 ' . $and . '
	ORDER BY title ASC LIMIT 0, 50';
 
	$sth = $db->prepare( $sql );
	if( ! empty( $title ) )
	{
		$sth->bindValue( ':title', '%' . $title . '%' );
	}
	$sth->execute();
	while( list( $group_user_id, $title ) = $sth->fetch( 3 ) )
	{
		$json[] = array( 'group_user_id' => $group_user_id, 'title' => nv_htmlspecialchars( $title ) );
	}
	 
	nv_jsonOutput( $json );

}
elseif( ACTION_METHOD == 'getQuestion' )
{
	$question = $nv_Request->get_string( 'keyword', 'get,post', '' );
	$typelist = array_unique( $nv_Request->get_typed_array( 'typelist', 'get,post', 'int', array() ) );
	$page = $nv_Request->get_int( 'page', 'get,post', 1 );
	$perpage = 30;
	
	$base_url = NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=question';

	$json = array();	
	$implode = array();	
	if( $question )
	{
		$implode[] = 'question LIKE \'%' . $db->dblikeescape( $question ) . '%\'';
		$base_url.= '&amp;question=' . $question;

	}

	if( ! empty( $typelist ) )
	{	
		$implode[] = 'category_id IN ('. implode( ',', $typelist ) .') ';
 
		foreach( $typelist as $category_id )
		{
			$base_url.= '&amp;typelist[]=' . $category_id;
		}	
	}
 
	$sql = TABLE_ONLINETEST_NAME . '_essay';

	if( $implode )
	{
		$sql .= ' WHERE '  . implode( ' AND ', $implode );
	} 
	
	$num_items = $db->query( 'SELECT COUNT(*) FROM ' . $sql )->fetchColumn();
 
	$db->sqlreset()->select( '*' )->from( $sql )->limit( $perpage )->offset( ( $page - 1 ) * $perpage );

	$result = $db->query( $db->sql() );
	
	$getLevel =  getLevel( $module_name );
	
	while( $item = $result->fetch() )
	{
		$json['data'][] = array( 'essay_id' => $item['essay_id'], 'question' => html_entity_decode( $item['question'] , ENT_QUOTES, 'UTF-8' ) );
	}
	$result->closeCursor();
 

	$json['generate_page'] = getDataPage( $base_url, $num_items, $perpage, $page, false, true, 'showContent', 'question2', true );
	
	nv_jsonOutput( $json );

}
elseif( ACTION_METHOD == 'pdfview' )
{
	$url = $nv_Request->get_string( 'url', 'get', '' );
	
	if( !nv_is_url( $url ) )
	{
		$url =  NV_MY_DOMAIN . $url;
	}
	echo onlineTest_viewpdf( $url );
	die();
}
elseif( ACTION_METHOD == 'add' || ACTION_METHOD == 'edit' )
{
 
	if ( defined('NV_EDITOR') ) 
	{
		require_once NV_ROOTDIR . '/' . NV_EDITORSDIR . '/' . NV_EDITOR . '/nv.php';
	}

	
	$currentpath = NV_UPLOADS_DIR . '/' . $module_upload . '/' . date( 'Y_m' );
	if( ! file_exists( $currentpath ) )
	{
		nv_mkdir( NV_UPLOADS_REAL_DIR . '/' . $module_upload, date( 'Y_m' ), true );
	}
	
	$getLevel = getLevel( $module_name );
	
	$data = array(
		'essay_exam_id' => 0,
		'group_exam_id' => 0,
		'group_exam_list' => '',
		'title' => '',
		'images' => '',
		'thumb' => 0,
		'introtext' => '',
		'description' => '',
		'keywords' => '',
		'num_question' => '',
		'point' => 0,
		'time' => '',
		'config' => '',
		'rules' => '',
		'group_user' => '',
		'type_id' => 0,
		'random' => 0,
		'pdf' => '',
		'analyzed' => '',
		'video' => '',
		'allow_download' => $onlineTestConfig['allow_download'],
		'allow_video' => $onlineTestConfig['allow_video'],
		'allow_show_answer' => $onlineTestConfig['allow_show_answer'],
		'status' => 1,
		'date_added' => NV_CURRENTTIME );
	$error = array();
	$getConfig = array();
	$question_list = array();
	$dataGroups = array();

	$data['essay_exam_id'] = $nv_Request->get_int( 'essay_exam_id', 'get,post', 0 );

	if( $data['essay_exam_id'] > 0 )
	{
		$data = $db->query( 'SELECT *
		FROM ' . TABLE_ONLINETEST_NAME . '_essay_exam
		WHERE essay_exam_id=' . $data['essay_exam_id'] )->fetch();
		
		$data['group_user'] = array_map( 'intval', explode( ',', $data['group_user'] ) );
		$data['group_user'] = array_unique( array_filter( $data['group_user'] ) );
		if( !empty( $data['group_user'] ) )
		{
 
			$result = $db->query('SELECT group_user_id, title FROM ' . TABLE_ONLINETEST_NAME . '_group_user WHERE group_user_id IN ( '. implode( ',',  $data['group_user'] ) .' )');
 
			while( $group = $result->fetch() )
			{
				$dataGroups[$group['group_user_id']] = $group;	
			}
			$result->closeCursor();
			
		}
		else
		{
			$data['group_user'] = array();
		}
	
		$getConfig = unserialize( $data['config'] );
		
		if(  !empty( $getConfig ) ) 
		{
			$result = $db->query('SELECT essay_id, question FROM ' . TABLE_ONLINETEST_NAME . '_essay WHERE essay_id IN ( '. implode( ',', $getConfig ) .' )');
			while( $item = $result->fetch() )
			{
				$question_list[$item['essay_id']] = $item;
			}	
			$result->closeCursor();			
		}
		$caption = $lang_module['essay_exam_edit'];
	}
	else
	{
		$caption = $lang_module['essay_exam_add'];
	}

	if( $nv_Request->get_int( 'save', 'post' ) == 1 )
	{

		$data['group_exam_id'] = $nv_Request->get_int( 'group_exam_id', 'post', 0 );
		
		$group_exam_list = array_unique( $nv_Request->get_typed_array( 'group_exam_list', 'post', 'int', array() ) );
		if( sizeof( $group_exam_list ) == 1 )
		{
			$data['group_exam_id'] = $group_exam_list[0];
		}
 
		$data['group_exam_list'] = implode( ',', $group_exam_list );
		
		$data['title'] = $nv_Request->get_title( 'title', 'post', '' );
		$data['images'] = $nv_Request->get_title( 'images', 'post', '' );
		$data['introtext'] = $nv_Request->get_textarea( 'introtext', '', 'br', 1 );
		$data['description'] = $nv_Request->get_textarea( 'description', '', 'br', 1 );
		$data['keywords'] = $nv_Request->get_title( 'keywords', 'post', '' );
		$data['num_question'] = $nv_Request->get_int( 'num_question', 'post', 0 );
		$data['point'] = $nv_Request->get_int( 'point', 'post', 0 );
		$data['time'] = $nv_Request->get_int( 'time', 'post', 0 );
		$data['group_user'] = $nv_Request->get_typed_array( 'group_user', 'post', 'int', array() );
			
		 
		$getConfig = $nv_Request->get_typed_array( 'question_list', 'post', 'int', array() );
		if( !empty( $getConfig ) ) 
		{
			$result = $db->query('SELECT essay_id, question FROM ' . TABLE_ONLINETEST_NAME . '_essay WHERE essay_id IN ( '. implode( ',', $getConfig ) .' )');
			while( $item = $result->fetch() )
			{
 				$question_list[$item['essay_id']] = $item;
			}		 
		}	
		 
 
		$data['status'] = $nv_Request->get_int( 'status', 'post', 0 );
		$data['rules'] = $nv_Request->get_editor('rules', '', NV_ALLOWED_HTML_TAGS);
 
		$is_exists = false;
		if( empty( $data['title'] ) )
		{
			$error['title'] = $lang_module['essay_exam_error_title'];
		}
		else
		{

			$sql = 'SELECT COUNT(*) FROM ' . TABLE_ONLINETEST_NAME . '_essay_exam WHERE title=' . $db->quote( $data['title'] ) . ' AND essay_exam_id !=' . intval( $data['essay_exam_id'] );
			$result = $db->query( $sql );
			$is_exists = $result->fetchColumn();
		}

		if( $is_exists )
		{
			$error['title'] = $lang_module['essay_exam_error_title_exists'];
		}

		if( empty( $data['time'] ) )
		{
			$error['time'] = $lang_module['essay_exam_error_time'];
		}

		if( empty( $data['num_question'] ) )
		{
			$error['num_question'] = $lang_module['essay_exam_error_num_question'];
		}
		if( empty( $data['group_exam_list'] ) )
		{
			$error['group_exam_list'] = $lang_module['essay_exam_error_group_exam_list'];
		}
 
		if( empty( $getConfig )  )
		{
			$error['config'] = $lang_module['essay_exam_error_question'];
		} 
 

		if( ! empty( $error ) && ! isset( $error['warning'] ) )
		{
			$error['warning'] = $lang_module['essay_exam_error_warning'];
		}

		if( empty( $error ) )
		{
			$images = NV_DOCUMENT_ROOT . $data['images'];
			if( !nv_is_url( $data['images'] ) and is_file( $images ) )
			{
				$lu = strlen( NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' );
				$data['images'] = substr( $data['images'], $lu );
				
				if( file_exists( NV_ROOTDIR . '/' . NV_FILES_DIR . '/' . $module_upload . '/' . $data['images'] ) )
				{
					$data['thumb'] = 1;
					 
				}
				else
				{
					$data['thumb'] = 2;
				}
				
			}
			elseif( nv_is_url( $data['images'] ) )
			{
				$data['thumb'] = 3;
			}
			else
			{
				$data['images'] = '';
			}
			
			 
			
			 
			$config = serialize( $getConfig );
			 
			$group_user = '';
			$data['group_user'] = array_unique( array_filter( $data['group_user'] ) );
			if( !empty( $data['group_user'] ) )
			{
				$group_user = ',' . implode( ',', $data['group_user'] ) . ',';
			}
 
			try
			{
				if( $data['essay_exam_id'] == 0 )
				{
 
					$stmt = $db->prepare( 'INSERT INTO ' . TABLE_ONLINETEST_NAME . '_essay_exam SET 
						group_exam_id=' . intval( $data['group_exam_id'] ) . ',
						group_exam_list=:group_exam_list, 
						title=:title, 
						images=:images, 
						introtext=:introtext, 
						description=:description, 
						keywords=:keywords, 
						config=:config, 
						rules=:rules, 
						group_user=:group_user, 
						thumb=' . intval( $data['thumb'] ) . ',
						num_question=' . intval( $data['num_question'] ) . ',
						point=' . intval( $data['point'] ) . ',
						time=' . intval( $data['time'] ) . ',
						status=' . intval( $data['status'] ) . ',
						date_added=' . intval( NV_CURRENTTIME ) );
 
					$stmt->bindParam( ':group_exam_list', $data['group_exam_list'], PDO::PARAM_STR );
					$stmt->bindParam( ':title', $data['title'], PDO::PARAM_STR );
					$stmt->bindParam( ':images', $data['images'], PDO::PARAM_STR );
					$stmt->bindParam( ':introtext', $data['introtext'], PDO::PARAM_STR, strlen( $data['introtext'] ) );
					$stmt->bindParam( ':description', $data['description'], PDO::PARAM_STR, strlen( $data['description'] ) );
					$stmt->bindParam( ':keywords', $data['keywords'], PDO::PARAM_STR );
					$stmt->bindParam( ':config', $config, PDO::PARAM_STR, strlen( $config ) );
					$stmt->bindParam( ':rules', $data['rules'], PDO::PARAM_STR, strlen( $data['rules'] ) );
					$stmt->bindParam( ':group_user', $group_user, PDO::PARAM_STR );
					$stmt->execute();

					if( $data['essay_exam_id'] = $db->lastInsertId() )
					{
						
						if( ! empty( $onlineTestConfig['format_code_id'] ) )
						{
			 
							$code = vsprintf( $onlineTestConfig['format_code_id'], $data['essay_exam_id'] );
							$sth = $db->prepare( 'UPDATE ' . TABLE_ONLINETEST_NAME . '_essay_exam SET code= :code WHERE essay_exam_id=' . $data['essay_exam_id'] );
							$sth->bindParam( ':code', $code, PDO::PARAM_STR );
							$sth->execute();
							$sth->closeCursor();
						}
						
						nv_insert_logs( NV_LANG_DATA, $module_name, 'Add type exam', 'essay_exam_id: ' . $data['essay_exam_id'], $admin_info['userid'] );

						$nv_Request->set_Session( $module_data . '_success', $lang_module['essay_exam_add_success'] );

					}
					else
					{
						$error['warning'] = $lang_module['essay_exam_error_save'];

					}
					$stmt->closeCursor();

				 

				}
				else
				{
				 

					$stmt = $db->prepare( 'UPDATE ' . TABLE_ONLINETEST_NAME . '_essay_exam SET 
							group_exam_id=' . intval( $data['group_exam_id'] ) . ',
							group_exam_list=:group_exam_list, 
							title=:title, 
							images=:images, 
							introtext=:introtext, 
							description=:description, 
							keywords=:keywords, 
							config=:config, 
							rules=:rules, 
							group_user=:group_user, 
							thumb=' . intval( $data['thumb'] ) . ',
							num_question=' . intval( $data['num_question'] ) . ',
							time=' . intval( $data['time'] ) . ',
							point=' . intval( $data['point'] ) . ',
							status=' . intval( $data['status'] ) . ',
							date_modified=' . intval( NV_CURRENTTIME ) . '
							WHERE essay_exam_id=' . $data['essay_exam_id'] );

					$stmt->bindParam( ':group_exam_list', $data['group_exam_list'], PDO::PARAM_STR );
					$stmt->bindParam( ':title', $data['title'], PDO::PARAM_STR );
					$stmt->bindParam( ':images', $data['images'], PDO::PARAM_STR );
					$stmt->bindParam( ':introtext', $data['introtext'], PDO::PARAM_STR, strlen( $data['introtext'] ) );
					$stmt->bindParam( ':description', $data['description'], PDO::PARAM_STR, strlen( $data['description'] ) );
					$stmt->bindParam( ':keywords', $data['keywords'], PDO::PARAM_STR );
					$stmt->bindParam( ':config', $config, PDO::PARAM_STR, strlen( $config ) );
					$stmt->bindParam( ':rules', $data['rules'], PDO::PARAM_STR, strlen( $data['rules'] ) );
					$stmt->bindParam( ':group_user', $group_user, PDO::PARAM_STR );
					$stmt->execute();

					if( $stmt->rowCount() )
					{
						nv_insert_logs( NV_LANG_DATA, $module_name, 'Edit Type Exam' . $data['essay_exam_id'], $admin_info['userid'] );

						$nv_Request->set_Session( $module_data . '_success', $lang_module['essay_exam_edit_success'] );

					}
					else
					{
						$error['warning'] = $lang_module['essay_exam_error_save'];

					}

					$stmt->closeCursor();
 
				}
			}
			catch ( PDOException $e )
			{
				$error['warning'] = $lang_module['essay_exam_error_save'];
				//var_dump($e);die();
			}
		}

		if( empty( $error ) )
		{

			Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
			die();
		}
		
		if( !empty( $data['group_user'] ) )
		{
			$result = $db->query('SELECT group_user_id, title FROM ' . TABLE_ONLINETEST_NAME . '_group_user WHERE group_user_id IN ( '. implode( ',',  $data['group_user'] ) .' )');
			while( $group = $result->fetch() )
			{
				$dataGroups[$group['group_user_id']] = $group;	
			}
		
		}
		

	}
	if( ! empty( $data['images'] ) and file_exists( NV_UPLOADS_REAL_DIR . '/' . $module_upload . '/' . $data['images'] ) )
	{
		$data['images'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $data['images'];
		// $currentpath = dirname( $data['images'] );
	}
  

	$data['introtext'] = nv_htmlspecialchars( nv_br2nl( $data['introtext'] ) );
	$data['description'] = nv_htmlspecialchars( nv_br2nl( $data['description'] ) );
	$data['rules'] = nv_htmlspecialchars( nv_editor_br2nl( $data['rules'] ) );
	if( defined( 'NV_EDITOR' ) and nv_function_exists( 'nv_aleditor' ) )
	{
		$data['rules'] = nv_aleditor( 'rules', '100%', '300px', $data['rules'], '', '','' );
	}
	else
	{
		$data['rules'] = '<textarea style="width: 100%" name="rules" id="rules" cols="20" rows="15">' . $data['rules'] . '</textarea>';
	}
	
	
	if(  !empty( $getConfig ) )
	{
		$data['num_question'] = 0;
		foreach( $getConfig as $_question_id )
		{
			if( isset( $question_list[$_question_id] ) )
			{
				++$data['num_question'];
			}
		}	
	}	
	
	
	
	$xtpl = new XTemplate( 'essay_exam_add.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
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
	$xtpl->assign( 'MODULE_UPLOAD', $module_upload );
	$xtpl->assign( 'CURRENT', $currentpath );
	$xtpl->assign( 'URL_PDF', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&amp;action=pdfview' );
 
	
	$array_group_in_row = explode( ',', $data['group_exam_list'] );
	
	
	if( $onlineTestGroupExam )
	{
		foreach( $onlineTestGroupExam as $key => $item )
		{
			$space = intval( $item['lev'] ) * 30;
			$display = ( sizeof( $array_group_in_row ) > 1 and ( in_array( $key, $array_group_in_row ) ) ) ? '' : ' display: none;';
			
			$xtpl->assign( 'GROUPEXAM', array(
				'key' => $key,
				'name' => $item['title'],
				'checked' => ( in_array( $key, $array_group_in_row ) ) ? ' checked="checked"' : '',
				'group_checked' => ( $key == $data['group_exam_id'] ) ? 'checked="checked"' : '',
				'space'=> $space,
				'display'=> $display ) );
			$xtpl->parse( 'main.group_exam' );
		}
	}
 
	if( $dataGroups )
	{
		foreach( $dataGroups as $_group_user_id => $group )
		{
			$xtpl->assign( 'GROUP', $group );
			$xtpl->parse( 'main.group' );
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
	 
	if( !empty( $getConfig ) )
	{
		foreach( $getConfig as $_question_id )
		{
			if( isset( $question_list[$_question_id] ) )
			{
				$xtpl->assign( 'QUESTION', $question_list[$_question_id] );
				$xtpl->parse( 'main.question' );
			}

		}
	}
 
	// if( $onlineTestCategory )
	// {
		// foreach( $onlineTestCategory as $key => $item )
		// {
			// $xtitle_i = '';
			// if( $item['lev'] > 0 )
			// {
				// $xtitle_i .= '&nbsp;';
				// for( $i = 1; $i <= $item['lev']; $i++ )
				// {
					// $xtitle_i .= '&nbsp;&nbsp;';
				// }
			// }
			
			// $xtitle_i .= $item['title'];
			
			// $xtpl->assign( 'CATEGORY', array(
				// 'key' => $key,
				// 'name' => $xtitle_i,
				// 'selected' => ( $key == $data['category_id'] ) ? 'selected="selected"' : '' ) );
			// $xtpl->parse( 'main.category' );
		// }
	// }  
	 
	if( $onlineTestCategory )
	{
		foreach( $onlineTestCategory as $key => $item )
		{
			$space = '';
			if( $item['lev'] > 0 )
			{
				$space .= '&nbsp;';
				for( $i = 1; $i <= $item['lev']; $i++ )
				{
					$space .= '&nbsp;&nbsp;';
				}
			}
 
			$xtpl->assign( 'CATEGORY', array(
				'key' => $key,
				'name' => $item['title'],
				'space' => $space,
				'checked' => ( isset( $getConfig[$key]['category_id'] ) && $key == $getConfig[$key]['category_id'] ) ? 'checked="checked"' : '' ) );
			
		 
 
			$xtpl->parse( 'main.category' );
			$xtpl->parse( 'main.category_search' );
			
		}
	}
	

	
	if( $error )
	{
		$check = 0;
		foreach( $error as $key => $_error )
		{
			if( !in_array( $key, array('config','level','percent') ) )
			{
				$xtpl->assign( 'error_' . $key, $_error );
				$xtpl->parse( 'main.error_' . $key );
			}else{
				$xtpl->assign( 'ERROR', $_error );
				$xtpl->parse( 'main.error_other.loop' );
				++$check;
				
			}
			
		}
		if( $check > 0 )
		{
			$xtpl->parse( 'main.error_other' );
		}else
		{
			 
			if( isset( $error['warning'] ) )
			{
				$xtpl->assign( 'WARNING', $error['warning'] );
				 
				$xtpl->parse( 'main.warning' );
				 
			}
		} 
	}
	if( ACTION_METHOD == 'edit' || $nv_Request->get_int( 'save', 'post' ) == 1 )
	{
		$xtpl->parse( 'main.script_edit');
	}
	

	$xtpl->parse( 'main' );
	$contents = $xtpl->text( 'main' );

	include NV_ROOTDIR . '/includes/header.php';
	echo nv_admin_theme( $contents );
	include NV_ROOTDIR . '/includes/footer.php';
}

/*show list essay_exam*/

$page = $nv_Request->get_int( 'page', 'get', 1 );

$data['title'] = $nv_Request->get_string( 'title', 'get', '' );
$data['description'] = $nv_Request->get_string( 'description', 'get' );
$data['group_exam_id'] = $nv_Request->get_int( 'group_exam_id', 'get', 0 );
$data['status'] = $nv_Request->get_string( 'status', 'get', '' );
 
$implode = array();

if( $data['title'] )
{
	$implode[] = 'title LIKE \'%' . $db_slave->dblikeescape( $data['title'] ) . '%\'';
}
if( $data['group_exam_id'] )
{
	$implode[] = 'group_exam_id = ' . intval( $data['group_exam_id'] );
}

if( is_numeric( $data['status'] ) )
{
	$implode[] = 'status = ' . intval( $data['status'] );
}
 

$sql = TABLE_ONLINETEST_NAME . '_essay_exam';

if( $implode )
{
	$sql .= ' WHERE ' . implode( ' AND ', $implode );
}

$sort = $nv_Request->get_string( 'sort', 'get', '' );

$order = $nv_Request->get_string( 'order', 'get' ) == 'asc' ? 'asc' : 'desc';

$sort_data = array(
	'group_exam_id',
	'title',
	'date_added',
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

$base_url = NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '='. $op .'&amp;sort=' . $sort . '&amp;order=' . $order . '&amp;per_page=' . $onlineTestConfig['perpage'];

$db->sqlreset()->select( '*' )->from( $sql )->limit( $onlineTestConfig['perpage'] )->offset( ( $page - 1 ) * $onlineTestConfig['perpage'] );

$result = $db->query( $db->sql() );

$dataContent = array();
while( $rows = $result->fetch() )
{
	$dataContent[] = $rows;
}

$xtpl = new XTemplate( 'essay_exam.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
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
$xtpl->assign( 'ADD_NEW', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=essay_exam&action=add' );

$xtpl->assign( 'DATA', $data );

$order2 = ( $order == 'asc' ) ? 'desc' : 'asc';

$xtpl->assign( 'URL_TITLE', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=title&amp;order=' . $order2 . '&amp;per_page=' . $onlineTestConfig['perpage'] );
$xtpl->assign( 'URL_DESCRIPTION', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=description&amp;order=' . $order2 . '&amp;per_page=' . $onlineTestConfig['perpage'] );
$xtpl->assign( 'URL_STATUS', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=status&amp;order=' . $order2 . '&amp;per_page=' . $onlineTestConfig['perpage'] );
$xtpl->assign( 'URL_TIME', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=time&amp;order=' . $order2 . '&amp;per_page=' . $onlineTestConfig['perpage'] );
$xtpl->assign( 'URL_POINT', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=point&amp;order=' . $order2 . '&amp;per_page=' . $onlineTestConfig['perpage'] );

$xtpl->assign( 'TITLE_ORDER', ( $sort == 'title' ) ? 'class="' . $order2 . '"' : '' );
$xtpl->assign( 'DESCRIPTION_ORDER', ( $sort == 'description' ) ? 'class="' . $order2 . '"' : '' );
$xtpl->assign( 'STATUS_ORDER', ( $sort == 'status' ) ? 'class="' . $order2 . '"' : '' );
$xtpl->assign( 'TIME_ORDER', ( $sort == 'time' ) ? 'class="' . $order2 . '"' : '' );
$xtpl->assign( 'POINT_ORDER', ( $sort == 'point' ) ? 'class="' . $order2 . '"' : '' );

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
			'selected' => ( $key == $data['group_exam_id'] ) ? 'selected="selected"' : '' ) );
		$xtpl->parse( 'main.category' );
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

		$item['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $item['essay_exam_id'] );
		$item['status_checked'] = ( $item['status'] ) ? 'checked="checked"' : '';
		$item['edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&action=edit&token=' . $item['token'] . '&essay_exam_id=' . $item['essay_exam_id'];
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
