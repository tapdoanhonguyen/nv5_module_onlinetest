<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

$page_title = $lang_module['report'];

$onlineTestReportStatus = array(
		'0'=> $lang_module['report_pending'],
		'1'=> $lang_module['report_viewed'],
		'2'=> $lang_module['report_handling'], 
	);
 
if( ACTION_METHOD == 'delete' )
{
	$json = array();

	$report_id = $nv_Request->get_int( 'report_id', 'post', 0 );

	$token = $nv_Request->get_title( 'token', 'post', '', 1 );

	$listid = $nv_Request->get_string( 'listid', 'post', '' );

	if( $listid != '' and md5( $nv_Request->session_id . $global_config['sitekey'] ) == $token )
	{
		$del_array = array_map( 'intval', explode( ',', $listid ) );
	}
	elseif( $token == md5( $nv_Request->session_id . $global_config['sitekey'] . $report_id ) )
	{
		$del_array = array( $report_id );
	}

	if( ! empty( $del_array ) )
	{

		$_del_array = array();

		$a = 0;
		foreach( $del_array as $report_id )
		{

			// if( $rows_total = $db->query( 'SELECT COUNT(*) FROM ' . TABLE_ONLINETEST_NAME . '_dshb WHERE report_id = ' . ( int )$report_id )->fetchColumn() )
			// {
				// $json['error'] = sprintf( $lang_module['report_error_city'], $rows_total );
			// }			 
			// else
			// {
				$db->query( 'DELETE FROM ' . TABLE_ONLINETEST_NAME . '_report WHERE report_id = ' . ( int )$report_id );

				$json['id'][$a] = $report_id;

				$_del_array[] = $report_id;

				++$a;
			//}
		}

		$count = sizeof( $_del_array );

		if( $count )
		{
			 
			nv_insert_logs( NV_LANG_DATA, $module_name, 'log_del_report', implode( ', ', $_del_array ), $admin_info['userid'] );

			$nv_Cache->delMod($module_name);

			$json['success'] = $lang_module['report_delete_success'];
		}

	}
	else
	{
		$json['error'] = $lang_module['report_error_security'];
	}

	nv_jsonOutput( $json );
}
elseif( ACTION_METHOD == 'status' )
{
	$json = array();

	$report_id = $nv_Request->get_int( 'report_id', 'post', 0 );
	$new_vid = $nv_Request->get_int( 'new_vid', 'post', 0 );
	$token = $nv_Request->get_title( 'token', 'post', '', 1 );

	if( $token == md5( $nv_Request->session_id . $global_config['sitekey'] . $report_id ) )
	{
		$sql = 'UPDATE ' . TABLE_ONLINETEST_NAME . '_report SET status=' . $new_vid . ' WHERE report_id=' . $report_id;
		if( $db->exec( $sql ) )
		{
			nv_insert_logs( NV_LANG_DATA, $module_name, 'log_change_status_report', 'report_id:' . $report_id, $admin_info['userid'] );

			$nv_Cache->delMod($module_name);

			$json['success'] = $lang_module['report_status_success'];

		}
		else
		{
			$json['error'] = $lang_module['report_error_status'];

		}
	}
	else
	{
		$json['error'] = $lang_module['report_error_security'];
	}

	nv_jsonOutput( $json );
}
elseif( ACTION_METHOD == 'reply' )
{
	$report_id= $nv_Request->get_int( 'report_id', 'get', 0 );
	$token = $nv_Request->get_string( 'token', 'get', '' );
	
	if( $token != md5( $nv_Request->session_id . $global_config['sitekey'] . $report_id ) )
	{
		Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
		die();
	}
	
	$error = array();
	$data = $db->query( 'SELECT r.*, u.email, u.username FROM ' . TABLE_ONLINETEST_NAME . '_report r LEFT JOIN '. NV_USERS_GLOBALTABLE .' u ON (r.userid=u.userid) WHERE r.report_id=' . $report_id )->fetch();
	
	if( $data['report_id'] == 0  )
	{
		Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
		die();
	}
	if( $data['status'] == 0 )
	{
		$db->query( 'UPDATE ' . TABLE_ONLINETEST_NAME . '_report SET status=1 WHERE report_id=' . $report_id );
	}
	
	$data['status'] = 2;
 
	$caption = $lang_module['report_reply'];
	
	if( defined( 'NV_EDITOR' ) )
	{
		require_once NV_ROOTDIR . '/' . NV_EDITORSDIR . '/' . NV_EDITOR . '/nv.php';
	}

	
	if( $nv_Request->get_int( 'save', 'post' ) == 1 )
	{

		$data['report_id'] = $nv_Request->get_int( 'report_id', 'post', 0 );
		$data['token'] = $nv_Request->get_title( 'token', 'post', '' );
		$data['reply'] = $nv_Request->get_editor( 'reply', '', NV_ALLOWED_HTML_TAGS );
		$data['status'] = $nv_Request->get_int( 'status', 'post', 0 );

		if( trim( strip_tags($data['reply'] ) ) == '' and ! preg_match( "/\<img[^\>]*alt=\"([^\"]+)\"[^\>]*\>/is", $data['reply'] ) )
		{
			$error['reply'] = $lang_module['report_error_reply'];
		}
		 

		if( ! empty( $error ) && ! isset( $error['warning'] ) )
		{
			$error['warning'] = $lang_module['report_error_warning'];
		}

		if( empty( $error ) )
		{
			
			$mail_user = $data['email'];
			$mail_title = 'Phản hồi: ' . $data['title'];
			$mail_content =  $data['note'];
			$mail_content.= '<br>----------------------------------------------------------------<br>';
			$mail_content.=  $data['reply'];
			$from = array( $global_config['site_name'], $global_config['site_email'] );			
			$is_check = @nv_sendmail( $from, $mail_user, $mail_title, $mail_content );
 			if( $is_check )
			{
				try
				{

					$stmt = $db->prepare( 'UPDATE ' . TABLE_ONLINETEST_NAME . '_report SET 
						status=' . intval( $data['status'] ) . ',
						reply =:reply 						
						WHERE report_id=' . $data['report_id'] );

					$stmt->bindParam( ':reply', $data['reply'], PDO::PARAM_STR, strlen( $data['reply'] ) );

					if( $stmt->execute() )
					{
						nv_insert_logs( NV_LANG_DATA, $module_name, 'Reply', 'report_id: ' . $data['report_id'], $admin_info['userid'] );
					}
					else
					{
						$error['warning'] = $lang_module['report_error_save'];

					}

					$stmt->closeCursor();

				}
				catch ( PDOException $e )
				{
					$error['warning'] = $lang_module['report_error_save'];
					//var_dump($e);
				}

			}else{
				$error['sendmail'] = $lang_module['report_error_sendmail'];
			}				
 
		}
	 
		if( empty( $error ) )
		{
			$nv_Cache->delMod($module_name); 

			Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
			die();
		}

	}
	
	$data['reply'] = htmlspecialchars( nv_editor_br2nl( $data['reply'] ) );
	
	
	$xtpl = new XTemplate( 'report_reply.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
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
	$xtpl->assign( 'OP', $op );
	$xtpl->assign( 'CAPTION', $caption );
	
	$xtpl->assign( 'CANCEL', NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=" . $op );
	
	if( defined( 'NV_EDITOR' ) and nv_function_exists( 'nv_aleditor' ) )
	{
		$data['reply'] = nv_aleditor( 'reply', '100%', '300px', $data['reply'] );
	}
	else
	{
		$data['reply'] = '<textarea style="width: 100%" name="reply" id="reply" cols="20" rows="15">' . $data['reply'] . '</textarea>';
	}
	
	
	$xtpl->assign( 'DATA', $data );
	
	if( isset( $error['warning'] ) )
	{
		$xtpl->assign( 'error_warning', $error['warning'] );
		$xtpl->parse( 'main.error_warning' );
	}
	if( isset( $error['reply'] ) )
	{
		$xtpl->assign( 'error_reply', $error['reply'] );
		$xtpl->parse( 'main.error_reply' );
	}
	if( isset( $error['sendmail'] ) )
	{
		$xtpl->assign( 'error_sendmail', $error['sendmail'] );
		$xtpl->parse( 'main.error_sendmail' );
	}
  
	if( $onlineTestReportStatus )
	{
		foreach( $onlineTestReportStatus as $key => $item )
		{		
			$xtpl->assign( 'STATUS', array('key'=> $key, 'name'=> $item, 'selected'=> ( $key == $data['status'] && is_numeric( $data['status'] ) ) ? 'selected="selected"' : '' ) );
			$xtpl->parse( 'main.status' );
		}
	}

	$xtpl->parse( 'main' );
	$contents = $xtpl->text( 'main' );

	include NV_ROOTDIR . '/includes/header.php';
	echo nv_admin_theme( $contents );
	include NV_ROOTDIR . '/includes/footer.php';
}

/*show list report*/

$per_page = 50;

$page = $nv_Request->get_int( 'page', 'get', 1 );

$sql = TABLE_ONLINETEST_NAME . '_report';

$sort = $nv_Request->get_string( 'sort', 'get', '' );

$order = $nv_Request->get_string( 'order', 'get' ) == 'asc' ? 'asc' : 'desc';

$sort_data = array(
	'title',
	'status',
	'date_added' );

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

$base_url = NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=report&amp;sort=' . $sort . '&amp;order=' . $order . '&amp;per_page=' . $per_page;

$db->sqlreset()->select( '*' )->from( $sql )->limit( $per_page )->offset( ( $page - 1 ) * $per_page );

$result = $db->query( $db->sql() );

$stt = 1;
$array = array();
while( $rows = $result->fetch() )
{
	$rows['stt'] = $stt + ( $page - 1 ) * $per_page;
	$array[] = $rows;
	++$stt;
}

$xtpl = new XTemplate( 'report.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
$xtpl->assign( 'LANG', $lang_module );
$xtpl->assign( 'NV_LANG_VARIABLE', NV_LANG_VARIABLE );
$xtpl->assign( 'NV_LANG_DATA', NV_LANG_DATA );
$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
$xtpl->assign( 'THEME', $global_config['site_theme'] );
$xtpl->assign( 'NV_BASE_ADMINURL', NV_BASE_ADMINURL );
$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
$xtpl->assign( 'MODULE_FILE', $module_file );
$xtpl->assign( 'MODULE_NAME', $module_name );
$xtpl->assign( 'TOKEN', md5( $nv_Request->session_id . $global_config['sitekey'] ) );
$xtpl->assign( 'ADD_NEW', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=report&action=add' );

$order2 = ( $order == 'asc' ) ? 'desc' : 'asc';

$xtpl->assign( 'URL_TITLE', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=title&amp;order=' . $order2 . '&amp;per_page=' . $per_page );
$xtpl->assign( 'URL_STATUS', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=status&amp;order=' . $order2 . '&amp;per_page=' . $per_page );

$xtpl->assign( 'TITLE_ORDER', ( $sort == 'title' ) ? 'class="' . $order2 . '"' : '' );
$xtpl->assign( 'STATUS_ORDER', ( $sort == 'status' ) ? 'class="' . $order2 . '"' : '' );


if( ! empty( $array ) )
{
	

	foreach( $array as $item )
	{

		$item['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $item['report_id'] );
		if( $item['type'] == 1 )
		{
			$item['reply'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&action=reply&token=' . $item['token'] . '&report_id=' . $item['report_id'];
		
			$item['view_question'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=essay&action=add&token=' . $item['token'] . '&essay_id=' . $item['question_id'];
		
		}
		else{
			$item['reply'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&action=reply&token=' . $item['token'] . '&report_id=' . $item['report_id'];
		
			$item['view_question'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=question&action=add&token=' . $item['token'] . '&question_id=' . $item['question_id'];

		}
		
		$xtpl->assign( 'LOOP', $item );

		foreach( $onlineTestReportStatus as $key => $val )
		{
			$xtpl->assign( 'STATUS', array(
				'key' => $key,
				'name' => $val,
				'selected' => $key == $item['status'] ? ' selected="selected"' : '' ) );
			$xtpl->parse( 'main.loop.status' );
		}
		$xtpl->parse( 'main.loop' );
	}

}

$generate_page = nv_generate_page( $base_url, $num_items, $per_page, $page );
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
