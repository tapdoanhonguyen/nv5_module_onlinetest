<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */

if ( ! defined( 'NV_ADMIN' ) or ! defined( 'NV_MAINFILE' ) or ! defined( 'NV_IS_MODADMIN' ) ) die( 'Stop!!!' );
 

define( 'NV_IS_FILE_ADMIN', true );

define( 'TABLE_ONLINETEST_NAME', NV_PREFIXLANG . '_' . $module_data ); 

define( 'ACTION_METHOD', $nv_Request->get_string( 'action', 'get,post', '' ) ); 
 
require_once NV_ROOTDIR . '/modules/' . $module_file . '/global.functions.php'; 
 
$onlineTestInhome = array( '0'=> $lang_module['no'], '1'=> $lang_module['yes'] );
