<?php
/**
*
* @package phpBB Extension - Vipaka Ultimate points* @copyright (c) 2014 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace vipaka\ultimate_points\event;

/**
* @ignore
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class main_listener implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return array(
			'core.user_setup'						=> 'load_language_on_setup',
			'core.page_header'						=> 'add_page_header_links',
      'core.common'            => 'obtain_points_config',
      'core.page_header_after'      => 'functions_template_vars',
     // 'core.viewtopic_assign_template_vars_before'  => '',
     // 'core.viewtopic_get_post_data'  => '',
      'core.viewtopic_cache_user_data'  => 'viewtopic_cache_user_data',
      //'core.viewtopic_cache_guest_data'   => 'viewtopic_cache_gdata',
       'core.viewtopic_modify_post_row'  => 'viewtopic_template_adds',


		);
	}

 /* @var \phpbb\controller\helper */
  protected $helper;

  /* @var \phpbb\template\template */
  protected $template;
  protected $db;
  protected $config;
  protected $config_text;

  /**
  * Constructor
  *
  * @param \phpbb\controller\helper $helper   Controller helper object
  * @param \phpbb\template      $template Template object
  */
  public function __construct(\phpbb\auth\auth $auth, \phpbb\controller\helper $helper, \phpbb\template\template $template, \phpbb\db\driver\driver_interface $db, \phpbb\config\config $config, \phpbb\config\db_text $config_text, \phpbb\cache\driver\driver_interface $driver, \phpbb\cache\service $cache)
  {
    $this->config = $config;
    $this->config_text = $config_text;
    $this->helper = $helper;
    $this->template = $template;
    $this->db = $db;
    $this->cache = $cache;
    $this->driver = $driver;
    $this->auth = $auth;
  }

	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'vipaka/ultimate_points',
			'lang_set' => 'common',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}
  public function add_page_header_links($event)
  {

    global $db, $config, $user, $table_prefix, $phpbb_root_path, $phpEx;

    $data = $this->config_text->get_array(array(
      'points_name',
      'ultimate_points_version',
    ));

    define('POINTS_VALUES_TABLE', $table_prefix . 'points_values');
    define('POINTS_CONFIG', $table_prefix . 'points_config');

    //another commented out error for time being.

    $this->template->assign_vars(array(
      //'U_POINTS'  => $this->helper->route('vipaka_ultimate_points_controller', array('name' => 'points')),
      'U_LOGS'  =>  $this->helper->route('vipaka_ultimate_points_controller', array('name' => 'points_logs')),
      'U_BANK'  =>  $this->helper->route('vipaka_ultimate_points_controller', array('name' => 'points_bank')),
      'U_TRANSFER_USER'  => $this->helper->route('vipaka_ultimate_points_controller', array('name' => 'points_transfer')),
      'U_ROBBERY' => $this->helper->route('vipaka_ultimate_points_controller', array('name' => 'points_robbery')),
      'U_LOTTERY' =>  $this->helper->route('vipaka_ultimate_points_controller', array('name' => 'points_lottery')),
      'U_VIEW_HISTORY' =>  $this->helper->route('vipaka_ultimate_points_controller', array('name' => 'points_lottery_history')),
      'U_VIEW_SELF_HISTORY' =>  $this->helper->route('vipaka_ultimate_points_controller', array('name' => 'points_lottery_history_self')),
      'U_INFO'  => $this->helper->route('vipaka_ultimate_points_controller', array('name' => 'points_info')),
      'U_OVERVIEW'  => $this->helper->route('vipaka_ultimate_points_controller', array('name' => 'points_overview')),
      'POINTS_ENABLED' => $config['points_enable'],
      'S_POINTS_NAME'   => $data['points_name'],
      'S_POINTS_VERSION'  => $data['ultimate_points_version'],
    ));
  }
  public function obtain_points_config($event)
  {
    global $db, $config, $user, $table_prefix, $phpbb_root_path, $phpEx;

    include($phpbb_root_path . 'ext/vipaka/ultimate_points/includes/points/functions_points.' . $phpEx);
    define('POINTS_CONFIG', $table_prefix . 'points_config');

   // $user_cache_data = $event['user_cache_data'];
   // $user_cache_data['user_points'] = $event['row']['user_points'];
    //$event['user_cache_data'] = $user_cache_data;

    //if (($points_config = $this->get('pointsconfig')) !== false)
    //{
      $sql = 'SELECT config_name, config_value
        FROM ' . POINTS_CONFIG;
      $result = $this->db->sql_query($sql);

      while ($row = $this->db->sql_fetchrow($result))
      {
        $points_config[$row['config_name']] = $row['config_value'];
      }
      $this->db->sql_freeresult($result);
   /* }
    else
    {
      $points_config = $cached_points_config = array();

      $sql = 'SELECT config_name, config_value
        FROM ' . POINTS_CONFIG_TABLE;
      $result = $this->db->sql_query($sql);

      while ($row = $this->db->sql_fetchrow($result))
      {
        print_r($row);
        $points_config[$row['config_name']] = $row['config_value'];
      }
      $this->db->sql_freeresult($result);

      $this->put('points_config', $cached_points_config);
    }*/
    $this->driver->put('points_config', $points_config);

    $this->template->assign_vars(array(
      'TRANSFER_ENABLE'  => $points_config['transfer_enable'],
      'LOTTERY_ENABLE' => $points_config['lottery_enable'],
      'BANK_ENABLE'   => $points_config['bank_enable'],
      'ROBBERY_ENABLE'  => $points_config['robbery_enable'],
    ));
    define('POINTS_VALUES_TABLE', $table_prefix . 'points_values');

    $sql_array = array(
      'SELECT'    => '*',
      'FROM'      => array(
        POINTS_VALUES_TABLE => 'v',
      ),
    );
    $sql = $db->sql_build_query('SELECT', $sql_array);
    $result = $db->sql_query($sql);
    $points_values = $db->sql_fetchrow($result);
    $db->sql_freeresult($result);
   
    $this->driver->put('points_values', $points_values);
     $this->template->assign_vars(array(
      'BANK_NAME'  => $points_values['bank_name'],
      'LOTTERY_NAME' => $points_values['lottery_name'],
    ));
    return $points_values;
  }
  
  public function viewtopic_php_data(){
    global $user, $db;
    $sql = 'SELECT  pb.id AS pb_id, pb.holding AS pb_holding,
      FROM ' . POINTS_BANK_TABLE . '
      WHERE poster_id = ' . (int) $poster_id;

    $has_account = true;
    $holding = (empty($holding)) ? array() : $holding;
    $pointslock = $banklock = '';

    if ($config['points_enable'])
    {
    // Get the points status
      $check_auth = new auth();
      $check_auth->acl($row);
      $pointslock = !$check_auth->acl_get('u_use_points');

    // Get the bank status
      if ($points_config['bank_enable'])
      {
        $check_auth = new auth();
        $check_auth->acl($row);
        $banklock = !$check_auth->acl_get('u_use_bank');
      }

      if (!isset($row['pb_holding']) && $poster_id > 0)
      {
        $has_account = false;
      }
      $holding[$poster_id] = ($row['pb_holding']) ? $row['pb_holding'] : '0';
    }
    else
    {
      $holding[$poster_id] = '0';
    }

  
  }
  public function viewtopic_cache_user_data($vars){
    //there is a looping problem here that I feel is either due to my own lack of knowledge of 3.1 or built into the way the vars are handled. 
    global $user, $db, $table_prefix;

    define('POINTS_BANK_TABLE', $table_prefix . 'points_bank');
    $poster_id = $vars['poster_id'];
    $row = $vars['row'];
    $user_cache_data = $vars['user_cache_data'];

    $sql = 'SELECT id, holding
      FROM ' . POINTS_BANK_TABLE . '
      WHERE user_id = ' . (int) $poster_id;
      $results = $db->sql_query($sql);
      while ($rows = $db->sql_fetchrow($results)){
        $holding = $rows['holding'];
      }
    $has_account = true;
    $holding = (empty($holding)) ? array() : $holding;
    $pointslock = $banklock = '';

    if ($this->config['points_enable'])
    {
    // Get the points status
     // $check_auth = new auth();
     // $check_auth->acl($row);
      $pointslock = $this->auth->acl_get('u_use_points');

    // Get the bank status
      if ($points_config['bank_enable'])
      {
        $banklock = $this->auth->acl_get('u_use_bank');
      }

      if (!isset($row['pb_holding']) && $poster_id > 0)
      {
        $has_account = false;
      }
      $holding[$poster_id] = ($row['pb_holding']) ? $row['pb_holding'] : '0';
    }
    else
    {
      $holding[$poster_id] = '0';
    }

    $user_cache_data[$poster_id]['points'] = array(
       // 'points'      => 0.00,
       // 'points_lock'   => true,
       // 'bank_lock'     => true,
       // 'bank_account'    => true,
        'points'    => $row['user_points'],
        'points_lock' => $pointslock,
        'bank_lock'   => $banklock,
        'bank_account'  => $has_account,
      );

    //honestly this whole function is a hot mess and this return result onlyl returns once, not for every post.
    return $user_cache_data;
  }
  public function viewtopic_cache_gdata(){
    $user_cache_data = array(
      //core.viewtopic_cache_user_data

      );
   // $vars = array('user_cache_data', 'poster_id', 'row');
  }
  public function viewtopic_template_adds($vars){
    $points_config = $this->driver->get('points_config');
    $poster_id = $vars['poster_id'];
    //this user_cache variable is a bag of worms since it isn't holding the correct points values.
    $user_cache[$poster_id] = $vars['row'];
            $templ = $this->template->assign_vars(array(
             'P_NAME'      => $this->config['points_name'],
             'USE_POINTS'    => $this->config['points_enable'],
             'USE_IMAGES_POINTS' => $points_config['images_topic_enable'],
              'USE_BANK'      => $points_config['bank_enable'],
            ));
            $post_row = array(
              //Start Ultimate Points
              'POSTER_POINTS'   => number_format_points($user_cache[$poster_id]['points']),
              'POSTER_LOCK'   => $user_cache[$poster_id]['points_lock'],
              'POSTER_BANK_LOCK'  => $user_cache[$poster_id]['bank_lock'],
              'USER_ID'     => $poster_id,
              'BANK_GOLD'     => number_format_points($holding[$poster_id]),
              'BANK_ACCOUNT'    => $user_cache[$poster_id]['bank_account'],
              'L_MOD_USER_POINTS'   => ($this->auth->acl_get('a_points') || $this->auth->acl_get('m_chg_points')) ? sprintf($user->lang['POINTS_MODIFY']) : '',
             'U_POINTS_MODIFY'   => ($this->auth->acl_get('a_points') || $this->auth->acl_get('m_chg_points')) ? append_sid("{$phpbb_root_path}points.$phpEx", "mode=points_edit&amp;user_id=".$poster_id."&amp;adm_points=1&amp;post_id=".$row['post_id'])  : '',
              'L_BANK_USER_POINTS'  => ($this->auth->acl_get('a_points') || $this->auth->acl_get('m_chg_bank')) ? sprintf($user->lang['POINTS_MODIFY']) : '',
              'U_BANK_MODIFY'     => ($this->auth->acl_get('a_points') || $this->auth->acl_get('m_chg_bank')) ? append_sid("{$phpbb_root_path}points.$phpEx", "mode=bank_edit&amp;user_id=".$poster_id."&amp;adm_points=1&amp;post_id=".$row['post_id'])  : '',
              'L_DONATE'        => ($this->auth->acl_get('u_use_transfer')) ? sprintf($user->lang['POINTS_DONATE']) : '',
             'U_POINTS_DONATE'   => ($this->auth->acl_get('u_use_transfer')) ? append_sid("{$phpbb_root_path}points.$phpEx", "mode=transfer&amp;i=".$poster_id."&amp;adm_points=1&amp;post_id=".$row['post_id'])  : '',
             'S_IS_OWN_POST'     => ($poster_id == $user->data['user_id']) ? true : false,
               //End Ultimate Points
            );

    $vari = array($templ, $post_row);
    return $vari;
  }
  //fucking kill me....
  public function viewtopic_attachment_query(){
    //no clue how to do this since f + r is gone.
  }
  public function functions_template_vars(){
    global $user, $phpEx, $phpbb_root_path;
    if (isset($this->config['points_name']))
    {
      //this determines how many decimal points to show on the users points variable.
      $decimals = 0;

      $upoints = number_format($user->data['user_points'], $decimals, $user->lang['POINTS_SEPARATOR_DECIMAL'], $user->lang['POINTS_SEPARATOR_THOUSANDS']);

      $this->template->assign_vars(array(
        'U_POINTS'        => append_sid("{$phpbb_root_path}points"),
        'POINTS_LINK'     => $this->config['points_name'],
        'USER_POINTS'     => sprintf($upoints),
        'S_POINTS_ENABLE'   => $this->config['points_enable'],
        'S_USE_POINTS'      => $this->auth->acl_get('u_use_points'),
      ));
    }
  }
}