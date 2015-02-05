<?php
/**
*
* @package phpBB Extension - Vipaka Ultimate Points
* @copyright (c) 2014 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace vipaka\ultimate_points\acp;

class points_module
{
  protected $config_text;
  protected $request;

	var $u_action;

	function main($id, $mode)
	{
		global $db, $user, $auth, $template, $cache, $request, $table_prefix, $points_config, $config, $phpbb_root_path, $phpbb_admin_path, $phpEx, $phpbb_container;

    $this->config_text = $phpbb_container->get('config_text');
    $this->request = $request;
		$user->add_lang('acp/common');
		$this->page_title = $user->lang('ACP_POINTS_SETTINGS');
	  add_form_key('acp_points');
    $points_config = $cache->get('points_config');
    $points_values = $cache->get('points_values');
    
    define('CONFIG_TEXT_TABLE', $table_prefix . 'config_text');
  
    $data = $this->config_text->get_array(array(
      'points_name',
      'ultimate_points_version',
    ));

		if ($request->is_set_post('submit_config'))
			{
				if (!check_form_key('vipaka/points'))
				{
				  trigger_error('FORM_INVALID');
				}

        $data['points_name'] = $this->request->variable('points_name', '', true);
        $data['ultimate_points_version'] = $this->request->variable('ultimate_points_version', '', true);
        
				$config->set('points_enable', $request->variable('points_enable', 0));
 

        $this->config_text->set_array(array(
          'points_name'     => $data['points_name'],
          'ultimate_points_version'    => $data['ultimate_points_version'],
        ));

				trigger_error($user->lang('ACP_POINTS_SETTING_SAVED') . adm_back_link($this->u_action));
			}
		
		define('POINTS_TABLE', $table_prefix . 'points_values');
		 switch($mode)
    	{
  
		case 'points':
        $this->page_title = $user->lang('ACP_POINTS_INDEX_TITLE');
        $this->tpl_name = 'acp_points_main';

        $submit = request_var('submit', '');

        if ($submit)
        {
          if (!check_form_key('acp_points'))
          {
           trigger_error($user->lang['FORM_INVALID'] . adm_back_link($this->u_action), E_USER_WARNING);
          }

          // Values for phpbb_config
          $points_name        = utf8_normalize_nfc(request_var('points_name', '', true));
          $points_enable        = request_var('points_enable', 0);

          // Values for phpbb_points_config
          $points_disablemsg      = utf8_normalize_nfc(request_var('points_disablemsg', '', true));
          $transfer_enable      = request_var('transfer_enable', 0);
          $transfer_pm_enable     = request_var('transfer_pm_enable', 0);
          $comments_enable      = request_var('comments_enable', 0);
          $stats_enable       = request_var('stats_enable', 0);
          $logs_enable        = request_var('logs_enable', 0);
          $images_topic_enable    = request_var('images_topic_enable', 0);
          $images_memberlist_enable   = request_var('images_memberlist_enable', 0);
          $gallery_deny_view      = request_var('gallery_deny_view', 0);

          // Values for phpbb_points_values
          $sql_ary = array (
            'number_show_per_page'      => request_var('number_show_per_page', 0),
            'number_show_top_points'    => request_var('number_show_top_points', 0),
            'points_per_attach'       => round(request_var('points_per_attach', 0.00), 2),
            'points_per_attach_file'    => round(request_var('points_per_attach_file', 0.00), 2),
            'points_per_poll'       => round(request_var('points_per_poll', 0.00), 2),
            'points_per_poll_option'    => round(request_var('points_per_poll_option', 0.00), 2),
            'points_per_topic_word'     => round(request_var('points_per_topic_word', 0.00), 2),
            'points_per_topic_character'  => round(request_var('points_per_topic_character', 0.00), 2),
            'points_per_post_word'      => round(request_var('points_per_post_word', 0.00), 2),
            'points_per_post_character'   => round(request_var('points_per_post_character', 0.00), 2),
            'reg_points_bonus'        => round(request_var('reg_points_bonus', 0.00), 2),
            'points_per_warn'       => round(request_var('points_per_warn', 0.00), 2),
            'gallery_upload'        => round(request_var('gallery_upload', 0.00), 2),
            'gallery_remove'        => round(request_var('gallery_remove', 0.00), 2),
            'gallery_view'          => round(request_var('gallery_view', 0.00), 2),
          );

          // Check if number_show_per_page is at least 5
          $per_page_check = request_var('number_show_per_page', 0);

          if ($per_page_check < 5)
          {
            trigger_error($user->lang['POINTS_SHOW_PER_PAGE_ERROR'] . adm_back_link($this->u_action), E_USER_WARNING);
          }

          // Update values in phpbb_config
          if ($points_name != $config['points_name'])
          {
            set_config('points_name', $points_name);
          }

          if ($points_enable != $config['points_enable'])
          {
            set_config('points_enable', $points_enable);
          }

          // Update values in phpbb_points_config
          if ($points_disablemsg != $points_config['points_disablemsg'])
          {
            set_points_config('points_disablemsg', $points_disablemsg);
          }

          if ($transfer_enable != $points_config['transfer_enable'])
          {
            set_points_config('transfer_enable', $transfer_enable);
          }

          if ($transfer_pm_enable != $points_config['transfer_pm_enable'])
          {
            set_points_config('transfer_pm_enable', $transfer_pm_enable);
          }

          if ($comments_enable != $points_config['comments_enable'])
          {
            set_points_config('comments_enable', $comments_enable);
          }

          if ($stats_enable != $points_config['stats_enable'])
          {
            set_points_config('stats_enable', $stats_enable);
          }

          if ($logs_enable != $points_config['logs_enable'])
          {
            set_points_config('logs_enable', $logs_enable);
          }

          if ($images_topic_enable != $points_config['images_topic_enable'])
          {
            set_points_config('images_topic_enable', $images_topic_enable);
          }

          if ($images_memberlist_enable != $points_config['images_memberlist_enable'])
          {
            set_points_config('images_memberlist_enable', $images_memberlist_enable);
          }

          if ($gallery_deny_view != $points_config['gallery_deny_view'])
          {
            set_points_config('gallery_deny_view', $gallery_deny_view);
          }

          // Update values in phpbb_points_values
          $sql = 'UPDATE ' . POINTS_VALUES_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $sql_ary);
          $db->sql_query($sql);

          // Add logs
          add_log('admin', 'LOG_MOD_POINTS_SETTINGS');
          trigger_error($user->lang['POINTS_CONFIG_SUCCESS'] . adm_back_link($this->u_action));
        }
        else
        {



          $template->assign_vars(array(
            'POINTS_DISABLEMSG'   => $points_config['points_disablemsg'],
            'TRANSFER_ENABLE'   => $points_config['transfer_enable'],
            'TRANSFER_PM_ENABLE'  => $points_config['transfer_pm_enable'],
            'COMMENTS_ENABLE'   => $points_config['comments_enable'],
            'STATS_ENABLE'    => $points_config['stats_enable'],
            'LOGS_ENABLE'     => $points_config['logs_enable'],
            'IMAGES_TOPIC_ENABLE'   => $points_config['images_topic_enable'],
            'IMAGES_MEMBERLIST_ENABLE'    => $points_config['images_memberlist_enable'],
            'GALLERY_DENY_VIEW'   => $points_values['gallery_deny_view'],
            'POINTS_NAME'         => $config['points_name'],
            'POINTS_PER_ATTACH'       => $points_values['points_per_attach'],
            'POINTS_PER_ATTACH_FILE'    => $points_values['points_per_attach_file'],
            'POINTS_PER_POLL'       => $points_values['points_per_poll'],
            'POINTS_PER_POLL_OPTION'    => $points_values['points_per_poll_option'],
            'POINTS_PER_TOPIC_WORD'     => $points_values['points_per_topic_word'],
            'POINTS_PER_TOPIC_CHARACTER'  => $points_values['points_per_topic_character'],
            'POINTS_PER_POST_WORD'      => $points_values['points_per_post_word'],
            'POINTS_PER_POST_CHARACTER'   => $points_values['points_per_post_character'],
            'POINTS_PER_WARN'       => $points_values['points_per_warn'],
            'REG_POINTS_BONUS'        => $points_values['reg_points_bonus'],

            'NUMBER_SHOW_TOP_POINTS'    => $points_values['number_show_top_points'],
            'NUMBER_SHOW_PER_PAGE'      => $points_values['number_show_per_page'],

            'POINTS_ENABLE'         => ($config['points_enable']) ? true : false,

            'GALLERY_UPLOAD'        => $points_values['gallery_upload'],
            'GALLERY_REMOVE'        => $points_values['gallery_remove'],
            'GALLERY_VIEW'          => $points_values['gallery_view'],
          ));
        }

        // Delete all userlogs
        $reset_pointslogs = (isset($_POST['action_points_logs'])) ? true : false;

        if ($reset_pointslogs)
        {
          if (confirm_box(true))
          {
            define('POINTS_LOG_TABLE', $table_prefix . 'points_log');
            if (!$auth->acl_get('a_points'))
            {
              trigger_error($user->lang['NO_AUTH_OPERATION'] . adm_back_link($this->u_action), E_USER_WARNING);
            }

            switch ($db->sql_layer)
            {
              case 'sqlite':
              case 'firebird':
                $db->sql_query('DELETE FROM ' . POINTS_LOG_TABLE);
              break;

              default:
                $db->sql_query('TRUNCATE TABLE ' . POINTS_LOG_TABLE);
              break;
            }

            add_log('admin', 'LOG_RESYNC_POINTSLOGSCOUNTS');
            trigger_error($user->lang['LOG_RESYNC_POINTSLOGSCOUNTS'] . adm_back_link($this->u_action));
          }
          // Create a confirmbox with yes and no.
          else
          {
            $s_hidden_fields = build_hidden_fields(array(
              'action_points_logs'    => true,
              )
            );

            // Display mode
            confirm_box(false, $user->lang['RESYNC_POINTSLOGS_CONFIRM'], $s_hidden_fields);
          }
        }

        // Delete all userpoints
        $reset_points_user = (isset($_POST['action_points'])) ? true : false;

        if ($reset_points_user)
        {
          if (confirm_box(true))
          {

            if (!$auth->acl_get('a_points'))
            {
              trigger_error($user->lang['NO_AUTH_OPERATION'] . adm_back_link($this->u_action), E_USER_WARNING);
            }

            $db->sql_query('UPDATE ' . USERS_TABLE . ' SET user_points = 0');

            add_log('admin', 'LOG_RESYNC_POINTSCOUNTS');
            trigger_error($user->lang['LOG_RESYNC_POINTSCOUNTS'] . adm_back_link($this->u_action));
          }
          // Create a confirmbox with yes and no.
          else
          {
            $s_hidden_fields = build_hidden_fields(array(
              'action_points'   => true,
              )
            );

            // Display mode
            confirm_box(false, $user->lang['RESYNC_POINTS_CONFIRM'], $s_hidden_fields);
          }
        }

        // Transfer or set points for groups
        $group_transfer     = (isset($_POST['group_transfer'])) ? true : false;
        $group_transfer_points  = request_var('group_transfer_points',  0.00);
        $func         = request_var('func', '');
        $group_id       = request_var('group_id', 0);
        $pm_subject       = utf8_normalize_nfc(request_var('pm_subject', '', true));
        $pm_text        = utf8_normalize_nfc(request_var('pm_text', '', true));

        $sql_array = array(
          'SELECT'  => 'group_id, group_name, group_type',
          'FROM'    => array(
            GROUPS_TABLE => 'g',
          ),
          'ORDER_BY'  => 'group_name',
        );
        $sql = $db->sql_build_query('SELECT', $sql_array);
        $result = $db->sql_query($sql);
        $total_groups = $db->sql_affectedrows($result);
        $db->sql_freeresult($result);

        $template->assign_vars(array(
          'U_SMILIES' => append_sid("{$phpbb_root_path}posting.$phpEx", 'mode=smilies'),
          'S_GROUP_OPTIONS' => group_select_options($total_groups),
          'U_ACTION'      => $this->u_action)
        );

        // Update the points
        if ($group_transfer)
        {
          if (!check_form_key('acp_points'))
          {
           // trigger_error($user->lang['FORM_INVALID'] . adm_back_link($this->u_action), E_USER_WARNING);
          }

          $sql_array = array(
            'SELECT'  => 'group_type, group_name',
            'FROM'    => array(
              GROUPS_TABLE => 'g',
            ),
            'WHERE' => 'group_id = ' . (int) $group_id,
          );
          $sql = $db->sql_build_query('SELECT', $sql_array);
          $result = $db->sql_query($sql);
          $row = $db->sql_fetchrow($result);
          $db->sql_freeresult($result);

          $group_name = ($row['group_type'] == GROUP_SPECIAL) ? $user->lang['G_' . $row['group_name']] : $row['group_name'];

          // Check if we try transfering to BOTS or GUESTS
          if ($row['group_name'] == 'BOTS' || $row['group_name'] == 'GUESTS')
          {
            trigger_error($user->lang['POINTS_GROUP_TRANSFER_SEL_ERROR'] . adm_back_link($this->u_action), E_USER_WARNING);
          }

          $sql_array = array(
            'SELECT'  => 'user_id',
            'FROM'    => array(
              USER_GROUP_TABLE => 'g',
            ),
            'WHERE' => 'user_pending <> ' . TRUE . '
              AND group_id = ' . (int) $group_id,
          );
          $sql = $db->sql_build_query('SELECT', $sql_array);
          $result = $db->sql_query($sql);

          $user_ids = array();

          while ($row = $db->sql_fetchrow($result))
          {
            $user_ids[] = $row['user_id'];
          }

          $db->sql_freeresult($result);

          if (sizeof($user_ids))
          {

            if ($func == 'add')
            {
              $sql = "UPDATE " . USERS_TABLE . "
                SET user_points = user_points + $group_transfer_points
                WHERE " . $db->sql_in_set('user_id', $user_ids);
              add_log('admin', 'LOG_GROUP_TRANSFER_ADD');
            }

            if ($func == 'substract')
            {
              $sql = "UPDATE " . USERS_TABLE . "
                SET user_points = user_points - $group_transfer_points
                WHERE " . $db->sql_in_set('user_id', $user_ids);
              add_log('admin', 'LOG_GROUP_TRANSFER_ADD');
            }

            if ($func == 'set')
            {
              $sql = "UPDATE " . USERS_TABLE . "
                SET user_points = $group_transfer_points
                WHERE " . $db->sql_in_set('user_id', $user_ids);
              add_log('admin', 'LOG_GROUP_TRANSFER_SET');
            }

            $result = $db->sql_query($sql);

            // Send PM, if pm subject and pm comment is entered
            if ($pm_subject != '' || $pm_text != '')
            {
              if ($pm_subject == '' || $pm_text == '')
              {
                trigger_error($user->lang['POINTS_GROUP_TRANSFER_PM_ERROR'] . adm_back_link($this->u_action), E_USER_WARNING);
              }
              else
              {
                $sql_array = array(
                  'SELECT'  => 'user_id, group_id',
                  'FROM'    => array(
                    USER_GROUP_TABLE => 'g',
                  ),
                  'WHERE' => 'user_pending <> ' . TRUE . '
                    AND group_id = ' . (int) $group_id,
                );
                $sql = $db->sql_build_query('SELECT', $sql_array);
                $result = $db->sql_query($sql);
                $group_to = array();

                while ($row = $db->sql_fetchrow($result))
                {
                   $group_to[$row['group_id']] = 'to';
                }

                $poll = $uid = $bitfield = $options = '';
                generate_text_for_storage($pm_subject, $uid, $bitfield, $options, false, false, false);
                generate_text_for_storage($pm_text, $uid, $bitfield, $options, true, true, true);

                include($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);

                $pm_data = array(
                  'address_list'    => array ('g' => $group_to),
                  'from_user_id'    => $user->data['user_id'],
                  'from_username'   => 'Points Transfer',
                  'icon_id'     => 0,
                  'from_user_ip'    => $user->data['user_ip'],

                  'enable_bbcode'   => true,
                  'enable_smilies'  => true,
                  'enable_urls'   => true,
                  'enable_sig'    => true,

                  'message'     => $pm_text,
                  'bbcode_bitfield' => $bitfield,
                  'bbcode_uid'    => $uid,
                );
                submit_pm('post', $pm_subject, $pm_data, false);

                $db->sql_freeresult($result);
              }
              $message = $user->lang['POINTS_GROUP_TRANSFER_PM_SUCCESS'] . adm_back_link($this->u_action);
              trigger_error($message);
            }
            else
            {
              $message = $user->lang['POINTS_GROUP_TRANSFER_SUCCESS'] . adm_back_link($this->u_action);
              trigger_error($message);
            }
          }
        }

        // phpBB Gallery integration
        if (isset($config['gallery_total_images']))
        {
          $template->assign_vars(array(
            'S_GALLERY_EXIST' => true,
            'POINTS_NAME'   => $config['points_name'],
          ));
        }

        $template->assign_vars(array(
          'S_POINTS_MAIN'     => true,
          'S_POINTS_ACTIVATED'  => ($config['points_enable']) ? true : false,
          'U_ACTION'        => $this->u_action)
        );

      break;

      case 'lottery':
        $this->page_title = 'ACP_POINTS_LOTTERY_TITLE';
        $this->tpl_name = 'acp_points_lottery';

        $action = request_var('action', '');
        $submit = request_var('submit', '');

        $lottery_data = $errors = array();

        if ($submit)
        {
          if (!check_form_key('acp_points'))
          {
            trigger_error($user->lang['FORM_INVALID'] . adm_back_link($this->u_action), E_USER_WARNING);
          }

          // Get current lottery_base_amount
          $current_lottery_jackpot = $points_values['lottery_jackpot'];
          $current_lottery_base_amount = $points_values['lottery_base_amount'];

          // Values for phpbb_points_config
          $lottery_enable         = request_var('lottery_enable', 0);
          $lottery_multi_ticket_enable  = request_var('lottery_multi_ticket_enable', 0);
          $display_lottery_stats      = request_var('display_lottery_stats', 0);

          // Values for phpbb_points_values
          $lottery_base_amount      = round(request_var('lottery_base_amount', 0.00), 2);
          $lottery_draw_period      = request_var('lottery_draw_period', 0) * 3600;
          $lottery_ticket_cost      = round(request_var('lottery_ticket_cost', 0.00), 2);
          $lottery_name         = utf8_normalize_nfc(request_var('lottery_name', '', true));
          $lottery_chance         = round(request_var('lottery_chance', 0.00), 2);
          $lottery_max_tickets      = round(request_var('lottery_max_tickets', 0.00), 2);
          $lottery_pm_from        = request_var('lottery_pm_from', 0);
          $lottery_current_jackpot    = round(request_var('lottery_current_jackpot', 0.00), 2);
          $lottery_calc         = request_var('lottery_last_draw_time', 0);

          // Calculate next lottery time
          $lottery_last_draw_time     = $lottery_calc - $lottery_draw_period;

          // Check entered lottery chance - has to be max 100
          if ($lottery_chance > 100)
          {
            trigger_error($user->lang['LOTTERY_CHANCE_ERROR'] . adm_back_link($this->u_action), E_USER_WARNING);
          }

          // If base amount increases, increase jackpot
          if ($lottery_base_amount > $current_lottery_base_amount)
          {
            $this->set_points_values('lottery_jackpot', ($current_lottery_jackpot +  $lottery_base_amount -  $current_lottery_base_amount));
          }

          // Update values in phpbb_points_config
          if ($lottery_enable != $points_config['lottery_enable'])
          {
            set_points_config('lottery_enable', $lottery_enable);
          }

          if ($lottery_multi_ticket_enable != $points_config['lottery_multi_ticket_enable'])
          {
            set_points_config('lottery_multi_ticket_enable', $lottery_multi_ticket_enable);
          }

          if ($lottery_last_draw_time > 0)
          {
            $this->set_points_values('lottery_last_draw_time', $lottery_last_draw_time);
          }

          if ($display_lottery_stats != $points_config['display_lottery_stats'])
          {
            set_points_config('display_lottery_stats', $display_lottery_stats);
          }

          // Update values in phpbb_points_values
          $this->set_points_values('lottery_base_amount', $lottery_base_amount);

          // Check if 0 is entered. Must be > 0
          if ($lottery_draw_period < 0)
          {
            trigger_error($user->lang['LOTTERY_DRAW_PERIOD_SHORT'] . adm_back_link($this->u_action), E_USER_WARNING);
          }
          else
          {
            $this->set_points_values('lottery_draw_period', $lottery_draw_period);
          }

          $this->set_points_values('lottery_ticket_cost', $lottery_ticket_cost);
          $this->set_points_values('lottery_name', $lottery_name);
          $this->set_points_values('lottery_chance', $lottery_chance);
          $this->set_points_values('lottery_max_tickets', $lottery_max_tickets);
          $this->set_points_values('lottery_jackpot', $lottery_current_jackpot);

          // Check, if the entered user_id really exists
          $sql_array = array(
            'SELECT'  => 'user_id',
            'FROM'    => array(
              USERS_TABLE => 'u',
            ),
            'WHERE'   => 'user_id = ' . (int) $lottery_pm_from,
          );
          $sql = $db->sql_build_query('SELECT', $sql_array);
          $result = $db->sql_query($sql);
          $id_exist = $db->sql_fetchfield('user_id');
          $db->sql_freeresult($result);

          if ($lottery_pm_from == 0)
          {
            $this->set_points_values('lottery_pm_from', $lottery_pm_from);
          }
          else if (empty($id_exist))
          {
            trigger_error($user->lang['NO_USER'] . adm_back_link($this->u_action), E_USER_WARNING);
          }
          else
          {
            $this->set_points_values('lottery_pm_from', $lottery_pm_from);
          }

          // Set last draw time to current time, if draw period activated
          if ($points_values['lottery_last_draw_time'] == 0 && $points_values['lottery_draw_period'] != 0)
          {
            $this->set_points_values('lottery_last_draw_time', time());
          }

          // Set last draw time to 0, if draw period deactivated
          if ($points_values['lottery_draw_period'] == 0)
          {
            $this->set_points_values('lottery_last_draw_time', 0);
          }

          // Add logs
          add_log('admin', 'LOG_MOD_POINTS_LOTTERY');
          trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
        }

        // Delete lottery history
        $reset_lottery_history = (isset($_POST['action_lottery_history'])) ? true : false;

        if ($reset_lottery_history)
        {
          if (confirm_box(true))
          {
            if (!$auth->acl_get('a_points'))
            {
              trigger_error($user->lang['NO_AUTH_OPERATION'] . adm_back_link($this->u_action), E_USER_WARNING);
            }

            switch ($db->sql_layer)
            {
              case 'sqlite':
              case 'firebird':
                $db->sql_query('DELETE FROM ' . POINTS_LOTTERY_HISTORY_TABLE);
              break;

              default:
                $db->sql_query('TRUNCATE TABLE ' . POINTS_LOTTERY_HISTORY_TABLE);
              break;
            }

            add_log('admin', 'LOG_RESYNC_LOTTERY_HISTORY');
            trigger_error($user->lang['LOG_RESYNC_LOTTERY_HISTORY'] . adm_back_link($this->u_action));
          }
          // Create a confirmbox with yes and no.
          else
          {
            $s_hidden_fields = build_hidden_fields(array(
              'action_lottery_history'    => true,
              )
            );

            // Display mode
            confirm_box(false, $user->lang['RESYNC_LOTTERY_HISTORY_CONFIRM'], $s_hidden_fields);
          }
        }

        $template->assign_vars(array(
          'LOTTERY_BASE_AMOUNT'     => $points_values['lottery_base_amount'],
          'LOTTERY_CURRENT_JACKPOT'   => $points_values['lottery_jackpot'],
          'LOTTERY_DRAW_PERIOD'     => ($points_values['lottery_draw_period'] == 0) ? $points_values['lottery_draw_period'] : $points_values['lottery_draw_period'] / 3600,
          'LOTTERY_NEXT_DRAWING'      => $user->format_date($points_values['lottery_last_draw_time'] + $points_values['lottery_draw_period'], false, true),
          'LOTTERY_TICKET_COST'     => $points_values['lottery_ticket_cost'],
          'LOTTERY_CASH_NAME'       => $config['points_name'],
          'LOTTERY_NAME'          => $points_values['lottery_name'],
          'LOTTERY_CHANCE'        => $points_values['lottery_chance'],
          'LOTTERY_MAX_TICKETS'     => $points_values['lottery_max_tickets'],
          'LOTTERY_PM_FROM'       => $points_values['lottery_pm_from'],

          'S_LOTTERY_ENABLE'        => ($points_config['lottery_enable']) ? true : false,
          'S_LOTTERY_MULTI_TICKET_ENABLE' => ($points_config['lottery_multi_ticket_enable']) ? true : false,
          'S_DISPLAY_LOTTERY_STATS'   => ($points_config['display_lottery_stats']) ? true : false,

          'S_LOTTERY'     => true,
          'U_ACTION'      => $this->u_action)
        );

      break;

      case 'bank':
        $this->page_title = 'ACP_POINTS_BANK_TITLE';
        $this->tpl_name = 'acp_points_bank';

        $action = request_var('action', '');
        $submit = request_var('submit', '');

        $bank_data = $errors = array();

        if ($submit)
        {
          if (!check_form_key('acp_points'))
          {
            trigger_error($user->lang['FORM_INVALID'] . adm_back_link($this->u_action), E_USER_WARNING);
          }

          // Values for phpbb_points_config
          $bank_enable    = request_var('bank_enable', 0);

          // Values for phpbb_points_values
          $bank_interest    = round(request_var('bank_interest', 0.00), 2);
          $bank_fees      = round(request_var('bank_fees', 0.00), 2);
          $bank_pay_period  = round(request_var('bank_pay_period', 0.00), 2) * 86400;
          $bank_min_withdraw  = round(request_var('bank_min_withdraw', 0.00), 2);
          $bank_min_deposit = round(request_var('bank_min_deposit', 0.00), 2);
          $bank_interestcut = round(request_var('bank_interestcut', 0.00), 2);
          $bank_cost      = round(request_var('bank_cost', 0.00), 2);
          $bank_name      = utf8_normalize_nfc(request_var('bank_name', '', true));

          // Check entered bank interesst - has to be max 100 and cannot be below 0
          if ($bank_interest > 100 || $bank_interest < 0)
          {
            trigger_error($user->lang['BANK_INTEREST_ERROR'] . adm_back_link($this->u_action), E_USER_WARNING);
          }

          // Check entered bank fees - has to be max 100 and cannot be below 0
          if ($bank_fees > 100 || $bank_fees < 0)
          {
            trigger_error($user->lang['BANK_FEES_ERROR'] . adm_back_link($this->u_action), E_USER_WARNING);
          }

          // Check the bank period
          if ($bank_pay_period < 0)
          {
            trigger_error($user->lang['BANK_PAY_ERROR'] . adm_back_link($this->u_action), E_USER_WARNING);
          }

          // Update values in phpbb_points_config
          if ($bank_enable != $points_config['bank_enable'])
          {
            set_points_config('bank_enable', $bank_enable);
          }

          // Update values in phpbb_points_values
          $this->set_points_values('bank_interest', $bank_interest);
          $this->set_points_values('bank_fees', $bank_fees);
          $this->set_points_values('bank_pay_period', $bank_pay_period);
          $this->set_points_values('bank_min_withdraw', $bank_min_withdraw);
          $this->set_points_values('bank_min_deposit', $bank_min_deposit);
          $this->set_points_values('bank_interestcut', $bank_interestcut);
          $this->set_points_values('bank_cost', $bank_cost);
          $this->set_points_values('bank_name', $bank_name);

          // Add logs
          add_log('admin', 'LOG_MOD_POINTS_BANK');

          trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
        }

        // Delete all bank accounts
        $delete_bank_accounts = (isset($_POST['action_bank_points'])) ? true : false;

        if ($delete_bank_accounts)
        {
          define('POINTS_BANK_TABLE', $table_prefix . 'points_bank');
          if (confirm_box(true))
          {

            if (!$auth->acl_get('a_points'))
            {
              trigger_error($user->lang['NO_AUTH_OPERATION'] . adm_back_link($this->u_action), E_USER_WARNING);
            }

            switch ($db->sql_layer)
            {
              case 'sqlite':
              case 'firebird':
                $db->sql_query('DELETE FROM ' . POINTS_BANK_TABLE);
              break;

              default:
                $db->sql_query('TRUNCATE TABLE ' . POINTS_BANK_TABLE);
              break;
            }

            add_log('admin', 'LOG_RESYNC_BANK_ACCOUNTS');
            trigger_error($user->lang['LOG_RESYNC_BANK_ACCOUNTS'] . adm_back_link($this->u_action));
          }
          // Create a confirmbox with yes and no.
          else
          {
            $s_hidden_fields = build_hidden_fields(array(
              'action_bank_points'    => true,
              )
            );

            // Display mode
            confirm_box(false, $user->lang['RESYNC_BANK_ACCOUNTS_CONFIRM'], $s_hidden_fields);
          }
        }

        $template->assign_vars(array(
          // Convert to days
          'BANK_PAY_PERIOD' => ($points_values['bank_pay_period'] == 0) ? $points_values['bank_pay_period'] : $points_values['bank_pay_period'] / 86400,
          'BANK_POINTS_NAME'  => $config['points_name'],
          'BANK_FEES'     => $points_values['bank_fees'],
          'BANK_INTEREST'   => $points_values['bank_interest'],
          'BANK_MIN_WITHDRAW' => $points_values['bank_min_withdraw'],
          'BANK_MIN_DEPOSIT'  => $points_values['bank_min_deposit'],
          'BANK_INTERESTCUT'  => $points_values['bank_interestcut'],
          'BANK_COST'     => $points_values['bank_cost'],
          'BANK_NAME'     => $points_values['bank_name'],

          'S_BANK_ENABLE'   => ($points_config['bank_enable']) ? true : false,

          'S_POINTS_BANK'   => true,
          'U_ACTION'      => $this->u_action)
        );

      break;

      case 'robbery':
        $this->page_title = 'ACP_POINTS_ROBBERY_TITLE';
        $this->tpl_name = 'acp_points_robbery';

        $action = request_var('action', '');
        $submit = request_var('submit', '');

        $robbery_data = $errors = array();

        if ($submit)
        {
          if (!check_form_key('acp_points'))
          {
            trigger_error($user->lang['FORM_INVALID'] . adm_back_link($this->u_action), E_USER_WARNING);
          }

          // Values for phpbb_points_config
          $robbery_enable = request_var('robbery_enable', 0);
          $robbery_sendpm = request_var('robbery_sendpm', 0);
          $robbery_usage  = request_var('robbery_usage', 0);

          // Values for phpbb_points_values
          $robbery_chance   = round(request_var('robbery_chance', 0.00), 2);
          $robbery_loose    = round(request_var('robbery_loose', 0.00), 2);
          $robbery_max_rob  = round(request_var('robbery_max_rob', 0.00), 2);

          // Check, if entered robbery chance is 0 or below
          if ($robbery_chance <= 0)
          {
            trigger_error($user->lang['ROBBERY_CHANCE_MINIMUM'] . adm_back_link($this->u_action), E_USER_WARNING);
          }

          // Check entered robbery chance - has to be max 100
          if ($robbery_chance > 100)
          {
            trigger_error($user->lang['ROBBERY_CHANCE_ERROR'] . adm_back_link($this->u_action), E_USER_WARNING);
          }

          // Check, if entered robbery loose is 0 or below
          if ($robbery_loose <= 0)
          {
            trigger_error($user->lang['ROBBERY_LOOSE_MINIMUM'] . adm_back_link($this->u_action), E_USER_WARNING);
          }

          // Check entered robbery loose - has to be max 100
          if ($robbery_loose > 100)
          {
            trigger_error($user->lang['ROBBERY_LOOSE_ERROR'] . adm_back_link($this->u_action), E_USER_WARNING);
          }

          // Check, if entered robbery is 0 or below
          if ($robbery_max_rob <= 0)
          {
            trigger_error($user->lang['ROBBERY_MAX_ROB_MINIMUM'] . adm_back_link($this->u_action), E_USER_WARNING);
          }

          // Check entered robbery max rob value - has to be max 100
          if ($robbery_max_rob > 100)
          {
            trigger_error($user->lang['ROBBERY_MAX_ROB_ERROR'] . adm_back_link($this->u_action), E_USER_WARNING);
          }

          // Update values in phpbb_points_config
          if ($robbery_enable != $points_config['robbery_enable'])
          {
            set_points_config('robbery_enable', $robbery_enable);
          }

          if ($robbery_sendpm != $points_config['robbery_sendpm'])
          {
            set_points_config('robbery_sendpm', $robbery_sendpm);
          }

          if ($robbery_usage != $points_config['robbery_usage'])
          {
            set_points_config('robbery_usage', $robbery_usage);
          }

          // Update values in phpbb_points_values
          $this->set_points_values('robbery_chance', $robbery_chance);
          $this->set_points_values('robbery_loose', $robbery_loose);
          $this->set_points_values('robbery_max_rob', $robbery_max_rob);

          // Add logs
          add_log('admin', 'LOG_MOD_POINTS_ROBBERY');
          trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
        }

        $template->assign_vars(array(
          'ROBBERY_CHANCE'  => $points_values['robbery_chance'],
          'ROBBERY_LOOSE'   => $points_values['robbery_loose'],
          'ROBBERY_MAX_ROB' => $points_values['robbery_max_rob'],
          'ROBBERY_USAGE'  => ($points_config['robbery_usage']) ? true : false,
          'S_ROBBERY_ENABLE'  => ($points_config['robbery_enable']) ? true : false,
          'S_ROBBERY_SENDPM'  => ($points_config['robbery_sendpm']) ? true : false,

          'S_ROBBERY'     => true,
          'U_ACTION'      => $this->u_action)
        );

      break;

      case 'userguide':
        $this->page_title = 'ACP_POINTS_USERGUIDE_TITLE';
        $this->tpl_name = 'acp_points_userguide';

        $template->assign_vars(array(
          'S_IN_POINTS_USERGUIDE'   => true,
          'L_BACK_TO_TOP'       => $user->lang['BACK_TO_TOP'],
          'ICON_BACK_TO_TOP'      => '<img src="' . $phpbb_admin_path . 'images/icon_up.gif" style="vertical-align: middle;" alt="' . $user->lang['BACK_TO_TOP'] . '" title="' . $user->lang['BACK_TO_TOP'] . '" />',
        ));

        $user->add_lang_ext('points', false, true);

        // Pull the array data from the lang pack
        foreach ($user->help as $help_ary)
        {
          if ($help_ary[0] == '--')
          {
            $template->assign_block_vars('userguide_block', array(
              'BLOCK_TITLE'   => $help_ary[1])
            );

            continue;
          }

          $template->assign_block_vars('userguide_block.userguide_row', array(
            'USERGUIDE_QUESTION'    => $help_ary[0],
            'USERGUIDE_ANSWER'      => $help_ary[1])
          );
        }
      break;

      case 'forumpoints':
              $this->page_title = 'ACP_POINTS_FORUM_TITLE';
        $this->tpl_name = 'acp_points_forum';

        $action = request_var('action', '');
        $submit = request_var('submit', '');

        $forum_data = $errors = array();

        $extension_points_list  = request_var('points_extension', array(0));
        $extension_points_costs = request_var('points_extension_costs', array(0.00));
        $set_point_switches = request_var('action_point_switches', '');
        $set_point_values = request_var('action_point_values', '');

        // Update forum points switches
        if ($set_point_switches)
        {
          if (!check_form_key('acp_points'))
          {
            trigger_error($user->lang['FORM_INVALID'] . adm_back_link($this->u_action), E_USER_WARNING);
          }

          // Get config values
          $pertopic_enable  = request_var('pertopic_enable', 0);
          $perpost_enable   = request_var('perpost_enable', 0);
          $peredit_enable   = request_var('peredit_enable', 0);

          // Update config values
          if ($pertopic_enable != $points_config['pertopic_enable'])
          {
            set_points_config('pertopic_enable', $pertopic_enable);
          }

          if ($perpost_enable != $points_config['perpost_enable'])
          {
            set_points_config('perpost_enable', $perpost_enable);
          }

          if ($peredit_enable != $points_config['peredit_enable'])
          {
            set_points_config('peredit_enable', $peredit_enable);
          }
          // Add logs
          add_log('admin', 'LOG_MOD_POINTS_FORUM_SWITCH');

          trigger_error($user->lang['FORUM_POINT_SETTINGS_UPDATED'] . adm_back_link($this->u_action));
        }

        // Update forum points values
        if ($set_point_values)
        {
          if (confirm_box(true))
          {
            $forum_topic  = round(request_var('forum_topic', 0.00), 2);
            $forum_post   = round(request_var('forum_post', 0.00), 2);
            $forum_edit   = round(request_var('forum_edit', 0.00), 2);
            $forum_costs  = request_var('forum_costs', 1);

            // Update values in phpbb_points_values
            $this->set_points_values('forum_topic', $forum_topic);
            $this->set_points_values('forum_post', $forum_post);
            $this->set_points_values('forum_edit', $forum_edit);

            // Update all forum points and attachment costs
            $data = array(
              'forum_pertopic'  => $forum_topic,
              'forum_perpost'   => $forum_post,
              'forum_peredit'   => $forum_edit,
              'forum_costs'   => $forum_costs,
            );

            $sql = 'UPDATE ' . FORUMS_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $data);
            $db->sql_query($sql);
            // Add logs
            add_log('admin', 'LOG_MOD_POINTS_FORUM');

            trigger_error($user->lang['FORUM_POINT_SETTINGS_UPDATED'] . adm_back_link($this->u_action));
          }
          else
          {
            $s_hidden_fields = build_hidden_fields(array(
              'forum_topic'     => request_var('forum_topic', 0.00),
              'forum_post'      => request_var('forum_post', 0.00),
              'forum_edit'      => request_var('forum_edit', 0.00),
              'forum_costs'     => request_var('forum_costs', 1),
              'mode'          => $mode,
              'action'        => $action,
              'action_point_values' => true,
              )
            );
            confirm_box(false, 'FORUM_POINT_UPDATE', $s_hidden_fields);
          }
        }
        $add_extension_points   = request_var('points_extension', 0);
        $add_extension_points_costs = request_var('points_extension_costs', 0.00);

        $template->assign_vars(array(
          'FORUM_POINTS_NAME' => $config['points_name'],
          'FORUM_TOPIC'   => $points_values['forum_topic'],
          'FORUM_POST'    => $points_values['forum_post'],
          'FORUM_EDIT'    => $points_values['forum_edit'],
          'PERTOPIC_ENABLE'   => $points_config['pertopic_enable'],
          'PERPOST_ENABLE'    => $points_config['perpost_enable'],
          'PEREDIT_ENABLE'    => $points_config['peredit_enable'],
          'S_FORUMPOINTS'   => true,
          'FORUM_COSTS'   => false,
          'ADD_EXTENSION_POINTS'      => (isset($add_extension_points)) ? $add_extension_points : '',
          'ADD_EXTENSION_POINTS_COSTS'  => (isset($add_extension_points_costs)) ? $add_extension_points_costs : '',
          'POINTS_NAME'         => $config['points_name'],
          'U_ACTION'      => $this->u_action)
        );
        $sql = 'SELECT points_extension, points_extension_costs 
              FROM ' . EXTENSIONS_TABLE;
             $result = $db->sql_query($sql);
              while ($row = $db->sql_fetchrow($result)){
                 if ($set_point_values)
                  {
                    if (confirm_box(true))
                    {
                      $extension_points_list  = request_var('points_extension', array(0));
                     $extension_points_costs = request_var('points_extension_costs', 0);
                      $extensions_points = array();
                      for ($i = 0, $size = sizeof($extension_points_list); $i < $size; $i++)
                      {
                        $extensions_points[$extension_points_list[$i]] = true;
                      }
                      print_r($extension_points_cost);
                     $new_extension_points = (isset($extensions_points[$row['extension_id']]) ? 1 : 0);

                    if ($row['points_extension'] <> $new_extension_points)
                    {
                      $sql = 'UPDATE ' . EXTENSIONS_TABLE . '
                       SET points_extension = ' . (int) $new_extension_points  . '
                       WHERE extension_id = ' . $row['extension_id'];
                      $db->sql_query($sql);
                       add_log('admin', 'LOG_ATTACH_POINTS_EXT_UPDATE', $row['extension']);
                    }

                    if ($row['points_extension_costs'] <> $extension_points_costs[$row['extension_id']])
                    {
                      $sql = 'UPDATE ' . EXTENSIONS_TABLE . '
                        SET points_extension_costs = ' . (float) $extension_points_costs[$row['extension_id']] . '
                       WHERE extension_id = ' . $row['extension_id'];
                      $db->sql_query($sql);
                      add_log('admin', 'LOG_ATTACH_POINTS_EXT_UPDATE', $row['extension']);
                    }
                  }
                }
                $template->assign_vars(array(
                  'EXTENSION_POINTS'      => $row['points_extension'],
                  'EXTENSION_POINTS_COSTS'  => $row['points_extension_costs']
                  ));
              }

        break;
      	}

		$template->assign_vars(array(
			'U_ACTION'				=> $this->u_action,
			'VIPAKA_POINTS_ENABLE'		=> $config['points_enable'],
		));
	}
  function set_points_values($field, $value)
  {
    global $db;

    $sql = "UPDATE " . POINTS_VALUES_TABLE . "
      SET " . $db->sql_escape($field) . " = '" . $db->sql_escape($value) . "'";
    $db->sql_query($sql);

    return;
  }
}
