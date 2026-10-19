# Access Gateway 1.0.1

Author: **bennybernaer**

A phpBB 3.3.x extension that allows a category to act as an access gateway.

When an ancestor category is configured as a gateway and the user cannot see
that category (`f_list`), local permissions below that category are treated as
denied. Child forum permissions stored in the database are not modified.

## Download

Download the latest stable release from:

https://github.com/bennybernaer/accessgateway/releases/tag/v1.0.1

The ready-to-install package is:

`bennybernaer_accessgateway_1.0.1.zip`

## Requirements

- phpBB 3.3.16 or newer
- phpBB 3.x (not phpBB 4)
- PHP 7.1.3 or newer

phpBB 3.3.16 is required because the extension uses the
`acp_forums_cat_options_append` template event.

## Installation

Upload:

    bennybernaer/accessgateway

to:

    ext/bennybernaer/accessgateway

Then enable **Access Gateway** under:

    ACP > Customise > Manage extensions

## Configuration

The gateway setting belongs to the category's forum settings. To change it, go to:

    ACP > Forums > Manage forums

Then click the **green gear icon** for the category you want to edit and set
**Act as access gateway** to Yes or No.

Clicking the category name itself opens that category; it does not open the
settings page where the gateway option is shown.

Normal user/group forum permissions remain in phpBB's permissions section. For
example, group access can be changed under:

    ACP > Permissions > Group forum permissions

This separation is normal: the extension adds a setting to the category itself,
while phpBB continues to manage user and group permissions in its existing
permissions screens.

## Behaviour

- Gateway = No (default): normal phpBB behaviour. Hiding/denying the category itself does not automatically deny access to its child forums; their own permissions still apply.
- Gateway = Yes and user can see the category: child ACLs work normally.
- Gateway = Yes and user cannot see the category: local access below it is denied.
- Stored child ACLs are never copied, overwritten or changed.
- Restoring access to the category immediately restores the original child ACL behaviour.

## Technical note

This extension replaces phpBB's `auth` service with a subclass of
`\phpbb\auth\auth` so the check happens centrally without modifying core files.

The extension also filters `acl_get_list()` results through the gateway and
stores effective gateway denials in the ACL cache so `acl_getf()` and
`acl_getf_global()` cannot bypass the gateway by reading a previously cached
raw result.

As with any extension replacing a core service, test compatibility with other
extensions that also replace the `auth` service.

## Version 1.0.1

Version 1.0.1 is a documentation and language clarification release based on
real-world testing. It does not change the gateway permission logic.
