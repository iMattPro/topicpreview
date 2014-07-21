<?php
/**
*
* Topic Preview [German]
* Translated by AmigoJack
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
	'TOPIC_PREVIEW'					=> 'Themenvorschau',
	'TOPIC_PREVIEW_EXPLAIN'			=> 'Fährt man mit der Maus über den Thementitel, wird im Browser ein Textausschnitt des ersten Beitrags angezeigt.',
	'TOPIC_PREVIEW_SETTINGS'		=> 'Einstellungen Themenvorschau',
	'TOPIC_PREVIEW_LENGTH'			=> 'Länge des Vorschautextes',
	'TOPIC_PREVIEW_LENGTH_EXPLAIN'	=> 'Maximal auszugebene Zeichen des Textes (Standard sind 150). Die Angabe “0” deaktiviert den Vorschautext gänzlich.',
	'TOPIC_PREVIEW_STRIP'			=> 'Aus Themenvorschau zu entfernende BBCodes',
	'TOPIC_PREVIEW_STRIP_EXPLAIN'	=> 'Aufzählung aller BBCodes, die (mitsamt deren Inhalt) nicht im Vorschautext erscheinen sollen (z.B. für Spoiler oder versteckte Texte). Mehrere BBCodes müssen mit “|” getrennt werden - Beispiel: “spoiler|hide|code”.',
	'TOPIC_PREVIEW_AVATARS'			=> 'Zeige Nutzeravatare (in jQuery-Themenvorschau)',
	'TOPIC_PREVIEW_LAST_POST'		=> 'Zeige “Letzter Beitrag”-Text (in jQuery-Themenvorschau)',
	'CHARS'							=> 'Zeichen',

	'TOPIC_PREVIEW_STYLE_SETTINGS'	=> 'Themenvorschau Style-Einstellungen',
	'TOPIC_PREVIEW_WIDTH'			=> 'Themenvorschau Breite (in Pixeln)',
	'TOPIC_PREVIEW_DELAY'			=> 'Verzögerung vor zeigt Vorschauen Thema (in Millisekunden)',
	'TOPIC_PREVIEW_DRIFT'			=> 'Animierte Drift Wirkung (in Pixeln)',
	'TOPIC_PREVIEW_DRIFT_EXPLAIN'	=> 'Anzahl der vertikalen Animation auf fadeout (negative Werte verwenden, um die Richtung zu ändern).',
	'TOPIC_PREVIEW_THEME'			=> 'Theme für %s',
	'TOPIC_PREVIEW_THEME_EXPLAIN'	=> 'Wählen Sie ein Thema für Themenvorschau %s.',
	'THEME'							=> 'Thema',
	'MILLISECOND'					=> 'ms',
));
