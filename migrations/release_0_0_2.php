<?php
/**
*
* @package phpBB Extension - Vipaka Ultimate points* @copyright (c) 2014 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace vipaka\ultimate_points\migrations;

class release_0_0_2 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\vipaka\ultimate_points\migrations\release_0_0_1');
	}
	public function update_schema()
	{
		return array(
		);
	}
	public function update_data()
	{
		return array(
		
			array('permission.add', array('a_points')),

			array('permission.permission_set', array('ROLE_ADMIN_STANDARD', 'a_points')),
		);
	}
	
	public function revert_schema()
	{
		return array(
	
			array('permission.remove', array('a_points')),
			
		);
	}
}
