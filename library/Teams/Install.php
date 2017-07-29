<?php
class Teams_Install {

	protected static $table = array(
		'createTeams' => "CREATE TABLE `xf_teams_teams` (
			`team_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`team_name` varchar(250) NOT NULL DEFAULT '',
			`team_remark` varchar(250) NOT NULL DEFAULT '',
			`managed_teams` text,
			`can_extend` tinyint(4) NOT NULL,
			`hierarchy` int(11) NOT NULL,
			`parent_id` int(11) NOT NULL,
			PRIMARY KEY (`team_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8",
		'dropTeams' => 'DROP TABLE IF EXISTS `xf_teams_teams`',

		'createroles' => "CREATE TABLE `xf_teams_user_role` (
			`role_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`team_id` int(11) NOT NULL,
			`user_id` int(11) DEFAULT NULL,
			`username` varchar(250) DEFAULT NULL,
			`role_title` varchar(250) NOT NULL DEFAULT '',
			`abreviation` varchar(250) NOT NULL DEFAULT '',
			`managed_team_ids` varbinary(255) NOT NULL DEFAULT '',
			`hierarchy` int(11) NOT NULL,
			`primary` tinyint(4) NOT NULL,
			`assigned_date` int(11) DEFAULT NULL,
			PRIMARY KEY (`role_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8",
		'droproles' => 'DROP TABLE IF EXISTS `xf_teams_roles`'
	);

	// This is the function to create a table in the database so our addon will work.
	public static function install($addon)
	{
		if ($addon['version_id'] <= 100) {
			$db = XenForo_Application::getDb();
			$db->query(self::$table['createTeams']);
			$db->query(self::$table['createroles']);
		}
	}

	// This is the function to DELETE the table of our addon in the database.
	public static function uninstall()
	{
		$db = XenForo_Application::getDb();
		$db->query(self::$table['dropTeams']);
		$db->query(self::$table['droproles']);
	}
}

?>
