<?php

class Teams_ControllerPublic_Abstract extends XenForo_ControllerPublic_Abstract
{
	/**
	 *	Checks if user has perms
	 *
	 * @param  [type] $action [description]
	 * @return [type]         [description]
	 */
	protected function _preDispatch($action)
	{
		if (!$this->_getTeamModel()->canViewTeams($errorPhraseKey))
		{
			throw $this->getErrorOrNoPermissionResponseException($errorPhraseKey);
		}
	}

	/**
	 * Gets the team or error
	 *
	 * @param  int $teamId
	 * @return [type]         [description]
	 */
	protected function _getTeamOrError($teamId) {
		$team = $this->_getTeamModel()->getTeamByIdSimple($teamId);

		if (!$team) {
			throw $this->responseException($this->responseError(new XenForo_Phrase('teams_team_not_found'), 404));
		}

		return $this->_getTeamModel()->prepareTeam($team);
	}

	/**
	 * Gets the team role or error
	 * @param  int $roleId
	 * @return [type]         [description]
	 */
	protected function _getTeamRoleOrError($roleId)
	{
		$role  = $this->_getRoleModel()->getRoleByIdSimple($roleId);

		if (!$role) {
			throw $this->responseException($this->responseError(new XenForo_Phrase('teams_role_not_found'), 404));
		}

		return $this->_getRoleModel()->prepareRole($role);
	}

	protected function _assertCanManageTeams()
	{
		// TODO build functions to assert management of given team
		// if (!$this->_getTeamModel()->canManageTeam($errorPhraseKey))
		// {
		// 	throw $this->getErrorOrNoPermissionResponseException($errorPhraseKey);
		// }
	}

	protected function _assertCanManageTeamUsers()
	{
		// TODO build functions to assert management of given users in given team
	}

	/**
	 * @return Teams_Model_Team
	 */
	protected function _getTeamModel()
	{
		return $this->getModelFromCache('Teams_Model_Team');
	}

	/**
	 * @return Teams_Model_Role
	 */
	protected function _getRoleModel()
	{
		return $this->getModelFromCache('Teams_Model_Role');
	}

	/**
	 * @return XenForo_Model_User
	 */
	protected function _getUserModel()
	{
		return $this->getModelFromCache('XenForo_Model_User');
	}

	/**
	 * @return XenForo_Model_UserGroup
	 */
	protected function _getUserGroupModel()
	{
		return $this->getModelFromCache('XenForo_Model_UserGroup');
	}

	/**
	 * @return XenForo_ControllerHelper_Editor
	 */
	protected function _getEditorHelper()
	{
		return $this->getHelper('Editor');
	}
}
