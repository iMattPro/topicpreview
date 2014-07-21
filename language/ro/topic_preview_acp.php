<?php
/**
*
* Topic Preview [Română]
* Translated by cybernet http://xDNS.ro
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
	'TOPIC_PREVIEW'					=> 'Previzualizare Subiect',
	'TOPIC_PREVIEW_EXPLAIN'			=> 'Va arăta o parte din textul primului subiect într-un tooltip în timp ce eşti cu mouse-ul deasupra titlului subiectului.',
	'TOPIC_PREVIEW_SETTINGS'		=> 'Setări pentru previzualizare subiecte',
	'TOPIC_PREVIEW_LENGTH'			=> 'Lungimea textului pentru subiectele previzualizate',
	'TOPIC_PREVIEW_LENGTH_EXPLAIN'	=> 'Introdu numărul de caractere ce vor fi afişate în tooltip ( valoarea implicită este 150 ). Setarea acestei valori la 0 scoate din funcţiune această particularitate.',
	'TOPIC_PREVIEW_STRIP'			=> 'Coduri BB de ascuns în previzualizarea subiectelor',
	'TOPIC_PREVIEW_STRIP_EXPLAIN'	=> 'Afişează codurile BB cu conţinutul pe care-l doreşti înlăturat din previzualizare ( spoiler şi texte cod BB ascunse, spre exemplu ). Separă codurile BB multiple folosind caracterul |, de exemplu: spoiler|ascunde|cod',
	'TOPIC_PREVIEW_AVATARS'			=> 'Afişează avatars in previzualizarea subiectelor',
	'TOPIC_PREVIEW_LAST_POST'		=> 'Include "Ultimul mesaj", in previzualizare',
	'CHARS'							=> 'Caractere',

	'TOPIC_PREVIEW_STYLE_SETTINGS'	=> 'Setări de stil pentru previzualizarea subiectului',
	'TOPIC_PREVIEW_WIDTH'			=> 'Lăţimea previzuălizarii ( în pixeli )',
	'TOPIC_PREVIEW_DELAY'			=> 'Amână afisarea previzualizării ( în milisecunde )',
	'TOPIC_PREVIEW_DRIFT'			=> 'Efect derivat animat ( in pixeli )',
	'TOPIC_PREVIEW_DRIFT_EXPLAIN'	=> 'Cantitatea de animaţie verticală ( foloseşte valori negative pentru a schimba direcţia ).',
	'TOPIC_PREVIEW_THEME'			=> 'Stil pentru %s',
	'TOPIC_PREVIEW_THEME_EXPLAIN'	=> 'Alege un stil pentru previzualizarea subiectului %s.',
	'THEME'							=> 'Stil',
	'MILLISECOND'					=> 'ms',
));
