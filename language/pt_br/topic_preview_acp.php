<?php
/**
*
* Topic Preview [Pt-BR]
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
	'TOPIC_PREVIEW'					=> 'Preview dos Tópicos',
	'TOPIC_PREVIEW_EXPLAIN'			=> 'Mostra uma pré visualização do tópico como um balão de dicas do Windows.',
	'TOPIC_PREVIEW_SETTINGS'		=> 'Configurações',
	'TOPIC_PREVIEW_LENGTH'			=> 'Largura do Texto',
	'TOPIC_PREVIEW_LENGTH_EXPLAIN'	=> 'Informe o número padrão de caracteres (default é 150). Configure como 0 para desabilitar.',
	'TOPIC_PREVIEW_STRIP'			=> 'Esconder BBCodes nos previews',
	'TOPIC_PREVIEW_STRIP_EXPLAIN'	=> 'Listar BBCodes que devem ser removidos do preview. Separados por | , exemplo: spoiler|hide|code',
	'TOPIC_PREVIEW_AVATARS'			=> 'Exibir Avatares',
	'TOPIC_PREVIEW_LAST_POST'		=> 'Mostrar “Última Postagem”',
	'CHARS'							=> 'Caracteres',

	'TOPIC_PREVIEW_STYLE_SETTINGS'	=> 'Estilo',
	'TOPIC_PREVIEW_WIDTH'			=> 'Largura das visualizações do tópico (em pixels)',
	'TOPIC_PREVIEW_DELAY'			=> 'Delay (em milisegundos)',
	'TOPIC_PREVIEW_DRIFT'			=> 'Efeito animado (em pixels)',
	'TOPIC_PREVIEW_DRIFT_EXPLAIN'	=> 'Duração da animação',
	'TOPIC_PREVIEW_THEME'			=> 'Tema para %s',
	'TOPIC_PREVIEW_THEME_EXPLAIN'	=> 'Selecione um tema para %s.',
	'THEME'							=> 'informado',
	'MILLISECOND'					=> 'ms',
));
