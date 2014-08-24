<?php

/**
*
* Zebraenhance [Dutch] Translated by Dutch Translators (https://github.com/dutch-translators)
*
* @package language
* @version $Id$
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
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
	'UCP_ZEBRA_PENDING_IN'	=>	'Wachten op goedkeuring',
	'UCP_ZEBRA_PENDING_IN_EXP'	=>	'Lijst met vriendschapsverzoeken wachtend op jouw goedkeuring.',

	'UCP_ZEBRA_PENDING_OUT'	=>	'Wachten op goedkeuring',
	'UCP_ZEBRA_PENDING_OUT_EXP'	=>	'Lijst met jou vriendschapsverzoeken wachtend goedkeuring.',

	'UCP_ZEBRA_PENDING_NONE'	=>	'Geen wachtende vriendschapsverzoeken',

	'UCP_ZEBRA_ENCHANCE_CONFIRM_CANCEL_ASK'	=>	'Weet je zeker dat je deze vriendschapsverzoek wilt wijgeren?',
	'UCP_ZEBRA_ENCHANCE_CONFIRM_CANCEL'	=> 'Vriendschapsverzoek is geweigerd!',

	'NOTIFICATION_TYPE_ZEBRA_ADD'	=>	'Nieuwe vriendschapsverzoek notificatie',
	'NOTIFICATION_ZEBRA_ADD'	=>	'%1$s heeft je een vriendsschapsverzoek gestuurd!',

	'NOTIFICATION_TYPE_ZEBRA_CONFIRM'	=>	'Bevestiging van je vriendschapsverzoek',
	'NOTIFICATION_ZEBRA_CONFIRM'	=>	'%1$s heeft je vriendschapsverzoek goedgekeurd!',

	'FRINEDLIST_TITLE'	=>	'Vriendenlijst',

	'NOT_ENEMY'	=>	'Alle behalve vijanden',
	'SPECIAL_FRIENDS'	=>	'Speciale Vrienden',

	'ZE_FRIENDLIST'	=>	'Vriendenlijst',
	'ZE_FRIENDLIST_EXPLAIN'	=>	'Wie kan je vriendenlijst zien?',

	'FRIENDLIST_ERROR_ACCESS'	=>	'Je hebt geen toegang tot de vriendenlijst van deze gebruiker.',

));
