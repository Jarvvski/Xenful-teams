<?php

class Teams_Model_Role extends Teams_Model_Abstract
{
	public function getRoleByIdSimple($roleId)
	{
		return $this->$db->fetchRow('
			SELECT *
			FROM xf_teams_relations
			WHERE role_id = ' .$this->$db->quote($roleId) .'
		');
	}

	public function getRolesById(array $roleIds)
	{
		if (!$roleIds)
		{
			return array();
		}

		return $this->fetchAllKeyed('
			SELECT *
			FROM xf_teams_relations
			WHERE relation_id IN (
				'.$this->$db->quote($roleIds).'
			)
			ORDER BY order DESC
		', 'relation_id');
	}

	public function getRolesByTeam(array $team)
	{
		if (!$team)
		{
			return array();
		}

		return $this->fetchAllKeyed('
			SELECT *
			FROM xf_teams_relations
			WHERE team_id = '.$this->$db->quote($team['team_id']).'
		');
	}

	public function getAllRoles()
	{
		return $this->fetchAll('
			SELECT *
			FROM xf_teams_relations
		');
	}

	public function getUserByRole($role)
	{
		if (!$role['user_id'])
		{
			return array();
		}

		$user = $this->_getUserModel()->getUserById($role['user_id']);
	}

	public function getRoleInTeamForUser(array $team, array $user)
	{
		if (!$team || !$user)
		{
			return array();
		}

		return $this->fetchAllKeyed('
		return $this->_getDb()->fetchRow('
			SELECT *
			FROM xf_teams_relations
			WHERE team_id = '.$this->_getDb()->quote($team['team_id']).'
			AND user_id = '.$this->_getDb()->quote($user['user_id']).'
		');
	}

}
