<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\MedicalDocument;
use App\Models\Pet;
use App\Support\OwnerHealthPdf;
use App\Support\OwnerHealthRecords;
use App\Support\OwnerPetHealth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class HealthController extends Controller
{
    private function authorizePet(Pet $pet): void
    {
        abort_unless((int) $pet->owner_id === (int) Auth::id(), 403);
    }

    public function index(Request $request, ?Pet $pet = null): View
    {
        $f = $request->validate(['pet_id' => ['nullable', 'integer', Rule::exists('pets', 'id')->where('owner_id', Auth::id())], 'search' => ['nullable', 'string', 'max:200'], 'type' => ['nullable', 'in:vaccination,treatment,illness,allergy,lab_report,other'], 'record' => ['nullable', 'regex:/^(record|vaccine|document|treatment)-[0-9]+$/']]);
        $pets = Pet::where('owner_id', Auth::id())->with(['images', 'species', 'breed'])->orderBy('name')->get();
        if ($pet && $pet->exists) {
            $this->authorizePet($pet);
        } else {
            $pet = isset($f['pet_id']) ? $pets->firstWhere('id', $f['pet_id']) : $pets->first();
        }
        $rows = collect();
        $counts = collect();
        $timeline = collect();
        $selected = null;
        if ($pet) {
            $pet->load(['images', 'species', 'breed']);
            $pet->vaccination_due = OwnerPetHealth::due($pet->vaccinations()->getQuery())->exists();
            $rows = OwnerHealthRecords::rows($pet);
            $counts = $rows->countBy('type');
            $timeline = $rows->take(6);
            if ($f['record'] ?? null) {
                $selected = $rows->firstWhere('key', $f['record']);
                abort_unless($selected, 404);
            }
            if ($f['type'] ?? null) {
                $rows = $rows->where('type', $f['type']);
            }
            $search = mb_strtolower(trim($f['search'] ?? ''));
            if ($search !== '') {
                $rows = $rows->filter(fn ($r) => str_contains(mb_strtolower(implode(' ', [$pet->name, $r->title, $r->details, $r->notes, $r->vet, $r->clinic, str_replace('_', ' ', $r->type)])), $search));
            }
        }
        $page = LengthAwarePaginator::resolveCurrentPage();
        $records = new LengthAwarePaginator($rows->forPage($page, 8)->values(), $rows->count(), 8, $page, ['path' => $request->url(), 'query' => $request->query()]);

        return view('owner.health.index', compact('pet', 'pets', 'records', 'counts', 'timeline', 'selected'));
    }

    public function download(Pet $pet)
    {
        $this->authorizePet($pet);

        return response(OwnerHealthPdf::make($pet->name, OwnerHealthRecords::rows($pet)), 200, ['Content-Type' => 'application/pdf', 'Content-Disposition' => 'attachment; filename="pet-'.$pet->id.'-health-history.pdf"', 'Cache-Control' => 'private, no-store']);
    }

    public function document(Pet $pet, MedicalDocument $document)
    {
        $this->authorizePet($pet);
        abort_unless((int) $document->pet_id === (int) $pet->id, 404);
        $path = $document->file_path;
        abort_if(str_contains($path, '..') || preg_match('~^(?:[a-z]+:|[/\\\\])~i', $path), 404);
        $disk = str_starts_with($path, 'medical-documents/') ? 'local' : 'public';
        abort_unless(Storage::disk($disk)->exists($path), 404);
        $name = preg_replace('/[^a-zA-Z0-9._ -]/', '_', $document->file_name);

        return Storage::disk($disk)->download($path, $name, ['Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff']);
    }

    public function store(Request $request, Pet $pet): RedirectResponse
    {
        $this->authorizePet($pet);
        $v = $request->validate(['record_date' => ['required', 'date_format:Y-m-d', 'before_or_equal:today', 'after_or_equal:'.($pet->date_of_birth?->toDateString() ?? '1900-01-01')], 'record_type' => ['required', 'in:treatment,illness,allergy,lab_report,other,medical,checkup,surgery,injury,dental,emergency'], 'description' => ['required', 'string', 'max:1000'], 'notes' => ['nullable', 'string', 'max:2000'], 'file' => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,doc,docx']]);
        $path = null;
        try {
            DB::transaction(function () use ($request, $v, $pet, &$path) {
                $record = $pet->healthRecords()->create(collect($v)->except('file')->all());
                if ($request->hasFile('file')) {
                    $path = $this->saveFile($request);
                    $this->createDocument($request, $pet, $path, $record->id, $v['record_type'], $v['description']);
                }
            });
        } catch (\Throwable $e) {
            if ($path) {
                Storage::disk('local')->delete($path);
            }throw $e;
        }

        return redirect()->route('owner.health.index', $pet)->with('success', 'Health record added successfully.');
    }

    private function saveFile(Request $request): string
    {
        $path = $request->file('file')->store('medical-documents', 'local');
        if (! $path) {
            throw ValidationException::withMessages(['file' => 'The file could not be saved. Please try again.']);
        }

        return $path;
    }

    private function createDocument(Request $request, Pet $pet, string $path, ?int $recordId, ?string $type, ?string $description): void
    {
        $pet->medicalDocuments()->create(['health_record_id' => $recordId, 'document_type' => $type, 'description' => $description, 'file_name' => $request->file('file')->getClientOriginalName(), 'file_path' => $path, 'mime_type' => $request->file('file')->getMimeType()]);
    }

    public function storeDocument(Request $request, Pet $pet): RedirectResponse
    {
        $this->authorizePet($pet);
        $v = $request->validate(['file' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,doc,docx'], 'document_type' => ['nullable', 'string', 'max:100'], 'description' => ['nullable', 'string', 'max:255']]);
        $path = $this->saveFile($request);
        try {
            $this->createDocument($request, $pet, $path, null, $v['document_type'] ?? 'lab_report', $v['description'] ?? null);
        } catch (\Throwable $e) {
            Storage::disk('local')->delete($path);
            throw $e;
        }

        return redirect()->route('owner.health.index', $pet)->with('success', 'Document uploaded successfully.');
    }

    public function storeVaccination(Request $request, Pet $pet): RedirectResponse
    {
        $this->authorizePet($pet);
        $v = $request->validate(['vaccine_name' => ['required', 'string', 'max:255'], 'vaccination_date' => ['required', 'date_format:Y-m-d', 'before_or_equal:today', 'after_or_equal:'.($pet->date_of_birth?->toDateString() ?? '1900-01-01')], 'next_due_date' => ['nullable', 'date_format:Y-m-d', 'after:vaccination_date'], 'batch_number' => ['nullable', 'string', 'max:100'], 'notes' => ['nullable', 'string', 'max:2000']]);
        $pet->vaccinations()->create($v);

        return redirect()->route('owner.health.index',$pet)->with('success','Vaccination record added successfully.');
    }
}
