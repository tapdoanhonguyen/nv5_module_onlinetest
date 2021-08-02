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
$dataQuestions = array();

$globalUserid = defined( 'NV_IS_USER' ) ? $user_info['userid'] : 0;
 
if( ! $globalUserid )
{
	nv_jsonOutput( array( 'error' => $lang_module['error_login_comment'] ) );
}

// if( ACTION_METHOD == 'getdefaulerror' )
// {
 
	// foreach( $onlineTestTitleError as $key => $item )
	// {
		// $json['data'][] = $item;
	// }
	// nv_jsonOutput( $json );
// }


$type = $nv_Request->get_int( 'type', 'post', 0 );
$question_id = $nv_Request->get_int( 'question_id', 'post', 0 );
$token = $nv_Request->get_title( 'token', 'post', '', 1 );

if( $token != md5( $nv_Request->session_id . $global_config['sitekey'] . $question_id ) )
{
	nv_jsonOutput( array( 'error' => $lang_module['error_security'] ) );
}

if( $type == 1 )
{
	$query = $db_slave->query( 'SELECT * FROM ' . TABLE_ONLINETEST_NAME . '_essay WHERE essay_id = ' . intval( $question_id ) );

}
else
{
	$query = $db_slave->query( 'SELECT * FROM ' . TABLE_ONLINETEST_NAME . '_question WHERE question_id = ' . intval( $question_id ) );

}
$dataContent = $query->fetch();

if( empty( $dataContent ) )
{
	nv_jsonOutput( array( 'error' => $lang_module['error_question'] ) );
}
  
$reportTitle = $nv_Request->get_title( 'reportTitle', 'post', '', 1 );
$reportNote = $nv_Request->get_textarea('reportNote', '', 'br', 1);
try 
{  
	$stmt = $db->prepare( 'INSERT INTO ' . TABLE_ONLINETEST_NAME . '_report SET 
		userid = ' . intval( $globalUserid ) . ', 
		question_id = ' . intval( $question_id ) . ', 
		type = ' . intval( $type ) . ', 
		date_added = ' . intval( NV_CURRENTTIME ) . ', 
		title=:title,
		note=:note');
	$stmt->bindParam( ':title', $reportTitle, PDO::PARAM_STR );
	$stmt->bindParam( ':note', $reportNote, PDO::PARAM_STR, strlen( $reportNote ) );
	$stmt->execute();
	$report_id = $db_slave->lastInsertId();	
	if( $report_id )
	{
		$json['success'] = $lang_module['success_report'];
		
	}
	
} catch (PDOException $e) {
	//trigger_error($e->getMessage());
	nv_jsonOutput( array( 'error' => $lang_module['error_question'] ) );

}	
 	
nv_jsonOutput( $json );

