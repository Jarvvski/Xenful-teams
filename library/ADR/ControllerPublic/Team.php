<?php

class Teams_ControllerPublic_Team extends Teams_ControllerPublic_Abstract {

	public function actionIndex()
	{
		return $this->responseReroute(__CLASS__, 'team');
	}

	public function actionTeam()
	{
		// TODO display team info for user
	}

	public function actionCreate()
	{
		// TODO create role
	}

	public function action()
	{
		// TODO assign users to role
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
		// TODO save data
	}
}
