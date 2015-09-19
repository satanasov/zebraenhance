<?php

/**
*
* Zebra Enhance [Russian]
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
	'UCP_ZEBRA_PENDING_IN'		=> 'Ожидание подтверждения',
	'UCP_ZEBRA_PENDING_IN_EXP'	=> 'Список запросов, ожидающих вашего одобрения.',
	'UCP_ZEBRA_PENDING_OUT'		=> 'Ожидание подтверждения',
	'UCP_ZEBRA_PENDING_OUT_EXP'	=> 'Ваши запросы ожидающие одобрения.',
	'UCP_ZEBRA_PENDING_NONE'	=> 'Нет ожидающих запросов',
	'UCP_ZEBRA_ENCHANCE_CONFIRM_CANCEL_ASK'	=> 'Вы действительно хотите отменить запрос?',
	'UCP_ZEBRA_ENCHANCE_CONFIRM_CANCEL'		=> 'Запрос был отменён!',
	'NOTIFICATION_TYPE_ZEBRA_ADD'			=> 'Запрос на добавление в друзья',
	'NOTIFICATION_ZEBRA_ADD'				=> '%1$s ожидает подтверждения для добавления в друзья!',
	'NOTIFICATION_TYPE_ZEBRA_CONFIRM'		=> 'Подтверждение запроса на добавление в друзья',
	'NOTIFICATION_ZEBRA_CONFIRM'			=> 'Запрос одобрен и %1$s теперь ваш друг.',
	'FRINEDLIST_TITLE'			=> 'Список друзей',
	'NOT_ENEMY'					=> 'Все кроме врагов',
	'SPECIAL_FRIENDS'			=> 'Особые друзья',
	'ZE_FRIENDLIST'				=> 'Список друзей',
	'ZE_FRIENDLIST_EXPLAIN'		=> 'Кому разрешён просмотр вашего списка?',
	'FRIENDLIST_ERROR_ACCESS'	=> 'Вы не имеете права просмотра списка друзей.',
));
