=== SkyzCrm Rest integrator ===
Contributors: wordpress.org,dimaatimpactsoftcoil
Donate link: http://crm-erp.co.il/
Tags: C7, SkyzCRM, REST, Leads
Requires at least: 3.0.1
Requires PHP: 5.6
Tested up to: 5.0.3
Stable tag: 1.0.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

SkyzCrm C7 to rest form send plugin.

== Description ==

This plugin allows to our customers send form data on enabled with C7 via REST-API directly to SkyzCRM.
According to specific API enabled appliance on customers site it will at least send Lead and in future Ticket data.
For more customization our specialist advising required.  

== Installation ==

1. Upload `skyz.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to plugin settings page and enter your api-id , secret ,selector field and url.
3.a. Test tokenization button to approve connectivity with your skyzcrm site. 
4. Go to C7 form (install c7 hiden fields plugin optionaly) and add selector tag as defined in plugin settings with values 'lead' or 'ticket'.

== Frequently Asked Questions ==

= Basic API requirement =

Actually all you need is SkyzCRM account with restapi feature enabled,also you need
 create application and attach resource endpoints to work with via restapi .

= What about more selectors =

SkyzCRM provides basic set of operations as turnkey solution, but if there is
 need in more complicated appliance please contact us and we will happy
 to provide all information how to accomplish it .

== Screenshots ==

1. Settings screenshot-1.png.
2. Defining selector screenshot-.png.

== Changelog ==
= 1.0.3 =
* Added skyz connectivity fail send email with email address recipient defined in c7 form.
* Fixed internal table update routine.

= 1.0.2 =
* Added responce message logging .

= 1.0.1 =
* Fixed owner and campaign set.

= 1.0.0 =
* First release.
* Lead selector supported.

= 0.5 =
* Prerelease version custom install to test features.

== Upgrade Notice ==

= 1.0.0 =
Fixed settings panel.

= 0.5 =
This version fixes a ssl verify related bug.  Upgrade to be able access crm with self signed cert.


Here's a link to [WordPress](http://wordpress.org/ "Your favorite software") and one to [Markdown's Syntax Documentation][markdown syntax].
Titles are optional, naturally.

[markdown syntax]: http://daringfireball.net/projects/markdown/syntax
            "Markdown is what the parser uses to process much of the readme file"

Markdown uses email style notation for blockquotes and I've been told:
> Asterisks for *emphasis*. Double it up  for **strong**.

`<?php code(); // goes in backticks ?>`
