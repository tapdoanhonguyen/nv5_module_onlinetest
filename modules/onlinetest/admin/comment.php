<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */
 
if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

$page_title = $lang_module['comment'];
 
if( ACTION_METHOD == 'delete' )
{
	$json = array();
	
	$redirect = $nv_Request->get_int( 'redirect', 'post', 0 );
	
	$comment_id = $nv_Request->get_int( 'comment_id', 'post', 0 );

	$token = $nv_Request->get_title( 'token', 'post', '', 1 );

	$listid = $nv_Request->get_string( 'listid', 'post', '' );

	if( $listid != '' and md5( $nv_Request->session_id . $global_config['sitekey'] ) == $token )
	{
		$del_array = array_map( 'intval', explode( ',', $listid ) );
	}
	elseif( $token == md5( $nv_Request->session_id . $global_config['sitekey'] . $comment_id ) )
	{
		$del_array = array( $comment_id );
	}

	if( ! empty( $del_array ) )
	{

		$_del_array = array();

		$a = 0;
		foreach( $del_array as $comment_id )
		{
			$question_id = $db->query('SELECT question_id FROM ' . TABLE_ONLINETEST_NAME . '_comment WHERE comment_id = ' . intval( $comment_id) )->fetchColumn();
			 
			$db->query( 'DELETE FROM ' . TABLE_ONLINETEST_NAME . '_comment WHERE comment_id = ' . ( int )$comment_id );
			$db->query('UPDATE ' . TABLE_ONLINETEST_NAME . '_question SET comment= IF(comment > 0, comment - 1, 0) WHERE question_id=' . intval( $question_id ) );
	
			$json['id'][$a] = $comment_id;

			$_del_array[] = $comment_id;
			
			++$a;
			 
		}

		$count = sizeof( $_del_array );

		if( $count )
		{
			 
			nv_insert_logs( NV_LANG_DATA, $module_name, 'log_del_comment', implode( ', ', $_del_array ), $admin_info['userid'] );

			$nv_Cache->delMod($module_name);

			$json['success'] = $lang_module['comment_delete_success'];
			if( $redirect )
			{
				$nv_Request->set_Session( $module_data . '_success', $lang_module['comment_delete_success'] );

				$json['link'] = NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=comment';
			}
			
			
		}

	}
	else
	{
		$json['error'] = $lang_module['comment_error_security'];
	}

	nv_jsonOutput( $json );
}
elseif( ACTION_METHOD == 'status' )
{
	$json = array();

	$comment_id = $nv_Request->get_int( 'comment_id', 'post', 0 );
	$new_vid = $nv_Request->get_int( 'new_vid', 'post', 0 );
	$token = $nv_Request->get_title( 'token', 'post', '', 1 );

	if( $token == md5( $nv_Request->session_id . $global_config['sitekey'] . $comment_id ) )
	{
		$sql = 'UPDATE ' . TABLE_ONLINETEST_NAME . '_comment SET status=' . $new_vid . ' WHERE comment_id=' . $comment_id;
		if( $db->exec( $sql ) )
		{
			nv_insert_logs( NV_LANG_DATA, $module_name, 'log_change_status_comment', 'comment_id:' . $comment_id, $admin_info['userid'] );

			$nv_Cache->delMod($module_name);

			$json['success'] = $lang_module['comment_status_success'];

		}
		else
		{
			$json['error'] = $lang_module['comment_error_status'];

		}
	}
	else
	{
		$json['error'] = $lang_module['comment_error_security'];
	}

	nv_jsonOutput( $json );
}
elseif( ACTION_METHOD == 'edit' )
{
	$comment_id= $nv_Request->get_int( 'comment_id', 'get', 0 );
	$token = $nv_Request->get_string( 'token', 'get', '' );
	
	if( $token != md5( $nv_Request->session_id . $global_config['sitekey'] . $comment_id ) )
	{
		Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
		die();
	}
	
	$error = array();
	$data = $db->query( 'SELECT r.*, u.email, u.username FROM ' . TABLE_ONLINETEST_NAME . '_comment r LEFT JOIN '. NV_USERS_GLOBALTABLE .' u ON (r.userid=u.userid) WHERE r.comment_id=' . $comment_id )->fetch();
	
	if( $data['comment_id'] == 0  )
	{
		Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
		die();
	}
 
 
	$caption = $lang_module['comment_edit'];
	
	if( defined( 'NV_EDITOR' ) )
	{
		require_once NV_ROOTDIR . '/' . NV_EDITORSDIR . '/' . NV_EDITOR . '/nv.php';
	}

	
	if( $nv_Request->get_int( 'save', 'post' ) == 1 )
	{

		$data['comment_id'] = $nv_Request->get_int( 'comment_id', 'post', 0 );
		$data['comment'] = $nv_Request->get_editor( 'comment', '', NV_ALLOWED_HTML_TAGS );
		$data['status'] = $nv_Request->get_int( 'status', 'post', 0 );

		if( trim( strip_tags($data['comment'] ) ) == '' and ! preg_match( "/\<img[^\>]*alt=\"([^\"]+)\"[^\>]*\>/is", $data['reply'] ) )
		{
			$error['comment'] = $lang_module['comment_error_comment'];
		}
		 

		if( ! empty( $error ) && ! isset( $error['warning'] ) )
		{
			$error['warning'] = $lang_module['comment_error_warning'];
		}

		if( empty( $error ) )
		{
			 
			try
			{

				$stmt = $db->prepare( 'UPDATE ' . TABLE_ONLINETEST_NAME . '_comment SET 
					status=' . intval( $data['status'] ) . ',
					comment =:comment 						
					WHERE comment_id=' . $data['comment_id'] );

				$stmt->bindParam( ':comment', $data['comment'], PDO::PARAM_STR, strlen( $data['comment'] ) );
 
				if( $stmt->execute() )
				{
					$nv_Request->set_Session( $module_data . '_success', $lang_module['comment_edit_success'] );

					nv_insert_logs( NV_LANG_DATA, $module_name, 'comment', 'comment_id: ' . $data['comment_id'], $admin_info['userid'] );
				}
				else
				{
					$error['warning'] = $lang_module['comment_error_save'];
				}

				$stmt->closeCursor();

			}
			catch ( PDOException $e )
			{
				$error['warning'] = $lang_module['comment_error_save'];
				//var_dump($e);
			}
		}
	 
		if( empty( $error ) )
		{
			$nv_Cache->delMod($module_name); 
			
			Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
			die();
		}

	}
	
	$data['old_comment'] = $data['comment'];
	$data['comment'] = htmlspecialchars( nv_editor_br2nl( $data['comment'] ) );
	$data['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $data['comment_id'] );
		
	
	$xtpl = new XTemplate( 'comment_add.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
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
	
	$xtpl->assign( 'CANCEL', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
	
	if( defined( 'NV_EDITOR' ) and nv_function_exists( 'nv_aleditor' ) )
	{
		$comment = nv_aleditor( 'comment', '100%', '300px', $data['comment'] );
	}
	else
	{
		$comment = '<textarea style="width: 100%" name="comment" id="comment" cols="20" rows="15">' . $data['comment'] . '</textarea>';
	}
	
	
	$xtpl->assign( 'COMMENT', $comment );
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
 
	if( $onlineTestStatus )
	{
		foreach( $onlineTestStatus as $key => $item )
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

/*show list comment*/

$per_page = 50;

$page = $nv_Request->get_int( 'page', 'get', 1 );

$sql = TABLE_ONLINETEST_NAME . '_comment c LEFT JOIN '. NV_USERS_GLOBALTABLE .' u ON( c.userid=u.userid ) ';

$sort = $nv_Request->get_string( 'sort', 'get', '' );

$order = $nv_Request->get_string( 'order', 'get' ) == 'asc' ? 'asc' : 'desc';

$sort_data = array(
	'c.username',
	'c.title',
	'c.status',
	'c.date_added' );

if( isset( $sort ) && in_array( $sort, $sort_data ) )
{

	$sql .= ' ORDER BY ' . $sort;
}
else
{
	$sql .= ' ORDER BY c.date_added';
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

$base_url = NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=comment&amp;sort=' . $sort . '&amp;order=' . $order . '&amp;per_page=' . $per_page;

$db->sqlreset()->select( 'c.*, u.username, u.first_name, u.last_name' )->from( $sql )->limit( $per_page )->offset( ( $page - 1 ) * $per_page );

$result = $db->query( $db->sql() );

$stt = 1;
$array = array();
while( $rows = $result->fetch() )
{
	$rows['stt'] = $stt + ( $page - 1 ) * $per_page;
	$array[] = $rows;
	++$stt;
}

$xtpl = new XTemplate( 'comment.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
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
$xtpl->assign( 'ADD_NEW', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=comment&action=add' );

$order2 = ( $order == 'asc' ) ? 'desc' : 'asc';

$xtpl->assign( 'URL_TITLE', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=title&amp;order=' . $order2 . '&amp;per_page=' . $per_page );
$xtpl->assign( 'URL_STATUS', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=status&amp;order=' . $order2 . '&amp;per_page=' . $per_page );

$xtpl->assign( 'TITLE_ORDER', ( $sort == 'title' ) ? 'class="' . $order2 . '"' : '' );
$xtpl->assign( 'STATUS_ORDER', ( $sort == 'status' ) ? 'class="' . $order2 . '"' : '' );

if( $nv_Request->get_string( $module_data . '_success', 'session' ) )
{
	$xtpl->assign( 'SUCCESS', $nv_Request->get_string( $module_data . '_success', 'session' ) );

	$xtpl->parse( 'main.success' );

	$nv_Request->unset_request( $module_data . '_success', 'session' );

}


if( ! empty( $array ) )
{
	

	foreach( $array as $item )
	{
		$item['date_added'] = nv_date('d/m/Y, H:i:s',  $item['date_added'] );
		$item['comment'] = nv_clean60( strip_tags( $item['comment'] ), 240 );
		$item['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $item['comment_id'] );
		$item['edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&action=edit&token=' . $item['token'] . '&comment_id=' . $item['comment_id'];
		
		$xtpl->assign( 'LOOP', $item );

		foreach( $onlineTestStatus as $key => $val )
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
