<?php
/**
*
* Topic Preview [Italian]
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
	'TOPIC_PREVIEW'					=> 'Anteprima topic',
	'TOPIC_PREVIEW_EXPLAIN'			=> 'Mostra una porzione di un topic quando il mouse passa sul suo titolo.',
	'TOPIC_PREVIEW_SETTINGS'		=> 'Impostazioni anteprima topic',
	'TOPIC_PREVIEW_LENGTH'			=> 'Lunghezza anteprima',
	'TOPIC_PREVIEW_LENGTH_EXPLAIN'	=> 'Inserire il numero di caratteri da mostrare (150 è il valore standard). Se impostato a 0, la funzione è disabilitata.',
	'TOPIC_PREVIEW_STRIP'			=> 'BBCode da nascondere',
	'TOPIC_PREVIEW_STRIP_EXPLAIN'	=> 'Lista di BBCode con i contenuti da rimuovere nell’anteprima (ad esempio, il codice per lo spoiler o quello per il testo nascosto). Separare più BBCode usando il carattere | (esempio: spoiler|hide|code)',
	'TOPIC_PREVIEW_AVATARS'			=> 'Mostra avatar nelle anteprime topic',
	'TOPIC_PREVIEW_LAST_POST'		=> 'Includi il testo dell’ultimo post nelle anteprime topic',
	'CHARS'							=> 'Caratteri',

	'TOPIC_PREVIEW_STYLE_SETTINGS'	=> 'Impostazioni aspetto',
	'TOPIC_PREVIEW_WIDTH'			=> 'Larghezza anteprime (in pixel)',
	'TOPIC_PREVIEW_DELAY'			=> 'Ritardo comparsa anteprima topic (in millisecondi)',
	'TOPIC_PREVIEW_DRIFT'			=> 'Effetto scorrimento animato (in pixel)',
	'TOPIC_PREVIEW_DRIFT_EXPLAIN'	=> 'Quantità di animazione verticale per l’effetto scomparsa (per invertire la direzione usare valori negativi).',
	'TOPIC_PREVIEW_THEME'			=> 'Tema per %s',
	'TOPIC_PREVIEW_THEME_EXPLAIN'	=> 'Scegliere un tema per l’anteprima topic per %s.',
	'THEME'							=> 'tema',
	'MILLISECOND'					=> 'ms',
));
