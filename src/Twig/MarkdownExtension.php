<?php

namespace App\Twig;

use App\Service\Markdown\MarkdownConverter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MarkdownExtension extends AbstractExtension
{
    public function __construct(private MarkdownConverter $converter) {}

    public function getFilters(): array
    {
        // is_safe: ['html'] – mówimy Twigowi, że zwracamy bezpieczny HTML
        return [
            new TwigFilter('markdown', [$this, 'markdown'], ['is_safe' => ['html']]),
        ];
    }

    public function markdown(?string $value): string
    {
        if (!$value) {
            return '';
        }

        return $this->converter->toHtml($value);
    }
}
