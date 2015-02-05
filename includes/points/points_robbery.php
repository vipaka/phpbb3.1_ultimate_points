<?php
/**
*
* @package Ultimate Points
* @version $Id: points_robbery.php 819 2012-02-18 08:47:16Z Wuerzi $
* @copyright (c) 2009 wuerzi & femu
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/


if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* @package Ultimate Points
*/

		global $template, $user, $db, $config, $phpEx, $phpbb_root_path, $table_prefix, $auth, $checked_user, $check_auth;

		// Check, if user is allowed to use the robbery
		if (!$auth->acl_get('u_use_robbery'))
		{
			$message = $user->lang['NOT_AUTHORISED'] . '<br /><br /><a href="' . append_sid("{$phpbb_root_path}points.$phpEx") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
			trigger_error($message);
		}

		// Check, if robbery is enabled
		if (!$points_config['robbery_enable'])
		{
			$message = $user->lang['ROBBERY_DISABLED'] . '<br /><br /><a href="' . append_sid("{$phpbb_root_path}points.$phpEx") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
			trigger_error($message);
		}

		// Add part to bar
		$template->assign_block_vars('navlinks', array(
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=robbery"),
			'FORUM_NAME'	=> sprintf($user->lang['POINTS_ROBBERY'], $config['points_name']),
		));

		// Read out cash of current user
		$pointsa = $user->data['user_points'];

		// Check key
		add_form_key('robbery_attack');

		if(isset($_POST['submit']))
		{
			if (!check_form_key('robbery_attack'))
			{
				trigger_error('FORM_INVALID');
			}

			// Add all required informations
			$username 			= utf8_normalize_nfc(request_var('username', '', true));
			$attacked_amount	= round(request_var('attacked_amount', 0.00), 2);

			if ($attacked_amount <= 0)
			{
				$message = $user->lang['ROBBERY_TOO_SMALL_AMOUNT'] . '<br /><br /><a href="' . append_sid("{$phpbb_root_path}points.$phpEx", "mode=robbery") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
				trigger_error($message);
			}

			// Check, if user has entered the name of the user to be robbed
			if (empty($username))
			{
				$message = $user->lang['ROBBERY_NO_ID_SPECIFIED'] . '<br /><br /><a href="' . append_sid("{$phpbb_root_path}points.$phpEx", "mode=robbery") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
				trigger_error($message);
			}

			// Check, if user tries to rob himself
			if ($user->data['username_clean'] == utf8_clean_string($username))
			{
				$message = $user->lang['ROBBERY_SELF'] . '<br /><br /><a href="' . append_sid("{$phpbb_root_path}points.$phpEx", "mode=robbery") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
				trigger_error($message);
			}

			// Check, if user is trying to rob to much cash
			if ($points_values['robbery_loose'] != 0)
			{
				if ($user->data['user_points'] < ($attacked_amount / 100 * $points_values['robbery_loose']))
				{
					$message = $user->lang['ROBBERY_TO_MUCH'] . '<br /><br /><a href="' . append_sid("{$phpbb_root_path}points.$phpEx", "mode=robbery") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
					trigger_error($message);
				}
			}

			// Select the user_id and language of user to be robbed
			$sql = 'SELECT user_id, user_type
				FROM ' . USERS_TABLE . "
				WHERE username_clean = '" . $db->sql_escape(utf8_clean_string($username)) . "'
					AND user_type IN (" . USER_NORMAL . ', ' . USER_FOUNDER . ')';
			$result = $db->sql_query($sql);
			$user_id = (int) $db->sql_fetchfield('user_id');
			$db->sql_freeresult($result);

			$sql_array = array(
				'SELECT'    => '*',
				'FROM'      => array(
					USERS_TABLE => 'u',
				),
				'WHERE'		=> 'user_id = "' . (int) $user_id . '"',
			);
			$sql = $db->sql_build_query('SELECT', $sql_array);
			$result = $db->sql_query($sql);
			$user_info = $db->sql_fetchrow($result);;
			$db->sql_freeresult($result);

			// If no matching user id is found
			if (!$user_id)
			{
				$message = $user->lang['POINTS_NO_USER'] . '<br /><br /><a href="' . append_sid("{$phpbb_root_path}points.$phpEx", "mode=robbery") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
				trigger_error($message);
			}

			// If the robbed user doesn't have enough cash
			$sql_array = array(
				'SELECT'    => 'user_points, user_robbery_pm',
				'FROM'      => array(
					USERS_TABLE => 'u',
				),
				'WHERE'		=> 'user_id = ' . (int) $user_id,
			);
			$sql = $db->sql_build_query('SELECT', $sql_array);
			$result = $db->sql_query($sql);
			$user1_row = $db->sql_fetchrow($result);

			$pointsa = $user1_row['user_points'];
			$user_robbery_pm = $user1_row['user_robbery_pm'];
			$db->sql_freeresult($result);

			if ($attacked_amount > $pointsa)
			{
				$message = $user->lang['ROBBERY_TO_MUCH_FROM_USER'] . '<br /><br /><a href="' . append_sid("{$phpbb_root_path}points.$phpEx", "mode=robbery") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
				trigger_error($message);
			}

			// Check, if user tries to rob more than x % of users cash
			if ($points_values['robbery_max_rob'] != 0)
			{
				if ($attacked_amount > ($pointsa / 100 * $points_values['robbery_max_rob']))
				{
					$message = sprintf($user->lang['ROBBERY_MAX_ROB'], $points_values['robbery_max_rob']) . '<br /><br /><a href="' . append_sid("{$phpbb_root_path}points.$phpEx", "mode=robbery") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
					trigger_error($message);
				}
			}

			// Get some info about the robbed user
			$user_namepoints = get_username_string('full', $checked_user['user_id'], $checked_user['username'], $checked_user['user_colour']);

			// Genarate a random number
			$rand_base = $points_values['robbery_chance'];
			$rand_value = rand(0, 100);

			// If robbery was successful and PM is enabled, send PM
			if ($rand_value <= $rand_base)
			{
				add_points($user->data['user_id'], $attacked_amount);
				substract_points($user_id, $attacked_amount);

				if ($points_config['robbery_sendpm'] && $user_info['user_allow_pm'] == 1 && $user_robbery_pm)
				{
					// Prepare user lang
					$sql_array = array(
						'SELECT'    => '*',
						'FROM'      => array(
							USERS_TABLE => 'u',
						),
						'WHERE'		=> 'user_id = ' . (int) $user_id,
					);
					$sql = $db->sql_build_query('SELECT', $sql_array);
					$result = $db->sql_query($sql);
					$user_row = $db->sql_fetchrow($result);
					$db->sql_freeresult($result);

					// first check if language file exists, if not, use the default language
					$user_row['user_lang'] = (file_exists($phpbb_root_path . 'language/' . $user_row['user_lang'] . "/mods/points.$phpEx")) ? $user_row['user_lang'] : $config['default_lang'];

					// load receivers language
					include($phpbb_root_path . 'language/' . basename($user_row['user_lang']) . "/mods/points.$phpEx");

					// Send PM
					$pm_subject	= utf8_normalize_nfc(sprintf($lang['ROBBERY_PM_SUCCESFUL_SUBJECT'], $config['points_name']));
					$pm_text	= utf8_normalize_nfc(sprintf($lang['ROBBERY_PM_SUCCESFUL_BODY'], $user_namepoints, $attacked_amount, $config['points_name']));

					$poll = $uid = $bitfield = $options = '';
					generate_text_for_storage($pm_subject, $uid, $bitfield, $options, false, false, false);
					generate_text_for_storage($pm_text, $uid, $bitfield, $options, true, true, true);

					$pm_data = array(
						'address_list'		=> array ('u' => array($user_id => 'to')),
						'from_user_id'		=> $user->data['user_id'],
						'icon_id'			=> 0,
						'from_username'		=> $user->lang['ROBBERY_PM_SENDER'],
						'from_user_ip'		=> '',

						'enable_bbcode'		=> true,
						'enable_smilies'	=> true,
						'enable_urls'		=> true,
						'enable_sig'		=> true,

						'message'			=> $pm_text,
						'bbcode_bitfield'	=> $bitfield,
						'bbcode_uid'		=> $uid,
					);

					submit_pm('post', $pm_subject, $pm_data, false);
				}

				$message = $user->lang['ROBBERY_SUCCESFUL'] . '<br /><br /><a href="' . append_sid("{$phpbb_root_path}points.$phpEx", "mode=robbery") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
				trigger_error($message);
			}
			// If robbery failed and PM is enabled, send PM
			else
			{

				if ($points_values['robbery_loose'] != 0)
				{
					$robbery_pm_info = '';
					$lose = $attacked_amount / 100 * $points_values['robbery_loose'];
					substract_points($user->data['user_id'], $lose);

					// Prepare user lang
					$sql_array = array(
						'SELECT'    => '*',
						'FROM'      => array(
							USERS_TABLE => 'u',
						),
						'WHERE'		=> 'user_id = ' . (int) $user_id,
					);
					$sql = $db->sql_build_query('SELECT', $sql_array);
					$result = $db->sql_query($sql);
					$user_row = $db->sql_fetchrow($result);
					$db->sql_freeresult($result);

					// What shall we do with the points we substract from the failed robbery?
					// Send it to the lottery jackpot
					if ($points_config['robbery_usage'])
					{
						$robbery_usage_info = $user->lang['ROBBERY_USAGE_INFO_LOTTERY'];

						$sql = 'UPDATE ' . POINTS_VALUES_TABLE . '
							SET lottery_jackpot = lottery_jackpot + ' . $lose;
						$result = $db->sql_query($sql);
					}
					else
					{
						// Select randomly, if robbed user will get the points or if it's added to the lottery jackpot
						$random_usage = mt_rand(0, 1);

						// Add to Lottery Jackpot
						if ($random_usage == 0)
						{
							$robbery_usage_info = $user->lang['ROBBERY_USAGE_INFO_LOTTERY'];

							$sql = 'UPDATE ' . POINTS_VALUES_TABLE . '
								SET lottery_jackpot = lottery_jackpot + ' . $lose;
							$result = $db->sql_query($sql);
						}
						// Else give robbed user
						else
						{
							$robbery_usage_info = sprintf($user->lang['ROBBERY_USAGE_INFO_USER'], $username);
							$robbery_pm_info = 1;

							$sql = 'UPDATE ' . USERS_TABLE . '
								SET user_points = user_points + ' . $lose . '
							WHERE user_id = ' . (int) $user_id;
							$result = $db->sql_query($sql);
						}
					}

					if ($points_config['robbery_sendpm']  && $user_info['user_allow_pm'] == 1 && $user_robbery_pm)
					{
						// Select the receiver language
						$user_row['user_lang'] = (file_exists($phpbb_root_path . 'language/' . $user_row['user_lang'] . "/mods/points.$phpEx")) ? $user_row['user_lang'] : $config['default_lang'];

						// load receivers language
						include($phpbb_root_path . 'language/' . basename($user_row['user_lang']) . "/mods/points.$phpEx");

						// Send PM
						$pm_subject	= utf8_normalize_nfc($lang['ROBBERY_PM_BAD_SUBJECT']);

						if ($robbery_pm_info == 1)
						{
							$pm_text	= utf8_normalize_nfc(sprintf($lang['ROBBERY_PM_BAD_BODY_1'], $user_namepoints, $attacked_amount, $config['points_name'], sprintf(number_format_points($lose))));
						}
						else
						{
							$pm_text	= utf8_normalize_nfc(sprintf($lang['ROBBERY_PM_BAD_BODY'], $user_namepoints, $attacked_amount, $config['points_name']));
						}

						$poll = $uid = $bitfield = $options = '';
						generate_text_for_storage($pm_subject, $uid, $bitfield, $options, false, false, false);
						generate_text_for_storage($pm_text, $uid, $bitfield, $options, true, true, true);

						$pm_data = array(
							'address_list'		=> array ('u' => array($user_id => 'to')),
							'from_user_id'		=> $user->data['user_id'],
							'from_username'		=> $user->lang['ROBBERY_PM_SENDER'],
							'icon_id'			=> 0,
							'from_user_ip'		=> '',

							'enable_bbcode'		=> true,
							'enable_smilies'	=> true,
							'enable_urls'		=> true,
							'enable_sig'		=> true,

							'message'			=> $pm_text,
							'bbcode_bitfield'	=> $bitfield,
							'bbcode_uid'		=> $uid,
						);
						submit_pm('post', $pm_subject, $pm_data, false);
					}

					$message = $user->lang['ROBBERY_BAD'] . '<br /><br />' . $robbery_usage_info . '<br /><br /><a href="' . append_sid("{$phpbb_root_path}points.$phpEx", "mode=robbery") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
					trigger_error($message);
				}
			}

			$template->assign_vars(array(
				'USER_NAME'				=> get_username_string('full', $checked_user['user_id'], $points_config['username'], $points_config['user_colour']),
				'U_ACTION'				=> $this->u_action,
				'S_HIDDEN_FIELDS'		=> $hidden_fields,
			));
		}

		// If Robbery PN is enabled, show option to disable for the users
		if ($points_config['robbery_sendpm'])
		{
			if(isset($_POST['robbery_pm']))
			{
				if (!check_form_key('robbery_attack'))
				{
					trigger_error('FORM_INVALID');
				}

				$user_robbery_pm = request_var('user_robbery_pm', 0);

				$sql = 'UPDATE ' . USERS_TABLE . '
					SET user_robbery_pm = ' . $user_robbery_pm . '
					WHERE user_id = ' . (int) $user->data['user_id'];
				$result = $db->sql_query($sql);

				$redirect_url = append_sid("{$phpbb_root_path}points.$phpEx", "mode=robbery");

				meta_refresh(5, $redirect_url);
				$message = $user->lang['ROBBERY_PM_CHANGE'] . '<br /><br /><a href="' . append_sid("{$phpbb_root_path}points.$phpEx", "mode=robbery") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
				trigger_error($message);

				$template->assign_vars(array(
					'USER_ROBBERY_PM'	=> $user->data['user_robbery_pm'],
					'U_ACTION'			=> $this->u_action,
				));
			}
		}

		$template->assign_vars(array(
			'S_ROBBERY_PM'			=> $points_config['robbery_sendpm'],

			'USER_POINTS'			=> sprintf(number_format_points($pointsa)),
			'POINTS_NAME'			=> $config['points_name'],
			'LOTTERY_NAME'			=> $points_values['lottery_name'],
			'BANK_NAME'				=> $points_values['bank_name'],

			'L_ROBBERY_CHANCE'		=> sprintf($user->lang['ROBBERY_CHANCE'], (number_format_points($points_values['robbery_max_rob'])), (number_format_points($points_values['robbery_chance']))),
			'L_ROBBERY_AMOUNTLOSE'	=> sprintf($user->lang['ROBBERY_AMOUNTLOSE'], (number_format_points($points_values['robbery_loose']))),

			'U_FIND_USERNAME'		=> append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=searchuser&amp;form=post&amp;field=username&amp;select_single=true"),
			'U_TRANSFER_USER'		=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=transfer_user"),
			'U_LOGS'				=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=logs"),
			'U_LOTTERY'				=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=lottery"),
			'U_BANK'				=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=bank"),
			'U_ROBBERY'				=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=robbery"),
			'U_INFO'				=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=info"),
			'U_USE_TRANSFER'		=> $auth->acl_get('u_use_transfer'),
			'U_USE_LOGS'			=> $auth->acl_get('u_use_logs'),
			'U_USE_LOTTERY'			=> $auth->acl_get('u_use_lottery'),
			'U_USE_BANK'			=> $auth->acl_get('u_use_bank'),
			'U_USE_ROBBERY'			=> $auth->acl_get('u_use_robbery'),
		));

		// Generate the page
		page_header($user->lang['POINTS_ROBBERY']);

		// Generate the page template
		$template->set_filenames(array(
			'body'	=> 'points_robbery.html'
		));

		page_footer();

?>