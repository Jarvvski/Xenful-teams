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

	public function actionIndex()
	{
		return $this->responseReroute(__CLASS__, 'list');
	}

	public function actionTeamView()
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
		$roles = $this->_getRoleModel()->prepareRoles($roles);

		$viewParams = array(
			'team' => $team,
			'roles' => $roles
		);

		return $this->responseView('Teams_ViewPublic_Team', 'Teams_team_index', $viewParams);
	}

	public function actionTeamCreate()
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

	public function actionTeamEdit()
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

	public function actionTeamDelete()
	{

		// TODO: create assertion for deletion of data
		// $this->_assertCanDeleteTeamData();

		$teamId = $this->_input->filterSingle('team_id', XenForo_Input::UINT);
		$team = $this->_getTeamOrError($teamId);

		if ($this->isConfirmedPost())
		{
			$dw = XenForo_DataWriter::create('Teams_DataWriter_Team');
			$dw->setExistingData($team['team_id']);

			$dw->delete();

			XenForo_Model_Log::logModeratorAction(
				'team', $team, 'removed', array(), $team
			);

			return $this->responseRedirect(
				XenForo_ControllerResponse_Redirect::SUCCESS,
				XenForo_Link::buildPublicLink('teams/org')
			);
		} else {

			$viewParams = array(
				'team' => $team
			);

			return $this->responseView('Teams_ViewPublic_Delete', 'Teams_team_delete', $viewParams);
		}
	}

	public function actionTeamSave()
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

		$dw->bulkSet($input);

		$dw->save();

		$team = $dw->getMergedData();

		return $this->responseRedirect(
			XenForo_ControllerResponse_Redirect::SUCCESS,
			XenForo_Link::buildPublicLink('teams/team-view?' . 'team_id=' . $team['team_id'])
		);
	}

	public function actionDash()
	{
		// TODO: action for displaying user specific team information

		$user = XenForo_Visitor::getInstance()->toArray();

		$primaryRole = $this->_getRoleModel()->getPrimaryRoleByUser($user);

		if (!$primaryRole) {
			$primaryTeam = array();
		} else {
			$primaryTeam = $this->_getRoleModel()->getTeamByRole($primaryRole);
			$this->_getTeamModel()->prepareTeam($primaryTeam);
		}

		$otherTeams = $this->_getTeamModel()->getNotPrimaryTeamsByUser($user);

		$viewParams = array(
			'primaryTeam' => $primaryTeam,
			'primaryRole' => $primaryRole,
			'otherTeams' => $otherTeams
		);

		return $this->responseView('Teams_ViewPublic_Dash', 'Teams_dash', $viewParams);
	}

	public function actionList()
	{
		$teams = array();

		$bases = $this->_getTeamModel()->getBaseTeams();

		foreach($bases as $base)
		{
			$this->sortChildren($base, $teams);
		}

		$viewParams = array(
			'teams' => $teams
		);

		return $this->responseView('Teams_ViewPublic_index', 'Teams_index', $viewParams);
	}

	public function sortChildren(array $team, array &$teams)
	{
		$team = $this->_getTeamModel()->prepareTeam($team);
		array_push($teams, $team);
		$children = $this->_getTeamModel()->getChildren($team);

		if (!empty($children))
		{
			foreach($children as $child)
			{
				$this->sortChildren($child, $teams);
			}
		}
		return $teams;
	}

	public function actionOrg()
	{
		$viewParams = array();

		return $this->responseView('Teams_ViewPublic_Org', 'Teams_org_index', $viewParams);
	}

	public function actionOrgJson()
	{
		$data = array(
			'cols' => array(),
			'rows' => array()
		);

		$data['cols'] = array(
			0 => array(
				'id' => '',
				'label' => 'Name',
				'pattern' => '',
				'type' => 'string',
			),
			1 => array(
				'id' => '',
				'label' => 'Parent',
				'pattern' => '',
				'type' => 'string',
			)
		);

		$teams = $this->_getTeamModel()->getAllTeams();

		foreach ($teams as $team)
		{
			$row = $this->_getTeamModel()->prepareTeamForOrg($team);
			array_push($data['rows'], $row);
		}

		$this->_routeMatch->setResponseType('json');
		return $this->responseView('Teams_ViewPublic_Org_Json', '', $data);
	}

	public function actionRoleView()
	{
		$roleId = $this->_input->filterSingle('role_id', XenForo_Input::UINT);

		$role = $this->_getTeamRoleOrError($roleId);

		$viewParams = array(
			'role' => $role
		);

		return $this->responseView('Teams_ViewPublic_Role', 'Teams_role_index', $viewParams);
	}

	public function actionRoleCreate()
	{
		$teamModel = $this->_getTeamModel();
		$teams = $teamModel->getAllTeams();

		$role = array(
			'role_id' => 0,
			'managed_team_ids' => array(),
			'assigned_date' => XenForo_Locale::date(XenForo_Application::$time, 'picker')
		);

		$viewParams = array(
			'role' => $role,
			'teams' => $teams
		);

		return $this->responseView('Teams_ViewPublic_EditRole', 'Teams_edit_role', $viewParams);
	}

	public function actionRoleEdit()
	{
		$roleId = $this->_input->filterSingle('role_id', XenForo_Input::UINT);
		$role = $this->_getTeamRoleOrError($roleId);
		$teams = $this->_getTeamModel()->getAllTeams();

		$role = $this->_getRoleModel()->prepareRole($role);

		$viewParams = array(
			'role' => $role,
			'teams' => $teams
		);

		return $this->responseView('Teams_ViewPublic_EditRole', 'Teams_edit_role', $viewParams);
	}

	public function actionRoleDelete()
	{

		// TODO: create assertion for deletion of data
		// $this->_assertCanDeleteTeamData();

		$roleId = $this->_input->filterSingle('role_id', XenForo_Input::UINT);
		$role = $this->_getTeamRoleOrError($roleId);

		if ($this->isConfirmedPost())
		{
			$dw = XenForo_DataWriter::create('Teams_DataWriter_Role');
			$dw->setExistingData($role['role_id']);

			$dw->delete();

			XenForo_Model_Log::logModeratorAction(
				'role', $role, 'removed', array(), $role
			);

			return $this->responseRedirect(
				XenForo_ControllerResponse_Redirect::SUCCESS,
				XenForo_Link::buildPublicLink('teams/org')
			);
		} else {

			$viewParams = array(
				'role' => $role
			);

			return $this->responseView('Teams_ViewPublic_Delete', 'Teams_role_delete', $viewParams);
		}
	}

	public function actionRoleSave()
	{
		$this->_assertPostOnly();

		$roleId = $this->_input->filterSingle('role_id', XenForo_Input::UINT);

		// TODO: specify input items from HTML form items
		$input = $this->_input->filter(array(
			'team_id' => XenForo_Input::UINT,
			'role_title' => XenForo_Input::STRING,
			'abreviation' => XenForo_Input::STRING,
			'remark' => XenForo_Input::STRING,
			'username' => XenForo_Input::STRING,
			'hierarchy' => XenForo_Input::UINT,
			'managed_team_ids' => array(XenForo_Input::UINT, 'array' => true),
			'primary' => XenForo_Input::BOOLEAN,
			'assigned_date' => XenForo_Input::STRING
		));

		// TODO: check if user has perm to manage role for given team

		$dw = XenForo_DataWriter::create('Teams_DataWriter_Role');

		if ($roleId)
		{
			$dw->setExistingData($roleId);
		}

		if ($input['username'])
		{
			$user = $this->_getUserOrError($input);
			$input['user_id'] = $user['user_id'];
			$assignedDate = new DateTime("$input[assigned_date]");
			$input['assigned_date'] = $assignedDate->format('U');
		}

		$dw->bulkSet($input);

		$dw->save();

		$role = $dw->getMergedData();

		return $this->responseRedirect(
			XenForo_ControllerResponse_Redirect::SUCCESS,
			XenForo_Link::buildPublicLink('teams/team-view?team_id='.$role['team_id'])
		);
	}

	public function actionBlueprintCreate()
	{
		// TODO: create blueprint
	}

	public function actionBlueprintEdit()
	{
		// TODO: edit blueprint
	}

	public function actionBlueprintDelete()
	{
		// TODO: delete blueprint
	}

	public function actionBlueprintSave()
	{
		$this->_assertPostOnly();

		$id = $this->_input->filterSingle('id', XenForo_Input::UINT);

		// TODO: specify input items from HTML form items
		$input = $this->_input->filter(array(
			'title' => XenForo_Input::STRING,
			'abreviation' => XenForo_Input::STRING,
			'remark' => XenForo_Input::STRING,
			'primary' => XenForo_Input::BOOLEAN,
		));

		// TODO: check if user has perm to manage role for given team

		$dw = XenForo_DataWriter::create('Teams_DataWriter_BluePrint');

		if ($id)
		{
			$dw->setExistingData($id);
		}

		$input['user_id'] = XenForo_Visitor::getUserId();

		$dw->bulkSet($input);

		$dw->save();

		$blueprint = $dw->getMergedData();

		return $this->responseRedirect(
			XenForo_ControllerResponse_Redirect::SUCCESS,
			XenForo_Link::buildPublicLink('teams/dash')
		);
	}
}
