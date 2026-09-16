<?php

namespace App\Support;

use App\Models\Pet;
use App\Models\Treatment;

class OwnerHealthRecords
{
    public static function category(?string $type): string
    {
        return match (strtolower(trim($type ?? ''))) {
            'vaccination','vaccine' => 'vaccination','treatment','medical','surgery','checkup','injury','dental','emergency' => 'treatment','allergy','allergies' => 'allergy','illness','disease' => 'illness','lab','lab_report','lab_result','xray','lab report','report','test' => 'lab_report',default => 'other'
        };
    }

    public static function rows(Pet $pet)
    {
        $pet->load(['healthRecords.vet.vetProfile', 'vaccinations', 'medicalDocuments']);
        $documents = $pet->medicalDocuments->groupBy('health_record_id');
        $rows = $pet->healthRecords->map(fn ($r) => (object) ['key' => 'record-'.$r->id, 'type' => self::category($r->record_type), 'title' => $r->description ?: ucfirst($r->record_type ?? 'Health Record'), 'details' => $r->notes, 'date' => $r->record_date, 'vet' => $r->vet?->name, 'clinic' => $r->vet?->vetProfile?->clinic_name, 'files' => $documents->get($r->id, collect()), 'notes' => $r->notes]);
        $rows = $rows->concat($pet->vaccinations->map(fn ($r) => (object) ['key' => 'vaccine-'.$r->id, 'type' => 'vaccination', 'title' => $r->vaccine_name, 'details' => $r->next_due_date ? 'Next due: '.$r->next_due_date->format('d M Y') : 'No next date recorded', 'date' => $r->vaccination_date, 'vet' => null, 'clinic' => null, 'files' => collect(), 'notes' => trim(($r->batch_number ? 'Batch: '.$r->batch_number."\n" : '').$r->notes)]));
        $rows = $rows->concat($pet->medicalDocuments->whereNull('health_record_id')->map(fn ($r) => (object) ['key' => 'document-'.$r->id, 'type' => self::category($r->document_type), 'title' => $r->description ?: $r->file_name, 'details' => $r->file_name, 'date' => $r->created_at, 'vet' => null, 'clinic' => null, 'files' => collect([$r]), 'notes' => $r->description]));
        $treatments = Treatment::where('pet_id', $pet->id)->with(['vet.vetProfile', 'prescriptions'])->get();
        $rows = $rows->concat($treatments->map(fn ($r) => (object) ['key' => 'treatment-'.$r->id, 'type' => 'treatment', 'title' => $r->diagnosis, 'details' => $r->treatment, 'date' => $r->created_at, 'vet' => $r->vet?->name, 'clinic' => $r->vet?->vetProfile?->clinic_name, 'files' => collect(), 'notes' => trim(($r->symptoms ? 'Symptoms: '.$r->symptoms."\n" : '').($r->notes ?? '')."\n".$r->prescriptions->map(fn ($p) => trim($p->medicine_name.' '.$p->dosage.' '.$p->frequency.' '.$p->duration))->implode("\n"))]));

        return $rows->sortByDesc('date')->values();
    }
}
