=== Transient Cleaner ===
Contributors: dartiss
Donate link: http://artiss.co.uk/donate
Tags: cache, clean, database, housekeep, options, table, tidy, transient, update, upgrade
Requires at least: 3.3
Tested up to: 4.5
Stable tag: 1.4.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Housekeep expired transients from your options table

== Description ==

"Transients are a simple and standardized way of storing cached data in the WordPress database temporarily by giving it a custom name and a timeframe after which it will expire and be deleted."

Unfortunately, expired transients only get deleted when you attempt to access them. If you don't access the transient then, even though it's expired, WordPress will not remove it. This is [a known "issue"](http://core.trac.wordpress.org/ticket/20316 "Ticket #20316") but due to reasons, which are explained in the FAQ, this has not been adequately resolved.

Why is this a problem? Transients are often used by plugins to "cache" data (my own plugins included). Because of the housekeeping problems this means that expired data can be left and build up, resulting in a bloated database table.

Meantime, this plugin is the hero that you've been waiting for. Simply activate the plugin, sit back and enjoy a much cleaner, smaller options table. It also adds the additional recommendation that after a database upgrade all transients will be cleared down.

Within `Administration` -> `Tools` -> `Transients` an options screen exists allowing you to tweak which of the various housekeeping you'd like to happen, including the ability to perform an ad-hoc run, and when you'd like the to be automatically scheduled. You can even request an optimization of the options table to give your system a real "pep"!

We'd like to thank WordPress Developer Andrew Nacin for his early discussion on this. Also, we'd like to acknowledge [the useful article at Everybody Staze](http://www.staze.org/wordpress-_transient-buildup/ "WordPress _transient buildup") for ensuring the proposed solution wasn't totally mad, and [W-Shadow.com](http://w-shadow.com/blog/2012/04/17/delete-stale-transients/ "Cleaning Up Stale Transients") for the cleaning code.

== Using hooks ==

If you're the type of odd person who likes to code for WordPress (really?) then we've added a couple of hooks so you can call our rather spiffy housekeeping functions...

`housekeep_transients` - this will clear down any expired transients
`clear_all_transients` - this will remove any and all transients, expired or otherwise

== Installation ==

Transient Cleaner can be found and installed via the Plugin menu within WordPress administration. Alternatively, it can be downloaded and installed manually...

1. Upload the entire `artiss-transient-cleaner` folder to your wp-content/plugins/ directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. That's it - you're done. Options can be changed in Administration via the Tools->Transients screen.

== Frequently Asked Questions ==

= Why hasn't this been fixed in the WordPress core? =

An attempt was made and lots of discussions ensued. Basically, some plugins don't use transients correctly and they use them as required storage instead of temporary cache data. This would mean any attempt by WordPress core to regularly housekeep transients may break some plugins and, hence, websites. WordPress didn't want to do this.

= Does that mean this plugin could break my site? =

If you have one of these badly written plugins, yes. However, we've yet to come across anybody reporting an issue.

= Have WordPress not done anything, then? =

Yes, they implemented the clearing down of all transients upon a database upgrade. If you have a multisite installation. And you're on the main site. They don't optimise the table after either, which this plugin does.

This could mean that the WordPress may run and ours as well but, well, if it's already been cleared then the second run isn't going to do anything so it doesn't add any overheads - it just ensures the optimisation occurs, no matter what.

= How often will expired transients be cleared down? =

Once a day and, by default, at midnight. However, the hour at which is runs can be changed in the settings screen.

It should be noted too that this will only run once the appropriate hour has passed AND somebody has been onto your site (with anybody visiting, the scheduler will not run).

= In the administration screen it sometimes refers to the number of transients and other times the number of records. What's the difference? =

A transient may consist of one or more records (normally a timed transient - the type that expires - has two) and without checking and matching them all up it can sometimes be hard to work out. So, where possible, we'll tell you the number of transients but, where we can't, we'll refer to the number of records on the database.

== Screenshots ==

1. Administration screen showing contextual help screen

== Changelog ==

= 1.4.2 =
* Maintenance: Updated branding, inc. adding donation links

= 1.4.1 =
* Bug: Awww... biscuits. I was being smart by including a call to a function to check something without realising you have to have WordPress 4.4 for it to work. Thankfully, it's not critical so I've removed it for now and will add a "proper" solution in future

= 1.4 =
* Enhancement: Re-written core code to work better with multisite installations
* Enhancement: Administration screen re-written to be more "in keeping" with the WordPress standard layout. More statistics about cleared transients are also shown
* Enhancement: Instead of piggy-backing the housekeeping schedule (which some people turn off) I've instead implemented my own - it defaults to midnight but, via the administration screen, you can change it to whatever hour floats your boat
* Enhancement: For those nerdy enough that they want to code links to our amazing cleaning functions, we've added some super whizzy hooks. Check the instructions about for further details
* Maintenance: This is now a Code Art production, so the author name has been updated and the donation link (including matching plugin meta) ripped out. I for one welcome our new overlords.
* Maintenance: Renamed the functions that began with atc_ to tc_
* Maintenance: I admit it, I've been naughty. I've been hard-coding the plugin folder in INCLUDES. Yes, I know. But I've fixed that now
* Maintenance: I've validated, sanitized, escaped and licked the data that's sent around the options screen. Okay, I didn't do that last one
* Bug: Some PHP errors were vanquished

= 1.3.1 =
* Maintenance: Added a text domain and domain path

= 1.3 =
* Enhancement: Added links to settings in plugin meta
* Enhancement: Updated admin screen headings for WP 4.3
* Enhancement: Now used time() instead of gmmktime(), so as to follow strict usage
* Bug: Big PHP error clean-up

= 1.2.4 =
* Maintenance: Updated links on plugin meta

= 1.2.3 =
* Bug: Removed PHP error

= 1.2.2 =
* Enhancement: Options are now only available to admin (super admin if a multisite)
* Bug: Removed reporting of "orphaned" transients - these are actually transients without a timeout

= 1.2.1 =
* Maintenance: Updated the branding of the plugin
* Enhancement: Added support link to plugin meta

= 1.2 =
* Maintenance: Split files because of additional code size
* Maintenance: Removed run upon activation
* Enhancement: Improved transient cleaning code efficiency (including housekeeping MU wide transients)
* Enhancement: Added administration screen (Tools->Transients) to allow ad-hoc runs and specify run options
* Enhancement: Show within new admin screen whether orphaned transients have been found (in this case full clear of the option table is recommended)
* Enhancement: Added internationalisation
* Enhancement: If external memory cache is in use display an admin box to indicate this plugin is not required

= 1.1 =
* Enhancement: Transients will be initially housekept when the plugin is activated

= 1.0 =
* Initial release

== Upgrade Notice ==

= 1.4.2 =
* Minor update to change branding

= 1.4.1 =
* Urgent update to fix a bug that will affect those running WordPress before version 4.4

= 1.4 =
* Lots of improved goodness, including a modifiable scheduler and better compatibility with multisite installations

= 1.3.1 =
* Minor update to add a text domain and path

= 1.3 =
* Some minor enhancements and a lot of PHP bug fixes

= 1.2.4 =
* Update to correct links on plugin meta

= 1.2.3 =
* Update to remove a pesky PHP error

= 1.2.2 =
* Update to ensure only admins can modify options

= 1.2.1 =
* Updated branding on the plugin

= 1.2 =
* Update to add new options screen and much improved housekeeping code

= 1.1 =
* Update to add housekeeping upon activation

= 1.0 =
* Initial release
