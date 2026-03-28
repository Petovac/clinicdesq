<?php
/**
 * Seed Indian Veterinary Brands for generics that have 0 brands.
 * Run: php artisan tinker database/seeds/seed_indian_vet_brands.php
 *
 * Sources: Publicly available Indian veterinary pharmacopoeia,
 * CDSCO approved drug lists, standard vet practice references.
 */

use App\Models\DrugGeneric;
use App\Models\DrugBrand;

$brandData = [
    // ===== SEDATIVES / ANESTHESIA =====
    'Acepromazine' => [
        ['brand_name' => 'Acevet', 'strength_value' => 10, 'strength_unit' => 'mg/ml', 'form' => 'injection', 'manufacturer' => 'Vetsfarma'],
        ['brand_name' => 'Acepromazine Inj', 'strength_value' => 10, 'strength_unit' => 'mg/ml', 'form' => 'injection', 'manufacturer' => 'Indian Immunologicals'],
    ],
    'Atipamezole' => [
        ['brand_name' => 'Antisedan', 'strength_value' => 5, 'strength_unit' => 'mg/ml', 'form' => 'injection', 'manufacturer' => 'Zoetis'],
    ],
    'Butorphanol' => [
        ['brand_name' => 'Butordol Vet', 'strength_value' => 10, 'strength_unit' => 'mg/ml', 'form' => 'injection', 'manufacturer' => 'Neon Labs'],
        ['brand_name' => 'Torbugesic', 'strength_value' => 10, 'strength_unit' => 'mg/ml', 'form' => 'injection', 'manufacturer' => 'Zoetis'],
    ],
    'Dexmedetomidine' => [
        ['brand_name' => 'Dexdomitor', 'strength_value' => 0.5, 'strength_unit' => 'mg/ml', 'form' => 'injection', 'manufacturer' => 'Zoetis'],
        ['brand_name' => 'Dexmet', 'strength_value' => 0.5, 'strength_unit' => 'mg/ml', 'form' => 'injection', 'manufacturer' => 'Neon Labs'],
    ],
    'Diazepam' => [
        ['brand_name' => 'Calmpose Vet', 'strength_value' => 5, 'strength_unit' => 'mg/ml', 'form' => 'injection', 'manufacturer' => 'Ranbaxy'],
        ['brand_name' => 'Diazepam Inj', 'strength_value' => 10, 'strength_unit' => 'mg/ml', 'form' => 'injection', 'manufacturer' => 'Neon Labs'],
    ],
    'Ketamine' => [
        ['brand_name' => 'Ketavet', 'strength_value' => 50, 'strength_unit' => 'mg/ml', 'form' => 'injection', 'manufacturer' => 'Vetsfarma'],
        ['brand_name' => 'Aneket', 'strength_value' => 50, 'strength_unit' => 'mg/ml', 'form' => 'injection', 'manufacturer' => 'Neon Labs'],
    ],
    'Propofol' => [
        ['brand_name' => 'Propofol Vet', 'strength_value' => 10, 'strength_unit' => 'mg/ml', 'form' => 'injection', 'manufacturer' => 'Neon Labs'],
        ['brand_name' => 'Neorof Vet', 'strength_value' => 10, 'strength_unit' => 'mg/ml', 'form' => 'injection', 'manufacturer' => 'Neon Labs'],
    ],
    'Xylazine' => [
        ['brand_name' => 'Xylaxin', 'strength_value' => 20, 'strength_unit' => 'mg/ml', 'form' => 'injection', 'manufacturer' => 'Indian Immunologicals'],
        ['brand_name' => 'Xylazine Inj', 'strength_value' => 20, 'strength_unit' => 'mg/ml', 'form' => 'injection', 'manufacturer' => 'Vetsfarma'],
    ],

    // ===== ANTIBIOTICS =====
    'Albendazole' => [
        ['brand_name' => 'Albomar', 'strength_value' => 400, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Intas'],
        ['brand_name' => 'Zentel Vet', 'strength_value' => 200, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'GSK'],
        ['brand_name' => 'Albomar Susp', 'strength_value' => 20, 'strength_unit' => 'mg/ml', 'form' => 'suspension', 'manufacturer' => 'Intas'],
    ],
    'Ampicillin' => [
        ['brand_name' => 'Ampivet', 'strength_value' => 500, 'strength_unit' => 'mg', 'form' => 'injection', 'manufacturer' => 'Vetsfarma'],
        ['brand_name' => 'Ampicillin Inj', 'strength_value' => 250, 'strength_unit' => 'mg', 'form' => 'injection', 'manufacturer' => 'Indian Immunologicals'],
    ],
    'Azithromycin' => [
        ['brand_name' => 'Azee', 'strength_value' => 250, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Cipla'],
        ['brand_name' => 'Azee', 'strength_value' => 500, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Cipla'],
        ['brand_name' => 'Azithral', 'strength_value' => 250, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Alembic'],
    ],
    'Clindamycin' => [
        ['brand_name' => 'Clindac', 'strength_value' => 150, 'strength_unit' => 'mg', 'form' => 'capsule', 'manufacturer' => 'Alkem'],
        ['brand_name' => 'Dalacin C', 'strength_value' => 300, 'strength_unit' => 'mg', 'form' => 'capsule', 'manufacturer' => 'Pfizer'],
    ],
    'Doxycycline' => [
        ['brand_name' => 'Doxypet', 'strength_value' => 100, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'TTK Healthcare'],
        ['brand_name' => 'Doxt-SL', 'strength_value' => 100, 'strength_unit' => 'mg', 'form' => 'capsule', 'manufacturer' => 'Dr Reddys'],
        ['brand_name' => 'Doxivet', 'strength_value' => 50, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Vetsfarma'],
    ],
    'Gentamicin' => [
        ['brand_name' => 'Gentavet', 'strength_value' => 40, 'strength_unit' => 'mg/ml', 'form' => 'injection', 'manufacturer' => 'Indian Immunologicals'],
        ['brand_name' => 'Garamycin', 'strength_value' => 80, 'strength_unit' => 'mg/ml', 'form' => 'injection', 'manufacturer' => 'Zydus'],
    ],
    'Marbofloxacin' => [
        ['brand_name' => 'Marbocyl', 'strength_value' => 20, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Vetoquinol'],
        ['brand_name' => 'Marbocyl', 'strength_value' => 80, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Vetoquinol'],
        ['brand_name' => 'Marbocyl Inj', 'strength_value' => 20, 'strength_unit' => 'mg/ml', 'form' => 'injection', 'manufacturer' => 'Vetoquinol'],
    ],
    'Nitrofurazone Ointment' => [
        ['brand_name' => 'Furacin', 'strength_value' => 0.2, 'strength_unit' => '%', 'form' => 'ointment', 'manufacturer' => 'Glaxo'],
    ],

    // ===== CARDIOVASCULAR =====
    'Amlodipine' => [
        ['brand_name' => 'Amlod', 'strength_value' => 5, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Cipla'],
        ['brand_name' => 'Amlod', 'strength_value' => 2.5, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Cipla'],
    ],
    'Atenolol' => [
        ['brand_name' => 'Aten', 'strength_value' => 25, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Zydus'],
        ['brand_name' => 'Aten', 'strength_value' => 50, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Zydus'],
    ],
    'Benazepril' => [
        ['brand_name' => 'Fortekor', 'strength_value' => 5, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Elanco'],
        ['brand_name' => 'Fortekor', 'strength_value' => 2.5, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Elanco'],
    ],
    'Digoxin' => [
        ['brand_name' => 'Digoxin Tab', 'strength_value' => 0.25, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'GSK'],
        ['brand_name' => 'Lanoxin', 'strength_value' => 0.25, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'GSK'],
    ],
    'Enalapril' => [
        ['brand_name' => 'Envas', 'strength_value' => 5, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Cadila'],
        ['brand_name' => 'Envas', 'strength_value' => 2.5, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Cadila'],
    ],
    'Pimobendan' => [
        ['brand_name' => 'Vetmedin', 'strength_value' => 1.25, 'strength_unit' => 'mg', 'form' => 'chewable', 'manufacturer' => 'Boehringer Ingelheim'],
        ['brand_name' => 'Vetmedin', 'strength_value' => 5, 'strength_unit' => 'mg', 'form' => 'chewable', 'manufacturer' => 'Boehringer Ingelheim'],
        ['brand_name' => 'Cardisure', 'strength_value' => 2.5, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Dechra'],
    ],
    'Sildenafil' => [
        ['brand_name' => 'Pulmopres', 'strength_value' => 20, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Cipla'],
    ],

    // ===== NSAIDS / PAIN =====
    'Firocoxib' => [
        ['brand_name' => 'Previcox', 'strength_value' => 57, 'strength_unit' => 'mg', 'form' => 'chewable', 'manufacturer' => 'Boehringer Ingelheim'],
        ['brand_name' => 'Previcox', 'strength_value' => 227, 'strength_unit' => 'mg', 'form' => 'chewable', 'manufacturer' => 'Boehringer Ingelheim'],
    ],
    'Gabapentin' => [
        ['brand_name' => 'Gabapin', 'strength_value' => 100, 'strength_unit' => 'mg', 'form' => 'capsule', 'manufacturer' => 'Intas'],
        ['brand_name' => 'Gabapin', 'strength_value' => 300, 'strength_unit' => 'mg', 'form' => 'capsule', 'manufacturer' => 'Intas'],
    ],
    'Tramadol' => [
        ['brand_name' => 'Tramadol Inj', 'strength_value' => 50, 'strength_unit' => 'mg/ml', 'form' => 'injection', 'manufacturer' => 'Neon Labs'],
        ['brand_name' => 'Contramal', 'strength_value' => 50, 'strength_unit' => 'mg', 'form' => 'capsule', 'manufacturer' => 'Abbott'],
    ],

    // ===== GI DRUGS =====
    'Famotidine' => [
        ['brand_name' => 'Famocid', 'strength_value' => 20, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Sun Pharma'],
        ['brand_name' => 'Famocid', 'strength_value' => 40, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Sun Pharma'],
    ],
    'Lactulose' => [
        ['brand_name' => 'Duphalac', 'strength_value' => 10, 'strength_unit' => 'g/15ml', 'form' => 'syrup', 'manufacturer' => 'Abbott'],
        ['brand_name' => 'Evalac', 'strength_value' => 10, 'strength_unit' => 'g/15ml', 'form' => 'syrup', 'manufacturer' => 'Intas'],
    ],
    'Loperamide' => [
        ['brand_name' => 'Lopamide', 'strength_value' => 2, 'strength_unit' => 'mg', 'form' => 'capsule', 'manufacturer' => 'Cipla'],
    ],
    'Metoclopramide' => [
        ['brand_name' => 'Perinorm', 'strength_value' => 10, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'IPCA'],
        ['brand_name' => 'Perinorm Inj', 'strength_value' => 5, 'strength_unit' => 'mg/ml', 'form' => 'injection', 'manufacturer' => 'IPCA'],
    ],
    'Omeprazole' => [
        ['brand_name' => 'Omez', 'strength_value' => 20, 'strength_unit' => 'mg', 'form' => 'capsule', 'manufacturer' => 'Dr Reddys'],
        ['brand_name' => 'Omez Inj', 'strength_value' => 40, 'strength_unit' => 'mg', 'form' => 'injection', 'manufacturer' => 'Dr Reddys'],
    ],
    'Pantoprazole' => [
        ['brand_name' => 'Pan', 'strength_value' => 40, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Alkem'],
        ['brand_name' => 'Pan IV', 'strength_value' => 40, 'strength_unit' => 'mg', 'form' => 'injection', 'manufacturer' => 'Alkem'],
    ],
    'Sucralfate' => [
        ['brand_name' => 'Sucralfate Susp', 'strength_value' => 1, 'strength_unit' => 'g/10ml', 'form' => 'suspension', 'manufacturer' => 'Abbott'],
        ['brand_name' => 'Sucral', 'strength_value' => 500, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Alkem'],
    ],

    // ===== ANTIHISTAMINES / ALLERGY =====
    'Cetirizine' => [
        ['brand_name' => 'Cetzine', 'strength_value' => 10, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Dr Reddys'],
        ['brand_name' => 'Alerid', 'strength_value' => 10, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Cipla'],
    ],
    'Chlorpheniramine' => [
        ['brand_name' => 'Avil', 'strength_value' => 4, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Sanofi'],
        ['brand_name' => 'Avil Inj', 'strength_value' => 22.75, 'strength_unit' => 'mg/ml', 'form' => 'injection', 'manufacturer' => 'Sanofi'],
    ],
    'Cyproheptadine' => [
        ['brand_name' => 'Ciplactin', 'strength_value' => 4, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Cipla'],
    ],
    'Diphenhydramine' => [
        ['brand_name' => 'Benadryl', 'strength_value' => 25, 'strength_unit' => 'mg', 'form' => 'capsule', 'manufacturer' => 'Johnson & Johnson'],
        ['brand_name' => 'Benadryl Inj', 'strength_value' => 50, 'strength_unit' => 'mg/ml', 'form' => 'injection', 'manufacturer' => 'Pfizer'],
    ],
    'Hydroxyzine' => [
        ['brand_name' => 'Atarax', 'strength_value' => 10, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'UCB'],
        ['brand_name' => 'Atarax', 'strength_value' => 25, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'UCB'],
    ],

    // ===== STEROIDS / IMMUNOSUPPRESSANTS =====
    'Azathioprine' => [
        ['brand_name' => 'Imuran', 'strength_value' => 50, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'GSK'],
        ['brand_name' => 'Azoran', 'strength_value' => 50, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'RPG Life Sciences'],
    ],
    'Betamethasone Cream' => [
        ['brand_name' => 'Betnovate', 'strength_value' => 0.1, 'strength_unit' => '%', 'form' => 'cream', 'manufacturer' => 'GSK'],
    ],
    'Cyclosporine' => [
        ['brand_name' => 'Atopica', 'strength_value' => 25, 'strength_unit' => 'mg', 'form' => 'capsule', 'manufacturer' => 'Elanco'],
        ['brand_name' => 'Atopica', 'strength_value' => 50, 'strength_unit' => 'mg', 'form' => 'capsule', 'manufacturer' => 'Elanco'],
        ['brand_name' => 'Atopica', 'strength_value' => 100, 'strength_unit' => 'mg', 'form' => 'capsule', 'manufacturer' => 'Elanco'],
    ],

    // ===== DERMATOLOGY =====
    'Chlorhexidine Shampoo' => [
        ['brand_name' => 'Hexaclean Shampoo', 'strength_value' => 2, 'strength_unit' => '%', 'form' => 'shampoo', 'manufacturer' => 'Intas'],
        ['brand_name' => 'Sebacil Shampoo', 'strength_value' => 4, 'strength_unit' => '%', 'form' => 'shampoo', 'manufacturer' => 'Bayer'],
    ],
    'Itraconazole' => [
        ['brand_name' => 'Itaspor', 'strength_value' => 100, 'strength_unit' => 'mg', 'form' => 'capsule', 'manufacturer' => 'Cipla'],
        ['brand_name' => 'Candiforce', 'strength_value' => 100, 'strength_unit' => 'mg', 'form' => 'capsule', 'manufacturer' => 'Mankind'],
    ],
    'Ketoconazole' => [
        ['brand_name' => 'Nizoral', 'strength_value' => 200, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Johnson & Johnson'],
        ['brand_name' => 'KZ Shampoo', 'strength_value' => 2, 'strength_unit' => '%', 'form' => 'shampoo', 'manufacturer' => 'Hegde & Hegde'],
    ],
    'Miconazole Shampoo' => [
        ['brand_name' => 'Malaseb Shampoo', 'strength_value' => 2, 'strength_unit' => '%', 'form' => 'shampoo', 'manufacturer' => 'Dechra'],
    ],

    // ===== OPHTHALMIC =====
    'Artificial Tears' => [
        ['brand_name' => 'Refresh Tears', 'strength_value' => 0.5, 'strength_unit' => '%', 'form' => 'eye drops', 'manufacturer' => 'Allergan'],
        ['brand_name' => 'Systane Ultra', 'strength_value' => 0.4, 'strength_unit' => '%', 'form' => 'eye drops', 'manufacturer' => 'Alcon'],
    ],
    'Ciprofloxacin Eye Drops' => [
        ['brand_name' => 'Ciplox Eye', 'strength_value' => 0.3, 'strength_unit' => '%', 'form' => 'eye drops', 'manufacturer' => 'Cipla'],
    ],
    'Clotrimazole Ear Drops' => [
        ['brand_name' => 'Surolan', 'strength_value' => 1, 'strength_unit' => '%', 'form' => 'ear drops', 'manufacturer' => 'Elanco'],
        ['brand_name' => 'Otomax', 'strength_value' => 1, 'strength_unit' => '%', 'form' => 'ear drops', 'manufacturer' => 'MSD'],
    ],
    'Tobramycin Eye Drops' => [
        ['brand_name' => 'Tobrex', 'strength_value' => 0.3, 'strength_unit' => '%', 'form' => 'eye drops', 'manufacturer' => 'Novartis'],
    ],

    // ===== IV FLUIDS / ELECTROLYTES =====
    'Calcium Gluconate' => [
        ['brand_name' => 'Calcium Gluconate Inj', 'strength_value' => 10, 'strength_unit' => '%', 'form' => 'injection', 'manufacturer' => 'Neon Labs'],
    ],
    'Calcium Supplement' => [
        ['brand_name' => 'Ostopet', 'strength_value' => 500, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'TTK Healthcare'],
        ['brand_name' => 'Shelcal', 'strength_value' => 500, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Elder Pharma'],
    ],
    'Dextrose 25%' => [
        ['brand_name' => 'Dextrose 25% Inj', 'strength_value' => 25, 'strength_unit' => '%', 'form' => 'injection', 'manufacturer' => 'Baxter'],
    ],
    'Dextrose 5%' => [
        ['brand_name' => 'DNS (Dextrose Normal Saline)', 'strength_value' => 5, 'strength_unit' => '%', 'form' => 'injection', 'manufacturer' => 'Baxter'],
    ],
    'Normal Saline' => [
        ['brand_name' => 'NS 0.9%', 'strength_value' => 0.9, 'strength_unit' => '%', 'form' => 'injection', 'manufacturer' => 'Baxter'],
    ],
    'Ringer Lactate' => [
        ['brand_name' => 'Ringer Lactate', 'strength_value' => 500, 'strength_unit' => 'ml', 'form' => 'injection', 'manufacturer' => 'Baxter'],
    ],

    // ===== DEWORMERS =====
    'Fenbendazole' => [
        ['brand_name' => 'Panacur', 'strength_value' => 150, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'MSD'],
        ['brand_name' => 'Panacur', 'strength_value' => 500, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'MSD'],
        ['brand_name' => 'Fentas', 'strength_value' => 150, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Intas'],
    ],
    'Praziquantel' => [
        ['brand_name' => 'Prazivet Plus', 'strength_value' => 50, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Virbac'],
        ['brand_name' => 'Drontal Plus', 'strength_value' => 50, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Bayer'],
    ],
    'Pyrantel Pamoate' => [
        ['brand_name' => 'Drontal Puppy', 'strength_value' => 15, 'strength_unit' => 'mg/ml', 'form' => 'suspension', 'manufacturer' => 'Bayer'],
        ['brand_name' => 'Nemocid', 'strength_value' => 250, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Pfizer'],
    ],

    // ===== ECTOPARASITICIDES =====
    'Fipronil' => [
        ['brand_name' => 'Frontline Plus', 'strength_value' => 9.8, 'strength_unit' => '%', 'form' => 'spot-on', 'manufacturer' => 'Boehringer Ingelheim'],
        ['brand_name' => 'Fiprofort Plus', 'strength_value' => 9.8, 'strength_unit' => '%', 'form' => 'spot-on', 'manufacturer' => 'Savavet'],
    ],
    'Ivermectin' => [
        ['brand_name' => 'Ivermectin Inj', 'strength_value' => 10, 'strength_unit' => 'mg/ml', 'form' => 'injection', 'manufacturer' => 'Indian Immunologicals'],
        ['brand_name' => 'Hitek Inj', 'strength_value' => 10, 'strength_unit' => 'mg/ml', 'form' => 'injection', 'manufacturer' => 'Venkys'],
        ['brand_name' => 'Ivermectin Tab', 'strength_value' => 3, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Intas'],
    ],

    // ===== ENDOCRINE =====
    'Levothyroxine' => [
        ['brand_name' => 'Thyrovet', 'strength_value' => 0.1, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Vetsfarma'],
        ['brand_name' => 'Thyronorm', 'strength_value' => 50, 'strength_unit' => 'mcg', 'form' => 'tablet', 'manufacturer' => 'Abbott'],
        ['brand_name' => 'Thyronorm', 'strength_value' => 100, 'strength_unit' => 'mcg', 'form' => 'tablet', 'manufacturer' => 'Abbott'],
    ],
    'Methimazole' => [
        ['brand_name' => 'Felimazole', 'strength_value' => 2.5, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Dechra'],
        ['brand_name' => 'Felimazole', 'strength_value' => 5, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Dechra'],
    ],
    'Trilostane' => [
        ['brand_name' => 'Vetoryl', 'strength_value' => 30, 'strength_unit' => 'mg', 'form' => 'capsule', 'manufacturer' => 'Dechra'],
        ['brand_name' => 'Vetoryl', 'strength_value' => 60, 'strength_unit' => 'mg', 'form' => 'capsule', 'manufacturer' => 'Dechra'],
    ],

    // ===== EMERGENCY / CRITICAL =====
    'Adrenaline (Epinephrine)' => [
        ['brand_name' => 'Adrenaline Inj', 'strength_value' => 1, 'strength_unit' => 'mg/ml', 'form' => 'injection', 'manufacturer' => 'Neon Labs'],
    ],
    'Dopamine' => [
        ['brand_name' => 'Dopamine Inj', 'strength_value' => 40, 'strength_unit' => 'mg/ml', 'form' => 'injection', 'manufacturer' => 'Neon Labs'],
    ],
    'Furosemide' => [
        ['brand_name' => 'Lasix', 'strength_value' => 40, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Sanofi'],
        ['brand_name' => 'Lasix Inj', 'strength_value' => 20, 'strength_unit' => 'mg/ml', 'form' => 'injection', 'manufacturer' => 'Sanofi'],
        ['brand_name' => 'Frusamide', 'strength_value' => 10, 'strength_unit' => 'mg/ml', 'form' => 'injection', 'manufacturer' => 'Cadila'],
    ],
    'Mannitol' => [
        ['brand_name' => 'Mannitol 20%', 'strength_value' => 20, 'strength_unit' => '%', 'form' => 'injection', 'manufacturer' => 'Baxter'],
    ],
    'Phenylephrine' => [
        ['brand_name' => 'Phenylephrine Inj', 'strength_value' => 10, 'strength_unit' => 'mg/ml', 'form' => 'injection', 'manufacturer' => 'Neon Labs'],
    ],
    'Vitamin K1' => [
        ['brand_name' => 'Phytonadione Inj', 'strength_value' => 10, 'strength_unit' => 'mg/ml', 'form' => 'injection', 'manufacturer' => 'Neon Labs'],
        ['brand_name' => 'Kenadion', 'strength_value' => 10, 'strength_unit' => 'mg/ml', 'form' => 'injection', 'manufacturer' => 'Roche'],
    ],

    // ===== SUPPLEMENTS =====
    'Liver Supplement' => [
        ['brand_name' => 'Liv 52 Vet', 'strength_value' => 375, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Himalaya'],
        ['brand_name' => 'Liv 52 Vet Liquid', 'strength_value' => 100, 'strength_unit' => 'ml', 'form' => 'syrup', 'manufacturer' => 'Himalaya'],
    ],
    'Multivitamin' => [
        ['brand_name' => 'Nutri-Coat Advance', 'strength_value' => 200, 'strength_unit' => 'ml', 'form' => 'syrup', 'manufacturer' => 'MPPL'],
        ['brand_name' => 'Petvit Power', 'strength_value' => 60, 'strength_unit' => 'ml', 'form' => 'syrup', 'manufacturer' => 'Petcare'],
    ],
    'Omega-3 Fatty Acids' => [
        ['brand_name' => 'Nutri-Coat', 'strength_value' => 200, 'strength_unit' => 'ml', 'form' => 'syrup', 'manufacturer' => 'MPPL'],
        ['brand_name' => 'Fur Magic', 'strength_value' => 200, 'strength_unit' => 'ml', 'form' => 'syrup', 'manufacturer' => 'Vivaldis'],
    ],
    'Probiotics' => [
        ['brand_name' => 'Gutwell', 'strength_value' => 10, 'strength_unit' => 'g', 'form' => 'sachet', 'manufacturer' => 'TTK Healthcare'],
        ['brand_name' => 'Enterogermina', 'strength_value' => 2, 'strength_unit' => 'billion spores', 'form' => 'vial', 'manufacturer' => 'Sanofi'],
    ],

    // ===== RESPIRATORY =====
    'Aminophylline' => [
        ['brand_name' => 'Aminophylline Inj', 'strength_value' => 25, 'strength_unit' => 'mg/ml', 'form' => 'injection', 'manufacturer' => 'Neon Labs'],
    ],
    'Terbutaline' => [
        ['brand_name' => 'Bricanyl', 'strength_value' => 2.5, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'AstraZeneca'],
        ['brand_name' => 'Bricanyl Inj', 'strength_value' => 0.5, 'strength_unit' => 'mg/ml', 'form' => 'injection', 'manufacturer' => 'AstraZeneca'],
    ],

    // ===== ANTI-SEIZURE =====
    'Levetiracetam' => [
        ['brand_name' => 'Levera', 'strength_value' => 250, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Sun Pharma'],
        ['brand_name' => 'Levera', 'strength_value' => 500, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Sun Pharma'],
    ],
    'Phenobarbital' => [
        ['brand_name' => 'Luminal', 'strength_value' => 30, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Abbott'],
        ['brand_name' => 'Luminal', 'strength_value' => 60, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Abbott'],
        ['brand_name' => 'Phenobarbitone Inj', 'strength_value' => 200, 'strength_unit' => 'mg/ml', 'form' => 'injection', 'manufacturer' => 'Neon Labs'],
    ],
    'Potassium Bromide' => [
        ['brand_name' => 'KBr Solution', 'strength_value' => 250, 'strength_unit' => 'mg/ml', 'form' => 'solution', 'manufacturer' => 'Compounded'],
    ],

    // ===== URINARY =====
    'Phenoxybenzamine' => [
        ['brand_name' => 'Fenoxene', 'strength_value' => 10, 'strength_unit' => 'mg', 'form' => 'capsule', 'manufacturer' => 'Glaxo'],
    ],
    'Prazosin' => [
        ['brand_name' => 'Minipress', 'strength_value' => 1, 'strength_unit' => 'mg', 'form' => 'capsule', 'manufacturer' => 'Pfizer'],
        ['brand_name' => 'Minipress', 'strength_value' => 2, 'strength_unit' => 'mg', 'form' => 'capsule', 'manufacturer' => 'Pfizer'],
    ],
    'Spironolactone' => [
        ['brand_name' => 'Aldactone', 'strength_value' => 25, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'RPG Life Sciences'],
        ['brand_name' => 'Aldactone', 'strength_value' => 50, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'RPG Life Sciences'],
    ],

    // ===== BEHAVIOURAL =====
    'Fluoxetine' => [
        ['brand_name' => 'Flunil', 'strength_value' => 20, 'strength_unit' => 'mg', 'form' => 'capsule', 'manufacturer' => 'Intas'],
        ['brand_name' => 'Reconcile', 'strength_value' => 8, 'strength_unit' => 'mg', 'form' => 'chewable', 'manufacturer' => 'Elanco'],
    ],
    'Trazodone' => [
        ['brand_name' => 'Trazodone Tab', 'strength_value' => 50, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Sun Pharma'],
        ['brand_name' => 'Trazodone Tab', 'strength_value' => 100, 'strength_unit' => 'mg', 'form' => 'tablet', 'manufacturer' => 'Sun Pharma'],
    ],
];

$created = 0;
$skipped = 0;
$notFound = [];

foreach ($brandData as $genericName => $brands) {
    $generic = DrugGeneric::where('name', $genericName)->first();

    if (!$generic) {
        $notFound[] = $genericName;
        continue;
    }

    foreach ($brands as $brand) {
        // Check if brand already exists
        $exists = DrugBrand::where('generic_id', $generic->id)
            ->where('brand_name', $brand['brand_name'])
            ->where('strength_value', $brand['strength_value'])
            ->exists();

        if ($exists) {
            $skipped++;
            continue;
        }

        DrugBrand::create(array_merge($brand, ['generic_id' => $generic->id]));
        $created++;
    }
}

echo "\n=== SEED RESULTS ===\n";
echo "Brands created: {$created}\n";
echo "Duplicates skipped: {$skipped}\n";
echo "Generics not found in DB: " . count($notFound) . "\n";

if (!empty($notFound)) {
    echo "\nMissing generics (need to add):\n";
    foreach ($notFound as $name) {
        echo "  - {$name}\n";
    }
}

echo "\nTotal brands now: " . DrugBrand::count() . "\n";
echo "Done!\n";
