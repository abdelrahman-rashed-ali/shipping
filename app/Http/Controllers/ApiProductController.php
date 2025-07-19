<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Illuminate\Support\Facades\Cache;

class ApiProductController extends Controller
{
    protected $locale;
    protected $translator;

    public function __construct(Request $request)
    {
        $this->locale = $request->route('lang', 'en');
        $this->translator = new GoogleTranslate($this->locale);
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
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

        $xpath = new \DOMXPath($dom);
        $textNodes = $xpath->query('//text()[normalize-space()]');

        foreach ($textNodes as $node) {
            $original = trim($node->nodeValue);
            $translated = $this->translateText($original);
            $node->nodeValue = $translated;
        }

        $body = $dom->getElementsByTagName('body')->item(0);
        $innerHTML = '';
        foreach ($body->childNodes as $child) {
            $innerHTML .= $dom->saveHTML($child);
        }

        return $innerHTML;
    }

    private function translate($text)
    {
        if (!$text || $this->locale === 'en') return $text;

        $cacheKey = "translated:{$this->locale}:" . md5($text);

        return Cache::rememberForever($cacheKey, function () use ($text) {
            try {
                return $this->translator->translate($text);
            } catch (\Exception $e) {
                return $text;
            }
        });
    }

    private function getAvailabilityStatus($months)
    {
        $currentMonth = Carbon::now()->month;
        $nextMonth = Carbon::now()->addMonth()->month;

        if (in_array($currentMonth, $months)) {
            return 'available';
        } elseif (in_array($nextMonth, $months)) {
            return 'soon';
        }

        return 'unavailable';
    }

    public function index()
    {
        $products = Product::with(['hasOneCategory', 'hasManyImages', 'hasManyData', 'hasManyTags'])
            ->get()
            ->map(function ($product) {
                $months = json_decode($product->months ?? '[]');
                $availability = $this->getAvailabilityStatus($months);
                $mainImage = $product->hasManyImages->firstWhere('is_main', true);

                return [
                    'id' => $product->id,
                    'category' => $this->translate(optional($product->hasOneCategory)->name),
                    'name' => $this->translate($product->name),
                    'price' => $product->price,
                    'subdescription' => $this->translate($product->subdescription),
                    'main_image' => $mainImage?->image,
                    'is_available' => $availability,
                ];
            })
            ->sortBy(function ($product) {
                return match ($product['is_available']) {
                    'available' => 0,
                    'soon' => 1,
                    'unavailable' => 2,
                };
            })->values();

        return response()->json($products);
    }

    public function bestSellers()
    {
        $products = Product::with(['hasOneCategory', 'hasManyImages'])
            ->where('best_seller', true)
            ->get()
            ->map(function ($product) {
                $months = json_decode($product->months ?? '[]');
                $availability = $this->getAvailabilityStatus($months);
                $mainImage = $product->hasManyImages->firstWhere('is_main', true);

                return [
                    'id' => $product->id,
                    'category' => $this->translate(optional($product->hasOneCategory)->name),
                    'name' => $this->translate($product->name),
                    'price' => $product->price,
                    'subdescription' => $this->translate($product->subdescription),
                    'main_image' => $mainImage?->image,
                    'is_available' => $availability,
                ];
            })
            ->sortBy(function ($product) {
                return match ($product['is_available']) {
                    'available' => 0,
                    'soon' => 1,
                    'unavailable' => 2,
                };
            })->values();

        return response()->json($products);
    }

    public function productsByCategory($categoryId)
    {
        $products = Product::with(['hasOneCategory', 'hasManyImages'])
            ->where('category_id', $categoryId)
            ->get()
            ->map(function ($product) {
                $months = json_decode($product->months ?? '[]');
                $availability = $this->getAvailabilityStatus($months);
                $mainImage = $product->hasManyImages->firstWhere('is_main', true);

                return [
                    'id' => $product->id,
                    'category' => $this->translate(optional($product->hasOneCategory)->name),
                    'name' => $this->translate($product->name),
                    'price' => $product->price,
                    'subdescription' => $this->translate($product->subdescription),
                    'main_image' => $mainImage?->image,
                    'is_available' => $availability,
                ];
            })
            ->sortBy(function ($product) {
                return match ($product['is_available']) {
                    'available' => 0,
                    'soon' => 1,
                    'unavailable' => 2,
                };
            })->values();

        return response()->json($products);
    }

    public function show($id)
    {
        $product = Product::with(['hasOneCategory', 'hasManyImages', 'hasManyData', 'hasManyTags'])
            ->findOrFail($id);

        $months = json_decode($product->months ?? '[]');
        $product->is_available = $this->getAvailabilityStatus($months);

        $product->name = $this->translate($product->name);
        $product->subdescription = $this->translate($product->subdescription);
        $product->description = $this->translateHtmlPreservingTags($product->description);

        if ($product->hasOneCategory) {
            $product->hasOneCategory->name = $this->translate($product->hasOneCategory->name);
        }

        $product->hasManyData->transform(function ($data) {
            $data->name = $this->translate($data->name);
            $data->description = $this->translate($data->description);
            return $data;
        });

        $product->hasManyTags->transform(function ($tag) {
            $tag->name = $this->translate($tag->name);
            $tag->description = $this->translate($tag->description);
            return $tag;
        });

        $relatedProducts = Product::with(['hasOneCategory', 'hasManyImages'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(6)
            ->get()
            ->map(function ($relatedProduct) {
                $relatedMonths = json_decode($relatedProduct->months ?? '[]');
                $availability = $this->getAvailabilityStatus($relatedMonths);
                $mainImage = $relatedProduct->hasManyImages->firstWhere('is_main', true);

                return [
                    'id' => $relatedProduct->id,
                    'category' => $this->translate(optional($relatedProduct->hasOneCategory)->name),
                    'name' => $this->translate($relatedProduct->name),
                    'price' => $relatedProduct->price,
                    'subdescription' => $this->translate($relatedProduct->subdescription),
                    'main_image' => $mainImage?->image,
                    'is_available' => $availability,
                ];
            })
            ->sortBy(function ($product) {
                return match ($product['is_available']) {
                    'available' => 0,
                    'soon' => 1,
                    'unavailable' => 2,
                };
            })->values()->toArray();

        return response()->json([
            'product' => $product,
            'related_products' => $relatedProducts,
        ]);
    }

    public function getCurrentDate()
    {
        return response()->json([
            'current_date' => Carbon::now()->toDateTimeString(),
            'current_month' => Carbon::now()->month
        ]);
    }

    public function store(Request $request)
    {
        //
    }

    public function update(Request $request, Product $product)
    {
        //
    }

    public function destroy(Product $product)
    {
        //
    }
}
