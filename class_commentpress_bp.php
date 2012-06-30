<?php /*
===============================================================
Class CommentPressBuddyPress Version 1.0
===============================================================
AUTHOR			: Christian Wach <needle@haystack.co.uk>
LAST MODIFIED	: 19/03/2012
---------------------------------------------------------------
NOTES
=====

This class encapsulates all BuddyPress compatibility

---------------------------------------------------------------
*/






/*
===============================================================
Class Name
===============================================================
*/

class CommentPressBuddyPress {






	/*
	===============================================================
	Properties
	===============================================================
	*/

	// parent object reference
	var $parent_obj;






	/**
	 * @description: initialises this object
	 * @param object $parent_obj a reference to the parent object
	 * @return object
	 * @todo:
	 *
	 */
	function __construct( $parent_obj = null ) {

		// store reference to "parent" (calling obj, not OOP parent)
		$this->parent_obj = $parent_obj;

		// check dependencies
		$this->check_dependencies();

		// init
		$this->_init();

		// --<
		return $this;

	}






	/**
	 * PHP 4 constructor
	 */
	function CommentPressBuddyPress( $parent_obj = null ) {

		// is this php5?
		if ( version_compare( PHP_VERSION, "5.0.0", "<" ) ) {

			// call php5 constructor
			$this->__construct( $parent_obj );

		}

		// --<
		return $this;

	}






	/**
	 * @description: set up all items associated with this object
	 * @todo:
	 *
	 */
	function initialise() {

	}






	/**
	 * @description: if needed, destroys all items associated with this object
	 * @todo:
	 *
	 */
	function destroy() {

	}






//#################################################################






	/*
	===============================================================
	PUBLIC METHODS
	===============================================================
	*/






	/*
	--------------------------------------------------------------------------------
	Plugin Internals
	--------------------------------------------------------------------------------
	*/

	/**
	 * @description: on activation, check for BuddyPress and BP-Groupblog
	 * @todo:
	 *
	 */
	function check_dependencies() {

		// init message
		$msg = array();

		// is it installed?
		if ( !defined( 'BP_VERSION' ) ) {

			// BuddyPress missing
			$msg[] = 'BuddyPress must be installed.';

		}

		// is BP Groupblog installed?
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if ( !is_plugin_active( 'bp-groupblog/loader.php' ) ) {

			// BuddyPress missing
			$msg[] = 'BP Groupblog must be installed.';

		}

		// did we get any errors?
		if ( !empty( $msg ) ) {

			// implode list for output
			$out = implode( "\n", $msg );

			// die
			die( $out );

		}

	}






	/**
	 * Add language support.
	 */
	function translation() {

		// only use, if we have it...
		if( function_exists('load_plugin_textdomain') ) {

			// load it
			load_plugin_textdomain(

				// unique name
				'cp-buddypress',

				// deprecated argument
				false,

				// path to directory containing translation files
				plugin_dir_path( CPBP_PLUGIN_FILE ) . 'languages/'

			);

		}

	}






	/*
	--------------------------------------------------------------------------------
	BuddyPress Compatibility
	--------------------------------------------------------------------------------
	*/

	/**
	 * @description: add an admin page for this plugin
	 * @todo:
	 *
	 */
	function add_admin_menu() {

		// we must be network admin
		if ( !is_super_admin() ) { return false; }



		// Add the admin page to the BuddyPress menu
		$page = add_submenu_page(

			'bp-general-settings',
			__( 'CommentPress Setup', 'cp-buddypress' ),
			__( 'CommentPress Setup', 'cp-buddypress' ),
			'manage_options',
			'cpbp_admin_page',
			array( &$this, 'admin_page' )

		);

		// add styles only on bp-groupblog admin page, see:
		// http://codex.wordpress.org/Function_Reference/wp_enqueue_script#Load_scripts_only_on_plugin_pages
		add_action( 'admin_print_styles-'.$page, array( &$this, 'add_admin_styles' ) );

	}






	/**
	 * @description: enqueue any styles and scripts needed by our admin page
	 * @todo:
	 *
	 */
	function add_admin_styles() {

		/*
		// EXAMPLES:

		// add css
		wp_enqueue_style('cpbp-admin-style', CPBP_PLUGIN_URL . 'css/admin.css');

		// add javascripts
		wp_enqueue_script( 'cpbp-admin-js', CPBP_PLUGIN_URL . 'js/admin.js' );
		*/

	}






	/**
	 * @description: show our admin page
	 * @todo:
	 *
	 */
	function admin_page() {

		// only allow network admins through
		if( is_super_admin() == false ) {

			// disallow
			wp_die( __( 'You do not have permission to access this page.', 'cp-buddypress' ) );

		}



		// sanitise admin page url
		$url = $_SERVER['REQUEST_URI'];
		$url_array = explode( '&', $url );
		if ( is_array( $url_array ) ) { $url = $url_array[0]; }





		// check for BuddyPress installation

		// assume not installed
		$bp = '<p class="alert">Please install BuddyPress correctly</p>';

		// is it installed?
		if ( defined( 'BP_VERSION' ) ) {

			// we've got it
			$bp = '<p>BuddyPress installed</p>';

		}




		// check for group blog installation
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$groupblog = is_plugin_active( 'bp-groupblog/loader.php' );

		// assume not installed
		$env = '<p class="alert">Please install BP Group Blog correctly</p>';

		// is it installed?
		if ( $groupblog ) {

			// we've got it
			$env = '<p>BP Group Blog installed</p>';

		}




		// define admin page - needs translation cap
		$admin_page = '
<div class="icon32" id="icon-options-general"><br/></div>

<h2>Commentpress for BuddyPress Settings</h2>

<form method="post" action="'.htmlentities($url.'&updated=true').'">

'.wp_nonce_field( 'cpbp_admin_action', 'cpbp_nonce', true, false ).'
'.wp_referer_field( false ).'



<p style="padding-top: 30px;">Checking environment...</p>

'.$bp.'
'.$env.'



<p class="submit">
	<input type="submit" name="cpbp_submit" value="Save Changes" class="button-primary" />
</p>

</form>'."\n\n\n\n";

		// done
		echo $admin_page;

	}






	/**
	 * @description: enqueue any styles and scripts needed by our public page
	 * @todo:
	 *
	 */
	function add_frontend_styles() {

		/*
		// EXAMPLES:

		// add css
		wp_enqueue_style('cpbp-admin-style', CPBP_PLUGIN_URL . 'css/admin.css');

		// add javascripts
		wp_enqueue_script( 'cpbp-admin-js', CPBP_PLUGIN_URL . 'js/admin.js' );
		*/

		// dequeue BP Tempate Pack CSS, even if queued
		wp_dequeue_style( 'bp' );

	}






	/*
	--------------------------------------------------------------------------------
	BP Groupblog Compatibility
	--------------------------------------------------------------------------------
	*/

	/**
	 * Allow HTML comments and content in Multisite blogs
	 */
	function allow_html_content() {

		// using publish_posts for now - means author+
		if ( current_user_can( 'publish_posts' ) ) {

			// remove html filtering on content. Note - this has possible consequences...
			// see: http://wordpress.org/extend/plugins/unfiltered-mu/
			kses_remove_filters();

		}
	}






	/**
	 * @description: override capability to comment based on group membership.
	 * @todo:
	 *
	 */
	function pre_comment_approved( $approved, $commentdata ) {

		//print_r( $commentdata ); die();

		global $wpdb;
		$blog_id = (int)$wpdb->blogid;

		// check if this blog is a group blog...
		$group_id = get_groupblog_group_id( $blog_id );

		// when this blog is a groupblog
		if ( is_numeric( $group_id ) ) {

			// is this user a member?
			if ( groups_is_user_member( $commentdata['user_id'], $group_id ) ) {

				// allow un-moderated commenting
				return 1;

			}

		}



		// pass through
		return $approved;

	}






	/**
	 * @description: override "publicness" of groupblogs so that we can set the hide_sitewide
	 * property of the activity item (post or comment) depending on the group's setting.
	 * @todo: test if they are CP-enabled?
	 *
	 */
	function is_blog_public( $blog_public_option ) {

		global $wpdb;
		$blog_id = (int)$wpdb->blogid;

		// check if this blog is a group blog...
		$group_id = get_groupblog_group_id( $blog_id );

		// when this blog is a groupblog
		if ( is_numeric( $group_id ) ) {

			// always true - so that activities are registered
			return 1;

		} else {

			return $blog_public_option;

		}

	}






	/**
	 * groupblog_set_group_to_post_activity ( $activity )
	 *
	 * Record the blog activity for the group - amended from bp_groupblog_set_group_to_post_activity
	 */
	function groupblog_custom_comment_activity( $activity ) {

		//print_r( array( 'a1' => $activity ) );// die();

		// only deal with comments
		if ( ( $activity->type != 'new_blog_comment' ) ) return;

		// only do this on CP-enabled groupblogs
		if ( ( false === $this->_is_commentpress_groupblog() ) ) return;



		// get the group
		$blog_id = $activity->item_id;
		$group_id = get_groupblog_group_id( $blog_id );
		if ( !$group_id ) return;
		$group = groups_get_group( array( 'group_id' => $group_id ) );
		//print_r( $group ); die();

		// see if we already have the modified activity for this blog post
		$id = bp_activity_get_activity_id( array(

			'user_id' => $activity->user_id,
			'type' => 'new_groupblog_comment',
			'item_id' => $group_id,
			'secondary_item_id' => $activity->secondary_item_id

		) );

		// if we don't find a modified item...
		if ( !$id ) {

			// see if we have an unmodified activity item
			$id = bp_activity_get_activity_id( array(

				'user_id' => $activity->user_id,
				'type' => $activity->type,
				'item_id' => $activity->item_id,
				'secondary_item_id' => $activity->secondary_item_id

			) );

		}



		// If we found an activity for this blog comment then overwrite that to avoid having
		// multiple activities for every blog comment edit
		if ( $id ) $activity->id = $id;



		// get the comment
		$comment = get_comment( $activity->secondary_item_id );
		//print_r( $comment ); //die();

		// get the post
		$post = get_post( $comment->comment_post_ID );
		//print_r( $post ); die();

		// was it a registered user?
		if ($comment->user_id != '0') {

			// get user details
			$user = get_userdata( $comment->user_id );

			// construct user link
			$user_link = bp_core_get_userlink( $activity->user_id );

		} else {

			// show anonymous user
			$user_link = '<span class="anon-commenter">'.__( 'Anonymous', 'cp-buddypress' ).'</span>';

		}

		// allow plugins to override the name of the activity item
		$activity_name = apply_filters(
			'cp_activity_post_name',
			__( 'blog post', 'cp-buddypress' )
		);

		// set key
		$key = '_cp_comment_page';

		// if the custom field has a value, we have a subpage comment...
		if ( get_comment_meta( $comment->comment_ID, $key, true ) != '' ) {

			// get comment's page from meta
			$page_num = get_comment_meta( $comment->comment_ID, $key, true );

			// get the url for the comment
			$link = cp_get_post_multipage_url( $page_num ).'#comment-'.$comment->comment_ID;

			// amend the primary link
			$activity->primary_link = $link;

			// init target link
			$target_post_link = '<a href="' . cp_get_post_multipage_url( $page_num, $post ) .'">' . esc_html( $post->post_title ) . '</a>';

		} else {

			// init target link
			$target_post_link = '<a href="' . get_permalink( $post->ID ) .'">' . esc_html( $post->post_title ) . '</a>';

		}

		// Replace the necessary values to display in group activity stream
		$activity->action = sprintf(

			__( '%s left a %s on a %s %s in the group %s:', 'cp-buddypress' ),

			$user_link,
			'<a href="' . $activity->primary_link .'">' . __( 'comment', 'cp-buddypress' ) . '</a>',
			$activity_name,
			$target_post_link,
			'<a href="' . bp_get_group_permalink( $group ) . '">' . esc_html( $group->name ) . '</a>'

		);

		// apply group id
		$activity->item_id = (int)$group_id;

		// change to groups component
		$activity->component = 'groups';

		// having marked all groupblogs as public, we need to hide activity from them if the group is private
		// or hidden, so they don't show up in sitewide activity feeds.
		if ( 'public' != $group->status ) {
			$activity->hide_sitewide = true;
		} else {
			$activity->hide_sitewide = false;
		}

		// set unique type
		$activity->type = 'new_groupblog_comment';



		// prevent from firing again
		remove_action( 'bp_activity_before_save', array( &$this, 'groupblog_custom_comment_activity' ) );


		// --<
		return $activity;

	}






	/**
	 * @description: add some meta for the activity item - bp_activity_after_save doesn't seem to fire
	 * @todo:
	 *
	 */
	function groupblog_custom_comment_meta( $activity ) {

		print_r( array( 'a' => $activity ) );

		// only deal with comments
		if ( ( $activity->type != 'new_groupblog_comment' ) ) return;

		// only do this on CP-enabled groupblogs
		if ( ( false === $this->_is_commentpress_groupblog() ) ) return;



		// set a meta value for the blog type of the post
		$meta_value = $this->_get_groupblog_type();
		print_r( array( 'a' => $activity ) );
		print_r( array( 'm' => $meta_value ) );
		$result = bp_activity_update_meta( $activity->id, 'groupblogtype', 'groupblogtype-'.$meta_value );
		print_r( array( 'r' => ( ( $result === true ) ? 't' : 'f' ) ) ); die();



		// prevent from firing again
		remove_action( 'bp_activity_after_save', array( &$this, 'groupblog_custom_comment_meta' ) );



		// --<
		return $activity;

	}






	/**
	 * see: bp_groupblog_set_group_to_post_activity ( $activity )
	 *
	 * Record the blog post activity for the group - by Luiz Armesto
	 */
	function groupblog_custom_post_activity( $activity ) {

		// only on new blog posts
		if ( ( $activity->type != 'new_blog_post' ) ) return;

		// only on CP-enabled groupblogs
		if ( ( false === $this->_is_commentpress_groupblog() ) ) return;

		//print_r( array( 'a1' => $activity ) ); //die();


		// clarify data
		$blog_id = $activity->item_id;
		$post_id = $activity->secondary_item_id;
		$post = get_post( $post_id );



		// get group id
		$group_id = get_groupblog_group_id( $blog_id );
		if ( !$group_id ) return;

		// get group
		$group = groups_get_group( array( 'group_id' => $group_id ) );



		// see if we already have the modified activity for this blog post
		$id = bp_activity_get_activity_id( array(

			'user_id' => $activity->user_id,
			'type' => 'new_groupblog_post',
			'item_id' => $group_id,
			'secondary_item_id' => $activity->secondary_item_id

		) );

		// if we don't find a modified item...
		if ( !$id ) {

			// see if we have an unmodified activity item
			$id = bp_activity_get_activity_id( array(

				'user_id' => $activity->user_id,
				'type' => $activity->type,
				'item_id' => $activity->item_id,
				'secondary_item_id' => $activity->secondary_item_id

			) );

		}

		// If we found an activity for this blog post then overwrite that to avoid have multiple activities for every blog post edit
		if ( $id ) $activity->id = $id;

		// allow plugins to override the name of the activity item
		$activity_name = apply_filters(
			'cp_activity_post_name',
			__( 'blog post', 'cp-buddypress' )
		);

		// Replace the necessary values to display in group activity stream
		$activity->action = sprintf(

			__( '%s wrote a new %s %s in the group %s:', 'cp-buddypress' ),

			bp_core_get_userlink( $post->post_author ),
			$activity_name,
			'<a href="' . get_permalink( $post->ID ) .'">' . esc_attr( $post->post_title ) . '</a>',
			'<a href="' . bp_get_group_permalink( $group ) . '">' . esc_attr( $group->name ) . '</a>'

		);

		$activity->item_id = (int)$group_id;
		$activity->component = 'groups';

		// having marked all groupblogs as public, we need to hide activity from them if the group is private
		// or hidden, so they don't show up in sitewide activity feeds.
		if ( 'public' != $group->status ) {
			$activity->hide_sitewide = true;
		} else {
			$activity->hide_sitewide = false;
		}

		// CMW: assume groupblog_post is intended
		$activity->type = 'new_groupblog_post';

		//print_r( array( 'a2' => $activity ) ); die();


		// prevent from firing again
		remove_action( 'bp_activity_before_save', array( &$this, 'groupblog_custom_post_activity' ) );



		// --<
		return $activity;

	}






	/**
	 * @description: add some meta for the activity item
	 * @todo:
	 *
	 */
	function groupblog_custom_post_meta( $activity ) {

		// only on new blog posts
		if ( ( $activity->type != 'new_groupblog_post' ) ) return;

		// only on CP-enabled groupblogs
		if ( ( false === $this->_is_commentpress_groupblog() ) ) return;



		// set a meta value for the blog type of the post
		$meta_value = $this->_get_groupblog_type();
		bp_activity_update_meta( $activity->id, 'groupblogtype', 'groupblogtype-'.$meta_value );



		// --<
		return $activity;

	}






	/**
	 * Add a filter option to the filter select box on group activity pages.
	 */
	function groupblog_comments_filter_option() {

		// default name
		$comment_name = __( 'Commentpress Comments', 'cp-buddypress' );

		// allow plugins to override the name of the option
		$comment_name = apply_filters( 'cp_groupblog_comment_name', $comment_name );

		// construct option
		$option = '<option value="new_groupblog_comment">'.$comment_name.'</option>'."\n";

		// print
		echo $option;

	}






	/**
	 * @description: override the name of the filter item
	 * @todo:
	 *
	 */
	function groupblog_posts_filter_option( $slug ) {

		// default name
		$_name = __( 'Commentpress Posts', 'cp-buddypress' );

		// allow plugins to override the name of the option
		$_name = apply_filters( 'cp_groupblog_post_name', $_name );

		// construct option
		$option = '<option value="new_groupblog_post">'.$_name.'</option>'."\n";

		// print
		echo $option;

	}






	/**
	 * @description: for group blogs, override the avatar with that of the group
	 * @todo:
	 *
	 */
	function get_blog_avatar( $avatar, $blog_id = '', $args ){

		// did we get anything?
		//print_r( $blog_id ); die();

		// get the group id
		$group_id = get_groupblog_group_id( $blog_id );

		// did we get a group for which this is the group blog?
		if ( isset( $group_id ) ) {

			// --<
			return bp_core_fetch_avatar( array( 'item_id' => $group_id, 'object' => 'group' ) );

		} else {

			// --<
			return $avatar;

		}

	}






	/**
	 * @description: override the name of the sub-nav item
	 * @todo:
	 *
	 */
	function filter_blog_name( $name ) {

		return __( 'Document', 'cp-buddypress' );

	}






	/**
	 * @description: override the slug of the sub-nav item
	 * @todo:
	 *
	 */
	function filter_blog_slug( $slug ) {

		return 'document';

	}







	/**
	 * @description: remove group blogs from blog list
	 * @todo:
	 *
	 */
	function remove_groupblog_from_loop( $b, $blogs ) {

		print_r( array( 'b' => $b, 'blogs' => $blogs ) ); die();

		// loop through them
		foreach ( $blogs->blogs as $key => $blog ) {

			// exclude if it's a group blog
			if ( function_exists( 'groupblog_group_id' ) ) {

				// get group id
				$group_id = get_groupblog_group_id( $blog->blog_id );
				//print_r( array( 'g' => $group_id ) );

				// did we get one?
				if ( is_numeric( $group_id ) ) {

					// exclude
					unset( $blogs->blogs[$key] );

					// recalculate global values
					$blogs->blog_count = $blogs->blog_count - 1;
					$blogs->total_blog_count = $blogs->total_blog_count - 1;
					$blogs->pag_num = $blogs->pag_num - 1;

				}

			}

		}

		//die();

		//print_r( array( 'b' => $b, 'blogs' => $blogs ) ); die();

		/* Renumber the array keys to account for missing items */
		$blogs_new = array_values( $blogs->blogs );
		$blogs->blogs = $blogs_new;

		return $blogs;

	}






	/**
	 * @description: override the name of the button on the BP "blogs" screen
	 * @todo:
	 *
	 */
	function get_blogs_visit_blog_button( $button ) {

		//print_r( $button ); die();

		global $blogs_template;
		if( !get_groupblog_group_id( $blogs_template->blog->blog_id ) ) {

			// leave the button untouched?

		} else {

			// update link
			$label = __( 'View Document', 'cp-buddypress' );
			$button['link_text'] = apply_filters( 'cp_get_blogs_visit_blog_button', $label );
			$button['link_title'] = apply_filters( 'cp_get_blogs_visit_blog_button', $label );

		}

		return $button;

	}






	/**
	 * @description: hook into the group blog signup form
	 * @todo:
	 *
	 */
	function signup_blogform( $errors ) {

		// only apply to group blog signup form
		if ( bp_is_groups_component() ) {

			global $bp, $groupblog_create_screen;

			$blog_id = get_groupblog_blog_id();

			if ( !$groupblog_create_screen && $blog_id != '' ) {

				// existing blog and group - do we need to present any options?

			} else {

				// creating a new group - no groupblog exists yet
				// NOTE: need to check that our context is right

				// define title
				$title = __( 'CommentPress Options', 'cp-buddypress' );

				// define text
				$text = __( 'When you create a group blog, you can choose to enable it as a CommentPress blog. This is a "one time only" option because you cannot disable CommentPress from here once the group blog is created. If you choose an existing blog as a group blog, setting this option will have no effect.', 'cp-buddypress' );

				// define enable label
				$enable_label = __( 'Enable CommentPress', 'cp-buddypress' );



				// off by default
				$has_workflow = false;

				// init output
				$workflow_html = '';

				// allow overrides
				$has_workflow = apply_filters( 'cp_blog_workflow_exists', $has_workflow );

				// if we have workflow enabled, by a plugin, say...
				if ( $has_workflow !== false ) {

					// define workflow label
					$workflow_label = __( 'Enable Custom Workflow', 'cp-buddypress' );

					// allow overrides
					$workflow_label = apply_filters( 'cp_blog_workflow_label', $workflow_label );

					// show it
					$workflow_html = '

					<div class="checkbox">
						<label for="cp_blog_workflow"><input type="checkbox" value="1" id="cp_blog_workflow" name="cp_blog_workflow" /> '.$workflow_label.'</label>
					</div>

					';

				}



				// assume no types
				$types = array();

				// init output
				$type_html = '';

				// but allow overrides for plugins to supply some
				$types = apply_filters( 'cp_blog_type_options', $types );

				// if we got any, use them
				if ( !empty( $types ) ) {

					// define blog type label
					$type_label = __( 'Document Type', 'cp-buddypress' );

					// allow overrides
					$type_label = apply_filters( 'cp_blog_type_label', $type_label );

					// construct options
					$type_option_list = array();
					$n = 0;
					foreach( $types AS $type ) {
						$type_option_list[] = '<option value="'.$n.'">'.$type.'</option>';
						$n++;
					}
					$type_options = implode( "\n", $type_option_list );

					// show it
					$type_html = '

					<div class="dropdown">
						<label for="cp_blog_type">'.$type_label.'</label> <select id="cp_blog_type" name="cp_blog_type">

						'.$type_options.'

						</select>
					</div>

					';

				}



				// construct form
				$form = '

				<br />
				<div id="cp-buddypress-options">

					<h3>'.$title.'</h3>

					<p>'.$text.'</p>

					<div class="checkbox">
						<label for="cpbp-groupblog"><input type="checkbox" value="1" id="cpbp-groupblog" name="cpbp-groupblog" /> '.$enable_label.'</label>
					</div>

					'.$workflow_html.'

					'.$type_html.'

				</div>

				';

				echo $form;

			}

		}

	}






	/**
	 * @description: hook into wpmu_new_blog and target plugins to be activated
	 * @todo:
	 *
	 */
	function wpmu_new_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {

		// test for presence of our checkbox variable in _POST
		if ( isset( $_POST['cpbp-groupblog'] ) AND $_POST['cpbp-groupblog'] == '1' ) {

			// get group id before switch
			$group_id = isset( $_COOKIE['bp_new_group_id'] )
						? $_COOKIE['bp_new_group_id']
						: bp_get_current_group_id();



			// wpmu_new_blog calls this *after* restore_current_blog, so we need to do it again
			switch_to_blog( $blog_id );



			// ----------------------
			// Activate CommentPress
			// ----------------------



			// get all themes
			$themes = get_themes();

			// get Commentpress theme by default, but allow overrides
			$target_theme = apply_filters(
				'cp_groupblog_theme_name',
				'Commentpress'
			);

			// the key is the theme name
			if ( isset( $themes[ $target_theme ] ) ) {

				// activate it
				switch_theme(
					$themes[ $target_theme ]['Template'],
					$themes[ $target_theme ]['Stylesheet']
				);

			}



			// get Commentpress plugin
			$path_to_plugin = $this->_find_plugin_by_name( 'Commentpress' );

			// if we got Commentpress...
			if ( false !== $path_to_plugin ) 	{

				// activate it in its buffered sandbox
				$this->_activate_plugin( $path_to_plugin, true );

				global $commentpress_obj, $wpdb;

				// post-activation configuration
				if ( is_null( $commentpress_obj ) ) {

					// create it
					$commentpress_obj = new CommentPress;

				}

				// install CP pages
				$commentpress_obj->db->create_special_pages();

				// TOC = posts
				$commentpress_obj->db->option_set( 'cp_show_posts_or_pages_in_toc', 'post' );

				// TOC show extended posts
				$commentpress_obj->db->option_set( 'cp_show_extended_toc', 1 );



				// check for (translation) workflow (checkbox)
				$cp_blog_workflow = 0;
				if ( isset( $_POST['cp_blog_workflow'] ) ) {
					// ensure boolean
					$cp_blog_workflow = ( $_POST['cp_blog_workflow'] == '1' ) ? 1 : 0;
				}

				// set workflow
				$commentpress_obj->db->option_set( 'cp_blog_workflow', $cp_blog_workflow );



				// database object
				global $wpdb;

				// check for blog type (dropdown)
				$cp_blog_type = 0;
				if ( isset( $_POST['cp_blog_type'] ) ) {
					$cp_blog_type = intval( $_POST['cp_blog_type'] );
				}

				// set blog type
				$commentpress_obj->db->option_set( 'cp_blog_type', $cp_blog_type );

				// did we get a group id before we switched blogs?
				if ( isset( $group_id ) ) {

					// allow plugins to override the blog type - for example if workflow is enabled,
					// it might become a new blog type as far as buddypress is concerned
					$_blog_type = apply_filters( 'cp_get_group_meta_for_blog_type', $cp_blog_type, $cp_blog_workflow );

					// set the type as group meta info
					// NOTE - we also need to change this when the type is changed from the CP admin page
					groups_update_groupmeta( $group_id, 'groupblogtype', 'groupblogtype-'.$_blog_type );

				}



				// save
				$commentpress_obj->db->options_save();

			}



			// get CP Ajaxified
			$path_to_plugin = $this->_find_plugin_by_name( 'Commentpress Ajaxified' );

			// if we got it...
			if ( false !== $path_to_plugin ) {

				// activate it in its buffered sandbox
				$this->_activate_plugin( $path_to_plugin, true );

			}



			// switch back
			restore_current_blog();

		}

	}






//#################################################################






	/*
	===============================================================
	PRIVATE METHODS
	===============================================================
	*/






	/**
	 * @description: object initialisation
	 * @todo:
	 *
	 */
	function _init() {

		// register hooks
		$this->_register_hooks();

	}






	/**
	 * @description: register Wordpress hooks
	 * @todo:
	 *
	 */
	function _register_hooks() {

		// enable translation
		add_action( 'init', array( &$this, 'translation' ) );

		// enable html comments and content for authors
		add_action( 'init', array( &$this, 'allow_html_content' ) );

		// amend comment activity
		add_filter( 'pre_comment_approved', array( &$this, 'pre_comment_approved' ), 20, 2 );

		// override "publicness" of groupblogs
		add_filter( 'bp_is_blog_public', array( &$this, 'is_blog_public' ), 20, 1 );

		// amend activity
		add_action( 'bp_loaded', array( &$this, '_groupblog_activity_mods' ), 30 );

		// get group avatar when listing groupblogs
		add_filter( 'bp_get_blog_avatar', array( &$this, 'get_blog_avatar' ), 20, 3 );

		// filter bp-groupblog defaults
		add_filter( 'bp_groupblog_subnav_item_name', array( &$this, 'filter_blog_name' ), 20 );
		add_filter( 'bp_groupblog_subnav_item_slug', array( &$this, 'filter_blog_slug' ), 20 );

		// override BP title of "visit site" button in blog lists
		add_filter( 'bp_get_blogs_visit_blog_button', array( &$this, 'get_blogs_visit_blog_button' ), 20 );

		// we can remove groupblogs from the blog list, but cannot update the total_blog_count_for_user
		// that is displayed on the tab *before* the blog list is built - hence filter disabled for now
		//add_filter( 'bp_has_blogs', array( &$this, 'remove_groupblog_from_loop' ), 20, 2 );

		// add form elements to groupblog form
		add_action( 'signup_blogform', array( &$this, 'signup_blogform' ) );

		// activate blog-specific CommentPress plugin
		add_action('wpmu_new_blog', array( &$this, 'wpmu_new_blog' ), 12, 6); // includes/ms-functions.php

		// register any public styles
		add_action('wp_enqueue_scripts', array( &$this, 'add_frontend_styles' ), 20);

		// is this the back end?
		if ( is_admin() ) {

			// if BP...
			if ( function_exists( 'bp_core_admin_hook' ) ) {

				// add menu to BuddyPress submenu
				add_action( bp_core_admin_hook(), array( &$this, 'add_admin_menu' ), 30 );

			}

		} else {

			// add filter options for the post and comment activities
			add_action( 'bp_include', array( &$this, '_groupblog_filter_options' ) );

		}

	}






	/**
	 * _groupblog_filter_options()
	 *
	 * Add a filter actions once BuddyPress is loaded.
	 */
	function _groupblog_filter_options() {

		// remove bp-groupblog's contradictory option
		remove_action( 'bp_group_activity_filter_options', 'bp_groupblog_posts' );

		// add our consistent one
		add_action( 'bp_activity_filter_options', array( &$this, 'groupblog_posts_filter_option' ) );
		add_action( 'bp_group_activity_filter_options', array( &$this, 'groupblog_posts_filter_option' ) );
		add_action( 'bp_member_activity_filter_options', array( &$this, 'groupblog_posts_filter_option' ) );

		// add our comments
		add_action( 'bp_activity_filter_options', array( &$this, 'groupblog_comments_filter_option' ) );
		add_action( 'bp_group_activity_filter_options', array( &$this, 'groupblog_comments_filter_option' ) );
		add_action( 'bp_member_activity_filter_options', array( &$this, 'groupblog_comments_filter_option' ) );

	}






	/**
	 * _groupblog_activity_mods()
	 *
	 * Amend Activity Methods once BuddyPress is loaded.
	 */
	function _groupblog_activity_mods() {

		// ditch bp-groupblog's post activity action
		remove_action( 'bp_activity_before_save', 'bp_groupblog_set_group_to_post_activity' );

		// add custom comment activity to bp-groupblog
		add_action( 'bp_activity_before_save', array( &$this, 'groupblog_custom_comment_activity' ), 20, 1 );

		// implement our own post activity
		add_action( 'bp_activity_before_save', array( &$this, 'groupblog_custom_post_activity' ), 20, 1 );

		// these don't seem to fire to allow us to add our meta values for the items...
		// instead, I'm trying to store the blog_type as group meta data
		//add_action( 'bp_activity_after_save', array( &$this, 'groupblog_custom_comment_meta' ), 20, 1 );
		//add_action( 'bp_activity_after_save', array( &$this, 'groupblog_custom_post_meta' ), 20, 1 );

	}






	/**
	 * @description: utility to wrap is_groupblog()
	 * @todo:
	 *
	 */
	function _is_commentpress_groupblog() {

		// check if this blog is a CP groupblog
		global $commentpress_obj;
		if (

			!is_null( $commentpress_obj )
			AND is_object( $commentpress_obj )
			AND $commentpress_obj->is_groupblog()

		) {

			return true;

		}

		return false;

	}






	/**
	 * @description: utility to get blog_type
	 * @todo:
	 *
	 */
	function _get_groupblog_type() {

		global $commentpress_obj;

		// if we have the plugin
		if (

			!is_null( $commentpress_obj )
			AND is_object( $commentpress_obj )

		) {

			// --<
			return $commentpress_obj->db->option_get( 'cp_blog_type' ) ;
		}



		// --<
		return false;

	}






	/**
	 * @description: get WP plugin reference by name (since we never know for sure what the enclosing
	 * directory is called)
	 * @todo:
	 *
	 */
	function _find_plugin_by_name( $plugin_name = '' ) {

		// kick out if no param supplied
		if ( $plugin_name == '' ) { return false; }



		// init path
		$path_to_plugin = false;

		// get plugins
		$plugins = get_plugins();
		//print_r( $plugins ); die();

		// because the key is the path to the plugin file, we have to find the
		// key by iterating over the values (which are arrays) to find the
		// plugin with the name Commentpress. Doh!
		foreach( $plugins AS $key => $plugin ) {

			// is it ours?
			if ( $plugin['Name'] == $plugin_name ) {

				// now get the key, which is our path
				$path_to_plugin = $key;
				break;

			}

		}



		// --<
		return $path_to_plugin;

	}





	/*
	--------------------------------------------------------------------------------
	Force a plugin to activate: adapted from https://gist.github.com/1966425
	Audited with reference to activate_plugin() with extra commenting inline
	--------------------------------------------------------------------------------
	*/

	/**
	 * @description: Helper to activate a plugin on another site without causing a
	 * fatal error by including the plugin file a second time
	 * Based on activate_plugin() in wp-admin/includes/plugin.php
	 * $buffer option is used for plugins which send output
	 * @todo:
	 *
	 */
	function _activate_plugin($plugin, $buffer = false) {

		// find our already active plugins
		$current = get_option('active_plugins', array());

		// no need to validate it...

		// check that the plugin isn't already active
		if ( !in_array($plugin, $current) ) {

			// no need to redirect...

			// open buffer if required
			if ($buffer) { ob_start(); }

			// safe include
			include_once( WP_PLUGIN_DIR . '/' . $plugin );

			// no need to check silent activation, just go ahead...
			do_action('activate_plugin', $plugin);
			do_action('activate_' . $plugin);

			// housekeeping
			$current[] = $plugin;
			sort($current);
			update_option('active_plugins', $current);
			do_action('activated_plugin', $plugin);

			// close buffer if required
			if ($buffer) { ob_end_clean(); }

		}

	}






//#################################################################






} // class ends






?>