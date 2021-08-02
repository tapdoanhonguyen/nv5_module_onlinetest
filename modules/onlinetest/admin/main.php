<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */

if ( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );
$page_title = $lang_module['main'];
 
$sql = 'SELECT COUNT(*) as number FROM ' . TABLE_ONLINETEST_NAME . '_category';
$result = $db->query( $sql );
$number = $result->fetchColumn();
$array_info[] = array(
	'title' => $lang_module['main_category_total'],  
	'value' => $number,  
	'link' => NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=category'   
);
 
$sql = 'SELECT COUNT(*) as number FROM ' . TABLE_ONLINETEST_NAME . '_group_exam';
$result = $db->query( $sql );
$number = $result->fetchColumn();
$array_info[] = array(
	'title' => $lang_module['main_group_exam_total'],  
	'value' => $number,  
	'link' => NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=group_exam'   
);
  
$sql = 'SELECT COUNT(*) as number FROM ' . TABLE_ONLINETEST_NAME . '_question';
$result = $db->query( $sql );
$number = $result->fetchColumn();
$array_info[] = array(
	'title' => $lang_module['main_question_total'],  
	'value' => $number,  
	'link' => NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=library',   
);

 
$sql = 'SELECT COUNT(DISTINCT userid) as number FROM ' . TABLE_ONLINETEST_NAME . '_history';
$result = $db->query( $sql );
$number = $result->fetchColumn();
$array_info[] = array(
	'title' => $lang_module['main_history_total'],  
	'value' => $number,  
	'link' => NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=history',   
);

$xtpl = new XTemplate( 'main.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
$xtpl->assign( 'LANG', $lang_module );
$xtpl->assign( 'GLANG', $lang_global );

foreach ( $array_info as $info )
{
	$xtpl->assign( 'ROW', $info );
	$xtpl->parse( 'main.row' );
}

$xtpl->parse( 'main' );
$contents = $xtpl->text( 'main' );  

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';