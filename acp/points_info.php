<?php
/**
*
* @package phpBB Extension - Ultimate Points
* @copyright (c) 2014 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace vipaka\ultimate_points\acp;

class points_info
{
	function module()
	{
		return array(
			'filename'	=> '\vipaka\points\acp\points_module',
			'title'		=> 'ACP_POINTS_TITLE',
			'version'	=> '0.0.1',
			'modes'		=> array(
				'points'	=> array('title' => 'ACP_POINTS', 'auth' => 'ext_vipaka/ultimate_points && acl_a_board', 'cat' => array('ACP_POINTS_TITLE')),
				'bank'	=> array('title' => 'ACP_POINTS_BANK', 'auth' => 'ext_vipaka/ultimate_points && acl_a_board', 'cat' => array('ACP_POINTS_TITLE')),
				'lottery'	=> array('title' => 'ACP_POINTS_LOTTERY', 'auth' => 'ext_vipaka/ultimate_points && acl_a_board', 'cat' => array('ACP_POINTS_TITLE')),
				'robbery'	=> array('title' => 'ACP_POINTS_ROBBERY', 'auth' => 'ext_vipaka/ultimate_points && acl_a_board', 'cat' => array('ACP_POINTS_TITLE')),
				'forumpoints'	=> array('title' => 'ACP_POINTS_FORUMPOINTS', 'auth' => 'ext_vipaka/ultimate_points && acl_a_board', 'cat' => array('ACP_POINTS_TITLE')),
				'userguide'	=> array('title' => 'ACP_POINTS_USERGUIDE', 'auth' => 'ext_vipaka/ultimate_points && acl_a_board', 'cat' => array('ACP_POINTS_TITLE')),
				
			),
		);
	}
}
