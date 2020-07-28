<?php
/*
 * Copyright (C) 2007-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2018 delcroip <patrick@pmpd.eu>
 * Copyright (C) ---Put here your own copyright and developer email---
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *    \file       dev/basketmatchs/basketmatch_page.php
 *        \ingroup    mymodule othermodule1 othermodule2
 *        \brief      This file is an example of a php page
 *                    Initialy built by build_class_from_table on 2020-07-16 10:10
 */

//if (! defined('NOREQUIREUSER'))  define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))    define('NOREQUIREDB','1');
//if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');
//if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');			// Do not check anti CSRF attack test
//if (! defined('NOSTYLECHECK'))   define('NOSTYLECHECK','1');			// Do not check style html tag into posted data
//if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1');		// Do not check anti POST attack test
//if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');			// If there is no need to load and show top and left menu
//if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');			// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
//if (! defined("NOLOGIN"))        define("NOLOGIN",'1');				// If this page is public (can be called outside logged session)

// Change this following line to use the correct relative path (../, ../../, etc)
require 'lib/includeMain.lib.php';
// Change this following line to use the correct relative path from htdocs
//include_once(DOL_DOCUMENT_ROOT.'/core/class/formcompany.class.php');
//require_once 'lib/mymodule.lib.php';
require_once 'basketmatch.class.php';
require_once 'lib/generic.lib.php';
require_once 'lib/basketmatch.lib.php';
dol_include_once('/core/lib/functions2.lib.php');
//document handling
dol_include_once('/core/lib/files.lib.php');
//dol_include_once('/core/lib/images.lib.php');
dol_include_once('/core/class/html.formfile.class.php');
dol_include_once('/core/class/html.formother.class.php');
dol_include_once('/core/class/html.formprojet.class.php');
$PHP_SELF = $_SERVER['PHP_SELF'];
// Load traductions files requiredby by page
//$langs->load("companies");
$langs->load("basket@basket");

// Get parameter
$id = GETPOST('id', 'int');
$ref = GETPOST('ref', 'alpha');
$action = GETPOST('action', 'alpha');
$backtopage = GETPOST('backtopage');
$cancel = GETPOST('cancel');
$confirm = GETPOST('confirm');
$tms = GETPOST('tms', 'alpha');
$toselect = GETPOST('toselect', 'array');
$massaction = GETPOST('massaction', 'alpha');
$show_files = GETPOST('show_files', 'int');
//// Get parameters
$sortfield = GETPOST('sortfield', 'alpha');
$sortorder = GETPOST('sortorder', 'alpha') ? GETPOST('sortorder', 'alpha') : 'ASC';
$removefilter = isset($_POST["removefilter_x"]) || isset($_POST["removefilter"]);
//$applyfilter = isset($_POST["search_x"]) ;//|| isset($_POST["search"]);
if (!$removefilter)        // Both test must be present to be compatible with all browsers
{
	$ls_ref = GETPOST('ls_ref', 'alpha');
	$ls_nom = GETPOST('ls_nom', 'alpha');
	$ls_soc1 = GETPOST('ls_soc1', 'alpha');
	$ls_soc2 = GETPOST('ls_soc2', 'alpha');
	$ls_tarif = GETPOST('ls_tarif', 'int');
	$ls_date_month = GETPOST('ls_date_month', 'int');
	$ls_date_year = GETPOST('ls_date_year', 'int');
	$ls_terrain = GETPOST('ls_terrain', 'alpha');
	$ls_categories = GETPOST('ls_categories', 'alpha');
}


$limit = GETPOST('limit', 'int') ? GETPOST('limit', 'int') : $conf->liste_limit;
$sortfield = GETPOST("sortfield", 'alpha');
$sortorder = GETPOST("sortorder", 'alpha');
$page = GETPOSTISSET('pageplusone') ? (GETPOST('pageplusone') - 1) : GETPOST("page", 'int');
if (empty($page) || $page < 0 || GETPOST('button_search', 'alpha') || GETPOST('button_removefilter', 'alpha')) {
	$page = 0;
}     // If $page is not defined, or '' or -1 or if we click on clear filters or if we select empty mass action
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if (!$sortfield) $sortfield = "t.ref";
if (!$sortorder) $sortorder = "ASC";

$diroutputmassaction = $conf->basket->dir_output . '/temp/massgeneration/' . $user->id;

$arrayfields = array(
	't.ref' => array('label' => $langs->trans("Ref"), 'checked' => 1),
	//'pfp.ref_fourn'=>array('label'=>$langs->trans("RefSupplier"), 'checked'=>1, 'enabled'=>(! empty($conf->barcode->enabled))),
	't.nom' => array('label' => $langs->trans("Name"), 'checked' => 1, 'position' => 10),
	't.fk_soc1' => array('label' => $langs->trans("Team1"), 'checked' => 1, 'position' => 11),
	't.fk_soc2' => array('label' => $langs->trans("Team2"), 'checked' => 1, 'position' => 12),
	't.tarif' => array('label' => $langs->trans("Price"), 'checked' => 1, 'position' => 13),
	't.date' => array('label' => $langs->trans("Date"), 'checked' => 1, 'position' => 19),
	't.terrain' => array('label' => $langs->trans('Terrain'), 'checked' => 0, 'position' => 20));

// uncomment to avoid resubmision
//if(isset( $_SESSION['basketmatch_class'][$tms]))
//{

//   $cancel = TRUE;
//  setEventMessages('Internal error, POST not exptected', null, 'errors');
//}


// Right Management
/*
if ($user->societe_id > 0 ||
	  (!$user->rights->mymodule->add && ($action == 'add' || $action = 'create')) ||
	  (!$user->rights->mymodule->view && ($action == 'list' || $action = 'view')) ||
	  (!$user->rights->mymodule->delete && ($action == 'confirm_delete')) ||
	  (!$user->rights->mymodule->edit && ($action == 'edit' || $action = 'update')))
{
   accessforbidden();
}
*/

// create object and set id or ref if provided as parameter
$object = new BasketMatch($db);
if ($id > 0) {
	$object->id = $id;
	$object->fetch($id);
	$ref = dol_sanitizeFileName($object->ref);
}
if (!empty($ref)) {
	$object->ref = $ref;
	$object->id = $id;
	$object->fetch($id, $ref);
	$ref = dol_sanitizeFileName($object->ref);
}
$objectclass = 'basketmatch';
$uploaddir = $conf->basket->dir_output;
$permissiontodelete = $user->rights->basket->supprimer;
include DOL_DOCUMENT_ROOT . '/core/actions_massactions.inc.php';


/*******************************************************************
 * ACTIONS
 *
 * Put here all code to do according to value of "action" parameter
 ********************************************************************/
$form = new Form($db);
$formother = new FormOther($db);
$formproject = new FormProjets($db);
// Action to remove record
switch ($action) {
	case 'confirm_delete':
		$result = ($confirm == 'yes') ? $object->delete($user) : 0;
		if ($result > 0) {
			// Delete OK
			setEventMessage($langs->trans('RecordDeleted'), 'mesgs');
		} else {
			// Delete NOK
			if (!empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
			else setEventMessage($langs->trans('RecordNotDeleted'), 'errors');
		}
		break;
	case 'delete':
		if ($action == 'delete' && !empty($toselect)) {
			foreach ($toselect as $cbselect) {
				$delsql = 'DELETE FROM ' . MAIN_DB_PREFIX . 'basket_match WHERE rowid = ' . $cbselect;
				$resdelsql = $db->query($delsql);
			}
			//to have the object to be deleted in the background\
		}

}

/***************************************************
 * VIEW
 *
 * Put here all code to build page
 ****************************************************/

llxHeader('', 'BasketMatch', '');
print "<div> <!-- module body-->";

$fuser = new User($db);
// Put here content of your page

// Example : Adding jquery code
/*print '<script type = "text/javascript" language = "javascript">
jQuery(document).ready(function() {
	function init_myfunc()
	{
		jQuery("#myid").removeAttr(\'disabled\');
		jQuery("#myid").attr(\'disabled\',\'disabled\');
	}
	init_myfunc();
	jQuery("#mybutton").click(function() {
		init_needroot();
	});
});
</script>';*/


$sql = 'SELECT';
$sql .= ' t.rowid,';
$sql .= ' t.ref,';
$sql .= ' t.nom,';
$sql .= ' t.fk_soc1,';
$sql .= ' t.fk_soc2,';
$sql .= ' t.tarif,';
$sql .= ' t.date,';
$sql .= ' t.terrain,';
$sql .= ' t.categ';


$sql .= ' FROM ' . MAIN_DB_PREFIX . 'basket_match as t';
$sql .= ' JOIN llx_societe as s ON t.fk_soc1 = s.rowid';
$sql .= ' JOIN llx_societe as s2 ON t.fk_soc2 = s2.rowid';
$sql .= ' JOIN llx_c_terrain as t2 ON t.terrain = t2.rowid';
$sql .= ' JOIN llx_c_categories as c ON t.categ = c.rowid';
$sqlwhere = '';
if (isset($object->entity))
	$sqlwhere .= ' AND t.entity = ' . $conf->entity;
if ($filter && $filter != -1)        // GETPOST('filtre') may be a string
{
	$filtrearr = explode(',', $filter);
	foreach ($filtrearr as $fil) {
		$filt = explode(':', $fil);
		$sqlwhere .= ' AND ' . $filt[0] . ' = ' . $filt[1];
	}
}
//pass the search criteria
if ($ls_ref) $sqlwhere .= natural_search('t.ref', $ls_ref);
if ($ls_nom) $sqlwhere .= natural_search('t.nom', $ls_nom);
if ($ls_soc1 != -1 && !empty($ls_soc1)) $sqlwhere .= natural_search('t.fk_soc1', $ls_soc1);
if ($ls_soc2 != -1 && !empty($ls_soc2)) $sqlwhere .= natural_search('t.fk_soc2', $ls_soc2);
if ($ls_tarif) $sqlwhere .= natural_search(array('t.tarif'), $ls_tarif);
if ($ls_date_month) $sqlwhere .= " AND MONTH(t.date)='" . $ls_date_month . "'";
if ($ls_date_year) $sqlwhere .= " AND YEAR(t.date)='" . $ls_date_year . "'";
if ($ls_terrain != -1 && !empty($ls_terrain)) $sqlwhere .= natural_search('t.terrain', $ls_terrain);

//list limit
if (!empty($sqlwhere))
	$sql .= ' WHERE ' . substr($sqlwhere, 5);

// Count total nb of records
$nbtotalofrecords = 0;
if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST)) {
	if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST)) {
		$result = $db->query($sql);
		$nbtotalofrecords = $db->num_rows($result);
		if (($page * $limit) > $nbtotalofrecords)    // if total resultset is smaller then paging size (filtering), goto and load page 0
		{
			$page = 0;
			$offset = 0;
		}
	}
}
if (!empty($sortfield)) {
	$sql .= $db->order($sortfield, $sortorder);
} else {
	$sortorder = 'ASC';
}

if (!empty($limit)) {
	$sql .= $db->plimit($limit + 1, $offset);
}

//execute SQL
dol_syslog($script_file, LOG_DEBUG);
$resql = $db->query($sql);
if ($resql) {
	$param = '';
	$arrayofselected = is_array($toselect) ? $toselect : array();
	if (!empty($contextpage) && $contextpage != $_SERVER["PHP_SELF"]) $param .= '&contextpage=' . urlencode($contextpage);
	if ($limit > 0 && $limit != $conf->liste_limit) $param .= '&limit=' . urlencode($limit);
	if (!empty($ls_ref)) $param .= '&ls_ref = ' . urlencode($ls_ref);
	if (!empty($ls_nom)) $param .= '&ls_nom = ' . urlencode($ls_nom);
	if (!empty($ls_soc1)) $param .= '&ls_soc1 = ' . urlencode($ls_soc1);
	if (!empty($ls_soc2)) $param .= '&ls_soc2 = ' . urlencode($ls_soc2);
	if (!empty($ls_tarif)) $param .= '&ls_tarif = ' . urlencode($ls_tarif);
	if (!empty($ls_date_month)) $param .= '&ls_date_month = ' . urlencode($ls_date_month);
	if (!empty($ls_date_year)) $param .= '&ls_date_year = ' . urlencode($ls_date_year);
	if (!empty($ls_terrain)) $param .= '&ls_terrain = ' . urlencode($ls_terrain);


	if ($filter && $filter != -1) $param .= '&filtre=' . urlencode($filter);
	// List of mass actions available
	$arrayofmassactions = array(
		'predelete' => $langs->trans("Delete"),
		//'builddoc'=>$langs->trans("PDFMerge"),
		//'presend'=>$langs->trans("SendByMail"),
	);
	$rightskey = 'basketmatch';
	if ($user->rights->{$rightskey}->supprimer) $arrayofmassactions['predelete'] = "<span class='fa fa-trash paddingrightonly'></span>" . $langs->trans("Delete");
	if (in_array($massaction, array('presend', 'predelete'))) $arrayofmassactions = array();
	$massactionbutton = $form->selectMassAction('', $arrayofmassactions);

	$varpage = empty($contextpage) ? $_SERVER["PHP_SELF"] : $contextpage;
	$selectedfields = $form->multiSelectArrayWithCheckbox('selectedfields', $arrayfields, $varpage); // This also change content of $arrayfields
	if ($massactionbutton) $selectedfields .= $form->showCheckAddButtons('checkforselect', 1);

	$num = $db->num_rows($resql);
	//print_barre_liste function defined in /core/lib/function.lib.php, possible to add a picto
	$newcardbutton = '';

	//NewCard Button
	$params = array();
	$label = 'NewBasketMatch';
	$newcardbutton .= dolGetButtonTitle($langs->trans($label), '', 'fa fa-plus-circle', DOL_URL_ROOT . '/custom/basket/basketmatch_card.php?action=create', '', 1, $params);

	print '<form method = "POST" action = "' . $_SERVER["PHP_SELF"] . '">';
	print_barre_liste($langs->trans("BasketMatch"), $page, $PHP_SELF, $param, $sortfield, $sortorder, $massactionbutton, $num, $nbtotalofrecords, 'basket@basket', 0, $newcardbutton, '', $limit, 0, 0, 1);

	$topicmail = "Information";
	$modelmail = "basket";
	$objecttmp = new BasketMatch($db);
	$trackid = 'match' . $object->id;
	include DOL_DOCUMENT_ROOT . '/core/tpl/massactions_pre.tpl.php';

	print '<table class = "liste" width = "100%">' . "\n";
	//TITLE
	print '<tr class = "liste_titre">';
	print_liste_field_titre($langs->trans('Ref'), $PHP_SELF, 't.ref', '', $param, '', $sortfield, $sortorder);
	print "\n";
	print_liste_field_titre($langs->trans('Nom'), $PHP_SELF, 't.nom', '', $param, '', $sortfield, $sortorder);
	print "\n";
	print_liste_field_titre($langs->trans('Soc1'), $PHP_SELF, 's.nom', '', $param, '', $sortfield, $sortorder);
	print "\n";
	print_liste_field_titre($langs->trans('Soc2'), $PHP_SELF, 's2.nom', '', $param, '', $sortfield, $sortorder);
	print "\n";
	print_liste_field_titre($langs->trans('Tarif'));
	print "\n";
	print_liste_field_titre($langs->trans('Date'), $PHP_SELF, 't.date', '', $param, '', $sortfield, $sortorder);
	print "\n";
	print_liste_field_titre($langs->trans('Terrain'), $PHP_SELF, 't2.nom_terrain', '', $param, '', $sortfield, $sortorder);
	print "\n";
	print_liste_field_titre($langs->trans('Categories'), $PHP_SELF, 'c.libelle', '', $param, '', $sortfield, $sortorder);
	print "\n";
	print_liste_field_titre($langs->trans(' '), $PHP_SELF, '', '', $param, '', $sortfield, $sortorder);
	print "\n";


	print '</tr>';
	//SEARCH FIELDS
	print '<tr class = "liste_titre">';
	//Search field forref
	print '<td class="liste_titre" colspan="1" >';
	print '<input class="flat" size="16" type="text" name="ls_ref" value="' . $ls_ref . '">';
	print '</td>';
//Search field for nom
	print '<td class="liste_titre" colspan="1" >';
	print '<input class="flat" size="16" type="text" name="ls_nom" value="' . $ls_nom . '">';
	print '</td>';
//Search field for soc1
	print '<td class="liste_titre" colspan="1" >';
	$sql_soc1 = array('table' => 'societe', 'keyfield' => 't.rowid', 'fields' => 't.nom', 'join' => 'join llx_basket_match', 'where' => 't.rowid = fk_soc1', 'tail' => '');
	$html_soc1 = array('name' => 'ls_soc1', 'class' => '', 'otherparam' => '', 'ajaxNbChar' => '', 'separator' => '-');
	$addChoices_soc1 = null;
	print select_sellist($sql_soc1, $html_soc1, $ls_soc1, $addChoices_soc1);
	print '</td>';
//Search field for soc2
	print '<td class="liste_titre" colspan="1" >';
	$sql_soc2 = array('table' => 'societe', 'keyfield' => 't.rowid', 'fields' => 't.nom', 'join' => 'join llx_basket_match', 'where' => 't.rowid = fk_soc2', 'tail' => '');
	$html_soc2 = array('name' => 'ls_soc2', 'class' => '', 'otherparam' => '', 'ajaxNbChar' => '', 'separator' => '-');
	$addChoices_soc2 = null;
	print select_sellist($sql_soc2, $html_soc2, $ls_soc2, $addChoices_soc2);
	print '</td>';
//Search field for tarif
	print '<td class="liste_titre" colspan="1" >';
	print '<input class="flat" size="16" type="text" name="ls_tarif" value="' . $ls_tarif . '">';
	print '</td>';
//Search field for date
	print '<td class="liste_titre" colspan="1" >';
	print '<input class="flat" type="text" size="1" maxlength="2" name="date_month" value="' . $ls_date_month . '">';
	$syear = $ls_date_year;
	$formother->select_year($syear ? $syear : -1, 'ls_date_year', 1, 20, 5);
	print '</td>';
//Search field for terrain
	print '<td class="liste_titre" colspan="1" >';
	$sql_terrain = array('table' => 'c_terrain', 'keyfield' => 't.rowid', 'fields' => 't.nom_terrain', 'join' => '', 'where' => '', 'tail' => '');
	$html_terrain = array('name' => 'ls_terrain', 'class' => '', 'otherparam' => '', 'ajaxNbChar' => '', 'separator' => '-');
	$addChoices_terrain = null;
	print select_sellist($sql_terrain, $html_terrain, $ls_terrain, $addChoices_terrain);
	print '</td>';
//Search field for categories
	print '<td class="liste_titre" colspan="1" >';
	$sql_categ = array('table' => 'c_categories', 'keyfield' => 't.rowid', 'fields' => 't.codecat', 'join' => '', 'where' => '', 'tail' => '');
	$html_categ = array('name' => 'ls_categories', 'class' => '', 'otherparam' => '', 'ajaxNbChar' => '', 'separator' => '-');
	$addChoices_categ = null;
	print select_sellist($sql_categ, $html_categ, $ls_categories, $addChoices_categ);
	print '</td>';


	print '<td width = "15px">';
	print '<input type = "image" class = "liste_titre" name = "search" src = "' . img_picto($langs->trans("Search"), 'search.png', '', '', 1) . '" value = "' . dol_escape_htmltag($langs->trans("Search")) . '" title = "' . dol_escape_htmltag($langs->trans("Search")) . '">';
	print '<input type = "image" class = "liste_titre" name = "removefilter" src = "' . img_picto($langs->trans("Search"), 'searchclear.png', '', '', 1) . '" value = "' . dol_escape_htmltag($langs->trans("RemoveFilter")) . '" title = "' . dol_escape_htmltag($langs->trans("RemoveFilter")) . '">';
	print '</td>';
	print '</tr>' . "\n";


	$i = 0;
	$basedurl = dirname($PHP_SELF) . '/basketmatch_card.php?action=view&id=';
	$totaltarif = 0;
	while ($i < $num && $i < $limit) {
		$obj = $db->fetch_object($resql);
		if ($obj) {
			// You can use here results
			print "<td>" . $object->getNomUrl($obj->ref, '', $obj->ref, 1) . "</td>";
			print "<td>" . $obj->nom . "</td>";
			//For team1
			if (class_exists('Societe')) {
				$team1 = new Societe($db);
				$team1->fetch($obj->fk_soc1);
				print "<td>" . $team1->getNomUrl('1') . "</td>";
			} else {
				print print_sellist($sql_soc1, $obj->fk_soc1);
			}
			//For team2
			if (class_exists('Societe')) {
				$team2 = new Societe($db);
				$team2->fetch($obj->fk_soc2);
				print "<td>" . $team2->getNomUrl('1') . "</td>";
			} else {
				print print_sellist($sql_soc2, $obj->fk_soc2);
			}
			//For tarif
			$totaltarif += $obj->tarif;
			print "<td>" . $obj->tarif . "</td>";
			//For date
			print "<td>" . dol_print_date($db->jdate($obj->date), 'day') . "</td>";
			//For terrain
			$terrainsql = 'SELECT nom_terrain FROM ' . MAIN_DB_PREFIX . 'c_terrain WHERE rowid = ' . $obj->terrain;
			$resterrain = $db->query($terrainsql);
			$terrain = $db->fetch_object($resterrain);
			print "<td>" . $terrain->nom_terrain . "</td>";
			//For categories
			$categsql = 'SELECT codecat FROM ' . MAIN_DB_PREFIX . 'c_categories WHERE rowid = ' . $obj->categ;
			$rescateg = $db->query($categsql);
			$categ = $db->fetch_object($rescateg);
			print "<td>" . $categ->codecat . "</td>";
			print '<td class="nowrap center">';


			if ($massactionbutton || $massaction)   // If we are in select mode (massactionbutton defined) or if we have already selected and sent an action ($massaction) defined
			{
				$selected = 0;
				if (in_array($obj->rowid, $arrayofselected)) $selected = 1;
				print '<input id="cb' . $obj->rowid . '" class="flat checkforselect" type="checkbox" name="toselect[]" value="' . $obj->rowid . '"' . ($selected ? ' checked="checked"' : '') . '>';
			}
			print '</td>';

			print "</tr>";


		}
		$i++;
	}
	print '<tr class="liste_total">';
	if ($num < $limit) print '<td class="left">' . $langs->trans("Total") . '</td>';
	else print '<td class="left">' . $langs->trans("Totalforthispage") . '</td>';
	print '<td></td>';
	print '<td></td>';
	print '<td></td>';
	print "<td>" . price($totaltarif) . "</td>";
	print '<td></td>';
	print '<td></td>';
	print '<td></td>';
	print '<td></td>';
	print '</tr>';
} else {
	$error++;
	dol_print_error($db);
}

print '</table>' . "\n";
print '</form>' . "\n";


// End of page
llxFooter();
$db->close();
