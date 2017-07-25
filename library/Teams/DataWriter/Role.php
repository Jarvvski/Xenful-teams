<?php

class Teams_DataWriter_Role extends XenForo_DataWriter
{
	protected function _getFields()
	{
		return array(
			'xf_teams_relations' => array(
				'relation_id' => array('type' => self::TYPE_UINT, 'autoIncrement' =>),
				'team_id' => array('type' => self::TYPE_INT),
				'user_id' => array('type' => self::TYPE_INT),
				'username' => array('type' => self::TYPE_STRING),
				'role_title' => array('type' => self::TYPE_STRING),
				'remark' => array('type' => self::TYPE_STRING),
				'admin' => array('type' => self::TYPE_BOOL),
				'mod' => array('type' => self::TYPE_BOOL),
				'hierarchy' => array('type' => self::TYPE_INT)
				'primary' => array('type' => self::TYPE_BOOL)
			)
		);
	}

	protected function _getExistingData($data)
	{
		if (!$id = $this->_getExistingPrimaryKey($data, 'relation_id'))
		{
			return false;
		}

		return array('xf_teams_relations' => $this->_getRoleModel()->$getRoleById($id));
	}

	protected function _getUpdateCondition($tableName)
	{
		return 'relation_id = ' . $this->_db->quote($this->getExisting('relation_id'));
	}

	protected function _getRoleModel()
	{
		return $this->getModelFromCache('Teams_Model_Role');
	}

}
