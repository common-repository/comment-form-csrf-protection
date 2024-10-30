<?php
/*
Plugin Name: Comment Form CSRF Protection
Plugin URI: https://wordpress.org/plugins/comment-form-csrf-protection
Description: WordPress's default comment forms are not protected against Cross-Site Request Forgery. This plugin fixes that.
Version: 1.4
Author: Ayesh Karunaratne
Author URI: https://aye.sh/open-source
License: GPLv2 or later
*/

if (!function_exists('add_action')) {
	die();
}

add_action('comment_form', function () {
	/**
	 * This looks paranoid, but we use 2 tokens here. Seed, the "build_id" with a
	 * CSPRNG (PHP 7.0+). It's base64 encoded to fit nicely within HTML attributes.
	 *
	 * Generate a traditional wp_nonce that makes use of the user session ID
	 * and has a tick function to prevent replay attacks.
	 *
	 * Secondly, we use our own csrf_token with a proper HMAC with sha256. wp_nonce
	 * is generated with MD5, which we no longer consider secure enough.
	 */
	$fields               = [
		'build_id' => 'comment-form-csrf-' . base64_encode(random_bytes(32)),
		'wp_nonce' => wp_create_nonce('comment_form_csrf'),
	];
	$fields['csrf_token'] = \base64_encode(\hash_hmac('sha256', $fields['build_id'] . $fields['wp_nonce'],
		\SECURE_AUTH_KEY, true));

	foreach ($fields as $name => $value) {
		echo "<input type='hidden' name='{$name}' value='{$value}' />";
	}
});

add_action('pre_comment_on_post', function () {
	$status = static function (): bool {
		if (!isset($_POST['build_id'], $_POST['wp_nonce'], $_POST['csrf_token'])) {
			return FALSE;
		}

		if (\strpos($_POST['build_id'], 'comment-form-csrf-') !== 0) {
			return FALSE;
		}

		if (!wp_verify_nonce($_POST['wp_nonce'], 'comment_form_csrf')) {
			return FALSE;
		}

		return \hash_equals(
			\base64_encode(\hash_hmac('sha256', $_POST['build_id'] . $_POST['wp_nonce'], \SECURE_AUTH_KEY, true)),
			$_POST['csrf_token']
		);
	};

	if (!$status()) {
		wp_die('<p>' . __('Comment submission failed due to security 
		validation failures. Please go back, refresh the page, and try again.') . '</p>',
			__('Comment Submission Failed'),
			['response' => 403, 'back_link' => TRUE]);
	}
});
