<?
/****************************************
 * HELPER FUNCTIONS						*
 ***************************************/

/**
 * Retrieve currently logged-in User's New Media 2012 porfolio.
 *
 * Returns null if not found, the Portfolio object otherwise.
 */
function getNMDPortfolio()
{
	$ports = PortfolioController::getOwnedPortfolios();
	$nmd_port = null;
	foreach ($ports as $p)
	{
		if ($p->title == "New Media Freshman Portfolio 2012")
		{
			$nmd_port = $p;
			break;
		}
	}
	
	return $nmd_port;
}

/**
 * Retrieve the AssignmentInstance for the New Media 2012 protfolio submissions.
 */
function getNMDAssignmentInstance()
{
	$instance = AssignmentController::viewAssignmentInstance(1);
	return $instance;
}

/**
 * Check whether or not the currently logged-in User's New Media 2012 portfolio has been submitted.
 *
 * Returns true if submitted, false otherwise.
 */
function portfolioIsSubmitted()
{
	$instance = getNMDAssignmentInstance();
	$port = getNMDPortfolio();
	foreach ($instance->children as $child_id=>$arr)
	{
		if ($child_id == $port->id())
		{
			return true;
		}
	}
	return false;
}

/**
 * Helper to redirect the User to a location with the webroot appended
 */
function redirect($destination)
{
	$GLOBALS['app']->redirect($GLOBALS['web_root'] . $destination);
}

/**
 * Helper to handle when a User does not have permission to access a page
 */
function permission_denied()
{
	$GLOBALS['app']->render('permission_denied.html');
}
?>