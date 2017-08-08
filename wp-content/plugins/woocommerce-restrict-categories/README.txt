
== Description ==

WooCommerce Restrict Categories allows you to select which product categories users can access in your store. 

It works based on user account, or user role - your choice.


== Installation ==

Upload the plugin and activate it

Go to WooCommerce -> Restrict Categories to configure access rights.

Keep in mind that if you do not configure users or roles with specific categories then access is allowed to all categories.


== Configuration ==

By default all customers on your WooCommerce site have a role of "Customer", or possibly "Subscriber" depending on circumstances.

Note that you can add new roles to WordPress using a free plugin such as WP Role Manager. Any roles you add are available for use in WordPress and WooCommerce.

Also note that category access for individual user accounts take precedence over user roles.

So for example, user account restrictions are checked first. If they exist for a logged-in user's account then they are applied
and no checking is performed for the user's role. If no user account restrictions exist for the logged-in user then
role restrictions are checked. If role restrictions exist for the user's role then they are applied. If no user account
restrictions exist for a logged-in user and no role restrictions exist the logged-in user's role then no restrictions are
applied. 

You may configured which categories non-logged in users see by defining categories in Roles tab, in the Not Logged In box.

When a restriction is applied the following with be true:

- The affected user will not see blocked categories in WooCommerce Product Categories widget
- The affected user will not be able to access block category pages
- The affected user will not be able to access individual products that are in blocked categories. 

NOTE: We have not tested this plugin with any 3rd party category widgets, category plugins, or themes ( collectively
referred to as 3rd party software ), and if this plugin conflicts with 3rd party software IgniteWoo **will not consider
that to be a bug**. By "3rd party" we are referring to widgets and plugins that are not a standard part of the base
WooCommerce package, and any theme that is not published by IgniteWoo or WooThemes. You may contact us if you discover
a conflict with 3rd software, however it is our sole discretion whether we opt to resolve the conflict.


== Translators ===

Text domain is "ignitewoo-restrict-categories"

Place the language file in wp-content/languages/woocommerce directory.

Example: wp-content/langauges/woocommerce/ignitewoo-restrict-categories-en_US.mo



== Support ==

For support, contact us using the Contact page on our site, or via email at team [at] ignitewoo {dot} com, or
call us toll free at the number listed on our site at the top of all pages.

