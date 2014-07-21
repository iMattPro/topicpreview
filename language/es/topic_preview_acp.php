<?php
/**
*
* Topic Preview [Spanish]
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
	'TOPIC_PREVIEW'					=> 'Topic Preview',
	'TOPIC_PREVIEW_EXPLAIN'			=> 'Mostrará el primer texto de un tema en una descripción emergente mientras el ratón está sobre el título del tema.',
	'TOPIC_PREVIEW_SETTINGS'		=> 'Configuración de topic preview',
	'TOPIC_PREVIEW_LENGTH'			=> 'Longitud del texto de Topic Preview',
	'TOPIC_PREVIEW_LENGTH_EXPLAIN'	=> 'Introduce el número de caracteres que se mostrarán en la descripción (por defecto es 150). Ajustando el valor a 0 desactiva esta función.',
	'TOPIC_PREVIEW_STRIP'			=> 'BBCodes a ocultar en Topic Preview',
	'TOPIC_PREVIEW_STRIP_EXPLAIN'	=> 'Lista de BBCodes con contenido que desea eliminar de la vista previa (BBCodes spoiler y texto oculto, por ejemplo). Separar múltiples BBCodes usando el caracter |, por ejemplo: spoiler|hide|code',
	'TOPIC_PREVIEW_AVATARS'			=> 'Mostrar avatares en Topic Preview',
	'TOPIC_PREVIEW_LAST_POST'		=> 'Incluir texto del “Último mensaje” en Topic Preview',
	'CHARS'							=> 'Caracteres',

	'TOPIC_PREVIEW_STYLE_SETTINGS'	=> 'Configuración de estilo Topic Preview',
	'TOPIC_PREVIEW_WIDTH'			=> 'Ancho de Topic Preview (en píxeles)',
	'TOPIC_PREVIEW_DELAY'			=> 'Retraso antes de mostrar Topic Preview (en milisegundos)',
	'TOPIC_PREVIEW_DRIFT'			=> 'Efecto de la deriva de animación (en píxeles)',
	'TOPIC_PREVIEW_DRIFT_EXPLAIN'	=> 'Monto de la animación vertical en fadeout (usar valores negativos para cambiar de dirección).',
	'TOPIC_PREVIEW_THEME'			=> 'Tema para %s',
	'TOPIC_PREVIEW_THEME_EXPLAIN'	=> 'Seleccione un tema previsualización del tema de %s.',
	'THEME'							=> 'tema',
	'MILLISECOND'					=> 'ms',
));
