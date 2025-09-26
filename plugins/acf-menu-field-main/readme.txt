=== Advanced Custom Fields: Menu Field ===
Contributors: Chris Miller
Tags: acf, menu, custom, field
Requires at least: 3.6.0
Tested up to: 4.9.0
Stable tag: trunk
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.en.html

A custom field for ACF that allows you to select any WordPress menu.

== Description ==

This plugin will add a new ACF field called "Menu" which can be found under "Relational". This field will allow you to pick from a list of all WordPress menu that have been added from the "Appearance > Menu" setting screen.

It will return a menu id which you can then use with function like `wp_nav_menu();` .

Here’s an example:

```
$menu_id = get_field('main_menu');

$args = array(
    'menu'          => $menu_id,
    'container'     => 'ul',
    'items_wrap'    => '%3$s'
);

wp_nav_menu($args);
```

## Why would you need something like this?
This plugin was created because a project we were working on required different menus on different pages. These menus needed to support submenus as well. Rather than recreate a menu editing experience, why not use what WordPress already provides us?

That said, you can build out all of the menus you need from the native menu builder in WordPress and use this plugin to select it on any page you need.

= Compatibility =

This ACF field type is compatible with:
* ACF 5
* ACF 4

== Installation ==

1. Copy the `acf-menu-field` folder into your `wp-content/plugins` folder
2. Activate the Menu plugin via the plugins admin page
3. Create a new field via ACF and select the Menu type
4. Read the description above for usage instructions

== Changelog ==

= 1.0.0 =
* Initial Release.