<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;

if( ACTION_METHOD == 'delete' )
{
	$json = array();
	
	$redirect = $nv_Request->get_int( 'redirect', 'post', 0 );
	
	$history_essay_id = $nv_Request->get_int( 'history_essay_id', 'post', 0 );

	$token = $nv_Request->get_title( 'token', 'post', '', 1 );

	$listid = $nv_Request->get_string( 'listid', 'post', '' );

	if( $listid != '' and md5( $nv_Request->session_id . $global_config['sitekey'] ) == $token )
	{
		$del_array = array_map( 'intval', explode( ',', $listid ) );
	}
	elseif( $token == md5( $nv_Request->session_id . $global_config['sitekey'] . $history_essay_id ) )
	{
		$del_array = array( $history_essay_id );
	}

	if( ! empty( $del_array ) )
	{

		$_del_array = array();

		$a = 0;
		foreach( $del_array as $history_essay_id )
		{
			$result = $db->query( 'DELETE FROM ' . TABLE_ONLINETEST_NAME . '_history_essay WHERE history_essay_id = ' . ( int )$history_essay_id );
			if( $result->rowCount() )
			{	
				$db->query( 'DELETE FROM ' . TABLE_ONLINETEST_NAME . '_history_essay_row WHERE history_essay_id = ' . ( int )$history_essay_id );

				$json['id'][$a] = $history_essay_id;
				$_del_array[] = $history_essay_id;
				++$a;
			}
		}
		$count = sizeof( $_del_array );

		if( $count )
		{
			nv_insert_logs( NV_LANG_DATA, $module_name, 'log_del_history_essay', implode( ', ', $_del_array ), $admin_info['userid'] );

			$nv_Cache->delMod( $module_name );
			
			$json['success'] = $lang_module['history_delete_success'];			
			if( $redirect )
			{
				$nv_Request->set_Session( $module_data . '_success', $lang_module['history_delete_success'] );

				$json['link'] = NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=history-essay';
			}
		}

	}
	else
	{
		$json['error'] = $lang_module['history_error_security'];
	}

	nv_jsonOutput( $json );
}
elseif( ACTION_METHOD == 'download' )
{
	$file_name = $nv_Request->get_string( 'file_name', 'get', '' );
 
	$file_path = NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . $file_name;
	
	if( file_exists( $file_path ) )
	{
		header( 'Content-Description: File Transfer' );
		header( 'Content-Type:application/pdf' );
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
 
elseif( ACTION_METHOD == 'print' )
{
	ini_set( 'memory_limit', '512M' );

	set_time_limit( 0 );
	
	$history_essay_id = $nv_Request->get_int( 'history_essay_id', 'post', 0 );

	$token = $nv_Request->get_title( 'token', 'post', '' );
	
	if( $token == md5( $nv_Request->session_id . $global_config['sitekey'] . $history_essay_id ) )
	{

	
		$result = $db->query( '
			SELECT h.*, u.first_name, u.last_name, u.username, tx.* FROM ' . TABLE_ONLINETEST_NAME . '_history_essay h 
			LEFT JOIN ' . TABLE_ONLINETEST_NAME . '_essay_exam tx ON (h.essay_exam_id = tx.essay_exam_id) 
			LEFT JOIN ' . NV_USERS_GLOBALTABLE . ' u ON (h.userid = u.userid) 
			WHERE h.history_essay_id=' . intval( $history_essay_id ) );

		$dataContent = $result->fetch();
	 
		if( $dataContent )
		{
			$dataContent['full_name'] = nv_show_name_user($dataContent['first_name'], $dataContent['last_name'], $dataContent['username']);

			
			$dataQuestion = array();
			$result = $db_slave->query( 'SELECT * FROM ' . TABLE_ONLINETEST_NAME . '_history_essay_row WHERE history_essay_id=' . intval( $dataContent['history_essay_id'] ));
				
			while( $item = $result->fetch() )
			{
				$dataQuestion[$item['essay_id']] = $item;
			}
			$result->closeCursor();
			
		 
			$xtpl = new XTemplate( 'history_essay_pdf.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
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
			
			if( $dataQuestion )
			{
				$stt = 1;
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
			
				
		 
			
			// $dataContent['date_added'] = nv_date('d/m/Y H:i:s', $dataContent['date_added']);	
			// $dataContent['time_do_test'] = gmdate('H:i:s', $dataContent['time_do_test']);	
			$dataContent['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $dataContent['history_essay_id'] );
			$dataContent['time_do_test'] = str_pad( ceil($dataContent['time_do_test']/60), 2, '0', STR_PAD_LEFT );	
		 
  			
			$xtpl->assign( 'DATA', $dataContent );
		 
			$xtpl->parse( 'main' );
			$contents = $xtpl->text( 'main' );

			$html2pdf = new Html2Pdf('portrait', 'A4', 'vi', true, 'utf-8' );
			$html2pdf->pdf->setFont('freeserif');
			$html2pdf->writeHTML( $contents );
			
			
			$file_name = nv_strtolower( change_alias( $dataContent['title'] ) . '_' . $dataContent['username'] ) . '.pdf';
				
			$file_path = NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . $file_name;
			
			// header( 'Content-Type: application/pdf' );
			// header( 'Content-Disposition: attachment;filename="'. $file_name .'"' );
			// header( 'Cache-Control: max-age=0' );

			$html2pdf->output($file_path, 'F'); // Generate and load the PDF in the browser.
			
			$link = NV_BASE_ADMINURL . "index.php?" . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=history-essay&action=download&file_name=' . $file_name;  
			
			nv_jsonOutput( array('link'=> $link) );		
			
			
		
		}
		else
		{
			
			$json['error'] = $lang_module['history_error_empty'];
		}
			
	}else
	{
		
		$json['error'] = $lang_module['history_error_security'];
	}
	nv_jsonOutput( $json );
	
}
elseif( ACTION_METHOD == 'view' )
{
	$history_essay_id = $nv_Request->get_int( 'history_essay_id', 'get', 0 );

	$token = $nv_Request->get_title( 'token', 'get', '', 1 );

	
	$result = $db->query( '
		SELECT h.*, u.first_name, u.last_name, u.username, tx.* FROM ' . TABLE_ONLINETEST_NAME . '_history_essay h 
		LEFT JOIN ' . TABLE_ONLINETEST_NAME . '_essay_exam tx ON (h.essay_exam_id = tx.essay_exam_id) 
		LEFT JOIN ' . NV_USERS_GLOBALTABLE . ' u ON (h.userid = u.userid) 
		WHERE h.history_essay_id=' . intval( $history_essay_id ) );

	$dataContent = $result->fetch();
 
	
	if( empty( $dataContent ) )
	{
		Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
		die();
	}
	
	
	$dataContent['full_name'] = nv_show_name_user($dataContent['first_name'], $dataContent['last_name'], $dataContent['username']);

	
	$dataQuestion = array();
	$result = $db_slave->query( 'SELECT * FROM ' . TABLE_ONLINETEST_NAME . '_history_essay_row WHERE history_essay_id=' . intval( $dataContent['history_essay_id'] ));
		
	while( $item = $result->fetch() )
	{
		$dataQuestion[$item['essay_id']] = $item;
	}
	$result->closeCursor();
	
 
	$xtpl = new XTemplate( 'history_essay_view.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
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
	
	if( $dataQuestion )
	{
		$stt = 1;
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
	
		
 
	
	// $dataContent['date_added'] = nv_date('d/m/Y H:i:s', $dataContent['date_added']);	
	// $dataContent['time_do_test'] = gmdate('H:i:s', $dataContent['time_do_test']);	
	$dataContent['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $dataContent['history_essay_id'] );
	$dataContent['time_do_test'] = str_pad( ceil($dataContent['time_do_test']/60), 2, '0', STR_PAD_LEFT );	
 
	$dataContent['score'] =  round($dataContent['score'], 1);
	$dataContent['token'] =  md5( $nv_Request->session_id . $global_config['sitekey'] . $dataContent['history_essay_id'] );
	
	$xtpl->assign( 'DATA', $dataContent );
 
	$xtpl->parse( 'main' );
	$contents = $xtpl->text( 'main' );

	include NV_ROOTDIR . '/includes/header.php';
	echo nv_admin_theme( $contents );
	include NV_ROOTDIR . '/includes/footer.php';

	
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
 

$sql = TABLE_ONLINETEST_NAME . '_history_essay h 
LEFT JOIN ' . TABLE_ONLINETEST_NAME . '_essay_exam tx ON (h.essay_exam_id = tx.essay_exam_id) 
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

$base_url = NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=history-essay&amp;sort=' . $sort . '&amp;order=' . $order . '&amp;per_page=' . $onlineTestConfig['perpage'];

$db->sqlreset()->select( 'h.*, u.username, tx.title, tx.code' )->from( $sql )->limit( $onlineTestConfig['perpage'] )->offset( ( $page - 1 ) * $onlineTestConfig['perpage'] );

$result = $db->query( $db->sql() );

$dataContent = array();
while( $rows = $result->fetch() )
{
	$dataContent[] = $rows;
}

$xtpl = new XTemplate( 'history_essay.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
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
$xtpl->assign( 'ADD_NEW', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=history-essay&action=add' );

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
			$db->exec( 'UPDATE ' . TABLE_ONLINETEST_NAME . '_history_essay SET is_sended=1, time_do_test=' . intval( $item['time_do_test'] )  . ' WHERE history_essay_id=' . intval( $item['history_essay_id'] ) );
	 
			 
		}
		
 
		$item['test_time'] = nv_date('d/m/Y H:i:s', $item['test_time']);
		$item['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $item['history_essay_id'] );
		$item['view'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&action=view&history_essay_id=' . $item['history_essay_id'] . '&token=' . $item['token'];
		
		
		
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
