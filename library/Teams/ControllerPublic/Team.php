<?php

class Teams_ControllerPublic_Team extends Teams_ControllerPublic_Abstract
{

	protected function _preDispatch($action)
	{
		// TODO: Chekc if we can abstract out models to this level?
		// $teamModel = $this->_getTeamModel();
		// $roleModel = $this->_getRoleModel();

		switch($action)
		{
			case 'view':
				// code to be run before actionView()
				break;
			case 'edit':
				// code to be run before actionEdit()
				break;
		}
	}

	public function actionView()
	{
		$teamModel = $this->_getTeamModel();
		$roleModel = $this->_getRoleModel();

		// if (!$teamModel->canViewTeam())
		// {
		// 	return $this->responseNoPermission();
		// }

		$teamId = $this->_input->filterSingle('team_id', XenForo_Input::UINT);

		$team = $this->_getTeamOrError($teamId);
		$roles = $teamModel->getRolesByTeam($team);

		$viewParams = array(
			'team' => $team,
			'roles' => $roles
		);

		return $this->responseView('Teams_ViewPublic_Team', 'Teams_team_index', $viewParams);
	}

	public function actionCreate()
	{
		// TODO: create assertion function
		// $this->_assertCanAdminTeam();

		$teamModel = $this->_getTeamModel();
		$teams = $teamModel->getAllTeams();

		// TODO: specify any variables that need to be in team creation
		$viewParams = array(
			'parents' => $teams
		);

		return $this->responseView('Teams_ViewPublic_EditTeam', 'Teams_edit_team', $viewParams);
	}

	public function actionEdit()
	{
		// TODO: create assertion function
		// $this->_assertCanAdminTeam();

		$teamId = $this->_input->filterSingle('team_id', XenForo_Input::UINT);
		$team = $this->_getTeamOrError($teamId);

		$teamModel = $this->_getTeamModel();
		$teams = $teamModel->getAllTeams();

		$viewParams = array(
			'parents' => $teams,
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

		// TODO: specify input items from html form items
		$input = $this->_input->filter(array(
			'team_name' => XenForo_Input::STRING,
			'team_remark' => XenForo_Input::STRING,
			'can_extend' => XenForo_Input::BOOLEAN,
			'hierarchy' => XenForo_Input::UINT,
			'parent_id' => XenForo_Input::UINT
		));

		$dw = XenForo_DataWriter::create('Teams_DataWriter_Team');

		if ($teamId)
		{
			$dw->setExistingData($teamId);
		}

		// TODO: set DW data specific to team

		$dw->set('team_name', $input['team_name']);
		$dw->set('team_remark', $input['team_remark']);
		$dw->set('can_extend', $input['can_extend']);
		$dw->set('hierarchy', $input['hierarchy']);
		$dw->set('parent_id', $input['parent_id']);
		$dw->save();

		$team = $dw->getMergedData();

		return $this->responseRedirect(
			XenForo_ControllerResponse_Redirect::SUCCESS,
			XenForo_Link::buildPublicLink('team/view?' . 'team_id=' . $team['team_id'])
		);
	}
}
