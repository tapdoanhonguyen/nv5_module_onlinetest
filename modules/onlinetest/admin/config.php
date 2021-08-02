<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */


if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );
 
$page_title = $lang_module['config'];

$groups_list = nv_groups_list();

$saveconfig = $nv_Request->get_int( 'saveconfig', 'post', 0 );

if( ! empty( $saveconfig ) )
{

	$onlineTestConfig = array();	
	$onlineTestConfig['open'] = $nv_Request->get_int( 'open', 'post', 0 );
	$onlineTestConfig['max_score'] = $nv_Request->get_int( 'max_score', 'post', 0 );
	$onlineTestConfig['test_limit'] = $nv_Request->get_int( 'test_limit', 'post', 0 );
	$onlineTestConfig['allow_show_answer'] = $nv_Request->get_int( 'allow_show_answer', 'post', 0 );
	$onlineTestConfig['allow_download'] = $nv_Request->get_int( 'allow_download', 'post', 0 );
	$onlineTestConfig['allow_video'] = $nv_Request->get_int( 'allow_video', 'post', 0 );
	$onlineTestConfig['show_comment'] = $nv_Request->get_int( 'show_comment', 'post', 0 );
	$onlineTestConfig['perpage'] = $nv_Request->get_int( 'perpage', 'post', 0 );
	$onlineTestConfig['bonus_score'] = $nv_Request->get_int( 'bonus_score', 'post', 0 );
	$number_comment = $nv_Request->get_int( 'number_comment', 'post', 0 );
	$onlineTestConfig['number_comment'] = ( $number_comment ) ? $number_comment : 1;
	$onlineTestConfig['time_modify_comment'] = $nv_Request->get_int( 'time_modify_comment', 'post', 0 );
	$onlineTestConfig['time_delete_comment'] = $nv_Request->get_int( 'time_delete_comment', 'post', 0 );
	$onlineTestConfig['test_timeout'] = $nv_Request->get_int( 'test_timeout', 'post', 0 );
	$onlineTestConfig['convert_to_vcoin'] = $nv_Request->get_int( 'convert_to_vcoin', 'post', 0 );
	$onlineTestConfig['format_code_id'] = $nv_Request->get_title( 'format_code_id', 'post', '' );	 
	$onlineTestConfig['facebook_appid'] = $nv_Request->get_title( 'facebook_appid', 'post', '');
	$onlineTestConfig['merchant_id'] = $nv_Request->get_title( 'merchant_id', 'post', '');
	$onlineTestConfig['secure_code'] = $nv_Request->get_title( 'secure_code', 'post', '');
	$onlineTestConfig['api_username'] = $nv_Request->get_title( 'api_username', 'post', '');
	$onlineTestConfig['api_password'] = $nv_Request->get_title( 'api_password', 'post', '');
	$onlineTestConfig['core_api_http_usr'] = $nv_Request->get_title( 'core_api_http_usr', 'post', '');
	$onlineTestConfig['core_api_http_pwd'] = $nv_Request->get_title( 'core_api_http_pwd', 'post', '' );
	$onlineTestConfig['default_form_import'] = $nv_Request->get_title( 'default_form_import', 'post', '' );
	$onlineTestConfig['default_group_teacher'] = $nv_Request->get_title( 'default_group_teacher', 'post', '' );
	$onlineTestConfig['default_group_student'] = $nv_Request->get_title( 'default_group_student', 'post', '' );
	$onlineTestConfig['intro'] = $nv_Request->get_editor( 'intro', '', NV_ALLOWED_HTML_TAGS );
	
	$sth = $db_slave->prepare( 'UPDATE ' . TABLE_ONLINETEST_NAME . '_config SET config_value = :config_value WHERE config_name = :config_name');
	foreach( $onlineTestConfig as $config_name => $config_value )
	{
 
			$sth->bindParam( ':config_name', $config_name, PDO::PARAM_STR );
			$sth->bindParam( ':config_value', $config_value, PDO::PARAM_STR );
			$sth->execute();
 
	}
 
	$sth->closeCursor();

 
	$nv_Cache->delMod( $module_name );

	Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&rand=' . nv_genpass() );

	die();

}


$result = $db->query('SELECT group_user_id, title FROM ' . TABLE_ONLINETEST_NAME . '_group_user WHERE group_user_id IN ( '. implode(',', array( $onlineTestConfig['default_group_teacher'], $onlineTestConfig['default_group_student'] ) ) .' )');
$dataGroup = array();
while( $group = $result->fetch() )
{
	$dataGroup[$group['group_user_id']] = $group['title'];
}
$xtpl = new XTemplate( 'config.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
$xtpl->assign( 'LANG', $lang_module );
$xtpl->assign( 'GLANG', $lang_global );
$xtpl->assign( 'NV_BASE_ADMINURL', NV_BASE_ADMINURL );
$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
$xtpl->assign( 'MODULE_NAME', $module_name );
$xtpl->assign( 'OP', $op );
$xtpl->assign( 'UPLOAD_CURRENT', NV_UPLOADS_DIR . '/' . $module_upload  );
 
$onlineTestConfig['open_checked'] = ( $onlineTestConfig['open'] == 1) ? 'checked="checked"' : '';
$onlineTestConfig['allow_show_answer_checked'] = ( $onlineTestConfig['allow_show_answer'] == 1) ? 'checked="checked"' : '';
$onlineTestConfig['allow_download_checked'] = ( $onlineTestConfig['allow_download'] == 1) ? 'checked="checked"' : '';
$onlineTestConfig['allow_video_checked'] = ( $onlineTestConfig['allow_video'] == 1) ? 'checked="checked"' : '';
$onlineTestConfig['show_comment_checked'] = ( $onlineTestConfig['show_comment'] == 1) ? 'checked="checked"' : '';
$onlineTestConfig['intro'] = htmlspecialchars( nv_editor_br2nl( $onlineTestConfig['intro'] ) );


$onlineTestConfig['default_group_teacher_title'] = isset( $dataGroup[$onlineTestConfig['default_group_teacher']] ) ? $dataGroup[$onlineTestConfig['default_group_teacher']] : '';
$onlineTestConfig['default_group_student_title'] = isset( $dataGroup[$onlineTestConfig['default_group_student']] ) ? $dataGroup[$onlineTestConfig['default_group_student']] : '';
 
$xtpl->assign( 'SHOW1', ( $onlineTestConfig['default_group_teacher_title'] == '' ) ? '' : 'showx' );
$xtpl->assign( 'SHOW2', ( $onlineTestConfig['default_group_student_title'] == '' ) ? '' : 'showx' );


$xtpl->assign( 'DATA', $onlineTestConfig );

if( defined( 'NV_EDITOR' ) )
{
	require_once NV_ROOTDIR . '/' . NV_EDITORSDIR . '/' . NV_EDITOR . '/nv.php';
}

if( defined( 'NV_EDITOR' ) and nv_function_exists( 'nv_aleditor' ) )
{

	$intro = nv_aleditor( 'intro', '100%', '300px', $onlineTestConfig['intro'], '' );
}
else
{
	$intro = "<textarea class=\"form-control\" style=\"width: 100%\" name=\"intro\" id=\"' . $module_data . '_intro\" rows=\"5\">" . $onlineTestConfig['intro'] . "</textarea>";
}
$xtpl->assign( 'INTRO', $intro );
 
for( $i = 5; $i <= 30; ++$i )
{
	$xtpl->assign( 'PERPAGE', array( 'key' => $i, 'name' => $i, 'selected' => $i == $onlineTestConfig['perpage'] ? ' selected="selected"' : '' ) );
	$xtpl->parse( 'main.perpage' );
}
     
$xtpl->parse( 'main' );
$contents = $xtpl->text( 'main' );
include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';