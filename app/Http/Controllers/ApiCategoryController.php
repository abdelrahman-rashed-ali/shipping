<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Stichoza\GoogleTranslate\GoogleTranslate;

class ApiCategoryController extends Controller
{
    protected $locale;
    protected $translator;

    public function __construct(Request $request)
    {
        $this->locale = $request->route('lang', 'en');
        $this->translator = new GoogleTranslate($this->locale);
    }

    private function translate($text)
    {
        if (!$text || $this->locale === 'en') return $text;

        // If plain text (not HTML), translate directly
        if (strip_tags($text) === $text) {
            return $this->translateText($text);
        }

        // Else, handle HTML-safe translation
        return $this->translateHtmlPreservingTags($text);
    }

    private function translateText($text)
    {
        $cacheKey = "translated:{$this->locale}:" . md5($text);
        return Cache::rememberForever($cacheKey, function () use ($text) {
            try {
                return $this->translator->translate($text);
            } catch (\Exception $e) {
                return $text;
            }
        });
    }

    private function translateHtmlPreservingTags($html)
    {
        libxml_use_internal_errors(true); // suppress invalid HTML warnings
        $dom = new \DOMDocument();
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

        $xpath = new \DOMXPath($dom);
        $textNodes = $xpath->query('//text()[normalize-space()]');

        foreach ($textNodes as $node) {
            $original = trim($node->nodeValue);
            $translated = $this->translateText($original);
            $node->nodeValue = $translated;
        }

        // Return the content inside <body> only
        $body = $dom->getElementsByTagName('body')->item(0);
        $innerHTML = '';
        foreach ($body->childNodes as $child) {
            $innerHTML .= $dom->saveHTML($child);
        }

        return $innerHTML;
    }

    public function index()
    {
        $categories = Category::all()->map(function ($category) {
            $category->name = $this->translate($category->name);
            $category->description = $this->translate($category->description);
            return $category;
        });

        return response()->json($categories);
    }

    public function e_category()
    {
        $categories = Category::take(8)->get()->map(function ($category) {
            $category->name = $this->translate($category->name);
            $category->description = $this->translate($category->description);
            return $category;
        });

        return response()->json($categories);
    }
}
