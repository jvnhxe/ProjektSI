<?php
/*
 * This file is part of the YourProject package.
 *
 * (c) Your Name <your-email@example.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Service\Markdown;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\DisallowedRawHtml\DisallowedRawHtmlExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\MarkdownConverter as LeagueMarkdownConverter;

/**
 * Service wrapper around league/commonmark that converts Markdown to (safe) HTML.
 */
class MarkdownConverter
{
    /**
     * Underlying league/commonmark converter.
     */
    private LeagueMarkdownConverter $converter;

    /**
     * MarkdownConverter constructor.
     *
     * Initializes a CommonMark environment with safe defaults:
     * - disallows raw HTML,
     * - disallows unsafe links,
     * - enables core CommonMark, tables and autolink extensions.
     */
    public function __construct()
    {
        $config = [
            'disallowed_raw_html' => true,
            'allow_unsafe_links'  => false,
        ];

        $env = new Environment($config);
        $env->addExtension(new CommonMarkCoreExtension());
        $env->addExtension(new DisallowedRawHtmlExtension());
        $env->addExtension(new TableExtension());
        $env->addExtension(new AutolinkExtension());

        $this->converter = new LeagueMarkdownConverter($env);
    }

    /**
     * Converts Markdown text to HTML.
     *
     * @param string $markdown Source Markdown string.
     *
     * @return string Rendered HTML.
     */
    public function toHtml(string $markdown): string
    {
        return (string) $this->converter->convert($markdown);
    }
}
