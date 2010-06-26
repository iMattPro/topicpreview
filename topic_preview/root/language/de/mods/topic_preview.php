<?php
/**
*
* topic_preview [German]
*
* @package language
* @version $Id: topic_preview.php, 7 2010-06-13 23:47:11GMT VSE $
* @copyright (c) 2010 Matt Friedman
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
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

/**
* @author AmigoJack
*/
$lang = array_merge($lang, array(
	'TOPIC_PREVIEW'			=> 'Länge des Vorschautextes',
	'TOPIC_PREVIEW_EXPLAIN'	=> 'Wenn man mit der Maus über den Thementitel fährt wird im Browser angezeigt mit einem Textausschnitt des ersten Beitrags. Hiermit wird die Zeichenanzahl des Textes festgelegt (Standard sind 150). Die Angabe “0” deaktiviert den Vorschautext gänzlich.',
	'CHARS'   				=> 'Zeichen',
	'DISPLAY_TOPIC_PREVIEW' => 'Zeige Themenvorschau',	
	'TOPIC_PREVIEW_STRIP'			=> 'Aus Themenvorschau zu entfernende BBcodes',
	'TOPIC_PREVIEW_STRIP_EXPLAIN'	=> 'Aufzählung aller BBcodes, die (mitsamt deren Inhalt) nicht im Vorschautext erscheinen sollen (z.B. für Spoiler oder versteckte Texte). Mehrere BBcodes müssen mit “|” getrennt werden - Beispiel: “spoiler|hide|code”.',

	// Installation language vars (UMIL)
	'TP_MOD'	=> 'Topic Preview',
));

?>