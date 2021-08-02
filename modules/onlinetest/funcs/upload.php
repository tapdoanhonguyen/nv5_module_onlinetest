<?php

/**
 * @Project NUKEVIET 4.x
 * @Author Thương mại số (thuongmaiso@gmail.com)
 * @Copyright (C) 2018 Thương mại số. All rights reserved
 * @License: Not free read more http://nukeviet.vn/vi/store/modules/nvtools/
 * @Createdate Thu, 08 Nov 2018 03:08:09 GMT
 */

if ( ! defined( 'NV_IS_MOD_TEST' ) ) die( 'Stop!!!' );


$allow_files_type = array('adobe', 'audio', 'documents', 'flash', 'images', 'real', 'video');


$upload = new NukeViet\Files\Upload( $allow_files_type, $global_config['forbid_extensions'], $global_config['forbid_mimes'], NV_UPLOAD_MAX_FILESIZE, NV_MAX_WIDTH, NV_MAX_HEIGHT );
$upload->setLanguage( $lang_global );
$upload_info = array();
if( isset( $_FILES['fileupload']['tmp_name'] ) and is_uploaded_file( $_FILES['fileupload']['tmp_name'] ) )
{$currentpath = NV_UPLOADS_DIR . '/' . $module_upload . '/' . date( 'Y_m' );
	
	$folder = date('Y_m', NV_CURRENTTIME);
	$currentpath = $module_upload . '/' . $folder;

	if( file_exists( NV_UPLOADS_REAL_DIR . '/' . $currentpath ) )
	{
		$upload_real_dir_page = NV_UPLOADS_REAL_DIR . '/' . $currentpath;
	}
	else
	{
		$upload_real_dir_page = NV_UPLOADS_REAL_DIR . '/' . $module_upload;
		$e = explode( '/', $currentpath );
		if( ! empty( $e ) )
		{
			$cp = '';
			foreach( $e as $p )
			{
				if( ! empty( $p ) and ! is_dir( NV_UPLOADS_REAL_DIR . '/' . $cp . $p ) )
				{
					$mk = nv_mkdir( NV_UPLOADS_REAL_DIR . '/' . $cp, $p );
					if( $mk[0] > 0 )
					{
						$upload_real_dir_page = $mk[2];
						try
						{
							$db->query( "INSERT INTO " . NV_UPLOAD_GLOBALTABLE . "_dir (dirname, time) VALUES ('" . NV_UPLOADS_DIR . "/" . $cp . $p . "', 0)" );
						}
						catch ( PDOException $e )
						{
							trigger_error( $e->getMessage() );
						}
					}
				}
				elseif( ! empty( $p ) )
				{
					$upload_real_dir_page = NV_UPLOADS_REAL_DIR . '/' . $cp . $p;
				}
				$cp .= $p . '/';
			}
		}
		$upload_real_dir_page = str_replace( '\\', '/', $upload_real_dir_page );
	}  

	
	$path = NV_UPLOADS_DIR . '/' . $module_upload .'/' . $folder;
	
	$upload_info = $upload->save_file( $_FILES['fileupload'], NV_ROOTDIR . '/' . $path, false, $global_config['nv_auto_resize'] );
 
	if( isset( $upload_info['name'] ) )
	{
		$json['result'] = 'success';
		$json['result_file'] = str_replace( NV_ROOTDIR, '', $upload_info['name'] );
 
	}
	else
	{
		$json['error'] = $upload_info['error'];
	}
}
else
{
	$json['error'] = $_FILES['fileupload']['error'];	
}
nv_jsonOutput( $json );
