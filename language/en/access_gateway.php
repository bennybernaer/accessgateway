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
    'ACCESS_GATE_EXPLAIN' => 'If access to this category is denied, access to forums and content below it is also denied. Child forum permissions themselves are not changed.',
));
