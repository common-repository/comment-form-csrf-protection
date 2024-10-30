=== Comment Form CSRF Protection ===
Contributors: ayeshrajans
Tags: comments, spam, security, csrf
Requires at least: 4.2
Tested up to: 6.3
Stable tag: 1.4
Requires PHP: 7.1
License: GPLv2 or later

Prevent Cross-Site Request Forgery attacks on your comments form.

== Description ==
WordPress has a 12-year-old unfixed security vulnerability that it does not properly validate incoming comments.

An attacker can trick both anonymous and logged-in users to post comments on a victim site without them realizing, while using their own credentials.

See this issue for more information: https://core.trac.wordpress.org/ticket/10931

This is a tiny (fewer than 40 effect lines of code) module that adds a secure token to the comment form and validate it before accepting any comment, thus making your comment forms secure as they should\'ve been for all these years!

It provides no UI - just install it, and you are all set!

1. This plugin adds a secret cryptographically-secure token to the comment form. This is a unique value and is computationally impractical to guess it.
2. Upon comment submission, the comment is rejected if the secret tokens are not present or computationally invalid.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. You are all set! There is nothing to configure. Your comment forms will contain the hidden token fields that will be properly validated upon submission.


== Changelog ==

= 1.0 =
* Initial release.

= 1.1 =
This is a minor release that contains minimal changes. 

* Marks the plugin as tested up-to WordPress 5.3
* Fix in `composer.json` file that it required PHP^7.2 instead of intended ^7.1
* A micro optimization in the plugin to call the lambda function directly within the CSRF check.

= 1.4 =
Minor release that contains several typo fixes and WordPress 6.3 compatibility
