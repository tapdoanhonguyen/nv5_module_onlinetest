<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */

if ( ! defined( 'NV_IS_MOD_TEST' ) ) die( 'Stop!!!' );
 
$page_title = $lang_module['history_list'];
 
$globalUserid = defined( 'NV_IS_USER' ) ? $user_info['userid'] : 0;
   
if( ACTION_METHOD == 'print' )
{
	
	if( ! $globalUserid )
	{
		$link_redirect = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=users&' . NV_OP_VARIABLE . '=login&&nv_redirect=' . nv_redirect_encrypt( $client_info['selfurl'] );

		Header( 'Location: ' . $link_redirect );
		exit();
	}
	
	$page_title = $lang_module['history_view'];
	
	$history_essay_id = $nv_Request->get_int( 'history_essay_id', 'get', 0 );

	$token = $nv_Request->get_title( 'token', 'get', '', 1 );
 
	$result = $db->query( '
		SELECT h.*, u.username, tx.* FROM ' . TABLE_ONLINETEST_NAME . '_history_essay h 
		LEFT JOIN ' . TABLE_ONLINETEST_NAME . '_essay_exam tx ON (h.essay_exam_id = tx.essay_exam_id) 
		LEFT JOIN ' . NV_USERS_GLOBALTABLE . ' u ON (h.userid = u.userid) 
		WHERE h.userid = '. intval( $globalUserid ) .' AND h.history_essay_id=' . intval( $history_essay_id ) );

	$dataContent = $result->fetch();$result->closeCursor();unset( $result );
	$dataContent['question'] = @unserialize( $dataContent['question'] );
 
	$listQuestionId = array_keys( $dataContent['question'] );
	
	$dataQuestion = array();
	if( $listQuestionId )
	{
		$result = $db_slave->query( 'SELECT * FROM ' . TABLE_ONLINETEST_NAME . '_question WHERE question_id IN ('.implode(',', $listQuestionId ).')');
		
		while( $item = $result->fetch() )
		{
			$dataQuestion[$item['question_id']] = $item;
		}
		$result->closeCursor();
	}
 
	$contents = ThemeOnlineTestHistoryPrint( $dataContent, $dataQuestion );

	include NV_ROOTDIR . '/includes/header.php';
	echo $contents;
	include NV_ROOTDIR . '/includes/footer.php';
}
elseif( ACTION_METHOD == 'delete' )
{
	
	if( ! $globalUserid )
	{
		nv_jsonOutput( array( 'error'=> $lang_module['error_login'] ) );
	}
	
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
			$result = $db->query( 'UPDATE ' . TABLE_ONLINETEST_NAME . '_history_essay SET is_deleted=1 WHERE history_essay_id = ' . ( int )$history_essay_id );
			if( $result->rowCount() )
			{
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
 
				$json['link'] = NV_BASE_SITEURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=history-essay';
		
			}
}

	}
	else
	{
		$json['error'] = $lang_module['history_error_security'];
	}

	nv_jsonOutput( $json );
}
elseif( ACTION_METHOD == 'continue' )
{
	
	if( ! $globalUserid )
	{
		nv_jsonOutput( array( 'error'=> $lang_module['error_login'] ) );
	}
	
	$json = array();

 
	$history_essay_id = $nv_Request->get_int( 'history_essay_id', 'post', 0 );

	$token = $nv_Request->get_title( 'token', 'post', '', 1 );

	if( $token == md5( $nv_Request->session_id . $global_config['sitekey'] . $history_essay_id ) )
	{
		$history = $db->query( 'SELECT h.*, tx.title FROM ' . TABLE_ONLINETEST_NAME . '_history_essay h LEFT JOIN ' . TABLE_ONLINETEST_NAME . '_essay_exam tx ON (h.essay_exam_id = tx.essay_exam_id) WHERE h.history_essay_id=' . intval( $history_essay_id ) )->fetch();
		if ( $history && ( $history['test_time'] + ( $history['time'] * 60 ) ) > NV_CURRENTTIME )
		{
			$nv_Request->set_Session( 'history_' . $history['essay_exam_id'], intval( $history_essay_id ) );
			
			$json['link'] = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=dotest/' . strtolower( change_alias( $history['title'] ) ) . '-' . $history['essay_exam_id'] . $global_config['rewrite_exturl'], true );

		}
		else
		{
			if( ( $history['test_time'] + ( $history['time'] * 60 ) ) <= NV_CURRENTTIME && $history['is_sended'] == 0 ) 
			{
				$history['is_sended'] = 1;	
				if( $history['time_do_test'] > ( $history['time'] * 60 ) )
				{
					$history['time_do_test'] = ( $history['time'] * 60 );
				}
				$db->exec( 'UPDATE ' . TABLE_ONLINETEST_NAME . '_history_essay SET is_sended=1, time_do_test=' . intval( $history['time_do_test'] ) . ' WHERE history_essay_id=' . intval( $history_essay_id ) );
		 
				 
			}
			
			$nv_Request->set_Session( 'history_' . $history['essay_exam_id'], 0 );	
			$json['error'] = $lang_module['history_error_timout'];
		}
	}
	else
	{
		$json['error'] = $lang_module['history_error_security'];
	}

	nv_jsonOutput( $json );
}


if( ! $globalUserid )
{
	$link_redirect = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=users&' . NV_OP_VARIABLE . '=login&&nv_redirect=' . nv_redirect_encrypt( $client_info['selfurl'] );

	Header( 'Location: ' . $link_redirect );
	exit();
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
$implode[] = 'h.userid=' . intval( $globalUserid );
$implode[] = 'h.is_deleted=0';
 
$sql = TABLE_ONLINETEST_NAME . '_history_essay h 
LEFT JOIN ' . TABLE_ONLINETEST_NAME . '_essay_exam tx ON (h.essay_exam_id = tx.essay_exam_id) 
LEFT JOIN ' . NV_USERS_GLOBALTABLE . ' u ON (h.userid = u.userid)';

if( $implode )
{
	$sql .= ' WHERE ' . implode( ' AND ', $implode );
}

$data['sort'] = $nv_Request->get_string( 'sort', 'get', '' );

$data['order'] = $nv_Request->get_string( 'order', 'get' ) == 'asc' ? 'asc' : 'desc';

$sort_data = array(
	'tx.title',
	'tx.code',
	'h.score',
	'h.test_time' );

if( isset( $data['sort'] ) && in_array( $data['sort'], $sort_data ) )
{

	$sql .= ' ORDER BY ' . $data['sort'];
}
else
{
	$sql .= ' ORDER BY h.test_time';
}

if( isset( $data['order'] ) && ( $data['order'] == 'desc' ) )
{
	$sql .= ' DESC';
}
else
{
	$sql .= ' ASC';
}

$num_items = $db->query( 'SELECT COUNT(*) FROM ' . $sql )->fetchColumn();

$base_url = NV_BASE_SITEURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=history-essay&amp;sort=' . $data['sort'] . '&amp;order=' . $data['order'] . '&amp;per_page=' . $onlineTestConfig['perpage'];

$db->sqlreset()->select( 'h.*, u.username, tx.title, tx.code' )->from( $sql )->limit( $onlineTestConfig['perpage'] )->offset( ( $page - 1 ) * $onlineTestConfig['perpage'] );

$result = $db->query( $db->sql() );
$stt = 1;
$dataContent = array();
while( $item = $result->fetch() )
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
	$item['stt'] = ( $page - 1 ) * $onlineTestConfig['perpage'] + $stt;
	$dataContent[] = $item;
	++$stt;
}

$generate_page = nv_generate_page( $base_url, $num_items, $onlineTestConfig['perpage'], $page );
	

$contents = ThemeOnlineTestHistoryEssay( $dataContent, $data, $generate_page ); 


include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';
