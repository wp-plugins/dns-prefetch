=== DNS Prefetch ===
Tags: dns, prefetch, optimization
Requires at least: 3.5
Tested up to: 3.9
Contributors: jp2112
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7EX9NB9TLFHVW
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds dns prefetching meta tags to your site.

== Description ==

This plugin implements DNS prefetching per the Mozilla specification for the Firefox browser. Hopefully, other browsers will eventually support DNS prefetching.

See https://developer.mozilla.org/en-US/docs/Controlling_DNS_prefetching for more detail.

Disclaimer: This plugin is not affiliated with or endorsed by Mozilla.

== Installation ==

1. Upload plugin file through the WordPress interface.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to Settings &raquo; DNS Prefetch, configure plugin.
4. View any of your pages, they should contain the following meta tag:

`<meta http-equiv="x-dns-prefetch-control" content="on">`

In addition, if you configured any additional domains, they should also be listed after the line of code above.

== Frequently Asked Questions ==

= How do I use the plugin? =

Go to Settings &raquo; DNS Prefetch and enter any domains you want to be prefetched by Firefox browsers, in addition to the ones already linked on your home page. Make sure the "enabled" checkbox is checked.

For example, you might have www.example.com linked on one of your subpages. By adding "//www.example.com" you instruct browsers to resolve the DNS for that domain, decreasing the latency should someone with a Firefox browser visit that page.

= I entered some text but don't see anything on the page. =

Are you caching your pages?

= I don't want the admin CSS. How do I remove it? =

Add this to your functions.php:

`remove_action('admin_head', 'insert_dpf_admin_css');`

== Screenshots ==

1. Plugin settings page (note the URLs entered)
2. HTML source of a webpage (the URLs above are added to the HTML source)

== Changelog ==

= 0.0.1 =
- created
- verified compatibility with WP 3.9

== Upgrade Notice ==

= 0.0.1 =
created, verified compatibility with WP 3.9