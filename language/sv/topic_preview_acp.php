<?php
/**
*
* Topic Preview [Swedish]
* Swedish translation by Holger (http://www.maskinisten.net)
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
	'TOPIC_PREVIEW'					=> 'Ämnesöversikt',
	'TOPIC_PREVIEW_EXPLAIN'			=> 'Ämnesöversikten visar ett kort urdrag ur första inlägget i en tooltip när musen hålls över ämnesrubriken.',
	'TOPIC_PREVIEW_SETTINGS'		=> 'Inställningar för ämnesöversikten',
	'TOPIC_PREVIEW_LENGTH'			=> 'Ämnesöversiktens längd',
	'TOPIC_PREVIEW_LENGTH_EXPLAIN'	=> 'Ange antalet tecken som skall visas i ämnesöversikten (standard: 150). Deaktivera denna funktion genom att ange 0 (noll).',
	'TOPIC_PREVIEW_STRIP'			=> 'BBCodes som skall döljas i ämnesöversikten',
	'TOPIC_PREVIEW_STRIP_EXPLAIN'	=> 'Lista upp BBCodes med innehåll som du vill dölja i ämnesöversikten (t.ex. spoiler- och dolt-text-BBCodes). Separera flera BBCoder genom att använda tecknet |, t.ex.: spoiler|hide|code',
	'TOPIC_PREVIEW_AVATARS'			=> 'Visa avatarer i ämnesöversikten',
	'TOPIC_PREVIEW_LAST_POST'		=> 'Visa texten “Senaste inlägg” i ämnesöversikten',
	'CHARS'							=> 'Tecken',

	'TOPIC_PREVIEW_STYLE_SETTINGS'	=> 'Mallinställningar för ämnesöversikten',
	'TOPIC_PREVIEW_WIDTH'			=> 'Ämnesöversiktens bredd (i pixel)',
	'TOPIC_PREVIEW_DELAY'			=> 'Fördröjning av visning (i millisekunder)',
	'TOPIC_PREVIEW_DRIFT'			=> 'Animerad förskjutningseffekt (i pixel)',
	'TOPIC_PREVIEW_DRIFT_EXPLAIN'	=> 'Animerad förskjutningseffekt vid fadeout (ange negativ siffra för att ändra riktningen).',
	'TOPIC_PREVIEW_THEME'			=> 'Tema för %s',
	'TOPIC_PREVIEW_THEME_EXPLAIN'	=> 'Välj tema för ämnesöversikten för %s.',
	'THEME'							=> 'tema',
	'MILLISECOND'					=> 'ms',
));
