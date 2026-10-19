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
    'ACCESS_GATE'         => 'Act as access gateway',
    'ACCESS_GATE_EXPLAIN' => 'If this is set to “Yes”, users who are denied access to this category are also denied access to the forums and content inside it. Child forum permissions themselves are not changed.<br><br>The default for this setting is “No”. In that case, denying access to this category does not affect access to forums and content inside it. The category is hidden from those users on the board index, but access to child forums and their content remains governed by their own forum permissions.',
));
