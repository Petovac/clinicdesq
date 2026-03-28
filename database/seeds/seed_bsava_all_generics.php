<?php
/**
 * Comprehensive BSAVA Small Animal Formulary 10th Edition - ALL Generics + Dosages
 * Adds ~200+ missing generics with drug class and species-specific dosages.
 * Run: php artisan tinker database/seeds/seed_bsava_all_generics.php
 *
 * Source: BSAVA Small Animal Formulary, Part A, 10th Edition (Fergus Allerton)
 * NOTE: Run expand_form_enum.php BEFORE this seed.
 */

use App\Models\DrugGeneric;
use App\Models\DrugDosage;

// Each entry: 'Name' => ['class' => ..., 'unit' => ..., 'dosages' => [...]]
// dosages: [species, dose_min, dose_max, dose_unit, routes[], frequencies[]]

$drugs = [

    // ===========================
    // ANAESTHETICS / SEDATIVES
    // ===========================
    'Alfaxalone' => [
        'class' => 'Anaesthetic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 1, 3, 'mg/kg', ['IV'], ['PRN']],
            ['cat', 2, 5, 'mg/kg', ['IV','IM'], ['PRN']],
        ],
    ],
    'Atracurium' => [
        'class' => 'Neuromuscular blocker',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 0.1, 0.25, 'mg/kg', ['IV'], ['PRN']],
            ['cat', 0.1, 0.25, 'mg/kg', ['IV'], ['PRN']],
        ],
    ],
    'Bupivacaine' => [
        'class' => 'Local anaesthetic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 1, 2, 'mg/kg', ['local','epidural'], ['PRN']],
            ['cat', 1, 2, 'mg/kg', ['local','epidural'], ['PRN']],
        ],
    ],
    'Doxapram' => [
        'class' => 'Respiratory stimulant',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 1, 5, 'mg/kg', ['IV'], ['PRN']],
            ['cat', 1, 5, 'mg/kg', ['IV'], ['PRN']],
        ],
    ],
    'Glycopyrrolate' => [
        'class' => 'Anticholinergic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 0.005, 0.01, 'mg/kg', ['IV','IM','SC'], ['PRN']],
            ['cat', 0.005, 0.01, 'mg/kg', ['IV','IM','SC'], ['PRN']],
        ],
    ],
    'Isoflurane' => [
        'class' => 'Inhalation anaesthetic',
        'unit' => '%',
        'dosages' => [
            ['dog', 1, 2.5, '%', ['inhalation'], ['continuous']],
            ['cat', 1, 3, '%', ['inhalation'], ['continuous']],
        ],
    ],
    'Levobupivacaine' => [
        'class' => 'Local anaesthetic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 1, 2, 'mg/kg', ['local','epidural'], ['PRN']],
            ['cat', 1, 1.5, 'mg/kg', ['local','epidural'], ['PRN']],
        ],
    ],
    'Lidocaine' => [
        'class' => 'Local anaesthetic / Antiarrhythmic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 1, 2, 'mg/kg', ['IV','local'], ['PRN']],
            ['cat', 0.25, 0.5, 'mg/kg', ['IV slowly'], ['PRN']],
        ],
    ],
    'Medetomidine' => [
        'class' => 'Alpha-2 agonist sedative',
        'unit' => 'mcg/kg',
        'dosages' => [
            ['dog', 5, 40, 'mcg/kg', ['IV','IM'], ['PRN']],
            ['cat', 10, 80, 'mcg/kg', ['IM'], ['PRN']],
        ],
    ],
    'Midazolam' => [
        'class' => 'Benzodiazepine',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 0.1, 0.3, 'mg/kg', ['IV','IM'], ['PRN']],
            ['cat', 0.1, 0.3, 'mg/kg', ['IV','IM'], ['PRN']],
        ],
    ],
    'Ropivacaine' => [
        'class' => 'Local anaesthetic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 1, 3, 'mg/kg', ['local','epidural'], ['PRN']],
            ['cat', 1, 2, 'mg/kg', ['local','epidural'], ['PRN']],
        ],
    ],
    'Sevoflurane' => [
        'class' => 'Inhalation anaesthetic',
        'unit' => '%',
        'dosages' => [
            ['dog', 2, 4, '%', ['inhalation'], ['continuous']],
            ['cat', 2.5, 4.5, '%', ['inhalation'], ['continuous']],
        ],
    ],
    'Thiopental' => [
        'class' => 'Barbiturate anaesthetic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 8, 15, 'mg/kg', ['IV'], ['PRN']],
            ['cat', 8, 12, 'mg/kg', ['IV'], ['PRN']],
        ],
    ],
    'Pentobarbital' => [
        'class' => 'Barbiturate',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 25, 30, 'mg/kg', ['IV'], ['PRN']],
            ['cat', 25, 30, 'mg/kg', ['IV'], ['PRN']],
        ],
    ],

    // ===========================
    // ANALGESICS / OPIOIDS
    // ===========================
    'Buprenorphine' => [
        'class' => 'Opioid analgesic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 0.01, 0.02, 'mg/kg', ['IV','IM','buccal'], ['q6-8h']],
            ['cat', 0.01, 0.02, 'mg/kg', ['IV','IM','buccal'], ['q6-8h']],
        ],
    ],
    'Codeine' => [
        'class' => 'Opioid analgesic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 1, 2, 'mg/kg', ['PO'], ['BID','TID']],
        ],
    ],
    'Fentanyl' => [
        'class' => 'Opioid analgesic',
        'unit' => 'mcg/kg',
        'dosages' => [
            ['dog', 2, 5, 'mcg/kg', ['IV','transdermal'], ['PRN','q1-2h']],
            ['cat', 1, 3, 'mcg/kg', ['IV','transdermal'], ['PRN','q1-2h']],
        ],
    ],
    'Methadone' => [
        'class' => 'Opioid analgesic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 0.1, 0.5, 'mg/kg', ['IV','IM','SC'], ['q4h']],
            ['cat', 0.1, 0.3, 'mg/kg', ['IV','IM'], ['q4h']],
        ],
    ],
    'Morphine' => [
        'class' => 'Opioid analgesic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 0.1, 0.5, 'mg/kg', ['IV','IM','SC','epidural'], ['q4h']],
            ['cat', 0.05, 0.2, 'mg/kg', ['IM','SC'], ['q4-6h']],
        ],
    ],
    'Naloxone' => [
        'class' => 'Opioid antagonist',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 0.01, 0.04, 'mg/kg', ['IV','IM','SC'], ['PRN']],
            ['cat', 0.01, 0.04, 'mg/kg', ['IV','IM','SC'], ['PRN']],
        ],
    ],
    'Oxymorphone' => [
        'class' => 'Opioid analgesic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 0.05, 0.1, 'mg/kg', ['IV','IM','SC'], ['q4-6h']],
            ['cat', 0.05, 0.1, 'mg/kg', ['IV','IM'], ['q4-6h']],
        ],
    ],
    'Pethidine' => [
        'class' => 'Opioid analgesic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 3, 5, 'mg/kg', ['IM'], ['q2h PRN']],
            ['cat', 3, 5, 'mg/kg', ['IM'], ['q2h PRN']],
        ],
    ],
    'Amantadine' => [
        'class' => 'NMDA antagonist analgesic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 3, 5, 'mg/kg', ['PO'], ['SID']],
            ['cat', 3, 5, 'mg/kg', ['PO'], ['SID']],
        ],
    ],
    'Paracetamol' => [
        'class' => 'Analgesic / Antipyretic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 10, 15, 'mg/kg', ['PO'], ['BID','TID']],
            // TOXIC TO CATS - no cat dosage
        ],
    ],
    'Dipyrone' => [
        'class' => 'NSAID / Analgesic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 25, 50, 'mg/kg', ['IV','IM','SC'], ['SID','BID']],
            ['cat', 25, 25, 'mg/kg', ['IV','SC'], ['SID']],
        ],
    ],

    // ===========================
    // NSAIDs (additional)
    // ===========================
    'Ketoprofen' => [
        'class' => 'NSAID',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 1, 2, 'mg/kg', ['PO','SC','IV'], ['SID']],
            ['cat', 1, 2, 'mg/kg', ['SC'], ['single dose']],
        ],
    ],
    'Piroxicam' => [
        'class' => 'NSAID',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 0.3, 0.3, 'mg/kg', ['PO'], ['SID','EOD']],
        ],
    ],
    'Robenacoxib' => [
        'class' => 'NSAID (COX-2 selective)',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 1, 2, 'mg/kg', ['PO','SC'], ['SID']],
            ['cat', 1, 2, 'mg/kg', ['PO','SC'], ['SID']],
        ],
    ],
    'Flunixin' => [
        'class' => 'NSAID',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 1, 1, 'mg/kg', ['IV'], ['single dose']],
        ],
    ],
    'Tepoxalin' => [
        'class' => 'NSAID',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 10, 20, 'mg/kg', ['PO'], ['SID']],
        ],
    ],

    // ===========================
    // ANTIBIOTICS (additional)
    // ===========================
    'Amikacin' => [
        'class' => 'Aminoglycoside antibiotic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 15, 30, 'mg/kg', ['IV','IM','SC'], ['SID']],
            ['cat', 10, 15, 'mg/kg', ['IV','IM','SC'], ['SID']],
        ],
    ],
    'Cefadroxil' => [
        'class' => 'Cephalosporin antibiotic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 22, 22, 'mg/kg', ['PO'], ['BID']],
            ['cat', 22, 22, 'mg/kg', ['PO'], ['SID']],
        ],
    ],
    'Cefazolin' => [
        'class' => 'Cephalosporin antibiotic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 20, 25, 'mg/kg', ['IV','IM'], ['TID']],
            ['cat', 20, 25, 'mg/kg', ['IV','IM'], ['TID']],
        ],
    ],
    'Cefovecin' => [
        'class' => 'Cephalosporin antibiotic (long-acting)',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 8, 8, 'mg/kg', ['SC'], ['q14days']],
            ['cat', 8, 8, 'mg/kg', ['SC'], ['q14days']],
        ],
    ],
    'Cefuroxime' => [
        'class' => 'Cephalosporin antibiotic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 10, 20, 'mg/kg', ['PO','IV'], ['BID','TID']],
            ['cat', 10, 20, 'mg/kg', ['PO','IV'], ['BID']],
        ],
    ],
    'Chloramphenicol' => [
        'class' => 'Antibiotic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 25, 50, 'mg/kg', ['PO','IV'], ['TID']],
            ['cat', 12.5, 25, 'mg/kg', ['PO'], ['BID']],
        ],
    ],
    'Erythromycin' => [
        'class' => 'Macrolide antibiotic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 10, 20, 'mg/kg', ['PO'], ['TID']],
            ['cat', 10, 15, 'mg/kg', ['PO'], ['TID']],
        ],
    ],
    'Florfenicol' => [
        'class' => 'Antibiotic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 20, 40, 'mg/kg', ['PO','SC'], ['BID']],
            ['cat', 22, 22, 'mg/kg', ['PO'], ['BID']],
        ],
    ],
    'Lincomycin' => [
        'class' => 'Lincosamide antibiotic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 15, 25, 'mg/kg', ['PO'], ['BID']],
            ['cat', 15, 25, 'mg/kg', ['PO'], ['BID']],
        ],
    ],
    'Meropenem' => [
        'class' => 'Carbapenem antibiotic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 8.5, 24, 'mg/kg', ['IV','SC'], ['TID']],
            ['cat', 8.5, 12, 'mg/kg', ['IV','SC'], ['TID']],
        ],
    ],
    'Neomycin' => [
        'class' => 'Aminoglycoside antibiotic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 10, 20, 'mg/kg', ['PO'], ['TID']],
            ['cat', 10, 20, 'mg/kg', ['PO'], ['TID']],
        ],
    ],
    'Ofloxacin' => [
        'class' => 'Fluoroquinolone antibiotic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 7.5, 15, 'mg/kg', ['PO'], ['SID']],
        ],
    ],
    'Orbifloxacin' => [
        'class' => 'Fluoroquinolone antibiotic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 2.5, 7.5, 'mg/kg', ['PO'], ['SID']],
            ['cat', 2.5, 7.5, 'mg/kg', ['PO'], ['SID']],
        ],
    ],
    'Oxytetracycline' => [
        'class' => 'Tetracycline antibiotic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 20, 20, 'mg/kg', ['PO'], ['TID']],
            ['cat', 20, 20, 'mg/kg', ['PO'], ['TID']],
        ],
    ],
    'Penicillin G' => [
        'class' => 'Beta-lactam antibiotic',
        'unit' => 'IU/kg',
        'dosages' => [
            ['dog', 20000, 40000, 'IU/kg', ['IV','IM'], ['QID']],
            ['cat', 20000, 40000, 'IU/kg', ['IV','IM'], ['QID']],
        ],
    ],
    'Pradofloxacin' => [
        'class' => 'Fluoroquinolone antibiotic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 3, 3, 'mg/kg', ['PO'], ['SID']],
            ['cat', 5, 10, 'mg/kg', ['PO'], ['SID']],
        ],
    ],
    'Sulfadiazine + Trimethoprim' => [
        'class' => 'Sulfonamide antibiotic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 15, 30, 'mg/kg', ['PO','SC'], ['BID']],
            ['cat', 15, 30, 'mg/kg', ['PO'], ['BID']],
        ],
    ],
    'Tetracycline' => [
        'class' => 'Tetracycline antibiotic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 15, 25, 'mg/kg', ['PO'], ['TID']],
            ['cat', 15, 25, 'mg/kg', ['PO'], ['TID']],
        ],
    ],
    'Tobramycin' => [
        'class' => 'Aminoglycoside antibiotic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 2, 4, 'mg/kg', ['IV','IM','SC'], ['TID']],
            ['cat', 2, 3, 'mg/kg', ['IV','IM','SC'], ['TID']],
        ],
    ],
    'Tylosin' => [
        'class' => 'Macrolide antibiotic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 10, 40, 'mg/kg', ['PO'], ['BID','TID']],
            ['cat', 10, 15, 'mg/kg', ['PO'], ['BID']],
        ],
    ],
    'Vancomycin' => [
        'class' => 'Glycopeptide antibiotic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 12, 15, 'mg/kg', ['IV slowly'], ['TID']],
        ],
    ],
    'Ronidazole' => [
        'class' => 'Antiprotozoal',
        'unit' => 'mg/kg',
        'dosages' => [
            ['cat', 30, 30, 'mg/kg', ['PO'], ['BID for 14 days']],
        ],
    ],
    'Paromomycin' => [
        'class' => 'Aminoglycoside antibiotic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 125, 165, 'mg/kg', ['PO'], ['BID for 5 days']],
        ],
    ],
    'Aztreonam' => [
        'class' => 'Monobactam antibiotic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 20, 30, 'mg/kg', ['IV','IM'], ['TID','QID']],
        ],
    ],

    // ===========================
    // ANTIFUNGALS (additional)
    // ===========================
    'Amphotericin B' => [
        'class' => 'Antifungal',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 0.25, 0.5, 'mg/kg', ['IV slowly'], ['EOD']],
            ['cat', 0.25, 0.5, 'mg/kg', ['IV slowly'], ['EOD']],
        ],
    ],
    'Fluconazole' => [
        'class' => 'Azole antifungal',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 5, 10, 'mg/kg', ['PO'], ['SID','BID']],
            ['cat', 5, 10, 'mg/kg', ['PO'], ['SID','BID']],
        ],
    ],
    'Flucytosine' => [
        'class' => 'Antifungal',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 25, 50, 'mg/kg', ['PO'], ['TID','QID']],
            ['cat', 25, 50, 'mg/kg', ['PO'], ['TID','QID']],
        ],
    ],
    'Griseofulvin' => [
        'class' => 'Antifungal',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 25, 50, 'mg/kg', ['PO'], ['SID','BID']],
            ['cat', 25, 50, 'mg/kg', ['PO'], ['SID','BID']],
        ],
    ],
    'Terbinafine' => [
        'class' => 'Antifungal',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 30, 30, 'mg/kg', ['PO'], ['SID']],
            ['cat', 30, 40, 'mg/kg', ['PO'], ['SID','alternate weeks']],
        ],
    ],
    'Voriconazole' => [
        'class' => 'Azole antifungal',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 3, 4, 'mg/kg', ['PO'], ['BID']],
            ['cat', 4, 6, 'mg/kg', ['PO'], ['BID']],
        ],
    ],
    'Enilconazole' => [
        'class' => 'Topical antifungal',
        'unit' => 'mg/kg',
        'dosages' => [], // topical only
    ],
    'Miconazole' => [
        'class' => 'Topical antifungal',
        'unit' => 'mg/kg',
        'dosages' => [], // topical only
    ],
    'Nystatin' => [
        'class' => 'Antifungal',
        'unit' => 'IU/kg',
        'dosages' => [
            ['dog', 100000, 100000, 'IU/dog', ['PO'], ['TID']],
        ],
    ],

    // ===========================
    // ANTIVIRALS
    // ===========================
    'Aciclovir' => [
        'class' => 'Antiviral',
        'unit' => 'mg/kg',
        'dosages' => [
            ['cat', 10, 25, 'mg/kg', ['PO'], ['TID']],
        ],
    ],
    'Famciclovir' => [
        'class' => 'Antiviral',
        'unit' => 'mg/kg',
        'dosages' => [
            ['cat', 40, 90, 'mg/kg', ['PO'], ['BID','TID']],
        ],
    ],
    'Interferon' => [
        'class' => 'Antiviral / Immunomodulator',
        'unit' => 'IU/kg',
        'dosages' => [
            ['dog', 1, 1, 'MU/kg', ['SC'], ['SID']],
            ['cat', 1, 1, 'MU/kg', ['SC'], ['SID']],
        ],
    ],

    // ===========================
    // CARDIOVASCULAR (additional)
    // ===========================
    'Clopidogrel' => [
        'class' => 'Antiplatelet',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 1, 2, 'mg/kg', ['PO'], ['SID']],
            ['cat', 18.75, 18.75, 'mg/cat', ['PO'], ['SID']],
        ],
    ],
    'Diltiazem' => [
        'class' => 'Calcium channel blocker',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 0.5, 2, 'mg/kg', ['PO'], ['TID']],
            ['cat', 1, 2.5, 'mg/kg', ['PO'], ['TID']],
        ],
    ],
    'Dobutamine' => [
        'class' => 'Inotrope',
        'unit' => 'mcg/kg/min',
        'dosages' => [
            ['dog', 2.5, 20, 'mcg/kg/min', ['IV CRI'], ['continuous']],
            ['cat', 2.5, 10, 'mcg/kg/min', ['IV CRI'], ['continuous']],
        ],
    ],
    'Heparin' => [
        'class' => 'Anticoagulant',
        'unit' => 'IU/kg',
        'dosages' => [
            ['dog', 100, 200, 'IU/kg', ['SC'], ['TID']],
            ['cat', 200, 300, 'IU/kg', ['SC'], ['TID']],
        ],
    ],
    'Dalteparin' => [
        'class' => 'Low molecular weight heparin',
        'unit' => 'IU/kg',
        'dosages' => [
            ['dog', 100, 150, 'IU/kg', ['SC'], ['BID','TID']],
            ['cat', 100, 150, 'IU/kg', ['SC'], ['BID','TID']],
        ],
    ],
    'Hydralazine' => [
        'class' => 'Vasodilator',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 0.5, 2, 'mg/kg', ['PO'], ['BID']],
        ],
    ],
    'Isoprenaline' => [
        'class' => 'Beta-adrenergic agonist',
        'unit' => 'mcg/kg/min',
        'dosages' => [
            ['dog', 0.04, 0.08, 'mcg/kg/min', ['IV CRI'], ['continuous']],
        ],
    ],
    'Milrinone' => [
        'class' => 'Phosphodiesterase inhibitor',
        'unit' => 'mcg/kg/min',
        'dosages' => [
            ['dog', 1, 10, 'mcg/kg/min', ['IV CRI'], ['continuous']],
        ],
    ],
    'Nitroprusside' => [
        'class' => 'Vasodilator',
        'unit' => 'mcg/kg/min',
        'dosages' => [
            ['dog', 0.5, 5, 'mcg/kg/min', ['IV CRI'], ['continuous']],
        ],
    ],
    'Norepinephrine' => [
        'class' => 'Vasopressor',
        'unit' => 'mcg/kg/min',
        'dosages' => [
            ['dog', 0.05, 0.3, 'mcg/kg/min', ['IV CRI'], ['continuous']],
            ['cat', 0.05, 0.3, 'mcg/kg/min', ['IV CRI'], ['continuous']],
        ],
    ],
    'Procainamide' => [
        'class' => 'Antiarrhythmic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 10, 20, 'mg/kg', ['PO','IV slowly'], ['TID','QID']],
        ],
    ],
    'Propranolol' => [
        'class' => 'Beta-blocker',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 0.2, 1, 'mg/kg', ['PO'], ['TID']],
            ['cat', 2.5, 5, 'mg/cat', ['PO'], ['BID','TID']],
        ],
    ],
    'Sotalol' => [
        'class' => 'Beta-blocker / Antiarrhythmic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 1, 3, 'mg/kg', ['PO'], ['BID']],
        ],
    ],
    'Verapamil' => [
        'class' => 'Calcium channel blocker',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 0.05, 0.15, 'mg/kg', ['IV slowly'], ['PRN']],
        ],
    ],
    'Glyceryl trinitrate' => [
        'class' => 'Vasodilator',
        'unit' => 'mg',
        'dosages' => [
            ['dog', 0.5, 2, 'cm strip', ['topical (pinna)'], ['TID','QID']],
        ],
    ],
    'Telmisartan' => [
        'class' => 'Angiotensin receptor blocker',
        'unit' => 'mg/kg',
        'dosages' => [
            ['cat', 1, 2, 'mg/kg', ['PO'], ['SID']],
            ['dog', 0.5, 1, 'mg/kg', ['PO'], ['SID']],
        ],
    ],

    // ===========================
    // GASTROINTESTINAL (additional)
    // ===========================
    'Bismuth subsalicylate' => [
        'class' => 'GI protectant',
        'unit' => 'ml/kg',
        'dosages' => [
            ['dog', 0.25, 2, 'ml/kg', ['PO'], ['TID','QID']],
        ],
    ],
    'Cimetidine' => [
        'class' => 'H2 antagonist',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 5, 10, 'mg/kg', ['PO','IV','IM'], ['TID','QID']],
            ['cat', 5, 10, 'mg/kg', ['PO','IV','IM'], ['TID']],
        ],
    ],
    'Cisapride' => [
        'class' => 'GI prokinetic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 0.1, 0.5, 'mg/kg', ['PO'], ['TID']],
            ['cat', 0.1, 0.5, 'mg/kg', ['PO'], ['BID','TID']],
        ],
    ],
    'Domperidone' => [
        'class' => 'GI prokinetic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 0.1, 0.3, 'mg/kg', ['PO'], ['TID']],
        ],
    ],
    'Esomeprazole' => [
        'class' => 'Proton pump inhibitor',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 0.5, 1, 'mg/kg', ['IV','PO'], ['SID','BID']],
            ['cat', 0.5, 1, 'mg/kg', ['IV','PO'], ['SID']],
        ],
    ],
    'Kaolin-Pectin' => [
        'class' => 'GI adsorbent',
        'unit' => 'ml/kg',
        'dosages' => [
            ['dog', 1, 2, 'ml/kg', ['PO'], ['q2-6h']],
            ['cat', 1, 2, 'ml/kg', ['PO'], ['q2-6h']],
        ],
    ],
    'Mesalazine' => [
        'class' => 'Anti-inflammatory (GI)',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 10, 20, 'mg/kg', ['PO'], ['TID']],
        ],
    ],
    'Misoprostol' => [
        'class' => 'GI protectant (prostaglandin)',
        'unit' => 'mcg/kg',
        'dosages' => [
            ['dog', 2, 5, 'mcg/kg', ['PO'], ['TID']],
        ],
    ],
    'Pancrelipase' => [
        'class' => 'Pancreatic enzyme supplement',
        'unit' => 'units',
        'dosages' => [
            ['dog', 1, 2, 'tsp/meal', ['PO with food'], ['with each meal']],
            ['cat', 0.5, 1, 'tsp/meal', ['PO with food'], ['with each meal']],
        ],
    ],
    'Propantheline' => [
        'class' => 'GI antispasmodic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 0.25, 0.5, 'mg/kg', ['PO'], ['TID']],
            ['cat', 7.5, 7.5, 'mg/cat', ['PO'], ['TID']],
        ],
    ],
    'Sulfasalazine' => [
        'class' => 'GI anti-inflammatory',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 10, 25, 'mg/kg', ['PO'], ['TID']],
            ['cat', 10, 20, 'mg/kg', ['PO'], ['BID']],
        ],
    ],
    'Ursodeoxycholic acid' => [
        'class' => 'Hepatoprotectant / Choleretic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 10, 15, 'mg/kg', ['PO'], ['SID']],
            ['cat', 10, 15, 'mg/kg', ['PO'], ['SID']],
        ],
    ],
    'S-Adenosylmethionine' => [
        'class' => 'Hepatoprotectant',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 18, 20, 'mg/kg', ['PO'], ['SID']],
            ['cat', 18, 20, 'mg/kg', ['PO'], ['SID']],
        ],
    ],
    'Probiotics' => [
        'class' => 'GI supplement',
        'unit' => 'varies',
        'dosages' => [],
    ],

    // ===========================
    // ENDOCRINE (additional)
    // ===========================
    'Carbimazole' => [
        'class' => 'Antithyroid',
        'unit' => 'mg',
        'dosages' => [
            ['cat', 5, 15, 'mg/cat', ['PO'], ['BID then SID']],
        ],
    ],
    'Desmopressin' => [
        'class' => 'ADH analogue',
        'unit' => 'mcg',
        'dosages' => [
            ['dog', 1, 4, 'mcg/dog', ['SC','conjunctival'], ['BID','TID']],
            ['cat', 0.5, 1, 'mcg/cat', ['SC','conjunctival'], ['BID']],
        ],
    ],
    'Diazoxide' => [
        'class' => 'Insulinoma management',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 5, 30, 'mg/kg', ['PO'], ['BID']],
        ],
    ],
    'Glipizide' => [
        'class' => 'Oral hypoglycaemic',
        'unit' => 'mg',
        'dosages' => [
            ['cat', 2.5, 5, 'mg/cat', ['PO'], ['BID']],
        ],
    ],
    'Insulin Glargine' => [
        'class' => 'Insulin (long-acting)',
        'unit' => 'IU/kg',
        'dosages' => [
            ['dog', 0.25, 0.5, 'IU/kg', ['SC'], ['BID']],
            ['cat', 0.25, 0.5, 'IU/kg', ['SC'], ['BID']],
        ],
    ],
    'Megestrol acetate' => [
        'class' => 'Progestogen',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 2, 2, 'mg/kg', ['PO'], ['SID for 8 days']],
            ['cat', 2.5, 5, 'mg/cat', ['PO'], ['SID for 5 days']],
        ],
    ],
    'Deslorelin' => [
        'class' => 'GnRH agonist',
        'unit' => 'mg',
        'dosages' => [
            ['dog', 4.7, 4.7, 'mg implant', ['SC implant'], ['single dose']],
        ],
    ],
    'Osaterone' => [
        'class' => 'Antiandrogen',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 0.25, 0.5, 'mg/kg', ['PO'], ['SID for 7 days']],
        ],
    ],
    'Diethylstilboestrol' => [
        'class' => 'Oestrogen',
        'unit' => 'mg',
        'dosages' => [
            ['dog', 0.1, 1, 'mg/dog', ['PO'], ['SID then weekly']],
        ],
    ],
    'Stanozolol' => [
        'class' => 'Anabolic steroid',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 1, 2, 'mg/dog', ['PO'], ['BID']],
            ['cat', 1, 1, 'mg/cat', ['PO'], ['SID']],
        ],
    ],
    'Testosterone' => [
        'class' => 'Androgen',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 1, 2, 'mg/kg', ['IM'], ['monthly']],
        ],
    ],
    'Mitotane' => [
        'class' => 'Adrenolytic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 25, 50, 'mg/kg', ['PO'], ['SID induction then weekly']],
        ],
    ],

    // ===========================
    // CORTICOSTEROIDS (additional)
    // ===========================
    'Betamethasone' => [
        'class' => 'Corticosteroid',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 0.1, 0.2, 'mg/kg', ['PO','IM'], ['SID']],
            ['cat', 0.1, 0.2, 'mg/kg', ['PO','IM'], ['SID']],
        ],
    ],
    'Fludrocortisone' => [
        'class' => 'Mineralocorticoid',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 0.01, 0.02, 'mg/kg', ['PO'], ['SID']],
        ],
    ],
    'Hydrocortisone' => [
        'class' => 'Corticosteroid',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 1, 5, 'mg/kg', ['IV'], ['PRN']],
            ['cat', 1, 5, 'mg/kg', ['IV'], ['PRN']],
        ],
    ],
    'Triamcinolone' => [
        'class' => 'Corticosteroid',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 0.1, 0.2, 'mg/kg', ['PO','IM'], ['SID']],
            ['cat', 0.1, 0.2, 'mg/kg', ['PO','IM'], ['SID']],
        ],
    ],

    // ===========================
    // ANTI-SEIZURE (additional)
    // ===========================
    'Clonazepam' => [
        'class' => 'Benzodiazepine anticonvulsant',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 0.05, 0.2, 'mg/kg', ['PO'], ['BID','TID']],
        ],
    ],
    'Imepitoin' => [
        'class' => 'Anticonvulsant',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 10, 30, 'mg/kg', ['PO'], ['BID']],
        ],
    ],
    'Primidone' => [
        'class' => 'Anticonvulsant',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 10, 15, 'mg/kg', ['PO'], ['BID','TID']],
        ],
    ],
    'Zonisamide' => [
        'class' => 'Anticonvulsant',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 5, 10, 'mg/kg', ['PO'], ['BID']],
            ['cat', 5, 10, 'mg/kg', ['PO'], ['SID']],
        ],
    ],

    // ===========================
    // IMMUNOSUPPRESSANTS (additional)
    // ===========================
    'Chlorambucil' => [
        'class' => 'Alkylating agent / Immunosuppressant',
        'unit' => 'mg/m2',
        'dosages' => [
            ['dog', 4, 6, 'mg/m2', ['PO'], ['EOD']],
            ['cat', 2, 6, 'mg/cat', ['PO'], ['EOD']],
        ],
    ],
    'Mycophenolate' => [
        'class' => 'Immunosuppressant',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 10, 20, 'mg/kg', ['PO'], ['BID']],
        ],
    ],
    'Tacrolimus' => [
        'class' => 'Immunosuppressant (topical)',
        'unit' => 'mg/kg',
        'dosages' => [], // primarily topical ophthalmic
    ],

    // ===========================
    // ONCOLOGY / CHEMOTHERAPY
    // ===========================
    'Carboplatin' => [
        'class' => 'Chemotherapy (platinum)',
        'unit' => 'mg/m2',
        'dosages' => [
            ['dog', 300, 300, 'mg/m2', ['IV'], ['q3weeks']],
            ['cat', 200, 240, 'mg/m2', ['IV'], ['q4weeks']],
        ],
    ],
    'Cisplatin' => [
        'class' => 'Chemotherapy (platinum)',
        'unit' => 'mg/m2',
        'dosages' => [
            ['dog', 60, 70, 'mg/m2', ['IV'], ['q3weeks']],
            // CONTRAINDICATED IN CATS
        ],
    ],
    'Cyclophosphamide' => [
        'class' => 'Alkylating agent',
        'unit' => 'mg/m2',
        'dosages' => [
            ['dog', 200, 250, 'mg/m2', ['IV','PO'], ['q2-3weeks']],
            ['cat', 200, 250, 'mg/m2', ['IV','PO'], ['q3weeks']],
        ],
    ],
    'Doxorubicin' => [
        'class' => 'Chemotherapy (anthracycline)',
        'unit' => 'mg/m2',
        'dosages' => [
            ['dog', 25, 30, 'mg/m2', ['IV'], ['q3weeks']],
            ['cat', 1, 1, 'mg/kg', ['IV'], ['q3weeks']],
        ],
    ],
    'Lomustine' => [
        'class' => 'Chemotherapy (nitrosourea)',
        'unit' => 'mg/m2',
        'dosages' => [
            ['dog', 60, 90, 'mg/m2', ['PO'], ['q3-6weeks']],
            ['cat', 50, 60, 'mg/m2', ['PO'], ['q3-6weeks']],
        ],
    ],
    'Melphalan' => [
        'class' => 'Alkylating agent',
        'unit' => 'mg/m2',
        'dosages' => [
            ['dog', 7, 7, 'mg/m2', ['PO'], ['SID for 5 days, repeat q3weeks']],
        ],
    ],
    'Methotrexate' => [
        'class' => 'Antimetabolite',
        'unit' => 'mg/m2',
        'dosages' => [
            ['dog', 2.5, 5, 'mg/m2', ['PO','IV'], ['q48h']],
        ],
    ],
    'Mitoxantrone' => [
        'class' => 'Chemotherapy',
        'unit' => 'mg/m2',
        'dosages' => [
            ['dog', 5, 5.5, 'mg/m2', ['IV'], ['q3weeks']],
            ['cat', 5, 6.5, 'mg/m2', ['IV'], ['q3weeks']],
        ],
    ],
    'Vinblastine' => [
        'class' => 'Vinca alkaloid',
        'unit' => 'mg/m2',
        'dosages' => [
            ['dog', 2, 2.5, 'mg/m2', ['IV'], ['q1-2weeks']],
            ['cat', 2, 2, 'mg/m2', ['IV'], ['q1-2weeks']],
        ],
    ],
    'Vincristine' => [
        'class' => 'Vinca alkaloid',
        'unit' => 'mg/m2',
        'dosages' => [
            ['dog', 0.5, 0.7, 'mg/m2', ['IV'], ['weekly']],
            ['cat', 0.5, 0.7, 'mg/m2', ['IV'], ['weekly']],
        ],
    ],
    'Toceranib' => [
        'class' => 'Tyrosine kinase inhibitor',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 2.5, 3.25, 'mg/kg', ['PO'], ['EOD']],
        ],
    ],
    'Asparaginase' => [
        'class' => 'Chemotherapy enzyme',
        'unit' => 'IU/kg',
        'dosages' => [
            ['dog', 400, 10000, 'IU/kg', ['IM','SC'], ['weekly']],
            ['cat', 400, 10000, 'IU/kg', ['IM','SC'], ['weekly']],
        ],
    ],
    'Bleomycin' => [
        'class' => 'Chemotherapy (antitumour antibiotic)',
        'unit' => 'IU/m2',
        'dosages' => [
            ['dog', 10, 10, 'IU/m2', ['IV','SC'], ['weekly']],
        ],
    ],
    'Hydroxycarbamide' => [
        'class' => 'Antineoplastic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 50, 80, 'mg/kg', ['PO'], ['3 days/week']],
            ['cat', 25, 25, 'mg/kg', ['PO'], ['3 days/week']],
        ],
    ],

    // ===========================
    // RESPIRATORY (additional)
    // ===========================
    'Bromhexine' => [
        'class' => 'Mucolytic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 2, 2, 'mg/kg', ['PO'], ['BID']],
            ['cat', 1, 1, 'mg/kg', ['PO'], ['SID']],
        ],
    ],
    'Ipratropium' => [
        'class' => 'Bronchodilator',
        'unit' => 'mcg',
        'dosages' => [
            ['dog', 20, 40, 'mcg/dog', ['nebulised'], ['TID','QID']],
        ],
    ],
    'Theophylline' => [
        'class' => 'Bronchodilator',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 10, 20, 'mg/kg', ['PO'], ['BID']],
            ['cat', 4, 8, 'mg/kg', ['PO'], ['SID evening']],
        ],
    ],

    // ===========================
    // ANTIPARASITICS (additional)
    // ===========================
    'Emodepside' => [
        'class' => 'Anthelmintic',
        'unit' => 'mg/kg',
        'dosages' => [],  // spot-on only, weight-based dosing
    ],
    'Epsiprantel' => [
        'class' => 'Anthelmintic (cestodes)',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 5.5, 5.5, 'mg/kg', ['PO'], ['single dose']],
            ['cat', 2.75, 2.75, 'mg/kg', ['PO'], ['single dose']],
        ],
    ],
    'Febantel' => [
        'class' => 'Anthelmintic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 15, 15, 'mg/kg', ['PO'], ['SID for 3 days']],
        ],
    ],
    'Fluralaner' => [
        'class' => 'Ectoparasiticide (isoxazoline)',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 25, 56, 'mg/kg', ['PO'], ['q12weeks']],
            ['cat', 40, 94, 'mg/kg', ['topical'], ['q12weeks']],
        ],
    ],
    'Imidacloprid' => [
        'class' => 'Ectoparasiticide',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 10, 10, 'mg/kg', ['topical'], ['monthly']],
            ['cat', 10, 10, 'mg/kg', ['topical'], ['monthly']],
        ],
    ],
    'Imidocarb' => [
        'class' => 'Antiprotozoal',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 5, 6.6, 'mg/kg', ['IM','SC'], ['repeat in 14 days']],
        ],
    ],
    'Levamisole' => [
        'class' => 'Anthelmintic / Immunostimulant',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 5, 8, 'mg/kg', ['PO'], ['SID for 2 days']],
        ],
    ],
    'Lufenuron' => [
        'class' => 'Insect growth regulator',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 10, 10, 'mg/kg', ['PO'], ['monthly']],
            ['cat', 30, 30, 'mg/kg', ['PO'], ['monthly']],
        ],
    ],
    'Mebendazole' => [
        'class' => 'Anthelmintic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 22, 22, 'mg/kg', ['PO'], ['SID for 5 days']],
        ],
    ],
    'Milbemycin' => [
        'class' => 'Macrocyclic lactone anthelmintic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 0.5, 1, 'mg/kg', ['PO'], ['monthly']],
            ['cat', 2, 2, 'mg/kg', ['PO'], ['monthly']],
        ],
    ],
    'Moxidectin' => [
        'class' => 'Macrocyclic lactone antiparasitic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 2.5, 2.5, 'mg/kg', ['topical'], ['monthly']],
            ['cat', 1, 1, 'mg/kg', ['topical'], ['monthly']],
        ],
    ],
    'Nitenpyram' => [
        'class' => 'Flea adulticide',
        'unit' => 'mg',
        'dosages' => [
            ['dog', 1, 1, 'mg/kg', ['PO'], ['SID PRN']],
            ['cat', 1, 1, 'mg/kg', ['PO'], ['SID PRN']],
        ],
    ],
    'Piperazine' => [
        'class' => 'Anthelmintic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 45, 65, 'mg/kg', ['PO'], ['single dose']],
            ['cat', 45, 65, 'mg/kg', ['PO'], ['single dose']],
        ],
    ],
    'Pyrantel' => [
        'class' => 'Anthelmintic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 5, 10, 'mg/kg', ['PO'], ['single dose']],
            ['cat', 5, 10, 'mg/kg', ['PO'], ['single dose']],
        ],
    ],
    'Sarolaner' => [
        'class' => 'Ectoparasiticide (isoxazoline)',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 2, 4, 'mg/kg', ['PO'], ['monthly']],
        ],
    ],
    'Selamectin' => [
        'class' => 'Macrocyclic lactone antiparasitic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 6, 12, 'mg/kg', ['topical'], ['monthly']],
            ['cat', 6, 12, 'mg/kg', ['topical'], ['monthly']],
        ],
    ],
    'Spinosad' => [
        'class' => 'Ectoparasiticide',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 30, 60, 'mg/kg', ['PO'], ['monthly']],
            ['cat', 50, 75, 'mg/kg', ['PO'], ['monthly']],
        ],
    ],
    'Atovaquone' => [
        'class' => 'Antiprotozoal',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 13.3, 13.3, 'mg/kg', ['PO'], ['TID']],
        ],
    ],
    'Closantel' => [
        'class' => 'Anthelmintic',
        'unit' => 'mg/kg',
        'dosages' => [],
    ],
    'Dichlorvos' => [
        'class' => 'Organophosphate antiparasitic',
        'unit' => 'mg/kg',
        'dosages' => [],
    ],
    'Pyrimethamine' => [
        'class' => 'Antiprotozoal',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 1, 1, 'mg/kg', ['PO'], ['SID']],
            ['cat', 0.5, 1, 'mg/kg', ['PO'], ['SID']],
        ],
    ],
    'Tinidazole' => [
        'class' => 'Antiprotozoal',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 15, 44, 'mg/kg', ['PO'], ['SID']],
        ],
    ],

    // ===========================
    // DIURETICS (additional)
    // ===========================
    'Hydrochlorothiazide' => [
        'class' => 'Thiazide diuretic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 2, 4, 'mg/kg', ['PO'], ['BID']],
        ],
    ],

    // ===========================
    // BEHAVIOURAL (additional)
    // ===========================
    'Amitriptyline' => [
        'class' => 'Tricyclic antidepressant',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 1, 2, 'mg/kg', ['PO'], ['BID']],
            ['cat', 0.5, 1, 'mg/kg', ['PO'], ['SID']],
        ],
    ],
    'Buspirone' => [
        'class' => 'Anxiolytic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 1, 2, 'mg/kg', ['PO'], ['BID']],
            ['cat', 0.5, 1, 'mg/kg', ['PO'], ['BID']],
        ],
    ],
    'Clomipramine' => [
        'class' => 'Tricyclic antidepressant',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 1, 3, 'mg/kg', ['PO'], ['BID']],
            ['cat', 0.25, 0.5, 'mg/kg', ['PO'], ['SID']],
        ],
    ],
    'Mirtazapine' => [
        'class' => 'Appetite stimulant / Antidepressant',
        'unit' => 'mg',
        'dosages' => [
            ['dog', 0.6, 1.5, 'mg/kg', ['PO'], ['SID']],
            ['cat', 1.88, 3.75, 'mg/cat', ['PO','transdermal'], ['q48h']],
        ],
    ],
    'Selegiline' => [
        'class' => 'MAO-B inhibitor',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 0.5, 1, 'mg/kg', ['PO'], ['SID morning']],
        ],
    ],

    // ===========================
    // URINARY (additional)
    // ===========================
    'Bethanechol' => [
        'class' => 'Cholinergic / Urinary',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 5, 15, 'mg/dog', ['PO'], ['TID']],
            ['cat', 1.25, 5, 'mg/cat', ['PO'], ['TID']],
        ],
    ],
    'Nitrofurantoin' => [
        'class' => 'Urinary antiseptic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 4.4, 4.4, 'mg/kg', ['PO'], ['TID']],
            ['cat', 4.4, 4.4, 'mg/kg', ['PO'], ['TID']],
        ],
    ],
    'Phenylpropanolamine' => [
        'class' => 'Urethral sphincter agonist',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 1, 2, 'mg/kg', ['PO'], ['BID','TID']],
        ],
    ],
    'Potassium citrate' => [
        'class' => 'Urinary alkalinizer',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 50, 75, 'mg/kg', ['PO'], ['BID']],
            ['cat', 50, 75, 'mg/kg', ['PO'], ['BID']],
        ],
    ],

    // ===========================
    // MUSCULOSKELETAL
    // ===========================
    'Dantrolene' => [
        'class' => 'Muscle relaxant',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 1, 5, 'mg/kg', ['PO'], ['TID']],
            ['cat', 0.5, 2, 'mg/kg', ['PO'], ['TID']],
        ],
    ],
    'Methocarbamol' => [
        'class' => 'Muscle relaxant',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 15, 45, 'mg/kg', ['PO','IV'], ['TID']],
            ['cat', 15, 45, 'mg/kg', ['PO'], ['TID']],
        ],
    ],
    'Glucosamine' => [
        'class' => 'Joint supplement',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 15, 20, 'mg/kg', ['PO'], ['SID']],
            ['cat', 125, 250, 'mg/cat', ['PO'], ['SID']],
        ],
    ],
    'Pentoxifylline' => [
        'class' => 'Rheological / Anti-inflammatory',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 10, 25, 'mg/kg', ['PO'], ['BID','TID']],
        ],
    ],

    // ===========================
    // EMERGENCY / TOXICOLOGY
    // ===========================
    'Acetylcysteine' => [
        'class' => 'Antidote (paracetamol) / Mucolytic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 140, 140, 'mg/kg', ['IV','PO'], ['loading, then 70 mg/kg q6h']],
            ['cat', 140, 140, 'mg/kg', ['IV','PO'], ['loading, then 70 mg/kg q6h']],
        ],
    ],
    'Activated charcoal' => [
        'class' => 'Adsorbent / Antidote',
        'unit' => 'g/kg',
        'dosages' => [
            ['dog', 1, 2, 'g/kg', ['PO'], ['single dose']],
            ['cat', 1, 2, 'g/kg', ['PO'], ['single dose']],
        ],
    ],
    'Apomorphine' => [
        'class' => 'Emetic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 0.02, 0.04, 'mg/kg', ['IV','conjunctival'], ['single dose']],
            // NOT recommended for cats
        ],
    ],
    'Deferoxamine' => [
        'class' => 'Iron chelator',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 10, 40, 'mg/kg', ['IV slowly','IM'], ['q4-8h']],
        ],
    ],
    'Methylene blue' => [
        'class' => 'Antidote (methaemoglobinaemia)',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 1, 1.5, 'mg/kg', ['IV slowly'], ['PRN']],
        ],
    ],
    'Pralidoxime' => [
        'class' => 'Cholinesterase reactivator',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 20, 50, 'mg/kg', ['IV slowly','IM'], ['BID']],
        ],
    ],
    'Protamine' => [
        'class' => 'Heparin antagonist',
        'unit' => 'mg',
        'dosages' => [
            ['dog', 1, 1.5, 'mg per 100IU heparin', ['IV slowly'], ['PRN']],
        ],
    ],
    'Sodium bicarbonate' => [
        'class' => 'Alkalinizing agent',
        'unit' => 'mEq/kg',
        'dosages' => [
            ['dog', 0.5, 1, 'mEq/kg', ['IV slowly'], ['PRN']],
            ['cat', 0.5, 1, 'mEq/kg', ['IV slowly'], ['PRN']],
        ],
    ],
    'Tolazoline' => [
        'class' => 'Alpha-2 antagonist',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 1, 4, 'mg/kg', ['IV slowly'], ['PRN']],
        ],
    ],
    'Tranexamic acid' => [
        'class' => 'Antifibrinolytic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 10, 25, 'mg/kg', ['IV','PO'], ['BID','TID']],
            ['cat', 10, 25, 'mg/kg', ['IV','PO'], ['BID','TID']],
        ],
    ],
    'Aminocaproic acid' => [
        'class' => 'Antifibrinolytic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 50, 100, 'mg/kg', ['IV','PO'], ['TID']],
        ],
    ],
    'Streptokinase' => [
        'class' => 'Thrombolytic',
        'unit' => 'IU/kg',
        'dosages' => [
            ['cat', 90000, 90000, 'IU/cat', ['IV'], ['over 30 min, then 45000 IU/h']],
        ],
    ],

    // ===========================
    // REPRODUCTIVE
    // ===========================
    'Aglepristone' => [
        'class' => 'Antiprogestogen',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 10, 10, 'mg/kg', ['SC'], ['day 1 and 2']],
            ['cat', 10, 10, 'mg/kg', ['SC'], ['day 1 and 2']],
        ],
    ],
    'Carbetocin' => [
        'class' => 'Oxytocin analogue',
        'unit' => 'mcg/kg',
        'dosages' => [
            ['dog', 35, 70, 'mcg/dog', ['SC'], ['PRN']],
        ],
    ],
    'Dinoprost' => [
        'class' => 'Prostaglandin F2α',
        'unit' => 'mcg/kg',
        'dosages' => [
            ['dog', 100, 250, 'mcg/kg', ['SC'], ['SID for 3-5 days']],
            ['cat', 100, 250, 'mcg/kg', ['SC'], ['SID for 3-5 days']],
        ],
    ],
    'Oxytocin' => [
        'class' => 'Uterotonic',
        'unit' => 'IU',
        'dosages' => [
            ['dog', 1, 5, 'IU/dog', ['IM','IV','SC'], ['q20-30min PRN']],
            ['cat', 0.5, 3, 'IU/cat', ['IM','IV','SC'], ['q20-30min PRN']],
        ],
    ],

    // ===========================
    // OPHTHALMIC
    // ===========================
    'Dorzolamide' => [
        'class' => 'Carbonic anhydrase inhibitor (ophthalmic)',
        'unit' => '%',
        'dosages' => [
            ['dog', 2, 2, '% drops', ['topical ophthalmic'], ['TID']],
            ['cat', 2, 2, '% drops', ['topical ophthalmic'], ['TID']],
        ],
    ],
    'Latanoprost' => [
        'class' => 'Prostaglandin analogue (ophthalmic)',
        'unit' => 'drops',
        'dosages' => [
            ['dog', 1, 1, 'drop', ['topical ophthalmic'], ['BID']],
        ],
    ],
    'Timolol' => [
        'class' => 'Beta-blocker (ophthalmic)',
        'unit' => '%',
        'dosages' => [
            ['dog', 0.25, 0.5, '% drops', ['topical ophthalmic'], ['BID']],
            ['cat', 0.25, 0.5, '% drops', ['topical ophthalmic'], ['BID']],
        ],
    ],
    'Phenylephrine' => [
        'class' => 'Sympathomimetic (ophthalmic/systemic)',
        'unit' => '%',
        'dosages' => [
            ['dog', 2.5, 10, '% drops', ['topical ophthalmic'], ['diagnostic']],
        ],
    ],
    'Acetazolamide' => [
        'class' => 'Carbonic anhydrase inhibitor',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 5, 10, 'mg/kg', ['PO','IV'], ['BID','TID']],
        ],
    ],
    'Idoxuridine' => [
        'class' => 'Antiviral (ophthalmic)',
        'unit' => '%',
        'dosages' => [
            ['cat', 0.1, 0.5, '% drops', ['topical ophthalmic'], ['q1-2h then QID']],
        ],
    ],

    // ===========================
    // DERMATOLOGICAL (additional)
    // ===========================
    'Clobetasol' => [
        'class' => 'Topical corticosteroid (potent)',
        'unit' => '%',
        'dosages' => [],
    ],
    'Pimecrolimus' => [
        'class' => 'Topical immunomodulator',
        'unit' => '%',
        'dosages' => [],
    ],
    'Silver sulfadiazine' => [
        'class' => 'Topical antimicrobial (burns)',
        'unit' => '%',
        'dosages' => [],
    ],
    'Mupirocin' => [
        'class' => 'Topical antibiotic',
        'unit' => '%',
        'dosages' => [],
    ],
    'Chlorhexidine' => [
        'class' => 'Topical antiseptic',
        'unit' => '%',
        'dosages' => [],
    ],

    // ===========================
    // SUPPLEMENTS / VITAMINS
    // ===========================
    'Vitamin A' => [
        'class' => 'Vitamin supplement',
        'unit' => 'IU/kg',
        'dosages' => [
            ['dog', 400, 800, 'IU/kg', ['PO'], ['SID']],
        ],
    ],
    'Vitamin B complex' => [
        'class' => 'Vitamin supplement',
        'unit' => 'ml',
        'dosages' => [
            ['dog', 0.5, 2, 'ml/dog', ['IM','SC'], ['SID']],
            ['cat', 0.5, 1, 'ml/cat', ['IM','SC'], ['SID']],
        ],
    ],
    'Ascorbic acid' => [
        'class' => 'Vitamin C',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 20, 30, 'mg/kg', ['PO'], ['SID']],
        ],
    ],
    'Vitamin D' => [
        'class' => 'Vitamin supplement',
        'unit' => 'IU/kg',
        'dosages' => [],
    ],
    'Vitamin E' => [
        'class' => 'Vitamin / Antioxidant',
        'unit' => 'IU/kg',
        'dosages' => [
            ['dog', 10, 20, 'IU/kg', ['PO'], ['SID']],
            ['cat', 10, 20, 'IU/kg', ['PO'], ['SID']],
        ],
    ],
    'Aluminium hydroxide' => [
        'class' => 'Phosphate binder',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 30, 90, 'mg/kg', ['PO with food'], ['TID']],
            ['cat', 30, 90, 'mg/kg', ['PO with food'], ['TID']],
        ],
    ],
    'Calcium carbonate' => [
        'class' => 'Calcium supplement / Phosphate binder',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 25, 50, 'mg/kg', ['PO with food'], ['BID','TID']],
            ['cat', 25, 50, 'mg/kg', ['PO with food'], ['BID','TID']],
        ],
    ],
    'Potassium chloride' => [
        'class' => 'Electrolyte supplement',
        'unit' => 'mEq/kg',
        'dosages' => [
            ['dog', 0.5, 1, 'mEq/kg/h', ['IV'], ['continuous']],
            ['cat', 0.5, 1, 'mEq/kg/h', ['IV'], ['continuous']],
        ],
    ],
    'Dextrose' => [
        'class' => 'Glucose supplement',
        'unit' => '%',
        'dosages' => [
            ['dog', 0.25, 0.5, 'g/kg', ['IV'], ['PRN']],
            ['cat', 0.25, 0.5, 'g/kg', ['IV'], ['PRN']],
        ],
    ],
    'Dextran' => [
        'class' => 'Colloid solution',
        'unit' => 'ml/kg',
        'dosages' => [
            ['dog', 10, 20, 'ml/kg', ['IV'], ['over 15-30 min']],
            ['cat', 5, 10, 'ml/kg', ['IV'], ['over 15-30 min']],
        ],
    ],

    // ===========================
    // MISCELLANEOUS
    // ===========================
    'Allopurinol' => [
        'class' => 'Xanthine oxidase inhibitor',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 10, 15, 'mg/kg', ['PO'], ['BID']],
        ],
    ],
    'Antithrombin' => [
        'class' => 'Anticoagulant protein',
        'unit' => 'IU/kg',
        'dosages' => [],
    ],
    'Clonidine' => [
        'class' => 'Alpha-2 agonist',
        'unit' => 'mcg/kg',
        'dosages' => [
            ['dog', 5, 20, 'mcg/kg', ['PO'], ['BID','TID']],
        ],
    ],
    'Colchicine' => [
        'class' => 'Anti-gout / Anti-fibrotic',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 0.01, 0.03, 'mg/kg', ['PO'], ['SID']],
        ],
    ],
    'Darbepoetin alfa' => [
        'class' => 'Erythropoiesis-stimulating agent',
        'unit' => 'mcg/kg',
        'dosages' => [
            ['dog', 0.45, 0.75, 'mcg/kg', ['SC'], ['weekly']],
            ['cat', 0.45, 0.75, 'mcg/kg', ['SC'], ['weekly']],
        ],
    ],
    'Erythropoietin' => [
        'class' => 'Erythropoiesis-stimulating agent',
        'unit' => 'IU/kg',
        'dosages' => [
            ['dog', 50, 100, 'IU/kg', ['SC'], ['3x/week']],
            ['cat', 50, 100, 'IU/kg', ['SC'], ['3x/week']],
        ],
    ],
    'Hyaluronidase' => [
        'class' => 'Spreading agent',
        'unit' => 'IU',
        'dosages' => [],
    ],
    'Loratadine' => [
        'class' => 'Antihistamine',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 0.5, 1, 'mg/kg', ['PO'], ['SID']],
        ],
    ],
    'Meclizine' => [
        'class' => 'Antiemetic / Anti-motion sickness',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 25, 25, 'mg/dog', ['PO'], ['SID']],
            ['cat', 12.5, 12.5, 'mg/cat', ['PO'], ['SID']],
        ],
    ],
    'Neostigmine' => [
        'class' => 'Anticholinesterase',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 0.01, 0.04, 'mg/kg', ['IM','SC'], ['PRN']],
        ],
    ],
    'Penicillamine' => [
        'class' => 'Chelating agent',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 10, 15, 'mg/kg', ['PO'], ['BID']],
            ['cat', 125, 125, 'mg/cat', ['PO'], ['BID']],
        ],
    ],
    'Prochlorperazine' => [
        'class' => 'Antiemetic / Phenothiazine',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 0.1, 0.5, 'mg/kg', ['IM','SC'], ['TID']],
        ],
    ],
    'Pyridostigmine' => [
        'class' => 'Anticholinesterase',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 0.02, 0.04, 'mg/kg', ['PO'], ['BID','TID']],
        ],
    ],
    'Rutin' => [
        'class' => 'Bioflavonoid (chylothorax)',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 50, 50, 'mg/kg', ['PO'], ['TID']],
            ['cat', 50, 50, 'mg/kg', ['PO'], ['TID']],
        ],
    ],
    'Thalidomide' => [
        'class' => 'Immunomodulator',
        'unit' => 'mg/kg',
        'dosages' => [],
    ],
    'Amitraz' => [
        'class' => 'Ectoparasiticide (topical)',
        'unit' => 'ppm',
        'dosages' => [],
    ],
    'Metformin' => [
        'class' => 'Oral hypoglycaemic',
        'unit' => 'mg',
        'dosages' => [],
    ],
    'Isoxsuprine' => [
        'class' => 'Vasodilator',
        'unit' => 'mg/kg',
        'dosages' => [],
    ],
    'Etamiphylline' => [
        'class' => 'Respiratory stimulant',
        'unit' => 'mg/kg',
        'dosages' => [],
    ],
    'Etodolac' => [
        'class' => 'NSAID',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 10, 15, 'mg/kg', ['PO'], ['SID']],
        ],
    ],
    'Nimesulide' => [
        'class' => 'NSAID',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 5, 5, 'mg/kg', ['PO'], ['SID']],
        ],
    ],
    'Vedaprofen' => [
        'class' => 'NSAID',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 0.5, 0.5, 'mg/kg', ['PO'], ['SID']],
        ],
    ],
    'Flurbiprofen' => [
        'class' => 'NSAID (ophthalmic)',
        'unit' => '%',
        'dosages' => [],
    ],
    'Pamidronate' => [
        'class' => 'Bisphosphonate',
        'unit' => 'mg/kg',
        'dosages' => [
            ['dog', 1, 2, 'mg/kg', ['IV over 2h'], ['q3-4weeks']],
        ],
    ],
    'Iohexol' => [
        'class' => 'Contrast agent',
        'unit' => 'ml/kg',
        'dosages' => [
            ['dog', 1, 2, 'ml/kg', ['IV'], ['diagnostic']],
            ['cat', 1, 2, 'ml/kg', ['IV'], ['diagnostic']],
        ],
    ],
    'Oxazepam' => [
        'class' => 'Benzodiazepine (appetite stimulant)',
        'unit' => 'mg',
        'dosages' => [
            ['cat', 2.5, 2.5, 'mg/cat', ['PO'], ['SID']],
        ],
    ],
];


// ==================================================
// SEED EXECUTION
// ==================================================

$genericCreated = 0;
$genericExisted = 0;
$dosageCreated = 0;
$dosageSkipped = 0;

foreach ($drugs as $name => $data) {
    $generic = DrugGeneric::where('name', $name)->first();

    if ($generic) {
        $genericExisted++;
    } else {
        $generic = DrugGeneric::create([
            'name' => $name,
            'drug_class' => $data['class'],
            'default_dose_unit' => $data['unit'],
        ]);
        $genericCreated++;
        echo "  + Created generic: {$name}\n";
    }

    // Add dosages
    if (!empty($data['dosages'])) {
        foreach ($data['dosages'] as $d) {
            $existing = DrugDosage::where('generic_id', $generic->id)
                ->where('species', $d[0])
                ->first();

            if ($existing) {
                $dosageSkipped++;
            } else {
                DrugDosage::create([
                    'generic_id' => $generic->id,
                    'species' => $d[0],
                    'dose_min' => $d[1],
                    'dose_max' => $d[2],
                    'dose_unit' => $d[3],
                    'routes' => $d[4],
                    'frequencies' => $d[5],
                ]);
                $dosageCreated++;
            }
        }
    }
}

echo "\n============================================\n";
echo "  BSAVA ALL GENERICS SEED COMPLETE\n";
echo "============================================\n";
echo "Generics created:  {$genericCreated}\n";
echo "Generics existed:  {$genericExisted}\n";
echo "Dosages created:   {$dosageCreated}\n";
echo "Dosages skipped:   {$dosageSkipped}\n";
echo "--------------------------------------------\n";
echo "Total generics now: " . DrugGeneric::count() . "\n";
echo "Total dosages now:  " . DrugDosage::count() . "\n";
echo "============================================\n";
