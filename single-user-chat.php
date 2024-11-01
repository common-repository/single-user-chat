<?php 
/*
plugin name: single user chat
Description: This plugin uses shortcode to provide one to one chat with logged in user.Backend setting to control multi-user chat with user icon and online status( active or offline).
Author: Aakash Bhagat
Version: 0.5
*/
//if this file called directly then die
if ( ! defined( 'WPINC' ) ) {
	die;
}
// create table on plugin activate
register_activation_hook( __FILE__, 'single_user_chat_createdb' );
function single_user_chat_createdb(){
	global $table_prefix, $wpdb;
	$tblname = 'chat_sessions';
	$secondname= 'chat_data';
	$wptracktbl = $table_prefix."$tblname";
	$wpsecondtrack= $table_prefix."$secondname";

	if ($wpdb->get_var("show tables like '$wptracktbl'") != $wptracktbl) {
		$sql = "CREATE TABLE `".$wptracktbl."` (";
		$sql .=	" `c_id` int(100) NOT NULL AUTO_INCREMENT PRIMARY KEY,";
		$sql .=	" `user1` int(100),";
		$sql .=	" `user2` int(100),";
		$sql .=	" `seen` ENUM('0','1'),";
		$sql .= " `date_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP";
		$sql .= ") ENGINE='InnoDB' COLLATE 'utf8mb4_unicode_520_ci'; ";

		$sql .= "CREATE TABLE `".$wpsecondtrack."` (";
		$sql .= "`cd_id` int(100) NOT NULL AUTO_INCREMENT PRIMARY KEY,";
		$sql .=	" `chat_session_id` int(100),";
		$sql .= " `sender_id` int(100),";
		$sql .= " `reciever_id` int(100),";
		$sql .= " `message` varchar(100),";
		$sql .= " `date_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP";
		$sql .= ") ENGINE='InnoDB' COLLATE 'utf8mb4_unicode_520_ci'; ";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
		update_option( 'single_user_chat', 'activated' );
	}
	
}
add_action('wp_enqueue_scripts','single_user_chat_suc_enqueue_js');
function single_user_chat_suc_enqueue_js(){
		wp_register_script('chat-js',plugin_dir_url( __FILE__ ) . 'assets/js/index-not-min.js');
		wp_localize_script( 'chat-js', 'ajaxurl',  admin_url( 'admin-ajax.php' )  );
		wp_enqueue_script('chat-js', plugin_dir_url( __FILE__ ) . 'assets/js/index-not-min.js', array('jquery'), '1.0', true );
		wp_register_style('chat-css', plugin_dir_url( __FILE__ ) . 'assets/css/style-not-min.css');
		wp_enqueue_style('chat-css');
		wp_register_style('chat-font-awesome','https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
		wp_enqueue_style('chat-font-awesome');
	}

function single_user_chat_chat_section($atts){
		$current_user = wp_get_current_user();
		if(!is_user_logged_in()) :
			echo "You are not looged in <br />Log in to start chatting";
		else:
			if(	isset($atts['user_id']) ):
				$user_to_chat_id = $atts['user_id'];
			endif;
		$user = get_userdata( $user_to_chat_id );
			if($user):
				if($user->data->user_nicename):
					$display_name = $user->data->user_nicename;
				else:
					$display_name = $user->data->user_email;
				endif;
				if($current_user->ID == $user_to_chat_id):
					echo "you can't chat with you<br />change the user_id in shortcode";
				else:
					$a = shortcode_atts( array(
						'user1' => $current_user->ID,
						'user2'  =>  $user_to_chat_id
						), $atts );
				require_once('include/chat-section.php');
				endif;
				else:
					echo "The person you want to chat is not exist";
			endif;
		endif;
		}

add_shortcode('single_chat','single_user_chat_chat_section');
		function single_user_chat_get_chat_history(){
			global $table_prefix, $wpdb;
			$tblname = 'chat_sessions';
			$secondname= 'chat_data';
			$wptracktbl = $table_prefix."$tblname";
			$wpsecondtrack= $table_prefix."$secondname";
			$provider_user_id = $_REQUEST["user_id"];
			$current_user = get_current_user_id();
			$sql_for_session = "SELECT `c_id` FROM `".$wptracktbl."` WHERE (`user1` = '".$current_user."' AND `user2` = '".$provider_user_id."') OR (`user2` = '".$current_user."' AND `user1` = '".$provider_user_id."')";
			$session = $wpdb->get_results( $sql_for_session );
			if (count($session)>0) :
				$session_id = $session[0]->c_id;
				$sql_for_history = "SELECT * FROM `".$wpsecondtrack."`  WHERE `chat_session_id` = '".$session_id."' ";
				$chat_history = $wpdb->get_results( $sql_for_history );
				$last_row = end($chat_history);
				if ($current_user == $last_row->reciever_id	) :
					$wpdb->update(
						$wptracktbl,
						array(
							'seen' => 1), 
						array(
							'c_id' => $session_id)
						); 	
				endif;
				$results = json_encode($chat_history);
				_e($results);
				die;
			else:
				$wpdb->insert( 
					$wptracktbl, 
					array( 
						'user1'	=>	$current_user,
						'user2' => $provider_user_id, 
						'seen' => 0
						)
					);	
			$sql_for_session = "SELECT `c_id` FROM `".$wptracktbl."` WHERE (`user1` = '".$current_user."' AND `user2` = '".$provider_user_id."') OR (`user2` = '".$current_user."' AND `user1` = '".$provider_user_id."')";
			$session = $wpdb->get_results( $sql_for_session );

				if (count($session)>0) :
					$session_id = $session[0]->c_id;
					$sql_for_history = "SELECT * FROM `".$wpsecondtrack."`  WHERE `chat_session_id` = '".$session_id."' ";
					$chat_history = $wpdb->get_results( $sql_for_history );
					$last_row = end($chat_history);
					if ($current_user == $last_row->reciever_id	) :
						$wpdb->update(
							$wptracktbl,
							array(
								'seen' => 1), 
							array(
								'c_id' => $session_id)
							); 	
					endif;

					$results = json_encode($chat_history);
					_e($results);
					die;
				else:

					$results = json_encode(array('error'=>'can"t create db session'));
					_e($results);
				die;
				endif;
			endif;
		}
add_action('wp_ajax_get_chat_history', 'single_user_chat_get_chat_history');
add_action('wp_ajax_nopriv_get_chat_history', 'single_user_chat_get_chat_history');
function single_user_chat_get_new_mssgs(){
			global $table_prefix, $wpdb;
			$tblname = 'chat_sessions';
			$secondname= 'chat_data';
			$wptracktbl = $table_prefix."$tblname";
			$wpsecondtrack= $table_prefix."$secondname";

			$current_user = get_current_user_id();
			set_transient($current_user.'user_status','active',5);
			$sql_recieve_mssg = "SELECT `c_id` FROM `".$wptracktbl."` WHERE $current_user IN (user1, user2) AND `seen`='0'";
			$session = $wpdb->get_results( $sql_recieve_mssg );
			if (count($session) > 0) :
				foreach ($session as $seesionkey => $sessionvalue) {
					$last_mssg_query = "SELECT `reciever_id`, `sender_id` FROM `".$wpsecondtrack."` WHERE chat_session_id = $sessionvalue->c_id ORDER BY cd_id DESC limit 1";
					$last_mssg_query1[] = $wpdb->get_results($last_mssg_query);
				}
			else:
				$last_mssg_query1 = '';
			endif;
				echo json_encode($last_mssg_query1);
				die;
}
add_action('wp_ajax_get_new_mssgs', 'single_user_chat_get_new_mssgs');
add_action('wp_ajax_nopriv_get_new_mssgs', 'single_user_chat_get_new_mssgs');
//send messages of current loggedin userid  to requested userid
		function single_user_chat_send_chat_message(){
			global $table_prefix, $wpdb;
			$tblname = 'chat_sessions';
			$secondname= 'chat_data';
			$wptracktbl = $table_prefix."$tblname";
			$wpsecondtrack= $table_prefix."$secondname";
			$userid = $_REQUEST['user_id'];
			$message = wp_kses_post($_REQUEST['mssg']);
			$message = htmlspecialchars($message);
			$message = preg_replace('/[\r\n]+/','', $message);
			$mssgcount =  strlen($message);
			if(!$message):
				die;
			endif;
			$current_user = get_current_user_id();
			$sql_for_session = "SELECT `c_id` FROM `".$wptracktbl."` WHERE (`user1` = '".$current_user."' AND `user2` = '".$userid."') OR (`user1` = '".$userid."' AND `user2` = '".$current_user."')";
			$session = $wpdb->get_results( $sql_for_session );
			if (count($session)> 0):
				$session_id = $session[0]->c_id;
			else:
				$wpdb->insert( 
					$wptracktbl, 
					array( 
						'user1' => $current_user,
						'user2' => $userid
						)
					);
			endif;
			$sql_for_session = "SELECT `c_id` FROM `".$wptracktbl."` WHERE (`user1` = '".$current_user."' AND `user2` = '".$userid."') OR (`user1` = '".$userid."' AND `user2` = '".$current_user."')";
			$session = $wpdb->get_results( $sql_for_session );
			if (count($session)> 0):
				$session_id = $session[0]->c_id;
				$wpdb->insert( 
					$wpsecondtrack, 
					array( 
						'chat_session_id' => $session_id, 
						'sender_id' => $current_user,
						'reciever_id' => $userid,
						'message' => $message
						)
					);
				$wpdb->update(
					$wptracktbl,
					array(
						'seen' => 0), 
					array(
						'c_id' => $session_id)
					);
			else:
				echo json_encode(array('status' => 'error' ));
			endif;
			die;
		}
		add_action('wp_ajax_send_chat_message', 'single_user_chat_send_chat_message');
		add_action('wp_ajax_nopriv_send_chat_message', 'single_user_chat_send_chat_message');
//////////Admin-Panel///////////////
		function single_user_chat_add_theme_menu_item()
		{
			add_menu_page("Single user chat", "Single user chat", "manage_options", "single-user-chat", "single_user_chat_theme_settings_page", null, 99);
		}
		add_action("admin_menu", "single_user_chat_add_theme_menu_item");
		function single_user_chat_theme_settings_page()
		{
				?>
				<div class="wrap">
					<h1>Theme Panel</h1>
					<form method="post" action="options.php">
					<h3>Use this shortcode [single_chat user_id=2] where 2 is the user id by which logged in user will chat</h3>
					<h3>use this shortcode [multi_chat] for using multi user chat option on single page or post</h3>
					<h4>Point to be noted:</h4>
					<h5>*If multi user on every screen is enabled then don't use [multi_chat] shortcode</h5>
					<h5>*If multi user option is not enable then you can't use multi-user on every screen</h5>
					<h5>*User must be logged in to chat with other users</h5>
					<h5>*you can use [single_chat user_id=2] dynamically change user_id by using do_shortcode in templates</h5>
					<h5>*User status shows on the basis of user logged in('shows online') and after logged out('shows offline')</h5>
						<?php
						settings_fields("section");
						do_settings_sections("single-user-chat");      
						submit_button(); 
						?>          
					</form>
				</div>
				<?php
			}
		function single_user_chat_display_multiuser()
		{
			?>
			<input type="checkbox" name="multiple_enable" id="multiple" value="1" <?php echo checked( 1, get_option('multiple_enable'), false );?> /> multiuser option 
			<?php
		}
		function single_user_chat_display_every_where()
		{
			?>
			<input type="checkbox" name="every_where" id="everywhere" value="1" <?php echo checked( 1, get_option('every_where'), false );?> /> Multichat on Every Screen option
			<?php
		}

		
		function single_user_chat_display_theme_panel_fields()
		{
			add_settings_section("section", "All Settings", null, "single-user-chat");

			add_settings_field("multiple_enable", "Multi User Enable", "single_user_chat_display_multiuser", "single-user-chat", "section");
			add_settings_field("every_where", "Enable Multiuser chat on Every Screen", "single_user_chat_display_every_where", "single-user-chat", "section");
			register_setting("section", "multiple_enable");
			register_setting("section", "every_where");
		}
		add_action("admin_init", "single_user_chat_display_theme_panel_fields");
///////////////////////////////////////////////
/////////////Multi-user///////////////////////
		function single_user_chat_multi_chat_section(){
			if(get_option('multiple_enable')){
				global $wpdb;
				require_once('include/multi-chat-scetion.php');
				echo "<div id='wpchat'>";
				if ( is_user_logged_in() ) {
					$allUsers = get_users();
					$currentloggedin = wp_get_current_user();
					$cuser = $currentloggedin->ID;
					$cuname = $currentloggedin->user_login;
					$countu = count($allUsers);
					if($countu > 1){
						echo "<div id='multichatusers'><h3 id='multichatuserstitle'>All users<span id='multichathub'></span></h3>";
						echo "<div id='allusers'>";
						foreach($allUsers as $user) {
							if($cuname == $user->user_login){
							}else{
									echo '<a rel="'.$user->user_login.'" class="chat open-chat-box chat-msg-status-'.$user->ID.'" data-providername ="'.$user->user_login.'" id="'.$user->ID.'" href="#">'.get_avatar( $user->ID, 32 ).'' . $user->user_login . '<span class="chat-msg-notification chat-msg-notification-'.$user->ID.'"></span></a>';		
															
							}
						}	
						echo "</div>";
						echo "</div>";
					}else{
						echo "<div id='multichatnousers'>Sorry but there is no users to chat. first register them</div>";
					}
					echo '<div rel="'.$cuname.'" class="currentuser" id="'.$cuser.'"></div>';
				}else{
					//echo "<div id='nologin'>Login to chat with other users!</div>";
				}
				echo "</div>";
			}else{
				//echo "<br/>Multi User is not enable by admin";
			}
		}
		add_shortcode("multi_chat", "single_user_chat_multi_chat_section"); 
		function single_user_chat_update_login() {
			update_option( $_POST['log'], 'login' );
		}
		add_action('wp_login', 'single_user_chat_update_login');
		function single_user_chat_update_logout() {
			$current_user = wp_get_current_user();
			update_option( $current_user->user_login, 'logout' );
		}
		add_action('wp_logout', 'single_user_chat_update_logout');
//////////////////////////////////////////
///////Function for showing Multiuser chat Everywhere if multi user chat enble////////
if (get_option('every_where')) {
	add_action('wp_head','single_user_chat_do_short_on_head');
}
function single_user_chat_do_short_on_head(){
	   	remove_shortcode('multi_chat');
	   	single_user_chat_multi_chat_section();
	}
/////////////////////////////////////////////////
////////Fucntion for get user status/////////////
	function get_user_status(){
		if (is_user_logged_in()):
			$allUsers = get_users();
			$currentloggedin = wp_get_current_user();
			$cuser = $currentloggedin->ID;
			$cuname = $currentloggedin->user_login;
			$countu = count($allUsers);
			if($countu > 0 ):
				foreach($allUsers as $user) {
							if($cuname == $user->user_login){
							}else{
									if (FALSE === get_transient($user->ID.'user_status')){
										$userstatus[] = array('offline' => $user->ID);
									}else{
										$userstatus[] = array('activeuser' => $user->ID);
									}					
							}
						}
						echo json_encode($userstatus);
						die;
			endif;
		endif;
	}
	add_action('wp_ajax_get_user_status','get_user_status');
	add_action('wp_ajax_nopriv_get_user_status', 'get_user_status');
?>