<?php
/**
*
* topic_preview [Spanish]
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
	'TOPIC_PREVIEW'			=> 'Longitud del texto de Topic Preview',
	'TOPIC_PREVIEW_EXPLAIN'	=> 'Mostrará el primer texto de un tema en una descripción emergente mientras el ratón está sobre el título del tema. Introduce el número de caracteres que se mostrarán en la descripción (por defecto es 150). Ajustando el valor a 0 desactiva esta función.',
	'CHARS'   				=> 'Caracteres',
	'DISPLAY_TOPIC_PREVIEW' => 'Mostrar previsualización del tema',	
	'TOPIC_PREVIEW_STRIP'			=> 'BBcodes a ocultar en Topic Preview',
	'TOPIC_PREVIEW_STRIP_EXPLAIN'	=> 'Lista de BBcodes con contenido que desea eliminar de la vista previa (BBcodes spoiler y texto oculto, por ejemplo). Separar múltiples BBcodes usando el caracter |, por ejemplo: spoiler|hide|code',

	// Installation language vars (UMIL)
	'TP_MOD'	=> 'Topic Preview',
));

?>