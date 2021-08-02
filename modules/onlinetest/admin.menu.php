<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */

if( ! defined( 'NV_ADMIN' ) ) die( 'Stop!!!' );

$allow_func = array( 'main', 'alias','category','essay','essay_exam', 'get_user', 'export', 'report', 'comment', 'config', 'level', 'question', 'question-temp', 'type_exam', 'group_exam', 'ranking', 'group_user', 'history',  'history-essay', 'contribute_permission', 'bank', 'recharge','rechargebank');


$submenu['history'] = $lang_module['history'];
//$submenu['history-essay'] = $lang_module['history_essay'];
$submenu['type_exam'] = $lang_module['type_exam'];
$submenu['question'] = $lang_module['question'];
$submenu['question-temp'] = $lang_module['question_temp'];
//$submenu['essay'] = $lang_module['essay'];
//$submenu['essay_exam'] = $lang_module['essay_exam'];
$submenu['comment'] = $lang_module['comment'];
$submenu['report'] = $lang_module['report'];
$submenu['category'] = $lang_module['category'];
$submenu['group_exam'] = $lang_module['group_exam'];
$submenu['level'] = $lang_module['level'];
$submenu['ranking'] = $lang_module['ranking'];
$submenu['group_user'] = $lang_module['group_user'];
$submenu['contribute_permission'] = $lang_module['contribute_permission'];

$menu_recharge = array();
$menu_recharge['recharge'] = $lang_module['rechargecard'];
$menu_recharge['rechargebank'] = $lang_module['rechargebank'];
$menu_recharge['rechargebank&action=add'] = $lang_module['rechargebank_add'];
$menu_recharge['bank'] = $lang_module['bank'];
//$submenu['recharge'] = array( 'title' => $lang_module['recharge'], 'submenu' => $menu_recharge );
 
$submenu['config'] = $lang_module['config'];
