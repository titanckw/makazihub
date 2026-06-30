<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\StaffDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $manager = Auth::user();
        $staffIds = Staff::where('manager_id', $manager->id)->pluck('id');

        $documents = StaffDocument::with('staff.user')
            ->whereIn('staff_id', $staffIds)
            ->when($request->filled('staff_id'), fn($q) => $q->where('staff_id', $request->staff_id))
            ->when($request->filled('category'), fn($q) => $q->where('category', $request->category))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $staffList = Staff::where('manager_id', $manager->id)->with('user')->get();

        return view('manager.documents.index', compact('documents', 'staffList'));
    }

    public function store(Request $request)
    {
        $manager = Auth::user();

        $validated = $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'title'    => 'required|string|max:255',
            'category' => 'required|in:cv,id,contract,certificate,other',
            'file'     => 'required|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
        ]);

        $staff = Staff::findOrFail($validated['staff_id']);
        abort_unless($staff->manager_id === $manager->id, 403);

        $file = $request->file('file');
        $path = $file->store('staff-documents/' . $staff->id, 'local');

        StaffDocument::create([
            'staff_id'      => $staff->id,
            'uploaded_by'   => $manager->id,
            'title'         => $validated['title'],
            'category'      => $validated['category'],
            'file_path'     => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $file->getClientMimeType(),
            'size'          => $file->getSize(),
        ]);

        return back()->with('success', 'Document uploaded successfully.');
    }

    public function download(StaffDocument $document)
    {
        $this->authorizeAccess($document);
        return Storage::disk('local')->download($document->file_path, $document->original_name);
    }

    public function destroy(StaffDocument $document)
    {
        $this->authorizeAccess($document);
        Storage::disk('local')->delete($document->file_path);
        $document->delete();

        return back()->with('success', 'Document deleted.');
    }

    private function authorizeAccess(StaffDocument $document): void
    {
        abort_unless($document->staff->manager_id === Auth::id(), 403);
    }
}
