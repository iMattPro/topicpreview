<?php
/**
*
* Topic Preview [German]
* Translated by AmigoJack and Outi / RasPiFun.de
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
	'TOPIC_PREVIEW_EXPLAIN'			=> 'Themenvorschau zeigt einen kurzen Auszug aus dem ersten Beitrag, wenn man mit der Maus über einen Thementitel fährt.',
	'TOPIC_PREVIEW_SETTINGS'		=> 'Einstellungen',
	'TOPIC_PREVIEW_LENGTH'			=> 'Länge des Textes der Themenvorschau',
	'TOPIC_PREVIEW_LENGTH_EXPLAIN'	=> 'Anzahl der Zeichen eingeben, die im Tooltip der Themenvorschau angezeigt werden sollen (Standardwert: 150). 0 deaktiviert diese Funktion.',
	'TOPIC_PREVIEW_STRIP'			=> 'BBCodes, die in Themenvorschauen ausgeblendet werden sollen',
	'TOPIC_PREVIEW_STRIP_EXPLAIN'	=> 'BBCodes auflisten, deren Inhalt aus der Vorschau entfernt werden soll (z.B. BBCodes für Spoiler und verborgenen Text). Mehrere BBCodes durch das Zeichen | trennen, z.B.: spoiler|hide|code',
	'TOPIC_PREVIEW_AVATARS'			=> 'Avatare in Themenvorschauen anzeigen',
	'TOPIC_PREVIEW_LAST_POST'		=> 'Text „Letzter Beitrag“ in Themenvorschauen anzeigen',
	'TOPIC_PREVIEW_RICH_TEXT'		=> 'Rich-Text-Vorschauen anzeigen',
	'TOPIC_PREVIEW_RICH_TEXT_EXPLAIN'=> 'In der Vorschau werden formatierte Inhalte mit vollständiger BBCode-Darstellung angezeigt. Ist diese Funktion deaktiviert, zeigt die Vorschau nur reinen Text an.',
	'TOPIC_PREVIEW_RICH_ATT'		=> 'Anhänge in Rich-Text-Vorschauen anzeigen',
	'TOPIC_PREVIEW_RICH_ATT_EXPLAIN'=> 'Anhänge in Rich-Text-Vorschauen anzeigen. Diese Option deaktivieren, wenn die Serverlast reduziert werden soll.',
	'CHARS'							=> 'Zeichen',

	'TOPIC_PREVIEW_STYLE_SETTINGS'	=> 'Designeinstellungen',
	'TOPIC_PREVIEW_WIDTH'			=> 'Breite der Themenvorschauen (in Pixeln)',
	'TOPIC_PREVIEW_DELAY'			=> 'Verzögerung vor der Anzeige von Themenvorschauen (in Millisekunden)',
	'TOPIC_PREVIEW_DRIFT'			=> 'Animierter Drift-Effekt (in Pixeln)',
	'TOPIC_PREVIEW_DRIFT_EXPLAIN'	=> 'Ausmaß der vertikalen Animation beim Ausblenden (negative Werte ändern die Richtung).',
	'TOPIC_PREVIEW_THEME'			=> 'Design für Theme %s',
	'TOPIC_PREVIEW_THEME_EXPLAIN'	=> 'Design für Themenvorschau im Theme %s auswählen.',
	'THEME'							=> 'Design',
	'MILLISECOND'					=> 'ms',
));
