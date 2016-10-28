=== InfiniteWP Client ===
Contributors: infinitewp
Tags: admin, administration, amazon, api, authentication, automatic, dashboard, dropbox, events, integration, manage, multisite, multiple, notification, performance, s3, security, seo, stats, tracking, infinitewp, updates, backup, restore, iwp, infinite
Requires at least: 3.0
Tested up to: 4.4.2
Stable tag: trunk

Install this plugin on unlimited sites and manage them all from a central dashboard.
This plugin communicates with your InfiniteWP Admin Panel.

== Description ==

[InfiniteWP](http://infinitewp.com/ "Manage Multiple WordPress") allows users to manage unlimited number of WordPress sites from their own server.

Main features:

*   Self-hosted system: Resides on your own server and totally under your control
*   One-click updates for WordPress, plugins and themes across all your sites
*   Instant backup and restore your entire site or just the database
*   One-click access to all WP admin panels
*   Bulk Manage plugins & themes: Activate & Deactive multiple plugins & themes on multiple sites simultaneously
*   Bulk Install plugins & themes in multiple sites at once
*   and more..

Visit us at [InfiniteWP.com](http://infinitewp.com/ "Manage Multiple WordPress").

Check out the [InfiniteWP Overview Video](http://www.youtube.com/watch?v=IOu7LdyPOSs) below.

http://www.youtube.com/watch?v=IOu7LdyPOSs

Credits: [Vladimir Prelovac](http://prelovac.com/vladimir) for his worker plugin on which the client plugin is being developed.


== Installation ==

1. Upload the plugin folder to your /wp-content/plugins/ folder
2. Go to the Plugins page and activate InfiniteWP Client
3. If you have not yet installed the InfiniteWP Admin Panel, visit [InfiniteWP.com](http://infinitewp.com/ "Manage Multiple WordPress"), download the free InfiniteWP Admin Panel & install on your server.
4. Add your WordPress site to the InfiniteWP Admin Panel and start using it.

== Screenshots ==

1. Sites & Group Management
2. Search WordPress Plugin Repository
3. Bulk Plugin & Theme Management
4. One-click access to WordPress admin panels
5. One-click updates

== Changelog ==

= 1.5.1.1 =
* Improvement: Verifying backup uploaded to Amazon S3 utilized higher bandwidth.

= 1.5.1 =
* Improvement: Some of the deprecated WP functions are replaced with newer ones.
* Fix: SQL error populated while converting the IWP backup tables to UTF8 or UTF8MB4 in certain WP sites.
* Fix: DB optimization not working on the WP Maintenance addon.
* Fix: Versions prior to WP v3.9 were getting the wpdb::check_connection() fatal error.

= 1.5.0 =
* Improvement: Compatibility with PHP7.
* Improvement: Memory usage in Multi call backup now optimized.
* Improvement: Support for new Amazon S3 SDK. Added support for Frankfut bucket which will solve random errors in amazon backup. For php < v5.3.3 will use older S3 library.
* Improvement: Timeout will be reduced in Single call backup Zip Archive.
* Improvement: Client plugin will support MySQLi by using wpdb class.
* Improvement: All tables created by client plugin will use default DB engine.
* Improvement: Maintenance mode status also included in reload data. This will result in the IWP Admin Panel displaying relevant status colours.
* Improvement: Support for WP Maintenance Addon's new options - Clear trash comments, Clear trash posts, Unused posts metadata, Unused comments metadata, Remove pingbacks, Remove trackbacks.
* Improvement: Dedicated cacert.pem file introduced for Dropbox API." client plugin.
* Fix: Issue with IWP DB Table version not updating.
* Fix: Backup DB table now uses WP's charset (default UTF8). This will solve filename issues with foreign (umlaut) characters.
* Fix: Temp files not getting deleted while using single call backup in certain cases.

= 1.4.2.2 =
* Fix: Fatal error while calling wp_get_translation_updates() in WP versions lower than v3.7.

= 1.4.2.1 =
* Fix: Reload data broken for certain users in v1.4.2 client plugin.

= 1.4.2 =
* Improvement: Translation update support.
* Improvement: All executable files in client plugin should check the running script against the file name to prevent running directly for improved security.
* Improvement: Error message improved for premium plugin/theme when not registered with iwp process.
* Fix: Some admin theme blocks IWP Client from displaying activation key.

= 1.4.1 =
* Fix: Branding should take effect which we lost in v1.4.0 without making any changes.

= 1.4.0 =
* Improvement: Compatibility with v2.5.0 and latest versions of addons.
* Fix: Updates-related conflict with iThemes Security plugin and InfiniteWP fix.
* Fix: Google Drive backups uploaded to some other infinitewp folder instead one present in main directory.
* Fix: Clearing temp files created by PCLZip which is left because of timeout issue.

= 1.3.16 =
* Fix: Dropbox download while restore create memory issue Fatal Error: Allowed Memory Size of __ Bytes Exhausted.

= 1.3.15 =
* Improvement: Security improvement.
* Fix: Parent theme update showing as child theme update.
* Fix: Bug fixes.

= 1.3.14 =
* Fix: Bug fix.

= 1.3.13 =
* Fix: In certain cases, a multi-call backup of a large DB missed a few table's data.

= 1.3.12 =
* Fix: In a few servers, readdir() was creating "Empty reply from server" error and in WPEngine it was creating 502 error while taking backup
* Fix: .mp4 was excluding by default 

= 1.3.11 =
* Improvement: using wp_get_theme() instead of get_current_theme() which is deprecated in WordPress      
* Fix: IWP failed to recognise the error from WP v4.0
* Fix: Restoring backup for second time
* Fix: $HTTP_RAW_POST_DATA is made global, which is conflicting with other plugin
* Fix: Install a plugin/theme from Install > My Computer from panel having IP and different port number
* Fix: Install a plugin/theme from Install > My Computer from panel protected by basic http authentication
* Fix: Google Webmaster Redirection not working with a few themes
* Fix: Bug fixes

= 1.3.10 =
* Fix: Bug Fix - This version fixes an Open SSL bug that was introduced in v1.3.9. If you updated to v1.3.9 and are encountering connection errors, update the Client Plugin from your WP dashboards. You don't have to re-add the sites to InfiniteWP.

= 1.3.9 =
* Fix: WP Dashboard jQuery conflict issue. 
* Fix: Empty reply from server created by not properly configured OpenSSL functions.
* Fix: Google Drive backup upload timeout issue.

= 1.3.8 =
* Fix: Fixed a security bug that would allow someone to put WP site into maintenance mode if they know the admin username. 

= 1.3.7 =
* Fix: Dropbox SSL3 verification issue.

= 1.3.6 =
* Fix: IWP's PCLZIP clash with other plugins. PCLZIP constants have been renamed to avoid further conflicts. This will fix empty folder error - "Error creating database backup folder (). Make sure you have correct write permissions."
* Fix: Amazon S3 related - Call to a member function list_parts() on a non-object in wp-content/plugins/iwp-client/backup.class.multicall.php on line 4587.

= 1.3.5 =
* Improvement: Support for iThemes Security Pro.
* Fix: IWP's PCLZIP clash with other plugins.

= 1.3.4 =
* Feature: Maintenance mode with custom HTML.
* New: WP site's server info can be viewed.
* Improvement: Simplified site adding process - One-click copy & paste.
* Improvement: New addons compatibility.

= 1.3.3 =
* Fix: False "FTP verification failed: File may be corrupted" error.

= 1.3.2 =
* Fix: Dropbox backup upload in single call more then 50MB file not uploading issue.

= 1.3.1 =
* Fix: "Unable to create a temporary directory" while cloning to exisiting site or restoring.
* Fix: Disabled tracking hit count.

= 1.3.0 =
* Improvement: Multi-call backup & upload.
* Fix: Fatal error Call to undefined function get_plugin_data() while client plugin update.
* Fix: Bug fixes.


= 1.2.15 =
* Improvement: Support for backup upload to SFTP repository.
* Fix: Bug fixes.

= 1.2.14 =
* Improvement: SQL dump taken via mysqldump made compatible for clone.

= 1.2.13 =
* Fix: Google library conflict issues are fixed.

= 1.2.12 =
* Improvement: Backup process will only backup WordPress tables which have configured prefix in wp-config.php.
* Improvement: Support for Google Drive for cloud backup addon.
* Improvement: Minor improvements.
* Fix: Bug fixes

= 1.2.11 =
* Fix: Bug fixes

= 1.2.10 =
* Fix: wp_iwp_redirect sql error is fixed


= 1.2.9 =
* Improvement: Support for new addons.
* Fix: Strict Non-static method set_hit_count() and is_bot() fixed.

= 1.2.8 =
* Fix: Minor security update

= 1.2.7 =
* Fix: Activation failed on multiple plugin installation is fixed
* Fix: Dropbox class name conflit with other plugins is fixed
* Fix: Bug fixes

= 1.2.6 =
* Fix: Bug fixes

= 1.2.5 =
* Improvement: Compatible with WP updates 3.7+


= 1.2.4 =
* Fix: Empty backup list when schedule backup is created/modified

= 1.2.3 =
* Fix: Gravity forms update support

= 1.2.2 =
* Improvement: Minor improvements for restore/clone
* Fix: Warning errors and bug fixes for restore/clone

= 1.2.1 =
* Fix: Fatal error calling prefix method while cloning a fresh package to existing site

= 1.2.0 =
* Improvement: Backup fail safe option now uses only php db dump and pclZip
* Improvement: Better feedback regarding completion of backups even in case of error
* Improvement: Restore using file system (better handling of file permissions)
* Fix: Notice issue with unserialise

= 1.1.10 =
* Charset issue fixed for restore / clone
* Dropbox improved
* Cloning URL and folder path fixed


= 1.1.9 =
* Better error reporting
* Improved connection reliability

= 1.1.8 =
* Minor fixes

= 1.1.7 =
* Old backups retained when a site is restored
* Compatible with Better WP Security
* Compatible with WP Engine
* Improved backups
* Bug fixes

= 1.1.6 =
* Multisite updates issue fixed

= 1.1.5 =
* WP 3.5 compatibility
* Backup system improved
* Dropbox upload 500 error fixed

= 1.1.4 =
* Bug in command line backup fixed

= 1.1.3 =
* Backup improved and optimize table while backing up fixed
* Excluding wp-content/cache & wp-content/w3tc/ by default
* Amazon S3 backup improved
* pclZip functions naming problem fixed
* get_themes incompatibility fixed

= 1.1.2 =
* Respository issue when openSSL is not available, fixed
* Restore MySQL charset issue fixed
* Backups will not be removed when sites are re-added

= 1.1.1 =
* Improved backups
* Bug fixes

= 1.1.0 =
* Premium addons bugs fixed
* Reload data improved

= 1.0.4 =
* Premium addons compatibility
* Clearing cache and sending WP data
* Bugs fixed

= 1.0.3 =
* WordPress Multisite Backup issue fixed
* Bugs fixed

= 1.0.2 =
* Bugs fixed

= 1.0.1 =  
* WordPress Multisite support
* Bugs fixed

= 1.0.0 =  
* Public release
* Bugs fixed
* Feature Improvements

= 0.1.5 =
* Client plugin update support from IWP Admin Panel 
* Backup file size format change


= 0.1.4 =  
* Private beta release
