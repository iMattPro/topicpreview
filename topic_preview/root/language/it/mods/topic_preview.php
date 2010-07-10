<?php
/**
*
* topic_preview [Italian]
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

$lang = array_merge($lang, array(
	'TOPIC_PREVIEW'			=> 'Lunghezza dell’anteprima',
	'TOPIC_PREVIEW_EXPLAIN'	=> 'Mostrer‡ una porzione del topic quando il mouse passer‡ sopra il titolo del topic. Inserisci il numero di caratteri da mostrare (default is 150). Se metti 0 disabiliti questa funzione.',
	'CHARS'   				=> 'Caratteri',
	'DISPLAY_TOPIC_PREVIEW' => 'Mostra anteprima del topic',	
	'TOPIC_PREVIEW_STRIP'			=> 'BBCode da nascondere nell’anteprima',
	'TOPIC_PREVIEW_STRIP_EXPLAIN'	=> 'Lista dei BBCode con i contenuti da rimuovere dall’anteprima. Separa i BBCode multipli usando il carattere |, ad esempio: spoiler|hide|code',

	// Installation language vars (UMIL)
	'TP_MOD'	=> 'Anteprima dei Topic',
));

?>