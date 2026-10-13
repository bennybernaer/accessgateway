<?php
/**
*
* Access Gateway extension for phpBB
*
* @copyright (c) bennybernaer
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace bennybernaer\accessgateway\auth;

/**
* Central ACL gateway implementation.
*
* Child ACL rows are never changed. If an enabled gateway category is not
* visible to the user, local permissions below it are treated as denied.
*/
class auth extends \phpbb\auth\auth
{
    /** @var \phpbb\db\driver\driver_interface */
    protected $db;

    /** @var string */
    protected $table_prefix;

    /** @var array|null */
    protected $gateway_forums = null;

    /** @var array */
    protected $gateway_user_acl = array();

    public function __construct(\phpbb\db\driver\driver_interface $db, $table_prefix)
    {
        $this->db = $db;
        $this->table_prefix = $table_prefix;
    }

    /**
    * Initialise phpBB permissions and reset per-user gateway caches.
    */
    public function acl(&$userdata)
    {
        $this->gateway_user_acl = array();
        return parent::acl($userdata);
    }

    /**
    * Look up an ACL option and apply the category gateway to local options.
    */
    public function acl_get($opt, $f = 0)
    {
        $result = parent::acl_get($opt, $f);

        $negate = false;
        $base_opt = $opt;

        if (strpos($base_opt, '!') === 0)
        {
            $negate = true;
            $base_opt = substr($base_opt, 1);
        }

        if (
            $f != 0
            && isset($this->acl_options['local'][$base_opt])
            && !$this->forum_access_gate_allows((int) $f)
        )
        {
            // Store the effective result so acl_getf() and acl_getf_global(),
            // which may read the ACL cache directly, see the gateway denial too.
            $this->cache[$f][$base_opt] = false;

            return $negate ? true : false;
        }

        return $result;
    }

    /**
    * Filter permission lists for arbitrary users through the same gateway.
    */
    public function acl_get_list($user_id = false, $opts = false, $forum_id = false)
    {
        $auth_ary = parent::acl_get_list($user_id, $opts, $forum_id);

        foreach ($auth_ary as $current_forum_id => $option_ary)
        {
            $current_forum_id = (int) $current_forum_id;

            if (!$current_forum_id)
            {
                continue;
            }

            foreach ($option_ary as $auth_option => $users)
            {
                foreach ($users as $key => $current_user_id)
                {
                    if (!$this->forum_access_gate_allows_user((int) $current_user_id, $current_forum_id))
                    {
                        unset($auth_ary[$current_forum_id][$auth_option][$key]);
                    }
                }

                if (isset($auth_ary[$current_forum_id][$auth_option]))
                {
                    $auth_ary[$current_forum_id][$auth_option] = array_values(
                        $auth_ary[$current_forum_id][$auth_option]
                    );

                    if (!$auth_ary[$current_forum_id][$auth_option])
                    {
                        unset($auth_ary[$current_forum_id][$auth_option]);
                    }
                }
            }

            if (isset($auth_ary[$current_forum_id]) && !$auth_ary[$current_forum_id])
            {
                unset($auth_ary[$current_forum_id]);
            }
        }

        return $auth_ary;
    }

    /**
    * Check all ancestor categories for the currently initialised user.
    */
    protected function forum_access_gate_allows($forum_id)
    {
        $this->load_gateway_forums();

        if (!$forum_id || !isset($this->gateway_forums[$forum_id]))
        {
            return true;
        }

        $visited = array();
        $parent_id = (int) $this->gateway_forums[$forum_id]['parent_id'];

        while ($parent_id)
        {
            if (isset($visited[$parent_id]))
            {
                break;
            }

            $visited[$parent_id] = true;

            if (!isset($this->gateway_forums[$parent_id]))
            {
                break;
            }

            $parent = $this->gateway_forums[$parent_id];

            if (
                !empty($parent['is_gateway'])
                && !parent::acl_get('f_list', $parent_id)
            )
            {
                return false;
            }

            $parent_id = (int) $parent['parent_id'];
        }

        return true;
    }

    /**
    * Check all gateway ancestors for an arbitrary user.
    */
    protected function forum_access_gate_allows_user($user_id, $forum_id)
    {
        $this->load_gateway_forums();

        if (!$forum_id || !isset($this->gateway_forums[$forum_id]))
        {
            return true;
        }

        if (!isset($this->gateway_user_acl[$user_id]))
        {
            $this->gateway_user_acl[$user_id] = $this->acl_raw_data_single_user($user_id);
        }

        $f_list_id = isset($this->acl_options['id']['f_list'])
            ? (int) $this->acl_options['id']['f_list']
            : 0;

        if (!$f_list_id)
        {
            return false;
        }

        $user_acl = $this->gateway_user_acl[$user_id];
        $visited = array();
        $parent_id = (int) $this->gateway_forums[$forum_id]['parent_id'];

        while ($parent_id)
        {
            if (isset($visited[$parent_id]))
            {
                break;
            }

            $visited[$parent_id] = true;

            if (!isset($this->gateway_forums[$parent_id]))
            {
                break;
            }

            $parent = $this->gateway_forums[$parent_id];

            if (!empty($parent['is_gateway']))
            {
                $setting = isset($user_acl[$parent_id][$f_list_id])
                    ? (int) $user_acl[$parent_id][$f_list_id]
                    : ACL_NO;

                if ($setting !== ACL_YES)
                {
                    return false;
                }
            }

            $parent_id = (int) $parent['parent_id'];
        }

        return true;
    }

    /**
    * Load the forum hierarchy and enabled gateway categories once per request.
    */
    protected function load_gateway_forums()
    {
        if ($this->gateway_forums !== null)
        {
            return;
        }

        $this->gateway_forums = array();
        $gateway_table = $this->table_prefix . 'access_gateway';

        $sql = 'SELECT f.forum_id, f.parent_id, f.forum_type,
                       g.forum_id AS gateway_forum_id
                FROM ' . FORUMS_TABLE . ' f
                LEFT JOIN ' . $gateway_table . ' g
                    ON g.forum_id = f.forum_id';
        $result = $this->db->sql_query($sql);

        while ($row = $this->db->sql_fetchrow($result))
        {
            $this->gateway_forums[(int) $row['forum_id']] = array(
                'parent_id' => (int) $row['parent_id'],
                'is_gateway' => (
                    (int) $row['forum_type'] === FORUM_CAT
                    && !empty($row['gateway_forum_id'])
                ),
            );
        }

        $this->db->sql_freeresult($result);
    }
}
