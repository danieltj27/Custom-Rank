<?php

/**
 * @package Custom Rank
 * @copyright (c) 2022 Daniel James
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace danieltj\customrank\migrations;

class update_users_table extends \phpbb\db\migration\migration {

	/**
	 * Already installed?
	 */
	public function effectively_installed() {

		return $this->db_tools->sql_column_exists( $this->table_prefix . 'users', 'user_customrank' );

	}

	/**
	 * Needs 3.1.x.
	 */
	static public function depends_on() {

		return [ '\phpbb\db\migration\data\v31x\v314rc1' ];

	}

	/**
	 * Add permission.
	 */
	public function update_data() {

		return [
			[
				'permission.add', [ 'u_custom_rank' ]
			]
		];

	}

	/**
	 * Add new column.
	 */
	public function update_schema() {

		return [
			'add_columns' => [
				$this->table_prefix . 'users' => [
					'user_custom_rank'	=> [
						'VCHAR:255', '', 'after' => 'user_rank'
					]
				]
			]
		];

	}

	/**
	 * Delete new column.
	 */
	public function revert_schema() {

		return [
			'drop_columns' => [
				$this->table_prefix . 'users' => [
					'user_custom_rank'
				]
			]
		];

	}

}
