# Access Gateway 1.0.0

Author: **bennybernaer**

A phpBB 3.3.x extension that allows a category to act as an access gateway.

When an ancestor category is configured as a gateway and the user cannot see
that category (`f_list`), local permissions below that category are treated as
denied. Child forum permissions stored in the database are not modified.

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

Edit a category under:

    ACP > Forums > Manage forums

and set **Act as access gateway** to Yes.

## Behaviour

- Gateway = No: normal phpBB behaviour.
- Gateway = Yes and user can see the category: child ACLs work normally.
- Gateway = Yes and user cannot see the category: local access below it is denied.
- Stored child ACLs are never copied, overwritten or changed.
- Restoring access to the category immediately restores the original child ACL behaviour.

## Technical note

This extension replaces phpBB's `auth` service with a subclass of
`\phpbb\auth\auth` so the check happens centrally without modifying core files.

Version 1.0.0 also filters `acl_get_list()` results through the gateway and
stores effective gateway denials in the ACL cache so `acl_getf()` and
`acl_getf_global()` cannot bypass the gateway by reading a previously cached
raw result.

As with any extension replacing a core service, test compatibility with other
extensions that also replace the `auth` service.
