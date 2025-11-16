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

		// Parse the text using phpBB's text formatter
		$rendered = generate_text_for_display($text, '', '', 7);

		// Trim to character limit while preserving HTML structure
		return $this->safe_trim_html($rendered, $limit);
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

		// Remove each BBCode type individually
		$bbcodes = array_map('trim', explode(',', $strip_bbcodes));
		foreach ($bbcodes as $bbcode)
		{
			if (!empty($bbcode))
			{
				$text = $this->utils->remove_bbcode($text, $bbcode);
			}
		}

		return $text;
	}

	/**
	 * Safely trim HTML content to the character limit without breaking tags
	 *
	 * @param string $html  Rendered HTML content
	 * @param int    $limit Character limit
	 *
	 * @return string Trimmed HTML
	 */
	protected function safe_trim_html($html, $limit)
	{
		// Get text content without HTML tags for length calculation
		$text_content = strip_tags($html);

		if (utf8_strlen($text_content) <= $limit)
		{
			return $html;
		}

		// Use DOM if available, otherwise fallback to regex
		if (class_exists('DOMDocument') && extension_loaded('libxml'))
		{
			return $this->dom_trim_html($html, $limit);
		}

		return $this->regex_trim_html($html, $limit);
	}

	/**
	 * DOM-based HTML trimming (preferred method)
	 *
	 * @param string $html  Rendered HTML content
	 * @param int    $limit Character limit
	 *
	 * @return string Trimmed HTML
	 */
	protected function dom_trim_html($html, $limit)
	{
		$dom = new \DOMDocument('1.0', 'UTF-8');
		$dom->encoding = 'UTF-8';

		// Suppress warnings for malformed HTML and load with UTF-8 encoding
		libxml_use_internal_errors(true);
		$dom->loadHTML('<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body>' . $html . '</body></html>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
		libxml_clear_errors();

		$body = $dom->getElementsByTagName('body')->item(0);
		if ($body)
		{
			$this->trim_dom_node($body, $limit);

			$trimmed = '';
			foreach ($body->childNodes as $child)
			{
				$trimmed .= $dom->saveHTML($child);
			}
		}
		else
		{
			$trimmed = $html;
		}

		if (utf8_strlen(strip_tags($trimmed)) >= $limit)
		{
			$trimmed .= '...';
		}

		return $trimmed;
	}

	/**
	 * Regex-based HTML trimming (fallback method)
	 *
	 * @param string $html  Rendered HTML content
	 * @param int    $limit Character limit
	 *
	 * @return string Trimmed HTML
	 */
	protected function regex_trim_html($html, $limit)
	{
		// Simple fallback: strip tags, trim, then add basic formatting back
		$text = strip_tags($html);

		if (utf8_strlen($text) <= $limit)
		{
			return $html;
		}

		$trimmed = utf8_substr($text, 0, $limit);
		return htmlspecialchars($trimmed, ENT_COMPAT, 'UTF-8') . '...';
	}

	/**
	 * Recursively trim DOM nodes while preserving the structure
	 *
	 * @param \DOMNode $node         Current DOM node
	 * @param int      $limit        Character limit
	 * @param int      $current_len  Current character count
	 *
	 * @return int Updated character count
	 */
	protected function trim_dom_node(\DOMNode $node, $limit, $current_len = 0)
	{
		if ($current_len >= $limit)
		{
			return $current_len;
		}

		$nodes_to_remove = [];

		foreach ($node->childNodes as $child)
		{
			if ($current_len >= $limit)
			{
				$nodes_to_remove[] = $child;
				continue;
			}

			if ($child->nodeType === XML_TEXT_NODE)
			{
				$text = $child->nodeValue;
				$text_len = utf8_strlen($text);

				if ($current_len + $text_len > $limit)
				{
					// Trim text node to fit within the limit
					$remaining = $limit - $current_len;

					// Ensure we don't split multibyte characters
					$trimmed_text = utf8_substr($text, 0, $remaining);

					// Try to break at word boundary for a longer text
					if ($remaining < $text_len && $remaining > 20)
					{
						$last_space = utf8_strrpos($trimmed_text, ' ');
						if ($last_space !== false && $last_space > $remaining * 0.5)
						{
							$trimmed_text = utf8_substr($trimmed_text, 0, $last_space);
						}
					}

					$child->nodeValue = $trimmed_text;
					$current_len += utf8_strlen($trimmed_text);
				}
				else
				{
					$current_len += $text_len;
				}
			}
			else if ($child->nodeType === XML_ELEMENT_NODE)
			{
				$current_len = $this->trim_dom_node($child, $limit, $current_len);
			}
		}

		// Remove nodes that exceed the limit
		foreach ($nodes_to_remove as $node_to_remove)
		{
			$node->removeChild($node_to_remove);
		}

		return $current_len;
	}
}
