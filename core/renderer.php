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
	 * @return string Rendered and trimmed HTML or plain text
	 */
	public function render_text($text, $limit)
	{
		if (empty($text))
		{
			return '';
		}

		// Remove ignored BBCode tags and their content
		$text = $this->remove_ignored_bbcodes($text);

		return $this->config['topic_preview_rich_text']
			? $this->render_rich_text($text, $limit)
			: $this->render_plain_text($text, $limit);
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
	 * Render plain text preview (no HTML formatting)
	 *
	 * @param string $text  Raw post text from database
	 * @param int    $limit Character limit for preview
	 *
	 * @return string Plain text preview
	 */
	protected function render_plain_text($text, $limit)
	{
		// Convert to plain text using unparse
		$plain_text = $this->utils->unparse($text);

		// Clean up remaining markup
		$patterns = [
			'#<!-- [lmw] --><a class="postlink[^>]*>(.*</a[^>]*>)?<!-- [lmw] -->#Usi', // Magic URLs
			'#<[a-zA-Z][^>]*>.*?</[a-zA-Z][^>]*>#Usi', // HTML tags (only valid tag names)
			'#\[/?[^]]+]#mi', // BBCode tags
			'#(http|https|ftp|mailto)(:|&\#58;)//\S+#i', // Remaining URLs
			'#[ \t]{2,}#' // Multiple spaces
		];
		$plain_text = trim(preg_replace($patterns, ' ', $plain_text));

		if (empty($plain_text))
		{
			return '';
		}

		// Normalize line breaks
		$plain_text = preg_replace('/(?:(?:\r\n|\r|\n)\s*){2}/', "\n\n", $plain_text);

		if (utf8_strlen($plain_text) <= $limit)
		{
			return nl2br(htmlspecialchars($plain_text, ENT_COMPAT, 'UTF-8'));
		}

		// Trim and remove partial words
		$trimmed = preg_replace('/\s+?(\S+)?$/', '', utf8_substr($plain_text, 0, $limit));

		return nl2br(htmlspecialchars($trimmed, ENT_COMPAT, 'UTF-8')) . '...';
	}

	/**
	 * Render rich text preview (HTML formatting)
	 *
	 * @param string $text  Raw post text from database
	 * @param int    $limit Character limit for preview
	 *
	 * @return string Rich HTML preview
	 */
	protected function render_rich_text($text, $limit)
	{
		// Get plain text for length checking
		$plain_text = $this->utils->clean_formatting($text);

		if (empty(trim($plain_text)))
		{
			return '';
		}

		$rendered_text = generate_text_for_display($text, '', '', 7);

		if (utf8_strlen($plain_text) <= $limit)
		{
			return $rendered_text;
		}

		// Render and trim
		return $this->trim_html_content($rendered_text, $limit);
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
		$dom->encoding = 'UTF-8';

		// Suppress warnings for malformed HTML and load with UTF-8 encoding
		libxml_use_internal_errors(true);
		$dom->loadHTML('<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body>' . $html . '</body></html>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
		libxml_clear_errors();

		$body = $dom->getElementsByTagName('body')->item(0);
		if ($body)
		{
			$this->trim_dom_text($body, $limit);

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
