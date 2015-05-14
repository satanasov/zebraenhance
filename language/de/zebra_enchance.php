<?php

/**
*
* Zebra Enhance [German]
*
* @package language
* @version $Id$
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
* German translation by franki (http://dieahnen.de/ahnenforum/)
*/

if (!defined('IN_PHPBB'))
{
	exit;
}
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'UCP_ZEBRA_PENDING_IN'					=> 'Warten auf Bestätigung',
	'UCP_ZEBRA_PENDING_IN_EXP'				=> 'Liste mit Anfragen warten auf Deine Zustimmung.',

	'UCP_ZEBRA_PENDING_OUT'					=> 'Noch zu bestätigen',
	'UCP_ZEBRA_PENDING_OUT_EXP'				=> 'Liste mit Deinen Anfragen warten auf Bestätigung.',

	'UCP_ZEBRA_PENDING_NONE'				=> 'Keine anstehenden Anfragen',

	'UCP_ZEBRA_ENCHANCE_CONFIRM_CANCEL_ASK'	=> 'Bist du sicher, dass Du die Freundschaftsanfrage abbrechen möchtest?',
	'UCP_ZEBRA_ENCHANCE_CONFIRM_CANCEL'		=> 'Freundschaftsanfrage wurde abgelehnt!',

	'NOTIFICATION_TYPE_ZEBRA_ADD'			=> 'Benachrichtigung bei neuen Freundschaftsanfragen',
	'NOTIFICATION_ZEBRA_ADD'				=> '%1$s schickt dir eine Freundschaftsanfrage!',

	'NOTIFICATION_TYPE_ZEBRA_CONFIRM'		=> 'Bestätigung für Freundschaftsanfrage',
	'NOTIFICATION_ZEBRA_CONFIRM'			=> '%1$s hat deine Freundschaftsanfrage bestätigt!',

	'FRINEDLIST_TITLE'						=> 'Freundesliste',

	'NOT_ENEMY'								=> 'Alle außer Feinde',
	'SPECIAL_FRIENDS'						=> 'Besondere Freunde',

	'ZE_FRIENDLIST'							=> 'Freundesliste',
	'ZE_FRIENDLIST_EXPLAIN'					=> 'Wer kann deine Freundeliste sehen?',

	'FRIENDLIST_ERROR_ACCESS'				=> 'Du hast keine Berechtigung die Benutzer-Freundesliste zu sehen.',

));
