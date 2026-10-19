# Changelog

All notable changes to this project are documented in this file.

## 1.0.1

- Clarified the English description of the **Act as access gateway** option.
- Improved the Dutch translation and clarified the default behaviour.
- Documented where the gateway setting can be found in the ACP.
- Documented that normal group forum permissions remain managed separately.
- No changes to the actual gateway permission logic.

## 1.0.0

- Initial release.
- Added the ability to make a category act as an access gateway.
- If a user cannot access the gateway category, local access below it is denied.
- Child forum permissions are not copied, overwritten or changed.
- No phpBB core-file changes are required.
