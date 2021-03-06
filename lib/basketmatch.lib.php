<?php
/*
 * Copyright (C) 2007-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2018	   Patrick DELCROIX     <pmpdelcroix@gmail.com>
 *  * Copyright (C) ---Put here your own copyright and developer email---
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
 *   	\file       dev/basketmatchs/basketmatch_page.php
 *		\ingroup    mymodule othermodule1 othermodule2
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2020-07-15 15:18
 */

function BasketMatchReloadPage($backtopage,$id,$ref){
        if (!empty($backtopage)){
            header("Location: ".$backtopage);
        }else if (!empty($ref) ){
            header("Location: ".dol_buildpath("/basket/basketmatch_card.php", 1).'?id='.$id.'&action=view');
        }else if ($id>0)
        {
           header("Location: ".dol_buildpath("/basket/basketmatch_card.php", 1).'?id='.$id);
        }else{
           header("Location: ".dol_buildpath("/basket/basketmatch_list.php", 1));

        }
    exit();
}
/**
 * Prepare array of tabs for BasketMatch
 *
 * @param	BasketMatch	$object		BasketMatch
 * @return 	array					Array of tabs
 */
function BasketMatchPrepareHead($object)
{
	global $db, $langs, $conf;

	$langs->load("basket@basket");

	$h = 0;
	$head = array();
	$head[$h][0] = dol_buildpath("/basket/basketmatch_card.php", 1).'?id='.$object->id;
	$head[$h][1] = $langs->trans("Card");
	$head[$h][2] = 'card';
	$h++;

	if (isset($object->fields['note_public']) || isset($object->fields['note_private']))
	{
		$nbNote = 0;
		if (!empty($object->note_private)) $nbNote++;
		if (!empty($object->note_public)) $nbNote++;
		$head[$h][0] = dol_buildpath('/basket/basketmatch_note.php', 1).'?id='.$object->id;
		$head[$h][1] = $langs->trans('Notes');
		if ($nbNote > 0) $head[$h][1].= ' <span class="badge">'.$nbNote.'</span>';
		$head[$h][2] = 'note';
		$h++;
	}

	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
	require_once DOL_DOCUMENT_ROOT.'/core/class/link.class.php';
	$upload_dir = $conf->mymodule->dir_output . "/BasketMatch/" . dol_sanitizeFileName($object->ref);
	$nbFiles = count(dol_dir_list($upload_dir,'files',0,'','(\.meta|_preview.*\.png)$'));
	$nbLinks=Link::count($db, $object->element, $object->id);


	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	//$this->tabs = array(
	//	'entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__'
	//); // to add new tab
	//$this->tabs = array(
	//	'entity:-tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__'
	//); // to remove a tab
	complete_head_from_modules($conf, $langs, $object, $head, $h, 'basket@basket');

	return $head;
}
function basket_admin_prepare_head()
{
	global $langs;

	$langs->load("basket");
	$h = 0;
	$head = array();

	$head[$h][0] = DOL_URL_ROOT.'/admin/user.php';
	$head[$h][1] = $langs->trans("Parameters");
	$head[$h][2] = 'card';
	$h++;


	return $head;
}
