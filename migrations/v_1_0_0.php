<?php
/**
*
* Access Gateway extension for phpBB
*
* @copyright (c) bennybernaer
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace bennybernaer\accessgateway\migrations;

class v_1_0_0 extends \phpbb\db\migration\migration
{
    public function effectively_installed()
    {
        return isset($this->config['access_gateway_version'])
            && version_compare($this->config['access_gateway_version'], '1.0.0', '>=');
    }

    public static function depends_on()
    {
        return array();
    }

    public function update_schema()
    {
        return array(
            'add_tables' => array(
                $this->table_prefix . 'access_gateway' => array(
                    'COLUMNS' => array(
                        'forum_id' => array('UINT', 0),
                    ),
                    'PRIMARY_KEY' => 'forum_id',
                ),
            ),
        );
    }

    public function revert_schema()
    {
        return array(
            'drop_tables' => array(
                $this->table_prefix . 'access_gateway',
            ),
        );
    }

    public function update_data()
    {
        return array(
            array('config.add', array('access_gateway_version', '1.0.0')),
        );
    }
}
