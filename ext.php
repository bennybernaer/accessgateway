<?php
/**
*
* Access Gateway extension for phpBB
*
* @copyright (c) bennybernaer
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace bennybernaer\accessgateway;

class ext extends \phpbb\extension\base
{
    public function is_enableable()
    {
        return phpbb_version_compare(PHPBB_VERSION, '3.3.16', '>=')
            && phpbb_version_compare(PHPBB_VERSION, '4.0.0', '<');
    }
}
