=== Single-user-chat ===
Contributors: aakashbhagat23
plugin name: single user chat
Tags: chat, one to one chat, multuser chat, group chat, chat with shortcode,chat plugin with history,single user chat, Single-user-chat,chat plugin,multiuser chat with setting
Requires at least: 4.6
Tested up to: 5.0.2
Requires PHP: 5.2.4
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
 
Single-user chat, multi-user chat & group chat using shortcodes & enable disable option in backend
 
== Description ==
This plugin uses shortcode to provide one to one chat with logged in user
Backend setting to control multi-user chat. 
Use this shortcode [single_chat user_id=2] where 2 is the user id of second user first will be the loggedin user.
use this shortcode [multi_chat] for using multi user chat option on single page or post (Please Enable multi_chat option from chat setting).
Point to be noted:
-If multi user on every screen is enabled then don't use [multi_chat] shortcode. just check both the checkboxes on admin page.
-If multi user option is not enable then you can't use multi-user on every screen.
-User must be logged in to chat with other users
-you can use [single_chat user_id=2] dynamically change user_id by using do_shortcode in templates
 
**There is lot more to come**

This plugin uses shortcode to provide one to one chat with logged in user.
Backend setting to control multi-user chat. 
Use this shortcode [single_chat user_id=2] where 2 is the user id of second user first will be the loggedin user.
use this shortcode [multi_chat] for using multi user chat option on single page or post (Please Enable multi_chat option from chat setting).
Point to be noted:
-If multi user on every screen is enabled then don't use [multi_chat] shortcode. just check both the checkboxes on admin page.
-If multi user option is not enable then you can't use multi-user on every screen.
-User must be logged in to chat with other users
-you can use [single_chat user_id=2] dynamically change user_id by using do_shortcode in templates
-Perfect active and offline status green represent active and red represent offline


== Installation ==

1. Upload `single-user-chat` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Now you are ready to go :) .

== Frequently Asked Questions ==

= Does this plugin requires any setup? =

yes this plugin requires setup, just register users to your website and they can chat with each other. you can use shortcode [single_chat user_id = 2 ] 'here 2 is the second userid and first user is the logged in user'

= Does it stores the chat history? =

Yes chat history are stored, this plugin is useful for just chatting between two users internally and stored data in Your website DB

= User shows active when its not? =

now it is working fine after new updation

= Chat option is not showing? =

User must be logged in to chat with other users if user is logged in then may be shortcode is not used correctly it is case sensitive
 
== Screenshots ==

1. screenshot-1.jpg
2. screenshot-2.jpg
3. screenshot-3.jpg
4. screenshot-4.jpg
 
