<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

$page_title = $lang_module['recharge'];

function recharge_fix_weight()
{
	global $db_slave;
	$sql = 'SELECT recharge_id FROM ' . TABLE_ONLINETEST_NAME . '_recharge ORDER BY weight ASC';
	$result = $db_slave->query( $sql );
	$weight = 0;
	while( $row = $result->fetch() )
	{
		++$weight;
		$db_slave->query( 'UPDATE ' . TABLE_ONLINETEST_NAME . '_recharge SET weight=' . $weight . ' WHERE recharge_id=' . $row['recharge_id'] );
	}
	$result->closeCursor();
}

if( ACTION_METHOD == 'delete' )
{
	$json = array();

	$recharge_id = $nv_Request->get_int( 'recharge_id', 'post', 0 );

	$token = $nv_Request->get_title( 'token', 'post', '', 1 );

	$listid = $nv_Request->get_string( 'listid', 'post', '' );

	if( $listid != '' and md5( $nv_Request->session_id . $global_config['sitekey'] ) == $token )
	{
		$del_array = array_map( 'intval', explode( ',', $listid ) );
	}
	elseif( $token == md5( $nv_Request->session_id . $global_config['sitekey'] . $recharge_id ) )
	{
		$del_array = array( $recharge_id );
	}

	if( ! empty( $del_array ) )
	{

		$_del_array = array();

		$a = 0;
		foreach( $del_array as $recharge_id )
		{

			if( $rows_total = $db_slave->query( 'SELECT COUNT(*) FROM ' . TABLE_ONLINETEST_NAME . '_rows WHERE recharge_id = ' . ( int )$recharge_id )->fetchColumn() )
			{
				$json['error'] = sprintf( $lang_module['recharge_error_rows'], $rows_total );
			}
			else
			{
				$db_slave->query( 'DELETE FROM ' . TABLE_ONLINETEST_NAME . '_recharge WHERE recharge_id = ' . ( int )$recharge_id );

				$json['id'][$a] = $recharge_id;

				$_del_array[] = $recharge_id;

				++$a;
			}
		}

		$count = sizeof( $_del_array );

		if( $count )
		{
			recharge_fix_weight();

			nv_insert_logs( NV_LANG_DATA, $module_name, 'log_del_recharge', implode( ', ', $_del_array ), $admin_info['userid'] );

			$nv_Cache->delMod($module_name);

			$json['success'] = $lang_module['recharge_delete_success'];
		}

	}
	else
	{
		$json['error'] = $lang_module['recharge_error_security'];
	}

	nv_jsonOutput( $json );
}
elseif( ACTION_METHOD == 'status' )
{
	$json = array();

	$recharge_id = $nv_Request->get_int( 'recharge_id', 'post', 0 );
	$new_vid = $nv_Request->get_int( 'new_vid', 'post', 0 );
	$token = $nv_Request->get_title( 'token', 'post', '', 1 );

	if( $token == md5( $nv_Request->session_id . $global_config['sitekey'] . $recharge_id ) )
	{
		$sql = 'UPDATE ' . TABLE_ONLINETEST_NAME . '_recharge SET status=' . $new_vid . ' WHERE recharge_id=' . $recharge_id;
		if( $db_slave->exec( $sql ) )
		{
			nv_insert_logs( NV_LANG_DATA, $module_name, 'log_change_status_recharge', 'recharge_id:' . $recharge_id, $admin_info['userid'] );

			$nv_Cache->delMod($module_name);

			$json['success'] = $lang_module['recharge_status_success'];

		}
		else
		{
			$json['error'] = $lang_module['recharge_error_status'];

		}
	}
	else
	{
		$json['error'] = $lang_module['recharge_error_security'];
	}

	nv_jsonOutput( $json );
}
elseif( ACTION_METHOD == 'weight' )
{
	$json = array();

	$recharge_id = $nv_Request->get_int( 'recharge_id', 'post', 0 );
	$new_vid = $nv_Request->get_int( 'new_vid', 'post', 0 );
	$token = $nv_Request->get_title( 'token', 'post', '', 1 );

	if( $token == md5( $nv_Request->session_id . $global_config['sitekey'] . $recharge_id ) )
	{
		$sql = 'SELECT recharge_id FROM ' . TABLE_ONLINETEST_NAME . '_recharge WHERE recharge_id!=' . $recharge_id . ' ORDER BY weight ASC';
		$result = $db_slave->query( $sql );

		$weight = 0;
		while( $row = $result->fetch() )
		{
			++$weight;
			if( $weight == $new_vid ) ++$weight;
			$sql = 'UPDATE ' . TABLE_ONLINETEST_NAME . '_recharge SET weight=' . $weight . ' WHERE recharge_id=' . intval( $row['recharge_id'] );
			$db_slave->query( $sql );
		}

		$sql = 'UPDATE ' . TABLE_ONLINETEST_NAME . '_recharge SET weight=' . $new_vid . ' WHERE recharge_id=' . $recharge_id;
		if( $db_slave->exec( $sql ) )
		{
			nv_insert_logs( NV_LANG_DATA, $module_name, 'log_change_weight_recharge', 'recharge_id:' . $recharge_id, $admin_info['userid'] );

			$nv_Cache->delMod($module_name);

			$json['success'] = $lang_module['recharge_weight_success'];
			$json['link'] = NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op;

		}
		else
		{
			$json['error'] = $lang_module['recharge_error_weight'];

		}
	}
	else
	{
		$json['error'] = $lang_module['recharge_error_security'];
	}

	nv_jsonOutput( $json );
}
elseif( ACTION_METHOD == 'add' || ACTION_METHOD == 'edit' )
{

	$data = array(
		'recharge_id' => 0,
		'title' => '',
		'weight' => '',
		'date_added' => NV_CURRENTTIME );

	$error = array();

	$data['recharge_id'] = $nv_Request->get_int( 'recharge_id', 'get,post', 0 );
	if( $data['recharge_id'] > 0 )
	{
		$data = $db_slave->query( 'SELECT *
		FROM ' . TABLE_ONLINETEST_NAME . '_recharge  
		WHERE recharge_id=' . $data['recharge_id'] )->fetch();

		$caption = $lang_module['recharge_edit'];
	}
	else
	{
		$caption = $lang_module['recharge_add'];
	}

	if( $nv_Request->get_int( 'save', 'post' ) == 1 )
	{

		$data['recharge_id'] = $nv_Request->get_int( 'recharge_id', 'post', 0 );
		$data['title'] = nv_substr( $nv_Request->get_title( 'title', 'post', '', '' ), 0, 255 );

		if( empty( $data['title'] ) )
		{
			$error['title'] = $lang_module['recharge_error_title'];
		}
		
		if( ! empty( $error ) && ! isset( $error['warning'] ) )
		{
			$error['warning'] = $lang_module['recharge_error_warning'];
		}

		if( empty( $error ) )
		{
			if( $data['recharge_id'] == 0 )
			{
				try
				{
					$stmt = $db_slave->prepare( 'SELECT MAX(weight) FROM ' . TABLE_ONLINETEST_NAME . '_recharge' );
					$stmt->execute();
					$weight = $stmt->fetchColumn();

					$weight = intval( $weight ) + 1;

					$stmt = $db_slave->prepare( 'INSERT INTO ' . TABLE_ONLINETEST_NAME . '_recharge SET 
						weight = ' . intval( $weight ) . ', 
						title =:title' );

					$stmt->bindParam( ':title', $data['title'], PDO::PARAM_STR );
					$stmt->execute();

					if( $data['recharge_id'] = $db_slave->lastInsertId() )
					{
						
						$nv_Request->set_Session( $module_data . '_success', $lang_module['recharge_insert_success'] );
						
						nv_insert_logs( NV_LANG_DATA, $module_name, 'Thêm khu vực', 'recharge_id: ' . $data['recharge_id'], $admin_info['userid'] );
						
						
					}
					else
					{
						$error['warning'] = $lang_module['recharge_error_save'];

					}
					$stmt->closeCursor();

				}
				catch ( PDOException $e )
				{
					$error['warning'] = $lang_module['recharge_error_save'];
					//var_dump($e);die();
				}

			}
			else
			{
				try
				{

					$stmt = $db_slave->prepare( 'UPDATE ' . TABLE_ONLINETEST_NAME . '_recharge SET 
						title =:title 
						WHERE recharge_id=' . $data['recharge_id'] );

					$stmt->bindParam( ':title', $data['title'], PDO::PARAM_STR );
					if( $stmt->execute() )
					{
						$nv_Request->set_Session( $module_data . '_success', $lang_module['recharge_update_success'] );
						
						nv_insert_logs( NV_LANG_DATA, $module_name, 'Chỉnh sửa khu vực', 'recharge_id: ' . $data['recharge_id'], $admin_info['userid'] );
					}
					else
					{
						$error['warning'] = $lang_module['recharge_error_save'];

					}

					$stmt->closeCursor();

				}
				catch ( PDOException $e )
				{
					$error['warning'] = $lang_module['recharge_error_save'];
					//var_dump($e);
				}

			}

		}
		if( empty( $error ) )
		{
			$nv_Cache->delMod($module_name); 

			Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
			die();
		}

	}

	$xtpl = new XTemplate( 'recharge_add.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
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
	$xtpl->assign( 'DATA', $data );
	$xtpl->assign( 'CANCEL', NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=" . $op );

	
	if( isset( $error['warning'] ) )
	{
		$xtpl->assign( 'error_warning', $error['warning'] );
		$xtpl->parse( 'main.error_warning' );
	}

	if( isset( $error['title'] ) )
	{
		$xtpl->assign( 'error_title', $error['title'] );
		$xtpl->parse( 'main.error_title' );
	}

 

	$xtpl->parse( 'main' );
	$contents = $xtpl->text( 'main' );

	include NV_ROOTDIR . '/includes/header.php';
	echo nv_admin_theme( $contents );
	include NV_ROOTDIR . '/includes/footer.php';
}

/*show list recharge*/

$per_page = 50;
$page = $nv_Request->get_int( 'page', 'get', 1 );
$data['username'] = $nv_Request->get_string( 'username', 'get', '' );
$data['seri_number'] = $nv_Request->get_string( 'seri_number', 'get', '' );
$data['pin_number'] = $nv_Request->get_string( 'pin_number', 'get', '' );
$data['date_from'] = $nv_Request->get_title( 'date_from', 'get', '' );
$data['date_to'] = $nv_Request->get_title( 'date_to', 'get', '' );
$sort = $nv_Request->get_string( 'sort', 'get', '' );
$order = $nv_Request->get_string( 'order', 'get' ) == 'desc' ? 'desc' : 'asc';
 
if( preg_match( '/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $data['date_from'], $m ) )
{

	$date_from = mktime( 0, 0, 0, $m[2], $m[1], $m[3] );
}
else
{
	$date_from = 0;
}
if( preg_match( '/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $data['date_to'], $m ) )
{

	$date_to = mktime( 23, 59, 59, $m[2], $m[1], $m[3] );
}
else
{
	$date_to = 0;
}
$implode = array();


if( $data['username'] )
{
	$implode[] = 'u.username=' . $db_slave->quote( $data['username'] );
}
if( $data['seri_number'] )
{
	$implode[] = 'r.seri_number=' . $db_slave->quote( $data['seri_number'] );
}
if( $data['pin_number'] )
{
	$implode[] = 'r.pin_number=' . $db_slave->quote( $data['pin_number'] );
}
if( $date_from && $date_to )
{
 
	$implode[] = 'r.date_added BETWEEN ' . intval( $date_from ) . ' AND ' . intval( $date_to );
}

$sql = TABLE_ONLINETEST_NAME . '_recharge r LEFT JOIN '. NV_USERS_GLOBALTABLE .' u ON r.userid = u.userid';

if( !empty( $implode ) )
{
	$sql.=' WHERE ' . implode('AND ', $implode );
	
}
 
$sort_data = array(
	'r.supplier',
	'r.money',
	'r.date_added' );

if( isset( $sort ) && in_array( $sort, $sort_data ) )
{

	$sql .= ' ORDER BY ' . $sort;
}
else
{
	$sql .= ' ORDER BY r.date_added';
}

if( isset( $order ) && ( $order == 'desc' ) )
{
	$sql .= ' DESC';
}
else
{
	$sql .= ' ASC';
}

$total = $db_slave->query( 'SELECT SUM(money) FROM ' . $sql )->fetchColumn();


$num_items = $db_slave->query( 'SELECT COUNT(*) FROM ' . $sql )->fetchColumn();

$base_url = NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=recharge&amp;sort=' . $sort . '&amp;order=' . $order . '&amp;per_page=' . $per_page;

$db_slave->sqlreset()->select( 'r.*, u.username' )->from( $sql )->limit( $per_page )->offset( ( $page - 1 ) * $per_page );

$result = $db_slave->query( $db_slave->sql() );

$array = array();
while( $rows = $result->fetch() )
{
	$array[] = $rows;
}

$xtpl = new XTemplate( 'recharge.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
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
$xtpl->assign( 'DATA', $data );
$xtpl->assign( 'TOTAL', number_format( intval( $total ), 0,',','.') );

$xtpl->assign( 'TOKEN', md5( $nv_Request->session_id . $global_config['sitekey'] ) );
$xtpl->assign( 'ADD_NEW', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=recharge&action=add' );

$order2 = ( $order == 'asc' ) ? 'desc' : 'asc';

$xtpl->assign( 'URL_SUPPLIER', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=r.supplier&amp;order=' . $order2 . '&amp;per_page=' . $per_page );
$xtpl->assign( 'URL_MONEY', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=r.money&amp;order=' . $order2 . '&amp;per_page=' . $per_page );

$xtpl->assign( 'SUPPLIER_ORDER', ( $sort == 'r.supplier' ) ? 'class="' . $order2 . '"' : '' );
$xtpl->assign( 'MONEY_ORDER', ( $sort == 'r.money' ) ? 'class="' . $order2 . '"' : '' );


if( $nv_Request->get_string( $module_data . '_success', 'session' ) )
{
	$xtpl->assign( 'SUCCESS', $nv_Request->get_string( $module_data . '_success', 'session' ) );

	$xtpl->parse( 'main.success' );

	$nv_Request->unset_request( $module_data . '_success', 'session' );

}


if( ! empty( $array ) )
{
	$stt = 1;
	foreach( $array as $item )
	{
		$item['stt'] = $stt;
		$item['date_added'] = date( 'H:s, d/m/Y', $item['date_added'] );
		$item['token'] = md5( session_id() . $global_config['sitekey'] . $item['recharge_id'] );
		$item['edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&action=edit&token=' . $item['token'] . '&recharge_id=' . $item['recharge_id'];

		$xtpl->assign( 'LOOP', $item );
		
		$xtpl->parse( 'main.loop' );
		++$stt;
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
