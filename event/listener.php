<?php
/**
*
* Access Gateway extension for phpBB
*
* @copyright (c) bennybernaer
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace bennybernaer\accessgateway\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
    /** @var \phpbb\db\driver\driver_interface */
    protected $db;

    /** @var \phpbb\request\request_interface */
    protected $request;

    /** @var \phpbb\template\template */
    protected $template;

    /** @var \phpbb\user */
    protected $user;

    /** @var string */
    protected $gateway_table;

    public function __construct(
        \phpbb\db\driver\driver_interface $db,
        \phpbb\request\request_interface $request,
        \phpbb\template\template $template,
        \phpbb\user $user,
        $table_prefix
    )
    {
        $this->db = $db;
        $this->request = $request;
        $this->template = $template;
        $this->user = $user;
        $this->gateway_table = $table_prefix . 'access_gateway';
    }

    public static function getSubscribedEvents()
    {
        return array(
            'core.acp_manage_forums_display_form'      => 'display_forum_form',
            'core.acp_manage_forums_update_data_after' => 'save_gateway_setting',
        );
    }

    public function display_forum_form($event)
    {
        $this->user->add_lang_ext('bennybernaer/accessgateway', 'access_gateway');

        $forum_data = $event['forum_data'];
        $forum_id = !empty($forum_data['forum_id']) ? (int) $forum_data['forum_id'] : 0;
        $enabled = false;

        if ($forum_id)
        {
            $sql = 'SELECT forum_id
                    FROM ' . $this->gateway_table . '
                    WHERE forum_id = ' . $forum_id;
            $result = $this->db->sql_query($sql);
            $enabled = (bool) $this->db->sql_fetchrow($result);
            $this->db->sql_freeresult($result);
        }

        $this->template->assign_var('S_ACCESS_GATE', $enabled);
    }

    public function save_gateway_setting($event)
    {
        $forum_data = $event['forum_data'];
        $errors = $event['errors'];

        if (!empty($errors) || empty($forum_data['forum_id']))
        {
            return;
        }

        $forum_id = (int) $forum_data['forum_id'];
        $forum_type = isset($forum_data['forum_type'])
            ? (int) $forum_data['forum_type']
            : FORUM_POST;

        $enabled = (
            $forum_type === FORUM_CAT
            && $this->request->variable('access_gateway', false)
        );

        $sql = 'DELETE FROM ' . $this->gateway_table . '
                WHERE forum_id = ' . $forum_id;
        $this->db->sql_query($sql);

        if ($enabled)
        {
            $sql = 'INSERT INTO ' . $this->gateway_table . ' ' .
                $this->db->sql_build_array('INSERT', array(
                    'forum_id' => $forum_id,
                ));
            $this->db->sql_query($sql);
        }
    }
}
