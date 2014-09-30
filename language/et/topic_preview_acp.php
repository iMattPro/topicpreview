<?php
/**
*
* Topic Preview [Estonian]
*
* @copyright (c) 2013 Matt Friedman, (c) 2014 phpbbeesti.com
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
	'TOPIC_PREVIEW'					=> 'Teema eelvaade',
	'TOPIC_PREVIEW_EXPLAIN'			=> 'Teema eelvaade näitab lühikest sisu esimesest postitusest, hiirega minna teema pealkirja peale.',
	'TOPIC_PREVIEW_SETTINGS'		=> 'Teema eelvaate seaded',
	'TOPIC_PREVIEW_LENGTH'			=> 'Teema eelvaate teksti pikkus',
	'TOPIC_PREVIEW_LENGTH_EXPLAIN'	=> 'Sisesta sümbolite arv, kui pikalt näidatakse teemat eelvaates (vaikimisi on see 150 sümbolit). Väärtus 0, keelab selle funktsiooni.',
	'TOPIC_PREVIEW_STRIP'			=> 'BBkoodid peidetakse teema eelvaates',
	'TOPIC_PREVIEW_STRIP_EXPLAIN'	=> 'Sisesta nimekiri BBkoodidest, mida sa ei soovi näidata teema eelvaates (näiteks, spoilerid ja peidetud teksti BBkoodid). Eralda mitu BBkoodi kasutades | sümbolit, näiteks: spoiler|hide|code',
	'TOPIC_PREVIEW_AVATARS'			=> 'Näita avatare teema eelvaates',
	'TOPIC_PREVIEW_LAST_POST'		=> 'Näita “viimase postituse” teksti teema eelvaates',
	'CHARS'							=> 'Sümbolid',

	'TOPIC_PREVIEW_STYLE_SETTINGS'	=> 'Teema eelvaate stiili seaded',
	'TOPIC_PREVIEW_WIDTH'			=> 'Teema eelvaate laius (pikslites)',
	'TOPIC_PREVIEW_DELAY'			=> 'Viivitus enne postituse näitamist eelvaades (millisekundites)',
	'TOPIC_PREVIEW_DRIFT'			=> 'Animeeritud kõrvalekalde effekt (pikslites)',
	'TOPIC_PREVIEW_DRIFT_EXPLAIN'	=> 'Vertikaalse animatsiooni hääbumise väärtus (kasuta suuna muutmiseks negatiivseid väärtusi).',
	'TOPIC_PREVIEW_THEME'			=> 'Kujundus %s',
	'TOPIC_PREVIEW_THEME_EXPLAIN'	=> 'Vali teema eelvaate kujundus %s.',
	'THEME'							=> 'kujundus',
	'MILLISECOND'					=> 'ms',
));
