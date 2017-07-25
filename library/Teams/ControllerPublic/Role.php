<?php

class Teams_ControllerPublic_Role extends Teams_ControllerPublic_Abstract
{

	public function actionView()
	{
		$relationId = $this->_input->filterSingle('relation_id', XenForo_Input::UINT);

		$role = $this->_getTeamRoleOrError($relationId);

		$viewParams = array(
			'role' => $role
		);

		return $this->responseView('Teams_ViewPublic_Role', 'Teams_role_index', $viewParams);
	}

	public function actionCreate()
	{
		// TODO specify any variables needed during role creation
		$viewParams = array(
			'key' => $var
		);

		return $this->responseView('Teams_ViewPublic_EditRole', 'Teams_edit_role', $viewParams);
	}

	public function actionEdit()
	{
		$relationId = $this->_input->filterSingle('relation_id', XenForo_Input::UINT);
		$role = $this->_getTeamRoleOrError($relationId);

		$viewParams = array(
			'role' => $role
		);

		return $this->responseView('Teams_ViewPublic_EditRole', 'Teams_edit_role', $viewParams);
	}

	public function actionDelete()
	{
		$this->_assertCanDeleteTeamData();

		if ($this->isConfirmedPost())
		{
			return $this->_deleteData(
				'Teams_DataWriter_Role', 'relation_id',
				XenForo_Link::buildPublicLink('teams')
			);
		} else {
			$relationId = $this->_input->filterSingle('relation_id', XenForo_Input::UINT);
			$role = $this->_getTeamRoleOrError($relationId);

			$viewParams = array(
				'role' => $role
			);

			return $this->responseView('Teams_ViewPublic_Delete', 'Teams_delete', $viewParams);
		}
	}

	public function actionSave()
	{
		$this->_assertPostOnly();

		$relationId = $this->_input->filterSingle('relation_id', XenForo_Input::UINT);

		// TODO specify input items from HTML form items
		$input = $this->_input->filter(array(
			'key1' => XenForo_Input::STRING,
			'key2' => XenForo_Input::UNUM,
			'key3' => XenForo_Input::UINT
		));

		$dw = XenForo_DataWriter::create('Teams_DataWriter_Role');

		if ($relationId)
		{
			$dw->setExistingData($relationId);
		}

		// TODO need to get the user we're going to add to the role
		// - get user_id
		// - get username
		// - store if needed/else null

		$dw->set('team_id', $input['team_id']);
		$dw->set('user_id', $roleUser['user_id'];
		$dw->set('username', $input['username']);
		$dw->set('role_title', $input['role_title']);
		$dw->set('remark', $input['remark']);
		$dw->set('admin', false);
		$dw->set('mod', false);
		$dw->set('hierarchy', $input['hierarchy']);
		$dw->set('primary', true);

		$dw->save();

		$role = $dw->getMergedData();

		return $this->responseRedirect(
			XenForo_ControllerResponse_Redirect::SUCCESS,
			XenForo_Link::buildPublicLink('teams/view') . $this->getLastHash($role['team_id'])
		);
	}
}
