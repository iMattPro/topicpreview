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

use phpbb\textformatter\s9e\utils;

class renderer
{
	/** Regex matching ATTACHMENT XML tags with filename and index */
	public const ATTACHMENT_PATTERN = '/<ATTACHMENT\b(?=[^>]*\bfilename="([^"]+)")(?=[^>]*\bindex="(\d+)")[^>]*>/';

	/** @var string[] Parsed tags rendered as a single visual unit */
	protected const VISUAL_TAGS = ['ATTACHMENT', 'E', 'EMOJI', 'FLASH', 'IMG', 'MEDIA'];

	/** @var string[] Parsed tags whose rendered text comes from an attribute */
	protected const TEXT_ATTRIBUTES = ['LINK_TEXT' => 'text'];

	/** @var utils */
	protected $utils;

	/**
	 * Constructor
	 *
	 * @param utils  $utils  Text formatter utils object
	 */
	public function __construct(utils $utils)
	{
		$this->utils = $utils;
	}

	/**
	 * Render and trim post-text for topic preview
	 *
	 * @param string $text          Raw post text from database
	 * @param int    $limit         Character limit for preview
	 * @param string $strip_bbcodes String of BBCodes to remove, pipe delimited
	 * @param bool   $rich_text     True to use rich text rendering, false for plain text rendering
	 * @param bool   $theme         True if a topic preview theme is set, false if no theme is set
	 * @param array  $attachments   Array of attachment data
	 * @param int    $forum_id      Forum ID for attachment parsing
	 * @return string Rendered and trimmed HTML or plain text
	 */
	public function render_text($text, $limit, $strip_bbcodes, $rich_text, $theme, $attachments = [], $forum_id = 0)
	{
		if (empty($text))
		{
			return '';
		}

		// Get all attachment XML indices and those to be excluded
		$attachment_info = !empty($attachments) ? $this->get_attachment_info($text, $strip_bbcodes) : [];

		$text = $this->remove_ignored_bbcodes($text, $strip_bbcodes);

		return $rich_text && $theme
			? $this->render_rich_text($text, $limit, $attachments, $forum_id, $attachment_info)
			: $this->render_plain_text($text, $limit);
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
		$plain_text = censor_text($plain_text);

		if (empty($plain_text))
		{
			return '';
		}

		// Normalize line breaks
		$plain_text = preg_replace('/(?:(?:\r\n|\r|\n)\s*){2}/', "\n\n", $plain_text);

		if (utf8_strlen($plain_text) <= $limit)
		{
			return nl2br(utf8_htmlspecialchars($plain_text));
		}

		// Trim and remove partial words
		$trimmed = preg_replace('/\s+?(\S+)?$/', '', utf8_substr($plain_text, 0, $limit));

		return nl2br(utf8_htmlspecialchars($trimmed)) . '...';
	}

	/**
	 * Render rich text preview (HTML formatting)
	 *
	 * @param string $text  Raw post text from database
	 * @param int    $limit Character limit for preview
	 * @param array  $attachments Array of attachment data
	 * @param int    $forum_id Forum ID for attachment parsing
	 * @param array  $attachment_info Attachment information including mapping
	 *
	 * @return string Rich HTML preview
	 */
	protected function render_rich_text($text, $limit, $attachments = [], $forum_id = 0, $attachment_info = [])
	{
		$was_trimmed = false;
		$is_parsed = false;
		$was_censored = false;
		$text = $this->trim_parsed_content($text, $limit, $was_trimmed, $is_parsed, $was_censored);
		if (!$is_parsed)
		{
			// Legacy or malformed stored text cannot be trimmed safely as rich markup.
			return $this->render_plain_text($text, $limit);
		}

		if (empty(trim($this->utils->clean_formatting($text))))
		{
			return '';
		}

		if (!empty($attachment_info['all_attachments']))
		{
			$attachment_info = $this->update_attachment_info_after_trim($text, $attachment_info);
		}

		// Filter out attachments that were inside stripped or hidden BBCodes
		$excluded_xml_indices = $attachment_info['excluded_xml_indices'] ?? [];
		$all_attachments = $attachment_info['all_attachments'] ?? [];
		$xml_to_array_map = $attachment_info['xml_to_array_map'] ?? [];

		$rendered_text = generate_text_for_display($text, '', '', 7, !$was_censored);

		if (!empty($all_attachments))
		{
			$rendered_xml_indices = [];
			if (preg_match_all('#<!-- ia(\d+) -->#', $rendered_text, $matches))
			{
				$rendered_xml_indices = array_unique(array_map('intval', $matches[1]));
			}

			foreach ($all_attachments as $xml_index => $filename)
			{
				if (!in_array($xml_index, $rendered_xml_indices, true))
				{
					$excluded_xml_indices[] = $xml_index;
				}
			}

			$excluded_xml_indices = array_unique($excluded_xml_indices);

			$new_array_index = 0;
			$xml_to_array_map = [];
			foreach ($all_attachments as $xml_index => $filename)
			{
				if (!in_array($xml_index, $excluded_xml_indices, true))
				{
					$xml_to_array_map[$xml_index] = $new_array_index++;
				}
			}
		}

		if (!empty($all_attachments) && !empty($attachments))
		{
			$source_attachments = $attachments;
			$ordered_inline_attachments = [];
			$used_attachment_indexes = [];
			$excluded_attachment_indexes = [];

			foreach ($excluded_xml_indices as $xml_index)
			{
				$attachment_index = $this->find_attachment_index($xml_index, $all_attachments[$xml_index] ?? '', $source_attachments, $excluded_attachment_indexes);
				if ($attachment_index !== null)
				{
					$excluded_attachment_indexes[] = $attachment_index;
				}
			}

			foreach ($xml_to_array_map as $xml_index => $new_index)
			{
				$attachment_index = $this->find_attachment_index($xml_index, $all_attachments[$xml_index] ?? '', $source_attachments, array_merge($used_attachment_indexes, $excluded_attachment_indexes));
				if ($attachment_index !== null)
				{
					$ordered_inline_attachments[$new_index] = $source_attachments[$attachment_index];
					$used_attachment_indexes[] = $attachment_index;
				}
			}

			ksort($ordered_inline_attachments);
			$attachments = $ordered_inline_attachments;
			foreach ($source_attachments as $index => $attachment)
			{
				if (!in_array($index, $used_attachment_indexes, true) && !in_array($index, $excluded_attachment_indexes, true))
				{
					$attachments[] = $attachment;
				}
			}
		}

		// Remove markers for excluded attachments and renumber remaining markers
		if (!empty($excluded_xml_indices))
		{
			foreach ($excluded_xml_indices as $xml_index)
			{
				// Remove inline attachment markers
				$rendered_text = preg_replace('#<div class="inline-attachment"><!-- ia' . $xml_index . ' -->.*?<!-- ia' . $xml_index . ' --></div>#s', '', $rendered_text);
			}
		}

		// Renumber remaining markers to match the re-indexed attachments array
		if (!empty($xml_to_array_map) && preg_match('#<!-- ia(\d+) -->#', $rendered_text))
		{
			$rendered_text = preg_replace_callback('#<!-- ia(\d+) -->#', static function ($match) use ($xml_to_array_map) {
				$xml_index = (int) $match[1];

				return isset($xml_to_array_map[$xml_index]) ? '<!-- ia' . $xml_to_array_map[$xml_index] . ' -->' : $match[0];
			}, $rendered_text);
		}

		// Parse attachments after text rendering
		if (!empty($attachments))
		{
			$update_count = [];
			parse_attachments($forum_id, $rendered_text, $attachments, $update_count);

			// Detached attachments appear at the end of a post, after the preview
			// cutoff. Keep them only when the post text was not truncated.
			if (!$was_trimmed)
			{
				foreach ($attachments as $attachment)
				{
					if (!empty($attachment))
					{
						$rendered_text .= $attachment;
					}
				}
			}
		}

		return $rendered_text;
	}

	/**
	 * Remove BBCode tags and their content that should be ignored in previews
	 *
	 * @param string $text Raw post text
	 * @param string $strip_bbcodes String of BBCodes to remove, pipe delimited
	 *
	 * @return string Text with ignored BBCodes removed
	 */
	protected function remove_ignored_bbcodes($text, $strip_bbcodes)
	{
		if (empty($strip_bbcodes))
		{
			return $text;
		}

		$stripped_text = $text;
		$bbcodes = array_filter(array_map('trim', explode('|', $strip_bbcodes)));
		foreach ($bbcodes as $bbcode)
		{
			$stripped_text = $this->utils->remove_bbcode($stripped_text, $bbcode);
		}
		return $stripped_text !== $text ? preg_replace('/\s+/', ' ', $stripped_text) : $text;
	}

	/**
	 * Trim parsed post content before phpBB renders it as HTML
	 *
	 * @param string $text Parsed post text
	 * @param int    $limit Character limit
	 * @param bool   $was_trimmed Whether content was removed
	 * @param bool   $is_parsed Whether text was valid parsed XML
	 * @param bool   $was_censored Whether post content was censored before trimming
	 *
	 * @return string Trimmed parsed post text
	 */
	protected function trim_parsed_content($text, $limit, &$was_trimmed, &$is_parsed, &$was_censored = false)
	{
		$was_trimmed = false;
		$was_censored = false;
		$is_parsed = preg_match('#^<[rt][ >]#', $text) === 1;
		if (!$is_parsed)
		{
			return $text;
		}

		$limit = max(0, (int) $limit);
		// Parsed XML includes content plus its markup, so content cannot exceed this
		// length. Avoid building a DOM for short posts that cannot need trimming.
		if (utf8_strlen($text) <= $limit && censor_text($text) === $text)
		{
			return $text;
		}

		$dom = new \DOMDocument('1.0', 'UTF-8');
		$use_internal_errors = libxml_use_internal_errors(true);
		try
		{
			$is_parsed = $dom->loadXML($text, LIBXML_COMPACT | LIBXML_PARSEHUGE) !== false;
		}
		finally
		{
			libxml_clear_errors();
			libxml_use_internal_errors($use_internal_errors);
		}

		$root = $dom->documentElement;
		if (!$is_parsed || !($root instanceof \DOMElement))
		{
			$is_parsed = false;
			return $text;
		}

		$this->censor_parsed_content($root, $was_censored);
		$content = $this->get_parsed_content_text($root);
		if (utf8_strlen($content) <= $limit)
		{
			// Nothing is being trimmed, so let phpBB apply its normal complete
			// censoring pass during rendering.
			$was_censored = false;
			return $text;
		}

		$limit = $this->get_word_boundary_limit($root, $content, $limit);
		$last_content_node = null;
		$this->trim_parsed_node($root, $limit, 0, $was_trimmed, $last_content_node);
		$was_trimmed = true;
		if ($last_content_node instanceof \DOMText)
		{
			$last_content_node->nodeValue .= '...';
		}
		else if ($last_content_node instanceof \DOMNode && $last_content_node->parentNode)
		{
			$last_content_node->parentNode->insertBefore($dom->createTextNode('...'), $last_content_node->nextSibling);
		}
		else
		{
			$root->insertBefore($dom->createTextNode('...'), $root->firstChild);
		}

		return $dom->saveXML($root);
	}

	/**
	 * Censor semantic post content before applying the character budget
	 *
	 * @param \DOMNode $node Current parsed node
	 * @param bool     $was_censored Whether any content was replaced
	 */
	protected function censor_parsed_content(\DOMNode $node, &$was_censored)
	{
		foreach ($node->childNodes as $child)
		{
			if ($child instanceof \DOMText)
			{
				$censored = censor_text($child->nodeValue);
				if ($censored !== $child->nodeValue)
				{
					$child->nodeValue = $censored;
					$was_censored = true;
				}
			}
			else if ($child instanceof \DOMElement && $child->nodeName !== 's' && $child->nodeName !== 'e' && !in_array($child->nodeName, self::VISUAL_TAGS, true))
			{
				foreach ($child->attributes as $attribute)
				{
					$censored = censor_text($attribute->nodeValue);
					if ($censored !== $attribute->nodeValue)
					{
						$attribute->nodeValue = $censored;
						$was_censored = true;
					}
				}

				if (isset(self::TEXT_ATTRIBUTES[$child->nodeName]))
				{
					continue;
				}

				$this->censor_parsed_content($child, $was_censored);
			}
		}
	}

	/**
	 * Get semantic post content without parser markers or rendered HTML labels
	 *
	 * @param \DOMNode $node Current parsed node
	 *
	 * @return string Semantic post content
	 */
	protected function get_parsed_content_text(\DOMNode $node)
	{
		$content = '';
		foreach ($node->childNodes as $child)
		{
			if ($child instanceof \DOMText)
			{
				$content .= $child->nodeValue;
			}
			else if ($child instanceof \DOMElement && $child->nodeName !== 's' && $child->nodeName !== 'e')
			{
				if (isset(self::TEXT_ATTRIBUTES[$child->nodeName]))
				{
					$content .= $child->getAttribute(self::TEXT_ATTRIBUTES[$child->nodeName]);
				}
				else
				{
					$content .= in_array($child->nodeName, self::VISUAL_TAGS, true)
						? '#'
						: $this->get_parsed_content_text($child);
				}
			}
		}

		return $content;
	}

	/**
	 * Move a cutoff to a nearby word boundary
	 *
	 * @param \DOMNode $root Parsed post root
	 * @param string   $content Semantic post content
	 * @param int      $limit Maximum character count
	 *
	 * @return int Character count at which content should be cut
	 */
	protected function get_word_boundary_limit(\DOMNode $root, $content, $limit)
	{
		if ($limit <= 20)
		{
			return $limit;
		}

		// If omitted content starts with whitespace, limit already ends a word.
		if (preg_match('/^\s/u', utf8_substr($content, $limit, 1)))
		{
			return $limit;
		}

		$boundary = -1;
		$prefix = utf8_substr($content, 0, $limit);
		if (preg_match('/\s+\S*$/u', $prefix, $match, PREG_OFFSET_CAPTURE))
		{
			$boundary = utf8_strlen(substr($prefix, 0, $match[0][1]));
		}

		$offset = 0;
		$this->get_structural_word_boundary($root, $offset, $limit, $boundary);

		return $boundary > $limit * 0.7 ? $boundary : $limit;
	}

	/**
	 * Find latest zero-width word boundary created by list-item structure
	 *
	 * @param \DOMNode $node Current parsed node
	 * @param int      $offset Current semantic-content offset
	 * @param int      $limit Maximum character count
	 * @param int      $boundary Latest boundary at or before limit
	 */
	protected function get_structural_word_boundary(\DOMNode $node, &$offset, $limit, &$boundary)
	{
		foreach ($node->childNodes as $child)
		{
			if ($child instanceof \DOMText)
			{
				$offset += utf8_strlen($child->nodeValue);
			}
			else if ($child instanceof \DOMElement && $child->nodeName !== 's' && $child->nodeName !== 'e')
			{
				if ($child->nodeName === 'LI' && $offset <= $limit)
				{
					$boundary = max($boundary, $offset);
				}

				if (isset(self::TEXT_ATTRIBUTES[$child->nodeName]))
				{
					$offset += utf8_strlen($child->getAttribute(self::TEXT_ATTRIBUTES[$child->nodeName]));
				}
				else if (in_array($child->nodeName, self::VISUAL_TAGS, true))
				{
					++$offset;
				}
				else
				{
					$this->get_structural_word_boundary($child, $offset, $limit, $boundary);
				}

				if ($child->nodeName === 'LI' && $offset <= $limit)
				{
					$boundary = max($boundary, $offset);
				}
			}
		}
	}

	/**
	 * Recursively apply a content-character budget to parsed post XML
	 *
	 * @param \DOMNode $node Current parsed node
	 * @param int      $limit Character limit
	 * @param int      $count Current content count
	 * @param bool     $was_trimmed Whether content was removed
	 * @param \DOMNode|null $last_content_node Last retained content node
	 *
	 * @return int Updated content count
	 */
	protected function trim_parsed_node(\DOMNode $node, $limit, $count, &$was_trimmed, &$last_content_node)
	{
		$nodes_to_remove = [];

		foreach ($node->childNodes as $child)
		{
			// phpBB parser source markers are formatting metadata, not content.
			if ($child instanceof \DOMElement && ($child->nodeName === 's' || $child->nodeName === 'e'))
			{
				continue;
			}

			if ($count >= $limit)
			{
				$nodes_to_remove[] = $child;
				$was_trimmed = true;
				continue;
			}

			if ($child instanceof \DOMText)
			{
				$text = $child->nodeValue;
				$text_length = utf8_strlen($text);
				if ($count + $text_length > $limit)
				{
					$remaining = $limit - $count;
					$child->nodeValue = utf8_substr($text, 0, $remaining);
					$count = $limit;
					$was_trimmed = true;
				}
				else
				{
					$count += $text_length;
				}

				if ($text_length > 0)
				{
					$last_content_node = $child;
				}
			}
			else if ($child instanceof \DOMElement)
			{
				if (isset(self::TEXT_ATTRIBUTES[$child->nodeName]))
				{
					$attribute = self::TEXT_ATTRIBUTES[$child->nodeName];
					$text = $child->getAttribute($attribute);
					$text_length = utf8_strlen($text);
					if ($count + $text_length > $limit)
					{
						$child->setAttribute($attribute, utf8_substr($text, 0, $limit - $count));
						$count = $limit;
						$was_trimmed = true;
					}
					else
					{
						$count += $text_length;
					}

					if ($text_length > 0)
					{
						$last_content_node = $child;
					}
				}
				else if (in_array($child->nodeName, self::VISUAL_TAGS, true))
				{
					++$count;
					$last_content_node = $child;
				}
				else
				{
					$count = $this->trim_parsed_node($child, $limit, $count, $was_trimmed, $last_content_node);
				}
			}
		}

		foreach ($nodes_to_remove as $node_to_remove)
		{
			$node->removeChild($node_to_remove);
		}

		return $count;
	}

	/**
	 * Get comprehensive attachment information from text
	 *
	 * @param string $text Raw post text
	 * @param string $strip_bbcodes String of BBCodes to remove, pipe delimited
	 *
	 * @return array Array with attachment mapping info
	 */
	protected function get_attachment_info($text, $strip_bbcodes)
	{
		// Get all inline attachments to build the mapping
		$all_attachments = [];
		if (preg_match_all(self::ATTACHMENT_PATTERN, $text, $all_matches))
		{
			foreach ($all_matches[2] as $idx => $xml_index)
			{
				$all_attachments[(int) $xml_index] = $all_matches[1][$idx];
			}
		}

		// Get attachments that are inside BBCodes to be stripped
		$excluded_xml_indices = [];

		$bbcodes = array_filter(array_map('trim', explode('|', $strip_bbcodes)));
		foreach ($bbcodes as $bbcode)
		{
			$bbcode_content = $this->extract_bbcode_content($text, $bbcode);
			if (preg_match_all(self::ATTACHMENT_PATTERN, $bbcode_content, $matches))
			{
				$excluded_xml_indices = array_merge($excluded_xml_indices, array_map('intval', $matches[2]));
			}
		}

		// Only build the mapping if we actually found attachments to exclude
		$xml_to_array_map = [];
		if (!empty($excluded_xml_indices))
		{
			$new_array_index = 0;
			foreach ($all_attachments as $xml_index => $filename)
			{
				if (!in_array($xml_index, $excluded_xml_indices, true))
				{
					$xml_to_array_map[$xml_index] = $new_array_index++;
				}
			}

			// array_unique only needed when we have excluded items
			$excluded_xml_indices = array_unique($excluded_xml_indices);
		}

		return [
			'excluded_xml_indices' => $excluded_xml_indices,
			'all_attachments' => $all_attachments,
			'xml_to_array_map' => $xml_to_array_map,
		];
	}

	/**
	 * Extract content from BBCode tags
	 *
	 * @param string $text Raw post text
	 * @param string $bbcode BBCode name to extract
	 *
	 * @return string Concatenated content from all instances of the BBCode
	 */
	protected function extract_bbcode_content($text, $bbcode)
	{
		$content = '';
		$bbcode_upper = strtoupper($bbcode);

		// Match opening and closing tags for this BBCode
		// This regex finds the BBCode start and end tags in the XML structure
		$pattern = '#<' . preg_quote($bbcode_upper, '#') . '(?:\s[^>]*)?>.*?</' . preg_quote($bbcode_upper, '#') . '>#s';

		if (preg_match_all($pattern, $text, $matches))
		{
			$content = implode(' ', $matches[0]);
		}

		return $content;
	}

	/**
	 * Update attachment mappings after parsed content has been trimmed
	 *
	 * @param string $text Parsed post text after trimming
	 * @param array  $attachment_info Original attachment mapping
	 *
	 * @return array Updated attachment mapping
	 */
	protected function update_attachment_info_after_trim($text, array $attachment_info)
	{
		$remaining = $this->get_attachment_info($text, '')['all_attachments'];
		$excluded = $attachment_info['excluded_xml_indices'];

		foreach ($attachment_info['all_attachments'] as $xml_index => $filename)
		{
			if (!array_key_exists($xml_index, $remaining))
			{
				$excluded[] = $xml_index;
			}
		}

		$attachment_info['excluded_xml_indices'] = array_unique(array_map('intval', $excluded));
		$attachment_info['xml_to_array_map'] = [];
		$new_index = 0;
		foreach ($attachment_info['all_attachments'] as $xml_index => $filename)
		{
			if (!in_array($xml_index, $attachment_info['excluded_xml_indices'], true))
			{
				$attachment_info['xml_to_array_map'][$xml_index] = $new_index++;
			}
		}

		return $attachment_info;
	}

	/**
	 * Find an attachment array index for an inline attachment XML index.
	 *
	 * @param int    $xml_index    Attachment index from the parsed post XML
	 * @param string $filename     Filename from the parsed post XML
	 * @param array  $attachments  Attachment rows passed to parse_attachments()
	 * @param array  $used_indexes Attachment array indexes already claimed
	 *
	 * @return int|null Matching attachment array index, or null when not found
	 */
	protected function find_attachment_index($xml_index, $filename, array $attachments, array $used_indexes = [])
	{
		if (array_key_exists($xml_index, $attachments) && !in_array($xml_index, $used_indexes, true) && $this->attachment_matches_filename($attachments[$xml_index], $filename))
		{
			return $xml_index;
		}

		foreach ($attachments as $index => $attachment)
		{
			if (!in_array($index, $used_indexes, true) && $this->attachment_matches_filename($attachment, $filename))
			{
				return $index;
			}
		}

		return null;
	}

	/**
	 * Check whether an attachment row matches an XML attachment filename.
	 *
	 * @param array  $attachment Attachment row
	 * @param string $filename   Filename from parsed post XML
	 *
	 * @return bool True when filename matches
	 */
	protected function attachment_matches_filename(array $attachment, $filename)
	{
		return $filename !== '' && (
			($attachment['real_filename'] ?? '') === $filename ||
			($attachment['physical_filename'] ?? '') === $filename
		);
	}
}
