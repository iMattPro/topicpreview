<?php
/**
*
* Topic Preview [Hungarian]
*
* @copyright (c) 2013 Matt Friedman
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge($lang, array(
	'TOPIC_PREVIEW'					=> 'Topic Preview',
	'TOPIC_PREVIEW_EXPLAIN'			=> 'A Topic Preview rövid részletet mutat az első hozzászólásból, amikor az egérmutató egy téma címe fölött van.',
	'TOPIC_PREVIEW_SETTINGS'		=> 'Téma-előnézet beállításai',
	'TOPIC_PREVIEW_LENGTH'			=> 'Téma-előnézet szövegének hossza',
	'TOPIC_PREVIEW_LENGTH_EXPLAIN'	=> 'Írd be a téma-előnézeti buborékban megjelenítendő karakterek számát (az alapértelmezés 150). Ha 0-ra állítod, a funkció le lesz tiltva.',
	'TOPIC_PREVIEW_STRIP'			=> 'A téma-előnézetben elrejtendő BBCode-ok',
	'TOPIC_PREVIEW_STRIP_EXPLAIN'	=> 'Sorold fel azokat a BBCode-okat, melyeket el akarsz rejteni az előnézetben (pl. spoilerek és rejtett szövegek). A BBCode-okat a | karakterrel válaszd el egymástól, pl.: spoiler|hide|code',
	'TOPIC_PREVIEW_AVATARS'			=> 'Avatarok megjelenítése a téma-előnézetekben',
	'TOPIC_PREVIEW_LAST_POST'		=> 'Az "Utolsó hozzászólás" szövegének mutatása a téma-előnézetben',
	'CHARS'							=> 'Karakterek',

	'TOPIC_PREVIEW_STYLE_SETTINGS'	=> 'Téma-előnézet stílusbeállításai',
	'TOPIC_PREVIEW_WIDTH'			=> 'Téma-előnézet szélessége (pixelben)',
	'TOPIC_PREVIEW_DELAY'			=> 'Késleltetés a téma-előnézet megjelenése előtt (milliszekundumban)',
	'TOPIC_PREVIEW_DRIFT'			=> 'Animált sodródási hatás (pixelben)',
	'TOPIC_PREVIEW_DRIFT_EXPLAIN'	=> 'Függőleges animáció eltűnéskor (irányváltáshoz adj meg negatív értéket).',
	'TOPIC_PREVIEW_THEME'			=> 'Stílus ehhez: %s',
	'TOPIC_PREVIEW_THEME_EXPLAIN'	=> 'Válassz egy téma-előnézeti stílust ehhez: %s.',
	'THEME'							=> 'stílus',
	'MILLISECOND'					=> 'ms',
));
