<?php

class Teams_ControllerPublic_Dashboard extends Teams_ControllerPublic_Abstract
{

	public function actionIndex()
	{
        // If cannot view, escape
        // and save on team load
        if (!XenForo_Visitor::getInstance()->hasPermission('XenFullTeams', 'viewDash'))
        {
            throw $this->getNoPermissionResponseException();
        }

		return $this->responseReroute(__CLASS__, 'dashboard');
	}

	public function actionDashboard()
	{
		// TODO: display dashboard for user
		// Dash will display lists of all current teams
		// + allow team managers to admin their teams

		// TODO: check for team admin or mod perms
		if (!XenForo_Visitor::getInstance()->hasPermission('XenFullTeams', 'teamAdmin'))
		{
			// don't show admin buttons
		}

		if (!XenForo_Visitor::getInstance()->hasPermission('XenFullTeams', 'teamMod'))
		{
			// don't show mod buttons
		}

		// Show primary team, then show all other teams in heirarchy order

		// TODO: display team stuff
		// 1. Find out visitor primary team
		// 2. Get all other visitor teams
		// 3. Get teams except for this team
		// 4. Display

		$visitor = XenForo_Visitor::getInstance()->toArray();

		$teamModel = $this->_getTeamModel();

		// TODO: perhaps only show teams user can modify? As is 'dashboard'?
		// Have 'Display' controller show all teams for everyone. Less stress
		// on db + PHP sorting of large team dataset + admin tools specific to
		// given visitor

		$primaryTeam = $teamModel->getPrimaryTeamByUser($visitor['user_id']);
		$otherTeams = $teamModel->getNotPrimaryTeamsByUser($visitor['user_id']);
		$teams = $teamModel->getNonUserTeams($visitor['user_id']);

		$viewParams = array(
			'primary_team' => $primary_team,
			'other_teams' => $othereams,
			'teams' => $teams
		);

		return $this->responseView('Teams_ViewPublic_dashboard', 'Teams_dashboard', $viewParams);
	}
}
