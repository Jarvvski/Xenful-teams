<?php

class Teams_Route_Prefix_Team implements XenForo_Route_Interface
{
	public function match($routePath, Zend_Controller_Request_Http $request, XenForo_Router $router)
	{
		return $router->getRouteMatch('Teams_ControllerPublic_Team', $routePath, 'forums');
	}
}
