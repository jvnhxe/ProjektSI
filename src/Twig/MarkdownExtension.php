<?php
/**
 * This file is part of your project.
 *
 * (c) Your Name or Company <you@example.com>
 *
 * @license MIT
 */

declare(strict_types=1);

namespace App\Twig;

use App\Service\Markdown\MarkdownConverter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Twig extension that exposes a `markdown` filter to render Markdown as safe HTML.
 */
class MarkdownExtension extends AbstractExtension
{
    /**
     * @param MarkdownConverter $converter service used to convert Markdown to HTML
     */
    public function __construct(private MarkdownConverter $converter)
    {
    }

    /**
     * Registers Twig filters provided by this extension.
     *
     * @return array<int, TwigFilter> list of filters (marked as returning safe HTML)
     */
    public function getFilters(): array
    {
        // is_safe: ['html'] – inform Twig the filter output is already safe HTML
        return [
            new TwigFilter('markdown', [$this, 'markdown'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Converts Markdown text to safe HTML.
     *
     * Empty or null input returns an empty string.
     *
     * @param string|null $value Markdown source text
     *
     * @return string Rendered HTML
     */
    public function markdown(?string $value): string
    {
        if (!$value) {
            return '';
        }

        return $this->converter->toHtml($value);
    }
}
