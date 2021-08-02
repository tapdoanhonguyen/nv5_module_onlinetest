<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

$page_title = $lang_module['rechargebank'];

function rechargebank_fix_weight()
{
	global $db_slave;
	$sql = 'SELECT rechargebank_id FROM ' . TABLE_ONLINETEST_NAME . '_rechargebank ORDER BY weight ASC';
	$result = $db_slave->query( $sql );
	$weight = 0;
	while( $row = $result->fetch() )
	{
		++$weight;
		$db_slave->query( 'UPDATE ' . TABLE_ONLINETEST_NAME . '_rechargebank SET weight=' . $weight . ' WHERE rechargebank_id=' . $row['rechargebank_id'] );
	}
	$result->closeCursor();
}

if( ACTION_METHOD == 'delete' )
{
	$json = array();

	$rechargebank_id = $nv_Request->get_int( 'rechargebank_id', 'post', 0 );

	$token = $nv_Request->get_title( 'token', 'post', '', 1 );

	$listid = $nv_Request->get_string( 'listid', 'post', '' );

	if( $listid != '' and md5( $nv_Request->session_id . $global_config['sitekey'] ) == $token )
	{
		$del_array = array_map( 'intval', explode( ',', $listid ) );
	}
	elseif( $token == md5( $nv_Request->session_id . $global_config['sitekey'] . $rechargebank_id ) )
	{
		$del_array = array( $rechargebank_id );
	}

	if( ! empty( $del_array ) )
	{

		$_del_array = array();

		$a = 0;
		foreach( $del_array as $rechargebank_id )
		{

			if( $rows_total = $db_slave->query( 'SELECT COUNT(*) FROM ' . TABLE_ONLINETEST_NAME . '_rows WHERE rechargebank_id = ' . ( int )$rechargebank_id )->fetchColumn() )
			{
				$json['error'] = sprintf( $lang_module['rechargebank_error_rows'], $rows_total );
			}
			else
			{
				$db_slave->query( 'DELETE FROM ' . TABLE_ONLINETEST_NAME . '_rechargebank WHERE rechargebank_id = ' . ( int )$rechargebank_id );

				$json['id'][$a] = $rechargebank_id;

				$_del_array[] = $rechargebank_id;

				++$a;
			}
		}

		$count = sizeof( $_del_array );

		if( $count )
		{
			rechargebank_fix_weight();

			nv_insert_logs( NV_LANG_DATA, $module_name, 'log_del_rechargebank', implode( ', ', $_del_array ), $admin_info['userid'] );

			$nv_Cache->delMod($module_name);

			$json['success'] = $lang_module['rechargebank_delete_success'];
		}

	}
	else
	{
		$json['error'] = $lang_module['rechargebank_error_security'];
	}

	nv_jsonOutput( $json );
}
elseif( ACTION_METHOD == 'weight' )
{
	$json = array();

	$rechargebank_id = $nv_Request->get_int( 'rechargebank_id', 'post', 0 );
	$new_vid = $nv_Request->get_int( 'new_vid', 'post', 0 );
	$token = $nv_Request->get_title( 'token', 'post', '', 1 );

	if( $token == md5( $nv_Request->session_id . $global_config['sitekey'] . $rechargebank_id ) )
	{
		$sql = 'SELECT rechargebank_id FROM ' . TABLE_ONLINETEST_NAME . '_rechargebank WHERE rechargebank_id!=' . $rechargebank_id . ' ORDER BY weight ASC';
		$result = $db_slave->query( $sql );

		$weight = 0;
		while( $row = $result->fetch() )
		{
			++$weight;
			if( $weight == $new_vid ) ++$weight;
			$sql = 'UPDATE ' . TABLE_ONLINETEST_NAME . '_rechargebank SET weight=' . $weight . ' WHERE rechargebank_id=' . intval( $row['rechargebank_id'] );
			$db_slave->query( $sql );
		}

		$sql = 'UPDATE ' . TABLE_ONLINETEST_NAME . '_rechargebank SET weight=' . $new_vid . ' WHERE rechargebank_id=' . $rechargebank_id;
		if( $db_slave->exec( $sql ) )
		{
			nv_insert_logs( NV_LANG_DATA, $module_name, 'log_change_weight_rechargebank', 'rechargebank_id:' . $rechargebank_id, $admin_info['userid'] );

			$nv_Cache->delMod($module_name);

			$json['success'] = $lang_module['rechargebank_weight_success'];
			$json['link'] = NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op;

		}
		else
		{
			$json['error'] = $lang_module['rechargebank_error_weight'];

		}
	}
	else
	{
		$json['error'] = $lang_module['rechargebank_error_security'];
	}

	nv_jsonOutput( $json );
}
elseif( ACTION_METHOD == 'getUsername' )
{
	$json = array();
	$data = array();
	
	$username = $nv_Request->get_title( 'username', 'get', '' );
 
	if( ! empty( $username ) )
	{
		$db->sqlreset()
			->select( 'userid, username' )
			->from( NV_USERS_GLOBALTABLE )
			->where( 'username LIKE :username' )
			->limit( '10' );

		$sth = $db->prepare( $db->sql() );
		$sth->bindValue( ':username', '%' . $username . '%' );
		$sth->execute();
		
		while( $rows = $sth->fetch() )
		{
			$json[] = $rows;
		}
	}
 
	nv_jsonOutput( $json );
	
}elseif( ACTION_METHOD == 'getBank' )
{
	$json = array();
 
	$banktitle = $nv_Request->get_title( 'banktitle', 'get', '' );
 
	if( ! empty( $banktitle ) )
	{
		$db->sqlreset()
			->select( 'bank_id, title, code' )
			->from( TABLE_ONLINETEST_NAME . '_bank' )
			->where( 'title LIKE :title OR code LIKE :code' )
			->limit( '10' );

		$sth = $db->prepare( $db->sql() );
		$sth->bindValue( ':title', '%' . $banktitle . '%' );
		$sth->bindValue( ':code', '%' . $banktitle . '%' );
		$sth->execute();
		
		while( $rows = $sth->fetch() )
		{
			$json[] = array( 'bank_id'=> $rows['bank_id'], 'title'=> $rows['title'] . '('. $rows['code'] .')' );
		}
 
	}
 
	nv_jsonOutput( $json );
	
}elseif( ACTION_METHOD == 'add' || ACTION_METHOD == 'edit' )
{
	 
	$data = array(
		'rechargebank_id' => 0,
		'username' => '',
		'userid' => 0,
		'bank_id' => 0,
		'banktitle' => '',
		'transaction' => '',
		'money' => '',
		'note' => '',
		'date_added' => NV_CURRENTTIME );

	$error = array();

	$data['rechargebank_id'] = $nv_Request->get_int( 'rechargebank_id', 'get,post', 0 );
	if( $data['rechargebank_id'] > 0 )
	{
		$data = $db_slave->query( 'SELECT *
		FROM ' . TABLE_ONLINETEST_NAME . '_rechargebank  
		WHERE rechargebank_id=' . $data['rechargebank_id'] )->fetch();
		list( $data['username'] ) = $db_slave->query('SELECT username FROM ' . NV_USERS_GLOBALTABLE . ' WHERE userid=' . $db_slave->quote( $data['userid'] ) )->fetch( 3 );
		list( $data['bank_id'], $data['banktitle'] , $data['bankcode'] ) = $db_slave->query('SELECT bank_id, title, code FROM ' . TABLE_ONLINETEST_NAME . '_bank WHERE bank_id=' . intval( $data['bank_id'] ) )->fetch( 3 );
		$data['banktitle'] = $data['banktitle'] . '('. $data['bankcode'] .')';
		$caption = $lang_module['rechargebank_edit'];
	}
	else
	{
		$caption = $lang_module['rechargebank_add'];
	}

	if( $nv_Request->get_int( 'save', 'post' ) == 1 )
	{

		$data['rechargebank_id'] = $nv_Request->get_int( 'rechargebank_id', 'post', 0 );
		$data['bank_id'] = $nv_Request->get_int( 'bank_id', 'post', 0 );
		$data['username'] = $nv_Request->get_title( 'username', 'post', '', '' );
		$data['banktitle'] = $nv_Request->get_title( 'banktitle', 'post', '', '' );
		
		list( $data['userid'] ) = $db_slave->query('SELECT userid FROM ' . NV_USERS_GLOBALTABLE . ' WHERE username=' . $db_slave->quote( $data['username'] ) )->fetch( 3 );
		list( $data['bank_id'], $data['banktitle'] ) = $db_slave->query('SELECT bank_id, title FROM ' . TABLE_ONLINETEST_NAME . '_bank WHERE bank_id=' . intval( $data['bank_id'] ) )->fetch( 3 );
 
		$data['transaction'] = nv_substr( $nv_Request->get_title( 'transaction', 'post', '', '' ), 0, 250 );
		
		$data['money'] = $nv_Request->get_title( 'money', 'post', '', '' );
		$data['money'] = preg_replace( '/([^0-9\.])/', '', $data['money'] );
		
		$data['note'] = nv_substr( $nv_Request->get_title( 'note', 'post', '', '' ), 0, 250 );
 
		if( empty( $data['username'] ) )
		{
			$error['username'] = $lang_module['rechargebank_error_username'];
		}
		if( empty( $data['bank_id'] ) )
		{
			$error['bank_id'] = $lang_module['rechargebank_error_bank_id'];
		}
		if( empty( $data['transaction'] ) )
		{
			$error['transaction'] = $lang_module['rechargebank_error_transaction'];
		}
		if( empty( $data['money'] ) )
		{
			$error['money'] = $lang_module['rechargebank_error_money'];
		}
		
		if( ! empty( $error ) && ! isset( $error['warning'] ) )
		{
			$error['warning'] = $lang_module['rechargebank_error_warning'];
		}

		if( empty( $error ) )
		{
			if( $data['rechargebank_id'] == 0 )
			{
				try
				{
 
					$stmt = $db_slave->prepare( 'INSERT INTO ' . TABLE_ONLINETEST_NAME . '_rechargebank SET 
						userid=' . intval( $data['userid'] ) . ', 
						bank_id=' . intval( $data['bank_id'] ) . ', 
						date_added=' . intval( $data['date_added'] ) . ', 
						transaction=:transaction,
						money=:money,
						note=:note' );

					$stmt->bindParam( ':transaction', $data['transaction'], PDO::PARAM_STR );
					$stmt->bindParam( ':money', $data['money'], PDO::PARAM_STR );
					$stmt->bindParam( ':note', $data['note'], PDO::PARAM_STR );
					$stmt->execute();

					if( $data['rechargebank_id'] = $db_slave->lastInsertId() )
					{
 
						$db_slave->query( 'INSERT INTO ' . TABLE_ONLINETEST_NAME . '_point (userid, point) VALUES ('. intval( $data['userid'] ) .', '. intval( $data['money'] ) .') ON DUPLICATE KEY UPDATE point = point + '. intval( $data['money'] ) );
 
						$nv_Request->set_Session( $module_data . '_success', $lang_module['rechargebank_insert_success'] );
						
						nv_insert_logs( NV_LANG_DATA, $module_name, 'Thêm khu vực', 'rechargebank_id: ' . $data['rechargebank_id'], $admin_info['userid'] );
 
					}
					else
					{
						$error['warning'] = $lang_module['rechargebank_error_save'];

					}
					$stmt->closeCursor();

				}
				catch ( PDOException $e )
				{
					$error['warning'] = $lang_module['rechargebank_error_save'];
					//var_dump($e);die();
				}

			}
			else
			{
				/* try
				{

					$stmt = $db_slave->prepare( 'UPDATE ' . TABLE_ONLINETEST_NAME . '_rechargebank SET 
						userid=' . intval( $data['userid'] ) . ', 
						bank_id=' . intval( $data['bank_id'] ) . ', 
						transaction=:transaction,
						money=:money,
						note=:note 
						WHERE rechargebank_id=' . $data['rechargebank_id'] );

					$stmt->bindParam( ':transaction', $data['transaction'], PDO::PARAM_STR );
					$stmt->bindParam( ':money', $data['money'], PDO::PARAM_STR );
					$stmt->bindParam( ':note', $data['note'], PDO::PARAM_STR );
					
					if( $stmt->execute() )
					{
						$nv_Request->set_Session( $module_data . '_success', $lang_module['rechargebank_update_success'] );
						
						nv_insert_logs( NV_LANG_DATA, $module_name, 'Chỉnh sửa khu vực', 'rechargebank_id: ' . $data['rechargebank_id'], $admin_info['userid'] );
					}
					else
					{
						$error['warning'] = $lang_module['rechargebank_error_save'];

					}

					$stmt->closeCursor();

				}
				catch ( PDOException $e )
				{
					$error['warning'] = $lang_module['rechargebank_error_save'];
					//var_dump($e);
				} */

			}

		}
		if( empty( $error ) )
		{
			$nv_Cache->delMod($module_name); 

			Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
			die();
		}

	}

	$xtpl = new XTemplate( 'rechargebank_add.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'NV_LANG_VARIABLE', NV_LANG_VARIABLE );
	$xtpl->assign( 'NV_LANG_DATA', NV_LANG_DATA );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'THEME', $global_config['module_theme'] );
	$xtpl->assign( 'NV_BASE_ADMINURL', NV_BASE_ADMINURL );
	$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
	$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
	$xtpl->assign( 'MODULE_FILE', $module_file );
	$xtpl->assign( 'MODULE_NAME', $module_name );
	$xtpl->assign( 'OP', $op );
	$xtpl->assign( 'CAPTION', $caption );
	$xtpl->assign( 'DATA', $data );
	$xtpl->assign( 'DISABLED', ( $data['rechargebank_id'] ) ? 'disabled="disabled"' : '' );
	$xtpl->assign( 'CANCEL', NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=" . $op );

	
	if( isset( $error['warning'] ) )
	{
		$xtpl->assign( 'error_warning', $error['warning'] );
		$xtpl->parse( 'main.error_warning' );
	}

	if( isset( $error['username'] ) )
	{
		$xtpl->assign( 'error_username', $error['username'] );
		$xtpl->parse( 'main.error_username' );
	}
	if( isset( $error['bank_id'] ) )
	{
		$xtpl->assign( 'error_bank_id', $error['bank_id'] );
		$xtpl->parse( 'main.error_bank_id' );
	}
	if( isset( $error['transaction'] ) )
	{
		$xtpl->assign( 'error_transaction', $error['transaction'] );
		$xtpl->parse( 'main.error_transaction' );
	}

	if( isset( $error['money'] ) )
	{
		$xtpl->assign( 'error_money', $error['money'] );
		$xtpl->parse( 'main.error_money' );
	}
 
	$xtpl->parse( 'main' );
	$contents = $xtpl->text( 'main' );

	include NV_ROOTDIR . '/includes/header.php';
	echo nv_admin_theme( $contents );
	include NV_ROOTDIR . '/includes/footer.php';
}

/*show list rechargebank*/

$per_page = 50;
$page = $nv_Request->get_int( 'page', 'get', 1 );
$data['username'] = $nv_Request->get_string( 'username', 'get', '' );
$data['transaction'] = $nv_Request->get_string( 'transaction', 'get', '' );
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
 
if( $date_from && $date_to )
{
 
	$implode[] = 'r.date_added BETWEEN ' . intval( $date_from ) . ' AND ' . intval( $date_to );
}

$sql = TABLE_ONLINETEST_NAME . '_rechargebank r LEFT JOIN '. NV_USERS_GLOBALTABLE .' u ON r.userid = u.userid';

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

$base_url = NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=rechargebank&amp;sort=' . $sort . '&amp;order=' . $order . '&amp;per_page=' . $per_page;

$db_slave->sqlreset()->select( 'r.*, u.username' )->from( $sql )->limit( $per_page )->offset( ( $page - 1 ) * $per_page );

$result = $db_slave->query( $db_slave->sql() );

$array = array();
while( $rows = $result->fetch() )
{
	$array[] = $rows;
}

$xtpl = new XTemplate( 'rechargebank.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
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
$xtpl->assign( 'ADD_NEW', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=rechargebank&action=add' );

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
	$sql = 'SELECT bank_id, title, code FROM ' . TABLE_ONLINETEST_NAME . '_bank ORDER BY weight ASC';
	$onlineTestBank = $nv_Cache->db( $sql, 'bank_id', $module_name );

	
	$stt = 1;
	foreach( $array as $item )
	{
		$item['stt'] = $stt;
		$item['banktitle'] = $onlineTestBank[$item['bank_id']]['title'];
		$item['date_added'] = date( 'H:s, d/m/Y', $item['date_added'] );
		$item['token'] = md5( session_id() . $global_config['sitekey'] . $item['rechargebank_id'] );
		$item['edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&action=edit&token=' . $item['token'] . '&rechargebank_id=' . $item['rechargebank_id'];

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
