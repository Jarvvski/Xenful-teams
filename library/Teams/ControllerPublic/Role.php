<?php

class Teams_ControllerPublic_Role extends Teams_ControllerPublic_Abstract
{

	public function actionView()
	{
		$roleId = $this->_input->filterSingle('role_id', XenForo_Input::UINT);

		$role = $this->_getTeamRoleOrError($roleId);

		$viewParams = array(
			'role' => $role
		);

		return $this->responseView('Teams_ViewPublic_Role', 'Teams_role_index', $viewParams);
	}

	public function actionCreate()
	{
		$teamModel = $this->_getTeamModel();
		$teams = $teamModel->getAllTeams();

		$role = array(
			'role_id' => 0,
			'managed_team_ids' => array()
		);

		$viewParams = array(
			'role' => $role,
			'teams' => $teams
		);

		return $this->responseView('Teams_ViewPublic_EditRole', 'Teams_edit_role', $viewParams);
	}

	public function actionEdit()
	{
		$roleId = $this->_input->filterSingle('role_id', XenForo_Input::UINT);
		$role = $this->_getTeamRoleOrError($roleId);
		$teamModel = $this->_getTeamModel();
		$teams = $teamModel->getAllTeams();

		$role['managed_team_ids'] = explode(',', $role['managed_team_ids']);
		$role['managed_team_ids'] = array_filter($role['managed_team_ids']);

		$viewParams = array(
			'role' => $role,
			'teams' => $teams
		);

		return $this->responseView('Teams_ViewPublic_EditRole', 'Teams_edit_role', $viewParams);
	}

	public function actionDelete()
	{
		$this->_assertCanDeleteTeamData();

		if ($this->isConfirmedPost())
		{
			return $this->_deleteData(
				'Teams_DataWriter_Role', 'role_id',
				XenForo_Link::buildPublicLink('teams')
			);
		} else {
			$roleId = $this->_input->filterSingle('role_id', XenForo_Input::UINT);
			$role = $this->_getTeamRoleOrError($roleId);

			$viewParams = array(
				'role' => $role
			);

			return $this->responseView('Teams_ViewPublic_Delete', 'Teams_delete', $viewParams);
		}
	}

	public function actionSave()
	{
		$this->_assertPostOnly();

		$roleId = $this->_input->filterSingle('role_id', XenForo_Input::UINT);

		// TODO: specify input items from HTML form items
		$input = $this->_input->filter(array(
			'team_id' => XenForo_Input::UINT,
			'role_title' => XenForo_Input::STRING,
			'remark' => XenForo_Input::STRING,
			'username' => XenForo_Input::STRING,
			'hierarchy' => XenForo_Input::UINT,
			'managed_team_ids' => array(XenForo_Input::UINT, 'array' => true),
			'primary' => XenForo_Input::BOOLEAN
		));

		// TODO: check if user has perm to manage role for given team

		$dw = XenForo_DataWriter::create('Teams_DataWriter_Role');

		if ($roleId)
		{
			$dw->setExistingData($roleId);
		}

		if ($input['username'])
		{
			$user = $this->getModelFromCache('XenForo_Model_User')->getUserByName($input['username']);
			$input['user_id'] = $user['user_id'];
		}

		$dw->bulkSet($input);

		$dw->save();

		$role = $dw->getMergedData();

		return $this->responseRedirect(
			XenForo_ControllerResponse_Redirect::SUCCESS,
			XenForo_Link::buildPublicLink('team/view?team_id='.$role['team_id'])
		);
	}
}
