<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */

if( ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );

if( ! nv_function_exists( 'nukevn_block_statistics' ) )
{
 
	function nukevn_block_statistics( $block_config )
	{
		global $module_array_group_exam, $nv_Cache, $client_info, $global_config, $module_info, $lang_module, $db, $db_slave, $module_config, $site_mods;

		$module = $block_config['module'];
		$mod_upload = $site_mods[$module]['module_upload'];
		$mod_data = $site_mods[$module]['module_data'];
		$mod_file= $site_mods[$module]['module_file'];
		$content = '';
		
		//$timereset = ( $block_config['timereset'] ) ? $block_config['timereset'] : 5;
		$timereset =  5;
		
		$timereset = $timereset * 60;
		
		$cache_file = NV_LANG_DATA . '_block_statistics_' . NV_CACHE_PREFIX . '.cache';
		
		$path_file = NV_ROOTDIR . '/' . NV_CACHEDIR . '/' . $module . '/' . $cache_file;
 
		if( file_exists( $path_file ) && ( NV_CURRENTTIME - $timereset ) > filemtime( $path_file ) )
		{
			nv_deletefile( $path_file );
		}
		
		
		// if( ( $cache = $nv_Cache->getItem( $module, $cache_file ) ) != false )
		// {
			// $content = unserialize( $cache );
		// }
		// else
		// {
			if( file_exists( NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $site_mods[$module]['module_file'] . '/BlockStatistics.tpl' ) )
			{
				$block_theme = $module_info['template'];
			}
			else
			{
				$block_theme = 'default';
			}
 
			$dataContent = array();
 

			$xtpl = new XTemplate( 'BlockStatistics.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/' . $site_mods[$module]['module_file'] . '' );
 
			$xtpl->assign( 'IP', $client_info['ip'] );

			$totalTypeExam = $db_slave->query( 'SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_type_exam WHERE status=1' )->fetchColumn();
			$totalGroupExam = $db_slave->query( 'SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_group_exam WHERE status=1' )->fetchColumn();
			$totalQuestion = $db_slave->query( 'SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_question WHERE status=1' )->fetchColumn();
			$totalHistory = $db_slave->query( 'SELECT COUNT(DISTINCT userid) FROM ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_history' )->fetchColumn();
			$totalCategory = $db_slave->query( 'SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_category WHERE status=1' )->fetchColumn();
 
			  
			$xtpl->assign( 'totalTypeExam', $totalTypeExam );
			$xtpl->assign( 'totalGroupExam', $totalGroupExam );
			$xtpl->assign( 'totalQuestion', $totalQuestion );
			$xtpl->assign( 'totalHistory', $totalHistory );
			$xtpl->assign( 'totalCategory', $totalCategory );
 

			$total_users = $db_slave->query( 'SELECT COUNT(*) FROM ' . NV_USERS_GLOBALTABLE . ' WHERE active=1' )->fetchColumn();
			$xtpl->assign( 'COUNT_USERS', number_format( $total_users ) );

			$current_month = date( 'dm', NV_CURRENTTIME );
			$usersBirthday = array();
			$sql = "SELECT username FROM " . NV_USERS_GLOBALTABLE . " WHERE active=1 AND DATE_FORMAT(FROM_UNIXTIME(birthday),'%d%m') = " . $current_month;
			
			$result = $db_slave->query( $sql );
			while( list( $username ) = $result->fetch( 3 ) )
			{
				$usersBirthday[] = $username;
			}
			$result->closeCursor();

			if( !empty( $usersBirthday ) )
			{
				$xtpl->assign( 'USERNAME_BIRTHDAY', implode( ', ', $usersBirthday ) );
				$xtpl->parse( 'main.birthday' );
			}

			$newUsers = array();
			$sql = "SELECT username FROM " . NV_USERS_GLOBALTABLE . " WHERE active=1 ORDER BY regdate DESC LIMIT 0,10";
			$result = $db_slave->query( $sql );
			while( list( $new_username ) = $result->fetch( 3 ) )
			{
				$newUsers[] = $new_username;
			}
			$result->closeCursor();

			if( $newUsers )
			{
				$xtpl->assign( 'NEW_USERNAME', implode( ', ', $newUsers ) );
				$xtpl->parse( 'main.new_users' );
			}

			
			$sql = "SELECT c_type, c_count FROM " . NV_COUNTER_GLOBALTABLE . " WHERE (c_type='day' AND c_val='" . date( 'd', NV_CURRENTTIME ) . "') OR (c_type='month' AND c_val='" . date( 'M', NV_CURRENTTIME ) . "') OR (c_type='total' AND c_val='hits')";
			$result = $db_slave->query( $sql );
			while( list( $c_type, $c_count ) = $result->fetch( 3 ) )
			{
				if( $c_type == 'day' )
				{
					$xtpl->assign( 'COUNT_DAY', number_format( $c_count ) );
				}
				elseif( $c_type == 'month' )
				{
					$xtpl->assign( 'COUNT_MONTH', number_format( $c_count ) );
				}
				elseif( $c_type == 'total' )
				{
					$xtpl->assign( 'COUNT_ALL', number_format( $c_count ) );
				}
			}
			$result->closeCursor();

			$sql = 'SELECT userid, username FROM ' . NV_SESSIONS_GLOBALTABLE . ' WHERE onl_time >= ' . ( NV_CURRENTTIME - NV_ONLINE_UPD_TIME );
			$result = $db_slave->query( $sql );
			$count_online = $users = $bots = $guests = 0;
			$listUsersOnline = array();
			while( $row = $result->fetch() )
			{
				++$count_online;

				if( $row['userid'] )
				{
					$listUsersOnline[] = $row['username'];

					++$users;
				}
				elseif( preg_match( '/^bot\:/', $row['username'] ) )
				{
					++$bots;
				}
				else
				{
					++$guests;
				}
			}
			$result->closeCursor();
			if( $listUsersOnline )
			{
				$xtpl->assign( 'USERS_ONLINE', implode( ', ', $listUsersOnline ) );
				$xtpl->parse( 'main.users_online' );
			}

			$xtpl->assign( 'COUNT_ONLINE_USERS', number_format( $users ) );
			$xtpl->assign( 'COUNT_ONLINE', number_format( $count_online ) );
			$xtpl->parse( 'main' );
			$content = $xtpl->text( 'main' );
			
			// $cache = serialize( $content );
			// $nv_Cache->setItem( $module, $cache_file, $cache );
			
			
		// }
		
		return $content;
	}
}

if( defined( 'NV_SYSTEM' ) )
{
	global $site_mods, $module_name, $module_array_group_exam, $nv_Cache;
	$module = $block_config['module'];
	if( isset( $site_mods[$module] ) )
	{
		$content = nukevn_block_statistics( $block_config );
	}
}
