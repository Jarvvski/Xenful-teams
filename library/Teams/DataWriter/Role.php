<?php

class Teams_DataWriter_Role extends XenForo_DataWriter
{
	protected function _getFields()
	{
		return array(
			'xf_teams_relations' => array(
				'relation_id' => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
				'team_id' => array('type' => self::TYPE_INT, 'required' => true),
				'user_id' => array('type' => self::TYPE_INT),
				'username' => array('type' => self::TYPE_STRING, 'maxLength' => 240),
				'role_title' => array('type' => self::TYPE_STRING, 'maxLength' => 240, 'required' => true),
				'remark' => array('type' => self::TYPE_STRING, 'maxLength' => 240),
				'admin' => array('type' => self::TYPE_BOOLEAN, 'required' => true, 'default' => 0),
				'mod' => array('type' => self::TYPE_BOOLEAN, 'required' => true, 'default' => 0),
				'hierarchy' => array('type' => self::TYPE_INT, 'required' => true),
				'primary' => array('type' => self::TYPE_BOOLEAN, 'required' => true, 'default' => 1)
			)
		);
	}

	protected function _getExistingData($data)
	{
		if (!$id = $this->_getExistingPrimaryKey($data, 'relation_id'))
		{
			return false;
		}

		return array('xf_teams_relations' => $this->_getRoleModel()->getRoleByIdSimple($id));
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
