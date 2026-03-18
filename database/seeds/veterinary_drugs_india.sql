-- ============================================================
-- COMPREHENSIVE VETERINARY DRUG DATABASE FOR INDIA
-- ClinicDesq — drug_generics, drug_brands, drug_dosages
-- ============================================================
-- Run this on your live database after backing up.
-- This covers injectable, oral, topical drugs commonly used in
-- small animal practice in India (dogs, cats, rabbits, birds).
-- ============================================================

SET NAMES utf8mb4;
SET time_zone = '+05:30';

-- ============================================================
-- 1. DRUG GENERICS
-- ============================================================
INSERT INTO `drug_generics` (`id`, `name`, `drug_class`, `default_dose_unit`, `created_by`, `created_at`, `updated_at`) VALUES
-- ANTIBIOTICS
(101, 'Amoxicillin', 'Penicillin', 'mg/kg', 1, NOW(), NOW()),
(102, 'Amoxicillin + Clavulanate', 'Penicillin + BLI', 'mg/kg', 1, NOW(), NOW()),
(103, 'Ampicillin', 'Penicillin', 'mg/kg', 1, NOW(), NOW()),
(104, 'Cephalexin', 'Cephalosporin (1st gen)', 'mg/kg', 1, NOW(), NOW()),
(105, 'Cefpodoxime', 'Cephalosporin (3rd gen)', 'mg/kg', 1, NOW(), NOW()),
(106, 'Ceftriaxone', 'Cephalosporin (3rd gen)', 'mg/kg', 1, NOW(), NOW()),
(107, 'Enrofloxacin', 'Fluoroquinolone', 'mg/kg', 1, NOW(), NOW()),
(108, 'Marbofloxacin', 'Fluoroquinolone', 'mg/kg', 1, NOW(), NOW()),
(109, 'Metronidazole', 'Nitroimidazole', 'mg/kg', 1, NOW(), NOW()),
(110, 'Doxycycline', 'Tetracycline', 'mg/kg', 1, NOW(), NOW()),
(111, 'Azithromycin', 'Macrolide', 'mg/kg', 1, NOW(), NOW()),
(112, 'Clindamycin', 'Lincosamide', 'mg/kg', 1, NOW(), NOW()),
(113, 'Gentamicin', 'Aminoglycoside', 'mg/kg', 1, NOW(), NOW()),
(114, 'Trimethoprim-Sulfamethoxazole', 'Sulfonamide', 'mg/kg', 1, NOW(), NOW()),
(115, 'Lincomycin', 'Lincosamide', 'mg/kg', 1, NOW(), NOW()),

-- NSAIDs & ANALGESICS
(120, 'Meloxicam', 'NSAID', 'mg/kg', 1, NOW(), NOW()),
(121, 'Carprofen', 'NSAID', 'mg/kg', 1, NOW(), NOW()),
(122, 'Tolfenamic Acid', 'NSAID', 'mg/kg', 1, NOW(), NOW()),
(123, 'Firocoxib', 'COX-2 Inhibitor', 'mg/kg', 1, NOW(), NOW()),
(124, 'Tramadol', 'Opioid Analgesic', 'mg/kg', 1, NOW(), NOW()),
(125, 'Butorphanol', 'Opioid Analgesic', 'mg/kg', 1, NOW(), NOW()),
(126, 'Paracetamol (Dogs only)', 'Analgesic/Antipyretic', 'mg/kg', 1, NOW(), NOW()),
(127, 'Metamizole (Dipyrone)', 'Analgesic/Antipyretic', 'mg/kg', 1, NOW(), NOW()),

-- CORTICOSTEROIDS
(130, 'Prednisolone', 'Corticosteroid', 'mg/kg', 1, NOW(), NOW()),
(131, 'Dexamethasone', 'Corticosteroid', 'mg/kg', 1, NOW(), NOW()),
(132, 'Methylprednisolone', 'Corticosteroid', 'mg/kg', 1, NOW(), NOW()),
(133, 'Triamcinolone', 'Corticosteroid', 'mg/kg', 1, NOW(), NOW()),

-- ANTIEMETICS
(140, 'Ondansetron', 'Antiemetic (5-HT3)', 'mg/kg', 1, NOW(), NOW()),
(141, 'Metoclopramide', 'Antiemetic/Prokinetic', 'mg/kg', 1, NOW(), NOW()),
(142, 'Maropitant (Cerenia)', 'NK1 Antagonist', 'mg/kg', 1, NOW(), NOW()),
(143, 'Domperidone', 'Antiemetic/Prokinetic', 'mg/kg', 1, NOW(), NOW()),

-- ANTIDIARRHEALS & GI
(150, 'Ranitidine', 'H2 Blocker', 'mg/kg', 1, NOW(), NOW()),
(151, 'Omeprazole', 'Proton Pump Inhibitor', 'mg/kg', 1, NOW(), NOW()),
(152, 'Pantoprazole', 'Proton Pump Inhibitor', 'mg/kg', 1, NOW(), NOW()),
(153, 'Sucralfate', 'Mucosal Protectant', 'mg/kg', 1, NOW(), NOW()),
(154, 'Loperamide', 'Antidiarrheal', 'mg/kg', 1, NOW(), NOW()),
(155, 'Metoclopramide', 'Prokinetic', 'mg/kg', 1, NOW(), NOW()),

-- ANTIPARASITICS
(160, 'Ivermectin', 'Avermectin', 'mcg/kg', 1, NOW(), NOW()),
(161, 'Fenbendazole', 'Benzimidazole', 'mg/kg', 1, NOW(), NOW()),
(162, 'Praziquantel', 'Cestocide', 'mg/kg', 1, NOW(), NOW()),
(163, 'Pyrantel Pamoate', 'Nematocide', 'mg/kg', 1, NOW(), NOW()),
(164, 'Albendazole', 'Benzimidazole', 'mg/kg', 1, NOW(), NOW()),
(165, 'Fipronil', 'Ectoparasiticide', 'mg/kg', 1, NOW(), NOW()),
(166, 'Imidacloprid', 'Ectoparasiticide', 'mg/kg', 1, NOW(), NOW()),
(167, 'Afoxolaner (NexGard)', 'Isoxazoline', 'mg/kg', 1, NOW(), NOW()),
(168, 'Fluralaner (Bravecto)', 'Isoxazoline', 'mg/kg', 1, NOW(), NOW()),
(169, 'Sarolaner (Simparica)', 'Isoxazoline', 'mg/kg', 1, NOW(), NOW()),
(170, 'Selamectin', 'Avermectin', 'mg/kg', 1, NOW(), NOW()),

-- CARDIAC
(180, 'Enalapril', 'ACE Inhibitor', 'mg/kg', 1, NOW(), NOW()),
(181, 'Benazepril', 'ACE Inhibitor', 'mg/kg', 1, NOW(), NOW()),
(182, 'Pimobendan', 'Inodilator', 'mg/kg', 1, NOW(), NOW()),
(183, 'Furosemide', 'Loop Diuretic', 'mg/kg', 1, NOW(), NOW()),
(184, 'Spironolactone', 'K-Sparing Diuretic', 'mg/kg', 1, NOW(), NOW()),
(185, 'Amlodipine', 'Ca Channel Blocker', 'mg/kg', 1, NOW(), NOW()),
(186, 'Atenolol', 'Beta Blocker', 'mg/kg', 1, NOW(), NOW()),
(187, 'Digoxin', 'Cardiac Glycoside', 'mg/kg', 1, NOW(), NOW()),
(188, 'Diltiazem', 'Ca Channel Blocker', 'mg/kg', 1, NOW(), NOW()),

-- ANTIHISTAMINES
(190, 'Chlorpheniramine', 'H1 Blocker', 'mg/kg', 1, NOW(), NOW()),
(191, 'Cetirizine', 'H1 Blocker (2nd gen)', 'mg/kg', 1, NOW(), NOW()),
(192, 'Diphenhydramine', 'H1 Blocker', 'mg/kg', 1, NOW(), NOW()),
(193, 'Hydroxyzine', 'H1 Blocker', 'mg/kg', 1, NOW(), NOW()),
(194, 'Cyproheptadine', 'Serotonin Antagonist', 'mg/kg', 1, NOW(), NOW()),

-- ANTIFUNGALS
(200, 'Ketoconazole', 'Azole Antifungal', 'mg/kg', 1, NOW(), NOW()),
(201, 'Itraconazole', 'Azole Antifungal', 'mg/kg', 1, NOW(), NOW()),
(202, 'Fluconazole', 'Azole Antifungal', 'mg/kg', 1, NOW(), NOW()),
(203, 'Griseofulvin', 'Antifungal', 'mg/kg', 1, NOW(), NOW()),
(204, 'Terbinafine', 'Allylamine Antifungal', 'mg/kg', 1, NOW(), NOW()),

-- SEDATIVES & ANESTHETICS
(210, 'Xylazine', 'Alpha-2 Agonist', 'mg/kg', 1, NOW(), NOW()),
(211, 'Medetomidine', 'Alpha-2 Agonist', 'mcg/kg', 1, NOW(), NOW()),
(212, 'Dexmedetomidine', 'Alpha-2 Agonist', 'mcg/kg', 1, NOW(), NOW()),
(213, 'Atipamezole', 'Alpha-2 Antagonist', 'mg/kg', 1, NOW(), NOW()),
(214, 'Diazepam', 'Benzodiazepine', 'mg/kg', 1, NOW(), NOW()),
(215, 'Midazolam', 'Benzodiazepine', 'mg/kg', 1, NOW(), NOW()),
(216, 'Acepromazine', 'Phenothiazine', 'mg/kg', 1, NOW(), NOW()),
(217, 'Ketamine', 'Dissociative Anesthetic', 'mg/kg', 1, NOW(), NOW()),
(218, 'Propofol', 'IV Anesthetic', 'mg/kg', 1, NOW(), NOW()),
(219, 'Isoflurane', 'Inhalation Anesthetic', '%', 1, NOW(), NOW()),
(220, 'Atropine', 'Anticholinergic', 'mg/kg', 1, NOW(), NOW()),
(221, 'Glycopyrrolate', 'Anticholinergic', 'mg/kg', 1, NOW(), NOW()),

-- ANTICONVULSANTS
(230, 'Phenobarbital', 'Barbiturate', 'mg/kg', 1, NOW(), NOW()),
(231, 'Potassium Bromide', 'Anticonvulsant', 'mg/kg', 1, NOW(), NOW()),
(232, 'Levetiracetam', 'Anticonvulsant', 'mg/kg', 1, NOW(), NOW()),
(233, 'Zonisamide', 'Anticonvulsant', 'mg/kg', 1, NOW(), NOW()),

-- ENDOCRINE
(240, 'Methimazole', 'Antithyroid', 'mg/cat', 1, NOW(), NOW()),
(241, 'Levothyroxine', 'Thyroid Supplement', 'mcg/kg', 1, NOW(), NOW()),
(242, 'Trilostane', 'Adrenal Inhibitor', 'mg/kg', 1, NOW(), NOW()),
(243, 'Insulin (NPH)', 'Hypoglycemic', 'IU/kg', 1, NOW(), NOW()),
(244, 'Insulin Glargine', 'Hypoglycemic', 'IU/cat', 1, NOW(), NOW()),

-- HEPATO / RENAL SUPPORT
(250, 'Ursodeoxycholic Acid', 'Hepatoprotectant', 'mg/kg', 1, NOW(), NOW()),
(251, 'S-Adenosylmethionine (SAMe)', 'Hepatoprotectant', 'mg/kg', 1, NOW(), NOW()),
(252, 'Silymarin', 'Hepatoprotectant', 'mg/kg', 1, NOW(), NOW()),

-- IMMUNOSUPPRESSANTS
(260, 'Cyclosporine', 'Calcineurin Inhibitor', 'mg/kg', 1, NOW(), NOW()),
(261, 'Azathioprine', 'Immunosuppressant', 'mg/kg', 1, NOW(), NOW()),
(262, 'Mycophenolate', 'Immunosuppressant', 'mg/kg', 1, NOW(), NOW()),
(263, 'Oclacitinib (Apoquel)', 'JAK Inhibitor', 'mg/kg', 1, NOW(), NOW()),

-- EMERGENCY / CRITICAL CARE
(270, 'Adrenaline (Epinephrine)', 'Sympathomimetic', 'mg/kg', 1, NOW(), NOW()),
(271, 'Dopamine', 'Vasopressor', 'mcg/kg/min', 1, NOW(), NOW()),
(272, 'Dobutamine', 'Inotrope', 'mcg/kg/min', 1, NOW(), NOW()),
(273, 'Mannitol', 'Osmotic Diuretic', 'g/kg', 1, NOW(), NOW()),
(274, 'Calcium Gluconate', 'Electrolyte', 'ml/kg', 1, NOW(), NOW()),
(275, 'Dextrose 25%', 'Glucose', 'ml/kg', 1, NOW(), NOW()),
(276, 'Sodium Bicarbonate', 'Alkaliniser', 'mEq/kg', 1, NOW(), NOW()),
(277, 'Naloxone', 'Opioid Antagonist', 'mg/kg', 1, NOW(), NOW()),

-- IV FLUIDS
(280, 'Normal Saline (0.9% NaCl)', 'Crystalloid', 'ml/kg/hr', 1, NOW(), NOW()),
(281, 'Ringer Lactate', 'Crystalloid', 'ml/kg/hr', 1, NOW(), NOW()),
(282, 'Dextrose 5%', 'Crystalloid', 'ml/kg/hr', 1, NOW(), NOW()),
(283, 'Dextrose Normal Saline', 'Crystalloid', 'ml/kg/hr', 1, NOW(), NOW()),

-- SUPPLEMENTS
(290, 'Multivitamin Syrup', 'Supplement', 'ml', 1, NOW(), NOW()),
(291, 'Iron Supplement', 'Supplement', 'mg/kg', 1, NOW(), NOW()),
(292, 'Calcium Supplement', 'Supplement', 'mg/kg', 1, NOW(), NOW()),
(293, 'Omega-3 Fatty Acids', 'Supplement', 'mg/kg', 1, NOW(), NOW()),
(294, 'Probiotics', 'Supplement', 'CFU', 1, NOW(), NOW()),
(295, 'L-Lysine (Cats)', 'Supplement', 'mg/cat', 1, NOW(), NOW()),
(296, 'Glucosamine + Chondroitin', 'Joint Supplement', 'mg/kg', 1, NOW(), NOW()),

-- DERMATOLOGICAL (TOPICAL)
(300, 'Miconazole Shampoo', 'Topical Antifungal', 'topical', 1, NOW(), NOW()),
(301, 'Chlorhexidine Shampoo', 'Topical Antiseptic', 'topical', 1, NOW(), NOW()),
(302, 'Mupirocin Ointment', 'Topical Antibiotic', 'topical', 1, NOW(), NOW()),
(303, 'Silver Sulfadiazine', 'Topical Antibiotic', 'topical', 1, NOW(), NOW()),
(304, 'Betamethasone Cream', 'Topical Steroid', 'topical', 1, NOW(), NOW()),

-- OPHTHALMIC
(310, 'Ciprofloxacin Eye Drops', 'Ophthalmic Antibiotic', 'drops', 1, NOW(), NOW()),
(311, 'Tobramycin Eye Drops', 'Ophthalmic Antibiotic', 'drops', 1, NOW(), NOW()),
(312, 'Artificial Tears', 'Ophthalmic Lubricant', 'drops', 1, NOW(), NOW()),
(313, 'Dorzolamide Eye Drops', 'Antiglaucoma', 'drops', 1, NOW(), NOW()),
(314, 'Timolol Eye Drops', 'Antiglaucoma', 'drops', 1, NOW(), NOW()),
(315, 'Prednisolone Acetate Eye Drops', 'Ophthalmic Steroid', 'drops', 1, NOW(), NOW()),

-- OTIC
(320, 'Clotrimazole Ear Drops', 'Otic Antifungal', 'drops', 1, NOW(), NOW()),
(321, 'Gentamicin + Clotrimazole + Beclomethasone Ear Drops', 'Otic Combo', 'drops', 1, NOW(), NOW());

-- ============================================================
-- 2. DRUG BRANDS (Indian market)
-- ============================================================
INSERT INTO `drug_brands` (`id`, `generic_id`, `brand_name`, `strength_value`, `strength_unit`, `pack_size`, `strength`, `form`, `manufacturer`, `created_at`, `updated_at`, `pack_unit`) VALUES
-- Amoxicillin
(101, 101, 'Moxivet', '250.00', 'mg', '10.00', '250mg', 'tablet', 'Indian Immunologicals', NOW(), NOW(), 'tabs'),
(102, 101, 'Moxivet', '500.00', 'mg', '10.00', '500mg', 'tablet', 'Indian Immunologicals', NOW(), NOW(), 'tabs'),
(103, 101, 'Moxivet Dry Syrup', '125.00', 'mg/5ml', '30.00', '125mg/5ml', 'oral_suspension', 'Indian Immunologicals', NOW(), NOW(), 'ml'),

-- Amoxicillin + Clavulanate
(110, 102, 'Intamox-CV', '250.00', 'mg', '10.00', '250+62.5mg', 'tablet', 'Intas', NOW(), NOW(), 'tabs'),
(111, 102, 'Intamox-CV', '500.00', 'mg', '10.00', '500+125mg', 'tablet', 'Intas', NOW(), NOW(), 'tabs'),
(112, 102, 'Clavpet', '250.00', 'mg', '10.00', '250+62.5mg', 'tablet', 'Sava Vet', NOW(), NOW(), 'tabs'),
(113, 102, 'Clavet Inj', '600.00', 'mg', '1.00', '500+100mg', 'injection', 'Sava Vet', NOW(), NOW(), 'vial'),

-- Cephalexin
(120, 104, 'Sporidex', '250.00', 'mg', '10.00', '250mg', 'capsule', 'Sun Pharma', NOW(), NOW(), 'caps'),
(121, 104, 'Sporidex', '500.00', 'mg', '10.00', '500mg', 'capsule', 'Sun Pharma', NOW(), NOW(), 'caps'),

-- Cefpodoxime
(125, 105, 'Cefpo-Vet', '100.00', 'mg', '10.00', '100mg', 'tablet', 'Intas', NOW(), NOW(), 'tabs'),
(126, 105, 'Cefpo-Vet', '200.00', 'mg', '10.00', '200mg', 'tablet', 'Intas', NOW(), NOW(), 'tabs'),

-- Ceftriaxone
(130, 106, 'Intacef', '250.00', 'mg', '1.00', '250mg', 'injection', 'Intas', NOW(), NOW(), 'vial'),
(131, 106, 'Intacef', '500.00', 'mg', '1.00', '500mg', 'injection', 'Intas', NOW(), NOW(), 'vial'),
(132, 106, 'Intacef', '1000.00', 'mg', '1.00', '1g', 'injection', 'Intas', NOW(), NOW(), 'vial'),
(133, 106, 'Monocef', '500.00', 'mg', '1.00', '500mg', 'injection', 'Aristo', NOW(), NOW(), 'vial'),

-- Enrofloxacin
(140, 107, 'Enrovet', '50.00', 'mg', '10.00', '50mg', 'tablet', 'Indian Immunologicals', NOW(), NOW(), 'tabs'),
(141, 107, 'Enrovet', '150.00', 'mg', '10.00', '150mg', 'tablet', 'Indian Immunologicals', NOW(), NOW(), 'tabs'),
(142, 107, 'Enroflox Inj', '100.00', 'mg/ml', '30.00', '100mg/ml', 'injection', 'Indian Immunologicals', NOW(), NOW(), 'ml'),
(143, 107, 'Bayrocin', '50.00', 'mg', '10.00', '50mg', 'tablet', 'Bayer', NOW(), NOW(), 'tabs'),

-- Metronidazole
(150, 109, 'Metrogyl', '200.00', 'mg', '20.00', '200mg', 'tablet', 'J&J', NOW(), NOW(), 'tabs'),
(151, 109, 'Metrogyl', '400.00', 'mg', '20.00', '400mg', 'tablet', 'J&J', NOW(), NOW(), 'tabs'),
(152, 109, 'Metrogyl IV', '5.00', 'mg/ml', '100.00', '500mg/100ml', 'injection', 'J&J', NOW(), NOW(), 'ml'),
(153, 109, 'Metrogyl Syrup', '200.00', 'mg/5ml', '60.00', '200mg/5ml', 'oral_suspension', 'J&J', NOW(), NOW(), 'ml'),

-- Doxycycline
(160, 110, 'Doxypet', '100.00', 'mg', '10.00', '100mg', 'capsule', 'Sava Vet', NOW(), NOW(), 'caps'),
(161, 110, 'Doxypet', '300.00', 'mg', '10.00', '300mg', 'tablet', 'Sava Vet', NOW(), NOW(), 'tabs'),

-- Meloxicam
(170, 120, 'Melonex', '1.50', 'mg/ml', '10.00', '1.5mg/ml', 'oral_suspension', 'Intas', NOW(), NOW(), 'ml'),
(171, 120, 'Melonex', '4.00', 'mg', '10.00', '4mg', 'tablet', 'Intas', NOW(), NOW(), 'tabs'),
(172, 120, 'Meloxicam Inj', '5.00', 'mg/ml', '10.00', '5mg/ml', 'injection', 'Various', NOW(), NOW(), 'ml'),

-- Carprofen
(175, 121, 'Rimadyl', '25.00', 'mg', '20.00', '25mg', 'tablet', 'Zoetis', NOW(), NOW(), 'tabs'),
(176, 121, 'Rimadyl', '75.00', 'mg', '20.00', '75mg', 'tablet', 'Zoetis', NOW(), NOW(), 'tabs'),
(177, 121, 'Rimadyl Inj', '50.00', 'mg/ml', '20.00', '50mg/ml', 'injection', 'Zoetis', NOW(), NOW(), 'ml'),

-- Prednisolone
(180, 130, 'Wysolone', '5.00', 'mg', '30.00', '5mg', 'tablet', 'Pfizer', NOW(), NOW(), 'tabs'),
(181, 130, 'Wysolone', '10.00', 'mg', '10.00', '10mg', 'tablet', 'Pfizer', NOW(), NOW(), 'tabs'),
(182, 130, 'Wysolone', '20.00', 'mg', '10.00', '20mg', 'tablet', 'Pfizer', NOW(), NOW(), 'tabs'),

-- Dexamethasone
(185, 131, 'Dexona', '0.50', 'mg', '20.00', '0.5mg', 'tablet', 'Zydus', NOW(), NOW(), 'tabs'),
(186, 131, 'Dexona Inj', '4.00', 'mg/ml', '1.00', '4mg/ml', 'injection', 'Zydus', NOW(), NOW(), 'ml'),
(187, 131, 'Decdan Inj', '8.00', 'mg/2ml', '1.00', '8mg/2ml', 'injection', 'Zydus', NOW(), NOW(), 'ampoule'),

-- Ondansetron
(190, 140, 'Emeset', '4.00', 'mg', '10.00', '4mg', 'tablet', 'Cipla', NOW(), NOW(), 'tabs'),
(191, 140, 'Emeset Inj', '2.00', 'mg/ml', '2.00', '4mg/2ml', 'injection', 'Cipla', NOW(), NOW(), 'ampoule'),

-- Maropitant
(195, 142, 'Cerenia', '16.00', 'mg', '4.00', '16mg', 'tablet', 'Zoetis', NOW(), NOW(), 'tabs'),
(196, 142, 'Cerenia', '24.00', 'mg', '4.00', '24mg', 'tablet', 'Zoetis', NOW(), NOW(), 'tabs'),
(197, 142, 'Cerenia Inj', '10.00', 'mg/ml', '20.00', '10mg/ml', 'injection', 'Zoetis', NOW(), NOW(), 'ml'),

-- Omeprazole
(200, 151, 'Omez', '10.00', 'mg', '10.00', '10mg', 'capsule', 'Dr Reddy', NOW(), NOW(), 'caps'),
(201, 151, 'Omez', '20.00', 'mg', '10.00', '20mg', 'capsule', 'Dr Reddy', NOW(), NOW(), 'caps'),
(202, 151, 'Omez IV', '40.00', 'mg', '1.00', '40mg', 'injection', 'Dr Reddy', NOW(), NOW(), 'vial'),

-- Pantoprazole
(205, 152, 'Pan-40', '40.00', 'mg', '10.00', '40mg', 'tablet', 'Alkem', NOW(), NOW(), 'tabs'),
(206, 152, 'Pantocid IV', '40.00', 'mg', '1.00', '40mg', 'injection', 'Sun Pharma', NOW(), NOW(), 'vial'),

-- Furosemide
(210, 183, 'Lasix', '40.00', 'mg', '15.00', '40mg', 'tablet', 'Sanofi', NOW(), NOW(), 'tabs'),
(211, 183, 'Lasix Inj', '10.00', 'mg/ml', '2.00', '20mg/2ml', 'injection', 'Sanofi', NOW(), NOW(), 'ampoule'),

-- Pimobendan
(215, 182, 'Vetmedin', '1.25', 'mg', '50.00', '1.25mg', 'chewable_tablet', 'Boehringer', NOW(), NOW(), 'tabs'),
(216, 182, 'Vetmedin', '5.00', 'mg', '50.00', '5mg', 'chewable_tablet', 'Boehringer', NOW(), NOW(), 'tabs'),

-- Enalapril
(220, 180, 'Envas', '2.50', 'mg', '14.00', '2.5mg', 'tablet', 'Cadila', NOW(), NOW(), 'tabs'),
(221, 180, 'Envas', '5.00', 'mg', '14.00', '5mg', 'tablet', 'Cadila', NOW(), NOW(), 'tabs'),
(222, 180, 'Envas', '10.00', 'mg', '14.00', '10mg', 'tablet', 'Cadila', NOW(), NOW(), 'tabs'),

-- Ivermectin
(230, 160, 'Ivomec', '1.00', '% w/v', '50.00', '10mg/ml', 'injection', 'Boehringer', NOW(), NOW(), 'ml'),
(231, 160, 'Hitek', '3.00', 'mg', '4.00', '3mg', 'tablet', 'Sava Vet', NOW(), NOW(), 'tabs'),
(232, 160, 'Hitek', '6.00', 'mg', '4.00', '6mg', 'tablet', 'Sava Vet', NOW(), NOW(), 'tabs'),

-- Afoxolaner (NexGard)
(235, 167, 'NexGard', '11.30', 'mg', '3.00', '2-4kg', 'chewable_tablet', 'Boehringer', NOW(), NOW(), 'tabs'),
(236, 167, 'NexGard', '28.30', 'mg', '3.00', '4-10kg', 'chewable_tablet', 'Boehringer', NOW(), NOW(), 'tabs'),
(237, 167, 'NexGard', '68.00', 'mg', '3.00', '10-25kg', 'chewable_tablet', 'Boehringer', NOW(), NOW(), 'tabs'),
(238, 167, 'NexGard', '136.00', 'mg', '3.00', '25-50kg', 'chewable_tablet', 'Boehringer', NOW(), NOW(), 'tabs'),

-- Xylazine
(240, 210, 'Xylaxin', '20.00', 'mg/ml', '30.00', '2%', 'injection', 'Indian Immunologicals', NOW(), NOW(), 'ml'),

-- Ketamine
(245, 217, 'Ketalar', '50.00', 'mg/ml', '10.00', '500mg/10ml', 'injection', 'Pfizer', NOW(), NOW(), 'ml'),
(246, 217, 'Aneket', '50.00', 'mg/ml', '10.00', '500mg/10ml', 'injection', 'Neon', NOW(), NOW(), 'ml'),

-- Propofol
(250, 218, 'Propofol', '10.00', 'mg/ml', '20.00', '200mg/20ml', 'injection', 'Various', NOW(), NOW(), 'ml'),

-- Atropine
(255, 220, 'Atropine Sulphate', '0.60', 'mg/ml', '1.00', '0.6mg/ml', 'injection', 'Various', NOW(), NOW(), 'ampoule'),

-- Diazepam
(260, 214, 'Calmpose', '5.00', 'mg/ml', '2.00', '10mg/2ml', 'injection', 'Ranbaxy', NOW(), NOW(), 'ampoule'),
(261, 214, 'Calmpose', '5.00', 'mg', '10.00', '5mg', 'tablet', 'Ranbaxy', NOW(), NOW(), 'tabs'),

-- Phenobarbital
(265, 230, 'Luminal', '30.00', 'mg', '100.00', '30mg', 'tablet', 'Abbott', NOW(), NOW(), 'tabs'),
(266, 230, 'Luminal', '60.00', 'mg', '100.00', '60mg', 'tablet', 'Abbott', NOW(), NOW(), 'tabs'),
(267, 230, 'Phenobarbitone Inj', '200.00', 'mg/ml', '1.00', '200mg/ml', 'injection', 'Various', NOW(), NOW(), 'ampoule'),

-- Tramadol
(270, 124, 'Tramadol', '50.00', 'mg', '10.00', '50mg', 'capsule', 'Various', NOW(), NOW(), 'caps'),
(271, 124, 'Tramadol Inj', '50.00', 'mg/ml', '2.00', '100mg/2ml', 'injection', 'Various', NOW(), NOW(), 'ampoule'),

-- Chlorpheniramine
(275, 190, 'Avil', '4.00', 'mg', '20.00', '4mg', 'tablet', 'Sanofi', NOW(), NOW(), 'tabs'),
(276, 190, 'Avil Inj', '22.75', 'mg/ml', '2.00', '22.75mg/ml', 'injection', 'Sanofi', NOW(), NOW(), 'ampoule'),

-- Adrenaline
(280, 270, 'Adrenaline', '1.00', 'mg/ml', '1.00', '1mg/ml', 'injection', 'Various', NOW(), NOW(), 'ampoule'),

-- Ringer Lactate
(285, 281, 'Ringer Lactate', '1000.00', 'ml', '1.00', '500ml', 'infusion', 'Various', NOW(), NOW(), 'bottle'),
(286, 281, 'Ringer Lactate', '500.00', 'ml', '1.00', '500ml', 'infusion', 'Various', NOW(), NOW(), 'bottle'),

-- Normal Saline
(290, 280, 'Normal Saline 0.9%', '500.00', 'ml', '1.00', '500ml', 'infusion', 'Various', NOW(), NOW(), 'bottle'),
(291, 280, 'Normal Saline 0.9%', '1000.00', 'ml', '1.00', '1000ml', 'infusion', 'Various', NOW(), NOW(), 'bottle'),

-- Oclacitinib
(295, 263, 'Apoquel', '3.60', 'mg', '20.00', '3.6mg', 'tablet', 'Zoetis', NOW(), NOW(), 'tabs'),
(296, 263, 'Apoquel', '5.40', 'mg', '20.00', '5.4mg', 'tablet', 'Zoetis', NOW(), NOW(), 'tabs'),
(297, 263, 'Apoquel', '16.00', 'mg', '20.00', '16mg', 'tablet', 'Zoetis', NOW(), NOW(), 'tabs'),

-- Cyclosporine
(300, 260, 'Atopica', '10.00', 'mg', '15.00', '10mg', 'capsule', 'Elanco', NOW(), NOW(), 'caps'),
(301, 260, 'Atopica', '25.00', 'mg', '15.00', '25mg', 'capsule', 'Elanco', NOW(), NOW(), 'caps'),
(302, 260, 'Atopica', '50.00', 'mg', '15.00', '50mg', 'capsule', 'Elanco', NOW(), NOW(), 'caps'),
(303, 260, 'Atopica', '100.00', 'mg', '15.00', '100mg', 'capsule', 'Elanco', NOW(), NOW(), 'caps');


-- ============================================================
-- 3. DRUG DOSAGES (species-specific)
-- ============================================================
INSERT INTO `drug_dosages` (`id`, `generic_id`, `species`, `dose_min`, `dose_max`, `dose_unit`, `created_at`, `updated_at`, `routes`, `frequencies`) VALUES
-- Amoxicillin
(101, 101, 'dog', 10.00, 25.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["BID","TID"]'),
(102, 101, 'cat', 10.00, 25.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["BID"]'),

-- Amoxicillin + Clavulanate
(110, 102, 'dog', 12.50, 25.00, 'mg/kg', NOW(), NOW(), '["Oral","IV"]', '["BID"]'),
(111, 102, 'cat', 12.50, 25.00, 'mg/kg', NOW(), NOW(), '["Oral","IV"]', '["BID"]'),

-- Cephalexin
(115, 104, 'dog', 15.00, 30.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["BID","TID"]'),
(116, 104, 'cat', 15.00, 30.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["BID"]'),

-- Cefpodoxime
(118, 105, 'dog', 5.00, 10.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["SID"]'),

-- Ceftriaxone
(120, 106, 'dog', 15.00, 50.00, 'mg/kg', NOW(), NOW(), '["IV","IM","SC"]', '["SID","BID"]'),
(121, 106, 'cat', 15.00, 50.00, 'mg/kg', NOW(), NOW(), '["IV","IM","SC"]', '["SID","BID"]'),

-- Enrofloxacin
(125, 107, 'dog', 5.00, 20.00, 'mg/kg', NOW(), NOW(), '["Oral","SC","IM"]', '["SID","BID"]'),
(126, 107, 'cat', 5.00, 5.00, 'mg/kg', NOW(), NOW(), '["Oral","SC"]', '["SID"]'),

-- Metronidazole
(130, 109, 'dog', 10.00, 25.00, 'mg/kg', NOW(), NOW(), '["Oral","IV"]', '["BID"]'),
(131, 109, 'cat', 10.00, 25.00, 'mg/kg', NOW(), NOW(), '["Oral","IV"]', '["BID"]'),

-- Doxycycline
(135, 110, 'dog', 5.00, 10.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["SID","BID"]'),
(136, 110, 'cat', 5.00, 10.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["SID","BID"]'),

-- Meloxicam
(140, 120, 'dog', 0.10, 0.20, 'mg/kg', NOW(), NOW(), '["Oral","SC","IV"]', '["SID"]'),
(141, 120, 'cat', 0.05, 0.10, 'mg/kg', NOW(), NOW(), '["Oral","SC"]', '["SID"]'),

-- Carprofen
(145, 121, 'dog', 2.00, 4.40, 'mg/kg', NOW(), NOW(), '["Oral","SC"]', '["SID","BID"]'),

-- Prednisolone
(150, 130, 'dog', 0.50, 2.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["SID","BID"]'),
(151, 130, 'cat', 1.00, 2.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["SID","BID"]'),

-- Dexamethasone
(155, 131, 'dog', 0.05, 0.20, 'mg/kg', NOW(), NOW(), '["IV","IM","SC","Oral"]', '["SID"]'),
(156, 131, 'cat', 0.05, 0.20, 'mg/kg', NOW(), NOW(), '["IV","IM","SC","Oral"]', '["SID"]'),

-- Ondansetron
(160, 140, 'dog', 0.10, 0.50, 'mg/kg', NOW(), NOW(), '["IV","Oral"]', '["BID","TID"]'),
(161, 140, 'cat', 0.10, 0.50, 'mg/kg', NOW(), NOW(), '["IV","Oral"]', '["BID","TID"]'),

-- Maropitant
(165, 142, 'dog', 1.00, 1.00, 'mg/kg', NOW(), NOW(), '["SC","Oral"]', '["SID"]'),
(166, 142, 'cat', 1.00, 1.00, 'mg/kg', NOW(), NOW(), '["SC","Oral"]', '["SID"]'),

-- Omeprazole
(170, 151, 'dog', 0.50, 1.00, 'mg/kg', NOW(), NOW(), '["Oral","IV"]', '["SID","BID"]'),
(171, 151, 'cat', 0.50, 1.00, 'mg/kg', NOW(), NOW(), '["Oral","IV"]', '["SID"]'),

-- Furosemide
(175, 183, 'dog', 1.00, 4.00, 'mg/kg', NOW(), NOW(), '["Oral","IV","IM"]', '["BID","TID"]'),
(176, 183, 'cat', 1.00, 2.00, 'mg/kg', NOW(), NOW(), '["Oral","IV","IM"]', '["BID","TID"]'),

-- Pimobendan
(180, 182, 'dog', 0.10, 0.30, 'mg/kg', NOW(), NOW(), '["Oral"]', '["BID"]'),

-- Enalapril
(185, 180, 'dog', 0.25, 0.50, 'mg/kg', NOW(), NOW(), '["Oral"]', '["SID","BID"]'),
(186, 180, 'cat', 0.25, 0.50, 'mg/kg', NOW(), NOW(), '["Oral"]', '["SID","BID"]'),

-- Ivermectin
(190, 160, 'dog', 6.00, 12.00, 'mcg/kg', NOW(), NOW(), '["SC","Oral"]', '["SID"]'),
(191, 160, 'cat', 200.00, 400.00, 'mcg/kg', NOW(), NOW(), '["SC","Oral"]', '["Monthly"]'),

-- Tramadol
(195, 124, 'dog', 2.00, 5.00, 'mg/kg', NOW(), NOW(), '["Oral","IV","IM"]', '["BID","TID"]'),
(196, 124, 'cat', 1.00, 2.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["BID"]'),

-- Xylazine
(200, 210, 'dog', 0.50, 1.10, 'mg/kg', NOW(), NOW(), '["IV","IM"]', '["PRN"]'),
(201, 210, 'cat', 0.50, 1.10, 'mg/kg', NOW(), NOW(), '["IV","IM"]', '["PRN"]'),

-- Ketamine
(205, 217, 'dog', 5.00, 10.00, 'mg/kg', NOW(), NOW(), '["IV","IM"]', '["PRN"]'),
(206, 217, 'cat', 5.00, 10.00, 'mg/kg', NOW(), NOW(), '["IV","IM"]', '["PRN"]'),

-- Atropine
(210, 220, 'dog', 0.02, 0.04, 'mg/kg', NOW(), NOW(), '["IV","IM","SC"]', '["PRN"]'),
(211, 220, 'cat', 0.02, 0.04, 'mg/kg', NOW(), NOW(), '["IV","IM","SC"]', '["PRN"]'),

-- Diazepam
(215, 214, 'dog', 0.20, 0.50, 'mg/kg', NOW(), NOW(), '["IV"]', '["PRN"]'),
(216, 214, 'cat', 0.20, 0.50, 'mg/kg', NOW(), NOW(), '["IV"]', '["PRN"]'),

-- Phenobarbital
(220, 230, 'dog', 2.50, 5.00, 'mg/kg', NOW(), NOW(), '["Oral","IV"]', '["BID"]'),
(221, 230, 'cat', 1.00, 2.00, 'mg/kg', NOW(), NOW(), '["Oral","IV"]', '["BID"]'),

-- Chlorpheniramine
(225, 190, 'dog', 0.20, 0.50, 'mg/kg', NOW(), NOW(), '["Oral","IM"]', '["BID","TID"]'),
(226, 190, 'cat', 1.00, 2.00, 'mg/cat', NOW(), NOW(), '["Oral","IM"]', '["BID"]'),

-- Adrenaline (emergency)
(230, 270, 'dog', 0.01, 0.02, 'mg/kg', NOW(), NOW(), '["IV","IM","IT"]', '["PRN"]'),
(231, 270, 'cat', 0.01, 0.02, 'mg/kg', NOW(), NOW(), '["IV","IM","IT"]', '["PRN"]'),

-- Apoquel
(235, 263, 'dog', 0.40, 0.60, 'mg/kg', NOW(), NOW(), '["Oral"]', '["BID x 14d then SID"]'),

-- Cyclosporine
(240, 260, 'dog', 5.00, 7.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["SID"]'),
(241, 260, 'cat', 7.00, 7.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["SID"]'),

-- Azithromycin
(245, 111, 'dog', 5.00, 10.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["SID"]'),
(246, 111, 'cat', 5.00, 10.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["SID"]');
