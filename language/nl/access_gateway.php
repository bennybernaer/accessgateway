<?php
/**
*
* Access Gateway extension for phpBB
*
* @copyright (c) bennybernaer
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

if (!defined('IN_PHPBB'))
{
    exit;
}

$lang = array_merge($lang, array(
    'ACCESS_GATE'         => 'Als toegangspoort gebruiken',
    'ACCESS_GATE_EXPLAIN' => 'Als deze optie op “Ja” staat, wordt voor gebruikers die geen toegang hebben tot deze categorie ook de toegang tot de forums en inhoud binnen deze categorie geweigerd. De permissies van de onderliggende forums zelf worden niet gewijzigd.<br><br>Standaard staat deze optie op “Nee”. In dat geval heeft het weigeren van toegang tot deze categorie geen invloed op de toegang tot de forums en inhoud daarbinnen. De categorie wordt voor die gebruikers niet weergegeven op de forumindex, maar de toegang tot onderliggende forums en hun inhoud blijft bepaald door de afzonderlijke forumpermissies.',
));
