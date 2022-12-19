<?php

/**
 * @package Custom Rank
 * @copyright (c) 2022 Daniel James
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace danieltj\customrank\event;

use phpbb\auth\auth;
use phpbb\db\driver\driver_interface as database;
use phpbb\request\request;
use phpbb\template\template;
use phpbb\user;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface {

	/**
	 * @var auth
	 */
	protected $auth;

	/**
	 * @var driver_interface
	 */
	protected $db;

	/**
	 * @var request
	 */
	protected $request;

	/**
	 * @var template
	 */
	protected $template;

	/**
	 * @var user
	 */
	protected $user;

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct( auth $auth, database $db, request $request, template $template, user $user ) {

		$this->auth = $auth;
		$this->db = $db;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;

	}

	/**
	 * RSVP to events.
	 */
	static public function getSubscribedEvents() {

		return [
			'core.user_setup'							=> 'add_languages',
			'core.permissions'							=> 'add_permissions',
			'core.acp_users_modify_profile'				=> 'acp_modify_profile',
			'core.acp_users_profile_modify_sql_ary'		=> 'acp_modify_sql_ary',
			'core.ucp_profile_modify_profile_info'		=> 'ucp_modify_profile',
			'core.ucp_profile_info_modify_sql_ary'		=> 'ucp_modify_sql_ary',
			'core.get_user_rank_after'					=> 'modify_user_rank'
		];

	}

	/**
	 * Let's talk.
	 */
	public function add_languages( $event ) {

		$lang_set_ext = $event[ 'lang_set_ext' ];

		$lang_set_ext[] = [
			'ext_name' => 'danieltj/customrank',
			'lang_set' => 'customrank',
		];

		$event[ 'lang_set_ext' ] = $lang_set_ext;

	}

	/**
	 * Register permissions.
	 */
	public function add_permissions( $event ) {

		$permissions = array_merge( $event[ 'permissions' ], [
			'u_custom_rank' => [
				'lang'	=> 'ACL_U_CUSTOM_RANK',
				'cat'	=> 'profile'
			]
		] );

		$event[ 'permissions' ] = $permissions;

	}

	/**
	 * Modify ACP data.
	 */
	public function acp_modify_profile( $event ) {

		$custom_rank = $this->request->variable( 'user_custom_rank', $event[ 'user_row' ][ 'user_custom_rank' ] );

		$event[ 'data' ] = array_merge( $event[ 'data' ], [
			'user_custom_rank' => $custom_rank
		] );

		$this->template->assign_vars( [
			'USER_CUSTOM_RANK'		=> $custom_rank
		] );

	}

	/**
	 * Modify ACP update query.
	 */
	public function acp_modify_sql_ary( $event ) {

		$event[ 'sql_ary' ] = array_merge( $event[ 'sql_ary' ], [
			'user_custom_rank' => $event[ 'data' ][ 'user_custom_rank' ],
		] );

	}

	/**
	 * Modify UCP data.
	 */
	public function ucp_modify_profile( $event ) {

		$custom_rank = $this->request->variable( 'user_custom_rank', $this->user->data['user_custom_rank'] );

		$event[ 'data' ] = array_merge( $event[ 'data' ], [
			'user_custom_rank' => $custom_rank
		] );

		$this->template->assign_vars( [
			'USER_CUSTOM_RANK'	=> $custom_rank,
			'U_CUSTOM_RANK'		=> ( $this->auth->acl_get( 'u_custom_rank' ) ) ? 1 : 0
		] );

	}

	/**
	 * Modify UCP update query.
	 */
	public function ucp_modify_sql_ary( $event ) {

		/**
		 * Check the user has permission to modify their rank
		 * title before altering the custom rank.
		 */
		if ( $this->auth->acl_get( 'u_custom_rank' ) ) {

			$event[ 'sql_ary' ] = array_merge( $event[ 'sql_ary' ], [
				'user_custom_rank' => $event[ 'data' ][ 'user_custom_rank' ],
			] );

		}

	}

	/**
	 * Filter the rank title.
	 */
	public function modify_user_rank( $event ) {

		/**
		 * Get the custom rank title directly from the
		 * database so we know it's up-to-date.
		 */
		$sql = 'SELECT * FROM ' . USERS_TABLE . ' WHERE ' . $this->db->sql_build_array( 'SELECT', [
			'user_id' => (int) $event[ 'user_data' ][ 'user_id' ]
		] );

		$result = $this->db->sql_query( $sql );

		$user = $this->db->sql_fetchrow( $result );

		$this->db->sql_freeresult( $result );

		$rank_data = $event[ 'user_rank_data' ];

		$rank_data[ 'title' ] = ( 0 < strlen( $user[ 'user_custom_rank' ] ) ) ? $user[ 'user_custom_rank' ] : $rank_data[ 'title' ];

		/**
		 * Just php being php.
		 */
		$event[ 'user_rank_data' ] = array_merge( $event[ 'user_rank_data' ], $rank_data );

	}

}
