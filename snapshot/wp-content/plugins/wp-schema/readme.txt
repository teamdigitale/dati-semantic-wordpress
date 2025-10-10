=== WP Schema Plugin ===
Contributors: luca
Tags: schema, search, custom search form
Requires at least: 5.0
Tested up to: 5.8
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==
WP Schema Plugin allows you to define custom search functionality within your WordPress site. This plugin provides a shortcode to include a custom search form on any page.

== Features ==
* Custom semantic search form with filters for type, rights holder, and theme.
* AJAX-powered results and pagination.
* Shortcodes for easy integration in pages and posts.
* Theme mapping and helper utilities.
* Dashboard and statistics functions (if enabled).

== Installation ==
1. Upload the plugin files to the `/wp-content/plugins/wp-schema` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the Settings->WP Schema screen to configure the plugin.

== Configuration ==
In the `config.php` file, you can define the necessary variables for the plugin to function.

The variable `$ID-search-page` should contain the ID of the page where the search script is executed.
You can call the search file using the shortcode `[custom_search_form]`.

Additionally, there is a configuration file for themes: `ThemesDataHelper.php`
 * which defines the mapping of themes.

== Shortcodes ==
The following shortcodes are available in this plugin:

* `[custom_search_form]` — Displays the custom search form with filters for type, rights holder, and theme.
* `[wp_schema_stats_total]` — Shows the total number of semantic resources for the current or specified year.
* `[wp_schema_stats_total_over_last_year]` — Shows the absolute increment of resources compared to the previous year.
* `[wp_schema_stats_total_percentage_over_last_year]` — Shows the percentage increment of resources compared to the previous year.
* `[wp_schema_stats_cv]` — Shows the total number of controlled vocabularies.
* `[wp_schema_stats_cv_over_last_year]` — Shows the absolute increment of controlled vocabularies compared to the previous year.
* `[wp_schema_stats_cv_percentage_over_last_year]` — Shows the percentage increment of controlled vocabularies compared to the previous year.
* `[wp_schema_stats_ontology]` — Shows the total number of ontologies.
* `[wp_schema_stats_ontology_over_last_year]` — Shows the absolute increment of ontologies compared to the previous year.
* `[wp_schema_stats_ontology_percentage_over_last_year]` — Shows the percentage increment of ontologies compared to the previous year.
* `[wp_schema_stats_schema]` — Shows the total number of schemas.
* `[wp_schema_stats_schema_over_last_year]` — Shows the absolute increment of schemas compared to the previous year.
* `[wp_schema_stats_schema_percentage_over_last_year]` — Shows the percentage increment of schemas compared to the previous year.
* `[wp_schema_print_all_stats]` — Prints all statistics in a debug format (for administrators).

== File Structure ==
* `config.php` - Main configuration file.
* `functions/HelperFunctions.php` - Core logic for search and results rendering.
* `functions/AjaxHandlers.php` - Handles AJAX requests for search and pagination.
* `functions/ThemesDataHelper.php` - Theme mapping and helpers.
* `templates/search.php` - Template for the search form and results.
* `templates/details.php` - Template for the resource details page.
* `functions/Dashboard/` - Dashboard and statistics related functions.

== Hooks & Filters ==
This plugin registers the following AJAX actions:
- `wp_ajax_load_more_results`
- `wp_ajax_nopriv_load_more_results`
- `wp_ajax_load_search_results`
- `wp_ajax_nopriv_load_search_results`

== Frequently Asked Questions ==
= How do I configure the search page? =
Edit the `config.php` file and set the `$ID-search-page` variable to the ID of your desired search page.

= How do I use the custom search form? =
Simply add the shortcode `[custom_search_form]` to any page or post where you want the search form to appear.

== Support ==
For support or to contribute, open an issue on the project repository or contact the author.

== Changelog ==
= 1.0 =
* Initial release of WP Schema Plugin.

== Upgrade Notice ==
= 1.0 =
Initial release.

