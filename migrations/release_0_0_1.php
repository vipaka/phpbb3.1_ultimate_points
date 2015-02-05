<?php
/**
*
* @package phpBB Extension - Vipaka Ultimate points* @copyright (c) 2014 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace vipaka\ultimate_points\migrations;

class release_0_0_1 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['vipaka_ultimate_points_enable']);
	}
	public function update_schema()
	{
		return array(
			'add_columns'	=> array(
				$this->table_prefix . 'users'	=> array(
					'user_points'	=> array('DECIMAL:20', 0.00),
					'user_robbery_pm'	=> array('TINT:1', 1),
				),
				$this->table_prefix . 'forums'	=> array(
					'forum_perpost'	=> array('DECIMAL:10', 5.00),
					'forum_peredit'	=> array('DECIMAL:10', 0.05),
					'forum_pertopic'	=> array('DECIMAL:10', 15.00),
					'forum_costs'	=> array('TINT:1', 1),
				),
				$this->table_prefix . 'posts'	=> array(
					'points_post_edit'	=> array('TINT:1', 0),
					'points_post_edit_temp'	=> array('DECIMAL:20', 0.00),
					'points_received' 	=> array('DECIMAL:20', 0.00),
					'points_poll_received'	=> array('DECIMAL:20', 0.00),
					'points_attachment_received'	=> array('DECIMAL:20', 0.00),
					'points_topic_received'	=> array('DECIMAL:20', 0.00),
					'points_post_received'	=> array('DECIMAL:20', 0.00),
				),
				$this->table_prefix . 'extensions'	=> array(
					'points_extension'	=> array('TINT:1', 1),
					'points_extension_costs'	=> array('DECIMAL:10', 1.00),
				),
			),
			'add_tables'		=> array(
				$this->table_prefix . 'points_bank' => array(
					'COLUMNS'		=> array(
						'id'			=> array('UINT:10', NULL, 'auto_increment'),
						'user_id'		=> array('UINT:10', 0),
						'holding'		=> array('DECIMAL:20', 0.00),
						'totalwithdrew'	=> array('DECIMAL:20', 0.00),
						'totaldeposit'	=> array('DECIMAL:20', 0.00),
						'opentime'		=> array('UINT:10', 0),
						'fees'			=> array('CHAR:5', 'on'),
					),
					'PRIMARY_KEY'	=> 'id',
				),
				$this->table_prefix . 'points_config' => array(
					'COLUMNS'		=> array(
						'config_name'		=> array('VCHAR', ''),
						'config_value'		=> array('VCHAR_UNI', ''),
					),
					'PRIMARY_KEY'	=> 'config_name',
				),
				$this->table_prefix . 'points_log' => array(
					'COLUMNS'		=> array(
						'id'			=> array('UINT:11', NULL, 'auto_increment'),
						'point_send'	=> array('UINT:11', NULL, ''),
						'point_recv'	=> array('UINT:11', NULL, ''),
						'point_amount'	=> array('DECIMAL:20', 0.00),
						'point_sendold'	=> array('DECIMAL:20', 0.00),
						'point_recvold'	=> array('DECIMAL:20', 0.00),
						'point_comment'	=> array('MTEXT_UNI', ''),
						'point_type'	=> array('UINT:11', NULL, ''),
						'point_date'	=> array('UINT:11', NULL, ''),
					),
					'PRIMARY_KEY'	=> 'id',
				),
				$this->table_prefix . 'points_lottery_history' => array(
					'COLUMNS'		=> array(
						'id'		=> array('UINT:11', NULL, 'auto_increment'),
						'user_id'	=> array('UINT', 0),
						'user_name'	=> array('VCHAR', ''),
						'time'		=> array('UINT:11', 0),
						'amount'	=> array('DECIMAL:20', 0.00),
					),
					'PRIMARY_KEY'	=> 'id',
				),
				$this->table_prefix . 'points_lottery_tickets' => array(
					'COLUMNS'		=> array(
						'ticket_id'	=> array('UINT:11', NULL, 'auto_increment'),
						'user_id'	=> array('UINT:11', 0),
					),
					'PRIMARY_KEY'	=> 'ticket_id',
				),
				$this->table_prefix . 'points_values' => array(
					'COLUMNS'		=> array(
						'bank_cost'						=> array('DECIMAL:10', 0.00),
						'bank_fees'						=> array('DECIMAL:10', 0.00),
						'bank_interest'					=> array('DECIMAL:10', 0.00),
						'bank_interestcut'				=> array('DECIMAL:20', 0.00),
						'bank_last_restocked'			=> array('UINT:11', NULL),
						'bank_min_deposit'				=> array('DECIMAL:10', 0.00),
						'bank_min_withdraw'				=> array('DECIMAL:10', 0.00),
						'bank_name'						=> array('VCHAR:100', NULL),
						'bank_pay_period'				=> array('UINT:10', 2592000),
						'forum_topic'					=> array('DECIMAL:10', 0.00),
						'forum_post'					=> array('DECIMAL:10', 0.00),
						'forum_edit'					=> array('DECIMAL:10', 0.00),
						'gallery_upload'				=> array('DECIMAL:10', 0.00),
						'gallery_remove'				=> array('DECIMAL:10', 0.00),
						'gallery_view'					=> array('DECIMAL:10', 0.00),
						'lottery_base_amount'			=> array('DECIMAL:10', 0.00),
						'lottery_chance'				=> array('DECIMAL', 50.00),
						'lottery_draw_period'			=> array('UINT:10', 3600),
						'lottery_jackpot'				=> array('DECIMAL:20', 50.00),
						'lottery_last_draw_time'		=> array('UINT:11', NULL),
						'lottery_max_tickets'			=> array('UINT:10', 10),
						'lottery_name'					=> array('VCHAR:100', ''),
						'lottery_pm_from'				=> array('UINT:10', 0),
						'lottery_prev_winner'			=> array('VCHAR', ''),
						'lottery_prev_winner_id'		=> array('UINT:10', 0),
						'lottery_ticket_cost'			=> array('DECIMAL:10', 0.00),
						'lottery_winners_total'			=> array('UINT', 0),
						'number_show_per_page'			=> array('UINT:10', 0),
						'number_show_top_points'		=> array('UINT', 0),
						'points_per_attach'				=> array('DECIMAL:10', 0.00),
						'points_per_attach_file'		=> array('DECIMAL:10', 0.00),
						'points_per_poll'				=> array('DECIMAL:10', 0.00),
						'points_per_poll_option'		=> array('DECIMAL:10', 0.00),
						'points_per_post_character'		=> array('DECIMAL:10', 0.00),
						'points_per_post_word'			=> array('DECIMAL:10', 0.00),
						'points_per_topic_character'	=> array('DECIMAL:10', 0.00),
						'points_per_topic_word'			=> array('DECIMAL:10', 0.00),
						'points_per_warn'				=> array('DECIMAL:10', 0.00),
						'reg_points_bonus'				=> array('DECIMAL:10', 0.00),
						'robbery_chance'				=> array('DECIMAL:5', 0.00),
						'robbery_loose'					=> array('DECIMAL:5', 0.00),
						'robbery_max_rob'				=> array('DECIMAL:5', 0.00),
					),
				),
			),
		
		);
	}
	public function update_data()
	{
		return array(
			array('config.add', array('points_enable', 0)),
			array('config_text.add', array('points_name', '')),
			array('config_text.add', array('ultimate_points_version', '')),

			array('config_text.update', array('points_name', 'Points')),
			array('config_text.update', array('ultimate_points_version', '0.0.1')),

			array('permission.add', array('u_use_points')),
			array('permission.add', array('u_use_bank')),
			array('permission.add', array('u_use_logs')),
			array('permission.add', array('u_use_robbery')),
			array('permission.add', array('u_use_lottery')),
			array('permission.add', array('u_use_transfer')),
			array('permission.add', array('m_chg_points')),
			array('permission.add', array('m_chg_bank')),
			array('permission.add', array('u_points')),
			array('permission.add', array('a_points')),

			array('permission.permission_set', array('REGISTERED', 'u_use_points', 'group')),
			array('permission.permission_set', array('REGISTERED', 'u_use_bank', 'group')),
			array('permission.permission_set', array('REGISTERED', 'u_use_logs' , 'group')),
			array('permission.permission_set', array('REGISTERED', 'u_use_robbery', 'group')),
			array('permission.permission_set', array('REGISTERED', 'u_use_lottery', 'group')),
			array('permission.permission_set', array('REGISTERED', 'u_use_transfer', 'group')),
			array('permission.permission_set', array('ROLE_ADMIN_STANDARD', 'a_points')),
			array('permission.permission_set', array('ROLE_MOD_STANDARD', 'm_chg_points')),
			array('permission.permission_set', array('ROLE_MOD_STANDARD', 'm_chg_bank')),

			
		 	array('module.add', array(
            	'acp',
            	'ACP_CAT_DOT_MODS',
            	'ACP_POINTS_INDEX_TITLE'
      		 )),
			array('module.add', array(
				'acp',
				'ACP_POINTS_INDEX_TITLE',
				array(
					'module_basename'	=> '\vipaka\ultimate_points\acp\points_module',
					'modes'				=> array('points', 'bank', 'lottery', 'robbery', 'forumpoints', 'userguide'),
				),
			)),
			array('custom', array(array($this, 'insert_points_data'))),
		);
	}
	public function insert_points_data(){
			global $table_prefix; 
				$sql_ary = array();

				$sql_ary[] = array('config_name' => 'transfer_enable',				'config_value' => 1,);
				$sql_ary[] = array('config_name' => 'transfer_pm_enable',			'config_value' => 1,);
				$sql_ary[] = array('config_name' => 'comments_enable',				'config_value' => 1,);
				$sql_ary[] = array('config_name' => 'pertopic_enable',				'config_value' => 1,);
				$sql_ary[] = array('config_name' => 'perpost_enable',				'config_value' => 1,);
				$sql_ary[] = array('config_name' => 'peredit_enable',				'config_value' => 1,);
				$sql_ary[] = array('config_name' => 'logs_enable',					'config_value' => 1,);
				$sql_ary[] = array('config_name' => 'gallery_deny_view',			'config_value' => 0,);
				$sql_ary[] = array('config_name' => 'images_topic_enable',			'config_value' => 1,);
				$sql_ary[] = array('config_name' => 'images_memberlist_enable',		'config_value' => 1,);
				$sql_ary[] = array('config_name' => 'lottery_enable',				'config_value' => 1,);
				$sql_ary[] = array('config_name' => 'bank_enable',					'config_value' => 1,);
				$sql_ary[] = array('config_name' => 'robbery_enable',				'config_value' => 1,);
				$sql_ary[] = array('config_name' => 'points_disablemsg',			'config_value' => 'Ultimate Points is currently disabled!',);
				$sql_ary[] = array('config_name' => 'stats_enable',					'config_value' => 1,);
				$sql_ary[] = array('config_name' => 'lottery_multi_ticket_enable',	'config_value' => 1,);
				$sql_ary[] = array('config_name' => 'robbery_sendpm',				'config_value' => 1,);
				$sql_ary[] = array('config_name' => 'robbery_usage',				'config_value' => 1,);
				$sql_ary[] = array('config_name' => 'display_lottery_stats',		'config_value' => 1,);

				$this->db->sql_multi_insert($table_prefix . 'points_config ', $sql_ary);
				$sql_ary2 = array();

				$sql_ary2[] = array(
					'number_show_per_page' => 15,
					'number_show_top_points' => 10,
					'reg_points_bonus' => 50,
					'gallery_upload' 	=> 0,
					'gallery_view'	=> 0,
					'gallery_remove'	=> 0,
					'lottery_jackpot' => 50,
					'lottery_winners_total' => 0,
					'lottery_prev_winner' => 0,
					'lottery_prev_winner_id' => 0,
					'lottery_last_draw_time' => 0,
					'bank_last_restocked' => 0,
					'lottery_base_amount' => 50,
					'lottery_draw_period' => 3600,
					'lottery_ticket_cost' => 10,
					'lottery_pm_from' => 0,
					'bank_fees' => 0,
					'bank_interest' => 0,
					'bank_pay_period' => 2592000,
					'bank_min_withdraw' => 0,
					'bank_min_deposit' => 0,
					'bank_interestcut' => 0,
					'points_per_poll_option' => 0,
					'points_per_poll' => 0,
					'points_per_attach_file' => 0,
					'points_per_attach' => 0,
					'points_per_post_word' => 0,
					'points_per_post_character' => 0,
					'points_per_topic_word' => 0,
					'points_per_topic_character' => 0,
					'points_per_warn' => 0,
					'robbery_chance' => 50,
					'robbery_loose' => 50,
					'robbery_max_rob' => 10.00,
					'bank_cost' => 0,
					'bank_name' => 'BANK NAME',
					'lottery_name' => 'LOTTERY NAME',
				);

				$this->db->sql_multi_insert($table_prefix . 'points_values ', $sql_ary2);
	}
	public function revert_schema()
	{
		return array(
			array('config.remove', array('points_enable')),
			array('config_text.remove', array('points_name')),
			array('config_text.remove', array('ultimate_points_version')),
			array('permission.remove', array('u_use_points')),
			array('permission.remove', array('u_use_bank')),
			array('permission.remove', array('u_use_logs')),
			array('permission.remove', array('u_use_robbery')),
			array('permission.remove', array('u_use_lottery')),
			array('permission.remove', array('u_use_transfer')),
			array('permission.remove', array('m_chg_points')),
			array('permission.remove', array('m_chg_bank')),
			array('permission.remove', array('u_points')),
			'drop_columns'	=> array(
				$this->table_prefix . 'users'	=> array(
					'user_points',
					'user_robbery_pm',
				),
				$this->table_prefix . 'forums'	=> array(
					'forum_perpost',
					'forum_peredit',
					'forum_pertopic',
					'forum_costs',
				),
				$this->table_prefix . 'posts'	=> array(
					'points_post_edit',
					'points_post_edit_temp',
				),
				$this->table_prefix . 'extensions'	=> array(
					'points_extension',
					'points_extension_costs',
				),
			),
			'drop_tables'		=> array(
				$this->table_prefix . 'points_bank',
				$this->table_prefix . 'points_config',
				$this->table_prefix . 'points_log',
				$this->table_prefix . 'points_lottery_history',
				$this->table_prefix . 'points_lottery_tickets',
				$this->table_prefix . 'points_values',
			),
		);
	}
}
