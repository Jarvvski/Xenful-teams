<?php

class Teams_Model_Role extends Teams_Model_Abstract
{

	public function getRoleByIdSimple($roleId)
	{
		return $this->_getDb()->fetchRow('
		SELECT *
		FROM xf_teams_roles
		WHERE role_id = ' .$this->_getDb()->quote($roleId) .'
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
		FROM xf_teams_roles
		WHERE role_id IN (
			'.$this->_getDb()->quote($roleIds).'
			)
			ORDER BY hierarchy ASC
			', 'role_id');
		}

		public function getRolesByTeam(array $team)
		{
			if (!$team)
			{
				return array();
			}

			return $this->fetchAllKeyed('
			SELECT *
			FROM xf_teams_roles
			WHERE team_id = '.$this->_getDb()->quote($team['team_id']).'
			', 'team_id');
		}

		public function getAllRoles()
		{
			return $this->fetchAll('
			SELECT *
			FROM xf_teams_roles
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

			return $this->_getDb()->fetchRow('
			SELECT *
			FROM xf_teams_roles
			WHERE team_id = '.$this->_getDb()->quote($team['team_id']).'
			AND user_id = '.$this->_getDb()->quote($user['user_id']).'
			');
		}

		public function prepareRoles(array &$roles)
		{
			foreach ($roles as $role)
			{
				$role = $this->prepareRole($role);
			}

			return $roles;
		}

		public function prepareRole(array &$role)
		{
			$role['managed_team_ids'] = explode(',', $role['managed_team_ids']);
			$role['managed_team_ids'] = array_filter($role['managed_team_ids']);

			$assignedDate = new DateTime(date('r', $role['assigned_date']));
			$role['assigned_date'] = $assignedDate->format('Y-m-d');

			return $role;
		}
	}
