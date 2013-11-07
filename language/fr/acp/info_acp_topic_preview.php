<?php
/**
*
* info_acp_topic_preview [French]
* 
* @package language
* @copyright (c) 2013 Matt Friedman - translator fr: (c) darky (http://www.foruminfopc.fr/)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
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
	'TOPIC_PREVIEW'					=> 'Aperçu des Sujets',
	'TOPIC_PREVIEW_EXPLAIN'			=> 'Affichera dans une info-bulle un peu de texte du premier message, lorsque la souris survole le titre du sujet. ',
	'TOPIC_PREVIEW_SETTINGS'		=> 'Topic preview settings',
	'TOPIC_PREVIEW_DISPLAY'			=> 'Afficher l’aperçu des sujets',
	'TOPIC_PREVIEW_LENGTH'			=> 'Longueur du texte de l’aperçu des sujets',
	'TOPIC_PREVIEW_LENGTH_EXPLAIN'	=> 'Saisissez le nombre de caractères à afficher dans l’info-bulle (la valeur par défaut est de 150). Saisissez 0 pour désactiver cette fonctionnalité.',
	'TOPIC_PREVIEW_STRIP'			=> 'BBCode à cacher dans l’aperçu des sujets',
	'TOPIC_PREVIEW_STRIP_EXPLAIN'	=> 'Liste des BBCodes de contenu à supprimer de l’aperçu (les BBCodes de texte caché et Spoiler par exemple). Séparez les différents BBCodes avec le caractère « | ». Exemples: spoiler|hide|code',
	'TOPIC_PREVIEW_AVATARS'			=> 'Afficher l’avatar des utilisateurs',
	'TOPIC_PREVIEW_LAST_POST'		=> 'Afficher le texte du « Dernier Message »',
	'CHARS'							=> 'Caractères',
));
