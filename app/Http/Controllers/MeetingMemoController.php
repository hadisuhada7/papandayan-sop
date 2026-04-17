<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMeetingMemoRequest;
use App\Http\Requests\UpdateMeetingMemoRequest;
use App\Models\Company;
use App\Models\Category;
use App\Models\MeetingMemo;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class MeetingMemoController extends Controller
{
    use LogsAuditTrail;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            $meetingMemos = MeetingMemo::with(['categories', 'company'])->orderBy('id')->get();
        } else {
            $selectedCompanyId = session('selected_company_id');
            $meetingMemos = MeetingMemo::where('company_id', $selectedCompanyId)
                ->with(['categories', 'company'])
                ->orderBy('id')
                ->get();
        }

        return view('admin.meeting-memos.index', compact('meetingMemos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            $companies = Company::all();
            $categories = Category::all();
        } else {
            $selectedCompanyId = session('selected_company_id');
            $company = Company::find($selectedCompanyId);
            $companies = $company ? collect([$company]) : collect([]);
            $categories = Category::where('company_id', $selectedCompanyId)->get();
        }

        return view('admin.meeting-memos.create', compact('companies', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMeetingMemoRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $validated = $request->validated();
                $categoryIds = $validated['category_ids'] ?? [];
                unset($validated['category_ids']);

                if (!empty($validated['no_expired_date'])) {
                    $validated['expired_date'] = null;
                }
                unset($validated['no_expired_date']);

                $documentNames = $request->input('document_names', []);
                $documentFiles = $request->file('documents', []);

                $meetingMemo = MeetingMemo::create($validated);
                $meetingMemo->categories()->sync($categoryIds);

                if (!empty($documentNames) && !empty($documentFiles)) {
                    foreach ($documentNames as $index => $name) {
                        if (isset($documentFiles[$index])) {
                            $file = $documentFiles[$index];
                            $originalName = $file->getClientOriginalName();
                            $attachmentPath = $file->storeAs('documents', $originalName, 'public');

                            $meetingMemo->documents()->create([
                                'name' => $name,
                                'attachment' => $attachmentPath,
                            ]);
                        }
                    }
                }

                $this->logAuditTrail('Created Meeting Memo', "Created Meeting Memo: {$meetingMemo->title}");
            });

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Meeting Memo created successfully.',
                    'redirect' => route('admin.meeting-memos.index', [], false),
                ]);
            }

            return redirect()->route('admin.meeting-memos.index')->with('toast', ['type' => 'success', 'message' => 'Meeting Memo created successfully.']);
        } catch (Throwable $e) {
            Log::error('Failed to store meeting memo', [
                'error' => $e->getMessage(),
                'document_names_count' => count($request->input('document_names', [])),
                'document_files_count' => count($request->file('documents', [])),
                'user_id' => optional(auth()->user())->id,
            ]);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'message' => config('app.debug')
                        ? $e->getMessage()
                        : 'Failed to save Meeting Memo. Please check document attachments and try again.',
                ], 500);
            }

            return back()->withInput()->with('toast', [
                'type' => 'error',
                'message' => 'Failed to save Meeting Memo. Please try again.',
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(MeetingMemo $meetingMemo)
    {
        $user = auth()->user();

        if (!$user->hasRole('super_admin')) {
            $selectedCompanyId = session('selected_company_id');

            if ($meetingMemo->company_id !== $selectedCompanyId) {
                abort(403, 'Unauthorized access to this meeting memo.');
            }
        }

        $meetingMemo->load(['company', 'categories', 'documents']);

        return view('admin.meeting-memos.detail', compact('meetingMemo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MeetingMemo $meetingMemo)
    {
        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            $companies = Company::all();
            $categories = Category::all();
        } else {
            $selectedCompanyId = session('selected_company_id');

            if ($meetingMemo->company_id !== $selectedCompanyId) {
                abort(403, 'Unauthorized access to this meeting memo.');
            }

            $company = Company::find($selectedCompanyId);
            $companies = $company ? collect([$company]) : collect([]);
            $categories = Category::where('company_id', $selectedCompanyId)->get();
        }

        $meetingMemo->load('categories');
        return view('admin.meeting-memos.edit', compact('meetingMemo', 'companies', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMeetingMemoRequest $request, MeetingMemo $meetingMemo)
    {
        try {
            DB::transaction(function () use ($request, $meetingMemo) {
                $validated = $request->validated();
                $categoryIds = $validated['category_ids'] ?? [];
                unset($validated['category_ids']);

                if (!empty($validated['no_expired_date'])) {
                    $validated['expired_date'] = null;
                }
                unset($validated['no_expired_date']);

                $documentNames = $request->input('document_names', []);
                $documentFiles = $request->file('documents', []);
                $existingDocumentIds = $request->input('existing_document_ids', []);

                $meetingMemo->update($validated);
                $meetingMemo->categories()->sync($categoryIds);

                if (!empty($existingDocumentIds)) {
                    $meetingMemo->documents()->whereNotIn('id', $existingDocumentIds)->delete();
                } else {
                    $meetingMemo->documents()->delete();
                }

                if (!empty($documentNames) && !empty($documentFiles)) {
                    foreach ($documentNames as $index => $name) {
                        if (isset($documentFiles[$index])) {
                            $file = $documentFiles[$index];
                            $originalName = $file->getClientOriginalName();
                            $attachmentPath = $file->storeAs('documents', $originalName, 'public');

                            $meetingMemo->documents()->create([
                                'name' => $name,
                                'attachment' => $attachmentPath,
                            ]);
                        }
                    }
                }

                $this->logAuditTrail('Updated Meeting Memo', "Updated Meeting Memo: {$meetingMemo->title}");
            });

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Meeting Memo updated successfully.',
                    'redirect' => route('admin.meeting-memos.index', [], false),
                ]);
            }

            return redirect()->route('admin.meeting-memos.index')->with('toast', ['type' => 'success', 'message' => 'Meeting Memo updated successfully.']);
        } catch (Throwable $e) {
            Log::error('Failed to update meeting memo', [
                'meeting_memo_id' => $meetingMemo->id,
                'error' => $e->getMessage(),
                'document_names_count' => count($request->input('document_names', [])),
                'document_files_count' => count($request->file('documents', [])),
                'user_id' => optional(auth()->user())->id,
            ]);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'message' => config('app.debug')
                        ? $e->getMessage()
                        : 'Failed to update Meeting Memo. Please check document attachments and try again.',
                ], 500);
            }

            return back()->withInput()->with('toast', [
                'type' => 'error',
                'message' => 'Failed to update Meeting Memo. Please try again.',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MeetingMemo $meetingMemo)
    {
        $meetingMemoTitle = $meetingMemo->title;

        DB::transaction(function () use ($meetingMemo, $meetingMemoTitle) {
            $meetingMemo->delete();

            $this->logAuditTrail('Deleted Meeting Memo', "Deleted Meeting Memo: {$meetingMemoTitle}");
        });

        return redirect()->route('admin.meeting-memos.index')->with('toast', ['type' => 'success', 'message' => 'Meeting Memo deleted successfully.']);
    }

    /**
     * Upload image from Summernote editor.
     */
    public function uploadSummernoteImage(Request $request)
    {
        $request->validate([
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('summernote-images', $filename, 'public');

            return response()->json([
                'url' => asset('storage/' . $path)
            ]);
        }

        return response()->json(['error' => 'No image uploaded'], 400);
    }
}
