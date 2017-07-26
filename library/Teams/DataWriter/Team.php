<?php

class Teams_DataWriter_Team extends XenForo_DataWriter
{
	protected function _getFields()
	{
		return array(
			'xf_teams_teams' => array(
				'team_id' => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
				'team_name' => array('type' => self::TYPE_STRING, 'required' => true, 'maxLength' => 200),
				'team_remark' => array('type' => self::TYPE_STRING, 'maxLength' => 240),
				'managed_teams' => array('type' => self::TYPE_UNKNOWN, 'default' => ''),
				'team_roles' => array('type' => self::TYPE_UNKNOWN, 'default' => ''),
				'can_extend' => array('type' => self::TYPE_BOOLEAN, 'default' => 0, 'required' => true),
				'hierarchy' => array('type' => self::TYPE_UINT, 'required' => true),
				'parent_id' => array('type' => self::TYPE_UINT, 'required' => true)
			)
		)
	}

	protected function _getExistingData($data)
	{
		if (!$id = $this->_getExistingPrimaryKey($data, 'team_id'))
		{
			return false;
		}

		return array('xf_teams_teams' => $this->_getTeamModel()->getTeamByIdSimple($id));
	}

	protected function _getUpdateCondition($tableName)
	{
		return 'team_id = ' . $this->_db->quote($this->getExisting('team_id'));
	}

	protected function _getTeamModel()
	{
		return $this->getModelFromCache('Teams_Model_Team');
	}
}
