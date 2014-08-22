<?php

/**
*
* Friend list enchance [Spanish]
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
	'UCP_ZEBRA_PENDING_IN'	=>	'Esperando confirmación',
	'UCP_ZEBRA_PENDING_IN_EXP'	=>	'Lista de solicitudes esperando su aprobación.',

	'UCP_ZEBRA_PENDING_OUT'	=>	'Pendiente de confirmación',
	'UCP_ZEBRA_PENDING_OUT_EXP'	=>	'Lista de solicitudes pendientes de aprobación.',

	'UCP_ZEBRA_PENDING_NONE'	=>	'No hay solicitudes pendientes',

	'UCP_ZEBRA_ENCHANCE_CONFIRM_CANCEL_ASK'	=>	'¿Seguro que desea cancelar la solicitud de amistad?',
	'UCP_ZEBRA_ENCHANCE_CONFIRM_CANCEL'	=> '¡La solicitud de amistad ha sido cancelada!',

	'NOTIFICATION_TYPE_ZEBRA_ADD'	=>	'Notificación de nueva solicitud de amistad',
	'NOTIFICATION_ZEBRA_ADD'	=>	'¡%1$s le envió una solicitud de amistad!',

	'NOTIFICATION_TYPE_ZEBRA_CONFIRM'	=>	'Confirmación de solicitud de amistad',
	'NOTIFICATION_ZEBRA_CONFIRM'	=>	'¡%1$s ha confirmado su solicitud de amistad!',

	'FRINEDLIST_TITLE'	=>	'Lista de amigos',

	'NOT_ENEMY'	=>	'Todos excepto ignorados',
	'SPECIAL_FRIENDS'	=>	'Amigos especiales',

	'ZE_FRIENDLIST'	=>	'Lista de amigos',
	'ZE_FRIENDLIST_EXPLAIN'	=>	'¿Quién puede ver tu Lista de amigos?',

	'FRIENDLIST_ERROR_ACCESS'	=>	'Usted no tiene acceso para ver la Lista de amigos del usuario.',

));
