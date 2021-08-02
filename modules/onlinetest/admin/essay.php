<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

if( ACTION_METHOD == 'delete' )
{
	$json = array();

	$essay_id = $nv_Request->get_int( 'essay_id', 'post', 0 );

	$token = $nv_Request->get_title( 'token', 'post', '', 1 );

	$listid = $nv_Request->get_string( 'listid', 'post', '' );

	if( $listid != '' and md5( $nv_Request->session_id . $global_config['sitekey'] ) == $token )
	{
		$del_array = array_map( 'intval', explode( ',', $listid ) );
	}
	elseif( $token == md5( $nv_Request->session_id . $global_config['sitekey'] . $essay_id ) )
	{
		$del_array = array( $essay_id );
	}

	if( ! empty( $del_array ) )
	{

		$_del_array = array();

		$a = 0;
		foreach( $del_array as $essay_id )
		{
			$result = $db->query( 'DELETE FROM ' . TABLE_ONLINETEST_NAME . '_essay WHERE essay_id = ' . ( int )$essay_id );
			if( $result->rowCount() )
			{
			
				$json['id'][$a] = $essay_id;
				$_del_array[] = $essay_id;
				++$a;
			}
		}
		$count = sizeof( $_del_array );

		if( $count )
		{
			nv_insert_logs( NV_LANG_DATA, $module_name, 'log_del_essay', implode( ', ', $_del_array ), $admin_info['userid'] );

			$nv_Cache->delMod( $module_name );

			$json['success'] = $lang_module['essay_delete_success'];
		}

	}
	else
	{
		$json['error'] = $lang_module['essay_error_security'];
	}

	nv_jsonOutput( $json );
}
elseif( ACTION_METHOD == 'status' )
{
	$json = array();

	$essay_id = $nv_Request->get_int( 'essay_id', 'post', 0 );
	$new_vid = $nv_Request->get_int( 'new_vid', 'post', 0 );
	$token = $nv_Request->get_title( 'token', 'post', '', 1 );

	if( $token == md5( $nv_Request->session_id . $global_config['sitekey'] . $essay_id ) )
	{
		
		$sql = 'UPDATE ' . TABLE_ONLINETEST_NAME . '_essay SET status=' . $new_vid . ' WHERE essay_id=' . $essay_id;
		if( $db->exec( $sql ) )
		{

			nv_insert_logs( NV_LANG_DATA, $module_name, 'log_change_status_essay', 'essay_id:' . $essay_id, $admin_info['userid'] );

			$nv_Cache->delMod($module_name);

			$json['success'] = $lang_module['essay_status_success'];

		}
		else
		{
			$json['error'] = $lang_module['essay_error_status'];

		}
		
	}
	else
	{
		$json['error'] = $lang_module['essay_error_security'];
	}

	nv_jsonOutput( $json );
}
elseif( ACTION_METHOD == 'add' || ACTION_METHOD == 'edit' )
{
	if( ! nv_function_exists( 'getEditor' ) and file_exists( NV_ROOTDIR . '/' . NV_EDITORSDIR . '/ckeditor/ckeditor.js' ) )
	{
 		$my_head .= '<script type="text/javascript" src="' . NV_BASE_SITEURL . NV_EDITORSDIR . '/ckeditor/ckeditor.js"></script>';

		function getEditor( $textareaname, $val = '', $width = '100%', $height = '450px', $path, $currentpath  )
		{
			global $module_data, $admin_info, $module_upload ;
			$return = '<textarea style="width: ' . $width . '; height:' . $height . ';" id="' . $module_data . '_' . $textareaname . '" name="' . $textareaname . '">' . $val . '</textarea>';
			$return .= "<script type=\"text/javascript\">
				CKEDITOR.replace( '" . $module_data . "_" . $textareaname . "',{ 
				width: '" . $width . "',
				height: '" . $height . "',
				toolbarGroups:[
					{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
					{ name: 'forms', groups: [ 'forms' ] },
					{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
					{ name: 'links', groups: [ 'links' ] },
					{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'paragraph' ] },
					{ name: 'insert', groups: [ 'insert' ] },
					{ name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
					{ name: 'styles', groups: [ 'styles' ] },
					{ name: 'colors', groups: [ 'colors' ] },
					{ name: 'tools', groups: [ 'tools' ] },
					{ name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
					{ name: 'others', groups: [ 'others' ] },
					{ name: 'about', groups: [ 'about' ] }
				],
				removePlugins: 'autosave,gg,switchbar',
				removeButtons: 'Templates,Googledocs,NewPage,Preview,Print,Save,Find,Replace,SelectAll,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Blockquote,Flash,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe,Maximize,About,Anchor,BidiRtl,CreateDiv,Indent,BulletedList,NumberedList,Outdent,ShowBlocks,Youtube,Video',"; 
			if( defined( 'NV_IS_ADMIN' ) )
			{
				if( empty( $path ) and empty( $currentpath ) )
				{
					$path = NV_UPLOADS_DIR;
					$currentpath = NV_UPLOADS_DIR;

					if( ! empty( $module_upload ) and file_exists( NV_UPLOADS_REAL_DIR . '/' . $module_upload . '/' . date( "Y_m" ) ) )
					{
						$currentpath = NV_UPLOADS_DIR . '/' . $module_upload . '/' . date( "Y_m" );
						$path = NV_UPLOADS_DIR . '/' . $module_upload;
					}
					elseif( ! empty( $module_upload ) and file_exists( NV_UPLOADS_REAL_DIR . '/' . $module_upload ) )
					{
						$currentpath = NV_UPLOADS_DIR . '/' . $module_upload;
					}
				}

				if( ! empty( $admin_info['allow_files_type'] ) )
				{
					$return .= "filebrowserUploadUrl: '" . NV_BASE_SITEURL . NV_ADMINDIR . "/index.php?" . NV_NAME_VARIABLE . "=upload&" . NV_OP_VARIABLE . "=upload&editor=ckeditor&path=" . $currentpath . "',";
				}

				if( in_array( 'images', $admin_info['allow_files_type'] ) )
				{
					$return .= "filebrowserImageUploadUrl: '" . NV_BASE_SITEURL . NV_ADMINDIR . "/index.php?" . NV_NAME_VARIABLE . "=upload&" . NV_OP_VARIABLE . "=upload&editor=ckeditor&path=" . $currentpath . "&type=image',";
				}

				if( in_array( 'flash', $admin_info['allow_files_type'] ) )
				{
					$return .= "filebrowserFlashUploadUrl: '" . NV_BASE_SITEURL . NV_ADMINDIR . "/index.php?" . NV_NAME_VARIABLE . "=upload&" . NV_OP_VARIABLE . "=upload&editor=ckeditor&path=" . $currentpath . "&type=flash',";
				}
				$return .= "filebrowserBrowseUrl: '" . NV_BASE_SITEURL . NV_ADMINDIR . "/index.php?" . NV_NAME_VARIABLE . "=upload&popup=1&path=" . $path . "&currentpath=" . $currentpath . "',";
				$return .= "filebrowserImageBrowseUrl: '" . NV_BASE_SITEURL . NV_ADMINDIR . "/index.php?" . NV_NAME_VARIABLE . "=upload&popup=1&type=image&path=" . $path . "&currentpath=" . $currentpath . "',";
				$return .= "filebrowserFlashBrowseUrl: '" . NV_BASE_SITEURL . NV_ADMINDIR . "/index.php?" . NV_NAME_VARIABLE . "=upload&popup=1&type=flash&path=" . $path . "&currentpath=" . $currentpath . "'";
 
			}		
			$return .= "	});";
			$return .= "</script>";
			return $return;

		}
	}

	$data = array(
		'essay_id' => 0,
		'category_id' => 0,
		'user_id' => $admin_info['userid'],
		'user_name' => $admin_info['username'],
		'question' => '',
		'date_added' => 0,
		'date_modified' => 0,
		'status' => 1  
	);
	$dataContent = array();
	$error = array();

	$data['essay_id'] = $nv_Request->get_int( 'essay_id', 'get,post', 0 );
	
	if( $data['essay_id'] > 0 )
	{
		$data = $db->query( 'SELECT *
		FROM ' . TABLE_ONLINETEST_NAME . '_essay  
		WHERE essay_id=' . $data['essay_id'] )->fetch();
 
		$caption = $lang_module['essay_edit'];
	}
	else
	{
		$caption = $lang_module['essay_add'];
	}

	if( $nv_Request->get_int( 'save', 'post' ) == 1 )
	{
		 
		$data['category_id'] = $nv_Request->get_int( 'category_id', 'post', 0 );
 		$data['status'] = $nv_Request->get_int( 'status', 'post', 0 );
 		$data['question'] = $nv_Request->get_editor('question', '', NV_ALLOWED_HTML_TAGS);
 
 
		if( empty( $data['category_id'] ) ) $error['category'] = $lang_module['essay_error_category_id'];
		if ( trim( strip_tags( $data['question'] ) ) == '' and ! preg_match("/\<img[^\>]*alt=\"([^\"]+)\"[^\>]*\>/is", $data['question'] ) ) 
		{
			$error['question'] =  $lang_module['essay_error_question'];
	    }
		if( ! empty( $error ) && ! isset( $error['warning'] ) )
		{
			$error['warning'] = $lang_module['essay_error_warning'];
		}

		if( empty( $error ) )
		{

			if( $data['essay_id'] == 0 )
			{
 
				try
				{
					$stmt = $db->prepare( 'INSERT INTO ' . TABLE_ONLINETEST_NAME . '_essay SET 
						category_id=' . intval( $data['category_id'] ) . ',
						user_id=' . intval( $data['user_id'] ) . ',
						user_name=:user_name, 
						question=:question, 
						status=' . intval( $data['status'] ) . ',
						date_added=' . intval( NV_CURRENTTIME ) );


					$stmt->bindParam( ':user_name', $data['user_name'], PDO::PARAM_STR );
					$stmt->bindParam( ':question', $data['question'], PDO::PARAM_STR, strlen(  $data['question'] ) );
					$stmt->execute();

					if( $data['essay_id'] = $db->lastInsertId() )
					{
						nv_insert_logs( NV_LANG_DATA, $module_name, 'Add Question', 'essay_id: ' . $data['essay_id'], $admin_info['userid'] );

						$nv_Request->set_Session( $module_data . '_success', $lang_module['essay_add_success'] );

					}
					else
					{
						$error['warning'] = $lang_module['essay_error_save'];

					}
					$stmt->closeCursor();

				}
				catch ( PDOException $e )
				{
					$error['warning'] = $lang_module['essay_error_save'];
					//var_dump( $e ); die();
				}

			}
			else
			{
				try
				{

					$stmt = $db->prepare( 'UPDATE ' . TABLE_ONLINETEST_NAME . '_essay SET 
							category_id=' . intval( $data['category_id'] ) . ',
							user_id=' . intval( $data['user_id'] ) . ',
							user_name=:user_name, 
							question=:question, 
							status=' . intval( $data['status'] ) . ',
							date_modified=' . intval( NV_CURRENTTIME ) . '
							WHERE essay_id=' . $data['essay_id'] );
					
					$stmt->bindParam( ':user_name', $data['user_name'], PDO::PARAM_STR );
					$stmt->bindParam( ':question', $data['question'], PDO::PARAM_STR, strlen(  $data['question'] ) );
					$stmt->execute();
					
					if( $stmt->rowCount() )
					{

						
						nv_insert_logs( NV_LANG_DATA, $module_name, 'Edit Question: ' . $data['essay_id'], $admin_info['userid'] );

						$nv_Request->set_Session( $module_data . '_success', $lang_module['essay_edit_success'] );

					}
					else
					{
						$error['warning'] = $lang_module['essay_error_save'];

					}

					$stmt->closeCursor();

				}
				catch ( PDOException $e )
				{
					$error['warning'] = $lang_module['essay_error_save'];
					//var_dump($e);die();
				}

			}

		}

		if( empty( $error ) )
		{

			Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
			die();
		}

	}
	$_question = $data['question'];
	$data['question'] = htmlspecialchars( nv_editor_br2nl( $data['question'] ) );

	if( nv_function_exists( 'getEditor' ) && isHtml( $_question ) )
	{
		$data['question'] = getEditor( 'question', $data['question'], '100%', '450px', '', '' );
	}
	else
	{
		$data['question'] = '<textarea style="width: 100%;height:400px" name="question" id="question" cols="20" rows="20" class="form-control">' . $data['question'] . '</textarea>';
	}
 
	$xtpl = new XTemplate( 'essay_add.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
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
	$xtpl->assign( 'MODULE_DATA', $module_data );
	$xtpl->assign( 'OP', $op );
	$xtpl->assign( 'CAPTION', $caption );
	$xtpl->assign( 'DATA', $data );
	$xtpl->assign( 'CANCEL', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
	$xtpl->assign( 'TOKEN', md5( $client_info['session_id'] . $global_config['sitekey'] ) );
 
	 
	if( $onlineTestCategory )
	{
		foreach( $onlineTestCategory as $key => $item )
		{
			$xtitle_i = '';
			if( $item['lev'] > 0 )
			{
				$xtitle_i .= '&nbsp;';
				for( $i = 1; $i <= $item['lev']; $i++ )
				{
					$xtitle_i .= '&nbsp;&nbsp;';
				}
			}
			
			$xtitle_i .= $item['title'];
			
			$xtpl->assign( 'CATEGORY', array(
				'key' => $key,
				'name' => $xtitle_i,
				'selected' => ( $key == $data['category_id'] ) ? 'selected="selected"' : '' ) );
			$xtpl->parse( 'main.category' );
		}
	}

 
	if( $onlineTestStatus )
	{
		foreach( $onlineTestStatus as $key => $item )
		{
			$xtpl->assign( 'STATUS', array(
				'key' => $key,
				'name' => $item,
				'selected' => ( $key == $data['status'] ) ? 'selected="selected"' : '' ) );
			$xtpl->parse( 'main.status' );
		}
	}	
	
 
	if( isset( $error['warning'] ) )
	{
		$xtpl->assign( 'WARNING', $error['warning'] );
		$xtpl->parse( 'main.error_warning' );
		unset($error['warning']);
	}
	if( $error )
	{
		foreach( $error as $key => $_error )
		{ 
			$xtpl->assign( 'error_' . $key, $_error );
			$xtpl->parse( 'main.error_' . $key );
		}
	}
	
	MakeDir( $module_upload . '/' . date( 'Y_m' ) );
	$path = NV_UPLOADS_DIR . '/' . $module_upload;
	$currentpath = NV_UPLOADS_DIR . '/' . $module_upload . '/' . date( 'Y_m' );
	
	$xtpl->assign( 'PATH', $path );
	$xtpl->assign( 'CURRENTPATH', $currentpath );
	
	if( defined( 'NV_IS_ADMIN' ) )
	{		
		if( empty( $path ) and empty( $currentpath ) )
		{
			$path = NV_UPLOADS_DIR;
			$currentpath = NV_UPLOADS_DIR;

			if( ! empty( $module_upload ) and file_exists( NV_UPLOADS_REAL_DIR . '/' . $module_upload . '/' . date( 'Y_m' ) ) )
			{
				$currentpath = NV_UPLOADS_DIR . '/' . $module_upload . '/' . date( 'Y_m' );
				$path = NV_UPLOADS_DIR . '/' . $module_upload;
			}
			elseif( ! empty( $module_upload ) and file_exists( NV_UPLOADS_REAL_DIR . '/' . $module_upload ) )
			{
				$currentpath = NV_UPLOADS_DIR . '/' . $module_upload;
			}
		}

		if( ! empty( $admin_info['allow_files_type'] ) )
		{
			$xtpl->assign( 'filebrowserUploadUrl', "filebrowserUploadUrl: '" . NV_BASE_SITEURL . NV_ADMINDIR . "/index.php?" . NV_NAME_VARIABLE . "=upload&" . NV_OP_VARIABLE . "=upload&editor=ckeditor&path=" . $currentpath . "'," );
			$xtpl->parse( 'main.filebrowserUploadUrl' );
		}

		if( in_array( 'images', $admin_info['allow_files_type'] ) )
		{
 
			$xtpl->assign( 'filebrowserImageUploadUrl', "filebrowserImageUploadUrl: '" . NV_BASE_SITEURL . NV_ADMINDIR . "/index.php?" . NV_NAME_VARIABLE . "=upload&" . NV_OP_VARIABLE . "=upload&editor=ckeditor&path=" . $currentpath . "&type=image'," );
			$xtpl->parse( 'main.filebrowserImageUploadUrl' );
		
		}

		if( in_array( 'flash', $admin_info['allow_files_type'] ) )
		{
			$xtpl->assign( 'filebrowserFlashUploadUrl', "filebrowserFlashUploadUrl: '" . NV_BASE_SITEURL . NV_ADMINDIR . "/index.php?" . NV_NAME_VARIABLE . "=upload&" . NV_OP_VARIABLE . "=upload&editor=ckeditor&path=" . $currentpath . "&type=flash'," );
			$xtpl->parse( 'main.filebrowserFlashUploadUrl' );
		}
		
		$xtpl->assign( 'filebrowserBrowseUrl', "filebrowserBrowseUrl: '" . NV_BASE_SITEURL . NV_ADMINDIR . "/index.php?" . NV_NAME_VARIABLE . "=upload&popup=1&path=" . $path . "&currentpath=" . $currentpath . "'," );
		$xtpl->assign( 'filebrowserImageBrowseUrl', "filebrowserImageBrowseUrl: '" . NV_BASE_SITEURL . NV_ADMINDIR . "/index.php?" . NV_NAME_VARIABLE . "=upload&popup=1&type=image&path=" . $path . "&currentpath=" . $currentpath . "'," );
		$xtpl->assign( 'filebrowserFlashBrowseUrl', "filebrowserFlashBrowseUrl: '" . NV_BASE_SITEURL . NV_ADMINDIR . "/index.php?" . NV_NAME_VARIABLE . "=upload&popup=1&type=flash&path=" . $path . "&currentpath=" . $currentpath . "'," );

	}
	
	
	$xtpl->parse( 'main' );
	$contents = $xtpl->text( 'main' );

	include NV_ROOTDIR . '/includes/header.php';
	echo nv_admin_theme( $contents );
	include NV_ROOTDIR . '/includes/footer.php';
}

/*show list essay*/

$page = $nv_Request->get_int( 'page', 'get', 1 );

$data['question'] = $nv_Request->get_string( 'question', 'get', '' );
$data['category_id'] = $nv_Request->get_int( 'category_id', 'get', 0 );
$data['user_name'] = $nv_Request->get_string( 'user_name', 'get' );
$data['status'] = $nv_Request->get_string( 'status', 'get', '' );
$data['date_from'] = $nv_Request->get_title( 'date_from', 'get', '' );
$data['date_to'] = $nv_Request->get_title( 'date_to', 'get', '' );

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

if( $data['question'] )
{
	$implode[] = 'question LIKE \'%' . $db_slave->dblikeescape( $data['question'] ) . '%\'';
}
if( $data['user_name'] )
{
	$implode[] = 'user_name LIKE \'%' . $db_slave->dblikeescape( $data['user_name'] ) . '%\'';
}
if( $data['category_id'] )
{
	$implode[] = 'category_id = ' . intval( $data['category_id'] );
}
 
if( is_numeric( $data['status'] ) )
{
	$implode[] = 'status = ' . intval( $data['status'] );
}

if( $date_from && $date_to )
{
	$implode[] = 'date_added BETWEEN ' . intval( $date_from ) . ' AND ' . intval( $date_to );
}

$sql = TABLE_ONLINETEST_NAME . '_essay';

if( $implode )
{
	$sql .= ' WHERE '  . implode( ' AND ', $implode );
}

$sort = $nv_Request->get_string( 'sort', 'get', '' );

$order = $nv_Request->get_string( 'order', 'get' ) == 'asc' ? 'asc' : 'desc';

$sort_data = array(
	'question',
	'category_id',
	'level_id',
	'user_name',
	'date_added',
	'date_modified',
	'status' );

if( isset( $sort ) && in_array( $sort, $sort_data ) )
{

	$sql .= ' ORDER BY ' . $sort;
}
else
{
	$sql .= ' ORDER BY date_added';
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

$base_url = NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=essay&amp;sort=' . $sort . '&amp;order=' . $order . '&amp;per_page=' . $onlineTestConfig['perpage'];

$db->sqlreset()->select( '*' )->from( $sql )->limit( $onlineTestConfig['perpage'] )->offset( ( $page - 1 ) * $onlineTestConfig['perpage'] );

$result = $db->query( $db->sql() );

$dataContent = array();
while( $rows = $result->fetch() )
{
	$dataContent[] = $rows;
}

$xtpl = new XTemplate( 'essay.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
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
$xtpl->assign( 'TOKEN', md5( $nv_Request->session_id . $global_config['sitekey'] ) );
$xtpl->assign( 'ADD_NEW', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=essay&action=add' );
 
$xtpl->assign( 'DATA', $data );

$order2 = ( $order == 'asc' ) ? 'desc' : 'asc';

$xtpl->assign( 'URL_QUESTION', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=question&amp;order=' . $order2 . '&amp;per_page=' . $onlineTestConfig['perpage'] );
$xtpl->assign( 'URL_CATEGORY', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=category_id&amp;order=' . $order2 . '&amp;per_page=' . $onlineTestConfig['perpage'] );
$xtpl->assign( 'URL_LEVEL', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=level_id&amp;order=' . $order2 . '&amp;per_page=' . $onlineTestConfig['perpage'] );
$xtpl->assign( 'URL_STATUS', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=status&amp;order=' . $order2 . '&amp;per_page=' . $onlineTestConfig['perpage'] );
$xtpl->assign( 'URL_DATE_ADDED', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=date_added&amp;order=' . $order2 . '&amp;per_page=' . $onlineTestConfig['perpage'] );
$xtpl->assign( 'URL_DATE_MODIFIED', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=date_modified&amp;order=' . $order2 . '&amp;per_page=' . $onlineTestConfig['perpage'] );
$xtpl->assign( 'URL_USER_NAME', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=user_name&amp;order=' . $order2 . '&amp;per_page=' . $onlineTestConfig['perpage'] );

$xtpl->assign( 'QUESTION_ORDER', ( $sort == 'question' ) ? 'class="' . $order2 . '"' : '' );
$xtpl->assign( 'CATEGORY_ORDER', ( $sort == 'category_id' ) ? 'class="' . $order2 . '"' : '' );
$xtpl->assign( 'LEVEL_ORDER', ( $sort == 'level_id' ) ? 'class="' . $order2 . '"' : '' );
$xtpl->assign( 'STATUS_ORDER', ( $sort == 'status' ) ? 'class="' . $order2 . '"' : '' );
$xtpl->assign( 'DATE_ADDED_ORDER', ( $sort == 'date_added' ) ? 'class="' . $order2 . '"' : '' );
$xtpl->assign( 'DATE_MODIFIED_ORDER', ( $sort == 'date_modified' ) ? 'class="' . $order2 . '"' : '' );
$xtpl->assign( 'USER_NAME_ORDER', ( $sort == 'user_name' ) ? 'class="' . $order2 . '"' : '' );

if( $nv_Request->get_string( $module_data . '_success', 'session' ) )
{
	$xtpl->assign( 'SUCCESS', $nv_Request->get_string( $module_data . '_success', 'session' ) );

	$xtpl->parse( 'main.success' );

	$nv_Request->unset_request( $module_data . '_success', 'session' );

}

if( $onlineTestCategory )
{
	foreach( $onlineTestCategory as $key => $item )
	{
		$xtitle_i = '';
		if( $item['lev'] > 0 )
		{
			$xtitle_i .= '&nbsp;';
			for( $i = 1; $i <= $item['lev']; $i++ )
			{
				$xtitle_i .= '&nbsp;&nbsp;';
			}
		}
		
		$xtitle_i .= $item['title'];
		$xtpl->assign( 'CATEGORY', array(
			'key' => $key,
			'name' => $xtitle_i,
			'selected' => ( $key == $data['category_id'] ) ? 'selected="selected"' : '' ) );
		$xtpl->parse( 'main.category' );
	}
}

 

if( $onlineTestStatus )
{
	foreach( $onlineTestStatus as $key => $item )
	{
		$xtpl->assign( 'STATUS', array(
			'key' => $key,
			'name' => $item,
			'selected' => ( $key == $data['status'] && is_numeric( $data['status'] ) ) ? 'selected="selected"' : '' ) );
		$xtpl->parse( 'main.status' );
	}
}

if( ! empty( $dataContent ) )
{

	foreach( $dataContent as $item )
	{
		$item['category'] = ( isset( $onlineTestCategory[$item['category_id']] ) ) ? $onlineTestCategory[$item['category_id']]['title'] : '';
		$item['status_checked'] = ( $item['status'] ) ? 'checked="checked"' : '';
		$item['date_added'] = nv_date( 'd/m/Y', $item['date_added'] );
		$item['date_modified'] = nv_date( 'd/m/Y', $item['date_modified'] );
		$item['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $item['essay_id'] );
		$item['edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&action=edit&token=' . $item['token'] . '&essay_id=' . $item['essay_id'];
		$xtpl->assign( 'LOOP', $item );
		$xtpl->parse( 'main.loop' );
	}

}

$generate_page = nv_generate_page( $base_url, $num_items, $onlineTestConfig['perpage'], $page );
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
