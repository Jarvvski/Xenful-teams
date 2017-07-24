<?php

class Teams_ControllerPublic_Team extends Teams_ControllerPublic_Abstract
{

	public function actionView()
	{
		$teamModel = $this->_getTeamModel();
		$roleModel = $this->_getRoleModel();

		if (!$teamModel->canViewTeam())
		{
			return $this->responseNoPermission();
		}

		$teamId = $this->_input->filterSingle('team_id', XenForo_Input::UINT);

		$team = $this->_getTeamOrError($teamId);
		$roles = $teamModel->getRolesByTeam($team);

		$viewParams = array(
			'team' => $team,
			'roles' => $roles,
			'canAdminTeam' => $teamModel->canAdminTeam($team);
			'canModTeam' => $teamModel->canModTeam($team);
		);

		return $this->responseView('Teams_ViewPublic_Team', 'Teams_team_index', $viewParams);
	}

	public function actionCreate()
	{
		// TODO create assertion function
		$this->_assertCanAdminTeam();

		// TODO specify any variables that need to be in team creation
		$viewParams = array(
			'key' => $var
		);

		return $this->responseView('Teams_ViewPublic_EditTeam', 'Teams_edit_team', $viewParams);
	}

	public function actionEdit()
	{
		$this->_assertCanAdminTeam();

		$teamId = $this->_input->filterSingle('team_id', XenForo_Input::UINT);
		$team = $this->_getTeamOrError($teamId);

		$viewParams = array(
			'team' => $team
		);

		return $this->responseView('Teams_ViewPublic_EditTeam', 'Teams_edit_team', $viewParams);
	}

	public function actionDelete()
	{
		$this->_assertCanDeleteTeamData();

		if ($this->isConfirmedPost())
		{
			return $this->_deleteData(
				'Teams_DataWriter_Team', 'team_id',
				XenForo_Link::buildPublicLink('teams')
			);
		} else {
			$teamId = $this->_input->filterSingle('team_id', XenForo_Input::UINT);
			$team = $this->_getTeamOrError($teamId);

			$viewParams = array(
				'team' => $team
			);

			return $this->responseView('Teams_ViewPublic_Delete', 'Teams_delete', $viewParams);
		}
	}

	public function actionSave()
	{
		$this->_assertPostOnly();

		$teamId = $this->_input->filterSingle('team_id', XenForo_Input::UINT);

		// TODO specify input items from html form items
		$input = $this->_input->filter(array(
			'key1' => XenForo_Input::STRING,
			'key2' => XenForo_Input::UNUM,
			'key3' => XenForo_Input::UINT
		));

		$dw = XenForo_DataWriter::create('Teams_DataWriter_Team');

		if ($teamId)
		{
			$dw->setExistingData($teamId);
		}

		// TODO set DW data specific to team

		$dw->save();

		$team = $dw->getMergedData();

		return $this->responseRedirect(
			XenForo_ControllerResponse_Redirect::SUCCESS,
			XenForo_Link::buildPublicLink('teams/view') . $this->getLastHash($team['team_id'])
		);
	}
}
