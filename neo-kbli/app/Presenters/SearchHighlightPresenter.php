<?php

namespace App\Presenters;

use Illuminate\Support\HtmlString;

class SearchHighlightPresenter
{
    /**
     * Render the text with the highlighted keyword.
     *
     * @param string $text
     * @param string|null $keyword
     * @return HtmlString
     */
    public static function render(string $text, ?string $keyword): HtmlString
    {
        if (empty($keyword)) {
            return new HtmlString(e($text));
        }

        $escapedText = e($text);
        $escapedKeyword = e($keyword);

        $pattern = '/' . preg_quote($escapedKeyword, '/') . '/i';
        $replacement = '<mark class="bg-yellow-300 text-gray-900 rounded px-1">$0</mark>';

        $highlighted = preg_replace($pattern, $replacement, $escapedText);

        return new HtmlString($highlighted);
    }
}
