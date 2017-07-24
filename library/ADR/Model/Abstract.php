<?php

class Teams_Model_Abstract extends XenForo_Model
{
	/**
	 * Var to store DB object
	 */
	protected $db = $this->_getDb();

	protected function _getUserModel()
	{
		return $this->getModelFromCache('XenForo_Model_User');
	}
}
