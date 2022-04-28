<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */

if( ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );
require_once NV_ROOTDIR . '/modules/onlinetest/phpoffice/autoload.php';
function getConfig( $module )
{
	global $nv_Cache, $site_mods;

	$list = $nv_Cache->db( 'SELECT config_name, config_value FROM ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_config', '', $module );

	$onlineTestConfig = array();
	foreach( $list as $values )
	{
		$onlineTestConfig[$values['config_name']] = $values['config_value'];
	}
	unset( $list );
	
	return $onlineTestConfig;
}

function getCategory( $module )
{
	global $nv_Cache, $site_mods;
 
	return $nv_Cache->db( 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_category ORDER BY sort ASC', 'category_id', $module);
}

function getLevel( $module )
{
	global $nv_Cache, $site_mods;

	$sql = 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_level ORDER BY weight ASC';
	
	return $nv_Cache->db( $sql, 'level_id', $module);
}

function getGroupExam( $module )
{
	global $nv_Cache, $site_mods;

	$sql = 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_group_exam ORDER BY sort ASC';
	
	return $nv_Cache->db( $sql, 'group_exam_id', $module);
}

function getContributePermission( $module )
{
	global $nv_Cache, $db_slave, $site_mods;
	
	$cache_file = NV_LANG_DATA . '_contribute_permission_' . NV_CACHE_PREFIX . '.cache';
	if( ( $cache = $nv_Cache->getItem( $module, $cache_file ) ) != false )
	{
		$data = unserialize( $cache );
	}
	else
	{
		$data = array();
 
		$result = $db_slave->query( 'SELECT group_id, permission  FROM ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_contribute_permission ORDER BY group_id ASC' );

		while( $item = $result->fetch( ) )
		{			
			$item['permission'] = !empty( $item['permission'] ) ? unserialize( $item['permission'] ) : array();
			$data[$item['group_id']] = $item['permission'];
		}
		
		$cache = serialize( $data );
		
		$nv_Cache->setItem( $module, $cache_file, $cache );
	}
	return $data;
}

function getRandomString( $length = 15 )
{
	return substr( md5( uniqid( '', true ) ), 0, $length );
}

function getHistoryCode( $history_alias )
{
	global $db_slave;
	$exist = $db_slave->query('SELECT COUNT(*) FROM ' . TABLE_ONLINETEST_NAME . '_history WHERE history_alias=' . $db_slave->quote( $history_alias ) )->fetchColumn();
	if( $exist )
	{
		$history_alias = getHistoryCode( $history_alias );
	}else
	{	
		$exist = $db_slave->query('SELECT COUNT(*) FROM ' . TABLE_ONLINETEST_NAME . '_history_essay WHERE history_alias=' . $db_slave->quote( $history_alias ) )->fetchColumn();
		if( $exist )
		{
			$history_alias = getHistoryCode( $history_alias );
		}
		else
		{
			return $history_alias;
		}	
	}
}

function hasPermission( $permission )
{
	global $module_name, $user_info;
	$onlineTestContributePermission = getContributePermission( $module_name );
	
	$return = false;
	foreach( $onlineTestContributePermission as $_group_id => $_permission )
	{ 
		if( !empty( $user_info['in_groups'] ) && in_array( $_group_id, $user_info['in_groups'] ) )
		{
			
			if( isset( $_permission[$permission] ) && $_permission[$permission] == 1 )
			{
				$return = true;
				break;
			}
		}
	}
	return $return;
} 
function shuffleAssoc( &$array )
{
	$keys = array_keys( $array );

	shuffle( $keys );

	foreach( $keys as $key )
	{
		$new[$key] = $array[$key];
	}

	$array = $new;

	return true;
} 
function arrayEqual($a, $b) {
    return ( is_array( $a ) && is_array( $b ) && array_diff( $a, $b ) === array_diff( $b, $a ) );
}

function isHtml( $string )
{
	if ( $string != strip_tags( $string ) )
	return true; 
	return false; 
}
function MakeDir( $currentpath )
{
	global $module_name, $db;
	if( file_exists( NV_UPLOADS_REAL_DIR . '/' . $currentpath ) )
	{
		$upload_real_dir_page = NV_UPLOADS_REAL_DIR . '/' . $currentpath;
	}
	else
	{
		$upload_real_dir_page = NV_UPLOADS_REAL_DIR . '/' . $module_name;
		$e = explode( "/", $currentpath );
		if( ! empty( $e ) )
		{
			$cp = "";
			foreach( $e as $p )
			{
				if( ! empty( $p ) and ! is_dir( NV_UPLOADS_REAL_DIR . '/' . $cp . $p ) )
				{
					$mk = nv_mkdir( NV_UPLOADS_REAL_DIR . '/' . $cp, $p );
					if( $mk[0] > 0 )
					{
						$upload_real_dir_page = $mk[2];
						$db->query( "INSERT IGNORE INTO " . NV_UPLOAD_GLOBALTABLE . "_dir (dirname, time) VALUES ('" . NV_UPLOADS_DIR . "/" . $cp . $p . "', 0)" );
					}
				}
				elseif( ! empty( $p ) )
				{
					$upload_real_dir_page = NV_UPLOADS_REAL_DIR . '/' . $cp . $p;
				}
				$cp .= $p . '/';
			}
		}
		$upload_real_dir_page = str_replace( "\\", "/", $upload_real_dir_page );
	}
	return $upload_real_dir_page;
}
 
/**
 * getDataPage()
 *
 * @param string $base_url
 * @param integer $num_items
 * @param integer $per_page
 * @param integer $on_page
 * @param bool $add_prevnext_text
 * @param bool $onclick
 * @param string $js_func_name
 * @param string $containerid
 * @param bool $full_theme
 * @return
 */
function getDataPage( $base_url, $num_items, $per_page, $on_page, $add_prevnext_text = true, $onclick = false, $js_func_name = 'nv_urldecode_ajax', $containerid = 'generate_page', $full_theme = true )
{
	global $lang_global;

	// Round up total page
	$total_pages = ceil( $num_items / $per_page );

	if( $total_pages < 2 )
	{
		return '';
	}

	if( ! is_array( $base_url ) )
	{
		$amp = preg_match( '/\?/', $base_url ) ? '&amp;' : '?';
		$amp .= 'page=';
	}
	else
	{
		$amp = $base_url['amp'];
		$base_url = $base_url['link'];
	}

	$page_string = '';

	if( $total_pages > 10 )
	{
		$init_page_max = ( $total_pages > 3 ) ? 3 : $total_pages;

		for( $i = 1; $i <= $init_page_max; ++$i )
		{
			$href = ( $i > 1 ) ? $base_url . $amp . $i : $base_url;
			$href = ! $onclick ? "href=\"" . $href . "\"" : "href=\"javascript:void(0)\" onclick=\"" . $js_func_name . "('". $i ."','" . $containerid . "')\"";
			$page_string .= '<li' . ( $i == $on_page ? ' class="active"' : '' ) . '><a' . ( $i == $on_page ? ' href="#"' : ' ' . $href ) . '>' . $i . '</a></li>';
		}

		if( $total_pages > 3 )
		{
			if( $on_page > 1 and $on_page < $total_pages )
			{
				if( $on_page > 5 )
				{
					$page_string .= '<li class="disabled"><span>...</span></li>';
				}

				$init_page_min = ( $on_page > 4 ) ? $on_page : 5;
				$init_page_max = ( $on_page < $total_pages - 4 ) ? $on_page : $total_pages - 4;

				for( $i = $init_page_min - 1; $i < $init_page_max + 2; ++$i )
				{
					$href = ( $i > 1 ) ? $base_url . $amp . $i : $base_url;
					$href = ! $onclick ? "href=\"" . $href . "\"" : "href=\"javascript:void(0)\" onclick=\"" . $js_func_name . "('". $i ."','" . $containerid . "')\"";
					$page_string .= '<li' . ( $i == $on_page ? ' class="active"' : '' ) . '><a' . ( $i == $on_page ? ' href="#"' : ' ' . $href ) . '>' . $i . '</a></li>';
				}

				if( $on_page < $total_pages - 4 )
				{
					$page_string .= '<li class="disabled"><span>...</span></li>';
				}
			}
			else
			{
				$page_string .= '<li class="disabled"><span>...</span></li>';
			}

			for( $i = $total_pages - 2; $i < $total_pages + 1; ++$i )
			{
				$href = ( $i > 1 ) ? $base_url . $amp . $i : $base_url;
				$href = ! $onclick ? "href=\"" . $href . "\"" : "href=\"javascript:void(0)\" onclick=\"" . $js_func_name . "('". $i ."','" . $containerid . "')\"";
				$page_string .= '<li' . ( $i == $on_page ? ' class="active"' : '' ) . '><a' . ( $i == $on_page ? ' href="#"' : ' ' . $href ) . '>' . $i . '</a></li>';
			}
		}
	}
	else
	{
		for( $i = 1; $i < $total_pages + 1; ++$i )
		{
			$href = ( $i > 1 ) ? $base_url . $amp . $i : $base_url;
			$href = ! $onclick ? "href=\"" . $href . "\"" : "href=\"javascript:void(0)\" onclick=\"" . $js_func_name . "('". $i ."','" . $containerid . "')\"";
			$page_string .= '<li' . ( $i == $on_page ? ' class="active"' : '' ) . '><a' . ( $i == $on_page ? ' href="#"' : ' ' . $href ) . '>' . $i . '</a></li>';
		}
	}

	if( $add_prevnext_text )
	{
		if( $on_page > 1 )
		{
			$href = ( $on_page > 2 ) ? $base_url . $amp . ( $on_page - 1 ) : $base_url;
			$href = ! $onclick ? "href=\"" . $href . "\"" : "href=\"javascript:void(0)\" onclick=\"" . $js_func_name . "('". $i ."','" . $containerid . "')\"";
			$page_string = "<li><a " . $href . " title=\"" . $lang_global['pageprev'] . "\">&laquo;</a></li>" . $page_string;
		}
		else
		{
			$page_string = '<li class="disabled"><a href="#">&laquo;</a></li>' . $page_string;
		}

		if( $on_page < $total_pages )
		{
			$href = ( $on_page ) ? $base_url . $amp . ( $on_page + 1 ) : $base_url;
			$href = ! $onclick ? "href=\"" . $href . "\"" : "href=\"javascript:void(0)\" onclick=\"" . $js_func_name . "('". $i ."','" . $containerid . "')\"";
			$page_string .= '<li><a ' . $href . ' title="' . $lang_global['pagenext'] . '">&raquo;</a></li>';
		}
		else
		{
			$page_string .= '<li class="disabled"><a href="#">&raquo;</a></li>';
		}
	}

	if( $full_theme !== true )
	{
		return $page_string;
	}

	return '<ul class="pagination">' . $page_string . '</ul>';
}


function onlineTest_viewpdf( $file_url )
{
	global $lang_module, $lang_global;
	$xtpl = new XTemplate( 'viewer.tpl', NV_ROOTDIR . '/' . NV_ASSETS_DIR . '/js/pdf.js' );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'PDF_JS_DIR', NV_BASE_SITEURL . NV_ASSETS_DIR . '/js/pdf.js/' );
	$xtpl->assign( 'PDF_URL', $file_url );
	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );
}
 
 
function getYoutubeId($sYoutubeUrl) {
 
    # set to zero
    $youtube_id = "";
    $sYoutubeUrl = trim($sYoutubeUrl);
 
    # the User entered only the eleven chars long id, Case 1
    if(strlen($sYoutubeUrl) === 11) {
        $youtube_id = $sYoutubeUrl;
        return $sYoutubeUrl;
    }
 
    # the User entered a Url
    else {
 
        # try to get all Cases
        if (preg_match('~(?:youtube.com/(?:user/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu.be/)([^"&?/ ]{11})~i', $sYoutubeUrl, $match)) {
            $youtube_id = $match[1];
            return $youtube_id;
        }
        # try to get some other channel codes, and fallback extractor
        elseif(preg_match('~http://www.youtube.com/v/([A-Za-z0-9-_]+).+?|embed/([0-9A-Za-z-_]{11})|watch?v=([0-9A-Za-z-_]{11})|#.*/([0-9A-Za-z-_]{11})~si', $sYoutubeUrl, $match)) {
 
            for ($i=1; $i<=4; $i++) {
                if (strlen($match[$i])==11) {
                    $youtube_id = $match[$i];
                    break;
                }
            }
            return $youtube_id;
        }
        else {
            $youtube_id = "No valid YoutubeId extracted";
            return false;
        }
    }
} 
 
$onlineTestConfig = getConfig( $module_name );
$onlineTestCategory = getCategory( $module_name );
$onlineTestGroupExam = getGroupExam( $module_name );
$onlineTestStatus = array( '0'=> $lang_module['question_status0'], '1'=> $lang_module['question_status1'] );
$onlineTestTypeView = array( '0'=> $lang_module['group_exam_type_view_0'], '1'=> $lang_module['group_exam_type_view_1'] );
$onlineTestTitleFirst = array( '0'=> 'A', '1'=> 'B', '2'=> 'C', '3'=> 'D', '4'=> 'E', '5'=> 'F', '6'=> 'G', '7'=> 'H', '8'=> 'I', '9'=> 'J', '10'=> 'K', '11'=> 'L', '12'=> 'M', '13'=> 'N', '14'=> 'O', '15'=> 'P', '16'=> 'Q', '17'=> 'R', '18'=> 'S', '19'=> 'T', '20'=> 'U', '21'=> 'V', '22'=> 'W', '23'=> 'X', '24'=> 'Y', '25'=> 'Z' );
$onlineTestTitleError = array( '1'=> 'Sai lỗi chính tả', '2'=> 'Sai đáp án, lời giải', '3'=> 'Sai đề bài', '4'=> 'Lỗi khác' );
$onlineTestTypeExam = array( '0'=> 'Tự động chọn câu hỏi', '1'=> 'Chọn câu hỏi thủ công' , '2'=> 'Câu hỏi theo file PDF' );
 
$onlineTestPermissionAll = array(
	'view' => $lang_module['permission_view'],	
	'create' => $lang_module['permission_create'],	
	'modified' => $lang_module['permission_modified'],
	'delete' => $lang_module['permission_delete'],
);


