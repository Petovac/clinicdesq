<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Organisation;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class OrganisationSettingsController extends Controller
{
    public function edit()
    {
        $org = Organisation::findOrFail(auth()->user()->organisation_id);

        return view('organisation.settings.branding', compact('org'));
    }

    public function updateLogo(Request $request)
    {
        $org = Organisation::findOrFail(auth()->user()->organisation_id);

        $request->validate([
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($org->logo_path) {
                Storage::disk('public')->delete($org->logo_path);
            }
            $org->logo_path = $request->file('logo')->store("logos/{$org->id}", 'public');
        }

        if ($request->has('remove_logo') && $org->logo_path) {
            Storage::disk('public')->delete($org->logo_path);
            $org->logo_path = null;
        }

        $org->save();

        return redirect()->route('organisation.settings.branding')
            ->with('success', 'Logo updated successfully.');
    }

    public function updateGst(Request $request)
    {
        $org = Organisation::findOrFail(auth()->user()->organisation_id);

        $request->validate([
            'gst_number' => 'nullable|string|max:50',
        ]);

        $org->gst_number = $request->gst_number;
        $org->save();

        return redirect()->route('organisation.settings.branding')
            ->with('success', 'GST information updated successfully.');
    }

    public function update(Request $request)
    {
        $org = Organisation::findOrFail(auth()->user()->organisation_id);

        $request->validate([
            'template_prescription' => 'required|in:classic,modern,minimal',
            'template_casesheet'    => 'required|in:classic,modern,minimal',
            'template_bill'         => 'required|in:classic,modern,minimal',
        ]);

        $org->template_prescription = $request->template_prescription;
        $org->template_casesheet    = $request->template_casesheet;
        $org->template_bill         = $request->template_bill;
        $org->save();

        return redirect()->route('organisation.settings.branding')
            ->with('success', 'Document templates updated successfully.');
    }

    /**
     * Preview a document template with sample data.
     */
    public function preview(string $type, string $template)
    {
        abort_if(!in_array($type, ['prescription', 'casesheet', 'bill']), 404);
        abort_if(!in_array($template, ['classic', 'modern', 'minimal']), 404);

        $org = Organisation::findOrFail(auth()->user()->organisation_id);

        $viewPath = "documents.{$type}.{$template}";
        abort_if(!view()->exists($viewPath), 404);

        $context = $this->buildSampleContext($org, $type);

        return view($viewPath, $context);
    }

    /**
     * Build sample context data for template preview.
     */
    private function buildSampleContext(Organisation $org, string $type): array
    {
        // Real org with its logo
        $logoUrl = $org->logo_path ? asset('storage/' . $org->logo_path) : null;

        // Sample clinic
        $clinic = (object) [
            'name'       => 'Demo Clinic',
            'address'    => '123 MG Road',
            'city'       => 'Mumbai',
            'state'      => 'Maharashtra',
            'pincode'    => '400001',
            'phone'      => '022-12345678',
            'gst_number' => $org->gst_number,
        ];

        // Sample pet
        $pet = (object) [
            'name'    => 'Buddy',
            'species' => 'dog',
            'breed'   => 'Golden Retriever',
        ];

        // Sample parent
        $parent = (object) [
            'name'  => 'Rahul Sharma',
            'phone' => '9876543210',
        ];

        // Sample vet
        $vet = (object) [
            'name'                => 'Dr. Priya Patel',
            'degree'              => 'BVSc & AH',
            'registration_number' => 'KSVC-12345',
            'signature_path'      => null,
        ];

        // Sample appointment
        $appointment = (object) [
            'calculated_age_at_visit' => '3 years',
            'weight'                  => 28,
            'scheduled_at'            => now(),
        ];

        $base = [
            'org'         => $org,
            'logoUrl'     => $logoUrl,
            'clinic'      => $clinic,
            'pet'         => $pet,
            'parent'      => $parent,
            'vet'         => $vet,
            'appointment' => $appointment,
            'date'            => now()->format('d M Y'),
            'vetSignatureUrl' => null,
        ];

        if ($type === 'prescription') {
            $base['prescription'] = (object) [
                'notes' => 'Complete the full course of antibiotics. Revisit if symptoms persist after 5 days.',
            ];
            $base['items'] = collect([
                (object) ['medicine' => 'Tab. Amoxicillin 500mg', 'dosage' => '500mg', 'frequency' => 'Twice daily', 'duration' => '7 days', 'instructions' => 'After food'],
                (object) ['medicine' => 'Tab. Meloxicam 1.5mg',   'dosage' => '1.5mg', 'frequency' => 'Once daily', 'duration' => '5 days', 'instructions' => 'With food, stop if vomiting'],
                (object) ['medicine' => 'Cap. Omeprazole 20mg',   'dosage' => '20mg',  'frequency' => 'Once daily', 'duration' => '7 days', 'instructions' => 'Before breakfast'],
            ]);
        }

        if ($type === 'casesheet') {
            $base['caseSheet'] = (object) [
                'presenting_complaint'  => 'Decreased appetite for 3 days, mild lethargy, occasional vomiting.',
                'history'               => 'Vaccinations up to date. No previous major illnesses. Regular deworming done 2 months ago.',
                'clinical_examination'  => 'Temperature: 102.8°F. Heart rate: 110 bpm. Mild dehydration. Abdomen slightly tense on palpation. No lymphadenopathy.',
                'differentials'         => 'Gastritis, Dietary indiscretion, Early pancreatitis',
                'diagnosis'             => 'Acute gastritis — likely dietary indiscretion',
                'treatment_given'       => 'Inj. Ondansetron 0.5mg/kg IV, Inj. Pantoprazole 1mg/kg IV, Subcutaneous fluids 150ml Ringer Lactate',
                'procedures_done'       => 'CBC and biochemistry panel sent to lab. Abdominal ultrasound — no significant findings.',
                'further_plan'          => 'Bland diet (boiled chicken + rice) for 5 days. Recheck if vomiting persists beyond 48 hours.',
                'advice'                => 'Avoid treats and table food for 1 week. Ensure fresh water available. Monitor stool consistency.',
                'prognosis'             => 'good',
                'followup_date'         => Carbon::now()->addDays(5),
                'followup_reason'       => 'Recheck appetite and hydration status',
            ];
        }

        if ($type === 'bill') {
            $base['bill'] = (object) [
                'status'       => 'confirmed',
                'total_amount' => 1650.00,
                'clinic'       => $clinic,
            ];
            $base['items'] = collect([
                (object) ['description' => 'General Consultation',   'priceItem' => null, 'source' => 'visit_fee', 'quantity' => 1, 'price' => 500.00, 'total' => 500.00, 'status' => 'approved'],
                (object) ['description' => 'Inj. Meloxicam 2ml SC', 'priceItem' => null, 'source' => 'treatment', 'quantity' => 1, 'price' => 350.00, 'total' => 350.00, 'status' => 'approved'],
                (object) ['description' => 'CBC + Biochemistry',     'priceItem' => null, 'source' => 'procedure', 'quantity' => 1, 'price' => 800.00, 'total' => 800.00, 'status' => 'approved'],
            ]);
            $base['gstNumber']   = $org->gst_number;
            $base['subtotal']    = 1650.00;
            $base['totalAmount'] = 1650.00;
        }

        return $base;
    }
}
