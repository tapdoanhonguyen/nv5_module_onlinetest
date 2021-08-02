<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2019 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Fri, 06 Mar 2019 10:28:30 GMT
 */

if ( ! defined( 'NV_ADMIN' ) or ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );

$module_version = array(
	'name' => 'Online Test',
	'modfuncs' => 'main, search, dotest, dotestessay, viewgroupexam, typeexam, group-user, history, history-essay, essay, contribute, recharge, recharge-history, share',
    'change_alias' => 'search,history,history-essay, essay, contribute, recharge, recharge-history, typeexam, group-user',
    'submenu' => 'search,history,history-essay, essay, contribute, recharge, recharge-history, typeexam, group-user',
	'is_sysmod' => 0,
	'virtual' => 1,
	'version' => '4.3.08',
	'date' => 'Fri, 06 Mar 2019 10:28:30 GMT',
	'author' => 'DANGDINHTU (dlinhvan@gmail.com)',
	'note' => '',
	'uploads_dir' => array(
		$module_name
	)
);