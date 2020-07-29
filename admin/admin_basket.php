<?php
/*
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file /agefodd/admin/admin_basket.php
 * \ingroup basket
 * \brief basket module setup page
 */

require '../../../main.inc.php';
require_once '../basketmatch.class.php';
require_once '../lib/basketmatch.lib.php';
require_once DOL_DOCUMENT_ROOT . "/core/lib/admin.lib.php";
require_once DOL_DOCUMENT_ROOT . "/core/lib/images.lib.php";
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';

$langs->load("admin");
$langs->load('basket@basket');

$action = GETPOST('action', 'alpha');
$backtopage = GETPOST('backtopage', 'alpha');
$value = GETPOST('value', 'alpha');

/*
 * Action
 */

include DOL_DOCUMENT_ROOT.'/core/actions_setmoduleoptions.inc.php';
if ($action == 'setvar') {
	global $db, $conf;

	require_once(DOL_DOCUMENT_ROOT . "/core/lib/files.lib.php");

	$default_price = GETPOST('BM_DEFAULT_PRICE', 'int');
	if (!empty($default_price)) {
		$res = dolibarr_set_const($db, 'BM_DEFAULT_PRICE', $default_price, 'int', 0, '', $conf->entity);
	} else {
		$res = dolibarr_set_const($db, 'BM_DEFAULT_PRICE', 100, 'int', 0, '', $conf->entity);
	}
	if (!$res > 0)
		$error++;
}

/*
 * View
 */

$form = new Form($db);


llxHeader('', $langs->trans("BasketSetup"));

$linkback = '<a href="'.DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1">'.$langs->trans("BackToModuleList").'</a>';
print load_fiche_titre($langs->trans("BasketSetup"), $linkback, 'title_setup');


$head = basket_admin_prepare_head();

dol_fiche_head($head, 'card', $langs->trans("MenuUsersAndGroups"), -1, 'user');
print '<form method="post" action="' . $_SERVER['PHP_SELF'] . '" enctype="multipart/form-data" >';
print '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
print '<input type="hidden" name="action" value="setvar">';



print '<table class="noborder centpercent">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Parameter").'</td>';
print '<td align="center" width="20">&nbsp;</td>';
print '<td align="center" width="100">'.$langs->trans("Value").'</td>'."\n";
print '</tr>';

print '<tr class="oddeven">';
print '<td>'.$langs->trans("DefaultPrice").'</td>';
print '<td align="center" width="20">&nbsp;</td>';
print '<td align="center">';
print '<input type="text"   name="BM_DEFAULT_PRICE" value="' . $conf->global->BM_DEFAULT_PRICE . '" size="20" ></td>';
print '</tr>';
print '<tr class="impair"><td colspan="3" align="right"><input type="submit" class="button" value="' . $langs->trans("Save") . '"></td>';
print '</tr>';
print '</table><br>';
print '</form>';

llxFooter();
$db->close();

