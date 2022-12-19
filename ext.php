<?php

/**
 * @package Custom Rank
 * @copyright (c) 2022 Daniel James
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace danieltj\customrank;

class ext extends \phpbb\extension\base {

	/**
	 * Check to see if at least phpBB 3.2 is used.
	 *
	 * @return boolean
	 */
	public function is_enableable() {

		$config = $this->container->get( 'config' );

		return phpbb_version_compare( $config[ 'version' ], '3.3', '>=' );

	}

}
