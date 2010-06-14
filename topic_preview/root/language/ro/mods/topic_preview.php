<?php
/**
*
* topic_preview [Română]
*
* @package language
* @version $Id: topic_preview.php, 7 2010-06-13 23:47:11GMT VSE $
* @copyright (c) 2010 Matt Friedman
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
* @traducere: Baros http://www.stoarcecreierul.ro/
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
	'TOPIC_PREVIEW'			=> 'Lungimea textului subiectelor previzualizate',
	'TOPIC_PREVIEW_EXPLAIN'	=> 'Va arăta o parte din textul primului subiect într-un tooltip în timp ce eşti cu mausul deasupra titlului subiectului. Introdu numărul de caractere ce vor fi afişate în tooltip (valoarea implicită este 150). Setarea acestei valori la 0 scoate din funcţiune această particularitate.',
	'CHARS'   				=> 'Caractere',
	'DISPLAY_TOPIC_PREVIEW' => 'Afişează previzualizările subiectelor',	
	'TOPIC_PREVIEW_STRIP'			=> 'Coduri BB de ascuns în previzualizarea subiectelor',
	'TOPIC_PREVIEW_STRIP_EXPLAIN'	=> 'Afişează codurile BB cu conţinutul pe care-l doreşti înlăturat din previzualizare (spoileri şi texte cod BB ascunse, spre exemplu). Separă codurile BB multiple folosind caracterul |, de exemplu: spoiler|ascunde|cod',

	// Installation language vars (UMIL)
	'TP_MOD'		=> 'Previzualizare Subiecte',
));

?>