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
		', 'relation_id');
	}

	public function getAllRoles()
	{
		return $this->fetchAll('
			SELECT *
			FROM xf_teams_relations
		');
	}

	public function getUserViaRole($role)
	{
		if (!$role['user_id'])
		{
			return array();
		}

		$user = $this->_getUserModel()->getUserById($role['user_id']);
	}


}
