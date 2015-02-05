<?php
/**
*
* @package phpBB Extension - Vipaka Ultimate Points
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace vipaka\ultimate_points\controller;

class main
{
	/* @var \phpbb\config\config */
	protected $config;

	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbb\template\template */
	protected $template;

	/* @var \phpbb\user */
	protected $user;

	protected $db;
	protected $auth;
	protected $root_path;
	protected $php_ext;
	/**
	* Constructor
	*
	* @param \phpbb\config\config		$config
	* @param \phpbb\controller\helper	$helper
	* @param \phpbb\template\template	$template
	* @param \phpbb\user				$user
	* @param \root_path				$root_path
	* @param \php_ext				$php_ext
	*/
	public function __construct(\phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\controller\helper $helper, \phpbb\template\template $template, \phpbb\user $user, \phpbb\cache\driver\driver_interface $driver, $root_path, $php_ext)
	{
		$this->config = $config;
		$this->helper = $helper;
		$this->template = $template;
		$this->user = $user;
		$this->db = $db;
		$this->auth = $auth;
		$this->driver = $driver;
		$this->php_ext = $php_ext;
		$this->root_path = $root_path;
		
	}

	public function handle($name)
	{
		$l_message = !$this->config['vipaka_points_enable'] ? 'POINTS' : 'POINTS_DISABLED';
		$this->template->assign_var('POINTS', $this->user->lang($l_message, $name));
		
		//include($this->phpbb_root_path . 'includes/functions_user.' . $phpEx);
		//include($this->phpbb_root_path . 'includes/functions_module.' . $phpEx);
		//include($this->phpbb_root_path . 'includes/functions_display.' . $phpEx);
		//include($this->phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);

		$submit = (isset($_POST['submit'])) ? true : false;
		$save = (isset($_POST['save'])) ? true : false;
		$gender = (isset($_POST['gender'])) ? true : false;
		$reset = (isset($_POST['reset'])) ? true : false;
		$user_id = $user->data['user_id'];
		$user_gender = $user->data['user_gender'];
		$points_config = $this->driver->get('points_config');
    	$points_values = $this->driver->get('points_values');

		// Disable Ultimate Points if the points_install file is still present
		if (file_exists($phpbb_root_path . 'install_ultimate_points.php'))
		{
		// Adjust the message slightly according to the permissions
			if ($auth->acl_gets('a_'))
			{
				$message = $user->lang['POINTS_REMOVE_INSTALL'];
			}
			else
			{
				$message = $points_config['points_disablemsg'];
			}
			trigger_error($message);
		}

		//Check if you are locked or not
		if (!$this->auth->acl_get('u_use_points'))
		{
			trigger_error('NOT_AUTHORISED');
		}

		// Get user's information
		$check_user = request_var('i', 0);
		$check_user = ($check_user == 0) ? $this->user->data['user_id'] : $check_user;

		$sql_array = array(
			'SELECT'	=> '*',
			'FROM'		=> array(
				USERS_TABLE => 'u',
			),
			'WHERE'		=> 'u.user_id = ' . (int) $check_user,
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$checked_user = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		//$this->auth = new auth();
		$this->auth->acl($checked_user);

		if (!$checked_user)
		{
			trigger_error('POINTS_NO_USER');
		}

		// Check if points system is enabled
		if (!$this->config['points_enable'])
		{
			trigger_error($points_config['points_disablemsg']);
		}

		// Add the base entry into the Nav Bar at top
		$this->template->assign_block_vars('navlinks', array(
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}points." . $phpEx),
			'FORUM_NAME'	=> sprintf($user->lang['POINTS_TITLE_MAIN'], $this->config['points_name']),
		));

		$this->template->assign_vars(array_change_key_case($checked_user, CASE_UPPER));

		$user_name = get_username_string('full', $user->data['user_id'], $user->data['username'], $user->data['user_colour'], $user->data['username']);

		//points config error, commented out.
		$this->template->assign_vars(array_merge(array_change_key_case($points_config, CASE_UPPER), array(
			'USER_POINTS'		=> number_format_points ($user->data['user_points']),
			'U_USE_POINTS'		=> $this->auth->acl_get('u_use_points'),
			'U_CHG_POINTS'		=> $this->auth->acl_get('m_chg_points'),
			'U_USE_TRANSFER'	=> $this->auth->acl_get('u_use_transfer'),
			'U_USE_LOGS'		=> $this->auth->acl_get('u_use_logs'),
			'U_USE_LOTTERY'		=> $this->auth->acl_get('u_use_lottery'),
			'U_USE_BANK'		=> $this->auth->acl_get('u_use_bank'),
			'U_USE_ROBBERY'		=> $this->auth->acl_get('u_use_robbery'),
		)));
  
		//$module = new p_master();
		$mode = $name;
		$this->user->add_lang_ext('points', false, true);


		switch($mode)
		{
			case 'transfer_user':
				//$module->load('points', 'transfer_user');
				//$module->display("{L_POINTS_TRANSFER}");
				$this->helper->render('points_transfer_user.html', '{L_POINTS_TRANSFER_USER}');
			break;

			case 'points_logs':
				include($this->root_path . 'ext/vipaka/ultimate_points/includes/points/points_logs.' . $this->php_ext);
				return $this->helper->render('points_logs.html', '{L_POINTS_LOGS}');
			case 'points_lottery':
				include($this->root_path . 'ext/vipaka/ultimate_points/includes/points/points_lottery.' . $this->php_ext);
				return $this->helper->render('points_lottery.html', '{L_POINTS_LOTTERY}');
			case 'points_lottery_history':
				include($this->root_path . 'ext/vipaka/ultimate_points/includes/points/points_lottery.' . $this->php_ext);
				return $this->helper->render('points_lottery.html', '{L_POINTS_LOTTERY}');
			case 'points_lottery_history_self':
				include($this->root_path . 'ext/vipaka/ultimate_points/includes/points/points_lottery.' . $this->php_ext);
				return $this->helper->render('points_lottery.html', '{L_POINTS_LOTTERY}');
			case 'points_transfer':
				include($this->root_path . 'ext/vipaka/ultimate_points/includes/points/points_transfer.' . $this->php_ext);
				return $this->helper->render('points_transfer.html', '{L_POINTS_TRANSFER}');
			case 'points_robbery':
				include($this->root_path . 'ext/vipaka/ultimate_points/includes/points/points_robbery.' . $this->php_ext);
				return $this->helper->render('points_robbery.html', '{L_POINTS_ROBBERY}');
			case 'points_edit':
			case 'points_bank':
				include($this->root_path . 'ext/vipaka/ultimate_points/includes/points/points_bank.' . $this->php_ext);
				return $this->helper->render('points_bank.html', '{L_POINTS_BANK}');
			case 'bank_edit':
			case 'points_info':
				//$module->load('points', $mode);
				//$module->display("{L_POINTS_}" . strtoupper($mode));
				include($this->root_path . 'ext/vipaka/ultimate_points/includes/points/points_info.' . $this->php_ext);
				return $this->helper->render('points_info.html', '{L_POINTS_INFO}');
			break;

			default:
				//$module->load('points', 'main');
				//$module->display("{L_POINTS_OVERVIEW}");
				include($this->root_path . 'ext/vipaka/ultimate_points/includes/points/points_main.' . $this->php_ext);
				$this->helper->render('points_main.html', '{L_POINTS_OVERVIEW}');
			break;
		}

		return $this->helper->render('points_main.html', $name);
		
	}
	
}
