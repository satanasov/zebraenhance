<?php

/**
*
* Zebra Enhance [Brazilian Portuguese [pt_br]]
* Brazilian Portuguese translation by eunaumtenhoid (c) 2017 [ver 1.0.4] (https://github.com/phpBBTraducoes)
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
	'UCP_ZEBRA_PENDING_IN'	=>	'Esperando confirmação',
	'UCP_ZEBRA_PENDING_IN_EXP'	=>	'Lista com solicitações aguardando sua aprovação.',

	'UCP_ZEBRA_PENDING_OUT'	=>	'Confirmação pendente',
	'UCP_ZEBRA_PENDING_OUT_EXP'	=>	'Lista com suas solicitações pendentes de aprovação.',

	'UCP_ZEBRA_PENDING_NONE'	=>	'Sem solicitações pendentes',

	'UCP_ZEBRA_ENCHANCE_CONFIRM_CANCEL_ASK'	=>	'Tem certeza de que deseja cancelar a solicitação de amizade?',
	'UCP_ZEBRA_ENCHANCE_CONFIRM_CANCEL'	=> 'A solicitação de amizade foi cancelada!',

	'NOTIFICATION_TYPE_ZEBRA_ADD'	=>	'Nova notificação de solicitação de amizade',
	'NOTIFICATION_ZEBRA_ADD'	=>	'%1$s lhe enviou uma solicitação de amizade!',

	'NOTIFICATION_TYPE_ZEBRA_CONFIRM'	=>	'Confirmação para solicitação de amizade',
	'NOTIFICATION_ZEBRA_CONFIRM'	=>	'%1$s Confirmou sua solicitação de amizade!',

	'FRINEDLIST_TITLE'	=>	'Lista de Amigos',

	'NOT_ENEMY'	=>	'Todos exceto inimigos',
	'SPECIAL_FRIENDS'	=>	'Amigos especiais',

	'ZE_FRIENDLIST'	=>	'Lista de Amigos',
	'ZE_FRIENDLIST_EXPLAIN'	=>	'Quem pode ver sua lista de amigos?',

	'FRIENDLIST_ERROR_ACCESS'	=>	'Você não tem acesso para ver a lista de amigos do usuário.',

));
