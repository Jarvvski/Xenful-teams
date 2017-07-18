<?php

class Teams_ControllerPublic_Dashboard extends Teams_ControllerPublic_Abstract
{

	public function actionIndex()
	{
		return $this->responseReroute(__CLASS__, 'dashboard');
	}

	public function actionDashboard()
	{
		// TODO display dashboard for user
	}

	public function actionCreate()
	{
		// TODO create team
	}

	public function actionEdit()
	{
		// TODO edit team
	}

	public function actionDelete()
	{
		// TODO delete team
	}

	public function actionSave()
	{
		// TODO save data
	}



}
