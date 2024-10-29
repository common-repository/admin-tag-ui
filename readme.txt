=== Admin Tag UI ===
Contributors: divspark
Tags: admin, backend, dashboard, tag, tags, tag list, tag cloud, interface, ui, edit post, add post
Requires at least: 5.8
Tested up to: 5.8
Stable tag: 1.1.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Improves the tag sections located in the admin backend (WordPress dashboard) classic editor post screens.


== Description ==

Admin Tag UI improves the tag sections found in the admin backend's classic editor add and edit post screens. There are several changes to the interface.

= Add, Edit Post Screens =
* Shows all tags instead of just the most used - Helpful to see all of the tags instead of having to guess for less commonly used ones.
* Displays the tags in a list on their own lines rather than trying to show them all on the same line.
* Displays the tags in 2 columns (or can be set to 1).
* Increased font size of tags making it easier to read and select.
* Highlights selected tags
* Clicking on tags will no longer jump the screen up to the "Add" tag field.
* Automatically reveals the tags instead of having to click to have them revealed.
* The appearance changes also apply to the tags under the currently selected tags section.
* For selected tags, hovering over the remove (X) icon highlights the entire tag in red. The purpose is to more easily identify which tags are being hovered over.

= Settings =
There is a settings page allowing several of the changes above to be turned on or off, or altered. This allows a fine tuning of the user interface to fit your needs. All of the settings above are enabled by default.


== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. The plugin settings can be accessed through the main plugins page. Use the 'Plugins->Admin Tag UI->Settings' link to configure the plugin.


== Frequently Asked Questions ==

= Is there support for Gutenberg editor? =

Currently, the tags will only change for the classic editor. The tags under Gutenberg block editor will not change.

= Does this apply to the post screens for custom post types? =

Yes, custom post types are supported.

= How do I access the settings page? =

There is no dedicated menu link for the settings page to minimize the impact of the plugin on the dashboard. Instead, the settings page can be accessed through the plugins page: `Plugins->Admin Tag UI->Settings`. 

= How do I enable highlighting selected tags? =

Please upgrade to version 1.1.1+ of the plugin. Also, a newer version of WordPress is required.


== Screenshots ==

1. Tag section under add/edit post pages changed to 2 columns list style
2. Selected tags style is also changed and hovering over the remove (X) now colors the entire tag red for better clarity
3. Also applies to tags found under custom post types add/edit pages
4. Settings page
5. Example of changing settings to show 1 column and allow tag size to change


== Changelog ==

= 1.1.4 =
* Fixed bug that appeared in the post edit screen

= 1.1.3 =
* Added compatibility for multiple tag boxes appear in the post edit screen (such as when there is a custom taxonomy)
* Fixed non-breaking tags that were too long

= 1.1.2 =
* Added compatibility for WordPress 4.9
* Fixed tags not being highlighted when adding tags with Add button

= 1.1.1 =
* Fixed bug related to highlighting selected tags.
* Updated to make highlighting selected tags compatible with WordPress 4.8.

= 1.1.0 =
* Prevents the screen from jumping up when adding a tag.
* Highlights selected tags.
* Small code changes.

= 1.0.0 =
* Release
