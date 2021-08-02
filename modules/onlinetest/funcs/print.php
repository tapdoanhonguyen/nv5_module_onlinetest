<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */

if ( ! defined( 'NV_IS_MOD_TEST' ) ) die( 'Stop!!!' );




$globalUserid = defined( 'NV_IS_USER' ) ? $user_info['userid'] : 0;
 
if( ! $globalUserid )
{
	$link_redirect = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=users&' . NV_OP_VARIABLE . '=login&&nv_redirect=' . nv_redirect_encrypt( $client_info['selfurl'] );

	Header( 'Location: ' . $link_redirect );
	exit();
}
 
$result = $db->query( '
		SELECT h.*, u.username, tx.group_exam_id, tx.group_exam_list, tx.title, tx.code, tx.config, tx.images, tx.pdf, tx.allow_show_answer, tx.allow_download, tx.allow_video, tx.video, tx.analyzed, tx.date_added FROM ' . TABLE_ONLINETEST_NAME . '_history h 
		LEFT JOIN ' . TABLE_ONLINETEST_NAME . '_type_exam tx ON (h.type_exam_id = tx.type_exam_id) 
		LEFT JOIN ' . NV_USERS_GLOBALTABLE . ' u ON (h.userid = u.userid) 
		WHERE h.userid = '. intval( $globalUserid ) .' AND h.is_deleted=0 AND h.history_alias=' . $db->quote( $historyCode ) );
 
$dataContent = $result->fetch();
 
$result->closeCursor();
unset( $result );
if( $dataContent )
{
	
	$dataContent['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $dataContent['type_exam_id'] );
	
	$dataContent['alias'] = strtolower( change_alias( $dataContent['title'] ) );
 
	// $base_url_rewrite = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $dataContent['history_alias'] . '/' .  strtolower( change_alias( $dataContent['title'] ) ) . $global_config['rewrite_exturl'], true ); // Cũ
	$base_url_rewrite = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=print/' . $dataContent['history_alias'] . '/' .  strtolower( change_alias( $dataContent['title'] ) ) . $global_config['rewrite_exturl'], true );
	if ( $_SERVER['REQUEST_URI'] == $base_url_rewrite )
	{
		$canonicalUrl = NV_MAIN_DOMAIN . $base_url_rewrite;
	}
	elseif ( NV_MAIN_DOMAIN . $_SERVER['REQUEST_URI'] != $base_url_rewrite )
	{
		//chuyen huong neu doi alias
		header( 'HTTP/1.1 301 Moved Permanently' );
		Header( 'Location: ' . $base_url_rewrite );
		die();
	}
	else
	{
		$canonicalUrl = $base_url_rewrite;
	}

	$dataContent['question'] = @unserialize( $dataContent['question'] );
	$dataQuestion = array();
	if( $dataContent['type_id'] == 0 || $dataContent['type_id'] == 1 )
	{
		$getLevel = getLevel( $module_name );
		
		if( !empty( $dataContent['question'] ) )
		{
			$listQuestionId = array_keys( $dataContent['question'] );
			
			$result = $db_slave->query( 'SELECT * FROM ' . TABLE_ONLINETEST_NAME . '_question WHERE question_id IN (' . implode( ',', $listQuestionId ) . ')' );

			while ( $item = $result->fetch() )
			{
				$item['trueanswer'] = array_map('intval', explode( ',', $item['trueanswer'] ));
				$item['answers'] = unserialize( $item['answers'] );
				$item['level'] = isset( $getLevel[$item['level_id']] ) ? $getLevel[$item['level_id']]['title'] : '';
				$dataQuestion[$item['question_id']] = $item;
			}
			$result->closeCursor();
		}
		
	}
	else
	{
		$dataQuestion = unserialize( $dataContent['config'] );
	}

	$dataContent['ranking'] = $db->query('SELECT title FROM ' . TABLE_ONLINETEST_NAME . '_ranking WHERE min_score >= '. floatval( $dataContent['score'] ) .' AND '. floatval( $dataContent['score'] ) .' < max_score ORDER BY max_score ASC LIMIT 1')->fetchColumn();
		
	$page_title = $dataContent['title'] . ' - ' . $dataContent['username'];
	
	
	$dataContent['url_print'] = nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=print/' . $dataContent['history_alias'] . '/' . $dataContent['alias']. $global_config['rewrite_exturl'], true);
	
	$ten = $user_info['last_name']; 
	$ho = $user_info['first_name']; 
	

	$contents = ThemeOnlineTestHistoryPrint( $dataContent, $dataQuestion, $ho, $ten, true );

}else{
	
	$url_Permanently = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name, true );
	header( "HTTP/1.1 301 Moved Permanently" );
	header( 'Location:' . $url_Permanently );
	exit();
}

include NV_ROOTDIR . '/includes/header.php';
echo $contents;
include NV_ROOTDIR . '/includes/footer.php';
