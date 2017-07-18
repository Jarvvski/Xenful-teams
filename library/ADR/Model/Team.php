<?php

class Teams_Model_Team extends XenForo_Model
{

	$db = $this->_getDb();

	/**
	 * Get a single team for a given teamID
	 *
	 * @param  int $teamId
	 * @return array/data obj
	 */
	public function getTeamByIdSimple($teamId)
	{
		return $this->$db->fetchRow('
			SELECT *
			FROM xf_teams_teams
			WHERE team_id = ' .$this->$db->quote($teamId) .'
		');
	}

	/**
	 * Gets an array of teams from an array of teamIDs
	 *
	 * @param  array  $teamIds collection of teamIDs (ints)
	 * @return array  Collection of Teams with corresponding data
	 */
	public function getTeamsByID(array $teamIds)
	{
		if (!$teamIds)
		{
			return array();
		}

		return $this->fetchAllKeyed('
			SELECT *
			FROM xf_teams_teams
			WHERE team_id IN (' .$this->$db->quote($teamIds) .')
		', 'team_id');
	}

	/**
	 * Gets all current Teams on the ADR
	 *
	 * @return array  Collection of all team Data
	 */
	public function getAllTeams()
	{
		return $this->fetchAll('
			SELECT *
			FROM xf_teams_teams
		');
	}

	/**
	 * Prepare Team for sending and add to cache
	 * @param  array/data Obj  $team
	 * @return array  team prepared
	 */
	public function prepareTeam($team)
	{
		if (!isset($team['fieldCache'])) {
			$team['fieldCache'] = @unserialize($team['field_cache']);

			if (!is_array($team['fieldCache'])) {
				$team['fieldCache'] = array();
			}
		}
		return $team;
	}

	/**
	 * Get team members via the team they are in
	 *
	 * @param  int  $teamId
	 * @return array  Users contained in relation of given team
	 */
	public function getTeamMembersByTeamId($teamId)
	{
		// TODO might need to include roles for each member
		// - maybe sepperate function

		return $this->fetchAllKeyed('
			SELECT relation.*, user.username,
				user.avatar_date, user.gravatar
			FROM xf_teams_relation AS relation
			INNER JOIN xf_user AS user ON (user.user_id = relation.user_id)
			WHERE relation.team_id = ' . $this->$db->quote($teamId). '
			ORDER BY user.username
		', 'user_id');
	}

	/**
	 * Gets roles availible in team
	 *
	 * @param  [type] $teamId [description]
	 * @return [type]         [description]
	 */
	public function getRolesByTeamId($teamId)
	{
		// TODO get all roles in team
	}

	/**
	 * [getRelationsByTeamId description]
	 * @param  [type] $teamId [description]
	 * @return [type]         [description]
	 */
	public function getRelationsByTeamId($teamId)
	{
		// TODO get all relations from team ID
	}

	/**
	 * Delets a user from a team via removal of relation
	 * @param  int $teamId
	 * @return [type]         [description]
	 */
	public function deleteTeamUser($teamId)
	{
		$relations = $this->getRelationsByTeamId($teamId);

		foreach ($relations as $relationID => $relation)
		{
			// TODO create relation DW
			$relationDw = XenForo_DataWriter::create('ADR_DataWriter_TeamUserRelation');
			$relationDw->setExistingData($userID);
			$relationDw->delete();
		}
	}

	/**
	 * Add a user to a team by building a relation
	 *
	 * @param int $teamId
	 * @param int $userId
	 */
	public function addTeamUser($teamId, $userId)
	{
		// TODO add user to team via relation
	}

	/**
	 * Gets the primary team for a given user
	 *
	 * @param int  $userId
	 * @return array team data
	 */
	public function getPrimaryTeamByUser($userId)
	{
		return $this->$db->fetchRow('
			SELECT teams.*
			FROM xf_teams_teams AS teams
			INNER JOIN xf_teams_relation AS relation
			ON relation.id = teams.relation_id
			WHERE relation.user_id = '.$this->$db->quote($userId).'
			AND relation.primary = TRUE
		');
	}

	/**
	 * Gets all the non-primary teams a given user
	 *
	 * @param  int  $userId
	 * @return array of team arrays (2d)
	 */
	public function getNotPrimaryTeamsByUser($userId)
	{
		return $this->fetchAllKeyed('
			SELECT teams.*
			FROM xf_teams_teams AS teams
			INNER JOIN xf_teams_relation AS relation
			ON relation_id = teams.relation_id
			WHERE relation.user_id = '.$this->$db->quote($userId).'
			AND relation.primary = FALSE
		');
	}

	/**
	 * Gets all teams except for those of a given user
	 *
	 * @param  [type] $userId [description]
	 * @return [type]         [description]
	 */
	public function getNonUserTeams($userId)
	{
		return $this->fetchAllKeyed('
			SELECT teams.*
			FROM xf_teams_teams AS teams
			INNER JOIN xf_teams_relation AS relation
			on relation_id = teams.relation_id
			WHERE relation.user_id != '.$this->$db->quote($userId).'
		');
	}




}
