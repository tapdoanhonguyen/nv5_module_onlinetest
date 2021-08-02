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
 

$globalUserid = defined( 'NV_IS_USER' ) ? $user_info['userid'] : 0;
 
if( ! $globalUserid )
{
	nv_jsonOutput( array( 'error' => $lang_module['error_login_comment'] ) );
}

$question_id = $nv_Request->get_int( 'question_id', 'post', 0 );
$page = $nv_Request->get_int( 'page', 'post', 1 );
$token = $nv_Request->get_title( 'token', 'post', '', 1 );

if( $token != md5( $nv_Request->session_id . $global_config['sitekey'] . $question_id ) )
{
	nv_jsonOutput( array( 'error' => $lang_module['error_security'] ) );
}

$query = $db_slave->query( 'SELECT * FROM ' . TABLE_ONLINETEST_NAME . '_question WHERE question_id = ' . intval( $question_id ) );
$dataContent = $query->fetch();
$dataContent['token'] = $token;
if( empty( $dataContent ) )
{
	nv_jsonOutput( array( 'error' => $lang_module['error_question'] ) );
}

if( ACTION_METHOD == 'getComment' )
{
 
	$total_comment = $db_slave->query( 'SELECT COUNT(*) FROM ' . TABLE_ONLINETEST_NAME . '_comment WHERE ( status=1 OR ( status=0 AND userid='. intval( $globalUserid ) .' ) ) AND question_id = ' . intval( $question_id ) )->fetchColumn();
	
	$dataComment = array();
	
	$result = $db_slave->query( '
				SELECT c.*, u.username, u.first_name, u.last_name, u.photo
				FROM ' . TABLE_ONLINETEST_NAME . '_comment c
				LEFT JOIN '. NV_USERS_GLOBALTABLE .' u ON(c.userid = u.userid) 
				WHERE c.question_id = ' . intval( $question_id ) . ' 
				  AND ( c.status=1 OR ( c.status=0 AND c.userid='. intval( $globalUserid ) .' ) )
				ORDER BY c.date_added 
				LIMIT 0,' . intval( $onlineTestConfig['number_comment'] ) );
 
	while( $item = $result->fetch() )
	{
		$dataComment[] = $item;
	}
	$result->closeCursor();
	$json['comment'] = ThemeOnlineTestCommentBox ( $dataContent, $dataComment, $page, $total_comment );
	
	nv_jsonOutput( $json );
	
}
elseif( ACTION_METHOD == 'getOnlyComment' && $dataContent['comment'] > 0 && $page > 1 )
{
	$total_comment = $db_slave->query( 'SELECT COUNT(*) FROM ' . TABLE_ONLINETEST_NAME . '_comment WHERE ( status=1 OR ( status=0 AND userid='. intval( $globalUserid ) .' ) ) AND question_id = ' . intval( $question_id ) )->fetchColumn();
	
	$dataComment = array();
 
	$db->sqlreset()
		->select( 'c.*, u.username, u.first_name, u.last_name, u.photo' )
		->from( TABLE_ONLINETEST_NAME . '_comment c LEFT JOIN '. NV_USERS_GLOBALTABLE .' u ON(c.userid = u.userid)')
		->where( 'c.question_id = ' . intval( $question_id ) . ' AND ( c.status=1 OR ( c.status=0 AND c.userid='. intval( $globalUserid ) . ') )' )
		->limit( $onlineTestConfig['number_comment'] )
		->offset( ( $page - 1 ) * $onlineTestConfig['number_comment'] );
 
	$result = $db->query( $db->sql() );
		
	while( $item = $result->fetch() )
	{
		$dataComment[] = $item;
	}
	$result->closeCursor();
	
	$total_page = ceil( $total_comment / $onlineTestConfig['number_comment'] );
	
	$loadMore = 1;
	if( $total_page <= $page  )
	{
		$loadMore = 0;
		
	}else{
		
		$json['page'] = $page + 1;
	}
	$json['total_comment'] = $total_comment;
	$json['loadMore'] = $loadMore;
	
	$json['comment'] = ThemeOnlineTestComment ( $dataContent, $dataComment );
	
	nv_jsonOutput( $json );
}
elseif( ACTION_METHOD == 'insertComment' )
{
	
	$last_comment = $nv_Request->get_string( 'lastcomment', 'post', '' );	
	$comment_id_token = $nv_Request->get_string( 'comment_id', 'post', '' );
	$comment_id = 0;
	$last_time = 0;
	if( !empty( $comment_id_token ) )
	{
		$commentid_token = explode(',', $comment_id_token);
		if( sizeOf( $commentid_token ) == 2 )
		{
			if( $commentid_token[1] != md5( $nv_Request->session_id . $global_config['sitekey'] . $commentid_token[0] ) )
			{
				nv_jsonOutput( array('error'=> $lang_module['error_security']) );
			}
		}else
		{
			nv_jsonOutput( array('error'=> $lang_module['error_security']) );
		}
		
		$comment_id = $commentid_token[0]; 
	}else{
		
		$lastcomment = explode(',', $last_comment);
		if( sizeOf( $lastcomment ) == 2 )
		{
			if( $lastcomment[1] != md5( $nv_Request->session_id . $global_config['sitekey'] . $lastcomment[0] ) )
			{
				nv_jsonOutput( array('error'=> $lang_module['error_security']) );
			}
		}else
		{
			nv_jsonOutput( array('error'=> $lang_module['error_security']) );
		}
		
		$last_time = $lastcomment[0];
		
		
	}
 
	$comment = $nv_Request->get_editor('comment', '', NV_ALLOWED_HTML_TAGS);
	$status = ( $onlineTestConfig['show_comment'] ) ? 0 : 1;
	if( $comment_id == 0 )
	{
		try 
		{ 
			$stmt = $db->prepare( 'INSERT INTO ' . TABLE_ONLINETEST_NAME . '_comment SET 
				userid = ' . intval( $globalUserid ) . ', 
				question_id = ' . intval( $question_id ) . ', 
				status = ' . intval( $status ) . ', 
				date_added = ' . intval( NV_CURRENTTIME ) . ', 
				comment=:comment');
	 
			$stmt->bindParam( ':comment', $comment, PDO::PARAM_STR, strlen( $comment ) );
			$stmt->execute();
			$comment_id = $db_slave->lastInsertId();	
			if( $comment_id )
			{
				$db->query('UPDATE ' . TABLE_ONLINETEST_NAME . '_question SET comment= comment+1 WHERE question_id=' . intval( $question_id ) );
				
				$json['success'] = 'Bình luận thành công !';
				$dataComment = array();
				$result = $db_slave->query( '
					SELECT c.*, u.username, u.first_name, u.last_name, u.photo
					FROM ' . TABLE_ONLINETEST_NAME . '_comment c
					LEFT JOIN '. NV_USERS_GLOBALTABLE .' u ON(c.userid = u.userid) 
					WHERE c.date_added > '. intval( $last_time ) .' 
					  AND c.question_id = ' . intval( $question_id ) . ' 
					  AND ( c.status=1 OR ( c.status=0 AND c.userid='. intval( $globalUserid ) .' ) )
					ORDER BY c.date_added 
					LIMIT 0,' . intval( $onlineTestConfig['number_comment'] ) );
		
				while( $item = $result->fetch() )
				{
					$dataComment[] = $item;
				}
				$result->closeCursor();
				$json['update'] = 0;
				$json['total_comment'] = sizeOf( $dataComment ); 
				$json['comment'] = ThemeOnlineTestComment ( $dataContent, $dataComment );
				$json['lastcomment'] = NV_CURRENTTIME . ',' . md5( $nv_Request->session_id . $global_config['sitekey'] . NV_CURRENTTIME );
			}
			$stmt->closeCursor();
			
		} catch (PDOException $e) {
			
			trigger_error($e->getMessage());
			
			nv_jsonOutput( array( 'error' => $lang_module['error_save'] ) );

		}
	}else{
		
		try 
		{ 
			$stmt = $db->prepare( 'UPDATE ' . TABLE_ONLINETEST_NAME . '_comment 
			SET 
				status = ' . intval( $status ) . ', 
				comment=:comment
			WHERE comment_id=' . intval( $comment_id ));
	 
			$stmt->bindParam( ':comment', $comment, PDO::PARAM_STR, strlen( $comment ) );
			$stmt->execute();
			 	
			if( $stmt->rowCount() )
			{
				$json['update'] = $comment;
				$json['comment_id'] = $comment_id;
			}
			$stmt->closeCursor();
			
		} catch (PDOException $e) {
			
			trigger_error($e->getMessage());
			
			nv_jsonOutput( array( 'error' => $lang_module['error_save'] ) );

		}
		
	}
 
	nv_jsonOutput( $json );
}
elseif( ACTION_METHOD == 'canEdit' )
{
	
	$commentid_token = $nv_Request->get_string( 'comment_id', 'post', '' );
 
	$commentid_token = explode(',', $commentid_token);
	if( sizeOf( $commentid_token ) == 2 )
	{
		if( $commentid_token[1] != md5( $nv_Request->session_id . $global_config['sitekey'] . $commentid_token[0] ) )
		{
			nv_jsonOutput( array('error'=> $lang_module['error_security']) );
		}
	}else
	{
		nv_jsonOutput( array('error'=> $lang_module['error_security']) );
	}
	
	$comment_id = $commentid_token[0]; 
	
	$dataComment = $db_slave->query( 'SELECT * FROM ' . TABLE_ONLINETEST_NAME . '_comment WHERE comment_id = ' . intval( $comment_id ) )->fetch();
	
	if( $dataComment['date_added'] < (  NV_CURRENTTIME - ( $onlineTestConfig['time_modify_comment'] * 60 ) )  ) 
	{
		nv_jsonOutput( array('error'=> $lang_module['error_time_comment']) ); 
	}
	
	$json['comment'] = $dataComment['comment'];
	
	nv_jsonOutput( $json );
}
elseif( ACTION_METHOD == 'canDelete' )
{
	
	$commentid_token = $nv_Request->get_string( 'comment_id', 'post', '' );
 
	$commentid_token = explode(',', $commentid_token);
	if( sizeOf( $commentid_token ) == 2 )
	{
		if( $commentid_token[1] != md5( $nv_Request->session_id . $global_config['sitekey'] . $commentid_token[0] ) )
		{
			nv_jsonOutput( array('error'=> $lang_module['error_security']) );
		}
	}else
	{
		nv_jsonOutput( array('error'=> $lang_module['error_security']) );
	}
	
	$comment_id = $commentid_token[0]; 
	
	$dataComment = $db_slave->query( 'SELECT * FROM ' . TABLE_ONLINETEST_NAME . '_comment WHERE comment_id = ' . intval( $comment_id ) )->fetch();
	
	if( $dataComment['date_added'] < (  NV_CURRENTTIME - ( $onlineTestConfig['time_delete_comment'] * 60 ) )  ) 
	{
		nv_jsonOutput( array('error'=> $lang_module['error_time_delete']) ); 
	}
	$db->query('UPDATE ' . TABLE_ONLINETEST_NAME . '_question SET comment= IF(comment > 0, comment - 1, 0) WHERE question_id=' . intval( $question_id ) );
				
	$json['success'] = $lang_module['delete_comment_success'];
	$json['comment_id'] = $comment_id;
	
	nv_jsonOutput( $json );
}
 
die('ERROR PERMISSION !');
