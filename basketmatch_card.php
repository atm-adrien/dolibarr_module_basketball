<?php
/*
 * Copyright (C) 2007-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2018	   Patrick DELCROIX     <pmpdelcroix@gmail.com>
 * * Copyright (C) ---Put here your own copyright and developer email---
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
include 'lib/includeMain.lib.php';
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

// Load traductions files required by by page
$langs->load("basket@basket");

// Get parameter
$id = GETPOST('id', 'int');
$ref = GETPOST('ref', 'alpha');
$action = GETPOST('action', 'alpha');
$backtopage = GETPOST('backtopage');
$cancel = GETPOST('cancel');
$tms = GETPOST('tms', 'alpha');
$nom = GETPOST('Nom', 'alpha');
$team1 = GETPOST('team1', 'int');
$team2 = GETPOST('team2', 'int');
$tarif = GETPOST('Tarif');
$date = GETPOST('Date');
$terrain = GETPOST('select_terrain', 'int');
$categ = GETPOST('select_categ', 'int');
$confirm = GETPOST('confirm', 'alpha');
$newref = GETPOST('newref');


//// Get parameters
/*
$sortfield = GETPOST('sortfield','alpha');
$sortorder = GETPOST('sortorder','alpha')?GETPOST('sortorder','alpha'):'ASC';
$removefilter=isset($_POST["removefilter_x"]) || isset($_POST["removefilter"]);
//$applyfilter=isset($_POST["search_x"]) ;//|| isset($_POST["search"]);
if (!$removefilter )		// Both test must be present to be compatible with all browsers
{
    $ls_fields1=GETPOST('ls_fields1','int');
    $ls_fields2=GETPOST('ls_fields2','alpha');
}
*/


// uncomment to avoid resubmision
//if(isset( $_SESSION['basketmatch_class'][$tms]))
//{

//   $cancel=TRUE;
//  setEventMessages('Internal error, POST not exptected', null, 'errors');
//}


// Right Management
/*
if ($user->societe_id > 0 ||
	  (!$user->rights->mymodule->add && ($action=='add' || $action='create')) ||
	  (!$user->rights->mymodule->view && ($action=='list' || $action='view')) ||
	  (!$user->rights->mymodule->delete && ($action=='confirm_delete')) ||
	  (!$user->rights->mymodule->edit && ($action=='edit' || $action='update')))
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
	$id = $object->id;
	$ref = dol_sanitizeFileName($object->ref);

}


/*******************************************************************
 * ACTIONS
 *
 * Put here all code to do according to value of "action" parameter
 ********************************************************************/

// Action to add record
$error = 0;
if (!empty($team1)){
	$object->soc1 = $team1;
}
if ($cancel) {
	BasketMatchReloadPage($backtopage, $id, $ref);
} else {
	if (($action == 'add') || ($action == 'update' && ($id > 0 || !empty($ref)))) {
		//block resubmit
		if (empty($tms) || (!isset($_SESSION['BasketMatch'][$tms]))) {
			setEventMessage($langs->trans('WrongTimeStamp_requestNotExpected'), 'errors');
			$action = ($action == 'add') ? 'create' : 'view';
		}
		//retrive the data
		$object->ref = $ref;
		$object->terrain = $terrain;
		$object->soc2 = $team2;
		$object->tarif = $tarif;
		$object->date = $date;
		$object->categ = $categ;
		$object->nom = $nom;

	} else if ($id == 0 && $ref == '' && $action != 'create') {
		$action = 'create';
	}
}

switch ($action) {
	case 'update':

		if (empty($ref) || empty($nom) || $team1 == -1 || $team2 == -1 || $date == -1 || $terrain == -1) {
			setEventMessage($langs->trans('AllFieldMustBeFilled'), 'errors');
			$action = 'view';
		} else {
			$newdate = DateTime::createFromFormat('d/m/Y', $object->date);
			$object->date = $newdate->getTimestamp();
			if (isset($tarif) || $categ != -1) {
				$tarifsql = 'SELECT prixpardef FROM ' . MAIN_DB_PREFIX . 'c_categories WHERE rowid = ' . $object->categ;
				$restarif = $db->query($tarifsql);
				$tar = $db->fetch_object($restarif);
				$object->tarif = $tar->prixpardef;
			}
			$result = $object->update($user);
			if ($result > 0) {
				// Creation OK
				unset($_SESSION['BasketMatch'][$tms]);
				setEventMessage($langs->trans('RecordUpdated'), 'mesgs');

			} else {
				// Creation KO
				if (!empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
				else setEventMessage($langs->trans('RecordNotUpdated'), 'errors');

			}
		}
		$action = 'view';
	case 'delete':
		if (isset($_GET['urlfile'])) $action = 'deletefile';

	case 'view':
	case 'viewinfo':
	case 'edit':
		// fetch the object data if possible
		if (empty($tarif) || $categ != -1) {
			$tarifsql = 'SELECT prixpardef FROM ' . MAIN_DB_PREFIX . 'c_categories WHERE rowid = ' . $object->categ;
			$restarif = $db->query($tarifsql);
			$tar = $db->fetch_object($restarif);
			$object->tarif = $tar->prixpardef;
		}
		if ($id > 0 || !empty($ref)) {
			$result = $object->fetch($id, $ref);
			if ($result < 0) {
				dol_print_error($db);
			} else { // fill the id & ref
				if (isset($object->id)) $id = $object->id;
				if (isset($object->rowid)) $id = $object->rowid;
				if (isset($object->ref)) $ref = $object->ref;
			}

		} else {
			setEventMessage($langs->trans('noIdPresent') . ' id:' . $id, 'errors');
			$action = 'create';
		}
		break;

	case 'add':

		if (empty($ref) || empty($nom) || $team1 == -1 || $team2 == -1 || $date == -1 || $terrain == -1) {
			setEventMessage($langs->trans('AllFieldMustBeFilled'), 'errors');
			$action = 'create';
		} else {
			//Change the date type into Timestamp
			$newdate = DateTime::createFromFormat('d/m/Y', $object->date);
			$object->date = $newdate->getTimestamp();
			$tarifglob = $conf->global->BM_DEFAULT_PRICE;
			if (empty($tarif) && $categ != -1) {
				$tarifsql = 'SELECT prixpardef FROM ' . MAIN_DB_PREFIX . 'c_categories WHERE rowid = ' . $object->categ;
				$restarif = $db->query($tarifsql);
				$tar = $db->fetch_object($restarif);
				$object->tarif = $tar->prixpardef;
			} elseif (empty($tarif) && $categ == -1) {
				$object->tarif = $tarifglob;
			} else {
				$object->tarif = price2num($tarif, 'MU');

			}
			$result = $object->create($user);
			if ($result > 0) {
				// Creation OK
				// remove the tms
				unset($_SESSION['BasketMatch'][$tms]);
				setEventMessage($langs->trans('RecordSucessfullyCreated'), 'mesgs');
				BasketMatchReloadPage($backtopage, $result, '');

			} else {
				// Creation KO
				if (!empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
				else  setEventMessage($langs->trans('RecordNotSucessfullyCreated'), 'errors');
				$action = 'create';
			}
		}
		break;

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
		BasketMatchReloadPage($backtopage, 0, '');
		break;


}
//Removing the tms array so the order can't be submitted two times
if (isset($_SESSION['BasketMatch'][$tms])) {
	unset($_SESSION['BasketMatch'][$tms]);
}
if (($action == 'create') || ($action == 'edit' && ($id > 0 || !empty($ref)))) {
	$tms = getToken();
	$_SESSION['BasketMatch'][$tms] = array();
	$_SESSION['BasketMatch'][$tms]['action'] = $action;

}

/***************************************************
 * VIEW
 *
 * Put here all code to build page
 ****************************************************/
if ($action == 'confirm_clone') {
	if ($confirm == 'yes') {
		//Ref vide non-acceptée
		if (empty($newref)) {
			$result = -1;
			$object->error = $langs->trans('FilledTheRef');
		}
		//Ne clone pas une ref déjà existante
		$refsql = 'SELECT * FROM ' . MAIN_DB_PREFIX . 'basket_match WHERE ref = ' . "'" . $newref . "'";
		$resref = $db->query($refsql);
		$testref = $db->fetch_object($resref);
		if (!empty($testref)) {
			$result = -1;
			$object->error = $langs->trans('ThisRefAlreadyExists');
		} else {
			$objectutil = dol_clone($object, 1); // To avoid to denaturate loaded object when setting some properties for clone. We use native clone to keep this->db valid.
			$result = $objectutil->createFromClone($id, $newref);
		}
		if ($result > 0) {
			header("Location: " . $_SERVER['PHP_SELF'] . '?id=' . $result);
			exit();
		} else {
			$langs->load("errors");
			setEventMessages($object->error, $object->errors, 'errors');
			$action = '';
		}
	}
}
llxHeader('', 'BasketMatch', '');
print "<div> <!-- module body-->";
$form = new Form($db);
$formother = new FormOther($db);
$formproject = new FormProjets($db);
$fuser = new User($db);
// Put here content of your page

if ($action == 'clone') {
	// Ask confirmation to clone
	$formquestion = array(array('type' => 'text', 'name' => 'newref', 'label' => $langs->trans("Ref")));
	print $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('ToClone'), $langs->trans('ConfirmCloneMatch', $object->ref), 'confirm_clone', $formquestion, '', 1, 250);
	$action = 'view';
}
// Example : Adding jquery code
/*print '<script type="text/javascript" language="javascript">
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

$edit = $new = 0;
if (empty($action)) $action = 'view';
switch ($action) {
	case 'create':
		$new = 1;
	case 'edit':
		$edit = 1;
	case 'delete';
		if ($action == 'delete' && ($id > 0 || $ref != "")) {
			$ret = $form->form_confirm($PHP_SELF . '?action=confirm_delete&id=' . $id, $langs->trans('DeleteBasketMatch'), $langs->trans('ConfirmDelete'), 'confirm_delete', '', 0, 1);
			if ($ret == 'html') print '<br />';
			//to have the object to be deleted in the background\
		}
	case 'view':
	{

		print '<br>';
		if ($edit == 1) {
			if ($new == 1) {
				print '<form method="POST" action="' . $PHP_SELF . '?action=add">';
			} else {
				print '<form method="POST" action="' . $PHP_SELF . '?action=update&id=' . $id . '">';
			}

			print '<input type="hidden" name="tms" value="' . $tms . '">';
			print '<input type="hidden" name="backtopage" value="' . $backtopage . '">';

		} else {// show the nav bar
			$basedurl = dol_buildpath("/basket/basketmatch_list.php", 1);
			$linkback = '<a href="' . $basedurl . (!empty($socid) ? '?socid=' . $socid : '') . '">' . $langs->trans("BackToList") . '</a>';
			if (!isset($object->ref))//save ref if any
				$object->ref = $object->id;
			print $form->showrefnav($object, 'ref', $linkback, 1, 'ref', 'ref', '');
			//reloqd the ref

		}
		print '<table class="border centpercent">' . "\n";

// show the field ref

		print "<tr>\n";
		print '<td class="fieldrequired">' . $langs->trans('Ref') . ' </td><td>';
		if ($action == 'create') {
			print '<input type="text" value="" name="ref">';
		} else {
			print $object->ref;
		}
		print "</td>";
		print "\n</tr>\n";

// show the field nom

		print "<tr>\n";
		print '<td class="fieldrequired">' . $langs->trans('Nom') . ' </td><td>';
		if ($edit == 1) {
			print '<input type="text" value="' . $object->nom . '" name="Nom">';
		} else {
			print $object->nom;
		}
		print "</td>";
		print "\n</tr>\n";

// show the field soc1

		print "<tr>\n";
		print '<td class="fieldrequired">' . $langs->trans('Soc1') . ' </td><td>';
		if ($edit == 1) {
			$sql_soc1 = array('table' => 'societe', 'keyfield' => 'rowid', 'fields' => 'nom', 'join' => '', 'where' => '', 'tail' => '');
			$html_soc1 = array('name' => 'team1', 'class' => '', 'otherparam' => '', 'ajaxNbChar' => '', 'separator' => '-');
			$addChoices_soc1 = null;
			print select_sellist($sql_soc1, $html_soc1, $object->soc1, $addChoices_soc1);
		} else {
			$team1 = new Societe($db);
			$team1->fetch($object->soc1);
			print $team1->getNomUrl('1');
		}
		print "</td>";
		print "\n</tr>\n";

// show the field soc2

		print "<tr>\n";
		print '<td class="fieldrequired">' . $langs->trans('Soc2') . ' </td><td>';
		if ($edit == 1) {
			$sql_soc2 = array('table' => 'societe', 'keyfield' => 'rowid', 'fields' => 'nom', 'join' => '', 'where' => '', 'tail' => '');
			$html_soc2 = array('name' => 'team2', 'class' => '', 'otherparam' => '', 'ajaxNbChar' => '', 'separator' => '-');
			$addChoices_soc2 = null;
			print select_sellist($sql_soc2, $html_soc2, $object->soc2, $addChoices_soc2);
		} else {
			$team1 = new Societe($db);
			$team1->fetch($object->soc2);
			print $team1->getNomUrl('1');
		}
		print "</td>";
		print "\n</tr>\n";

// show the field tarif

		print "<tr>\n";
		print '<td>' . $langs->trans('Tarif') . ' </td><td>';
		if ($edit == 1) {
			print '<input type="text" value="' . $object->tarif . '" name="Tarif">';
		} else {
			if (empty($object->tarif) && $categ != -1) {
				$tarifsql = 'SELECT prixpardef FROM ' . MAIN_DB_PREFIX . 'c_categories WHERE rowid = ' . $object->categ;
				$restarif = $db->query($tarifsql);
				$tar = $db->fetch_object($restarif);
				$object->tarif = $tar;
				print $object->tarif->prixpardef;
			} else {
				print $object->tarif;
			}

		}
		print "</td>";
		print "\n</tr>\n";

// show the field date

		print "<tr>\n";
		print '<td class="fieldrequired">' . $langs->trans('Date') . ' </td><td>';
		if ($edit == 1) {
			if ($new == 1) {
				print $form->select_date(-1, 'Date');
			} else {
				print $form->select_date($object->date, 'Date');
			}
		} else {
			print dol_print_date($object->date, 'day');
		}
		print "</td>";
		print "\n</tr>\n";

// show the field terrain

		print "<tr>\n";
		print '<td class="fieldrequired">' . $langs->trans('Terrain') . ' </td>';

		if ($edit == 1) {
			$sql_terrain = array('table' => 'c_terrain', 'keyfield' => 'rowid', 'fields' => 'nom_terrain', 'join' => '', 'where' => 'active = 1', 'tail' => '');
			$html_terrain = array('name' => 'select_terrain', 'class' => '', 'otherparam' => '', 'ajaxNbChar' => '', 'separator' => '-');
			$addChoices_terrain = null;
			print "<td>" . select_sellist($sql_terrain, $html_terrain, $object->terrain, $addChoices_terrain) . "</td>";
		} else {
			$terrainsql = 'SELECT nom_terrain FROM ' . MAIN_DB_PREFIX . 'c_terrain WHERE rowid = ' . $object->terrain;
			$resterrain = $db->query($terrainsql);
			$terrain = $db->fetch_object($resterrain);
			print "<td>" . $terrain->nom_terrain . "</td>";
		}
		print "\n</tr>\n";

// show the field Categories

		print "<tr>\n";
		print '<td>' . $langs->trans('Categories') . ' </td>';

		if ($edit == 1) {
			$sql_categ = array('table' => 'c_categories', 'keyfield' => 'rowid', 'fields' => 'codecat', 'join' => '', 'where' => 'active = 1', 'tail' => '');
			$html_categ = array('name' => 'select_categ', 'class' => '', 'otherparam' => '', 'ajaxNbChar' => '', 'separator' => '-');
			$addChoices_categ = null;
			print "<td>" . select_sellist($sql_categ, $html_categ, $object->categ, $addChoices_categ) . "</td>";
		} else {
			$categsql = 'SELECT codecat FROM ' . MAIN_DB_PREFIX . 'c_categories WHERE rowid = ' . $object->categ;
			$rescateg = $db->query($categsql);
			$categorie = $db->fetch_object($rescateg);
			print "<td>" . $categorie->codecat . "</td>";
		}
		print "\n</tr>\n";

		print "<td></td></tr>\n";


		print '</table>' . "\n";
		print '<br>';
		print '<div class="center">';
		if ($edit == 1) {
			if ($new == 1) {

				print '<input type="submit" class="butAction" name="add" value="' . $langs->trans('Add') . '">';
			} else {

				print '<input type="submit" name="update" value="' . $langs->trans('Update') . '" class="butAction">';
			}
			print ' &nbsp; <input type="submit" class="butActionDelete" name="cancel" value="' . $langs->trans('Cancel') . '"></div>';
			print '</form>';
		} else {
			$parameters = array();
			$reshook = $hookmanager->executeHooks('addMoreActionsButtons', $parameters, $object, $action);    // Note that $action and $object may have been modified by hook
			if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

			if (empty($reshook)) {
				print '<div class="tabsAction">';

				// Boutons d'actions
				//if($user->rights->BasketMatch->edit)
				//{
				print '<a href="' . $PHP_SELF . '?id=' . $id . '&action=edit" class="butAction">' . $langs->trans('Update') . '</a>';
				//}
				print '<a class="butAction' . ($conf->use_javascript_ajax ? ' reposition' : '') . '" href="' . $_SERVER['PHP_SELF'] . '?id=' . $object->id . '&amp;action=clone">' . $langs->trans("ToClone") . '</a>';
				//if ($user->rights->BasketMatch->delete)
				//{
				print '<a class="butActionDelete" href="' . $PHP_SELF . '?id=' . $id . '&action=delete">' . $langs->trans('Delete') . '</a>';

				//}
				//else
				//{
				//    print '<a class="butActionRefused" href="#" title="'.dol_escape_htmltag($langs->trans("NotAllowed")).'">'.$langs->trans('Delete').'</a>';
				//}

				print '</div>';
			}
		}
		break;
	}
	case 'viewinfo':
		print_fiche_titre($langs->trans('BasketMatch'));
		$head = BasketMatchPrepareHead($object);
		dol_fiche_head($head, 'info', $langs->trans("BasketMatch"), 0, 'basket@basket');
		print '<table width="100%"><tr><td>';
		dol_print_object_info($object);
		print '</td></tr></table>';
		print '</div>';
		break;

	case 'delete':
		if (($id > 0 || $ref != '')) {
			$ret = $form->form_confirm($PHP_SELF . '?action=confirm_delete&id=' . $id, $langs->trans('DeleteBasketMatch'), $langs->trans('ConfirmDelete'), 'confirm_delete', '', 0, 1);
			if ($ret == 'html') print '<br />';
			//to have the object to be deleted in the background
		}


}
dol_fiche_end();

// End of page
llxFooter();
$db->close();
