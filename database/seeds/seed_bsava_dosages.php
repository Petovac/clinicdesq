<?php
/**
 * Seed comprehensive dosage data based on BSAVA Small Animal Formulary 10th Ed.
 * Updates/adds species-specific dosages for all generics in the KB.
 * Run: php artisan tinker database/seeds/seed_bsava_dosages.php
 *
 * Source: BSAVA Small Animal Formulary, Part A, 10th Edition (Fergus Allerton)
 * + standard veterinary pharmacology references
 */

use App\Models\DrugGeneric;
use App\Models\DrugDosage;

$dosageData = [
    // ===== ANTIBIOTICS =====
    'Amoxicillin' => [
        ['species' => 'dog', 'dose_min' => 10, 'dose_max' => 25, 'dose_unit' => 'mg/kg', 'routes' => ['PO','SC','IM'], 'frequencies' => ['BID','TID']],
        ['species' => 'cat', 'dose_min' => 10, 'dose_max' => 25, 'dose_unit' => 'mg/kg', 'routes' => ['PO','SC','IM'], 'frequencies' => ['BID','TID']],
    ],
    'Amoxicillin + Clavulanate' => [
        ['species' => 'dog', 'dose_min' => 12.5, 'dose_max' => 25, 'dose_unit' => 'mg/kg', 'routes' => ['PO','SC','IV'], 'frequencies' => ['BID','TID']],
        ['species' => 'cat', 'dose_min' => 12.5, 'dose_max' => 25, 'dose_unit' => 'mg/kg', 'routes' => ['PO','SC','IV'], 'frequencies' => ['BID','TID']],
    ],
    'Ampicillin' => [
        ['species' => 'dog', 'dose_min' => 10, 'dose_max' => 20, 'dose_unit' => 'mg/kg', 'routes' => ['IV','IM','SC'], 'frequencies' => ['TID','QID']],
        ['species' => 'cat', 'dose_min' => 10, 'dose_max' => 20, 'dose_unit' => 'mg/kg', 'routes' => ['IV','IM','SC'], 'frequencies' => ['TID','QID']],
    ],
    'Azithromycin' => [
        ['species' => 'dog', 'dose_min' => 5, 'dose_max' => 10, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['SID']],
        ['species' => 'cat', 'dose_min' => 5, 'dose_max' => 10, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['SID','EOD']],
    ],
    'Cephalexin' => [
        ['species' => 'dog', 'dose_min' => 15, 'dose_max' => 30, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['BID','TID']],
        ['species' => 'cat', 'dose_min' => 15, 'dose_max' => 30, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['BID']],
    ],
    'Cefpodoxime' => [
        ['species' => 'dog', 'dose_min' => 5, 'dose_max' => 10, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['SID']],
        ['species' => 'cat', 'dose_min' => 5, 'dose_max' => 10, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['SID']],
    ],
    'Ceftriaxone' => [
        ['species' => 'dog', 'dose_min' => 25, 'dose_max' => 50, 'dose_unit' => 'mg/kg', 'routes' => ['IV','IM','SC'], 'frequencies' => ['SID','BID']],
        ['species' => 'cat', 'dose_min' => 25, 'dose_max' => 50, 'dose_unit' => 'mg/kg', 'routes' => ['IV','IM','SC'], 'frequencies' => ['SID','BID']],
    ],
    'Clindamycin' => [
        ['species' => 'dog', 'dose_min' => 5.5, 'dose_max' => 11, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['BID']],
        ['species' => 'cat', 'dose_min' => 5.5, 'dose_max' => 11, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['BID']],
    ],
    'Doxycycline' => [
        ['species' => 'dog', 'dose_min' => 5, 'dose_max' => 10, 'dose_unit' => 'mg/kg', 'routes' => ['PO','IV'], 'frequencies' => ['BID','SID']],
        ['species' => 'cat', 'dose_min' => 5, 'dose_max' => 10, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['BID','SID']],
    ],
    'Enrofloxacin' => [
        ['species' => 'dog', 'dose_min' => 5, 'dose_max' => 20, 'dose_unit' => 'mg/kg', 'routes' => ['PO','SC','IV'], 'frequencies' => ['SID']],
        ['species' => 'cat', 'dose_min' => 5, 'dose_max' => 5, 'dose_unit' => 'mg/kg', 'routes' => ['PO','SC'], 'frequencies' => ['SID']],
    ],
    'Gentamicin' => [
        ['species' => 'dog', 'dose_min' => 9, 'dose_max' => 14, 'dose_unit' => 'mg/kg', 'routes' => ['IV','IM','SC'], 'frequencies' => ['SID']],
        ['species' => 'cat', 'dose_min' => 5, 'dose_max' => 8, 'dose_unit' => 'mg/kg', 'routes' => ['IV','IM','SC'], 'frequencies' => ['SID']],
    ],
    'Marbofloxacin' => [
        ['species' => 'dog', 'dose_min' => 2, 'dose_max' => 5.5, 'dose_unit' => 'mg/kg', 'routes' => ['PO','SC','IV'], 'frequencies' => ['SID']],
        ['species' => 'cat', 'dose_min' => 2, 'dose_max' => 5.5, 'dose_unit' => 'mg/kg', 'routes' => ['PO','SC'], 'frequencies' => ['SID']],
    ],
    'Metronidazole' => [
        ['species' => 'dog', 'dose_min' => 10, 'dose_max' => 15, 'dose_unit' => 'mg/kg', 'routes' => ['PO','IV'], 'frequencies' => ['BID']],
        ['species' => 'cat', 'dose_min' => 10, 'dose_max' => 15, 'dose_unit' => 'mg/kg', 'routes' => ['PO','IV'], 'frequencies' => ['BID']],
    ],

    // ===== NSAIDs / ANALGESICS =====
    'Carprofen' => [
        ['species' => 'dog', 'dose_min' => 2, 'dose_max' => 4, 'dose_unit' => 'mg/kg', 'routes' => ['PO','SC','IV'], 'frequencies' => ['SID','BID']],
        ['species' => 'cat', 'dose_min' => 1, 'dose_max' => 4, 'dose_unit' => 'mg/kg', 'routes' => ['SC','IV'], 'frequencies' => ['single dose']],
    ],
    'Firocoxib' => [
        ['species' => 'dog', 'dose_min' => 5, 'dose_max' => 5, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['SID']],
    ],
    'Meloxicam' => [
        ['species' => 'dog', 'dose_min' => 0.1, 'dose_max' => 0.2, 'dose_unit' => 'mg/kg', 'routes' => ['PO','SC','IV'], 'frequencies' => ['SID']],
        ['species' => 'cat', 'dose_min' => 0.05, 'dose_max' => 0.1, 'dose_unit' => 'mg/kg', 'routes' => ['PO','SC'], 'frequencies' => ['SID']],
    ],
    'Gabapentin' => [
        ['species' => 'dog', 'dose_min' => 5, 'dose_max' => 10, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['BID','TID']],
        ['species' => 'cat', 'dose_min' => 3, 'dose_max' => 10, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['BID','TID']],
    ],
    'Tramadol' => [
        ['species' => 'dog', 'dose_min' => 2, 'dose_max' => 5, 'dose_unit' => 'mg/kg', 'routes' => ['PO','IV','IM'], 'frequencies' => ['BID','TID']],
        ['species' => 'cat', 'dose_min' => 1, 'dose_max' => 2, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['BID']],
    ],

    // ===== STEROIDS =====
    'Dexamethasone' => [
        ['species' => 'dog', 'dose_min' => 0.1, 'dose_max' => 0.5, 'dose_unit' => 'mg/kg', 'routes' => ['IV','IM','PO'], 'frequencies' => ['SID']],
        ['species' => 'cat', 'dose_min' => 0.1, 'dose_max' => 0.5, 'dose_unit' => 'mg/kg', 'routes' => ['IV','IM','PO'], 'frequencies' => ['SID']],
    ],
    'Prednisolone' => [
        ['species' => 'dog', 'dose_min' => 0.5, 'dose_max' => 2, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['SID','BID']],
        ['species' => 'cat', 'dose_min' => 0.5, 'dose_max' => 2, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['SID','BID']],
    ],
    'Methylprednisolone' => [
        ['species' => 'dog', 'dose_min' => 0.5, 'dose_max' => 2, 'dose_unit' => 'mg/kg', 'routes' => ['PO','IV','IM'], 'frequencies' => ['SID']],
        ['species' => 'cat', 'dose_min' => 0.5, 'dose_max' => 2, 'dose_unit' => 'mg/kg', 'routes' => ['PO','IV','IM'], 'frequencies' => ['SID']],
    ],

    // ===== GI DRUGS =====
    'Ondansetron' => [
        ['species' => 'dog', 'dose_min' => 0.5, 'dose_max' => 1, 'dose_unit' => 'mg/kg', 'routes' => ['IV','PO'], 'frequencies' => ['BID','TID']],
        ['species' => 'cat', 'dose_min' => 0.5, 'dose_max' => 1, 'dose_unit' => 'mg/kg', 'routes' => ['IV','PO'], 'frequencies' => ['BID','TID']],
    ],
    'Maropitant' => [
        ['species' => 'dog', 'dose_min' => 1, 'dose_max' => 2, 'dose_unit' => 'mg/kg', 'routes' => ['SC','PO'], 'frequencies' => ['SID']],
        ['species' => 'cat', 'dose_min' => 1, 'dose_max' => 1, 'dose_unit' => 'mg/kg', 'routes' => ['SC','IV'], 'frequencies' => ['SID']],
    ],
    'Metoclopramide' => [
        ['species' => 'dog', 'dose_min' => 0.2, 'dose_max' => 0.5, 'dose_unit' => 'mg/kg', 'routes' => ['PO','SC','IM','IV'], 'frequencies' => ['TID','QID']],
        ['species' => 'cat', 'dose_min' => 0.2, 'dose_max' => 0.5, 'dose_unit' => 'mg/kg', 'routes' => ['PO','SC','IM'], 'frequencies' => ['TID']],
    ],
    'Omeprazole' => [
        ['species' => 'dog', 'dose_min' => 0.5, 'dose_max' => 1, 'dose_unit' => 'mg/kg', 'routes' => ['PO','IV'], 'frequencies' => ['SID','BID']],
        ['species' => 'cat', 'dose_min' => 0.5, 'dose_max' => 1, 'dose_unit' => 'mg/kg', 'routes' => ['PO','IV'], 'frequencies' => ['SID','BID']],
    ],
    'Pantoprazole' => [
        ['species' => 'dog', 'dose_min' => 0.7, 'dose_max' => 1, 'dose_unit' => 'mg/kg', 'routes' => ['IV','PO'], 'frequencies' => ['SID','BID']],
        ['species' => 'cat', 'dose_min' => 0.5, 'dose_max' => 1, 'dose_unit' => 'mg/kg', 'routes' => ['IV','PO'], 'frequencies' => ['SID']],
    ],
    'Famotidine' => [
        ['species' => 'dog', 'dose_min' => 0.5, 'dose_max' => 1, 'dose_unit' => 'mg/kg', 'routes' => ['PO','IV','SC','IM'], 'frequencies' => ['BID']],
        ['species' => 'cat', 'dose_min' => 0.5, 'dose_max' => 1, 'dose_unit' => 'mg/kg', 'routes' => ['PO','IV','SC'], 'frequencies' => ['BID']],
    ],
    'Ranitidine' => [
        ['species' => 'dog', 'dose_min' => 1, 'dose_max' => 2, 'dose_unit' => 'mg/kg', 'routes' => ['PO','IV','IM','SC'], 'frequencies' => ['BID','TID']],
        ['species' => 'cat', 'dose_min' => 1, 'dose_max' => 2, 'dose_unit' => 'mg/kg', 'routes' => ['PO','IV'], 'frequencies' => ['BID']],
    ],
    'Sucralfate' => [
        ['species' => 'dog', 'dose_min' => 0.5, 'dose_max' => 1, 'dose_unit' => 'g', 'routes' => ['PO'], 'frequencies' => ['TID','QID']],
        ['species' => 'cat', 'dose_min' => 0.25, 'dose_max' => 0.5, 'dose_unit' => 'g', 'routes' => ['PO'], 'frequencies' => ['TID']],
    ],
    'Lactulose' => [
        ['species' => 'dog', 'dose_min' => 0.5, 'dose_max' => 1, 'dose_unit' => 'ml/kg', 'routes' => ['PO'], 'frequencies' => ['BID','TID']],
        ['species' => 'cat', 'dose_min' => 0.5, 'dose_max' => 1, 'dose_unit' => 'ml/kg', 'routes' => ['PO'], 'frequencies' => ['BID','TID']],
    ],
    'Loperamide' => [
        ['species' => 'dog', 'dose_min' => 0.08, 'dose_max' => 0.2, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['BID','TID']],
    ],

    // ===== ANTIEMETICS =====
    'Chlorpromazine' => [
        ['species' => 'dog', 'dose_min' => 0.5, 'dose_max' => 1, 'dose_unit' => 'mg/kg', 'routes' => ['IM','SC'], 'frequencies' => ['TID']],
        ['species' => 'cat', 'dose_min' => 0.2, 'dose_max' => 0.5, 'dose_unit' => 'mg/kg', 'routes' => ['IM'], 'frequencies' => ['TID']],
    ],

    // ===== CARDIOVASCULAR =====
    'Amlodipine' => [
        ['species' => 'dog', 'dose_min' => 0.05, 'dose_max' => 0.1, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['SID']],
        ['species' => 'cat', 'dose_min' => 0.625, 'dose_max' => 1.25, 'dose_unit' => 'mg/cat', 'routes' => ['PO'], 'frequencies' => ['SID']],
    ],
    'Atenolol' => [
        ['species' => 'dog', 'dose_min' => 0.25, 'dose_max' => 1, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['BID']],
        ['species' => 'cat', 'dose_min' => 6.25, 'dose_max' => 12.5, 'dose_unit' => 'mg/cat', 'routes' => ['PO'], 'frequencies' => ['SID','BID']],
    ],
    'Benazepril' => [
        ['species' => 'dog', 'dose_min' => 0.25, 'dose_max' => 0.5, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['SID','BID']],
        ['species' => 'cat', 'dose_min' => 0.25, 'dose_max' => 0.5, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['SID']],
    ],
    'Digoxin' => [
        ['species' => 'dog', 'dose_min' => 0.005, 'dose_max' => 0.01, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['BID']],
        ['species' => 'cat', 'dose_min' => 0.005, 'dose_max' => 0.01, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['EOD']],
    ],
    'Enalapril' => [
        ['species' => 'dog', 'dose_min' => 0.25, 'dose_max' => 0.5, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['SID','BID']],
        ['species' => 'cat', 'dose_min' => 0.25, 'dose_max' => 0.5, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['SID','BID']],
    ],
    'Furosemide' => [
        ['species' => 'dog', 'dose_min' => 1, 'dose_max' => 4, 'dose_unit' => 'mg/kg', 'routes' => ['PO','IV','IM'], 'frequencies' => ['SID','BID','TID']],
        ['species' => 'cat', 'dose_min' => 1, 'dose_max' => 4, 'dose_unit' => 'mg/kg', 'routes' => ['PO','IV','IM'], 'frequencies' => ['SID','BID','TID']],
    ],
    'Pimobendan' => [
        ['species' => 'dog', 'dose_min' => 0.1, 'dose_max' => 0.3, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['BID']],
    ],
    'Sildenafil' => [
        ['species' => 'dog', 'dose_min' => 1, 'dose_max' => 2, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['BID','TID']],
    ],
    'Spironolactone' => [
        ['species' => 'dog', 'dose_min' => 1, 'dose_max' => 2, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['SID','BID']],
        ['species' => 'cat', 'dose_min' => 1, 'dose_max' => 2, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['SID']],
    ],

    // ===== SEDATIVES / ANESTHESIA =====
    'Acepromazine' => [
        ['species' => 'dog', 'dose_min' => 0.01, 'dose_max' => 0.05, 'dose_unit' => 'mg/kg', 'routes' => ['IV','IM','SC'], 'frequencies' => ['PRN']],
        ['species' => 'cat', 'dose_min' => 0.01, 'dose_max' => 0.05, 'dose_unit' => 'mg/kg', 'routes' => ['IV','IM','SC'], 'frequencies' => ['PRN']],
    ],
    'Atropine' => [
        ['species' => 'dog', 'dose_min' => 0.02, 'dose_max' => 0.04, 'dose_unit' => 'mg/kg', 'routes' => ['IV','IM','SC'], 'frequencies' => ['PRN']],
        ['species' => 'cat', 'dose_min' => 0.02, 'dose_max' => 0.04, 'dose_unit' => 'mg/kg', 'routes' => ['IV','IM','SC'], 'frequencies' => ['PRN']],
    ],
    'Butorphanol' => [
        ['species' => 'dog', 'dose_min' => 0.1, 'dose_max' => 0.4, 'dose_unit' => 'mg/kg', 'routes' => ['IV','IM','SC'], 'frequencies' => ['PRN','q2-4h']],
        ['species' => 'cat', 'dose_min' => 0.1, 'dose_max' => 0.4, 'dose_unit' => 'mg/kg', 'routes' => ['IV','IM','SC'], 'frequencies' => ['PRN','q2-4h']],
    ],
    'Dexmedetomidine' => [
        ['species' => 'dog', 'dose_min' => 1, 'dose_max' => 20, 'dose_unit' => 'mcg/kg', 'routes' => ['IV','IM'], 'frequencies' => ['PRN']],
        ['species' => 'cat', 'dose_min' => 5, 'dose_max' => 40, 'dose_unit' => 'mcg/kg', 'routes' => ['IM'], 'frequencies' => ['PRN']],
    ],
    'Diazepam' => [
        ['species' => 'dog', 'dose_min' => 0.2, 'dose_max' => 0.5, 'dose_unit' => 'mg/kg', 'routes' => ['IV'], 'frequencies' => ['PRN']],
        ['species' => 'cat', 'dose_min' => 0.2, 'dose_max' => 0.5, 'dose_unit' => 'mg/kg', 'routes' => ['IV'], 'frequencies' => ['PRN']],
    ],
    'Ketamine' => [
        ['species' => 'dog', 'dose_min' => 5, 'dose_max' => 10, 'dose_unit' => 'mg/kg', 'routes' => ['IV','IM'], 'frequencies' => ['PRN']],
        ['species' => 'cat', 'dose_min' => 5, 'dose_max' => 10, 'dose_unit' => 'mg/kg', 'routes' => ['IV','IM'], 'frequencies' => ['PRN']],
    ],
    'Propofol' => [
        ['species' => 'dog', 'dose_min' => 3, 'dose_max' => 6, 'dose_unit' => 'mg/kg', 'routes' => ['IV'], 'frequencies' => ['PRN']],
        ['species' => 'cat', 'dose_min' => 3, 'dose_max' => 6, 'dose_unit' => 'mg/kg', 'routes' => ['IV'], 'frequencies' => ['PRN']],
    ],
    'Xylazine' => [
        ['species' => 'dog', 'dose_min' => 0.5, 'dose_max' => 1, 'dose_unit' => 'mg/kg', 'routes' => ['IV','IM'], 'frequencies' => ['PRN']],
        ['species' => 'cat', 'dose_min' => 0.5, 'dose_max' => 1, 'dose_unit' => 'mg/kg', 'routes' => ['IM'], 'frequencies' => ['PRN']],
    ],
    'Atipamezole' => [
        ['species' => 'dog', 'dose_min' => 50, 'dose_max' => 200, 'dose_unit' => 'mcg/kg', 'routes' => ['IM'], 'frequencies' => ['PRN']],
        ['species' => 'cat', 'dose_min' => 50, 'dose_max' => 200, 'dose_unit' => 'mcg/kg', 'routes' => ['IM'], 'frequencies' => ['PRN']],
    ],

    // ===== ANTIHISTAMINES =====
    'Cetirizine' => [
        ['species' => 'dog', 'dose_min' => 1, 'dose_max' => 2, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['SID','BID']],
        ['species' => 'cat', 'dose_min' => 5, 'dose_max' => 5, 'dose_unit' => 'mg/cat', 'routes' => ['PO'], 'frequencies' => ['SID']],
    ],
    'Chlorpheniramine' => [
        ['species' => 'dog', 'dose_min' => 0.2, 'dose_max' => 0.5, 'dose_unit' => 'mg/kg', 'routes' => ['PO','IM','SC'], 'frequencies' => ['BID','TID']],
        ['species' => 'cat', 'dose_min' => 2, 'dose_max' => 4, 'dose_unit' => 'mg/cat', 'routes' => ['PO'], 'frequencies' => ['BID']],
    ],
    'Cyproheptadine' => [
        ['species' => 'dog', 'dose_min' => 0.5, 'dose_max' => 2, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['BID','TID']],
        ['species' => 'cat', 'dose_min' => 1, 'dose_max' => 4, 'dose_unit' => 'mg/cat', 'routes' => ['PO'], 'frequencies' => ['BID']],
    ],
    'Diphenhydramine' => [
        ['species' => 'dog', 'dose_min' => 1, 'dose_max' => 4, 'dose_unit' => 'mg/kg', 'routes' => ['PO','IM'], 'frequencies' => ['BID','TID']],
        ['species' => 'cat', 'dose_min' => 1, 'dose_max' => 2, 'dose_unit' => 'mg/kg', 'routes' => ['PO','IM'], 'frequencies' => ['BID']],
    ],
    'Hydroxyzine' => [
        ['species' => 'dog', 'dose_min' => 1, 'dose_max' => 2, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['BID','TID']],
    ],

    // ===== DEWORMERS =====
    'Albendazole' => [
        ['species' => 'dog', 'dose_min' => 25, 'dose_max' => 50, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['SID for 3 days']],
        ['species' => 'cat', 'dose_min' => 25, 'dose_max' => 50, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['SID for 3 days']],
    ],
    'Fenbendazole' => [
        ['species' => 'dog', 'dose_min' => 50, 'dose_max' => 50, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['SID for 3-5 days']],
        ['species' => 'cat', 'dose_min' => 50, 'dose_max' => 50, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['SID for 3-5 days']],
    ],
    'Praziquantel' => [
        ['species' => 'dog', 'dose_min' => 5, 'dose_max' => 7.5, 'dose_unit' => 'mg/kg', 'routes' => ['PO','SC','IM'], 'frequencies' => ['single dose']],
        ['species' => 'cat', 'dose_min' => 5, 'dose_max' => 7.5, 'dose_unit' => 'mg/kg', 'routes' => ['PO','SC','IM'], 'frequencies' => ['single dose']],
    ],
    'Ivermectin' => [
        ['species' => 'dog', 'dose_min' => 0.006, 'dose_max' => 0.6, 'dose_unit' => 'mg/kg', 'routes' => ['PO','SC'], 'frequencies' => ['varies']],
        ['species' => 'cat', 'dose_min' => 0.2, 'dose_max' => 0.3, 'dose_unit' => 'mg/kg', 'routes' => ['SC'], 'frequencies' => ['q2weeks']],
    ],

    // ===== ENDOCRINE =====
    'Levothyroxine' => [
        ['species' => 'dog', 'dose_min' => 10, 'dose_max' => 20, 'dose_unit' => 'mcg/kg', 'routes' => ['PO'], 'frequencies' => ['BID']],
        ['species' => 'cat', 'dose_min' => 50, 'dose_max' => 100, 'dose_unit' => 'mcg/cat', 'routes' => ['PO'], 'frequencies' => ['SID','BID']],
    ],
    'Methimazole' => [
        ['species' => 'cat', 'dose_min' => 1.25, 'dose_max' => 5, 'dose_unit' => 'mg/cat', 'routes' => ['PO'], 'frequencies' => ['BID']],
    ],
    'Trilostane' => [
        ['species' => 'dog', 'dose_min' => 1, 'dose_max' => 3, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['SID','BID']],
    ],

    // ===== ANTI-SEIZURE =====
    'Phenobarbital' => [
        ['species' => 'dog', 'dose_min' => 2.5, 'dose_max' => 5, 'dose_unit' => 'mg/kg', 'routes' => ['PO','IV'], 'frequencies' => ['BID']],
        ['species' => 'cat', 'dose_min' => 1, 'dose_max' => 3, 'dose_unit' => 'mg/kg', 'routes' => ['PO','IV'], 'frequencies' => ['BID']],
    ],
    'Levetiracetam' => [
        ['species' => 'dog', 'dose_min' => 20, 'dose_max' => 60, 'dose_unit' => 'mg/kg', 'routes' => ['PO','IV'], 'frequencies' => ['TID']],
        ['species' => 'cat', 'dose_min' => 20, 'dose_max' => 30, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['TID']],
    ],
    'Potassium Bromide' => [
        ['species' => 'dog', 'dose_min' => 15, 'dose_max' => 40, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['SID']],
    ],

    // ===== IMMUNOSUPPRESSANTS =====
    'Azathioprine' => [
        ['species' => 'dog', 'dose_min' => 1, 'dose_max' => 2, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['SID then EOD']],
    ],
    'Cyclosporine' => [
        ['species' => 'dog', 'dose_min' => 5, 'dose_max' => 10, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['SID']],
        ['species' => 'cat', 'dose_min' => 5, 'dose_max' => 7, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['SID']],
    ],

    // ===== EMERGENCY =====
    'Adrenaline (Epinephrine)' => [
        ['species' => 'dog', 'dose_min' => 0.01, 'dose_max' => 0.02, 'dose_unit' => 'mg/kg', 'routes' => ['IV','IT'], 'frequencies' => ['q3-5min PRN']],
        ['species' => 'cat', 'dose_min' => 0.01, 'dose_max' => 0.02, 'dose_unit' => 'mg/kg', 'routes' => ['IV','IT'], 'frequencies' => ['q3-5min PRN']],
    ],
    'Calcium Gluconate' => [
        ['species' => 'dog', 'dose_min' => 50, 'dose_max' => 150, 'dose_unit' => 'mg/kg', 'routes' => ['IV slowly'], 'frequencies' => ['PRN']],
        ['species' => 'cat', 'dose_min' => 50, 'dose_max' => 150, 'dose_unit' => 'mg/kg', 'routes' => ['IV slowly'], 'frequencies' => ['PRN']],
    ],
    'Dopamine' => [
        ['species' => 'dog', 'dose_min' => 2, 'dose_max' => 10, 'dose_unit' => 'mcg/kg/min', 'routes' => ['IV CRI'], 'frequencies' => ['continuous']],
        ['species' => 'cat', 'dose_min' => 2, 'dose_max' => 10, 'dose_unit' => 'mcg/kg/min', 'routes' => ['IV CRI'], 'frequencies' => ['continuous']],
    ],
    'Mannitol' => [
        ['species' => 'dog', 'dose_min' => 0.5, 'dose_max' => 1.5, 'dose_unit' => 'g/kg', 'routes' => ['IV over 20min'], 'frequencies' => ['q6-8h PRN']],
        ['species' => 'cat', 'dose_min' => 0.25, 'dose_max' => 1, 'dose_unit' => 'g/kg', 'routes' => ['IV over 20min'], 'frequencies' => ['q6-8h PRN']],
    ],
    'Vitamin K1' => [
        ['species' => 'dog', 'dose_min' => 2.5, 'dose_max' => 5, 'dose_unit' => 'mg/kg', 'routes' => ['PO','SC','IM'], 'frequencies' => ['BID']],
        ['species' => 'cat', 'dose_min' => 2.5, 'dose_max' => 5, 'dose_unit' => 'mg/kg', 'routes' => ['PO','SC','IM'], 'frequencies' => ['BID']],
    ],

    // ===== BEHAVIOURAL =====
    'Fluoxetine' => [
        ['species' => 'dog', 'dose_min' => 1, 'dose_max' => 2, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['SID']],
        ['species' => 'cat', 'dose_min' => 0.5, 'dose_max' => 1.5, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['SID']],
    ],
    'Trazodone' => [
        ['species' => 'dog', 'dose_min' => 3, 'dose_max' => 7, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['BID','TID']],
        ['species' => 'cat', 'dose_min' => 3, 'dose_max' => 5, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['SID','BID']],
    ],

    // ===== ANTIFUNGALS =====
    'Itraconazole' => [
        ['species' => 'dog', 'dose_min' => 5, 'dose_max' => 10, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['SID']],
        ['species' => 'cat', 'dose_min' => 5, 'dose_max' => 10, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['SID','alternate weeks']],
    ],
    'Ketoconazole' => [
        ['species' => 'dog', 'dose_min' => 5, 'dose_max' => 10, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['SID','BID']],
        ['species' => 'cat', 'dose_min' => 5, 'dose_max' => 10, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['SID','BID']],
    ],

    // ===== RESPIRATORY =====
    'Aminophylline' => [
        ['species' => 'dog', 'dose_min' => 10, 'dose_max' => 10, 'dose_unit' => 'mg/kg', 'routes' => ['PO','IV slowly'], 'frequencies' => ['TID']],
        ['species' => 'cat', 'dose_min' => 5, 'dose_max' => 5, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['BID']],
    ],
    'Terbutaline' => [
        ['species' => 'dog', 'dose_min' => 1.25, 'dose_max' => 5, 'dose_unit' => 'mg/dog', 'routes' => ['PO'], 'frequencies' => ['BID','TID']],
        ['species' => 'cat', 'dose_min' => 0.1, 'dose_max' => 0.2, 'dose_unit' => 'mg/kg', 'routes' => ['PO','SC'], 'frequencies' => ['BID','TID']],
    ],

    // ===== ECTOPARASITICIDES =====
    'Afoxolaner (NexGard)' => [
        ['species' => 'dog', 'dose_min' => 2.5, 'dose_max' => 6.9, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['monthly']],
    ],
    'Fipronil' => [
        ['species' => 'dog', 'dose_min' => 6.7, 'dose_max' => 6.7, 'dose_unit' => 'mg/kg', 'routes' => ['topical'], 'frequencies' => ['monthly']],
        ['species' => 'cat', 'dose_min' => 6.7, 'dose_max' => 6.7, 'dose_unit' => 'mg/kg', 'routes' => ['topical'], 'frequencies' => ['monthly']],
    ],

    // ===== URINARY =====
    'Prazosin' => [
        ['species' => 'dog', 'dose_min' => 0.5, 'dose_max' => 2, 'dose_unit' => 'mg/dog', 'routes' => ['PO'], 'frequencies' => ['BID','TID']],
        ['species' => 'cat', 'dose_min' => 0.25, 'dose_max' => 0.5, 'dose_unit' => 'mg/cat', 'routes' => ['PO'], 'frequencies' => ['BID','TID']],
    ],
    'Phenoxybenzamine' => [
        ['species' => 'dog', 'dose_min' => 0.25, 'dose_max' => 0.5, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['BID']],
        ['species' => 'cat', 'dose_min' => 0.5, 'dose_max' => 0.5, 'dose_unit' => 'mg/kg', 'routes' => ['PO'], 'frequencies' => ['SID','BID']],
    ],
];

$created = 0;
$updated = 0;
$skipped = 0;
$notFound = [];

foreach ($dosageData as $genericName => $dosages) {
    $generic = DrugGeneric::where('name', $genericName)->first();

    if (!$generic) {
        $notFound[] = $genericName;
        continue;
    }

    foreach ($dosages as $dosage) {
        // Check if dosage already exists for this species
        $existing = DrugDosage::where('generic_id', $generic->id)
            ->where('species', $dosage['species'])
            ->first();

        if ($existing) {
            // Update if dose range is different (BSAVA is more authoritative)
            if ($existing->dose_min != $dosage['dose_min'] || $existing->dose_max != $dosage['dose_max']) {
                $existing->update([
                    'dose_min' => $dosage['dose_min'],
                    'dose_max' => $dosage['dose_max'],
                    'dose_unit' => $dosage['dose_unit'],
                    'routes' => $dosage['routes'],
                    'frequencies' => $dosage['frequencies'],
                ]);
                $updated++;
            } else {
                $skipped++;
            }
        } else {
            DrugDosage::create(array_merge($dosage, ['generic_id' => $generic->id]));
            $created++;
        }
    }
}

echo "\n=== BSAVA DOSAGE SEED RESULTS ===\n";
echo "New dosages created: {$created}\n";
echo "Existing dosages updated: {$updated}\n";
echo "Unchanged (skipped): {$skipped}\n";
echo "Generics not found: " . count($notFound) . "\n";

if (!empty($notFound)) {
    echo "\nMissing generics:\n";
    foreach ($notFound as $name) {
        echo "  - {$name}\n";
    }
}

echo "\nTotal dosages now: " . DrugDosage::count() . "\n";
echo "Done!\n";
