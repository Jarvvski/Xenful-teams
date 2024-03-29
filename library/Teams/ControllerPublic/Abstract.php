<?php

class Teams_ControllerPublic_Abstract extends XenForo_ControllerPublic_Abstract
{
	/**
	*	Checks if user has perms
	*
	* @param  [type] $action [description]
	* @return [type]         [description]
	*/
	protected function _preDispatchFirst($action)
	{
		// TODO: commented out for debug
		// if (XenForo_Application::get('options')->XT_disableTeams)
		// {
		// 	throw $this->responseException($this->responseError(XenForo_Application::get('options')->XT_DDMessage));
		// }
		//
		// if (!$this->_getTeamModel()->canViewTeams($errorPhraseKey))
		// {
		// 	throw $this->getErrorOrNoPermissionResponseException($errorPhraseKey);
		// }
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

		return $role;
	}

	/**
	* Gets a user via a user array containing either the username index
	* or the user_id index
	*
	* @param  array $input an incomplete user record
	* @return array        a complete user record
	*/
	protected function _getUserOrError(array $input)
	{
		if ($input['username'])
		{
			$user = $this->_getUserModel()->getUserByName($input['username']);

			if (!empty($user))
			{
				return $user;
			}

			$errorData = $input['username'];
		}

		if ($input['user_id'])
		{
			$user = $this->_getUserModel()->getUserById($input['user_id']);

			if (!empty($user))
			{
				return $user;
			}

			$errorData = $input['user_id'];
		}

		throw $this->responseException($this->responseError(new XenForo_Phrase(
			'requested_user_x_not_found', array('name' => $errorData)
		)));
	}

	protected function _assertCanManageTeams()
	{
		// TODO: build functions to assert management of given team
		// if (!$this->_getTeamModel()->canManageTeam($errorPhraseKey))
		// {
		// 	throw $this->getErrorOrNoPermissionResponseException($errorPhraseKey);
		// }
	}

	protected function _assertCanManageTeamUsers()
	{
		// TODO: build functions to assert management of given users in given team
		// XenForo_Visitor::getInstance()->hasPermission('CavToolsGroupId', 'sendAWOLPM')
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
