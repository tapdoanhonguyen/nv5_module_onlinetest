<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */

if( ! defined( 'NV_IS_MOD_TEST' ) ) die( 'Stop!!!' );

$page_title = $module_info['custom_title'];
$key_words = $module_info['keywords'];

if( ! in_array( $onlineTestConfig['default_group_teacher'], array_keys($groupUsers) ) )
{
	nv_redirect_location( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name, true );
}

/**
 * fix_group_user_order()
 *
 * @param integer $parent_id
 * @param integer $order
 * @param integer $lev
 * @return
 */
function fix_group_user_order( $parent_id = 0, $order = 0, $lev = 0 )
{
	global $db, $module_data;

	$sql = 'SELECT group_user_id, parent_id FROM ' . TABLE_ONLINETEST_NAME . '_group_user WHERE parent_id=' . $parent_id . ' ORDER BY weight ASC';
	$result = $db->query( $sql );
	$array_cat_order = array();
	while( $row = $result->fetch() )
	{
		$array_cat_order[] = $row['group_user_id'];
	}
	$result->closeCursor();
	$weight = 0;
	if( $parent_id > 0 )
	{
		++$lev;
	}
	else
	{
		$lev = 0;
	}
	foreach( $array_cat_order as $group_user_id_i )
	{
		++$order;
		++$weight;
		$sql = 'UPDATE ' . TABLE_ONLINETEST_NAME . '_group_user SET weight=' . $weight . ', sort=' . $order . ', lev=' . $lev . ' WHERE group_user_id=' . intval( $group_user_id_i );
		$db->query( $sql );
		$order = fix_group_user_order( $group_user_id_i, $order, $lev );
	}
	$numsubcat = $weight;
	if( $parent_id > 0 )
	{
		$sql = 'UPDATE ' . TABLE_ONLINETEST_NAME . '_group_user SET numsubcat=' . $numsubcat;
		if( $numsubcat == 0 )
		{
			$sql .= ',subcatid=\'\'';
		}
		else
		{
			$sql .= ',subcatid=\'' . implode( ',', $array_cat_order ) . '\'';
		}
		$sql .= ' WHERE group_user_id=' . intval( $parent_id );
		$db->query( $sql );
	}
	return $order;
}

if( ACTION_METHOD == 'delete' )
{
	$json = array();

	$group_user_id = $nv_Request->get_int( 'group_user_id', 'post', 0 );

	$token = $nv_Request->get_title( 'token', 'post', '', 1 );

	$listid = $nv_Request->get_string( 'listid', 'post', '' );

	if( $listid != '' and md5( $nv_Request->session_id . $global_config['sitekey'] ) == $token )
	{
		$del_array = array_map( 'intval', explode( ',', $listid ) );
	}
	elseif( $token == md5( $nv_Request->session_id . $global_config['sitekey'] . $group_user_id ) )
	{
		$del_array = array( $group_user_id );
	}

	if( ! empty( $del_array ) )
	{

		$_del_array = array();

		$a = 0;
		foreach( $del_array as $group_user_id )
		{
 
			$db->query( 'DELETE FROM ' . TABLE_ONLINETEST_NAME . '_group_user WHERE group_user_id = ' . ( int )$group_user_id );
			$db->query( 'DELETE FROM ' . TABLE_ONLINETEST_NAME . '_group_user_list WHERE group_user_id = ' . ( int )$group_user_id );

			$json['id'][$a] = $group_user_id;

			$_del_array[] = $group_user_id;

			++$a;
			 
		}

		$count = sizeof( $_del_array );

		if( $count )
		{
			fix_group_user_order();

			nv_insert_logs( NV_LANG_DATA, $module_name, 'log_del_group_user', implode( ', ', $_del_array ), $user_info['userid'] );

			$nv_Cache->delMod( $module_name );

			$json['success'] = $lang_module['group_user_delete_success'];
		}

	}
	else
	{
		$json['error'] = $lang_module['group_user_error_security'];
	}

	nv_jsonOutput( $json );
}
elseif( ACTION_METHOD == 'status' )
{
	$json = array();

	$group_user_id = $nv_Request->get_int( 'group_user_id', 'post', 0 );
	$new_vid = $nv_Request->get_int( 'new_vid', 'post', 0 );
	$token = $nv_Request->get_title( 'token', 'post', '', 1 );

	if( $token == md5( $nv_Request->session_id . $global_config['sitekey'] . $group_user_id ) )
	{
		$sql = 'UPDATE ' . TABLE_ONLINETEST_NAME . '_group_user SET status=' . $new_vid . ' WHERE group_user_id=' . $group_user_id;
		if( $db->exec( $sql ) )
		{
			nv_insert_logs( NV_LANG_DATA, $module_name, 'log_change_status_group_user', 'group_user_id:' . $group_user_id, $user_info['userid'] );

			$nv_Cache->delMod( $module_name );

			$json['success'] = $lang_module['group_user_status_success'];

		}
		else
		{
			$json['error'] = $lang_module['group_user_error_inhome'];

		}
	}
	else
	{
		$json['error'] = $lang_module['group_user_error_security'];
	}

	nv_jsonOutput( $json );
}
elseif( ACTION_METHOD == 'weight' )
{
	$json = array();

	$group_user_id = $nv_Request->get_int( 'group_user_id', 'post', 0 );
	$new_vid = $nv_Request->get_int( 'new_vid', 'post', 0 );
	$token = $nv_Request->get_title( 'token', 'post', '', 1 );

	if( $token == md5( $nv_Request->session_id . $global_config['sitekey'] . $group_user_id ) )
	{
		list( $group_user_id, $parent_id, $numsubcat ) = $db->query( 'SELECT group_user_id, parent_id, numsubcat FROM ' . TABLE_ONLINETEST_NAME . '_group_user WHERE group_user_id=' . $group_user_id )->fetch( 3 );
		if( $group_user_id > 0 )
		{

			$sql = 'SELECT group_user_id FROM ' . TABLE_ONLINETEST_NAME . '_group_user WHERE group_user_id!=' . $group_user_id . ' AND parent_id=' . $parent_id . ' ORDER BY weight ASC';
			$result = $db->query( $sql );

			$weight = 0;
			while( $row = $result->fetch() )
			{
				++$weight;
				if( $weight == $new_vid ) ++$weight;
				$sql = 'UPDATE ' . TABLE_ONLINETEST_NAME . '_group_user SET weight=' . $weight . ' WHERE group_user_id=' . intval( $row['group_user_id'] );
				$db->query( $sql );
			}

			$sql = 'UPDATE ' . TABLE_ONLINETEST_NAME . '_group_user SET weight=' . $new_vid . ' WHERE group_user_id=' . $group_user_id;
			if( $db->exec( $sql ) )
			{
				fix_group_user_order();

				nv_insert_logs( NV_LANG_DATA, $module_name, 'log_change_weight_group_user', 'group_user_id:' . $group_user_id, $user_info['userid'] );

				$nv_Cache->delMod( $module_name );

				$json['success'] = $lang_module['group_user_weight_success'];
				$json['link'] = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op, true );

			}
			else
			{
				$json['error'] = $lang_module['group_user_error_weight'];

			}
		}
	}
	else
	{
		$json['error'] = $lang_module['group_user_error_security'];
	}

	nv_jsonOutput( $json );
}
elseif( ACTION_METHOD == 'get_user' )
{
	$username = $nv_Request->get_string( 'username', 'get', '' );

	// $group_user_id = $nv_Request->get_int( 'group_user_id', 'post', 0 );

	// $userlist = $nv_Request->get_typed_array( 'userlist', 'post', 'int', array() );

	// $userlist = array_unique( array_filter( $userlist ) );

	$json = array();

	$and = '';
	if( ! empty( $username ) )
	{
		$and .= ' AND username LIKE :username';
	}
	$sql = 'SELECT userid, username FROM ' . NV_USERS_GLOBALTABLE . '  
	WHERE active=1 ' . $and . '
	ORDER BY username ASC LIMIT 0, 50';
	$sth = $db->prepare( $sql );
	if( ! empty( $username ) )
	{
		$sth->bindValue( ':username', '%' . $username . '%' );
	}
	$sth->execute();
	while( list( $userid, $username ) = $sth->fetch( 3 ) )
	{
		$json[] = array( 'userid' => $userid, 'username' => nv_htmlspecialchars( $username ) );
	}

	nv_jsonOutput( $json );

}
elseif( ACTION_METHOD == 'get_group' )
{
	$title = $nv_Request->get_string( 'title', 'get', '' );
	$json = array();

	$and = '';
	if( ! empty( $title ) )
	{
		$and .= ' AND title LIKE :title';
	}
	$sql = 'SELECT group_user_id, title FROM ' . TABLE_ONLINETEST_NAME . '_group_user  
	WHERE status=1 AND user_create_id = ' . intval( $user_info['userid'] ) . ' ' . $and . '
	ORDER BY title ASC LIMIT 0, 50';

	$sth = $db->prepare( $sql );
	if( ! empty( $title ) )
	{
		$sth->bindValue( ':title', '%' . $title . '%' );
	}
	$sth->execute();
	while( list( $group_user_id, $title ) = $sth->fetch( 3 ) )
	{
		$json[] = array( 'group_user_id' => $group_user_id, 'title' => nv_htmlspecialchars( $title ) );
	}

	nv_jsonOutput( $json );

}
elseif( ACTION_METHOD == 'userlist' )
{
	$per_page = 100;

	$data['group_user_id'] = $nv_Request->get_int( 'group_user_id', 'get', 0 );

	$page = $nv_Request->get_int( 'page', 'get', 1 );

	$sql = TABLE_ONLINETEST_NAME . '_group_user_list gul INNER JOIN ' . TABLE_ONLINETEST_NAME . '_group_user gu ON (gu.group_user_id = gul.group_user_id)  LEFT JOIN ' . NV_USERS_GLOBALTABLE . ' u ON (gul.userid = u.userid) WHERE gul.group_user_id = ' . $data['group_user_id'];

	$sort = $nv_Request->get_string( 'sort', 'get', '' );

	$order = $nv_Request->get_string( 'order', 'get' ) == 'desc' ? 'desc' : 'asc';

	$sort_data = array( 'u.username' );

	if( isset( $sort ) && in_array( $sort, $sort_data ) )
	{

		$sql .= ' ORDER BY ' . $sort;
	}
	else
	{
		$sql .= ' ORDER BY u.username';
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

	$base_url = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op, true ) . '?sort=' . $sort . '&amp;order=' . $order . '&amp;per_page=' . $per_page;

	$db->sqlreset()->select( 'gu.title group_title, gul.group_user_id, u.userid, u.username, u.first_name, u.last_name, u.active, u.email' )->from( $sql )->limit( $per_page )->offset( ( $page - 1 ) * $per_page );

	$result = $db->query( $db->sql() );

	$dataContent = array();
	while( $rows = $result->fetch() )
	{
		$dataContent[] = $rows;
	}

	$xtpl = new XTemplate( 'ThemeOnlineTestGroupUserlist.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'NV_LANG_VARIABLE', NV_LANG_VARIABLE );
	$xtpl->assign( 'NV_LANG_DATA', NV_LANG_DATA );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );

	$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
	$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
	$xtpl->assign( 'MODULE_FILE', $module_file );
	$xtpl->assign( 'MODULE_NAME', $module_name );
	$xtpl->assign( 'DATA', $data );
	$xtpl->assign( 'TOKEN', md5( $nv_Request->session_id . $global_config['sitekey'] ) );
	$xtpl->assign( 'ADD_NEW', nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op, true ) . '?action=add_user&token=' . md5( $nv_Request->session_id . $global_config['sitekey'] . $data['group_user_id'] ) . '&group_user_id=' . $data['group_user_id'] );
	$xtpl->assign( 'TOTAL_USER', $num_items );

	$order2 = ( $order == 'asc' ) ? 'desc' : 'asc';
	$xtpl->assign( 'URL_USERNAME', nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op, true ) . '?sort=gu.title&amp;order=' . $order2 . '&amp;group_user_id=' . $data['group_user_id'] . '&amp;per_page=' . $per_page );
	$xtpl->assign( 'URL_FULL_NAME', nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op, true ) . '?sort=gu.weight&amp;order=' . $order2 . '&amp;group_user_id=' . $data['group_user_id'] . '&amp;per_page=' . $per_page );
	$xtpl->assign( 'URL_ACTIVE', nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op, true ) . '?sort=gu.status&amp;order=' . $order2 . '&amp;group_user_id=' . $data['group_user_id'] . '&amp;per_page=' . $per_page );

	$xtpl->assign( 'USERNAME_ORDER', ( $sort == 'u.username' ) ? 'class="' . $order2 . '"' : '' );
	$xtpl->assign( 'FULL_NAME_ORDER', ( $sort == 'u.full_name' ) ? 'class="' . $order2 . '"' : '' );
	$xtpl->assign( 'ACTIVE_ORDER', ( $sort == 'u.active' ) ? 'class="' . $order2 . '"' : '' );

	if( $nv_Request->get_string( $module_data . '_success', 'session' ) )
	{
		$xtpl->assign( 'SUCCESS', $nv_Request->get_string( $module_data . '_success', 'session' ) );

		$xtpl->parse( 'main.success' );

		$nv_Request->unset_request( $module_data . '_success', 'session' );

	}

	if( ! empty( $dataContent ) )
	{
		foreach( $dataContent as $item )
		{

			$item['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $item['userid'] );
			$item['full_name'] = nv_show_name_user( $item['first_name'], $item['last_name'], $item['username'] );

			$item['user_link'] = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=users&' . NV_OP_VARIABLE . '=edit', true ) . '?userid=' . $item['userid'];
			$item['active_checked'] = ( $item['active'] ) ? 'checked="checked"' : '';

			$xtpl->assign( 'LOOP', $item );

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
	echo nv_site_theme( $contents );
	include NV_ROOTDIR . '/includes/footer.php';

}
elseif( ACTION_METHOD == 'add_user' )
{
	$data = array();
	$dataUsers = array();
	$error = array();
	$data['group_user_id'] = $nv_Request->get_int( 'group_user_id', 'get,post', 0 );

	$data = $db->query( 'SELECT group_user_id, title, lev FROM ' . TABLE_ONLINETEST_NAME . '_group_user WHERE group_user_id= ' . intval( $data['group_user_id'] ) . ' ORDER BY sort ASC' )->fetch();

	if( $nv_Request->get_int( 'save', 'post' ) == 1 )
	{
		$data['group_user_id'] = $nv_Request->get_int( 'group_user_id', 'post', 0 );

		$userlist = $nv_Request->get_typed_array( 'userlist', 'post', 'int', array() );

		$userlist = array_unique( array_filter( $userlist ) );

		if( empty( $data['group_user_id'] ) )
		{
			$error['group_user_id'] = $lang_module['group_user_error_group_user_id'];
		}

		if( empty( $data['group_user_id'] ) )
		{
			$error['userlist'] = $lang_module['error_group_user_userlist'];
		}

		try
		{
			$countInsert = 0;
			if( ! empty( $userlist ) )
			{
				foreach( $userlist as $key => $_userid )
				{
					$insert = $db->prepare( 'INSERT IGNORE INTO ' . TABLE_ONLINETEST_NAME . '_group_user_list SET group_user_id = ' . intval( $data['group_user_id'] ) . ', userid=' . intval( $_userid ) );
					$insert->execute();
					if( $insert->rowCount() )
					{
						++$countInsert;
					}
				}
			}
			if( $countInsert > 0 )
			{

				$db->query( 'UPDATE ' . TABLE_ONLINETEST_NAME . '_group_user SET number = ( SELECT COUNT(*) FROM ' . TABLE_ONLINETEST_NAME . '_group_user_list WHERE group_user_id=' . intval( $data['group_user_id'] ) . ') WHERE group_user_id=' . intval( $data['group_user_id'] ) );

				nv_insert_logs( NV_LANG_DATA, $module_name, 'Add User To Group', 'group_user_id: ' . $data['group_user_id'], $user_info['userid'] );

				$nv_Request->set_Session( $module_data . '_success', $lang_module['group_user_insert_user_success'] );

				$nv_Cache->delMod( $module_name );

			}

		}
		catch ( PDOException $e )
		{
			$error['warning'] = $lang_module['group_user_add_user_error_save'];
			// var_dump($e);die('ok');
		}

		if( empty( $error ) )
		{
			$nv_Cache->delMod( $module_name );

			nv_redirect_location( nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op, true ) . '?action=userlist&group_user_id=' . $data['group_user_id'] );

		}

		if( ! empty( $userlist ) )
		{
			$result = $db->query( 'SELECT userid, username FROM ' . NV_USERS_GLOBALTABLE . ' WHERE userid IN ( ' . implode( ',', $userlist ) . ' )' );
			while( $user = $result->fetch() )
			{
				$dataUsers[$user['userid']] = $user;
			}

		}
	}

	$xtpl = new XTemplate( 'ThemeOnlineTestGroupUserAddUser.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'NV_LANG_VARIABLE', NV_LANG_VARIABLE );
	$xtpl->assign( 'NV_LANG_DATA', NV_LANG_DATA );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );

	$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
	$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
	$xtpl->assign( 'MODULE_FILE', $module_file );
	$xtpl->assign( 'MODULE_NAME', $module_name );
	$xtpl->assign( 'OP', $op );
	$xtpl->assign( 'CAPTION', $lang_module['group_user_add_user'] );
	$xtpl->assign( 'DATA', $data );
	$xtpl->assign( 'CANCEL', nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op, true ) );

	$xtpl->assign( 'SHOW', ( $data['title'] == '' ) ? '' : 'showx' );

	if( ! empty( $dataUsers ) )
	{
		foreach( $dataUsers as $_userid => $user )
		{
			$xtpl->assign( 'USER', $user );
			$xtpl->parse( 'main.user' );
		}

	}
	if( ! empty( $error ) )
	{
		foreach( $error as $key => $_error )
		{
			$xtpl->assign( 'error_' . $key, $_error );
			$xtpl->parse( 'main.error_' . $key );
		}

	}

	$xtpl->parse( 'main' );
	$contents = $xtpl->text( 'main' );
	include NV_ROOTDIR . '/includes/header.php';
	echo nv_site_theme( $contents );
	include NV_ROOTDIR . '/includes/footer.php';
}
elseif( ACTION_METHOD == 'delete_user' )
{
	$json = array();

	$group_user_id = $nv_Request->get_int( 'group_user_id', 'post', 0 );

	$userid = $nv_Request->get_int( 'userid', 'post', 0 );

	$token = $nv_Request->get_title( 'token', 'post', '', 1 );

	$listid = $nv_Request->get_string( 'listid', 'post', '' );

	if( $listid != '' and md5( $nv_Request->session_id . $global_config['sitekey'] ) == $token )
	{
		$del_array = array_map( 'intval', explode( ',', $listid ) );
	}
	elseif( $token == md5( $nv_Request->session_id . $global_config['sitekey'] . $userid ) )
	{
		$del_array = array( $userid );
	}

	if( ! empty( $del_array ) )
	{

		$_del_array = array();

		$a = 0;
		foreach( $del_array as $userid )
		{

			if( $db->query( 'DELETE FROM ' . TABLE_ONLINETEST_NAME . '_group_user_list WHERE userid=' . intval( $userid ) . ' AND group_user_id = ' . intval( $group_user_id ) ) )
			{
				$json['id'][$a] = $userid;

				$_del_array[] = $userid;

				++$a;
			}
		}

		$count = sizeof( $_del_array );

		if( $count )
		{
			$db->query( 'UPDATE ' . TABLE_ONLINETEST_NAME . '_group_user SET number = ( SELECT COUNT(*) FROM ' . TABLE_ONLINETEST_NAME . '_group_user_list WHERE group_user_id=' . intval( $group_user_id ) . ') WHERE group_user_id=' . intval( $group_user_id ) );

			nv_insert_logs( NV_LANG_DATA, $module_name, 'log_del_user_from_group', implode( ', ', $_del_array ), $user_info['userid'] );

			$nv_Cache->delMod( $module_name );

			$json['success'] = $lang_module['group_user_delete_user_success'];
		}

	}
	else
	{
		$json['error'] = $lang_module['group_user_error_security'];
	}

	nv_jsonOutput( $json );
}

elseif( ACTION_METHOD == 'add' || ACTION_METHOD == 'edit' )
{

	$data = array(
		'group_user_id' => 0,
		'parent_id' => 0,
		'title' => '',
		'alias' => '',
		'description' => '',
		'weight' => '',
		'sort' => '',
		'lev' => '',
		'numsubcat' => '',
		'subcatid' => '',
		'user_manager_title' => '',
		'user_manager_id' => $user_info['userid'],
		'user_create_id' => $user_info['userid'],
		'status' => 1,
		'date_added' => NV_CURRENTTIME,
		'date_modified' => NV_CURRENTTIME );

	$error = array();

	$data['group_user_id'] = $nv_Request->get_int( 'group_user_id', 'get,post', 0 );
	$data['parent_id'] = $nv_Request->get_int( 'parent_id', 'get,post', 0 );

	if( $data['group_user_id'] > 0 )
	{
		$data = $db->query( 'SELECT *
		FROM ' . TABLE_ONLINETEST_NAME . '_group_user  
		WHERE group_user_id=' . $data['group_user_id'] )->fetch();

		$caption = $lang_module['group_user_edit'];
	}
	else
	{
		$caption = $lang_module['group_user_add'];
	}

	if( $nv_Request->get_int( 'save', 'post' ) == 1 )
	{

		$data['group_user_id'] = $nv_Request->get_int( 'group_user_id', 'post', 0 );
		$data['parentid_old'] = $nv_Request->get_int( 'parentid_old', 'post', 0 );
		$data['parent_id'] = $nv_Request->get_int( 'parent_id', 'post', 0 );
		$data['status'] = 1;
		$data['title'] = nv_substr( $nv_Request->get_title( 'title', 'post', '', '' ), 0, 255 );
		$data['alias'] = nv_substr( $nv_Request->get_title( 'alias', 'post', '', '' ), 0, 255 );
		$data['alias'] = ! empty( $data['alias'] ) ? : strtolower( change_alias( $data['title'] ) );

		$data['description'] = $nv_Request->get_textarea( 'description', 'post', '', 'br', 1 );

		if( empty( $data['title'] ) )
		{
			$error['title'] = $lang_module['group_user_error_title'];
		}

		if( ! empty( $error ) && ! isset( $error['warning'] ) )
		{
			$error['warning'] = $lang_module['group_user_error_warning'];
		}

		$stmt = $db->prepare( 'SELECT COUNT(*) FROM ' . TABLE_ONLINETEST_NAME . '_group_user WHERE group_user_id !=' . $data['group_user_id'] . ' AND alias= :alias' );
		$stmt->bindParam( ':alias', $data['alias'], PDO::PARAM_STR );
		$stmt->execute();
		$check_alias = $stmt->fetchColumn();

		if( $check_alias and $data['parent_id'] > 0 )
		{
			$parentid_alias = $db->query( 'SELECT  FROM ' . TABLE_ONLINETEST_NAME . ' WHERE group_user_id=' . $data['parent_id'] )->fetchColumn();
			$data['alias'] = $parentid_alias . '-' . $data['alias'];
		}

		if( empty( $error ) )
		{
			try
			{
				if( $data['group_user_id'] == 0 )
				{

					$stmt = $db->prepare( 'SELECT max(weight) FROM ' . TABLE_ONLINETEST_NAME . '_group_user WHERE parent_id= ' . intval( $data['parent_id'] ) );
					$stmt->execute();
					$weight = $stmt->fetchColumn();

					$weight = intval( $weight ) + 1;

					$stmt = $db->prepare( 'INSERT INTO ' . TABLE_ONLINETEST_NAME . '_group_user SET 
						parent_id = ' . intval( $data['parent_id'] ) . ', 
						weight = ' . intval( $weight ) . ', 
						status=' . intval( $data['status'] ) . ', 
						user_manager_id=' . intval( $data['user_manager_id'] ) . ',  
						user_create_id=' . intval( $data['user_create_id'] ) . ',  
						date_added=' . intval( $data['date_added'] ) . ',  
						date_modified=' . intval( $data['date_modified'] ) . ',  
						title =:title,
						alias =:alias,
						description =:description' );

					$stmt->bindParam( ':title', $data['title'], PDO::PARAM_STR );
					$stmt->bindParam( ':alias', $data['alias'], PDO::PARAM_STR );
					$stmt->bindParam( ':description', $data['description'], PDO::PARAM_STR );
					$stmt->execute();

					if( $data['group_user_id'] = $db->lastInsertId() )
					{

						fix_group_user_order();

						nv_insert_logs( NV_LANG_DATA, $module_name, 'Add A Group', 'group_user_id: ' . $data['group_user_id'], $user_info['userid'] );

						$nv_Request->set_Session( $module_data . '_success', $lang_module['group_user_insert_success'] );

						$nv_Cache->delMod( $module_name );
					}
					else
					{
						$error['warning'] = $lang_module['group_user_error_save'];

					}
					$stmt->closeCursor();

				}
				else
				{

					$stmt = $db->prepare( 'UPDATE ' . TABLE_ONLINETEST_NAME . '_group_user SET 
						parent_id = ' . intval( $data['parent_id'] ) . ',
						user_manager_id=' . intval( $data['user_manager_id'] ) . ',  
						user_create_id=' . intval( $data['user_create_id'] ) . ', 						
						status=' . intval( $data['status'] ) . ', 
						date_modified=' . intval( $data['date_modified'] ) . ', 
						title =:title,
						alias =:alias,
						description =:description
						WHERE group_user_id=' . $data['group_user_id'] );

					$stmt->bindParam( ':title', $data['title'], PDO::PARAM_STR );
					$stmt->bindParam( ':alias', $data['alias'], PDO::PARAM_STR );
					$stmt->bindParam( ':description', $data['description'], PDO::PARAM_STR );

					if( $stmt->execute() )
					{

						nv_insert_logs( NV_LANG_DATA, $module_name, 'Edit A Group', 'group_user_id: ' . $data['group_user_id'], $user_info['userid'] );

						$nv_Request->set_Session( $module_data . '_success', $lang_module['group_user_update_success'] );

						if( $data['parent_id'] != $data['parentid_old'] )
						{
							$stmt = $db->prepare( 'SELECT max(weight) FROM ' . TABLE_ONLINETEST_NAME . '_group_user WHERE parent_id= :parent_id ' );
							$stmt->bindParam( ':parent_id', $data['parent_id'], PDO::PARAM_INT );
							$stmt->execute();

							$weight = $stmt->fetchColumn();

							$weight = intval( $weight ) + 1;
							$sql = 'UPDATE ' . TABLE_ONLINETEST_NAME . '_group_user SET weight=' . $weight . ' WHERE group_user_id=' . intval( $data['group_user_id'] );
							$db->query( $sql );

							fix_group_user_order();
						}
						$nv_Cache->delMod( $module_name );

					}
					else
					{
						$error['warning'] = $lang_module['group_user_error_save'];

					}

					$stmt->closeCursor();

				}
			}
			catch ( PDOException $e )
			{
				$error['warning'] = $lang_module['group_user_error_save'];
				// var_dump($e);die('ok');
			}
		}
		if( empty( $error ) )
		{
			$nv_Cache->delMod( $module_name );
			nv_redirect_location( nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op, true ) . '?parent_id=' . $data['parent_id'] );

		}

	}

	$xtpl = new XTemplate( 'ThemeOnlineTestGroupUserAdd.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'NV_LANG_VARIABLE', NV_LANG_VARIABLE );
	$xtpl->assign( 'NV_LANG_DATA', NV_LANG_DATA );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );

	$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
	$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
	$xtpl->assign( 'MODULE_FILE', $module_file );
	$xtpl->assign( 'MODULE_NAME', $module_name );
	$xtpl->assign( 'OP', $op );
	$xtpl->assign( 'CAPTION', $caption );
	$xtpl->assign( 'DATA', $data );
	$xtpl->assign( 'CANCEL', nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op, true ) );

	$xtpl->assign( 'SHOW', ( $data['user_manager_title'] == '' ) ? '' : 'showx' );

	if( isset( $error['warning'] ) )
	{
		$xtpl->assign( 'WARNING', $error['warning'] );
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
	echo nv_site_theme( $contents );
	include NV_ROOTDIR . '/includes/footer.php';
}

/*show list group_user*/

$per_page = 100;

$page = $nv_Request->get_int( 'page', 'get', 1 );

$parent_id = $nv_Request->get_int( 'parent_id', 'get', 0 );

$sql = TABLE_ONLINETEST_NAME . '_group_user gu LEFT JOIN ' . NV_USERS_GLOBALTABLE . ' u ON (gu.user_manager_id = u.userid)';

$implode = array();

$implode[] = 'gu.user_create_id = ' . intval( $user_info['userid'] );

$implode[] = 'gu.parent_id = ' . intval( $parent_id );

if( $implode )
{
	$sql .= ' WHERE ' . implode( ' AND ', $implode );
}

$sort = $nv_Request->get_string( 'sort', 'get', '' );

$order = $nv_Request->get_string( 'order', 'get' ) == 'desc' ? 'desc' : 'asc';

$sort_data = array(
	'gu.title',
	'gu.status',
	'gu.sort' );

if( isset( $sort ) && in_array( $sort, $sort_data ) )
{

	$sql .= ' ORDER BY ' . $sort;
}
else
{
	$sql .= ' ORDER BY gu.sort';
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

$base_url = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op, true ) . '?sort=' . $sort . '&amp;order=' . $order . '&amp;per_page=' . $per_page;

$db->sqlreset()->select( 'gu.*, u.username user_manager' )->from( $sql )->limit( $per_page )->offset( ( $page - 1 ) * $per_page );

$result = $db->query( $db->sql() );

$dataContent = array();
while( $rows = $result->fetch() )
{
	$dataContent[] = $rows;
}

$xtpl = new XTemplate( 'ThemeOnlineTestGroupUser.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
$xtpl->assign( 'LANG', $lang_module );
$xtpl->assign( 'NV_LANG_VARIABLE', NV_LANG_VARIABLE );
$xtpl->assign( 'NV_LANG_DATA', NV_LANG_DATA );
$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
$xtpl->assign( 'TEMPLATE', $module_info['template'] );

$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
$xtpl->assign( 'MODULE_FILE', $module_file );
$xtpl->assign( 'MODULE_NAME', $module_name );
$xtpl->assign( 'TOKEN', md5( $nv_Request->session_id . $global_config['sitekey'] ) );
$xtpl->assign( 'ADD_NEW', nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op, true ) . '?action=add&amp;parent_id=' . $parent_id );

$order2 = ( $order == 'asc' ) ? 'desc' : 'asc';
$xtpl->assign( 'URL_TITLE', nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op, true ) . '?sort=gu.title&amp;order=' . $order2 . '&amp;parent_id=' . $parent_id . '&amp;per_page=' . $per_page );
$xtpl->assign( 'URL_WEIGHT', nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op, true ) . '?sort=gu.weight&amp;order=' . $order2 . '&amp;parent_id=' . $parent_id . '&amp;per_page=' . $per_page );
$xtpl->assign( 'URL_STATUS', nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op, true ) . '?sort=gu.status&amp;order=' . $order2 . '&amp;parent_id=' . $parent_id . '&amp;per_page=' . $per_page );

$xtpl->assign( 'TITLE_ORDER', ( $sort == 'gu.title' ) ? 'class="' . $order2 . '"' : '' );
$xtpl->assign( 'WEIGHT_ORDER', ( $sort == 'gu.weight' ) ? 'class="' . $order2 . '"' : '' );
$xtpl->assign( 'STATUS_ORDER', ( $sort == 'gu.status' ) ? 'class="' . $order2 . '"' : '' );

if( $nv_Request->get_string( $module_data . '_success', 'session' ) )
{
	$xtpl->assign( 'SUCCESS', $nv_Request->get_string( $module_data . '_success', 'session' ) );

	$xtpl->parse( 'main.success' );

	$nv_Request->unset_request( $module_data . '_success', 'session' );

}

if( $parent_id > 0 )
{
	$parentid_i = $parent_id;
	$array_cat_title = array();
	$a = 0;

	while( $parentid_i > 0 )
	{
		list( $group_user_id_i, $parentid_i, $title_i ) = $db->query( 'SELECT group_user_id, parent_id, title FROM ' . TABLE_ONLINETEST_NAME . '_group_user 
 		WHERE group_user_id=' . intval( $parentid_i ) )->fetch( 3 );

		$array_cat_title[] = '<a href=\'' . nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op, true ) . '?parent_id=' . $group_user_id_i . '\'><strong>' . $title_i . '</strong></a>';

		++$a;
	}

	for( $i = $a - 1; $i >= 0; $i-- )
	{
		$xtpl->assign( 'CAT_NAV', $array_cat_title[$i] . ( $i > 0 ? ' &raquo; ' : '' ) );
		$xtpl->parse( 'main.catnav.loop' );
	}

	$xtpl->parse( 'main.catnav' );
}

if( ! empty( $dataContent ) )
{
	foreach( $dataContent as $item )
	{

		$item['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $item['group_user_id'] );

		$item['user_link'] = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=users&' . NV_OP_VARIABLE . '=edit', true ) . '?userid=' . $item['group_user_id'];
		$item['user_add'] = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op, true ) . '?action=add_user&token=' . $item['token'] . '&group_user_id=' . $item['group_user_id'];
		$item['user_list'] = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op, true ) . '?action=userlist&group_user_id=' . $item['group_user_id'];
		$item['link'] = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op, true ) . '?parent_id=' . $item['group_user_id'];
		$item['edit'] = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op, true ) . '?action=edit&token=' . $item['token'] . '&group_user_id=' . $item['group_user_id'] . '&parent_id=' . $item['parent_id'];
		$item['status_checked'] = ( $item['status'] ) ? 'checked="checked"' : '';
		$item['numsubcat'] = $item['numsubcat'] > 0 ? ' <span style="color:#FF0101;">(' . $item['numsubcat'] . ')</span>' : '';

		$xtpl->assign( 'LOOP', $item );

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
echo nv_site_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';
