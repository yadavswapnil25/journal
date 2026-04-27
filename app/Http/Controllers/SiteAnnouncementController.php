<?php

namespace App\Http\Controllers;

use App\Models\SiteAnnouncement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class SiteAnnouncementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'isAdmin']);
    }

    protected function ensureSuperadmin(): void
    {
        $types = DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_id', Auth::id())
            ->pluck('roles.role_type')
            ->toArray();

        if (! in_array('superadmin', $types, true)) {
            abort(403);
        }
    }

    public function index()
    {
        $this->ensureSuperadmin();
        $announcements = SiteAnnouncement::query()->ordered()->get();

        return view('admin.site-announcements.index', compact('announcements'));
    }

    public function create()
    {
        $this->ensureSuperadmin();

        return view('admin.site-announcements.create');
    }

    public function store(Request $request)
    {
        $this->ensureSuperadmin();
        $data = $this->validatedData($request);
        $data['image'] = $this->uploadImage($request);
        SiteAnnouncement::create($data);
        Session::flash('message', trans('prs.announcement_saved'));

        return redirect()->route('manageAnnouncements');
    }

    public function edit(int $id)
    {
        $this->ensureSuperadmin();
        $announcement = SiteAnnouncement::findOrFail($id);

        return view('admin.site-announcements.edit', compact('announcement'));
    }

    public function update(Request $request, int $id)
    {
        $this->ensureSuperadmin();
        $announcement = SiteAnnouncement::findOrFail($id);
        $data = $this->validatedData($request);
        $newImage = $this->uploadImage($request);
        if (!empty($newImage)) {
            $this->deleteImageFile($announcement->image);
            $data['image'] = $newImage;
        }
        if ($request->boolean('remove_image')) {
            $this->deleteImageFile($announcement->image);
            $data['image'] = null;
        }
        $announcement->update($data);
        Session::flash('message', trans('prs.announcement_updated'));

        return redirect()->route('manageAnnouncements');
    }

    public function destroy(int $id)
    {
        $this->ensureSuperadmin();
        $announcement = SiteAnnouncement::findOrFail($id);
        $this->deleteImageFile($announcement->image);
        $announcement->delete();
        Session::flash('message', trans('prs.announcement_deleted'));

        return redirect()->route('manageAnnouncements');
    }

    /**
     * @return array<string, mixed>
     */
    protected function validatedData(Request $request): array
    {
        $validated = $request->validate([
            'message' => 'required|string|max:500',
            'body' => 'nullable|string',
            'link_slug' => 'nullable|string|max:191|regex:/^[a-z0-9\-]*$/',
            'sort_order' => 'nullable|integer|min:0|max:65535',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'remove_image' => 'nullable|boolean',
        ]);

        $slug = isset($validated['link_slug']) ? trim($validated['link_slug']) : '';
        $validated['link_slug'] = $slug === '' ? null : $slug;
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['message'] = htmlspecialchars($validated['message'], ENT_QUOTES, 'UTF-8');
        $validated['body'] = $validated['body'] ?? null;

        return $validated;
    }

    protected function uploadImage(Request $request): ?string
    {
        $uploadedImage = $request->file('image');
        if (empty($uploadedImage)) {
            return null;
        }

        $extension = strtolower((string) $uploadedImage->getClientOriginalExtension());
        $fileName = time() . '-' . Str::random(8) . '.' . $extension;
        $destinationDir = public_path('uploads/site-announcements');
        if (!is_dir($destinationDir)) {
            @mkdir($destinationDir, 0755, true);
        }

        $uploadedImage->move($destinationDir, $fileName);

        return 'uploads/site-announcements/' . $fileName;
    }

    protected function deleteImageFile(?string $relativePath): void
    {
        if (empty($relativePath)) {
            return;
        }
        $absolutePath = public_path($relativePath);
        if (is_file($absolutePath)) {
            @unlink($absolutePath);
        }
    }
}
