<?php


class ActionsBasket
{
	function addMoreActionsButtons($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $db, $langs ;
		if (in_array('thirdpartycard', explode(':', $parameters['context'])))
		{
			print '<a class="butAction" href="'.DOL_URL_ROOT .'/custom/basket/basketmatch_card.php?action=create&team1='.$object->id.'">'.$langs->trans("CreateMatch").'</a>'."\n";

		}
		return 0;
	}
}
