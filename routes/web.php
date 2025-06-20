<?php

use Inertia\Inertia;
use BaseCms\Http\Controllers\PageController;
use BaseCms\Http\Controllers\SlideController;
use BaseCms\Http\Controllers\WidgetController;
use BaseCms\Http\Controllers\SocialMediaController;
use BaseCms\Http\Controllers\ImageController;
use BaseCms\Http\Controllers\CheckoutController;
use BaseCms\Http\Controllers\PromoCodeController;
use BaseCms\Http\Controllers\FooterController;
use BaseCms\Http\Controllers\LayoutController;
use BaseCms\Http\Controllers\BlogController;
use BaseCms\Http\Controllers\ProductController;
use BaseCms\Http\Controllers\ListingController;
use BaseCms\Http\Controllers\EventController;
use BaseCms\Http\Controllers\HeaderController;
use Illuminate\Support\Facades\Route;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Encoders\JpegEncoder;
use BaseCms\Http\Controllers\PayPalController;
use BaseCms\Http\Controllers\OrderController;
use BaseCms\Http\Controllers\CategoryController;


Route::get('/test-image', function () {
    $path = public_path('storage/slides/mBISSusgdfnv7DHlh1XMtmhQwt1DqAI72axjKdrQ.jpg');

    $img = Image::read($path)->resize(300, 200);

    return response($img->encode(new JpegEncoder()))
        ->header('Content-Type', 'image/jpeg');
});

Route::get('/resize/{path}', function ($path) {
    $width = request('w');
    $height = request('h');
    dd($path, $width, $height);


    $storagePath = public_path('storage/' . $path);

    if (!file_exists($storagePath)) {
        abort(404, 'Image not found: ' . $path);
    }

    $img = Image::read($storagePath);
    $originalWidth = $img->width();
    $originalHeight = $img->height();

    if ($width && $height) {
        $targetRatio = $width / $height;
        $originalRatio = $originalWidth / $originalHeight;

        if ($originalRatio > $targetRatio) {
            $img->scale(height: $height);
            $cropX = intval(($img->width() - $width) / 2);
            $img->crop($width, $height, $cropX, 0);
        } else {
            $img->scale(width: $width);
            $cropY = intval(($img->height() - $height) / 2);
            $img->crop($width, $height, 0, $cropY);
        }
    } elseif ($width) {
        $img->scale(width: $width);
    } elseif ($height) {
        $img->scale(height: $height);
    }

    return response($img->encode(new JpegEncoder()))
        ->header('Content-Type', 'image/jpeg');
})->where('path', '.*');

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

Route::get('api/pages', [PageController::class, 'index']);
Route::get('api/pages', [PageController::class, 'index']);

Route::middleware('auth')->prefix('cms')->group(function () {
    Route::get('/', fn() => redirect()->route('cms.dashboard'));
    Route::get('/dashboard', [PageController::class, 'load_dashboard'])->name('cms.dashboard');
    Route::get('/slides/edit/{slide_slug}', [SlideController::class, 'load_edit'])->name('api.slides.load.edit');
    Route::get('/slides', [SlideController::class, 'load'])->name('slides.load');
    Route::get('/pages', [PageController::class, 'load'])->name('pages.load');
    Route::delete('/pages/delete/{page_id}', [PageController::class, 'destroy'])->name('pages.delete');
    Route::delete('/layouts/delete/{layout_id}', [LayoutController::class, 'destroy'])->name('layouts.delete');
    Route::delete('/blog/delete/{blog_id}', [BlogController::class, 'destroy'])->name('blog.delete');
    Route::delete('/crm/product/delete/{product_id}', [ProductController::class, 'destroy'])->name('product.delete');
    Route::delete('/crm/event/delete/{event_id}', [EventController::class, 'destroy'])->name('event.delete');
    Route::delete('/crm/listing/delete/{listing_id}', [ListingController::class, 'destroy'])->name('listing.delete');
    Route::get('/pages/children/{page_slug}', [PageController::class, 'children'])->where('page_slug', '.*')->name('pages.children');
    Route::get('/pages/edit/{page_slug}', [PageController::class, 'load_edit'])->name('pages.load.edit');
    Route::get('/pages/edit-content/{page_slug}', [PageController::class, 'load_edit_content'])
        ->where('page_slug', '.*') 
        ->name('pages.load.edit.content');
    Route::get('/layouts/edit-content/{id}', [LayoutController::class, 'load_edit_content'])->name('layouts.load.edit.content');
    Route::get('/layouts', [PageController::class, 'load_layouts'])->name('pages.load.layouts');
    Route::get('/layouts/index', [LayoutController::class, 'index'])->name('layouts.index');
    Route::get('/blog/edit-content/{id}', [BlogController::class, 'load_edit_content'])->name('blogs.load.edit.content');
    Route::get('/blog', [PageController::class, 'load_blogs'])->name('pages.load.blogs');
    Route::get('/blog/index', [BlogController::class, 'index'])->name('blog.index');
    Route::get('/pages/primary', [PageController::class, 'load_primary'])->name('pages.load.primary');
    Route::get('/pages/secondary', [PageController::class, 'load_secondary'])->name('pages.load.secondary');
    Route::get('/pages/footer', [PageController::class, 'load_footer'])->name('pages.load.footer');
    Route::post('/pages/save', [PageController::class, 'save'])->name('page.save');
    Route::post('/layouts/save', [LayoutController::class, 'save'])->name('layout.save');
    Route::post('/blog/post/save', [BlogController::class, 'save'])->name('blog.save');
    Route::get('/images', [ImageController::class, 'load'])->name('images.load');
    Route::get('/images/edit', [ImageController::class, 'load_edit'])->name('images.load.edit');
    Route::post('/images/update', [ImageController::class, 'update'])->name('images.update');
    Route::get('/slides/new', [SlideController::class, 'load_create'])->name('slides.load.create');
    Route::delete('/slides/delete/{id}', [SlideController::class, 'delete'])->name('slides.delete');
    Route::delete('/crm/coupon/delete/{id}', [PromoCodeController::class, 'delete'])->name('coupon.delete');
    Route::delete('/crm/category/delete/{id}', [CategoryController::class, 'delete_cat'])->name('category.delete');
    Route::delete('/crm/subcategory/delete/{id}', [CategoryController::class, 'delete_subcat'])->name('subcategory.delete');
    Route::get('/pages/{pageId}/widgets', [WidgetController::class, 'index'])->name('widgets.index');
    Route::get('/pages/{pageId}/widgets/create', [WidgetController::class, 'create'])->name('widgets.create');
    Route::post('/pages/{pageId}/widgets', [WidgetController::class, 'store'])->name('widgets.store');
    Route::get('/widgets/{id}/edit', [WidgetController::class, 'edit'])->name('widgets.edit');
    Route::get('/crm/products', [ProductController::class, 'load_products'])->name('pages.load.products');
    Route::get('/crm/products/index', [ProductController::class, 'index'])->name('crm.products.index');
    Route::get('/crm/products/index', [ProductController::class, 'index'])->name('crm.products.variant.index');
    Route::get('/crm/listings', [ListingController::class, 'load_listings'])->name('pages.load.listings');
    Route::get('/crm/coupons', [PromoCodeController::class, 'load_coupons'])->name('pages.load.coupons');
    Route::get('/crm/categories', [CategoryController::class, 'load_categories'])->name('pages.load.categories');
    Route::get('/crm/listings/index', [ListingController::class, 'index'])->name('crm.listings.index');
    Route::get('/crm/events/index', [EventController::class, 'index'])->name('crm.events.index');
    Route::get('/crm/events', [EventController::class, 'load_events'])->name('pages.load.events');
    Route::post('/products/store', [ProductController::class, 'store'])->name('products.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/crm/event/{id}', [EventController::class, 'update'])->name('api.event.update');
    Route::post('/crm/listing/{id}', [ListingController::class, 'update'])->name('api.listing.update');
    Route::put('/pages/update/{slug}', [PageController::class, 'update'])->name('api.pages.update');
    Route::post('/pages/store', [PageController::class, 'store'])->name('api.pages.store');
    Route::post('/category/store', [CategoryController::class, 'store'])->name('api.category.store');
    Route::post('/coupons/store', [PromoCodeController::class, 'store'])->name('api.coupons.store');
    Route::post('/subcategory/store', [CategoryController::class, 'store_subcat'])->name('api.subcategory.store');
    Route::post('/layout/store', [LayoutController::class, 'store'])->name('api.layout.store');
    Route::post('/blog/store', [BlogController::class, 'store'])->name('api.blogs.store');
    Route::post('/cms/crm/product/store', [ProductController::class, 'store'])->name('api.product.store');
    Route::post('/cms/crm/product/variant/store', [ProductController::class, 'store'])->name('api.product.variant.store');
    Route::post('/cms/crm/listing/store', [ListingController::class, 'store'])->name('api.listing.store');
    Route::post('/cms/crm/event/store', [EventController::class, 'store'])->name('api.event.store');
    Route::post('/pages/link/store', [PageController::class, 'store_link'])->name('api.pages.link.store');
    Route::get('/api/slides', [SlideController::class, 'index'])->name('api.slides.index');
    Route::post('/api/slides', [SlideController::class, 'store'])->name('api.slides.store');
    Route::put('/api/slides', [SlideController::class, 'update'])->name('api.slides.update');
    Route::post('/create/new-image', [ImageController::class, 'store'])->name('cms.image.store');
    Route::get('/api/images/all', [ImageController::class, 'index'])->name('cms.image.index');
    Route::get('/api/pages/all', [PageController::class, 'index_all'])->name('cms.page.index');
    Route::put('/widgets/{widget}/save', [WidgetController::class, 'save_widget'])->name('widgets.save');
    Route::put('/header/{header}/save', [HeaderController::class, 'save_header'])->name('header.save');
    Route::put('/footer/{footer}/save', [FooterController::class, 'save_footer'])->name('footer.save');
    Route::post('/item/delete-save', [WidgetController::class, 'delete_save_item'])->name('widgets.delete-save');
    Route::post('/item/update-save', [WidgetController::class, 'update_save_item'])->name('widgets.update-save');
    Route::post('/widgets/create-save', [WidgetController::class, 'create_save_widget'])->name('widgets.create-save');
    Route::delete('/widget/delete/{widget}', [WidgetController::class, 'delete_saved_widget'])->name('widgets.delete.save');
    Route::post('/widget/unsave/{widget}', [WidgetController::class, 'unsave_widget'])->name('widgets.unsave');
    Route::post('/header/create-save', [WidgetController::class, 'create_save_header'])->name('header.create-save');
    Route::post('/footer/create-save', [FooterController::class, 'create_save'])->name('footer.create-save');
    Route::get('/api/social-media', [SocialMediaController::class, 'index'])->name('api.social.index');
    Route::post('/api/social-media/store', [SocialMediaController::class, 'store'])->name('api.social.store');
    Route::post('/api/store/cta', [WidgetController::class, 'cta_test'])->name('api.create.cta.test');
});

Route::get('/api/coupons/index', [PromoCodeController::class, 'index'])->name('api.coupons.index');
Route::get('/api/categories/index', [CategoryController::class, 'index'])->name('api.categories.index');
Route::get('/api/listings/grid', [ListingController::class, 'grid'])->name('api.listings.grid');
Route::get('/api/events/grid', [EventController::class, 'grid'])->name('api.events.grid');
Route::get('/api/products/grid', [ProductController::class, 'grid'])->name('api.products.grid');

Route::post('/paypal/capture-order', [PayPalController::class, 'captureOrder']);
Route::get('/event/{slug}', [EventController::class, 'load_page'])->where('slug', '.*')->name('event.load');
Route::get('/listing/{slug}', [ListingController::class, 'load_page'])->where('slug', '.*')->name('listing.load');
Route::get('/product/{slug}', [ProductController::class, 'load_page'])->where('slug', '.*')->name('product.load');

$blogPrefix = config('cms.blog_page', 'blog');

Route::get("/{$blogPrefix}/post/{slug}", [PageController::class, 'blog_post'])
    ->where('slug', '.*')
    ->name('page.blog.post');

Route::get('/api/addresses', [CheckoutController::class, 'getAddresses']);
Route::get('/api/top-selling-products', [ProductController::class, 'getTopSellingProducts']);
Route::get('/basket', [ProductController::class, 'basket'])->name('product.basket');
Route::get('/order-confirmation/{order_number}', [OrderController::class, 'order_confirmation'])->name('order.confirmation');
Route::get('/checkout', [ProductController::class, 'checkout'])->name('product.checkout');
Route::post('/checkout/start', [CheckoutController::class, 'start']);
Route::get('/checkout/{id}', [CheckoutController::class, 'show']);
// Route::get('/test-mail', [OrderController::class, 'testMail']);
Route::post('/promo/validate', [PromoCodeController::class, 'validatePromo']);
Route::get('/{slug}', [PageController::class, 'show'])->where('slug', '.*')->name('page.show');
