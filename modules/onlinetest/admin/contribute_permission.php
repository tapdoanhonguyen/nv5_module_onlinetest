<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */
if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

$page_title = $lang_module['contribute_permission'];
$groups_list = nv_groups_list();
$data = array();
$getPermission = array();
 
$result = $db->query('SELECT group_id, permission FROM ' . TABLE_ONLINETEST_NAME . '_contribute_permission');
while( $rows = $result->fetch() )
{
	if( isset( $groups_list[$rows['group_id']] ) )
	{
		$getPermission[$rows['group_id']] = @unserialize( $rows['permission'] );
	}
	else
	{
		$db->query('DELETE FROM ' . TABLE_ONLINETEST_NAME . '_contribute_permission WHERE group_id=' . intval( $rows['group_id'] ) );

	}
}
$result->closeCursor();
if( $nv_Request->get_int( 'save', 'post' ) == 1 )
{
	$permission = $nv_Request->get_typed_array( 'permission', 'post', 'array', array() );
 
	if( empty( $permission ) )
	{
		$db->query('TRUNCATE ' . TABLE_ONLINETEST_NAME . '_contribute_permission');	
	}else
	{
		$dataGroup = array();
		$result = $db->query( 'SELECT group_id FROM ' . TABLE_ONLINETEST_NAME . '_contribute_permission ORDER BY group_id ASC' );
		while( $rows = $result->fetch() )
		{
			$dataGroup[$rows['group_id']] = $rows['group_id']; 
		}
		$result->closeCursor();
		try
		{
			
			foreach( $permission as $group_id => $_permission )
			{
				if( isset( $_permission['group_id'] ) )
				{
					unset( $_permission['group_id'] );
					$_permission = serialize( $_permission );
					if( in_array( $group_id, $dataGroup ) )
					{
						unset( $dataGroup[$group_id] );
						$stmt = $db->prepare( '
						UPDATE ' . TABLE_ONLINETEST_NAME . '_contribute_permission 
						SET 
							permission=:permission, 
							date_modified=' . NV_CURRENTTIME .' 
						WHERE group_id=' . intval( $group_id ));
						$stmt->bindParam( ':permission', $_permission, PDO::PARAM_STR, strlen(  $_permission ) );
						$stmt->execute();
						$stmt->closeCursor();
						unset($stmt);
						
					}else
					{
						
						$stmt = $db->prepare( '
						INSERT INTO ' . TABLE_ONLINETEST_NAME . '_contribute_permission 
						SET 
							group_id='. intval( $group_id ) .', 
							permission=:permission, 
							date_added=' . NV_CURRENTTIME );
						$stmt->bindParam( ':permission', $_permission, PDO::PARAM_STR, strlen(  $_permission ) );
						$stmt->execute();
						$stmt->closeCursor();
						unset($stmt);
					}
					
					
					
				}

			}
			
			if( !empty( $dataGroup ) )
			{
				$db->query('DELETE FROM ' . TABLE_ONLINETEST_NAME . '_contribute_permission WHERE group_id IN ( '. implode(',', $dataGroup ) .' )' );

			}
			 
		}
		catch ( PDOException $e )
		{
			$error['warning'] = $lang_module['contribute_permission_error_save'];
			var_dump( $e );
			die( 'ok' );
		}
		
	}
	
	 
	if( empty( $error ) )
	{
		$nv_Cache->delMod( $module_name );
		Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=contribute_permission' );
		die();
	}

}

$xtpl = new XTemplate( 'contribute_permission_add.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
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
$xtpl->assign( 'CAPTION', $lang_module['contribute_permission'] );
$xtpl->assign( 'DATA', $data );
$xtpl->assign( 'CANCEL', NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=" . $op );
if( $nv_Request->get_string( $module_data . '_success', 'session' ) )
{
	$xtpl->assign( 'SUCCESS', $nv_Request->get_string( $module_data . '_success', 'session' ) );

	$xtpl->parse( 'main.success' );

	$nv_Request->unset_request( $module_data . '_success', 'session' );

} 
if( $groups_list )
{
	 
	foreach( $groups_list as $group_id => $name )
	{
		if( !in_array( $group_id, array(1,2,5,6,7) ) )
		{
 	 
			$xtpl->assign( 'GROUP', array( 'key' => $group_id, 'name' => $name, 'checked' => isset( $getPermission[$group_id] ) ? 'checked="checked"' : '' ) );
			
			if( $onlineTestPermissionAll )
			{
				foreach( $onlineTestPermissionAll as $key => $permission )
				{
				
					$checked = isset( $getPermission[$group_id][$key] ) ? 'checked="checked"' : '';
					$xtpl->assign( 'PERMISSION', array( 'key'=> $key, 'name'=> $permission, 'checked' => $checked ) );
			
					$xtpl->parse( 'main.group.permission' );
					
				}
			}
			$xtpl->parse( 'main.group' );
		}
	}
}

if( isset( $error['warning'] ) )
{
	$xtpl->assign( 'WARNING', $error['warning'] );
	$xtpl->parse( 'main.error_warning' );
}

$xtpl->parse( 'main' );
$contents = $xtpl->text( 'main' );
include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';
