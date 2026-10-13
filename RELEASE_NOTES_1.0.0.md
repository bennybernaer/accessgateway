# Access Gateway 1.0.0

Initial public release.

## What it does
- Lets a category act as an access gateway.
- If the user cannot access the gateway category, local access below it is denied.
- Child forum permissions are not copied, overwritten or changed.
- No phpBB core-file changes are required.

## Requirements
- phpBB 3.3.16+
- phpBB 3.x
- PHP 7.1.3+

## Installation
Upload `bennybernaer/accessgateway` to `ext/bennybernaer/accessgateway/` and enable it in ACP → Customise → Manage extensions.
