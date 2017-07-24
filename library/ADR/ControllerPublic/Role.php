<?php

class Teams_ControllerPublic_Role extends Teams_ControllerPublic_Abstract
{
	public function actionIndex()
	{
		return $this->responseReroute(__CLASS__, 'list');
	}

	public function actionList()
	{
		// TODO list roles
	}

	public function actionEdit()
	{
		// TODO edit role
	}

	public function actionDelete()
	{
		// TODO delete role
	}

	public function actionSave()
	{
		// TODO Save role data
	}
}
