<?php

class Teams_Model_Team extends Teams_Model_Abstract
{

	/**
	* Get a single team for a given teamID
	*
	* @param  int $teamId
	* @return array/data obj
	*/
	public function getTeamByIdSimple($teamId)
	{
		return $this->_getDb()->fetchRow('
		SELECT *
		FROM xf_teams_teams
		WHERE team_id = ' .$this->_getDb()->quote($teamId) .'
		');
	}

	/**
	* Gets an array of teams from an array of teamIDs
	*
	* @param  array  $teamIds collection of teamIDs (ints)
	* @return array  Collection of Teams with corresponding data
	*/
	public function getTeamsByID(array $teamIds)
	{
		if (!$teamIds)
		{
			return array();
		}

		return $this->fetchAllKeyed('
		SELECT *
		FROM xf_teams_teams
		WHERE team_id IN (' .$this->_getDb()->quote($teamIds) .')
		', 'team_id');
	}

	/**
	* Gets all current Teams on the ADR
	*
	* @return array  Collection of all team Data
	*/
	public function getAllTeams()
	{
		return $this->_getDb()->fetchAll('
		SELECT *
		FROM xf_teams_teams
		ORDER BY hierarchy
		');
	}

	public function getBaseTeams()
	{
		return $this->_getDb()->fetchAll('
		SELECT *
		FROM xf_teams_teams
		WHERE parent_id = 0
		');
	}

	public function getChildren(array $team)
	{
		if (!$team)
		{
			return array();
		}

		return $this->_getDb()->fetchAll('
		SELECT *
		FROM xf_teams_teams
		WHERE parent_id = ' . $this->_getDb()->quote($team['team_id']). '
		ORDER BY hierarchy ASC
		');
	}

	/**
	* Prepare Team for sending and add to cache
	* @param  array/data Obj  $team
	* @return array  team prepared
	*/
	public function prepareTeam(&$team)
	{
		$team['roles'] = $this->getRolesByTeam($team);
		return $team;
	}

	/**
	* Get team members via the team they are in
	*
	* @param  int  $teamId
	* @return array  Users contained in role of given team
	*/
	public function getTeamMembersByTeamId($teamId)
	{
		// TODO: might need to include roles for each member
		// - maybe sepperate function

		return $this->_getDb()->fetchAll('
		SELECT role.*, user.username,
		user.avatar_date, user.gravatar
		FROM xf_teams_roles AS role
		INNER JOIN xf_user AS user ON (user.user_id = role.user_id)
		WHERE role.team_id = ' . $this->_getDb()->quote($teamId). '
		ORDER BY role.hierarchy ASC
		');
	}

	/**
	* Gets roles availible in team
	*
	* @param  array
	* @return [type]         [description]
	*/
	public function getRolesByTeam(array $team)
	{
		// TODO: get all roles in team

		if (!$team)
		{
			return array();
		}

		// return $this->getTeamMembersByTeamId($team['team_id']);

		$roles = $this->_getDb()->fetchAll('
		SELECT *
		FROM xf_teams_roles
		WHERE team_id = '.$this->_getDb()->quote($team['team_id']).'
		');

		return $this->_getRoleModel()->prepareRoles($roles);
	}

	/**
	* Delets a user from a team via removal of role
	* @param  int $teamId
	* @return [type]         [description]
	*/
	public function deleteTeamUser($teamId)
	{
		$roles = $this->getrolesByTeamId($teamId);

		foreach ($roles as $roleId => $role)
		{
			// TODO: create role DW
			$roleDw = XenForo_DataWriter::create('ADR_DataWriter_TeamUserrole');
			$roleDw->setExistingData($userID);
			$roleDw->delete();
		}
	}

	/**
	* Gets the primary team for a given user
	*
	* @param int  $userId
	* @return array team data
	*/
	public function getPrimaryTeamByUser(array $user)
	{
		if (!$user)
		{
			return array();
		}

		return $this->_getDb()->fetchRow('
		SELECT teams.*
		FROM xf_teams_teams AS teams
		INNER JOIN xf_teams_role AS role
		ON role.team_id = teams.team_id
		WHERE role.user_id = '.$this->_getDb()->quote($user['user_id']).'
		AND role.primary = TRUE
		');
	}

	/**
	* Gets all the non-primary teams a given user
	*
	* @param  int  $userId
	* @return array of team arrays (2d)
	*/
	public function getNotPrimaryTeamsByUser(array $user)
	{
		if (!$user)
		{
			return array();
		}

		return $this->_getDb()->fetchAll('
		SELECT teams.*
		FROM xf_teams_teams AS teams
		INNER JOIN xf_teams_roles AS role
		ON role.team_id = teams.team_id
		WHERE role.user_id = '.$this->_getDb()->quote($user['user_id']).'
		AND role.primary = FALSE
		');
	}

	/**
	* Gets all teams except for those of a given user
	*
	* @param  [type] $userId [description]
	* @return [type]         [description]
	*/
	public function getNonUserTeams($userId)
	{
		return $this->fetchAllKeyed('
		SELECT teams.*
		FROM xf_teams_teams AS teams
		INNER JOIN xf_teams_role AS role
		on role_id = teams.role_id
		WHERE role.user_id != '.$this->_getDb()->quote($userId).'
		');
	}

	public function prepareTeamForOrg($team)
	{
		$roleData = "";

		$roles = $this->getRolesByTeam($team);

		foreach($roles as $role)
		{
			if ($role['username'] == '')
			{
				$role['username'] = "Vacant";
			}

			$roleData .= "<tr><td>".$role['abreviation']."</td><td>".$role['username']."</td></tr>";
		}

		$data = "<h5><a href='teams/team-view?team_id=".$team['team_id']."'>".$team['team_name']."</a></h5>
		<table>
		<tr>

		</tr>
		".
		$roleData
		."
		</table>
		";

		$row = array(
			'c' => array(
				0 => array(
					'v' => $team['team_id'],
					'f' => $data
				),
				1 => array(
					'v' => $team['parent_id']
				)
			)
		);

		return $row;
	}

	// TODO: make perm functions for can View team / can Admin team etc etc
	// public function canDonate(&$errorPhraseKey = '', array $viewingUser = null)
	// {
	// 	$this->standardizeViewingUserReference($viewingUser);
	//
	// 	return XenForo_Permission::hasPermission($viewingUser['permissions'], 'general', 'canDonate');
	// }

	// public function canAdminTeam(array $team)
	// {
	// 	$user = XenForo_Visitor::getInstance()->toArray();
	// 	$roleModel = $this->_getRoleModel();
	//
	// 	$role = $roleModel->getRoleInTeamForUser($team, $user);
	// 	return $role['admin'];
	// }
	//
	// public function canModTeam(array $team)
	// {
	// 	$user = XenForo_Visitor::getInstance()->toArray();
	// 	$roleModel = $this->_getRoleModel();
	//
	// 	$role = $roleModel->getRoleInTeamForUser($team, $user);
	// 	return $role['mod'];
	// }
}
