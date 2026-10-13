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
    'ACCESS_GATE_EXPLAIN' => 'Als toegang tot deze categorie wordt geweigerd, wordt ook de toegang tot forums en inhoud eronder geweigerd. De opgeslagen rechten van onderliggende forums worden niet gewijzigd.',
));
