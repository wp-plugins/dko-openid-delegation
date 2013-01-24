=== Plugin Name ===
Contributors: davidosomething
Donate link: http://davidosomething.com/
Tags: openid, login, delegate, delegation, myopenid
Requires at least: 3.0.1
Tested up to: 3.5
Stable tag: trunk
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds link and meta tags for OpenID server delegation to your WP_HEAD.

== Description ==

Please report issues, fork, etc. from the GitHub repository:
https://github.com/davidosomething/dko-openid-delegation

This plugin adds the relevant link and meta tags for OpenID server delegation
to your WP_HEAD. <a href="https://www.myopenid.com/help#own_domain">See this
MyOpenID page for more information</a>.

If rewrite rules from a caching plugin, or custom rewrite rules, cause the page
to do a 301 redirect, login will fail, at least for some sites. Remove these
rewrites to fix it.

= Other plugins that do OpenID delegation and their differences =

* [OpenID Delegation](http://wordpress.org/extend/plugins/wordpress-openid-delegation/) by [rodrigosprimo](http://profiles.wordpress.org/rodrigosprimo/) - doesn't have the meta X-XRDS-Location tag, but does a cool thing and check if the provider you entered is actually a working OpenID provider.
* [MyOpenID Delegation](http://wordpress.org/extend/plugins/myopenid-delegation/) by [benatkins](http://profiles.wordpress.org/benatkin/) - limited to MyOpenID as a provider.
* [Open ID Delegate](http://wordpress.org/extend/plugins/openid-delegate/) by [alexisabarca](http://profiles.wordpress.org/alexisabarca/) - OpenID v1 only

== Installation ==

1. Install and activate this plugin through the 'Plugins' menu in WordPress

OR

1. Upload the `dkoopenid` folder to your plugins directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

Please report issues, fork, etc. from the GitHub repository:
https://github.com/davidosomething/dko-openid-delegation

== Screenshots ==

1. The plugin settings page

== Changelog ==

= 1.0.2 =
* Add settings link on plugins page

= 1.0.1 =
* Trivial copy fix, use variables for titles and slugs where they exist

= 1.0 =
* Initial commit

== Upgrade Notice ==

None.
