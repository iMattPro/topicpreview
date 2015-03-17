<?php
/**
*
* Topic Preview [Croatian]
* Croatian translation by Ančica Sečan (http://ancica.sunceko.net)
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
	'TOPIC_PREVIEW'					=> 'Prikaz tema',
	'TOPIC_PREVIEW_EXPLAIN'			=> 'Ova ekstenzija prikazuje kratak izvadak teksta prvog posta teme u prikaznom balončiću prilikom prelaska mišem preko naslova tema.',
	'TOPIC_PREVIEW_SETTINGS'		=> 'Postavke',
	'TOPIC_PREVIEW_LENGTH'			=> 'Dužina prikaza teksta prvog posta teme',
	'TOPIC_PREVIEW_LENGTH_EXPLAIN'	=> 'Upiši broj koliko će znakova prvog posta teme biti prikazano u prikaznom balončiću prilikom prelaska mišem preko naslova tema [zadano=150, 0=onemogućeno].',
	'TOPIC_PREVIEW_STRIP'			=> 'Skrivanje prikaza BBkodova u prikazima tema',
	'TOPIC_PREVIEW_STRIP_EXPLAIN'	=> 'Ukoliko želiš onemogućiti prikaz [pojedinih] BBkodova, s pripadajućim im sadržajem [npr. <em>spoiler</em> i <em>skriven tekst</em>], u prikaznom balončiću, naniži kodove razdvajajući ih | znakom [npr. spoiler|hide|code].',
	'TOPIC_PREVIEW_AVATARS'			=> 'Prikaži avatare u prikazima tema',
	'TOPIC_PREVIEW_LAST_POST'		=> 'Prikaži (i) “Zadnji post” u prikazima tema',
	'CHARS'							=> 'znak(ov)a',

	'TOPIC_PREVIEW_STYLE_SETTINGS'	=> 'Postavke stila',
	'TOPIC_PREVIEW_WIDTH'			=> 'Širina prikaza tema (u pikselima)',
	'TOPIC_PREVIEW_DELAY'			=> 'Vremenski odmak prije prikazivanja prikaza tema (u milisekundama)',
	'TOPIC_PREVIEW_DRIFT'			=> 'Animirani <em>drift</em> effect (u pikselima)',
	'TOPIC_PREVIEW_DRIFT_EXPLAIN'	=> 'Vrijednost vertikalne animacije <em>fadeout</em> efekta [negativna vrijednost=suprotan smjer].',
	'TOPIC_PREVIEW_THEME'			=> 'Tema za %s',
	'TOPIC_PREVIEW_THEME_EXPLAIN'	=> 'Izaberi temu prikaza tema za %s.',
	'THEME'							=> 'tema',
	'MILLISECOND'					=> 'ms',
));
