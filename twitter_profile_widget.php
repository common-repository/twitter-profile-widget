<?php
/*
Plugin Name: Twitter Profile Widget
Description: Adds a sidebar widget to display Twitter profiles. 
Version: 0.9
Author: nemooon
Author URI: http://nemooon.jp/
Plugin URI: http://nemooon.jp/plugin/twitter-profile-widget/
*/

define( 'TWITTER_PROFILE_PLUGIN_DIR', WP_PLUGIN_DIR.'/'.plugin_basename( 'twitter-profile-widget' ).'/' );
define( 'TWITTER_PROFILE_PLUGIN_URL', WP_PLUGIN_URL.'/'.plugin_basename( 'twitter-profile-widget' ).'/' );
define( 'TWITTER_API_USER_SHOW', 'http://api.twitter.com/1/users/show' );

class TwitterProfileWidget extends WP_Widget {
	
	var $screen_name = array();
	
	function TwitterProfileWidget(){
		wp_enqueue_style( 'twitter-profile-widget-style', TWITTER_PROFILE_PLUGIN_URL.'style.css', array(), null );
		$name = "Twitter Profile";
		$widget_options = array( 'description' => 'Twitterのプロフィールを表示します。' );
		parent::WP_Widget( false, $name, $widget_options );
		$settings = $this->get_settings();
		foreach( $settings as $instance ){
			if( !$instance['screen_name'] ) continue;
			$screen_name_list = explode( ',', $instance['screen_name'] );
			for( $i=0; $i<count( $screen_name_list ); $i++ ){
				if( !empty( $screen_name_list[$i] ) ){
					$this->screen_name[] = $screen_name_list[$i];
				}
			}
		}
		if( !empty( $this->screen_name ) ){
			add_action('template_redirect', array( $this, 'addUserShowFilter' ) );
		}
	}
	
	function addUserShowFilter(){
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'twitter-profile-widget', TWITTER_PROFILE_PLUGIN_URL.'twitter_profile.js', array( 'jquery' ), null );
		foreach( $this->screen_name as $screen_name ){
			$query = array( 'screen_name' => $screen_name, 'callback' => 'twitterProfileUpdate', 'suppress_response_codes' =>'1' );
			$url = TWITTER_API_USER_SHOW . '.json?' . http_build_query( $query );
			wp_enqueue_script( 'twitter-profile-widget-jsop-'.$screen_name, $url, array( 'jquery', 'twitter-profile-widget' ), null );
		}
	}
	
	function widget($args, $instance) {
		extract( $args );
		$title = apply_filters('widget_title', $instance['title']);
		if( empty( $instance['screen_name'] ) ) return;
		$this->screen_name = $screen_name;
		echo $before_widget . PHP_EOL;
		if ( $title ) echo $before_title . $title . $after_title . PHP_EOL;
		$screen_name_list = explode( ',', $instance['screen_name'] );
		$callbacks = array();
		for( $i=0; $i<count( $screen_name_list ); $i++ ):
			$screen_name = $screen_name_list[$i];
			if( $screen_name == '' ) continue;
			$last = count( $screen_name_list ) == $i+1;
			$query = array( 'screen_name' => $screen_name );
			$jsonp = true;
?>
	<div id="<?php echo "TwitterProfile_$screen_name"; ?>" class="twitter-profile">
		<div class="tp_icon_name">
			<a class="tp_user_link" href="http://twitter.com/<?php echo $screen_name; ?>" target="_blank">
				<img width="48" height="48" class="tp_profile_image" src="<?php echo $usershow['profile_image_url']; ?>" />
			</a>
			<a class="tp_user_link" href="http://twitter.com/<?php echo $screen_name; ?>" target="_blank">
				<span class="tp_name"></span>
			</a><br />
			@<span class="tp_screen_name"><?php echo $screen_name; ?></span><br />
			<span class="tp_time_zone"></span>
		</div>
		<div class="tp_profile">
			<span class="label">Location</span>
			<span class="tp_location"></span>
		</div>
		<div class="tp_profile">
			<span class="label">Bio</span>
			<span class="tp_description"></span>
		</div>
		<div class="tp_profile">
			<span class="label">Web</span>
			<span class="tp_url"></span>
		</div>
		<div class="tp_profile">
			<span class="label">Latest Tweet</span>
			<span class="tp_status_text"></span>
		</div>
		<table class="tp_counts">
			<tr>
				<td>
					<a href="http://twitter.com/<?php echo $screen_name; ?>">
						<span class="tp_statuses_count"></span><br />tweets
					</a>
				</td>
				<td>
					<a href="http://twitter.com/<?php echo $screen_name; ?>/following">
						<span class="tp_friends_count"></span><br />following
					</a>
				</td>
				<td>
					<a href="http://twitter.com/<?php echo $screen_name; ?>/followers">
						<span class="tp_followers_count"></span><br />followers
					</a>
				</td>
			</tr>
		</table>
	</div>
<?php
		endfor;
		echo $after_widget . PHP_EOL;
	}
	
	function update($new_instance, $old_instance) {
		return $new_instance;
	}
	
	function form($instance) {
		$title = esc_attr($instance['title']);
		$screen_name = esc_attr($instance['screen_name']);
?>
<p>
	<label><?php _e('Title:'); ?>
	<input class="widefat" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label>
</p>
<p>
	<label><?php _e('Screen name:'); ?>
	<input class="widefat" name="<?php echo $this->get_field_name('screen_name'); ?>" type="text" value="<?php echo $screen_name; ?>" /></label>
</p>
<?php
	}
}

add_action('widgets_init', create_function( '', 'return register_widget("TwitterProfileWidget");' ));
?>