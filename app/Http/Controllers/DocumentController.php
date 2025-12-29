<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Document::class);

        $user = $request->user();
        $query = Document::with('uploader');

        if ($user->hasRole(['board_member', 'accountant'])) {
            $query->forCommunity($user->community_id);
        }
        // Admin sees all? Or scoping to current "primary" community view?
        // Let's stick to "Staff Scope" mostly. If Admin has no community_id, might see all or empty?
        // Usually Admin acts as Superuser.
        // For simple Phase 4: Admin sees all if no ID, or we fetch first community?
        // Let's assume Admin sees all for now.

        $documents = $query->latest()->paginate(20);

        return Inertia::render('Documents/Index', [
            'documents' => $documents,
            'canUpload' => $user->can('upload', Document::class),
        ]);
    }

    public function create()
    {
        $this->authorize('upload', Document::class);
        return Inertia::render('Documents/Upload');
    }

    public function store(Request $request)
    {
        $this->authorize('upload', Document::class);

        $request->validate([
            'file' => 'required|file|max:10240', // 10MB
            'community_id' => 'required|exists:communities,id', 
            // In real app, we might infer community_id from user scope to prevent uploading to others.
        ]);

        $user = $request->user();
        $communityId = $request->input('community_id');

        // Scoping check for Upload
        if (!$user->hasRole('admin') && $user->community_id != $communityId) {
             abort(403, 'Invalid community scope.');
        }

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getMimeType();
        $size = $file->getSize();

        // Secure storage path (UUID)
        $path = $file->storeAs(
            'documents/' . $communityId, 
            (string) Str::uuid() . '.' . $file->getClientOriginalExtension()
        );

        if (!$path) {
            return back()->with('error', 'File upload failed.');
        }

        Document::create([
            'community_id' => $communityId,
            'original_name' => $originalName,
            'stored_path' => $path,
            'mime_type' => $mimeType,
            'size_bytes' => $size,
            'uploaded_by_user_id' => $user->id,
            // 'finance_entry_id' => optional
        ]);

        return redirect()->route('documents.index')->with('success', 'Document uploaded.');
    }

    public function download(Document $document)
    {
        $this->authorize('download', $document);

        if (!Storage::exists($document->stored_path)) {
            abort(404);
        }

        return Storage::download($document->stored_path, $document->original_name);
    }
}
