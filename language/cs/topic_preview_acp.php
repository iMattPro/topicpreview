<?php
/**
*
* Topic Preview [Čeština]
* Pro nahlášení chyb v překladu a podporu překladu použijte stránky http://www.skolaponetu.cz. Děkujeme
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
	'TOPIC_PREVIEW'					=> 'Úryvek z tématu',
	'TOPIC_PREVIEW_EXPLAIN'			=> 'Náhled tématu zobrazí krátký úryvek prvního příspěvku po najetí myší na název tématu.',
	'TOPIC_PREVIEW_SETTINGS'		=> 'Nastavení náhledu tématu',
	'TOPIC_PREVIEW_LENGTH'			=> 'Úryvek z tématu',
	'TOPIC_PREVIEW_LENGTH_EXPLAIN'	=> 'Zde máte možnost zadat počet znaků zobrazených v úryvku z tématu.<br><strong>Zadáním 0 funkci vypnete.</strong>',
	'TOPIC_PREVIEW_STRIP'			=> 'Skryté BBkódy',
	'TOPIC_PREVIEW_STRIP_EXPLAIN'	=> 'Zde máte možnost zadat BBkódy, které v úryvku nebudou zobrazeny. Pro oddělení využijte znak |.<br><i>Například: list|code|quote.</i>',
	'TOPIC_PREVIEW_AVATARS'			=> 'Zobrazit avatary v náhledech témat',
	'TOPIC_PREVIEW_LAST_POST'		=> 'Zobrazit text „Poslední příspěvek“ v náhledech témat',
	'TOPIC_PREVIEW_RICH_TEXT'		=> 'Zobrazit náhledy s formátovaným textem',
	'TOPIC_PREVIEW_RICH_TEXT_EXPLAIN'=> 'Náhledy zobrazí formátovaný obsah s úplným zpracováním BBCode. Pokud je tato možnost vypnuta, náhledy zobrazí pouze prostý text.',
	'TOPIC_PREVIEW_RICH_ATT'		=> 'Zobrazit přílohy v náhledech s formátovaným textem',
	'TOPIC_PREVIEW_RICH_ATT_EXPLAIN'=> 'Zobrazí přiložené soubory v náhledech s formátovaným textem. Vypněte tuto možnost, chcete-li snížit zatížení serveru.',
	'CHARS'							=> 'Počet znaků',

	'TOPIC_PREVIEW_STYLE_SETTINGS'	=> 'Nastavení vzhledu náhledu tématu',
	'TOPIC_PREVIEW_WIDTH'			=> 'Šířka náhledu tématu (v pixelech)',
	'TOPIC_PREVIEW_DELAY'			=> 'Prodleva před zobrazením náhledu tématu (v milisekundách)',
	'TOPIC_PREVIEW_DRIFT'			=> 'Animovaný efekt posunu (v pixelech)',
	'TOPIC_PREVIEW_DRIFT_EXPLAIN'	=> 'Velikost svislé animace při mizení (záporné hodnoty změní směr).',
	'TOPIC_PREVIEW_THEME'			=> 'Motiv pro %s',
	'TOPIC_PREVIEW_THEME_EXPLAIN'	=> 'Vyberte motiv náhledu tématu pro %s.',
	'THEME'							=> 'motiv',
	'MILLISECOND'					=> 'ms',
));
