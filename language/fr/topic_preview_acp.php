<?php
/**
*
* Topic Preview [French]
* Translated by darky (http://www.foruminfopc.fr/) & Galixte (http://www.galixte.com)
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
	'TOPIC_PREVIEW'					=> 'Aperçu des sujets',
	'TOPIC_PREVIEW_EXPLAIN'			=> 'Affiche dans une infobulle une partie du texte du premier message du sujet, lorsque la souris survole son titre.',
	'TOPIC_PREVIEW_SETTINGS'		=> 'Paramètres de l’aperçu des sujets',
	'TOPIC_PREVIEW_LENGTH'			=> 'Longueur du texte de l’aperçu des sujets',
	'TOPIC_PREVIEW_LENGTH_EXPLAIN'	=> 'Saisissez le nombre de caractères à afficher dans l’infobulle (la valeur par défaut est de 150). Saisissez 0 pour désactiver cette fonctionnalité.',
	'TOPIC_PREVIEW_STRIP'			=> 'BBCode à cacher dans l’aperçu des sujets',
	'TOPIC_PREVIEW_STRIP_EXPLAIN'	=> 'Liste des BBCodes à supprimer de l’aperçu (les BBCodes de texte caché et de texte dévoilé par exemple). Listez les différents BBCodes séparés par le caractère « | ». Exemples : spoiler|hide|code',
	'TOPIC_PREVIEW_AVATARS'			=> 'Afficher l’avatar des utilisateurs',
	'TOPIC_PREVIEW_LAST_POST'		=> 'Afficher le texte du « dernier message »',
	'CHARS'							=> 'Caractères',

	'TOPIC_PREVIEW_STYLE_SETTINGS'	=> 'Paramètres de style de l’aperçu des sujets',
	'TOPIC_PREVIEW_WIDTH'			=> 'Largeur de l’aperçu des sujets (en pixels)',
	'TOPIC_PREVIEW_DELAY'			=> 'Délais d’affichage de l’aperçu des sujets (en millisecondes)',
	'TOPIC_PREVIEW_DRIFT'			=> 'Effet dérive animée (Animated Drift)',
	'TOPIC_PREVIEW_DRIFT_EXPLAIN'	=> 'Taille de l’animation verticale en pixels lors du fondu de sortie. Utilisez des valeurs positives pour un sens montant et négatives pour un sens descendant.',
	'TOPIC_PREVIEW_THEME'			=> 'Style pour %s',
	'TOPIC_PREVIEW_THEME_EXPLAIN'	=> 'Choisissez un style d’aperçu des sujets, pour %s.',
	'THEME'							=> 'style',
	'MILLISECOND'					=> 'ms',
));
