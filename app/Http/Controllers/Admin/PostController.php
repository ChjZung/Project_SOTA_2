<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PostController extends Controller
{
    /**
     * Danh sách bài viết & công thức.
     */
    public function index(Request $request): View
    {
        $query = Post::with('author');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        $posts = $query->latest()->paginate(10)->withQueryString();

        return view('admin.posts.index', compact('posts'));
    }

    /**
     * Form tạo bài viết mới.
     */
    public function create(): View
    {
        return view('admin.posts.create');
    }

    /**
     * Lưu bài viết mới.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:posts,slug'],
            'category' => ['nullable', 'string', 'max:100'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
            'is_published' => ['nullable', 'boolean'],
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề bài viết.',
            'content.required' => 'Vui lòng nhập nội dung bài viết.',
            'thumbnail.image' => 'Ảnh đại diện phải là tệp ảnh hợp lệ.',
        ]);

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['name'] ?? $validated['title']);

        $originalSlug = $slug;
        $counter = 1;
        while (Post::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/posts'), $filename);
            $thumbnailPath = 'uploads/posts/' . $filename;
        }

        Post::create([
            'author_id' => Auth::id() ?? 1,
            'title' => $validated['title'],
            'slug' => $slug,
            'category' => $validated['category'] ?? 'Recipes',
            'excerpt' => $validated['excerpt'] ?? null,
            'content' => $validated['content'],
            'thumbnail' => $thumbnailPath,
            'is_published' => $request->boolean('is_published'),
        ]);

        return redirect()->route('admin.posts.index')
            ->with('success', "Thêm bài viết '{$validated['title']}' thành công!");
    }

    /**
     * Form chỉnh sửa bài viết.
     */
    public function edit(Post $post): View
    {
        return view('admin.posts.edit', compact('post'));
    }

    /**
     * Cập nhật bài viết.
     */
    public function update(Request $request, Post $post): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:posts,slug,' . $post->id],
            'category' => ['nullable', 'string', 'max:100'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
            'is_published' => ['nullable', 'boolean'],
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề bài viết.',
            'content.required' => 'Vui lòng nhập nội dung bài viết.',
            'thumbnail.image' => 'Ảnh đại diện phải là tệp ảnh hợp lệ.',
        ]);

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['title']);

        $thumbnailPath = $post->thumbnail;
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/posts'), $filename);
            $thumbnailPath = 'uploads/posts/' . $filename;
        }

        $post->update([
            'title' => $validated['title'],
            'slug' => $slug,
            'category' => $validated['category'] ?? 'Recipes',
            'excerpt' => $validated['excerpt'] ?? null,
            'content' => $validated['content'],
            'thumbnail' => $thumbnailPath,
            'is_published' => $request->boolean('is_published'),
        ]);

        return redirect()->route('admin.posts.index')
            ->with('success', "Cập nhật bài viết '{$post->title}' thành công!");
    }

    /**
     * Xóa bài viết.
     */
    public function destroy(Post $post): RedirectResponse
    {
        $title = $post->title;
        $post->delete();

        return redirect()->route('admin.posts.index')
            ->with('success', "Đã xóa bài viết '{$title}' thành công!");
    }
}
