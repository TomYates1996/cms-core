<?php

namespace BaseCms\Http\Controllers;

use Illuminate\Http\Request;
use BaseCms\Models\Blog;
use BaseCms\Models\Header;
use BaseCms\Models\Footer;
use BaseCms\Models\Widget;
use BaseCms\Models\Page;
use Inertia\Inertia;
use Illuminate\Support\Str;


class BlogController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();
           
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:blogs,slug',
            'created_by' => 'nullable|string|max:255',
            'image_id' => 'nullable|integer',
            'image_path' => 'nullable|string|max:1024', 
            'is_featured' => 'nullable|boolean',
            'category_id' => 'nullable|exists:categories,id',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'status' => 'nullable|in:draft,published,archived', 
        ]);

        $validated['content'] = '';
        $validated['author_id'] = $user->id;
           
        // $validated['created_by'] = $user->id;
        $blog = Blog::create($validated);

        // Add the blog header
        $widget = Widget::create([
            'title' => null,
            'type' => 'blog',              
            'variant' => 'blog_post_header',   
            'image_path' => $validated['image_path'] ?? null,
            'description' => null,
            'content' => 
                '<h1 class="post-title">'.$validated['title'].'</h1>'.
                '<p class="author">'.$user->name.'</p>'.
                '<p class="publish-date">'.now()->toDateString().'</p>'
        ]);

        $blog->widgets()->attach($widget->id, ['position' => 1]);
           
        return;
    }

    public function destroy(Request $request, $id) 
    {
        $user = auth()->user();
    
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    
        $blog = Blog::findOrFail($id);
    
        // DB::transaction(function () use ($layout) {
        //     $widgetIds = $layout->widgets()->pluck('widgets.id')->toArray();
    
        //     $layout->widgets()->detach();
    
        //     foreach ($widgetIds as $widgetId) {
        //         $widget = Widget::find($widgetId);
    
        //         if (
        //             $widget &&
        //             $widget->layouts()->count() === 0 &&
        //             $widget->pages()->count() === 0 &&
        //             $widget->footers()->count() === 0 &&
        //             !$widget->is_saved
        //         ) {
        //             $widget->delete();
        //         }
        //     }
    
            $blog->delete();
        // });
    
        return;
    }

    // Load the CMS Edit Content Page
    public function load_edit_content(Request $request, $id)
    {
        $flatPages = Page::orderBy('level')->get();
        $groupedFlatPages = $flatPages->groupBy('section');

        $formattedPages = [];
        foreach ($groupedFlatPages as $section => $pagesInSection) {
            $formattedPages[$section] = $this->buildTree($pagesInSection->toArray());
        }

        $blog = Blog::where('id', $id)
            ->with('widgets.slides.image')
            ->firstOrFail();

        // $page = Page::where('slug', 'blog/post')
        //     ->with('widgets.slides.image', 'header.logo', 'footer.logo', 'footer.socialMedia', 'footer.widgets')
        //     ->firstOrFail();

        // $header = $layout->header;
        // $footer = $layout->footer;

        // Load the saved widgets
        $savedWidgets = Widget::with('slides.image')
            ->where('is_saved', true)
            ->whereNull('page_id')
            ->get();

        return Inertia::render('cms/pages/EditContent', [
            'pages' => $formattedPages,
            'page' => $blog,
            'widgets' => $blog->widgets,
            'savedWidgets' => $savedWidgets,
            'savedHeaders' => $savedHeaders ?? null,
            'savedFooters' => $savedFooters ?? null,
            'header' => $header ?? null,
            'footer' => $footer ?? null,
            'layout' => false,
            'isBlog' => true,
        ]);
    }

    public function save(Request $request)
    {
        $request->validate([
            'blog_id' => 'required|integer',
            'widgets' => 'nullable|array',
            'widgets.*.title' => 'nullable|string',
            'widgets.*.type' => 'required|string',
            'widgets.*.variant' => 'required|string',
            'widgets.*.slides' => 'nullable|array', 
            'widgets.*.slides.*.id' => 'integer|exists:slides,id',
        ]);

        
        $widgets = $request->input('widgets');
        $blog_id = $request->input('blog_id');
        $blog = Blog::find($blog_id);
        
        $incomingWidgetIds = collect($widgets)->pluck('id')->filter()->toArray(); 
        $currentWidgetIds = $blog->widgets()->pluck('widgets.id')->toArray();

        $textWidget = collect($widgets)->firstWhere('type', 'text');

        if ($textWidget && !empty($textWidget['content'])) {
            $excerptText = Str::limit(strip_tags($textWidget['content']), 200);
            $blog->excerpt = $excerptText;
        } else {
            $blog->excerpt = '';
        }

        $blog->save();
        
        $widgetsToDetach = array_diff($currentWidgetIds, $incomingWidgetIds);

        if (!empty($widgetsToDetach)) {
            $blog->widgets()->detach($widgetsToDetach);
        }
        
        Widget::whereIn('id', $widgetsToDetach)
            ->where('is_saved', false)
            ->get()
            ->each(function ($widget) {
                if (
                    $widget->pages()->count() === 0 &&
                    $widget->layouts()->count() === 0 &&
                    $widget->blogs()->count() === 0 
                ) {
                    $widget->delete();
                }
            });
        
        foreach ($widgets as $index => $widgetData) {
            
            if (isset($widgetData['id'])) {
                $existingWidget = Widget::find($widgetData['id']);
                $existingWidget->update([
                    'title' => $widgetData['title'] ?? null,
                    'description' => $widgetData['description'] ?? null,
                    'subtitle' => $widgetData['subtitle'] ?? null,
                    'link' => $widgetData['link'] ?? null,
                    'link_text' => $widgetData['link_text'] ?? null,
                    'slide_link_text' => $widgetData['slide_link_text'] ?? null,
                    'content' => $widgetData['content'] ?? null,
                ]);
                $widget = $existingWidget;
            } else {
                $widget = Widget::create($widgetData);
            }
    
            $widget->blogs()->syncWithoutDetaching([$blog_id => ['position' => $index + 1]]);
    
            if (isset($widgetData['slides']) && is_array($widgetData['slides'])) {
                $slideIds = array_column($widgetData['slides'], 'id');
    
                if ($widget->wasRecentlyCreated) {
                    $widget->slides()->attach($slideIds); 
                } else {
                    $widget->slides()->sync($slideIds); 
                }
            }
        }
        
        return;
    }

    private function buildTree($pages, $parentId = null) {
        $branch = [];
    
        foreach ($pages as $page) {
            if ($page['parent_id'] === $parentId) {
                $children = $this->buildTree($pages, $page['id']);
                if ($children) {
                    $page['children'] = $children;
                }
                $branch[] = $page;
            }
        }
    
        return $branch;
    }
}
