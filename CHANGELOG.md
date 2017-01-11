# Changelog

## 2.2.4 - 2017-01-10

- Refactored themes to inherit from a parent theme for easier theming
- Added Neon theme originally by PayBas
- Added fixes for we_universal and we_clearblue styles
- Internal code improvements

## 2.2.3 - 2016-05-01

- Improve compatibility with phpBB 3.1.x and 3.2.x
- Added support for phpBB 3.2's s9e/textformatter utilities
- Added missing ALT attribute to fallback no-avatar image in HTML
- Refactored topic preview's trim tools
- Improved TWIG code
- Added Bulgarian Translation
- Added Hungarian Translation

## 2.2.2 - 2015-09-14

- Improved avatar handling including an issue where avatars would not load in phpBB 3.1.7
- Support lazy loading of avatars
- Improved BBCode handling
- Completed transition to TWIG template syntax
- Added Chinese Translation

## 2.2.1 - 2015-02-09

- Converted to TWIG template syntax
- Updated support for Top Five extension
- Updated French Translation
- Added Russian Translation
- Added Turkish Translation
- Added Swedish Translation
- Added Croatian Translation
- Added Arabic Translation

## 2.2.0 - 2015-01-30

- Improved handling of nested bbcodes
- Improved theme handling (fallback to No theme if expected theme is missing)
- Removed version number displayed in ACP module title
- Major internal coding revisions for improving code quality

## 2.1.0-RC1 - 2014-12-03

- Fix issue where recursive BBCode stripping could fail
- Fix some issues with broken avatars
- Fix handling of missing CSS theme files
- Add support for Top Five extension
- Use UTF8 chars in language files
- Various code refactoring

## 2.1.0-b3 - 2014-07-12

- Updated for compatibility with phpBB 3.1.0-RC2

## 2.1.0-b2 - 2014-06-23

- Avatar icons keep their original proportions when scaled to fit
- Refactored the codebase along with minor improvements and updates
- Complete Persian translation
- Ensure Similar Topics is installed on phpBB 3.1.0-b4 or later
- Switched to phpBB test framework
- Added additional test coverage of the codebase

## 2.1.0-b1 - 2014-06-06

- Ported from MOD to the new Extension format for phpBB 3.1
