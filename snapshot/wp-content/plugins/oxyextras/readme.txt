=== OxyExtras ===
Contributors: David Browne, Gagan Goraya, Sridhar Katakam
Tags: oxygen builder
Requires at least: 4.7
Tested up to: 10.0.0
Requires PHP: 5.6
Stable tag: trunk
License: GPL3
License URI: http://www.gnu.org/licenses/gpl-3.0.txt

Lightweight component library for Oxygen Builder

== Description ==

Current Components:

1. Adjacent Posts
2. Alert Box
3. Author Box
4. Back to Top
5. Burger Trigger
6. Carousel Builder
7. Cart Counter
8. Circular Progress
9. Copy to Clipboard
10. Content Switcher
11. Content Timeline
12. Copyright Year
13. Countdown Timer
14. Counter
15. Dynamic Tabs
16. Extras Login Form
17. Fluent Form
18. Gutenberg Reusable Block
19. Header Search
20. Horizontal Slide Menu
21. Hotspots / Popovers
22. Infinite Scroller
23. Interactive Cursor
24. Lightbox
25. Lottie Animation
26. Mega Menu / Dropdown
27. Mini Cart
28. Off Canvas
29. Post Modified Date
30. Post Terms
31. Preloader
32. Pro Accordion
33. Pro Media Player
34. Read More / Less
35. Reading Progress Bar
36. Reading Time
37. Slide Menu
38. Social Share
39. Table of Contents
40. Toggle Switch

== Installation ==

1. Click on the download link in your purchase confirmation email if you have not already downloaded it after your purchase.

2. Download the plugin's zip file.

3. Go to Plugins > Add New in your WordPress admin. Click "Add New" button, then "Upload Plugin" button, then "Choose File", browse to and select the plugin's zip file.

4. Activate the plugin.

5. Enter the license key and activate your plugin license at Oxygen > OxyExtras > License.

Valid license key should be entered for the plugin to function and to receive automatic updates.

== Changelog ==

= 1.4.7 ( May 23, 2024 ) =
* [Copy to Clipboard] - New element for enabling users to copy text or dynamic data.
* [Social Share] - Added 'print' option for enabling users to print the page (or a specific area of the page).
* [Content Timeline] - Now supports changing the marker's vertical positioning (previously always centered).
* [OffCanvas] - Added focus trapping.
* [OffCanvas] - Fixed PHP warning if 'State on page load' setting is unset and has no value.
* [General] - Improved keyboard navigation across a number of elements.

= 1.4.6 ( Mar 27, 2024 ) =
* [Pro Accordion] - Added an option to open the first accordion item when being used inside a repeater.
* [Carousel Builder] - Sync functionality can now be used with carousels inside repeaters.
* [Gutenberg Reusable Block] - Fixed deprecation notice.

= 1.4.5 ( Mar 13, 2024 ) =
* [General] - Security improvement.

= 1.4.4 ( Mar 12, 2024 ) =
* [Table of Contents] - Added option to use heading text as heading IDs inside content.
* [Slide Menu] - Added option to auto close menu if hashlink menu item clicked.
* [Content Timeline] - Now supports being inside nested repeaters.
* [Social Share Buttons] - Changed defaults to use the X icon.
* [Carousel Builder] - Fixed issue with the carousel's preview mode showing an error in builder if the autoplay setting has no value.
* [Reading time] - Fixed issue where default words per minute value wasn't being used inside repeater when inside builder.
* [General] - Security patch.

= 1.4.3 ( May 31, 2022 ) =
* [General] - Small update to all icons to better match Oxygen v4 UI.
* [Carousel Builder] - Prevent Repeater divs wrapping onto multiple lines inside the builder.
* [Read More] - Added separate control for "transparent" gradient color.

= 1.4.2 ( May 07, 2022 ) =
* [Pro Media Player] Fixed SVG controls in the media player not showing up.

= 1.4.1 ( May 06, 2022 ) =
* [Horizontal Slide Menu] - New component for horizontal sliding menus (mmenu.js).
* [Interactive Cursor] - New component for adding a custom cursor that can interact with other elements.
* [General] - Added compatibility for Oxygen v4's JSON for all nestable components.
* [General] - Added compatibility for use with Oxygen v4's new Repeater changes.
* [General] - Multiple elements that use dynamic data now support HTML / WYSIWYG.
* [General] - Multiple elements now have specific aria-label controls for buttons.
* [General] - Component options now cleared from database after uninstalling.
* [OffCanvas] - Better accessibility incl. support for "inert", will add aria-controls to click trigger etc.
* [OffCanvas] - Will now automatically hide in the builder if not editing the template that contains it.
* [Lightbox] - Added option to allow users to swipe/flick through grouped slides.
* [Lightbox] - Added option to infinite loop grouped slides.
* [Lightbox] - Added option to include thumbnail gallery if using manual links with grouped images.
* [Lightbox] - "Returning focus" option now supports having multiple click triggers on page.
* [Content Timeline] - Added "meta content inner" for more layout possibilities.
* [Carousel Builder] - Now possible to use sub fields from inside Metabox group fields for image galleries.
* [Carousel Builder] - Added the ability to enable/disable SRCSET for images in galleries.
* [Mega Menu] - Added flyout menu specific style controls for easier styling of mobile menu.
* [Mega Menu] - Now supports anchor link (scroll to) behaviour in menu links that have no dropdown.
* [Countdown] - Added UTC offsets to timezone option.
* [Table of Contents] - HTML tag can now be changed (div, nav or custom).
* [Popovers] - Added support for use with dynamic content added by infinite scroller.
* [Pro Accordion] - Added "inherit" as default button color to prevent iOS Safari showing the blue color as default if no link color has yet been set.
* [Burger Trigger] - Hover opacity on burger lines now customizable.
* [Lottie] - Lottie version bump (addresses issue where some Lottie files would flicker in Safari).
* [Carousel Builder] - Fixed issue with arrow placement where "auto" wasn't accepted as a value.
* [Read More] - Fixed an issue with the content sometimes not being visible inside Repeaters (in the builder).
* [Slide Menu] - Fixed an issue causing menu ID not being fetched with dynamic data.
* [Content Timeline] - Fixed an issue where icon size control wouldn't be applied.
* [Pro Media Player] - Fixed an issue with full screen mode not taking up the whole viewport in Safari.
* [Lightbox] - Added a fix for AutomaticCSS users where a min-height on the body tag was being applied inside of lightbox iframe.

= 1.4.0 ( Dec 08, 2021 ) =
* [Content Timeline] - Added an option to change the scroll position for classes to be added.
* [Content Timeline] - Added more style controls for marker.
* [Content Timeline] - Performance improvements for the timeline scroll animation.
* [Mega menu] - Added an option to have the mega menu close whenever hash links are clicked inside a dropdown.
* [Carousel] - Fixed the issue with the carousel not initializing if the script is minified/combined.

= 1.3.9 ( Dec 06, 2021 ) =
* [Content Timeline] - New component for adding animated vertical timelines.
* [Carousel] - Added patch to fix Flickity's resizing issues with iOS 15.
* [Carousel] - Added option to choose cell selector for repeater carousels (allows support for the case when duplicate IDs are removed from repeaters).
* [Carousel] - Added "random order" option to all galleries.
* [Carousel] - Added setting to disable Flickity's "imagesLoaded" option.
* [Carousel] - Arrows/dots now both disabled automatically if there is only one slide / group of cells.
* [Pro Media Player] - Added new control for supporting dynamic data to populate the poster images.
* [Popovers] - Added support for use within WP Grid Builder AJAX added content.
* [Lightbox] - Added legacy support for IE11 browser.
* [Fluent Form] - More styling control for radio buttons that use images.
* [Mega Menu] - Added option to disable the "click for dropdown to remain open" (hides the hashlinks also).
* [Mega Menu] - Prevents mobile styles from being applied to desktop view if user rotates device with mobile menu still open.
* [Read More] - Fixed issue with Galaxy A21s devices miscalculating the content height and not always expanding.
* [Table of Contents] Prefix for auto-IDs added to content headings, 'toc-' can now be customized.
* [Table of Contents] - Fixed heading icon links not having the the correct URL when used on nested sub pages.
* [Mini Cart] - Added option to disable buttons and more specific style options.
* [Cart counter] - Added option to open/close on hover as well as click.
* [Reading Progress bars] - Added better support for when using multiple progress bars.
* [Countdown] - Fixed couple of bugs with plural/single and countdown remaining invisible.
* [Social Share] - Fixed link issue with Facebook icon linking other elements.
* [Off Canvas] - Tweaked the staggered animation to prevent rare issue where user could open offcanvas before animations had time to reset.

= 1.3.8 ( Aug 17, 2021 ) =
* [Countdown Timer] - Now accepts both YYYY/MM/DD and YYYY-MM-DD formats.
* [Hotspots] - Prevent the SSL error being displayed in cases where the SSL certificate could not be verified on the site.
* [Carousel Builder] - Hashlink option now will override the initial cell setting (to allow for hashlinking between pages).
* [Dynamic Tabs] - Fixed layout issue with Meta Box clone fields.
* [Read More] - Better support for use inside Oxygen's tabs component (no flicker).

= 1.3.7 ( July 26, 2021 ) =
* [Countdown Timer] - New component for countdown timer, 'evergreen, recurring, fixed dates'.
* [Header Search] - Added option to prevent scrolling when search is open.
* [Header Search] - Added option to require input before submitting.
* [Lightbox] - Added option to prepend the lightbox to body element instead of inside the component (useful if needing the lightbox component inside carousels & other transformed containers).
* [Lottie] - Can now use any dynamic data as Lottie JSON URL.
* [Back to Top] - Added option to make it keyboard focusable.
* [Mega Menu] - Added current menu ancestor styles to dropdown links.
* [Mega Menu] - Edit - removed auto-scroll back up to menu if dropdown is open.
* [Infinite scroller] - Custom mode now allows for defining a custom container for the content.

= 1.3.6 ( May 30, 2021 ) =
* [Fluent Form] - Fixed the issue preventing default CSS to be visible inside the builder.
* [General] - Fixed the issue affecting WP Grid Builder layout where no facets were found on the page.

= 1.3.5 ( May 28, 2021 ) =
* [Pro Accordion] - Added support for Metabox Term Meta &amp; formatting WYSIWYG fields.
* [Dynamic Tabs] - Added support for Metabox Term Meta &amp; formatting WYSIWYG fields.
* [Social Share] - Updated line network brand colors.
* [Social Share] - Added an option for popup behavior.
* [Preloader] - Added an option to close on user clicking.
* [Lightbox] - Now supports using the Read More component inside lightboxes.
* [Pro Media Player] - If inside carousel, now automatically ensures carousel always has the correct size for aspect ratli>
* [Popovers] - More styling controls.
* [MegaMenu] - Added current menu link typography for flyout menus.
* [Copyright Year] - Replaced '&amp;nbsp;' with space to avoid the large visible gap.
* [Carousel] - Fixed the issue with not continuing autoplay after scrolling on mobile.
* [Pro Accordion] - Fixed the issue with global heading color overriding accordion header color.
* [General] - Multiple in-builder performance improvements.
* [General] - Out of the box support for WPGridBuilder loaded content (carousel, tabs, accordion, read more, lightbox).

= 1.3.4 ( April 29, 2021 ) =
* [Table of Contents] - New component for adding an automatic table of contents.
* [Pro Accordion] - Added an option to close all other accordion items within a container (not just sibling).
* [Pro Accordion] - Added support for Meta Box user fields and settings pages.
* [Dynamic Tabs] - Added support for Meta Box user fields and settings pages.
* [Carousel] - Added support for Meta Box user fields and settings pages.
* [Social Share] - Added a width setting to allow for equal width buttons.
* [Social Share] - Added 'Line' as a new network.
* [Lightbox] - Small close button now fully stylable.
* [Login Form] - Finer controls over element styles.
* [Pro Media Player] - Added option to load VimeJS direct from CDN.
* [Pro Media Player] - Fixed an issue causing some external mp4s not being able to be fetched.
* [Pro Accordion] - Fixed counter font-family not being applied.
* [Cart Counter] - Fixed the issue that was causing some styles not to be applied in Gutenberg.
* [Carousel] - Fixed the issue with an old version of Safari v9 (2015) not being draggable.
* [Carousel] - Fixed the issue causing navigation arrows to behave strangely when inside Repeaters.
* [Read More] - Fixed the issue with Read More sometimes not opening immediately if there is a lot of content.

= 1.3.3 ( April 07, 2021 ) =
* [Dynamic Tabs] - Added Meta Box support (cloneable group fields).
* [Dynamic Tabs] - Now can use ACF option pages and ACF field from other pages.
* [Pro Accordion] - Added Meta Box support (cloneable group fields).
* [Carousel] - Added Meta Box support for galleries (image fields).
* [Carousel] - Added WooCommerce product images gallery.
* [Carousel] - Added object-fit control for changing how the images fit into the cells.
* [Carousel] - Carousel now completely hidden on frontend if gallery is empty or no gallery found.
* [Lightbox]- Now supports carousel builders inside "inline" lightboxes. 
* [Fluent Form] - Added style controls for 'step' progress type.
* [Popovers] - Added option to be open by default, added transitions when moving placement.
* [Popovers] - More sensible defaults (fallback as "auto").
* [Infinite Scroller] - Added "page content" option for breaking up posts/pages via page breaks (i.e., layered linking).
* [Infinite Scroller] - Added support for using custom classes in Easy Posts (not just oxy-post).
* [Cart Counter] - Added option to hide counter if there are no cart items.
* [Slide Menu] - Menu can now be selected via dynamic data.
* [OffCanvas] - Allowed for including slide menu top-level items in stagger animation.
* [OffCanvas] - Fixed issue where preventing site scrolling wasn't working on iOS.
* [Author Box] - Allowed no content for the name prefix.
* [Social Share] - Fixed issue with icon background color overlapping the button when border radius added.
* [General] - Fixed a couple of typos, added icons and added more helper descriptions.

= 1.3.2 ( March 11, 2021 ) =
* [Hotspots] - New component - Adds container for adding hotspots on images.
* [Popovers] - New component - To create individual hotspots, popovers, or tooltips for other elements.
* [Mega Menu] - Added support for use in custom headers (just needs header tag).
* [Mega Menu] - Added hover/active/focus border styling to dropdown links.
* [Social Share] - Added Xing as a new network.
* [Fluent Forms] - Added more style settings and slight UI change for fewer clicks.
* [Carousel Builder] - Added alignment for images inside the cells for galleries.
* [Carousel Builder] - Added full support for using carousels inside of Repeaters.
* [Carousel Builder] - ACF gallery data can now be from other pages or ACF options page.
* [Carousel Builder] - Draggable can now be disabled for Fade carousels.
* [Carousel Builder] - Added an option for the gallery images to preserve aspect ratio when in full screen mode.
* [Carousel Builder] - Fixed an issue with WooCommerce cell widths sometimes not being visible inside the builder.
* [Pro Accordion] - Now can be used inside Repeaters to create larger accordions using any dynamic data.
* [Pro Media Player] - VimeJS now comes packaged inside the plugin (no external CDN).
* [Lightbox] - Added support for being used in AJAX content.
* [Read More] - Added support for being used in AJAX content.
* [Dynamic Tabs] - Fixed an issue with some style changes not being immediately visible inside the builder.
* [Off Canvas] - No longer prevents Lottie animations from animating if being used directly as the click trigger.
* [Off Canvas] - Fixed an issue with smooth scroll (Oxygen v3.7+) not smooth for hashlinks inside Off Canvas.

= 1.3.1 ( Febrary 11, 2021 ) =
* [Dynamic Tabs] - Hash suffix now optional/customizable for creating custom hashlinks to individual tab content (page/#componentID-1-suffix, page/#componentID-2-suffix).
* [Dynamic Tabs] - Added support for using in reusable components.
* [Infinite Scroller] - Added "custom" option, so any containers can be used to pull in content from a next page by following any links.
* [Infinite Scroller] - Added option to retrigger scroll animations when new content loaded.
* [Carousel Builder] - Fixed issue "use as navigation" not syncing correctly.
* [Lottie] - Fixed issue with scroll offsets not calculating correctly when using more than two scroll animations.

= 1.3.0 ( Febrary 09, 2021 ) =
* [Dynamic Tabs] - New component for adding tabs populated by repeater fields. (accordion option for mobile).
* [Carousel Builder] - Added option to auto-calc cell width based on number of visible cells needed.
* [Carousel Builder] - Added option to resume autoplay X milliseconds after ending user interaction.
* [Carousel Builder] - Galleries now support image lazy loading.
* [Carousel Builder] - Added support for syncing carousels (2-way syncing).
* [Carousel Builder] - Added option to fade in carousel only after initializing is finished, to help prevent FOUC.
* [OffCanvas] - Added burger syncing for animating two separate burger triggers.
* [Infinite Scroller] - Now supports being used with Masonry / Isotope layouts (including when using filters).
* [Header Search] - Added support for use with Polylang.
* [Header Search] - Added option to add text to the search button.
* [Lightbox] - Manual mode now supports elements added to page dynamically (i.e., posts in infinite scroller, WPGridbuilder etc).
* [Mega Menu] - Added horizontal alignments & margins for more control over positioning of dropdown links.
* [Lightbox] - More animation options (can now be previewed inside the builder).
* [Lightbox] - Images no longer stretch inside the lightbox in the builder.
* [Lightbox] - Fixed the issue with inline lightboxes not scrollable on some iPads.
* [Carousel Builder] - Fixed the issue with background lazy loading needing image lazy loading also being enabled.
* [OffCanvas] - Fixed the conflict when using "prevent scroll" with push type offcanvas.

= 1.2.9 ( January 25, 2021 ) =
* [Lightbox] - New component for visually building lightboxes, support for AJAX, iFrame, Inline, Video, Image Galleries or external links.
* [Carousel Builder] - Added support for background-image lazy loading.
* [Carousel Builder] - Added link option for lightbox support on galleries.
* [Carousel Builder] - Added caption support for galleries.
* [Carousel Builder] - Added option to retrigger AOS animations inside carousel.
* [Carousel Builder] - Prev/Next will now pause autoPlay briefly before resuming to prevent flickering.
* [Circular Progress] - Now can build pie chart style progress bars (using scale & new 'butt' ending controls).
* [Mega Menu] - Added mobile menu settings to allow for use with mobile sticky headers.
* [Mega Menu] - Added dynamic data option to dropdown links
* [Fluent Form] - Added "checkbox display" for preventing GDPR & T&Cs checkboxes being misaligned with other checkboxes.
* [Read More] - Added support for being used inside closed tabs.
* [General] - Minor UI cleanup, for more consistency across components.

= 1.2.8 ( December 22, 2020 ) =
* [Off Canvas] - Added push type Offcanvas.
* [Mega Menu] - Added current page link color and typography controls.
* [Carousel Builder] - Disabling pause Autoplay on hover now supported for fade carousels.
* [Off Canvas] - Box-shadow now hidden when Offcanvas closed.
* [Mega Dropdown] - Issue fixed with links not clickable if no dropdown and when reveal on mouseover disabled.
* [Read More / Less] - Fixed issue with read more link not clickable in the latest Firefox.
* [Pro Accordion]  - Fixed error if attempting to use Dynamic items without ACF Pro already being active.

= 1.2.7 ( December 16, 2020 ) =
* [General] OxyExtras now respects Oxygen's Client Control. Admin menu item won't show for Edit Only roles and users. Also, only the elements specified under "Enable Elements" checkbox at Oxyen > Client Control will appear in the editor for Edit Only users.
* [Lottie] JS now gets enqueued on init to prevent an error inside the builder when multiple lotties are used.

= 1.2.6 ( December 10, 2020 ) =
* [Carousel Builder] - Lazy loading now available when using custom elements as cells.
* [Mega Menu] - Dropdown alignments & positioning adjustable across screen sizes. 
* [Mega Dropdown] - Link text can be removed if just wanting an icon.
* [Mega Dropdown] - Link URL can be disabled for mobile menu.
* [Mega Dropdown] - Dropdown can be set to be expanded by default when mobile menu opened.
* [Counter / Circular Progress] - Can now be used inside repeaters.
* [Burger Trigger] - Added control over touch events for touch screen devices.
* [Slide Menu] - Sub menu toggles now functional inside the builder. 
* [General] - Fixed issue with components as reusable templates where styles weren't added inside the builder.
* [General] - In-builder performance, no JS from active components loaded inside the builder unless added on that specific page.
* [Pro Media Player] - Fixed issue with audio playback not starting correctly in Safari.
* [Mega Menu] - Fixed issue with long menu text preventing the click to toggle the containers on mobile.


* [Mega Menu] -  New component for adding mega menu style dropdowns in header builder.
* [General] - Slight JS adjustments to ensure full support for jQuery 3.5.1.
* [Fluent Form] - Added separate style controls for GDPR.
* [Social Share] - Customisable share URL's and more control over the text in email title and body.
* [Slide Menu] - New "mega menu list" option for displaying columned menu lists inside Mega Menus.
* [Pro Media Player] - Removed aspect ratio setting for Vimeo (now automatic).
* [Carousel Builder] - Added option for auto cell grouping and percentage cell grouping.
* [Carousel Builder] - Carousel now horizontally scrollable inside builder while in edit mode.
* [Off Canvas] - Built-in support for linking offcanvas' to ensure both offcanvas' close together.
* [Off Canvas] - Auto-close when hashlink clicked is now optional.
* [Pro Media Player] - Fixed PHP warning "Undefined variable: vime_player_selector".

= 1.2.4 ( November 11, 2020 ) =
* [Carousel Builder] - Hotfix for making parallax work again.

= 1.2.3 ( November 11, 2020 ) =
* [Carousel Builder] - No longer need to specify cell selector when using Repeaters.
* [Carousel Builder] - Added option to disable pause Auto Play On Hover.
* [Carousel Builder] - Performance improvements inside the builder. Preview mode works faster.
* [Carousel Builder] - Added "Prioritize property" option for Galleries for choosing either dynamic height or widths.
* [Pro Accordion] - Added support for ACF option pages and using fields from specific page IDs.
* [Pro Media Player] - Added loop option for videos.
* [Pro Media Player] - Fixed dynamic data button not working correctly for Audio URL field.
* [Carousel Builder] - Fixed incorrect description text, "image URL" replaced with "image ID" for ACF galleries
* [Slide Menu] - Fixed issue with schema markup sometimes preventing the menu text being clickable.
* [Read More] - Fixed gradient sometimes remaining visible when fade gradient disabled.

= 1.2.2 ( November 04, 2020 ) =
* [Carousel] - Added fade transition carousel type.
* [Carousel] - Added support for WP media library galleries.
* [Carousel] - Added option to remove pause-on-hover when using ticker mode.
* [Slide Menu] - Added current menu item styles and the ability to have the current menu item visible on page load.
* [Header Search] - Added expand form, slide reveal and accessibility controls options.
* [Read More] - Allow expanding inside builder for easier access to elements inside.
* [Read More] - Now dynamic. If content isn't taller than the expanded height, read more button (and gradient) will automatically not be visible.
* [Read More] - Can now be used inside Repeaters without issue.
* [Read More] - Added icons for the read more link, a fade transition and more control over the gradient overlay.
* [Infinite Scroller] - Now dynamic. If not enough posts found for there to be a second page, infinite scroll won't run (and read more button automatically hidden),
* [Alert Box] - Added click trigger and show/hide alert functions for programmatically triggering alerts.
* [Alert Box] - Added "header notice" alert type.
* [Adjacent Post] - Added alt tags to post images.
* [Accordion Pro] - Added option to turn off automatic sibling accordion item closing.
* [Pro Accordion] Fixed issue with accordion buttons being triggered too quickly when scrolling on iOS.
* [Slide Menu] - Fixed issue with hash links not triggering the sub menu.
* [Carousel] - Fixed issue with force equal heights being overridden by other CSS.
* [Carousel] - Fixed issue with high z-index on dots causing them to be visible over the top of modals.

= 1.2.1 ( October 26, 2020 ) =
* [General optimization] - Less default CSS output for multiple components where possible.
* [General optimization] - Improved reliability/usability of some components inside the builder.
* [General optimization] - Added browser-performance setting to carousel / Off Canvas for smoother transforms.
* [Carousel Builder]  - Added support for lazy loading images in cells when using Repeaters or Easy Posts.
* [Pro Accordion] - Dynamic mode will now show demo content inside builder if no ACF data found for each field (only in the builder for easier styling).
* [Pro Accordion] - Can now preview toggle animation and active style changes inside the builder by clicking the accordion header.
* [Burger Trigger] - Burger animation will now always be previewable inside the builder.

= 1.2.0 ( October 21, 2020 ) =
* [Pro Accordion] - New component for adding dynamic accordions.
* [Circular Progress] - Drop Shadows & Inner circle control added for more fancy styling.
* [Pro Media Player] - Added custom poster image option when using YouTube & Vimeo.
* [Pro Media Player] - Added new UI layout with custom play / pause icons.
* [Pro Media Player] - Added autoplay, autopause & more Vimeo options.
* [Carousel Builder] - ACF gallery image now automatically the same height.
* [Carousel Builder] - Add option to force equal height for cells.
* [Carousel Builder] - Fixed issue when heights overriding fullscreen mode.
* [Preloader] - Fixed pre-loader being visible if used on pages editable in Gutenberg.
* [Author Box] - Fixed issue with website link not displaying.
* [Offcanvas] - Fixed issue with inner-animations sometimes not resetting.

= 1.1.9 ( October 07, 2020 ) =
* [Circular Progress] - New component for adding animated circular progress bars.
* [Pro Media Player] - New component for adding lazy loading videos & audio with customizable UI.
* [Carousel Builder] - Added support for ACF gallery.
* [Carousel Builder] - Added 'fullscreen' option.
* [Carousel Builder] - Added styles for disabled navigation (when no more cells to navigate to).
* [Counter] - Counter will now show end number instead of start number inside the Oxygen builder.
* [Post Terms] - CPT taxonomies now included in taxonomy dropdown.
* [Fluent Form] - Fixed issue with characters no being visible inside form dropdown.
* [Slide Menu] - Fixed issue with arrow triggering menu link when viewing on Chrome mobile view.
* [Adjacent Posts] - Fixed issue with 'stack posts' setting causing some CSS from components to not be included.
* [Read More / Less] - Added gradient fade option.

= 1.1.8 ( September 15, 2020 ) =
* [Infinite Scroller] - New component for applying infinite scrolling to Easy Posts/ or Repeaters or Products Lists.
* [Carousel Builder] - Added carousel "ticker" option for allowing continuous movement.
* [OffCanvas] - Fixed an issue with hash links not scrolling after closing offcanvas.
* [OffCanvas] - Fixed staggered animations not resetting when revealing offcanvas from the top.
* [General] - Support for dynamic data added to multiple components.
* [General] - Selector fields now support attribute selectors, eg .class[attr=value]
* [General] - Components list on the plugin settings page now in alphabetical order.

= 1.1.7 ( September 02, 2020 ) =
* [Carousel Builder] - Added support for Easy Posts, Woo Components or using custom elements as cells.
* [Carousel Builder] - Added the ability to turn off carousel functionality at any breakpoint.
* [Carousel Builder] - Added Scale & transition controls for page dots.
* [Carousel Builder] - Added support for parallax elements (using the Repeater).
* [Lottie] - JSON now lazy loaded (can be disabled).
* [Off Canvas] - Added 'staggered animation' option for inner elements using Oxygen's scroll animation.
* [Preloader] - Fixed a rare issue where some elements would appear before preloader.
* [Burger Trigger] - Fixed an issue with some unpredictable behavior when used with slide menu.
* [Fluent Form] - Fixed hover opacity for submit button.
* [General] - Fixed an issue with the text fields not allowing quotes.

= 1.1.6 ( August 17, 2020 ) =
* [Carousel Builder] - New component for visually building carousels (for use with repeater component).
* [Offcanvas] - Any elements inside the offcanvas can now make use of Oxygen's scroll animations, triggered when the offcanvas is opened (rather than on page load).
* [Slide Menu] - Added new menu alignment controls and focus controls for the sub menu buttons.
* [Lottie] - Added option to render animation as <canvas> instead of <svg> (to prevent rare cases of some animations flickering in Safari).

= 1.1.5 ( August 06, 2020 ) =
* [Fluent Form] - Fixed padding issue on phone/mobile field when flag is disabled.
* [Cart Counter] - Fixed issue with cart number not updating (present only in v1.1.4).

= 1.1.4 ( August 05, 2020 ) =
* [Toggle Switch] New component for switching content or toggling classes.
* [Content Switcher] New component allowing to switch between two elements (for use with the toggle switch).
* [Burger Trigger] - Added 'scale' changing size of burger independent of size of the button.
* [Cart counter] - Accessibility improvement -  Dropdown cart now can be accessed by keyboard.
* [Counter] - Number fields now accept dynamic data.
* [Off Canvas] - Accessibility improvement - Now able to change the focus when offcanvas opened to any selector inside.
* [Off Canvas] - Trigger can now be from inside dynamically added content.
* [Slide Menu] - Added site navigation schema markup option.
* [Fluent Form] - Colour controls added for Invalid input state.
* [Fluent Form] - Label Typography font weight issue fixed.
* [Fluent Form] - Smart UI checkbox issue fixed on iPhone.
* [Fluent Form] - Fixed issue with form dropdown not appearing in Oxygen if a form name contained disallowed words.
* [Fluent Form] - Added Form ID control which accepts dynamic data.
* [Social Share] - Added Support for Pinterest, WhatsApp & Telegram.
* [Social Share] - Fixed issue with email share link when post titles contained certain characters.
* [Header Search] - Fixed a W3C Validator issue.
* [Lottie] - ACF field can now be used to get the JSON URL.
* [Lottie] - Cursor position control can now be relative to any container element.
* [Lottie] - Added MouseOver control (similar to hover but with frame control & reverse animation when use stops hovering.
* [General] Small performance improvements for users on Oxygen v3.4+ (less inline JS output where possible).
* [General] In-builder performance improvements (loading less JS).
* [General] Added support ready for Oxygen v3.5 (new preset code).

= 1.1.3 ( June 25, 2020 ) =
* Added checkboxes in the plugin's settings page so that only selected components can be added to the Oxygen editor.

= 1.1.2 ( June 24, 2020 ) =
* [Alert Box] - Can now be used as a header notification bar, with 'SlideUp' close option added.
* [Fluent Form] - Compatibility with v3.6.0.
* [Header Search] - More control over icon positioning.
* [Mini Cart] - Fixed scrollbar issue, more controls for positioning of inner elements.
* [Off Canvas] - Added z-index controls for both backdrop & inner content.

= 1.1.1 ( June 18, 2020 ) =
* [Off Canvas] - Fixed an issue with chosen selector not triggering Off Canvas.
* [Preloader] - Fixed slight glitchy animation on iPhones.

= 1.1 ( June 18, 2020 ) =
* [Cart Counter] - New component for displaying Woocommerce Cart Count.
* [Mini Cart] - New component for displaying Woocommerce Mini Cart.
* [Preloader] - New component for building preloaders to hide FOUC or FOUT.
* [Fluent Form] - Added style controls for Payment Summary and Checkable Grids.
* [Fluent Form] - Added "General Styles" for overall form typography, button transitions.
* [Off Canvas] - Slide from Top/Button now available.
* [Off Canvas] - Can now be triggered from inside a modal if click trigger is also a trigger for closing the modal.
* [Slide Menu] - Prevent issue with browsers auto-scrolling with hashlinks & sub menu items collapsing height.

= 1.0.9 ( June 13, 2020 ) =
* [Fluent Form] Added support for Smart UI styling, added more style options and renamed controls to match official Fluent Forms.
* [Fluent Form] Fixed code issue if FF not active.
* [Reading Time] Added "Text After (Singular)" and "Text After (Plurarl)" settings for customizable After text.
* [Off Canvas] Menu items with hash links inside Off Canvas can now open submenu by clicking entire menu item.
* Fixed license activation/deactivation issue especially after site has been migrated to a new location.
* A few other general code polishes.

= 1.0.8 ( June 10, 2020 ) =
* [Adjacent Posts] Fixed an issue with the next post showing even if empty.

= 1.0.7 ( June 09, 2020 ) =
* [Fluent Form] Added a check to ensure that there will be no errors if Fluent Forms is not active.

= 1.0.6 ( June 09, 2020 ) =
* [Fluent Form] Added dropdown for selecting form by name, instead of ID
* [Lottie Animation] Added Click Animation Trigger with optional reverse second clicks.
* [Lottie Animation] Easier controls with sliders for frames and speed & width / height.
* [Back to Top] Added ability to include any element inside button to build manually.
* [Back to Top] Added option to be visible only when scrolling up.
* [Off Canvas] Fixed issue with iPhone 5/6 with backdrop.
* [Off Canvas] Added auto close if any hash links clicked from inside off canvas.
* [Adjacent Posts] Prev/next posts can now be split across two components for more flexible positioning.
* [Alert Box] Now can add divs inside without issue.

= 1.0.5 ( June 05, 2020 ) =
* [Fluent Form] Added more focus styles to forms (& more style options for multi step forms).
* [Lottie Animation] Added Top/Bottom offset controls to scroll animations.
* [Lottie Animation] Added cursor position based animation.
* [Lottie Animation] Added the ability for sync scrolling to any container.
* [Lottie Animation] Added the ability to toggle loop on/off.
* [Lottie Animation] Fixed an issue with scrolling not working with multiple animations.

= 1.0.4 ( June 04, 2020 ) =
* Fixed - Button hover issue in Back to Top.
* Fixed - Off Canvas builder visibility causing elements to be unclickable.
* New - Option to add custom aria label to Burger Trigger button.
* Fixed - Prevent duplicate IDs on search icons.
* Edit - Removed the ability to change Slide Menu type in media queries to prevent issues.

= 1.0.3 ( June 03, 2020 ) =
* Fixed duplicate ID on icons in the Adjacent Posts component.
* Fixed a bug in the Slide Menu component causing it not to function properly when hidden by default.
* Moved "Open / Close Trigger selector" setting in Off Canvas component to the Primary tab for easier access.
* Changed container elements' tags from "span" to "div" in Back to Top component's output.
* Added a screenshot under the License Key form showing where the extras added by the plugin can be found.

= 1.0.2 ( June 02, 2020 ) =
* Fixed Burger Trigger and Off Canvas components not appearing.

= 1.0.1 ( June 02, 2020 ) =
* Added a link to Settings under the plugin name.

= 1.0.0 ( June 02, 2020 ) =
* Initial release
