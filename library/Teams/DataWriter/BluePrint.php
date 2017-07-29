<?php

class Teams_DataWriter_BluePrint extends XenForo_DataWriter
{
	protected function _getFields()
	{
		return array(
			'xf_teams_blueprint_role' => array(
				'id' => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
				'title' => array('type' => self::TYPE_STRING, 'required' => true, 'maxLength' => 200),
				'remark' => array('type' => self::TYPE_STRING, 'maxLength' => 400),
				'abreviation' => array('type' => self::TYPE_STRING, 'maxLength' => 4, 'required' => true),
				'primary' => array('type' => self::TYPE_BOOLEAN, 'required' => true, 'default' => 1),
				'user_id' => array('type' => self::TYPE_UINT, 'required' => true),
			)
		);
	}

	protected function _getExistingData($data)
	{
		if (!$id = $this->_getExistingPrimaryKey($data, 'id'))
		{
			return false;
		}

		return array('xf_teams_blueprint_role' => $this->_getRoleModel()->getBluePrintByIdSimple($id));
	}

	protected function _getUpdateCondition($tableName)
	{
		return 'id = ' . $this->_db->quote($this->getExisting('id'));
	}

	protected function _getRoleModel()
	{
		return $this->getModelFromCache('Teams_Model_Role');
	}
}
