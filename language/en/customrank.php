<?php

/**
 * @package Custom Rank
 * @copyright (c) 2022 Daniel James
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if ( ! defined( 'IN_PHPBB' ) ) {

	exit;

}

if ( empty( $lang ) || ! is_array( $lang ) ) {

	$lang = [];

}

$lang = array_merge( $lang, [
	'L_CUSTOM_RANK'			=> 'Custom rank',
	'L_CUSTOM_RANK_EXPLAIN'	=> 'Your custom rank title will overwrite your default rank title.'
] );
