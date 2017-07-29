<?php

class Teams_ViewPublic_Org_Json extends XenForo_ViewPublic_Base
{
	public function renderJson()
	{
		return json_encode($this->_params);
	}
}
