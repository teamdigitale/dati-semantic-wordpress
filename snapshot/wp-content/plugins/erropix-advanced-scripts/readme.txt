=== Advanced Scripts ===
Tags: custom code, custom css, custom php, sass, less
Requires at least: 5.0
Tested up to: 6.7.2
Requires PHP: 7.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

This plugin allows you to create advanced custom scripts and styles using an intuitive interface

== Description ==

Advanced Scripts is the best scripts manager on the market. Besides PHP, CSS, and Javascript, you can also write SCSS and LESS code, use custom hooks, and all of that in a better interface.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/erropix-advanced-scripts` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Go to `Tool > Advanced Scripts` and create your awesome scripts.

== Changelog ==

= 2.6.1 - April 15, 2025 =

-   Fix: Fixed a bug with the `contains` operator causing a fatal PHP error.

= 2.6.0 - April 14, 2025 =

-   New: Added 10 new filters to the Conditions Editor for greater control and flexibility.
-   New: Split the Expression filter into `WP Expression` and `PHP Expression` variants.
-   New: Introduced the Match operator with support for wildcard pattern matching.
-   New: Display badges indicating required hooks for filters.
-   New: Updated fields layout for a clearer, more consistent editing experience.
-   Fix: Improved performance when loading the post list on large sites.

= 2.5.2 - December 31, 2023 =

-   Fix: Upgraded the ACE editor to the latest version.
-   Fix: False linter errors with css variables.

= 2.5.1 - December 15, 2023 =

-   Fix: Fatal error with PHP scripts that define a namespace.

= 2.5.0 - December 13, 2023 =

-   New: Revamped storage system with permanent files instead of temporary files.
-   New: SCSS partials for modular stylesheets to reduce repetition.
-   New: Site health checks to ensure that advanced scripts is running correctly.

= 2.4.1 - July 9, 2023 =

-   Fix: Sanitization of the title and description field values
-   Fix: WP Engine temporary files permissions issues for PHP snippets.

= 2.4.0 - July 5, 2023 =

-   New: Custom shortcodes now have access to the $atts, $content and $tag variables for more customization options.
-   New: Enhanced error handling with safety checks and comprehensive error reporting.
-   New: Ability to enforce the safe mode by defining the AS_SAFE_MODE constant in wp-config.php.
-   New: Added additional functions to streamline the migration process for the upcoming release.
-   Fix: Implemented a new temporary file management system for PHP scripts to solve the permissions issue.
-   Fix: Upgraded Bundled ScssPhp to the latest version.
-   Fix: Upgraded Freemius SDK to the latest version.

= 2.3.5 - December 14, 2022 =

-   Fix: compatibility issues with PHP 8.1

= 2.3.4 - February 26, 2022 =

-   Fix: Minor security issues
-   Fix: Removed conflicting emmet keyboard shortcuts

= 2.3.3 - November 16, 2021 =

-   Fix: Conflict with plugins and themes that load an older version of the ScssPhp/ScssPhp composer package.

= 2.3.2 - November 12, 2021 =

-   Fix: SCSS parser crash when Oxygen color value is NULL

= 2.3.1 - November 10, 2021 =

-   Fix: the oxygen template condition options were empty on some sites

= 2.3.0 - November 9, 2021 =

-   NEW: Added script folders for better organization
-   NEW: Added conditions for date and time
-   NEW: Added a condition for the current Oxygen template
-   NEW: Added a condition for post ancestors (parents)
-   NEW: Added a UI indicator for scripts with conditions
-   NEW: Start the import by dragging files over the scripts list
-   NEW: Ability to change the JSON filename during export

= 2.2.0 - June 7, 2021 =

-   New: added a better safe mode notice in the editor screen
-   New: allowed customization of the script shortcode tag
-   New: Renamed the script location "Manually" to "Shortcode"
-   Fixed: delete confirmation popup do not show in full screen mode
-   Fixed: oxygen context condition issues
-   Fixed: mac keyboard shortcuts conflict in the Emmet extension

= 2.1.0 - April 21, 2021 =

-   New: built-in condition for the Oxygen Builder context.
-   New: wp_body_open hook option with polyfill for the Oxygen builder.
-   New: button to toggle full screen mode.
-   New: include private, draft, and future post status for post condition.
-   New: display created scripts list after import.
-   New: option to enable scripts during import.
-   Fixed: removed the automatic admin menu collapse.
-   Fixed: keyboard shortcut ⌘ + S not working on Mac.
-   Fixed: unlink operation not permitted warnings in PHP processor.
-   Fixed: scripts are not executed when the license is inactive.
-   Fixed: uncaught type error with PHP version prior to 7.2.
-   Fixed: icons CSS conflict with the Toolset Types plugin.

= 2.0.2 - January 17, 2021 =

-   Fixed a critical bug that broke websites in some managed WordPress hosts.

= 2.0.1 - January 4, 2021 =

-   New interface that focus on ease of use.
-   New code editor with better autocomplete and Emmet support.
-   Conditions manager to restrict script execution.
-   Import/Export that supports code snippets json files.
-   Drag and drop to reorder scripts.
-   Improved the safe mode to catch more errors.

= 2.0.0 - December 22, 2020 =

-   Fixed the editor highlighting issue for long code.
-   Removed Wordpress version from CSS and javascript URL scripts.
-   Fixed the safemode notice not appearing for error types.
-   Fixed PHP warning when Oxygen builder is inactive.

= 1.1.0 - Septenber 3, 2020 =

-   Major imporvements to the interface and user experience.
-   A user-friendly safe mode and allows you to recover in case of fatal errors.

= 1.0.0 - june 19, 2020 =

-   Initial release.
