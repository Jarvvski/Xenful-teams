<?php

class Teams_DataWriter_Role extends XenForo_DataWriter
{
	protected function _getFields()
	{
		return array(
			'xf_teams_roles' => array(
				'role_id' => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
				'team_id' => array('type' => self::TYPE_UINT, 'required' => true),
				'user_id' => array('type' => self::TYPE_UINT),
				'username' => array('type' => self::TYPE_STRING, 'maxLength' => 240),
				'role_title' => array('type' => self::TYPE_STRING, 'maxLength' => 240, 'required' => true),
				'remark' => array('type' => self::TYPE_STRING, 'maxLength' => 240),
				'managed_team_ids' => array('type' => self::TYPE_UNKNOWN, 'required' => true, 'default' => '',
				'verification' => array('$this', '_verifyManagedTeamIds')
			),
			'hierarchy' => array('type' => self::TYPE_UINT, 'required' => true),
			'primary' => array('type' => self::TYPE_BOOLEAN, 'required' => true, 'default' => 1),
			'assigned_date' => array('type' => self::TYPE_UINT),
		)
	);
}

protected function _getExistingData($data)
{
	if (!$id = $this->_getExistingPrimaryKey($data, 'role_id'))
	{
		return false;
	}

	return array('xf_teams_roles' => $this->_getRoleModel()->getRoleByIdSimple($id));
}

protected function _verifyManagedTeamIds(&$teamIds)
{
	if (!is_array($teamIds))
	{
		$teamIds = preg_split('#,\s*#', $teamIds);
	}

	$teamIds = array_map('intval', $teamIds);
	$teamIds = array_unique($teamIds);
	sort($teamIds, SORT_NUMERIC);
	$teamIds = implode(',', $teamIds);

	return true;
}

protected function _getUpdateCondition($tableName)
{
	return 'role_id = ' . $this->_db->quote($this->getExisting('role_id'));
}

protected function _getRoleModel()
{
	return $this->getModelFromCache('Teams_Model_Role');
}

}
