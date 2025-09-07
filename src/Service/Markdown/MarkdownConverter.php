<?php

namespace App\Service\Markdown;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\DisallowedRawHtml\DisallowedRawHtmlExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\MarkdownConverter as LeagueMarkdownConverter;

class MarkdownConverter
{
    private LeagueMarkdownConverter $converter;

    public function __construct()
    {
        // Bezpieczna konfiguracja: brak raw HTML, linki autolink, tabele
        $config = [
            'disallowed_raw_html' => true,          // blokuje surowe HTML w treści
            'allow_unsafe_links'  => false,         // blokuje niebezpieczne schematy
        ];

        $env = new Environment($config);
        $env->addExtension(new CommonMarkCoreExtension());
        $env->addExtension(new DisallowedRawHtmlExtension());
        $env->addExtension(new TableExtension());
        $env->addExtension(new AutolinkExtension());

        $this->converter = new LeagueMarkdownConverter($env);
    }

    public function toHtml(string $markdown): string
    {
        return (string) $this->converter->convert($markdown);
    }
}
