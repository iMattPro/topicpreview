<?php
/**
 *
 * Topic Preview
 *
 * @copyright (c) 2025 Matt Friedman
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace vse\topicpreview\core;

use phpbb\config\config;
use phpbb\textformatter\s9e\utils;

class renderer
{
	/** @var config */
	protected $config;

	/** @var utils */
	protected $utils;

	/**
	 * Constructor
	 *
	 * @param config $config Config object
	 * @param utils  $utils  Text formatter utils object
	 */
	public function __construct(config $config, utils $utils)
	{
		$this->config = $config;
		$this->utils = $utils;
	}

	/**
	 * Render and trim post-text for topic preview
	 *
	 * @param string $text   Raw post text from database
	 * @param int    $limit  Character limit for preview
	 *
	 * @return string Rendered and trimmed HTML
	 */
	public function render_text($text, $limit)
	{
		if (empty($text))
		{
			return '';
		}

		// Remove ignored BBCode tags and their content
		$text = $this->remove_ignored_bbcodes($text);

		// Get plain text for length checking
		$plain_text = $this->utils->clean_formatting($text);
		if (utf8_strlen($plain_text) <= $limit)
		{
			return generate_text_for_display($text, '', '', 7);
		}

		// Render and trim
		return $this->trim_html_content(generate_text_for_display($text, '', '', 7), $limit);
	}

	/**
	 * Remove BBCode tags that should be ignored in previews
	 *
	 * @param string $text Raw post text
	 *
	 * @return string Text with ignored BBCodes removed
	 */
	protected function remove_ignored_bbcodes($text)
	{
		$strip_bbcodes = $this->config['topic_preview_strip_bbcodes'] ?? '';
		if (empty($strip_bbcodes))
		{
			return $text;
		}

		$bbcodes = array_filter(array_map('trim', explode('|', $strip_bbcodes)));
		foreach ($bbcodes as $bbcode)
		{
			$text = $this->utils->remove_bbcode($text, $bbcode);
		}
		return $text;
	}

	/**
	 * Trim HTML content while preserving basic formatting
	 *
	 * @param string $html  Rendered HTML content
	 * @param int    $limit Character limit
	 *
	 * @return string Trimmed HTML
	 */
	protected function trim_html_content($html, $limit)
	{
		// Count text + images for proper length calculation
		$text_content = strip_tags($html);
		$total_length = utf8_strlen($text_content) + substr_count($html, '<img');

		if ($total_length <= $limit)
		{
			return $html;
		}

		// Find where to cut in the plain text
		$cut_pos = $limit;
		if ($limit > 20)
		{
			$last_space = utf8_strrpos(utf8_substr($text_content, 0, $limit), ' ');
			if ($last_space !== false && $last_space > $limit * 0.7)
			{
				$cut_pos = $last_space;
			}
		}

		// Use DOM to safely trim HTML
		if (class_exists('DOMDocument') && extension_loaded('libxml'))
		{
			return $this->dom_trim_html($html, $cut_pos);
		}

		// Fallback: simple text truncation
		$trimmed = utf8_substr($text_content, 0, $cut_pos);
		return htmlspecialchars($trimmed, ENT_COMPAT, 'UTF-8') . '...';
	}

	/**
	 * Use DOM to safely trim HTML content
	 *
	 * @param string $html  HTML content
	 * @param int    $limit Character limit
	 *
	 * @return string Trimmed HTML
	 */
	protected function dom_trim_html($html, $limit)
	{
		$dom = new \DOMDocument('1.0', 'UTF-8');
		libxml_use_internal_errors(true);

		// Wrap in div to ensure valid HTML structure
		if (!$dom->loadHTML('<div>' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD))
		{
			// If DOM fails, return plain text
			$text = strip_tags($html);
			return htmlspecialchars(utf8_substr($text, 0, $limit), ENT_COMPAT, 'UTF-8') . '...';
		}

		libxml_clear_errors();
		$div = $dom->documentElement;
		$this->trim_dom_text($div, $limit);

		$result = '';
		foreach ($div->childNodes as $child)
		{
			$result .= $dom->saveHTML($child);
		}
		return $result . '...';
	}

	/**
	 * Trim DOM text content to the limit
	 *
	 * @param \DOMNode $node  DOM node
	 * @param int      $limit Character limit
	 * @param int      $count Current character count
	 *
	 * @return int Updated character count
	 */
	protected function trim_dom_text(\DOMNode $node, $limit, $count = 0)
	{
		$nodes_to_remove = [];

		foreach ($node->childNodes as $child)
		{
			if ($count >= $limit)
			{
				$nodes_to_remove[] = $child;
				continue;
			}

			if ($child->nodeType === XML_TEXT_NODE)
			{
				$text = $child->nodeValue;
				$text_len = utf8_strlen($text);

				if ($count + $text_len > $limit)
				{
					$remaining = $limit - $count;
					$child->nodeValue = utf8_substr($text, 0, $remaining);
					$count = $limit;
				}
				else
				{
					$count += $text_len;
				}
			}
			else if ($child->nodeType === XML_ELEMENT_NODE)
			{
				// Count img tags (emojis/smilies) as 1 character each
				if ($child->nodeName === 'img')
				{
					if ($count + 1 > $limit)
					{
						$nodes_to_remove[] = $child;
						continue;
					}
					++$count;
				}
				else
				{
					$count = $this->trim_dom_text($child, $limit, $count);
				}
			}
		}

		foreach ($nodes_to_remove as $node_to_remove)
		{
			$node->removeChild($node_to_remove);
		}

		return $count;
	}
}
