<?php

class Teams_Model_Abstract extends XenForo_Model
{
	// protected static $db = $this->_getDb();

	protected function _getUserModel()
	{
		return $this->getModelFromCache('XenForo_Model_User');
	}

	protected function _getTeamModel()
	{
		return $this->getModelFromCache('Teams_Model_Team');
	}

	protected function _getRoleModel()
	{
		return $this->getModelFromCache('Teams_Model_Role');
	}
}
