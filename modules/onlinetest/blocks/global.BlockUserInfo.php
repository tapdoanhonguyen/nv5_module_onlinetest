<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */

if( ! defined( 'NV_MAINFILE' ) )
{
	die( 'Stop!!!' );
}

if( ! nv_function_exists( 'nukevn_block_userinfo' ) )
{
 
	function nukevn_block_userinfo_config( $module, $data_block, $lang_block )
	{
		global $db, $site_mods;
		$html = '';
		
		$html .= '<div class="form-group">';
		$html .= '	<label class="control-label col-sm-6">' . $lang_block['display_mode'] . '</label>';
		$html .= '	<div class="col-sm-18">';
		$html .= '		<select class="w300 form-control" name="config_display_mode">';
		
		for( $i = 0; $i <= 1; $i++ )
		{
			$html .= '	<option value="' . $i . '"' . ( $data_block['display_mode'] == $i ? ' selected="selected"' : '' ) . '>' . $lang_block['display_mode' . $i] . '</option>';
		}
		
		$html .= '	</select>';
		$html .= '	</div>';
		$html .= '</div>';		
		
		$html .= '<div class="form-group">';
		$html .= '	<label class="control-label col-sm-6">' . $lang_block['popup_register'] . '</label>';
		$html .= '	<div class="col-sm-18">';
		$html .= '		<select class="w300 form-control" name="config_popup_register">';
		
		for( $i = 0; $i <= 1; $i++ )
		{
			$html .= '	<option value="' . $i . '"' . ( $data_block['popup_register'] == $i ? ' selected="selected"' : '' ) . '>' . $lang_block['popup_register' . $i] . '</option>';
		}
		
		$html .= '	</select>';
		$html .= '	</div>';
		$html .= '</div>';		
 
		return $html;
	}

	function nukevn_block_userinfo_config_submit( $module, $lang_block )
	{
		global $nv_Request;
		$return = array();
		$return['error'] = array();
		$return['config'] = array();
		$return['config']['display_mode'] = $nv_Request->get_int( 'config_display_mode', 'post', 0 );
		$return['config']['popup_register'] = $nv_Request->get_int( 'config_popup_register', 'post', 0 );
		return $return;
	}

 
	function nukevn_block_userinfo( $block_config )
	{
		global $client_info, $groupUsers, $onlineTestConfig , $global_config, $module_name, $db_slave, $user_info, $lang_global, $my_head, $admin_info, $blockID, $nv_Cache, $db, $module_info, $site_mods, $db_config;

		$content = '';
		$module = $block_config['module'];
		$mod_upload = $site_mods[$module]['module_upload'];
		$mod_data = $site_mods[$module]['module_data'];
		$mod_file = $site_mods[$module]['module_file'];
		if( $global_config['allowuserlogin'] )
		{
			if( file_exists( NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/onlinetest/BlockUserInfo.tpl' ) )
			{
				$block_theme = $global_config['module_theme'];
			}
			elseif( file_exists( NV_ROOTDIR . '/themes/' . $global_config['site_theme'] . '/modules/onlinetest/BlockUserInfo.tpl' ) )
			{
				$block_theme = $global_config['site_theme'];
			}
			else
			{
				$block_theme = 'default';
			}

			$xtpl = new XTemplate( 'BlockUserInfo.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/onlinetest' );

			if( file_exists( NV_ROOTDIR . '/modules/onlinetest/language/' . NV_LANG_DATA . '.php' ) )
			{
				include NV_ROOTDIR . '/modules/onlinetest/language/' . NV_LANG_DATA . '.php';
			}
			else
			{
				include NV_ROOTDIR . '/modules/onlinetest/language/vi.php';
			}

			$xtpl->assign( 'LANG', $lang_module );
			$xtpl->assign( 'GLANG', $lang_global );
			$xtpl->assign( 'BLOCKID', $blockID );

			if( defined( 'NV_IS_USER' ) )
			{
				if( file_exists( NV_ROOTDIR . '/' . $user_info['photo'] ) and ! empty( $user_info['photo'] ) )
				{
					$avata = NV_BASE_SITEURL . $user_info['photo'];
				}
				else
				{
					$avata = NV_BASE_SITEURL . 'themes/' . $block_theme . '/images/users/no_avatar.png';
				}

				$user_info['current_login_txt'] = nv_date( 'd/m, H:i', $user_info['current_login'] );

				$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
				$xtpl->assign( 'NV_LANG_VARIABLE', NV_LANG_VARIABLE );
				$xtpl->assign( 'NV_LANG_DATA', NV_LANG_DATA );
				$xtpl->assign( 'URL_LOGOUT', defined( 'NV_IS_ADMIN' ) ? 'nv_admin_logout' : 'bt_logout' );
				$xtpl->assign( 'MODULENAME', $module_info['custom_title'] );
				$xtpl->assign( 'AVATA', $avata );
				$xtpl->assign( 'USER', $user_info );
				$xtpl->assign( 'WELCOME', defined( 'NV_IS_ADMIN' ) ? $lang_global['admin_account'] : $lang_global['your_account'] );
				$xtpl->assign( 'LEVEL', defined( 'NV_IS_ADMIN' ) ? $admin_info['level'] : 'user' );
				$xtpl->assign( 'URL_MODULE', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=users' );
				$xtpl->assign( 'URL_AVATAR', nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=users&amp;' . NV_OP_VARIABLE . '=avatar/upd', true ) );
				$xtpl->assign( 'URL_HREF', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=users&amp;' . NV_OP_VARIABLE . '=' );
				$xtpl->assign( 'URL_ONLINETEST_VCOIN', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '='. $module .'&amp;' . NV_OP_VARIABLE . '=recharge-history' );
				$xtpl->assign( 'URL_ONLINETEST_HISTORY', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '='. $module .'&amp;' . NV_OP_VARIABLE . '=history' );
				$xtpl->assign( 'URL_ONLINETEST_ESSAY', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '='. $module .'&amp;' . NV_OP_VARIABLE . '=history-essay' );
				
				if( $module != $module_name )
				{
					$groupUsers = array();
					if( !empty( $user_info ) )
					{
						$result = $db->query( 'SELECT gu.group_user_id, gu.title FROM ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_group_user_list gul INNER JOIN ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_group_user gu ON( gul.group_user_id = gu.group_user_id ) WHERE gul.userid = ' . intval( $user_info['userid'] ) );

						while( $group = $result->fetch() )
						{
							$groupUsers[$group['group_user_id']] = $group['title'];
							
						}
						$result->closeCursor();
						
					}
					
					$list = $nv_Cache->db( 'SELECT config_name, config_value FROM ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_config', '', $module );

					$onlineTestConfig = array();
					foreach( $list as $values )
					{
						$onlineTestConfig[$values['config_name']] = $values['config_value'];
					}
					unset( $list );
	 
				}
				elseif( !isset( $groupUsers ) )
				{
					$groupUsers = array();
				}
 
				
				if( in_array( $onlineTestConfig['default_group_teacher'], array_keys( $groupUsers ) ) )
				{
					$xtpl->assign( 'URL_ONLINETEST_GROUP', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '='. $module .'&amp;' . NV_OP_VARIABLE . '=group-user' );
					$xtpl->assign( 'URL_ONLINETEST_TYPEEXAM', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '='. $module .'&amp;' . NV_OP_VARIABLE . '=typeexam' );
 
					$xtpl->parse( 'signed.group' );	
				}					
				
 
				
				$totalPoint = $db_slave->query( 'SELECT point FROM ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_point WHERE userid=' . $user_info['userid'] )->fetchColumn();
			
				$totalPoint = intval( $totalPoint );
				$xtpl->assign( 'POINT', number_format( $totalPoint,0, ",", ".") );
  
				list( $exam_number, $total_score ) = $db->query( 'SELECT COUNT(h.userid) as exam_number, sum(h.score) as total_score 
					FROM ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_history h 	 
					LEFT JOIN ' . NV_USERS_GLOBALTABLE . ' u ON ( h.userid = u.userid ) WHERE h.userid=' . intval( $user_info['userid'] ) )->fetch( 3 );
				
				$exam_number = intval( $exam_number );
				$total_score = intval( $total_score );
				
				if( $total_score > 0 && $exam_number > 0)
				{
					$user_level = floor( $total_score / $exam_number );
				}else{
					$user_level =  0;
				}
				 
				$xtpl->assign( 'EXAM_NUMBER', number_format( $exam_number,0, ",", ".") );
				$xtpl->assign( 'TOTAL_SCORE', number_format( $total_score,0, ",", ".") );
				$xtpl->assign( 'USER_LEVEL', number_format( $user_level,0, ",", ".") );
 
				
				if( defined( 'NV_OPENID_ALLOWED' ) )
				{
					$xtpl->parse( 'signed.allowopenid' );
				}

				if( defined( 'NV_IS_ADMIN' ) )
				{
					$new_drag_block = ( defined( 'NV_IS_DRAG_BLOCK' ) ) ? 0 : 1;
					$lang_drag_block = ( $new_drag_block ) ? $lang_global['drag_block'] : $lang_global['no_drag_block'];

					$xtpl->assign( 'NV_ADMINDIR', NV_ADMINDIR );
					$xtpl->assign( 'URL_DBLOCK', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;drag_block=' . $new_drag_block );
					$xtpl->assign( 'LANG_DBLOCK', $lang_drag_block );
					$xtpl->assign( 'URL_ADMINMODULE', NV_BASE_SITEURL . NV_ADMINDIR . '/index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name );
					$xtpl->assign( 'URL_AUTHOR', NV_BASE_SITEURL . NV_ADMINDIR . '/index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=authors&amp;id=' . $admin_info['admin_id'] );

					if( defined( 'NV_IS_SPADMIN' ) )
					{
						$xtpl->parse( 'signed.admintoolbar.is_spadadmin' );
					}
					if( defined( 'NV_IS_MODADMIN' ) and ! empty( $module_info['admin_file'] ) )
					{
						$xtpl->parse( 'signed.admintoolbar.is_modadmin' );
					}
					$xtpl->parse( 'signed.admintoolbar' );
				}

				$xtpl->parse( 'signed' );
				$content = $xtpl->text( 'signed' );
			}
			else
			{
				$xtpl->assign( 'USER_LOGIN', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=users&amp;' . NV_OP_VARIABLE . '=login' );
				$xtpl->assign( 'USER_REGISTER', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=users&amp;' . NV_OP_VARIABLE . '=register' );
				$xtpl->assign( 'USER_LOSTPASS', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=users&amp;' . NV_OP_VARIABLE . '=lostpass' );
				$xtpl->assign( 'NICK_MAXLENGTH', NV_UNICKMAX );
				$xtpl->assign( 'NICK_MINLENGTH', NV_UNICKMIN );
				$xtpl->assign( 'PASS_MAXLENGTH', NV_UPASSMAX );
				$xtpl->assign( 'PASS_MINLENGTH', NV_UPASSMIN );
				$xtpl->assign( 'GFX_WIDTH', NV_GFX_WIDTH );
				$xtpl->assign( 'GFX_HEIGHT', NV_GFX_HEIGHT );
				$xtpl->assign( 'GFX_MAXLENGTH', NV_GFX_NUM );
				$xtpl->assign( 'N_CAPTCHA', $lang_global['securitycode'] );
				$xtpl->assign( 'CAPTCHA_REFRESH', $lang_global['captcharefresh'] );
				$xtpl->assign( 'SRC_CAPTCHA', NV_BASE_SITEURL . 'index.php?scaptcha=captcha&t=' . NV_CURRENTTIME );
				$xtpl->assign( 'NV_HEADER', '' );
				$xtpl->assign( 'NV_REDIRECT', '' );
				$xtpl->assign( 'CHECKSS', NV_CHECK_SESSION );

				$username_rule = empty( $global_config['nv_unick_type'] ) ? sprintf( $lang_global['username_rule_nolimit'], NV_UNICKMIN, NV_UNICKMAX ) : sprintf( $lang_global['username_rule_limit'], $lang_global['unick_type_' . $global_config['nv_unick_type']], NV_UNICKMIN, NV_UNICKMAX );
				$password_rule = empty( $global_config['nv_upass_type'] ) ? sprintf( $lang_global['password_rule_nolimit'], NV_UPASSMIN, NV_UPASSMAX ) : sprintf( $lang_global['password_rule_limit'], $lang_global['upass_type_' . $global_config['nv_upass_type']], NV_UPASSMIN, NV_UPASSMAX );

				$display_layout = empty( $block_config['display_mode'] ) ? 'display_form' : 'display_button';

				$xtpl->assign( 'USERNAME_RULE', $username_rule );
				$xtpl->assign( 'PASSWORD_RULE', $password_rule );

				if( in_array( $global_config['gfx_chk'], array(
					2,
					4,
					5,
					7 ) ) )
				{
					$xtpl->parse( 'main.' . $display_layout . '.captcha' );
				}

				if( in_array( $global_config['gfx_chk'], array(
					3,
					4,
					6,
					7 ) ) )
				{
					$xtpl->parse( 'main.allowuserreg.reg_captcha' );
				}

				if( defined( 'NV_OPENID_ALLOWED' ) )
				{
					foreach( $global_config['openid_servers'] as $server )
					{
						$assigns = array();
						$assigns['href'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=users&amp;' . NV_OP_VARIABLE . '=oauth&amp;server=' . $server . '&amp;nv_redirect=' . nv_redirect_encrypt( $client_info['selfurl'] );
						$assigns['title'] = $lang_global['openid_login'] . ' ' . ucfirst( $server );
						$assigns['img_src'] = NV_BASE_SITEURL . 'themes/' . $block_theme . '/images/users/' . $server . '.png';
						$assigns['img_width'] = $assigns['img_height'] = 24;

						$xtpl->assign( 'OPENID', $assigns );
						$xtpl->parse( 'main.' . $display_layout . '.openid.server' );
					}
					$xtpl->parse( 'main.' . $display_layout . '.openid' );
				}

				if( $global_config['allowuserreg'] )
				{
					if( empty( $block_config['popup_register'] ) )
					{
						! empty( $block_config['display_mode'] ) ? $xtpl->parse( 'main.' . $display_layout . '.allowuserreg_link' ) : $xtpl->parse( 'main.' . $display_layout . '.allowuserreg_linkform' );
					}
					else
					{
						$data_questions = array();
						$sql = "SELECT qid, title FROM " . $db_config['prefix'] . "_users_question WHERE lang='" . NV_LANG_DATA . "' ORDER BY weight ASC";
						$result = $db->query( $sql );
						while( $row = $result->fetch() )
						{
							$data_questions[$row['qid']] = array( 'qid' => $row['qid'], 'title' => $row['title'] );
						}

						foreach( $data_questions as $array_question_i )
						{
							$xtpl->assign( 'QUESTION', $array_question_i['title'] );
							$xtpl->parse( 'main.allowuserreg.frquestion' );
						}

						$datepicker = false;

						$array_field_config = array();
						$result_field = $db->query( 'SELECT * FROM ' . $db_config['prefix'] . '_users_field ORDER BY weight ASC' );
						while( $row_field = $result_field->fetch() )
						{
							$language = unserialize( $row_field['language'] );
							$row_field['title'] = ( isset( $language[NV_LANG_DATA] ) ) ? $language[NV_LANG_DATA][0] : $row['field'];
							$row_field['description'] = ( isset( $language[NV_LANG_DATA] ) ) ? nv_htmlspecialchars( $language[NV_LANG_DATA][1] ) : '';
							if( ! empty( $row_field['field_choices'] ) )
							{
								$row_field['field_choices'] = unserialize( $row_field['field_choices'] );
							}
							elseif( ! empty( $row_field['sql_choices'] ) )
							{
								$row_field['sql_choices'] = explode( '|', $row_field['sql_choices'] );
								$query = 'SELECT ' . $row_field['sql_choices'][2] . ', ' . $row_field['sql_choices'][3] . ' FROM ' . $row_field['sql_choices'][1];
								$result = $db->query( $query );
								$weight = 0;
								while( list( $key, $val ) = $result->fetch( 3 ) )
								{
									$row_field['field_choices'][$key] = $val;
								}
							}
							$array_field_config[] = $row_field;
						}

						if( ! empty( $array_field_config ) )
						{
							$userid = 0;
							foreach( $array_field_config as $_k => $row )
							{
								$row['customID'] = $_k;

								if( ( $row['show_register'] and $userid == 0 ) or $userid > 0 )
								{
									if( $userid == 0 and empty( $custom_fields ) )
									{
										if( ! empty( $row['field_choices'] ) )
										{
											if( $row['field_type'] == 'date' )
											{
												$row['value'] = ( $row['field_choices']['current_date'] ) ? NV_CURRENTTIME : $row['default_value'];
											}
											elseif( $row['field_type'] == 'number' )
											{
												$row['value'] = $row['default_value'];
											}
											else
											{
												$temp = array_keys( $row['field_choices'] );
												$tempkey = intval( $row['default_value'] ) - 1;
												$row['value'] = ( isset( $temp[$tempkey] ) ) ? $temp[$tempkey] : '';
											}
										}
										else
										{
											$row['value'] = $row['default_value'];
										}
									}
									else
									{
										$row['value'] = ( isset( $custom_fields[$row['field']] ) ) ? $custom_fields[$row['field']] : $row['default_value'];
									}
									$row['required'] = ( $row['required'] ) ? 'required' : '';

									$xtpl->assign( 'FIELD', $row );
									if( $row['required'] )
									{
										$xtpl->parse( 'main.allowuserreg.field.loop.required' );
									}
									if( $row['field_type'] == 'textbox' or $row['field_type'] == 'number' )
									{
										$xtpl->parse( 'main.allowuserreg.field.loop.textbox' );
									}
									elseif( $row['field_type'] == 'date' )
									{
										$row['value'] = ( empty( $row['value'] ) ) ? '' : date( 'd/m/Y', $row['value'] );
										$xtpl->assign( 'FIELD', $row );
										$xtpl->parse( 'main.allowuserreg.field.loop.date' );
										$datepicker = true;
									}
									elseif( $row['field_type'] == 'textarea' )
									{
										$row['value'] = nv_htmlspecialchars( nv_br2nl( $row['value'] ) );
										$xtpl->assign( 'FIELD', $row );
										$xtpl->parse( 'main.allowuserreg.field.loop.textarea' );
									}
									elseif( $row['field_type'] == 'editor' )
									{
										$row['value'] = htmlspecialchars( nv_editor_br2nl( $row['value'] ) );
										if( defined( 'NV_EDITOR' ) and nv_function_exists( 'nv_aleditor' ) )
										{
											$array_tmp = explode( '@', $row['class'] );
											$edits = nv_aleditor( 'custom_fields[' . $row['field'] . ']', $array_tmp[0], $array_tmp[1], $row['value'] );
											$xtpl->assign( 'EDITOR', $edits );
											$xtpl->parse( 'main.allowuserreg.field.loop.editor' );
										}
										else
										{
											$row['class'] = '';
											$xtpl->assign( 'FIELD', $row );
											$xtpl->parse( 'main.allowuserreg.field.loop.textarea' );
										}
									}
									elseif( $row['field_type'] == 'select' )
									{
										foreach( $row['field_choices'] as $key => $value )
										{
											$xtpl->assign( 'FIELD_CHOICES', array(
												'key' => $key,
												'selected' => ( $key == $row['value'] ) ? ' selected="selected"' : '',
												'value' => $value ) );
											$xtpl->parse( 'main.allowuserreg.field.loop.select.loop' );
										}
										$xtpl->parse( 'main.allowuserreg.field.loop.select' );
									}
									elseif( $row['field_type'] == 'radio' )
									{
										$number = 0;
										foreach( $row['field_choices'] as $key => $value )
										{
											$xtpl->assign( 'FIELD_CHOICES', array(
												'id' => $row['fid'] . '_' . $number++,
												'key' => $key,
												'checked' => ( $key == $row['value'] ) ? ' checked="checked"' : '',
												'value' => $value ) );
											$xtpl->parse( 'main.allowuserreg.field.loop.radio.loop' );
										}
										$xtpl->parse( 'main.allowuserreg.field.loop.radio' );
									}
									elseif( $row['field_type'] == 'checkbox' )
									{
										$number = 0;
										$valuecheckbox = ( ! empty( $row['value'] ) ) ? explode( ',', $row['value'] ) : array();
										foreach( $row['field_choices'] as $key => $value )
										{
											$xtpl->assign( 'FIELD_CHOICES', array(
												'id' => $row['fid'] . '_' . $number++,
												'key' => $key,
												'checked' => ( in_array( $key, $valuecheckbox ) ) ? ' checked="checked"' : '',
												'value' => $value ) );
											$xtpl->parse( 'main.allowuserreg.field.loop.checkbox.loop' );
										}
										$xtpl->parse( 'main.allowuserreg.field.loop.checkbox' );
									}
									elseif( $row['field_type'] == 'multiselect' )
									{
										$valueselect = ( ! empty( $row['value'] ) ) ? explode( ',', $row['value'] ) : array();
										foreach( $row['field_choices'] as $key => $value )
										{
											$xtpl->assign( 'FIELD_CHOICES', array(
												'key' => $key,
												'selected' => ( in_array( $key, $valueselect ) ) ? ' selected="selected"' : '',
												'value' => $value ) );
											$xtpl->parse( 'main.allowuserreg.field.loop.multiselect.loop' );
										}
										$xtpl->parse( 'main.allowuserreg.field.loop.multiselect' );
									}
									$xtpl->parse( 'main.allowuserreg.field.loop' );
								}
							}
							$xtpl->parse( 'main.allowuserreg.field' );
						}

						$xtpl->parse( 'main.allowuserreg.agreecheck' );
						$xtpl->parse( 'main.allowuserreg' );
						! empty( $block_config['display_mode'] ) ? $xtpl->parse( 'main.' . $display_layout . '.allowuserreg2' ) : $xtpl->parse( 'main.' . $display_layout . '.allowuserreg2_form' );

						if( $datepicker )
						{
							$xtpl->parse( 'main.datepicker' );
						}
					}
				}

				$xtpl->parse( 'main.' . $display_layout );
				$xtpl->parse( 'main' );
				$content = $xtpl->text( 'main' );
			}
		}

		return $content;
	}
}

if( defined( 'NV_SYSTEM' ) )
{
	$content = nukevn_block_userinfo( $block_config );
}
