<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

$page_title = $lang_module['category'];

/**
 * fix_category_order()
 *
 * @param integer $parent_id
 * @param integer $order
 * @param integer $lev
 * @return
 */
function fix_category_order( $parent_id = 0, $order = 0, $lev = 0 )
{
	global $db_slave, $module_data;

	$sql = 'SELECT category_id, parent_id FROM ' . TABLE_ONLINETEST_NAME . '_category WHERE parent_id=' . $parent_id . ' ORDER BY weight ASC';
	$result = $db_slave->query( $sql );
	$array_cat_order = array();
	while( $row = $result->fetch() )
	{
		$array_cat_order[] = $row['category_id'];
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
	foreach( $array_cat_order as $category_id_i )
	{
		++$order;
		++$weight;
		$sql = 'UPDATE ' . TABLE_ONLINETEST_NAME . '_category SET weight=' . $weight . ', sort=' . $order . ', lev=' . $lev . ' WHERE category_id=' . intval( $category_id_i );
		$db_slave->query( $sql );
		$order = fix_category_order( $category_id_i, $order, $lev );
	}
	$numsubcat = $weight;
	if( $parent_id > 0 )
	{
		$sql = 'UPDATE ' . TABLE_ONLINETEST_NAME . '_category SET numsubcat=' . $numsubcat;
		if( $numsubcat == 0 )
		{
			$sql .= ",subcatid=''";
		}
		else
		{
			$sql .= ",subcatid='" . implode( ',', $array_cat_order ) . "'";
		}
		$sql .= ' WHERE category_id=' . intval( $parent_id );
		$db_slave->query( $sql );
	}
	return $order;
}

if( ACTION_METHOD == 'delete' )
{
	$json = array();

	$category_id = $nv_Request->get_int( 'category_id', 'post', 0 );

	$token = $nv_Request->get_title( 'token', 'post', '', 1 );

	$listid = $nv_Request->get_string( 'listid', 'post', '' );

	if( $listid != '' and md5( $nv_Request->session_id . $global_config['sitekey'] ) == $token )
	{
		$del_array = array_map( 'intval', explode( ',', $listid ) );
	}
	elseif( $token == md5( $nv_Request->session_id . $global_config['sitekey'] . $category_id ) )
	{
		$del_array = array( $category_id );
	}

	if( ! empty( $del_array ) )
	{

		$_del_array = array();

		$a = 0;
		foreach( $del_array as $category_id )
		{

			if( $rows_total = $db_slave->query( 'SELECT COUNT(*) FROM ' . TABLE_ONLINETEST_NAME . '_question WHERE category_id = ' . ( int )$category_id )->fetchColumn() )
			{
				$json['error'] = sprintf( $lang_module['category_error_question'], $rows_total );
			}
			else
			{
				$db_slave->query( 'DELETE FROM ' . TABLE_ONLINETEST_NAME . '_category WHERE category_id = ' . ( int )$category_id );

				$json['id'][$a] = $category_id;

				$_del_array[] = $category_id;

				++$a;
			}
		}

		$count = sizeof( $_del_array );

		if( $count )
		{
			fix_category_order();

			nv_insert_logs( NV_LANG_DATA, $module_name, 'log_del_category', implode( ', ', $_del_array ), $admin_info['userid'] );

			$nv_Cache->delMod($module_name);

			$json['success'] = $lang_module['category_delete_success'];
		}

	}
	else
	{
		$json['error'] = $lang_module['category_error_security'];
	}

	nv_jsonOutput( $json );
}
elseif( ACTION_METHOD == 'status' )
{
	$json = array();

	$category_id = $nv_Request->get_int( 'category_id', 'post', 0 );
	$new_vid = $nv_Request->get_int( 'new_vid', 'post', 0 );
	$token = $nv_Request->get_title( 'token', 'post', '', 1 );

	if( $token == md5( $nv_Request->session_id . $global_config['sitekey'] . $category_id ) )
	{
		$sql = 'UPDATE ' . TABLE_ONLINETEST_NAME . '_category SET status=' . $new_vid . ' WHERE category_id=' . $category_id;
		if( $db_slave->exec( $sql ) )
		{
			nv_insert_logs( NV_LANG_DATA, $module_name, 'log_change_status_category', 'category_id:' . $category_id, $admin_info['userid'] );
			
			$nv_Cache->delMod($module_name);
			
			$json['success'] = $lang_module['category_status_success'];	
		
		}else{
			$json['error'] = $lang_module['category_error_inhome'];	
		
		}
	} 
	else
	{
		$json['error'] = $lang_module['category_error_security'];
	}

	nv_jsonOutput( $json );
}
elseif( ACTION_METHOD == 'weight' )
{
	$json = array();

	$category_id = $nv_Request->get_int( 'category_id', 'post', 0 );
	$new_vid = $nv_Request->get_int( 'new_vid', 'post', 0 );
	$token = $nv_Request->get_title( 'token', 'post', '', 1 );

	if( $token == md5( $nv_Request->session_id . $global_config['sitekey'] . $category_id ) )
	{
		list( $category_id, $parent_id, $numsubcat ) = $db_slave->query( 'SELECT category_id, parent_id, numsubcat FROM ' . TABLE_ONLINETEST_NAME . '_category WHERE category_id=' . $category_id )->fetch( 3 );
		if( $category_id > 0 )
		{
			
			
			
			$sql = 'SELECT category_id FROM ' . TABLE_ONLINETEST_NAME . '_category WHERE category_id!=' . $category_id . ' AND parent_id=' . $parent_id . ' ORDER BY weight ASC';
			$result = $db_slave->query( $sql );

			$weight = 0;
			while( $row = $result->fetch() )
			{
				++$weight;
				if( $weight == $new_vid ) ++$weight;
				$sql = 'UPDATE ' . TABLE_ONLINETEST_NAME . '_category SET weight=' . $weight . ' WHERE category_id=' . intval( $row['category_id'] );
				$db_slave->query( $sql );
			}

			$sql = 'UPDATE ' . TABLE_ONLINETEST_NAME . '_category SET weight=' . $new_vid . ' WHERE category_id=' . $category_id;
			if( $db_slave->exec( $sql ) )
			{
				fix_category_order();
				
				nv_insert_logs( NV_LANG_DATA, $module_name, 'log_change_weight_category', 'category_id:' . $category_id, $admin_info['userid'] );
				
				$nv_Cache->delMod($module_name);
				
				$json['success'] = $lang_module['category_weight_success'];	
				$json['link'] =  NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op;	
			
			}else{
				$json['error'] = $lang_module['category_error_weight'];	
			
			}
		}
	} 
	else
	{
		$json['error'] = $lang_module['category_error_security'];
	}

	nv_jsonOutput( $json );
}
elseif( ACTION_METHOD == 'add' || ACTION_METHOD == 'edit' )
{
 
	$data = array(
		'category_id' => 0,
		'parent_id' => 0,
		'title' => '',
		'alias' => '',
		'description' => '',
		'meta_title' => '',
		'meta_description' => '',
		'meta_keyword' => '',
		'weight' => '',
		'sort' => '',
		'lev' => '',
		'numsubcat' => '',
		'subcatid' => '',
		'status' => 1,
		'date_added' => NV_CURRENTTIME,
		'date_modified' => NV_CURRENTTIME 
	);
	 
	$error = array();
 
	$data['category_id'] = $nv_Request->get_int( 'category_id', 'get,post', 0 );
	$data['parent_id'] = $nv_Request->get_int( 'parent_id', 'get,post', 0 );
	if( $data['category_id'] > 0 )
	{
		$data = $db_slave->query( 'SELECT *
		FROM ' . TABLE_ONLINETEST_NAME . '_category  
		WHERE category_id=' . $data['category_id'] )->fetch();
		
		$caption = $lang_module['category_edit'];
	}
	else
	{
		$caption = $lang_module['category_add'];
	}

	if( $nv_Request->get_int( 'save', 'post' ) == 1 )
	{

		$data['category_id'] = $nv_Request->get_int( 'category_id', 'post', 0 );
		$data['parentid_old'] = $nv_Request->get_int( 'parentid_old', 'post', 0 );
		$data['parent_id'] = $nv_Request->get_int( 'parent_id', 'post', 0 );
		$data['status'] = $nv_Request->get_int( 'status', 'post', 0 );
		$data['title'] = nv_substr( $nv_Request->get_title( 'title', 'post', '', '' ), 0, 255 );
		$data['alias'] = nv_substr( $nv_Request->get_title( 'alias', 'post', '', '' ), 0, 255 );
		$data['alias'] = !empty( $data['alias'] ) ? strtolower( change_alias( $data['alias'] ) ) : strtolower( change_alias( $data['title'] ) );
		
		$data['description'] = $nv_Request->get_textarea( 'description', 'post', '', 'br', 1 );
		$data['meta_title'] = nv_substr( $nv_Request->get_title( 'meta_title', 'post', '', '' ), 0, 255 );
		$data['meta_description'] = nv_substr( $nv_Request->get_title( 'meta_description', 'post', '', '' ), 0, 255 );
		$data['meta_keyword'] = nv_substr( $nv_Request->get_title( 'meta_keyword', 'post', '', '' ), 0, 255 );
				
		
		if( empty( $data['title'] ) )
		{
			$error['title'] = $lang_module['category_error_title'];	
		}
 
		if( ! empty( $error ) && ! isset( $error['warning'] ) )
		{
			$error['warning'] = $lang_module['category_error_warning'];
		}
 
		$stmt = $db_slave->prepare( 'SELECT COUNT(*) FROM ' . TABLE_ONLINETEST_NAME . '_category WHERE category_id !=' . $data['category_id'] . ' AND alias= :alias' );
		$stmt->bindParam( ':alias', $data['alias'], PDO::PARAM_STR );
		$stmt->execute();
		$check_alias = $stmt->fetchColumn();

		if( $check_alias and $data['parent_id'] > 0 )
		{
			$parentid_alias = $db_slave->query( 'SELECT  FROM ' . TABLE_ONLINETEST_NAME . ' WHERE category_id=' . $data['parent_id'] )->fetchColumn();
			$data['alias'] = $parentid_alias . '-' . $data['alias'];
		}
		
		if( empty( $error ) )
		{
			if( $data['category_id'] == 0 )
			{

				$stmt = $db_slave->prepare( 'SELECT max(weight) FROM ' . TABLE_ONLINETEST_NAME . '_category WHERE parent_id= ' . intval( $data['parent_id'] ) );
				$stmt->execute();
				$weight = $stmt->fetchColumn();

				$weight = intval( $weight ) + 1;

				$stmt = $db->prepare( 'INSERT INTO ' . TABLE_ONLINETEST_NAME . '_category SET 
					parent_id = ' . intval( $data['parent_id'] ) . ', 
					weight = ' . intval( $weight ) . ', 
					status=' . intval( $data['status'] ) . ', 
					date_added=' . intval( $data['date_added'] ) . ',  
					date_modified=' . intval( $data['date_modified'] ) . ', 
					sort = 0,
					lev = 0,
					numsubcat=0, 
					title =:title,
					alias =:alias,
					description =:description,
					meta_title =:meta_title,
					meta_description =:meta_description,
					meta_keyword =:meta_keyword,
					subcatid=:subcatid' );
				
				$stmt->bindParam( ':title', $data['title'], PDO::PARAM_STR );
				$stmt->bindParam( ':alias', $data['alias'], PDO::PARAM_STR );
				$stmt->bindParam( ':description', $data['description'], PDO::PARAM_STR );
				$stmt->bindParam( ':meta_title', $data['meta_title'], PDO::PARAM_STR );
				$stmt->bindParam( ':meta_description', $data['meta_description'], PDO::PARAM_STR );
				$stmt->bindParam( ':meta_keyword', $data['meta_keyword'], PDO::PARAM_STR );
				$stmt->bindParam( ':subcatid', $data['subcatid'], PDO::PARAM_STR );
				$stmt->execute();

				if( $data['category_id'] = $db_slave->lastInsertId() )
				{
 
					fix_category_order();
					
					nv_insert_logs( NV_LANG_DATA, $module_name, 'Add A Category', 'category_id: ' . $data['category_id'], $admin_info['userid'] );	 
					
					$nv_Request->set_Session( $module_data . '_success', $lang_module['category_insert_success'] );
						
						
					$nv_Cache->delMod($module_name);	
				}
				else
				{
					$error['warning'] = $lang_module['category_error_save'];

				}
				$stmt->closeCursor();

			}
			else
			{
				try
				{
					
					$stmt = $db->prepare( 'UPDATE ' . TABLE_ONLINETEST_NAME . '_category SET 
						parent_id = ' . intval( $data['parent_id'] ) . ', 
						status=' . intval( $data['status'] ) . ', 
						date_modified=' . intval( $data['date_modified'] ) . ', 
						title =:title,
						alias =:alias,
						description =:description,
						meta_title =:meta_title,
						meta_description =:meta_description,
						meta_keyword =:meta_keyword
						WHERE category_id=' . $data['category_id'] );
				
					$stmt->bindParam( ':title', $data['title'], PDO::PARAM_STR );
					$stmt->bindParam( ':alias', $data['alias'], PDO::PARAM_STR );
					$stmt->bindParam( ':description', $data['description'], PDO::PARAM_STR );
					$stmt->bindParam( ':meta_title', $data['meta_title'], PDO::PARAM_STR );
					$stmt->bindParam( ':meta_description', $data['meta_description'], PDO::PARAM_STR );
					$stmt->bindParam( ':meta_keyword', $data['meta_keyword'], PDO::PARAM_STR );
					
					if( $stmt->execute() )
					{

						nv_insert_logs( NV_LANG_DATA, $module_name, 'Edit A Category', 'category_id: ' . $data['category_id'], $admin_info['userid'] );
						
						$nv_Request->set_Session( $module_data . '_success', $lang_module['category_update_success'] );
						
						if( $data['parent_id'] != $data['parentid_old'] )
						{
							$stmt = $db->prepare( 'SELECT max(weight) FROM ' . TABLE_ONLINETEST_NAME . '_category WHERE parent_id= :parent_id ' );
							$stmt->bindParam( ':parent_id', $data['parent_id'], PDO::PARAM_INT );
							$stmt->execute();
							
							$weight = $stmt->fetchColumn();
							
							$weight = intval( $weight ) + 1;
							$sql = 'UPDATE ' . TABLE_ONLINETEST_NAME . '_category SET weight=' . $weight . ' WHERE category_id=' . intval( $data['category_id'] );
							$db->query( $sql );
							
							fix_category_order();
						}
						$nv_Cache->delMod($module_name);
					
					}
					else
					{
						$error['warning'] = $lang_module['category_error_save'];

					}

					$stmt->closeCursor();

				}
				catch ( PDOException $e )
				{ 
					$error['warning'] = $lang_module['category_error_save'];
					var_dump($e);die('ok');
				}

			}

		}
		if( empty( $error ) )
		{
			$nv_Cache->delMod($module_name);
			Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=category&parent_id=' . $data['parent_id'] );
			die();
		}

	}
 
	$xtpl = new XTemplate( 'category_add.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
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
		$xtpl->assign( 'WARNING', $error['warning'] );
		$xtpl->parse( 'main.error_warning' );
	}

	if( isset( $error['title'] ) )
	{
		$xtpl->assign( 'error_title', $error['title'] );
		$xtpl->parse( 'main.error_title' );
	}
 
	$sql = 'SELECT category_id, title, lev FROM ' . TABLE_ONLINETEST_NAME . '_category WHERE category_id !=' . $data['category_id'] . ' ORDER BY sort ASC';

	$result = $db_slave->query( $sql );
	
	$array_cat_list = array();
 
	while( list( $category_id_i, $title_i, $lev_i ) = $result->fetch( 3 ) )
	{
		$xtitle_i = '';
		if( $lev_i > 0 )
		{
			$xtitle_i .= '&nbsp;';
			for( $i = 1; $i <= $lev_i; $i++ )
			{
				$xtitle_i .= '---';
			}
		}
		$xtitle_i .= $title_i;
		$array_cat_list[] = array( $category_id_i, $xtitle_i );
	}
 
	foreach( $array_cat_list as $rows_i )
	{
		$xtpl->assign( 'CATEGORY', array( 'key'=> $rows_i[0], 'name'=> $rows_i[1], 'selected'=> ( $rows_i[0] == $data['parent_id'] ) ? 'selected="selected"' : '' ) );
		$xtpl->parse( 'main.category' );
	}
 
	
	
	if( $onlineTestStatus )
	{
		foreach( $onlineTestStatus as $key => $item )
		{		
			$xtpl->assign( 'STATUS', array('key'=> $key, 'name'=> $item, 'selected'=> ( $key == $data['status'] && is_numeric( $data['status'] ) ) ? 'selected="selected"' : '' ) );
			$xtpl->parse( 'main.status' );
		}
	}
		
	if( empty( $data['alias'] ) )
	{
		$xtpl->parse( 'main.getalias' );
	}
	$xtpl->parse( 'main' );
	$contents = $xtpl->text( 'main' );
	include NV_ROOTDIR . '/includes/header.php';
	echo nv_admin_theme( $contents );
	include NV_ROOTDIR . '/includes/footer.php';
}

/*show list category*/

$per_page = 50;

$page = $nv_Request->get_int( 'page', 'get', 1 );

$parent_id = $nv_Request->get_int( 'parent_id', 'get', 0 );

$sql = TABLE_ONLINETEST_NAME . '_category WHERE  parent_id = ' . $parent_id;

$sort = $nv_Request->get_string( 'sort', 'get', '' );

$order = $nv_Request->get_string( 'order', 'get' ) == 'desc' ? 'desc' : 'asc';

$sort_data = array( 'title', 'status', 'sort' );

if( isset( $sort ) && in_array( $sort, $sort_data ) )
{

	$sql .= " ORDER BY " . $sort;
}
else
{
	$sql .= " ORDER BY sort";
}

if( isset( $order ) && ( $order == 'desc' ) )
{
	$sql .= ' DESC';
}
else
{
	$sql .= ' ASC';
}

$num_items = $db_slave->query( 'SELECT COUNT(*) FROM ' . $sql )->fetchColumn();

$base_url = NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=category&amp;sort=' . $sort . '&amp;order=' . $order . '&amp;per_page=' . $per_page;

$db_slave->sqlreset()->select( '*' )->from( $sql )->limit( $per_page )->offset( ( $page - 1 ) * $per_page );

$result = $db_slave->query( $db_slave->sql() );

$dataContent = array();
while( $rows = $result->fetch() )
{
	$dataContent[] = $rows;
}

$xtpl = new XTemplate( 'category.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
$xtpl->assign( 'LANG', $lang_module );
$xtpl->assign( 'NV_LANG_VARIABLE', NV_LANG_VARIABLE );
$xtpl->assign( 'NV_LANG_DATA', NV_LANG_DATA );
$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
$xtpl->assign( 'THEME', $global_config['site_theme'] );
$xtpl->assign( 'NV_BASE_ADMINURL', NV_BASE_ADMINURL );
$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
$xtpl->assign( 'MODULE_FILE', $module_file );
$xtpl->assign( 'MODULE_NAME', $module_name );
$xtpl->assign( 'TOKEN', md5( $nv_Request->session_id . $global_config['sitekey'] ) );
$xtpl->assign( 'ADD_NEW', NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=category&action=add&amp;parent_id=" . $parent_id );

$order2 = ( $order == 'asc' ) ? 'desc' : 'asc';
$xtpl->assign( 'URL_TITLE', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=title&amp;order=' . $order2 . '&amp;parent_id=' . $parent_id . '&amp;per_page=' . $per_page );
$xtpl->assign( 'URL_WEIGHT', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=weight&amp;order=' . $order2 . '&amp;parent_id=' . $parent_id . '&amp;per_page=' . $per_page );
$xtpl->assign( 'URL_STATUS', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=status&amp;order=' . $order2 . '&amp;parent_id=' . $parent_id . '&amp;per_page=' . $per_page );

$xtpl->assign( 'TITLE_ORDER', ( $sort == 'title' ) ? 'class="' . $order2 . '"' : '' );
$xtpl->assign( 'WEIGHT_ORDER', ( $sort == 'weight' ) ? 'class="' . $order2 . '"' : '' );
$xtpl->assign( 'STATUS_ORDER', ( $sort == 'status' ) ? 'class="' . $order2 . '"' : '' );

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
		list( $category_id_i, $parentid_i, $title_i ) = $db_slave->query( 'SELECT category_id, parent_id, title FROM ' . TABLE_ONLINETEST_NAME . '_category 
 		WHERE category_id=' . intval( $parentid_i ) )->fetch( 3 );

		$array_cat_title[] = "<a href=\"" . NV_BASE_ADMINURL . "index.php?" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=category&amp;parent_id=" . $category_id_i . "\"><strong>" . $title_i . "</strong></a>";

		++$a;
	}

	for( $i = $a - 1; $i >= 0; $i-- )
	{
		$xtpl->assign( 'CAT_NAV', $array_cat_title[$i] . ( $i > 0 ? " &raquo; " : "" ) );
		$xtpl->parse( 'main.catnav.loop' );
	}

	$xtpl->parse( 'main.catnav' );
}

if( ! empty( $dataContent ) )
{
	foreach( $dataContent as $item )
	{
 
		$item['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $item['category_id'] );

		$item['link'] = NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=category&parent_id=" . $item['category_id'];
		$item['edit'] = NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=category&action=edit&token=" . $item['token'] . "&category_id=" . $item['category_id'] . '&parent_id=' . $item['parent_id'];
		$item['status_checked'] = ( $item['status'] ) ? 'checked="checked"': '';
		$item['numsubcat'] = $item['numsubcat'] > 0 ? ' <span style="color:#FF0101;">(' . $item['numsubcat'] . ')</span>' : '';

		$xtpl->assign( 'LOOP', $item );

		for( $i = 1; $i <= $num_items; ++$i )
		{
			$xtpl->assign( 'WEIGHT', array( 'w' => $i, 'selected' => ( $i == $item['weight'] ) ? ' selected="selected"' : '' ) );

			$xtpl->parse( 'main.loop.weight' );
		}
 
		
 
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
echo nv_admin_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';
