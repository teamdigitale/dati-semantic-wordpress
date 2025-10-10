=== Oxy Toolbox ===
Contributors: Gagan Goraya, Sridhar Katakam
Tags: oxygen, oxygen builder, oxygen editor
Requires at least: 4.9
Tested up to: 5.10
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds several useful and time-saving features for the Oxygen builder.

== Description ==

Current Features:

* Functionality of Oxy Class Act - lets you move or copy the styles associated with id or a class of any element to a new class; lets you reset all the styles for the class/id.
* Functionality of Oxy Cleaner - lets you rename classes and remove unused ones.
* Fullscreen - lets you toggle panels off and on and make code editor panel full width using a hotkey. Detached layout opens the center frame in a new tab/window.
* Oxy Text Edit - adds a text area to view/edit text of Heading and Text components.
* Back To Top - allows users to smoothly scroll back to the top of the page.
* Code Completion - enables Emmet for HTML and CSS abbreviation expansion in Oxygen's code editor panels.
* Conditions - adds additional conditions incl. WooCommerce specific ones in the Oxygen editor.
* Editor Tweaks
	* Autosave - enables you to automatically press Save in the Oxygen editor and optionally in a smart manner (only when there is no keyboard and mouse activity)
	* Back To WP Additions - adds Templates and Pages menu items under "Back to WP" menu in the Oxygen editor.
	* Components Panel - adds a panel of Oxygen components which appears when hovered along the top edge of Oxygen editor.
	* CSS Tweaks - adds general CSS fixes for the conditions diaglog on smaller screens, removes the unneeded scrollbars and light gray background for range slider inputs, vertically centers the color picker circle in Firefox etc.
	* Currently Editing - adds the name of current Template or Page or any other kind of entry that is currently being edited to the left of Structure button in Oxygen's editor interface.
	* Open Back To WP Menu Links In New Tabs - makes links under Back to WP Menu like Admin and Frontend buttons to open in a new tab.
* Oxygen Navigator - adds useful links in the Toolbar for directly editing Pages and Templates with Oxygen; optionally show the toolbar in the Oxygen editor if you want. Makes direct editing of any Template or Page from anywhere literally a single click away.
* SEOPress - generates automatic meta description from Oxygen Builder content.
* Oxygen Essentials - enables HTML5 support for elements like the search form and adds some CSS for making essential changes like making all images responsive and fixing the WordPress Toolbar submenu links not clickable issue with sticky header.
* Add a link to go back to the Templates list screen when editing a Oxygen Template.
JS/jQuery Scripts - registers popular scripts like Flickity, Isotope and Infinite Scroll so you can enqueue them anywhere in Oxygen.
* Remove "Themes" and "Theme Editor" menu items from the Appearance menu in the WP admin.
* Gutenberg - Adds options to disable Gutenberg and to make the Gutenberg editor full width.
* Adds a checkbox to disable Oxygen metabox on post types other than `page` in the plugin settings page.

== Installation ==

1. Click on the download link in your purchase confirmation email if you have not already downloaded it after your purchase.

2. Download the plugin's zip file.

3. Go to Plugins > Add New in your WordPress admin. Click "Add New" button, then "Upload Plugin" button, then "Choose File", browse to and select the plugin's zip file.

4. Activate the plugin.

5. Enter the license key and activate your plugin license at Oxygen > Oxy Toolbox > License.

Valid license key should be entered for the plugin to function and to receive automatic updates.

== Changelog ==

= 1.6.2 ( Dec 16, 2024 ) =
* [Classes Cleaner] Fixed used classes being detected as un-used.

= 1.6.1 ( Oct 22, 2024 ) =
* [Conditions] Removed the usage of $global variable.

= 1.6.0 ( Mar 13, 2024 ) =
* [Image Width and Height Size Attributes] Fixed PHP warnings.
* [General] Security fix.

= 1.5.9 ( Mar 01, 2023 ) =
* [Conditions] Fixed "PHP Warning: call_user_func_array() expects parameter 1 to be a valid callback.." (register_condition_user_has_written_public_only).

= 1.5.8 ( Mar 01, 2023 ) =
* [Classes Cleaner] Fixed the issue with class renaming working only on the current post.
* [Text Edit] Fixed "Invalid argument supplied for foreach()" warning.

= 1.5.7 ( Jul 27, 2022 ) =
* [Editor Tweaks] Removed these redundant features: Compact View For Element Buttons, Dropdowns On Hover, Expanded Oxygen Toolbar Menus, Expanded Select Box Options and Media Query Buttons.
* [Navigator] Adjusted the CSS to remove unwanted gaps at the top and for correct height of the structure panel.
* [Others] Removed Rank Math support since it is now built-in.

= 1.5.6 ( May 25, 2022 ) =
* [Conditions] Fixed these WooCommerce specific conditions: Has Empty Cart, Has Product in Cart, Product in Category, Product has Tag, Product on Sale, Product is Virtual, Product is Downloadable, Product has Image, Product in Stock, Has Cart Weight, Is at Endpoint, Has Purchased Product, Stock Status, Has Purchased Product and Has Cart Total.
* [Scripts] Updated scripts' assets.
* [Essentials] Cleaned up.
* [Classes Cleaner] Updated CSS for Oxygen 4.0.

= 1.5.5 ( Jan 17, 2022 ) =
* [Text Edit] Fixed missing default text of components, when text edit is on.
* [Fullscreen] Fixed Shrinking of viewport.
* [Conditions] Fixed a couple of notices.

= 1.5.4 ( Aug 17, 2021 ) =
* [Text Edit] Fixed a couple of notices.
* [Conditions] Added "User has written" condition which enables you to output elements depending on whether or not the current user viewing the page has written at least 1 item of the selected post type. Screenshot: https://d.pr/i/u5H57E.
* [Conditions] Fixed Country condition detection. If the condition is already being used, please remove and re-apply.

= 1.5.3 ( July 22, 2021 ) =
* [Editor Tweaks] → Easy Panels: Added sub option "Show Oxygen's Primary/Advanced tabs as well". https://d.pr/i/TEAzxQ, https://d.pr/i/dZli4X
* [Editor Tweaks] → Easy Panels: Added blue light indicator for panels with user-set properties. https://d.pr/i/nfE9qp
* [Editor Tweaks] → Easy Panels: Added tooltips for the panels. https://d.pr/i/dOyrNu
* [Essentials] Fixed close button (x) of panels like the Structure not working. https://www.facebook.com/groups/oxyplugins/posts/611019480311560/
* [Essentials] Removed 100% width for svg. https://www.facebook.com/groups/oxyplugins/posts/610264510387057/?comment_id=610813393665502

= 1.5.2 ( July 21, 2021 ) =
* [Editor Tweaks] → Easy Panels. A new option for quicker access to the property panels. https://d.pr/i/Sd6mxH
* [Conditions] → Mobile Detect: Fixed "Handheld" condition not working.
* [Essentials] Removed 100% height for svg. https://www.facebook.com/groups/284626916284153?multi_permalinks=610264510387057&hoisted_section_header_type=recently_seen

= 1.5.1 ( July 15, 2021 ) =
* [Essentials] Removed a CSS rule that resulted in elements not appearing in Safari.
* [Editor Tweaks] → Expanded Select Box Options: Increased the left and right padding around the four-sides measure box unit selector.

= 1.5.0 ( July 13, 2021 ) =
* [Text Edit] Disabled Oxygen's text element content field when Text Edit module in Toolbox is enabled.
* [Offline Mode] Fixed unwanted search box appearing in the builder.
* [Table of Contents] Fixed the issue with smooth scrolling of hash links.
* [Conditions] Added "Date and time" (https://wpdevdesign.com/condition-date-and-time/).
* [Conditions] Updated Country condition per https://wpdevdesign.com/how-to-get-visitor-country-in-wordpress/.
* [Editor Tweaks] → Expanded Select Box Options: Fixed the size of the controls at Advanced > Layout > Position. Screenshot: https://d.pr/i/5XD2M7.
* [Conditions] → WooCommerce: Removed the check for logged-in user for certain conditions so they now apply to guests as well. "Has Purchased Product" will continue to work like before i.e., only for logged-in users.
* [Scripts] Updated Flickity, GLightbox, Splide, Headroom, Infinite Scroll.
* [Navigator] Added a link to Swiss Knife's settings page under "Oxygen" admin bar menu item.
* [Essentials] Enabled HTML5 Support for script and style tags. Added some rules from https://gist.github.com/jackdomleo7/55659bafe581d19cc341ef775d6a9e6b.

= 1.4.9 ( March 24, 2021 ) =
* [Emmet] Added a new module for writing HTML and CSS faster in code areas using Emmet, https://emmet.io/.
* [Editor Tweaks] Added a sub option under "Copy Selector" to include # or . in the copied selector.
* [Conditions] Added "Is Blog" condition.
* [Navigator] Fixed links to edit Fluent Forms' forms.
* [Editor Tweaks] Fixed a few overlapping issues with the "Expanded Select Box Options" option.
* [Conditions] Fixed HTTP Referrer condition from returning false i.e., not outputting when there is no referrer.

= 1.4.8 ( February 17, 2021 ) =
* [Editor Tweaks] Added "Copy Selector" option. Adds a copy button next to element's ID/class using which the ID or class can be copied to clipboard with a single click.
* [Editor Tweaks] Added "Disable Oxygen Composite Elements" option. Details: https://www.facebook.com/groups/wpdevdesign/permalink/3090682121034715/.
* [Navigator] Added "Remove Customize From Admin Bar in Oxygen Editor" and "Remove My Account (User Profile) From Admin Bar in Oxygen Editor" options. These admin bar menu items are generally not needed and the latter interferes/could get annoying when the cursor is taken on/around the Save button.
* [Conditions] Added "Product in Sub Categories of" condition under Woo Product. This will enable you to output an element only if the current product is in the child categories (of any depth) of the chosen product category.
* [Conditions] Improved these WooCommerce conditions by making them more performant: Product in Category, Product has Tag, Selected Tag has at least One Product, Selected Category has at least One Product.

= 1.4.7 ( February 07, 2021 ) =
* [SEOPress] Fixed Oxygen's slider component getting broken (no styling). Details: https://www.facebook.com/groups/seopress/permalink/1367204456976316/
* [Conditions] Added Locale (when using Polylang) condition under Post category. Thanks to Alexander Buzmakov for https://oxywp.com/polylang-condition-in-oxygen/.
* [Conditions] Added "Post published during the last" (1 week/month/year) condition.
* [Essentials] Added a fix to prevent flickering of images in Oxygen's gallery that have hover image opacity.
* [General] Moved Export/Import to a separate tab.
* [General] Made the Save button on the plugin settings page sticky so it is always in view.
* [Conditions] Renamed "Language" to "Language (visitor)".

= 1.4.6 ( February 03, 2021 ) =
* [Stylesheets Editor] Fixed an issue with stylesheets getting corrupted.
* [Editor Tweaks] Added a new option called "Expanded Select Box Options" - Makes px, %, em etc. units, Font Weight and Tag dropdown options appear horizontally so the value can be set by a single click.

= 1.4.5 ( January 28, 2021 ) =
* [Conditions] Added these new WooCommerce product-specific conditions: Product Price, Stock Status and Backorders Allowed.
* [SEOPress] A new module that generates automatic meta description from Oxygen Builder content.
* [Scripts] Added GLightbox.
* [General] Removed Yoast Integration module from Toolbox since it is no longer needed with the recent updates in Oxygen. WordPress content gets included in Yoast's SEO analysis out of the box in Oxygen now.

= 1.4.4 ( January 12, 2021 ) =
* [Stylesheets Editor] Fixed backslashes being added when quotes are present in the CSS code.
* [Stylesheets Editor] Height now auto-expands as more code is added.
* [Editor Tweaks] Added "Highlight Active Options" option. Details: https://wpdevdesign.com/anti-ant-mode-for-luci/.
* [Navigator] Added "Advanced Scripts" top-level WP admin bar option.
* [Conditions] Changed "Current CPT Author" condition to "Current Post Author" so it can now be used with not only CPTs but also built-in post types, `page` and `post`.

= 1.4.3 ( December 29, 2020 ) =
* [Navigator] Added granular control with options for enabling/disabling Pages, Posts etc. separately.
* [Navigator] Added "Stylesheets Editor" item under "Oxygen" menu.

= 1.4.2 ( December 20, 2020 ) =
* [Stylesheets Editor] Added new module for managing user stylesheets outside Oxygen editor.

= 1.4.1 ( December 16, 2020 ) =
* [Navigator] Added "Plugins" option for quickly navigating to Installed Plugins, Plugins > Add New, Active, Inactive, Auto-updates Enabled, Auto-updates Disabled, Upload Plugin admin pages from anywhere with links to also open in new tabs for admins.
* [Navigator] Made additions under New option only for admin users.
* [Navigator] Templates and Pages (for directly editing with Oxygen) top-level WP admin bar menu items now appear only for admins.

= 1.4.0 ( December 15, 2020 ) =
* [Rank Math] Fixed Oxygen's shortcodes from appearing in Rank Math's XML sitemaps for images and image captions.
* [General] "Oxy Toolbox" admin menu item no longer appears for "Edit Only" users.
* [Conditions] Added new "Is Archive" and "Post Has Tags" conditions.
* [Editor Tweaks] Added a sub-option for the Components Panel to appear on click instead of on hover.
* [Editor Tweaks] Set "Expanded Oxygen Toolbar Menus" option to work only for non-Edit Only admins.
* [Fullscreen] Fixed the center frame in Oxygen editor getting zoomed in.
* [General] Added "oxy-toolbox-is-active" to Oxygen editor page's body classes list so other Oxygen plugins can target Oxy Toolbox' CSS.
* [Navigator] Made "Oxygen" WP admin bar menu item not appear for Edit Only users, Replaced Role Manager and Post Type Manager menu item links under "Oxygen" with Client Control.

= 1.3.9 ( October 22, 2020 ) =
* Added "Components Panel" option under Editor Tweaks module.
* Added a checkbox to disable Oxygen metabox on post types other than `page` in the plugin settings page.
* Added "Small to Large" suboption under Editor Tweaks > Media Query Buttons.
* Moved Autosave option of the Undo module into Editor Tweaks.
* Removed Undo module.
* Added Tab as a hotkey option in Fullscreen module.

= 1.3.8 ( September 10, 2020 ) =
* Added a new "WordPress" module.
** Disable Admin Email Check
** Disable Auto-update UI Elements
** Disable Auto-update Emails
** Disable Core Sitemaps
** Disable Sitemap Generation for Oxygen Templates
** Disable XML-RPC
** Disable Year Month Folders for Uploads
** Remove Help
** Remove Screen Options
* [Gutenberg] Added new options.
** Disable Editor Fullscreen by Default
** Disable NUX
** Disable Welcome Guide
* [Editor Tweaks] Reversed the order of Media Query Buttons so the breakpoints appear larger to smaller from left to right.
* [Fullscreen] Fixed KeyLoaded is not defined Uncaught ReferenceError.
* Added expand-collapse functionality in the plugin's settings page with the modules collapsed by default.

= 1.3.7 ( August 28, 2020 ) =
* Added Revisions module.
* [Conditions] Replaced `wp_get_referer` with `wp_get_raw_referer` for improved referer detection.

= 1.3.6 ( August 21, 2020 ) =
* [Conditions] Added HTTP Referer condition. Allows you to conditionally output elements depending on whether the provided string is present in or not present in or is exactly equal to the referring page URL. Documentation: https://oxyplugins.com/doc/conditions/#HTTPReferer.
* [Scripts] Updated Splide's files to 2.4.11.

= 1.3.5 ( August 18, 2020 ) =
* [Conditions] Product selection dropdown has been changed to a text input for entering the product ID in "Has Product in Cart" and "Has Purchased Product" conditions to avoid performance issues on sites with a large number of products.

= 1.3.4 ( August 12, 2020 ) =
* [Fullscreen] Fixed "ReferenceError: keyLoaded is not defined.."

= 1.3.3 ( August 12, 2020 ) =
* Added a new module: Fullscreen. Documentation: https://oxyplugins.com/doc/fullscreen/.
* Added a new module: Move Oxygen Admin Menu Up. Documentation: https://oxyplugins.com/doc/move-oxygen-admin-menu-up/.

= 1.3.2 ( August 05, 2020 ) =
* [Navigator] Added the missing "Edit Post", "Edit Page", "Edit <CPT>" etc. in the WP toolbar.
* [Navigator] Fixed the CSS loading on the front end even when the admin bar is not showing.
* [Conditions] Fixed these WooCommerce conditions so they only work with published products: Selected Tag has at least One Product, Selected Category has at least One Product.
* [Conditions] Moved these WooCommerce conditions in the conditions dialog from "Woo Product" category to "WooCommerce": Selected Tag has at least One Product, Selected Category has at least One Product, At least One Featured Product, At least One Product on Sale. These conditions can be used on any page. If you are currently using one of these conditions, you will need to delete it and re-add.
* [Conditions] Fixed warnings related to "Invalid argument supplied for foreach()...Cannot modify header information - headers already sent" in the WLM membership(s) code.

= 1.3.1 ( July 17, 2020 ) =
* [Gutenberg] Moved "Disable Gutenberg" as an option under a new "Gutenberg" module and added another option, "Full Width Editor".
* [Back To Top] Fixed Back To Top button not appearing when NextGen Gallery is active.

= 1.3 ( July 14, 2020 ) =
* [Conditions] Fixed Mobile Detect conditions.
* [Navigator] Edit Page, Edit Post and Edit Product admin bar links now appear only on relevant views.
* [Navigator] Fixed frontend links to view the Pages and Posts. All of them were linking to the homepage earlier.

= 1.2.9 ( July 10, 2020 ) =
* [Conditions] Added "Is Affiliate (AffiliateWP)" condition.
* [Navigator] Fixed misalignment of panels in the Oxygen editor due to a bug in v1.2.8.

= 1.2.8 ( July 09, 2020 ) =
* Fixed settings export not including the nested options.
* [Table of Contents] Wrapped the button element and div.toc-list in div.oxy-toc in the TOC HTML output.
* [Navigator] Fixed edit post link missing for user roles according to their permissions.
* [Navigator] Changed WP_Query to direct SQL query for the page/posts/templates listings in the WP admin bar for better performance.
* Added function exists checks for oxygen_vsb_register_condition to prevent fatal error when Oxygen is deactivated.
* Added function exists check for is_oxygen_edit_post_locked() to prevent fatal error with older versions of Oxygen.

= 1.2.7 ( June 13, 2020 ) =
* Added the ability to save/export/import plugin's settings.
* Fixed the URL for Isotope script.
* Added Headroom script.
* Updated Splide script.

= 1.2.6 ( June 13, 2020 ) =
* Added a new module: Image Width and Height Size Attributes.

= 1.2.5 ( June 12, 2020 ) =
* Fixed license activation/deactivation issue especially after site has been migrated to a new location.
* Fixed "At least One Product on Sale" condition.

= 1.2.4 ( June 09, 2020 ) =
* Added these WooCommerce conditions:
** Selected Tag has at least One Product
** Selected Category has at least One Product
** At least One Featured Product
** At least One Product on Sale

= 1.2.3 ( June 08, 2020 ) =
* Fixed fatal error on sites where WooCommerce is not active.
* Masked the license key with dots.

= 1.2.2 ( June 07, 2020 ) =
* Fixed Mobile Detect conditions.
* Added WooCommerce conditions. Details: https://oxyplugins.com/doc/conditions/#WooCommerce.
* Added links to OxyExtras' settings page (same and new tab) under Navigator's Oxygen admin bar menu item.

= 1.2.1 (June 04, 2020) =

* Improved Rank Math module to have Rank Math detect images from Oxygen in its XML sitemaps under the Images column.
* Fixed the width of toolbar menu when "Expanded Oxygen Toolbar Menus" editor tweak is enabled.

= 1.2 ( June 01, 2020 ) =
* Removed the visibility of License Key from the plugin's settings page after the plugin has been licensed. Changed the 2-step process of saving + activating/deactivating to a single step.
* Added "Wider Library Flyout Panel" editor tweak.
* Moved Oxygen's "Edit with Oxygen" or "Edit <Template>" under the Navigator's Oxygen admin bar menu.
* Fixed Collapse Editor button not appearing when editing a Stylesheet.
* Fixed custom TOC title not appearing.
* Fixed the CSS of "Expanded Oxygen Toolbar Menus" in preparation for Oxygen 3.4.
* Removed the "Remove default padding, default margin and list styles on ul, ol elements with a class attribute" CSS code in Essentials.

= 1.1.9 ( May 27, 2020 ) =
* Major overhaul of Navigator module
** added support for editing and viewing Pages and Posts.
** added links to open in new tab for Templates and Pages.
** added "Show ACF Field Groups" option.
** added "Show Fluent Forms' Forms" option.
** added "Show WooCommerce Products" option.
** added "New" Admin Bar Menu Additions option which adds links to new ACF Field Group, Code Snippet, Fluent Forms form, Reusable under "New" menu item.

Credit: inspired by Admin Page Spider plugin.

= 1.1.8 ( May 24, 2020 ) =
* Improved Rank Math module to include the parent Oxygen Template (if a Template has been selected in the RENDER PAGE USING TEMPLATE dropdown) in Rank Math's content analysis.
* Fixed media query buttons appearing vertically on hover when Dropdowns On Hover option is enabled.
* Added blue underline for the active media query button when Dropdowns On Hover option is enabled.
* Replaced system font with Source Sans Pro in Offline Mode module.
* Changed display of label from inline-block to inline in the Essentials module.

= 1.1.7 ( May 23, 2020 ) =
* Fixed remove media styles button not being easily clickable when "Media Query Buttons" editor tweak is enabled.

= 1.1.6 ( May 23, 2020 ) =
* Added "Offline Mode" module. Credit: Supa Mike and David Browne.
* Added "Media Query Buttons" editor tweak. Credit: Yan Fodé Kiara.
* Added "CSS Tweaks" editor tweak.
* Added "Remove min-width and min-height for empty Divs" editor tweak.
* Added Splide script.
* Changed display of label to inline-block from block in Essentials.

= 1.1.5 ( May 18, 2020 ) =
* Added Body Class condition.
* Added Browser Detect condition.
* Made the Text Edit module's textarea resizable with a min-height of 15vh.
* Added != operator for the "Author has CPT entry" condition.

= 1.1.4 ( May 16, 2020 ) =
* Fixed an issue with adding library elements in the Undo module.

= 1.1.3 ( May 15, 2020 ) =
* Added the following Conditions:
** Archive Type
** MemberPress Membership
** RCP Membership
** Referrer URL Parameter
** Language
* Fixed undefined index notice issue introduced from the recently added success message upon saving plugin's settings.

= 1.1.2 ( May 13, 2020 ) =
* Added Conditions module with several conditions. More to follow.

= 1.1.1 ( May 12, 2020 ) =
* Added an option to disable keyboard shortcuts in the Undo module.
* Added "Oxy Toolbox settings have been saved." success message after pressing the Save Changes button on the plugin's settings page.

= 1.1 ( May 11, 2020 ) =
* Disabled undo and redo in the class name fields and fixed error upon undoing/redoing after adding a class name.

= 1.0.9 ( May 11, 2020 ) =
* Added Disable Gutenberg module.

= 1.0.8 ( May 10, 2020 ) =
* Added Table of Contents module.
* Added a link to the Feature requests Trello board in the plugin's settings page.

= 1.0.7 ( May 08, 2020 ) =
* Added Reading Progress Bar module.
* Excluded styling of lists in Gutenberg from being reset. Thanks Yan Fodé Kiara.
* Fixed styling in Navigator module to make the ruler in Oxygen editor visible again.
* Added links to changelog, Facebook group and Support on the plugin's settings page.

= 1.0.6 ( May 07, 2020 ) =
* Fixed the size of Oxygen icons appearing larger than they should be in the WP Toolbar on admin pages when certain plugins like Gravity Forms/MailPoet and Navigator are enabled.
* Fixed Repeater components vanishing on clearing styles with Class Act module.
* Removed antialiased font smoothing for body tag from the Essentials module. Ref.: https://usabilitypost.com/2012/11/05/stop-fixing-font-smoothing/, https://responsivedesign.is/articles/font-smoothing/.
* Removed "text-rendering: optimizeSpeed" for body tag from the Essentials module. Ref.: https://bocoup.com/blog/text-rendering.

= 1.0.5 ( May 06, 2020 ) =
* Added link to the settings page in the plugins list admin page.
* Replaced base64 string for Oxygen icons with png in the WP Toolbar when Navigator is enabled.

= 1.0.4 ( May 05, 2020 ) =
* Added a new module: Open External Links In A New Tab
* Fixed undefined variable PHP notices.
* Got rid of CSS for removing all animations and transitions for people that prefer not to see them via prefers-reduced-motion. This is apparently causing issues with Animate on Scroll and other needed animations.

= 1.0.3 ( May 04, 2020 ) =
* Fixed Templates and Pages links under Back To WP Additions not linking to the correct URLs for sites on subdomains.
* Fixed items in the Previewing dropdown not being reachable on hover with "Dropdowns On Hover" enabled.

= 1.0.2 ( May 04, 2020 ) =
* Fixed unwanted space at the top coming from enabling WP Toolbar in the Oxygen editor via Navigator module.
* Fixed back to top button not being clickable reliably in the Oxygen editor.

= 1.0.1 ( May 03, 2020 ) =
* License check deactivation to make the plugin work temporarily.

= 1.0 ( May 03, 2020 ) =
* Initial Release.