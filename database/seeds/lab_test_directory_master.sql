-- ============================================================
-- MASTER LAB TEST DIRECTORY — Single source of truth
-- All in-house labs + external labs pick from this list
-- Sample requirements & TAT from Ideal Diagnostics 2025-26
-- ============================================================

TRUNCATE TABLE lab_test_directory;

-- ========================
-- HEMATOLOGY
-- ========================
INSERT INTO lab_test_directory (code, name, category, sample_type, preferred_sample, tat, aliases, created_at, updated_at) VALUES
('CBC', 'Complete Blood Count (CBC)', 'hematology', 'blood', '2ml EDTA Blood', '2 Hrs', '["CBC","Complete Blood Count","Hemogram","Full Blood Count","FBC"]', NOW(), NOW()),
('PLT-MAN', 'Manual Platelet Count', 'hematology', 'blood', '2ml EDTA Blood', '4 Hrs', '["Manual Platelet Count","Platelet Count"]', NOW(), NOW()),
('BSE', 'Blood Smear Examination', 'hematology', 'blood', '2ml EDTA Blood', '1 day', '["Blood Smear","Peripheral Smear","PBS","Parasite","Hemoprotozoan"]', NOW(), NOW()),
('XMATCH', 'Blood Cross Matching', 'hematology', 'blood', '2ml EDTA Blood', '5 Hrs', '["Cross Match","Blood Cross Matching","Crossmatch"]', NOW(), NOW()),
('BTYP', 'Blood Typing', 'hematology', 'blood', '2ml EDTA Blood', '1 day', '["Blood Typing","Blood Group","Blood Type"]', NOW(), NOW()),
('COOMBS', 'Coombs Test', 'hematology', 'blood', '2ml EDTA Blood', '1 day', '["Coombs","Direct Coombs","Antiglobulin Test"]', NOW(), NOW()),

-- ========================
-- COAGULATION
-- ========================
('PT', 'Prothrombin Time (PT)', 'coagulation', 'blood', '2ml Blood in Sodium Citrate', '1 day', '["PT","Prothrombin","Prothrombin Time"]', NOW(), NOW()),
('APTT', 'Activated Partial Thromboplastin Time (APTT)', 'coagulation', 'blood', '2ml Blood in Sodium Citrate', '1 day', '["APTT","aPTT","Partial Thromboplastin"]', NOW(), NOW()),
('FIB', 'Fibrinogen', 'coagulation', 'blood', '2ml Blood in Sodium Citrate', '1 day', '["Fibrinogen"]', NOW(), NOW()),
('DDIM', 'D-Dimer', 'coagulation', 'blood', '2ml Serum', '1 day', '["D-Dimer","D Dimer"]', NOW(), NOW()),
('DDIM2', 'Two Step D-Dimer', 'coagulation', 'blood', '2ml Serum', '1 day', '["Two Step D-Dimer"]', NOW(), NOW()),

-- ========================
-- BIOCHEMISTRY — LIVER
-- ========================
('ALT', 'Alanine Aminotransferase (ALT/SGPT)', 'biochemistry', 'blood', '2ml Serum/Plasma', '3 Hrs', '["ALT","SGPT","Alanine Aminotransferase"]', NOW(), NOW()),
('AST', 'Aspartate Aminotransferase (AST/SGOT)', 'biochemistry', 'blood', '2ml Serum/Plasma', '3 Hrs', '["AST","SGOT","Aspartate Aminotransferase"]', NOW(), NOW()),
('ALP', 'Alkaline Phosphatase (ALP)', 'biochemistry', 'blood', '2ml Serum', '3 Hrs', '["ALP","Alkaline Phosphatase"]', NOW(), NOW()),
('GGT', 'Gamma-Glutamyltransferase (GGT)', 'biochemistry', 'blood', '2ml Serum/Plasma', '3 Hrs', '["GGT","Gamma GT","g-GT"]', NOW(), NOW()),
('ALB', 'Albumin', 'biochemistry', 'blood', '2ml Serum/Plasma', '3 Hrs', '["Albumin","Alb"]', NOW(), NOW()),
('GLOB', 'Globulin', 'biochemistry', 'blood', '2ml Serum/Plasma', '3 Hrs', '["Globulin","Glob"]', NOW(), NOW()),
('TP', 'Total Protein', 'biochemistry', 'blood', '2ml Serum/Plasma', '3 Hrs', '["Total Protein","TP","Serum Protein"]', NOW(), NOW()),
('TBIL', 'Total Bilirubin', 'biochemistry', 'blood', '2ml Serum/Plasma', '3 Hrs', '["Total Bilirubin","TBIL","Bilirubin Total"]', NOW(), NOW()),
('DBIL', 'Direct Bilirubin', 'biochemistry', 'blood', '2ml Serum/Plasma', '3 Hrs', '["Direct Bilirubin","DBIL","Conjugated Bilirubin"]', NOW(), NOW()),
('IBIL', 'Indirect Bilirubin', 'biochemistry', 'blood', '2ml Serum/Plasma', '3 Hrs', '["Indirect Bilirubin","IBIL","Unconjugated Bilirubin"]', NOW(), NOW()),
('BIL3', 'Bilirubin: Total, Direct, Indirect', 'biochemistry', 'blood', '2ml Serum/Plasma', '3 Hrs', '["Bilirubin Panel","Bilirubin Total Direct Indirect"]', NOW(), NOW()),

-- ========================
-- BIOCHEMISTRY — KIDNEY
-- ========================
('BUN', 'Blood Urea Nitrogen (BUN)', 'biochemistry', 'blood', '2ml Serum/Plasma', '3 Hrs', '["BUN","Blood Urea Nitrogen","Blood Urea"]', NOW(), NOW()),
('CREA', 'Creatinine', 'biochemistry', 'blood', '2ml Serum/Plasma', '3 Hrs', '["Creatinine","CR","CRT","Serum Creatinine"]', NOW(), NOW()),
('UREA', 'Urea', 'biochemistry', 'blood', '2ml Serum/Plasma', '3 Hrs', '["Urea","Serum Urea"]', NOW(), NOW()),
('UA', 'Uric Acid', 'biochemistry', 'blood', '2ml Serum/Plasma', '3 Hrs', '["Uric Acid","UA","Serum Uric Acid"]', NOW(), NOW()),
('PHOS', 'Phosphorous', 'biochemistry', 'blood', '2ml Serum/Plasma', '3 Hrs', '["Phosphorous","Phosphorus","P","Serum Phosphorus"]', NOW(), NOW()),

-- ========================
-- BIOCHEMISTRY — ELECTROLYTES & MINERALS
-- ========================
('NA', 'Sodium (Na)', 'biochemistry', 'blood', '2ml Serum/Plasma', '3 Hrs', '["Sodium","Na","Serum Sodium"]', NOW(), NOW()),
('K', 'Potassium (K)', 'biochemistry', 'blood', '2ml Serum/Plasma', '3 Hrs', '["Potassium","K","Serum Potassium"]', NOW(), NOW()),
('CL', 'Chloride (Cl)', 'biochemistry', 'blood', '2ml Serum/Plasma', '3 Hrs', '["Chloride","Cl","Serum Chloride"]', NOW(), NOW()),
('CA', 'Calcium Total', 'biochemistry', 'blood', '2ml Serum/Plasma', '3 Hrs', '["Calcium","Ca","Total Calcium","Serum Calcium"]', NOW(), NOW()),
('CA-ION', 'Calcium Ionised', 'biochemistry', 'blood', '2ml Serum/Plasma', '1 day', '["Ionized Calcium","Ionised Calcium","Ca Ionized"]', NOW(), NOW()),
('MG', 'Magnesium', 'biochemistry', 'blood', '2ml Serum/Plasma', '3 Hrs', '["Magnesium","Mg","Serum Magnesium"]', NOW(), NOW()),
('IRON', 'Iron', 'biochemistry', 'blood', '2ml Serum/Plasma', '6 Hrs', '["Iron","Serum Iron","Fe"]', NOW(), NOW()),
('BICARB', 'Bicarbonate', 'biochemistry', 'blood', '2ml Serum/Plasma', '1 day', '["Bicarbonate","HCO3"]', NOW(), NOW()),
('FERR', 'Ferritin', 'biochemistry', 'blood', '2ml Serum', '1 day', '["Ferritin","Serum Ferritin"]', NOW(), NOW()),

-- ========================
-- BIOCHEMISTRY — METABOLIC
-- ========================
('GLU-R', 'Glucose Random', 'biochemistry', 'blood', '2ml Serum/Plasma', '3 Hrs', '["Glucose Random","Blood Sugar Random","BSR"]', NOW(), NOW()),
('GLU-F', 'Glucose Fasting (Pre-prandial)', 'biochemistry', 'blood', '2ml Serum/Plasma', '3 Hrs', '["Glucose Fasting","FBS","Fasting Blood Sugar","Pre-prandial"]', NOW(), NOW()),
('GLU-PP', 'Glucose Postprandial', 'biochemistry', 'blood', '2ml Serum/Plasma', '3 Hrs', '["Glucose Postprandial","PPBS","Post-prandial Blood Sugar"]', NOW(), NOW()),
('HBA1C', 'Glycosylated Hemoglobin (HbA1c)', 'biochemistry', 'blood', '2ml Serum/Plasma', '3 Hrs', '["HbA1c","HBA1C","Glycosylated Hemoglobin"]', NOW(), NOW()),
('FRUC', 'Fructosamine', 'biochemistry', 'blood', '2ml Serum/Plasma', '3 days', '["Fructosamine"]', NOW(), NOW()),
('AMY', 'Amylase', 'biochemistry', 'blood', '2ml Serum/Plasma', '3 Hrs', '["Amylase","AMY","Serum Amylase"]', NOW(), NOW()),
('LIP', 'Lipase', 'biochemistry', 'blood', '2ml Serum/Plasma', '3 Hrs', '["Lipase","Serum Lipase"]', NOW(), NOW()),
('CPLI', 'Lipase Canine Specific (cPLI/Spec cPL)', 'biochemistry', 'blood', '2ml Serum/Plasma', '3 Hrs', '["cPLI","Spec cPL","Canine Pancreatic Lipase","Lipase Canine Specific"]', NOW(), NOW()),
('FPLI', 'Lipase Feline Specific (fPLI)', 'biochemistry', 'blood', '2ml Serum/Plasma', '3 Hrs', '["fPLI","Feline Pancreatic Lipase","Lipase Feline Specific"]', NOW(), NOW()),
('CK', 'Creatine Kinase (CK)', 'biochemistry', 'blood', '2ml Serum/Plasma', '3 Hrs', '["CK","Creatine Kinase","CPK"]', NOW(), NOW()),
('LDH', 'Lactate Dehydrogenase (LDH)', 'biochemistry', 'blood', '2ml Serum/Plasma', '6 Hrs', '["LDH","Lactate Dehydrogenase"]', NOW(), NOW()),
('CHOL', 'Total Cholesterol', 'biochemistry', 'blood', '2ml Serum/Plasma', '3 Hrs', '["Total Cholesterol","TC","Cholesterol"]', NOW(), NOW()),
('TG', 'Triglycerides', 'biochemistry', 'blood', '2ml Serum/Plasma', '3 Hrs', '["Triglycerides","TG"]', NOW(), NOW()),
('HDL', 'HDL Cholesterol', 'biochemistry', 'blood', '2ml Serum/Plasma', '3 Hrs', '["HDL","HDL-C","High Density Lipoprotein"]', NOW(), NOW()),
('LDL', 'LDL Cholesterol', 'biochemistry', 'blood', '2ml Serum/Plasma', '3 Hrs', '["LDL","LDL-C","Low Density Lipoprotein"]', NOW(), NOW()),
('IGE', 'Immunoglobulin E (IgE)', 'biochemistry', 'blood', '2ml Serum/Plasma', '1 day', '["IgE","Immunoglobulin E"]', NOW(), NOW()),
('PHENO', 'Phenobarbitol', 'biochemistry', 'blood', '2ml Serum/Plasma', '3-4 days', '["Phenobarbitol","Phenobarbital"]', NOW(), NOW()),

-- ========================
-- THYROID
-- ========================
('T3', 'Triiodothyronine (T3)', 'endocrinology', 'blood', '2ml Serum/Plasma', '2 Hrs', '["T3","Triiodothyronine"]', NOW(), NOW()),
('T4', 'Thyroxine (T4)', 'endocrinology', 'blood', '2ml Serum/Plasma', '2 Hrs', '["T4","Thyroxine","Total T4"]', NOW(), NOW()),
('TSH', 'Thyroid Stimulating Hormone (TSH)', 'endocrinology', 'blood', '2ml Serum/Plasma', '2 Hrs', '["TSH","Thyroid Stimulating Hormone"]', NOW(), NOW()),
('FT3', 'Free T3 (fT3)', 'endocrinology', 'blood', '2ml Serum/Plasma', '2 Hrs', '["fT3","Free T3"]', NOW(), NOW()),
('FT4', 'Free T4 (fT4)', 'endocrinology', 'blood', '2ml Serum/Plasma', '2 Hrs', '["fT4","Free T4"]', NOW(), NOW()),
('THYR-P', 'Thyroid Profile (T3, T4, TSH)', 'endocrinology', 'blood', '2ml Serum/Plasma', '2 Hrs', '["Thyroid Profile","Thyroid Panel","T3 T4 TSH"]', NOW(), NOW()),
('THYR-F', 'Thyroid Profile Free (fT3, fT4)', 'endocrinology', 'blood', '2ml Serum/Plasma', '2 Hrs', '["Thyroid Profile Free","Free Thyroid Panel"]', NOW(), NOW()),
('CT4', 'Canine Specific T4', 'endocrinology', 'blood', '2ml Serum/Plasma', '2 Hrs', '["Canine T4","Canine Specific T4"]', NOW(), NOW()),
('CTSH', 'Canine Specific TSH', 'endocrinology', 'blood', '2ml Serum/Plasma', '2 Hrs', '["Canine TSH","Canine Specific TSH"]', NOW(), NOW()),
('CFT4', 'Canine Specific Free T4', 'endocrinology', 'blood', '2ml Serum/Plasma', '2 Hrs', '["Canine Free T4","Canine Specific Ft4"]', NOW(), NOW()),
('CTHYR', 'Canine Specific Thyroid Profile (TSH & T4)', 'endocrinology', 'blood', '2ml Serum/Plasma', '2 Hrs', '["Canine Thyroid Profile","Canine Thyroid Panel"]', NOW(), NOW()),

-- ========================
-- FERTILITY / HORMONES
-- ========================
('LH', 'Luteinizing Hormone (LH)', 'endocrinology', 'blood', '2ml Serum', '2 Hrs', '["LH","Luteinizing Hormone"]', NOW(), NOW()),
('AMH', 'Anti-Mullerian Hormone (AMH)', 'endocrinology', 'blood', '2ml Serum', '2 Hrs', '["AMH","Anti-Mullerian Hormone"]', NOW(), NOW()),
('BHCG', 'Beta-HCG', 'endocrinology', 'blood', '2ml Serum', '2 Hrs', '["Beta-HCG","b-HCG","Human Chorionic Gonadotropin"]', NOW(), NOW()),
('E2', 'Estradiol (E2)', 'endocrinology', 'blood', '2ml Serum', '2 Hrs', '["Estradiol","E2"]', NOW(), NOW()),
('FSH', 'Follicle Stimulating Hormone (FSH)', 'endocrinology', 'blood', '2ml Serum', '2 Hrs', '["FSH","Follicle Stimulating Hormone"]', NOW(), NOW()),
('PROG', 'Progesterone', 'endocrinology', 'blood', '2ml Serum', '2 Hrs', '["Progesterone"]', NOW(), NOW()),
('CPROG', 'Canine Specific Progesterone', 'endocrinology', 'blood', '2ml Serum', '2 Hrs', '["Canine Progesterone","Canine Specific Progesterone"]', NOW(), NOW()),
('PRL', 'Prolactin', 'endocrinology', 'blood', '2ml Serum', '2 Hrs', '["Prolactin","PRL"]', NOW(), NOW()),
('TESTO', 'Testosterone', 'endocrinology', 'blood', '2ml Serum', '2 Hrs', '["Testosterone"]', NOW(), NOW()),
('RELAX', 'Canine Relaxin Test (Pregnancy)', 'endocrinology', 'blood', '2ml Serum', '6 Hrs', '["Relaxin","Canine Relaxin","Pregnancy Test"]', NOW(), NOW()),
('CORT', 'Cortisol', 'endocrinology', 'blood', '2ml Serum', '1 day', '["Cortisol","Serum Cortisol"]', NOW(), NOW()),
('ACTH', 'ACTH', 'endocrinology', 'blood', '2ml Serum', '2 days', '["ACTH","Adrenocorticotropic Hormone"]', NOW(), NOW()),
('PTH', 'Parathyroid Hormone', 'endocrinology', 'blood', '2ml Serum', '1 day', '["PTH","Parathyroid Hormone","Parathyroid"]', NOW(), NOW()),

-- ========================
-- RENAL INJURY MARKERS
-- ========================
('MAU', 'Micro-albumin (MAU)', 'biochemistry', 'blood', '2ml Serum', '1 day', '["Micro-albumin","MAU","Microalbumin"]', NOW(), NOW()),
('SDMA-C', 'SDMA Canine', 'biochemistry', 'blood', '2ml Serum', '3 Hrs', '["SDMA Canine","SDMA-Canine","Symmetric Dimethylarginine Canine"]', NOW(), NOW()),
('SDMA-F', 'SDMA Feline', 'biochemistry', 'blood', '2ml Serum', '3 Hrs', '["SDMA Feline","SDMA-Feline","Symmetric Dimethylarginine Feline"]', NOW(), NOW()),

-- ========================
-- TUMOR MARKERS
-- ========================
('AFP', 'Alpha Fetoprotein (AFP)', 'tumor_markers', 'blood', '2ml Serum', '1 day', '["AFP","Alpha Fetoprotein"]', NOW(), NOW()),
('PSA', 'Prostate-Specific Antigen (PSA)', 'tumor_markers', 'blood', '2ml Serum', '1 day', '["PSA","Prostate Specific Antigen"]', NOW(), NOW()),
('CEA', 'Carcino-Embryonic Antigen (CEA)', 'tumor_markers', 'blood', '2ml Serum', '1 day', '["CEA","Carcino-Embryonic Antigen"]', NOW(), NOW()),
('FPSA', 'Free Prostate Specific Antigen (fPSA)', 'tumor_markers', 'blood', '2ml Serum', '1 day', '["fPSA","Free PSA"]', NOW(), NOW()),

-- ========================
-- CARDIAC MARKERS
-- ========================
('NTPRO', 'NT-proBNP', 'cardiac', 'blood', '2ml Serum', '1 day', '["NT-proBNP","BNP","Brain Natriuretic Peptide"]', NOW(), NOW()),
('CTNI', 'Cardiac Troponin I (cTnI)', 'cardiac', 'blood', '2ml Serum', '1 day', '["cTnI","Troponin","Cardiac Troponin"]', NOW(), NOW()),
('CKMB', 'Creatine Kinase-MB (CK-MB)', 'cardiac', 'blood', '2ml Serum', '1 day', '["CK-MB","CKMB","Creatine Kinase MB"]', NOW(), NOW()),
('MYO', 'Myoglobin', 'cardiac', 'blood', '2ml Serum', '1 day', '["Myoglobin","MYO"]', NOW(), NOW()),
('HFABP', 'Heart-type Fatty Acid-Binding Protein (H-FABP)', 'cardiac', 'blood', '2ml Serum', '1 day', '["H-FABP","HFABP","Heart FABP"]', NOW(), NOW()),
('CARD3', 'Cardiac 3 in 1 (cTnI/Myo/CK-MB)', 'cardiac', 'blood', '2ml Serum', '1 day', '["Cardiac 3 in 1","Triple Cardiac"]', NOW(), NOW()),
('CARD2', 'Cardiac 2 in 1 (cTnI/NT-proBNP)', 'cardiac', 'blood', '2ml Serum', '1 day', '["Cardiac 2 in 1","Dual Cardiac"]', NOW(), NOW()),

-- ========================
-- INFLAMMATION
-- ========================
('PCT', 'Pro-calcitonin (PCT)', 'inflammation', 'blood', '2ml Serum', '1 day', '["PCT","Pro-calcitonin","Procalcitonin"]', NOW(), NOW()),
('CRP', 'C-Reactive Protein (CRP)', 'inflammation', 'blood', '2ml Serum', '6 Hrs', '["CRP","C-Reactive Protein"]', NOW(), NOW()),
('HSCRP', 'High-sensitivity CRP (hsCRP)', 'inflammation', 'blood', '2ml Serum', '1 day', '["hsCRP","High Sensitivity CRP"]', NOW(), NOW()),
('IL6', 'Interleukin-6 (IL-6)', 'inflammation', 'blood', '2ml Serum', '1 day', '["IL-6","Interleukin 6"]', NOW(), NOW()),
('CRPPCT', 'CRP + PCT Combo', 'inflammation', 'blood', '2ml Serum', '1 day', '["CRP PCT","CRP+PCT"]', NOW(), NOW()),

-- ========================
-- VITAMINS
-- ========================
('VITD', 'Vitamin D', 'biochemistry', 'blood', '2ml Serum', '6 Hrs', '["Vitamin D","Vit D","25-OH Vitamin D"]', NOW(), NOW()),
('VITB12', 'Vitamin B12', 'biochemistry', 'blood', '2ml Serum', '1 day', '["Vitamin B12","Vit B12","Cobalamin"]', NOW(), NOW()),
('FOLAT', 'Folate', 'biochemistry', 'blood', '2ml Serum', '1 day', '["Folate","Folic Acid"]', NOW(), NOW()),

-- ========================
-- URINE ANALYSIS
-- ========================
('URINE', 'Routine Urine Analysis (Physical & Microscopic)', 'urinalysis', 'urine', '10ml Urine', '4 Hrs', '["Urinalysis","Urine Routine","Urine Analysis","UA Complete"]', NOW(), NOW()),
('USTRIP', 'Urine Examination (Strip Test)', 'urinalysis', 'urine', '5ml Urine', '4 Hrs', '["Urine Strip","Dipstick","Urine Examination"]', NOW(), NOW()),
('UCCR', 'Urine Cortisol Creatinine Ratio', 'urinalysis', 'urine', '10ml Urine', '4 days', '["UCCR","Urine Cortisol","Cortisol Creatinine Ratio"]', NOW(), NOW()),
('UPCR', 'Urine Protein Creatinine Ratio', 'urinalysis', 'urine', '10ml Urine', '1 day', '["UPCR","Urine Protein","Protein Creatinine Ratio"]', NOW(), NOW()),
('UMPRO', 'Urine Microprotein', 'urinalysis', 'urine', '5ml Urine', '4 Hrs', '["Urine Microprotein"]', NOW(), NOW()),

-- ========================
-- MICROBIOLOGY
-- ========================
('CS-BAC', 'Culture & Sensitivity — Bacteria (Pus/Urine/Swab)', 'microbiology', 'swab', 'Pus, Urine, Swab', '3-4 days', '["Culture Sensitivity","C&S","Bacterial Culture","ABST Bacteria"]', NOW(), NOW()),
('CS-FUN', 'Culture & Sensitivity — Fungus', 'microbiology', 'swab', 'Pus, Urine, Swab', '21 days', '["Fungal Culture","ABST Fungus","Fungal C&S"]', NOW(), NOW()),
('CS-STL', 'Culture & Sensitivity — Stool', 'microbiology', 'feces', 'Stool sample', '3-4 days', '["Stool Culture","Stool C&S"]', NOW(), NOW()),
('CS-BLD', 'Culture & Sensitivity — Blood', 'microbiology', 'blood', '4ml EDTA Blood', '3-4 days', '["Blood Culture","Blood C&S"]', NOW(), NOW()),

-- ========================
-- HISTOPATHOLOGY & CYTOLOGY
-- ========================
('HISTO-S', 'Histopathology — Small Tissue', 'histopathology', 'tissue', 'Tissue sample in Formalin', '7-10 days', '["Histopathology Small","Biopsy Small"]', NOW(), NOW()),
('HISTO-L', 'Histopathology — Large Tissue', 'histopathology', 'tissue', 'Tissue sample in Formalin', '7-10 days', '["Histopathology Large","Biopsy Large"]', NOW(), NOW()),
('FNAC', 'Cytology / FNAC', 'cytology', 'tissue', 'Aspirate smear', '1-2 days', '["FNAC","Fine Needle Aspiration","Cytology"]', NOW(), NOW()),

-- ========================
-- OTHERS / SPECIALTY
-- ========================
('ALLERGY', 'Food Allergy Testing (180 Allergens)', 'serology', 'blood', '4ml Serum', '7-10 days', '["Food Allergy","Allergy Panel","Allergen Test"]', NOW(), NOW()),
('EPO', 'Erythropoietin (EPO)', 'biochemistry', 'blood', '2ml Serum', '2 days', '["EPO","Erythropoietin"]', NOW(), NOW()),
('PLFLUID', 'Pleural Fluid Analysis', 'other', 'fluid', '5ml Pleural Fluid', '1-2 days', '["Pleural Fluid","Pleural Analysis"]', NOW(), NOW()),
('CPELAST', 'Canine Faecal Pancreatic Elastase', 'biochemistry', 'feces', 'Stool sample', '3 days', '["Faecal Elastase","Pancreatic Elastase","cPE"]', NOW(), NOW()),
('STOOL', 'Stool Examination', 'other', 'feces', 'Stool sample', '1-2 days', '["Stool Exam","Fecal Examination","Stool Test"]', NOW(), NOW()),
('RFIT', 'Rabies Antibody Titer (RFFIT)', 'serology', 'blood', '4ml Serum', '15 days', '["Rabies Titer","RFFIT","Rabies Antibody"]', NOW(), NOW()),
('STONE', 'Stone Analysis', 'other', 'other', 'Stone/Calculus', '5 days', '["Stone Analysis","Urinary Stone","Calculus Analysis"]', NOW(), NOW()),

-- ========================
-- PANELS (Combo Tests)
-- ========================
('LKFCBC', 'LFT + KFT + CBC Panel', 'panel', 'blood', '2ml EDTA Blood & 2ml Serum', '3 Hrs', '["LFT KFT CBC","Complete Panel"]', NOW(), NOW()),
('LFT', 'Liver Function Test (LFT) Panel', 'panel', 'blood', '2ml Serum', '3 Hrs', '["LFT","Liver Function Test","Liver Panel","Hepatic Panel"]', NOW(), NOW()),
('KFT', 'Kidney Function Test (KFT) Panel', 'panel', 'blood', '2ml Serum', '3 Hrs', '["KFT","Kidney Function Test","Renal Panel","RFT"]', NOW(), NOW()),
('ELEC', 'Serum Electrolyte Panel (Na, K, Cl)', 'panel', 'blood', '2ml Serum', '3 Hrs', '["Electrolytes","Electrolyte Panel","Na K Cl"]', NOW(), NOW()),
('PANC-P', 'Pancreatic Profile (Amylase, Lipase)', 'panel', 'blood', '2ml Serum', '3 Hrs', '["Pancreatic Profile","Amylase Lipase"]', NOW(), NOW()),
('MIN-P', 'Mineral Profile (Ca, Mg, P, Na, K, Cl)', 'panel', 'blood', '2ml Serum', '3 Hrs', '["Mineral Profile","Mineral Panel"]', NOW(), NOW()),
('ANEM-P', 'Anemia Panel (CBC, Iron, Ferritin)', 'panel', 'blood', '2ml Blood and 2ml Serum', '1-2 days', '["Anemia Panel","Anemia Profile"]', NOW(), NOW()),
('LIPID-P', 'Lipid Profile (TC, HDL-C, LDL-C, TG)', 'panel', 'blood', '2ml Serum', '6 Hrs', '["Lipid Profile","Lipid Panel","Cholesterol Panel"]', NOW(), NOW()),

-- ========================
-- PCR — CANINE
-- ========================
('CTICK10', 'Canine Tick Panel I — 10 Infections', 'pcr', 'blood', '2ml EDTA Blood; Urine for Leptospira', '8 Hrs', '["Canine Tick Panel 10","Tick Panel I"]', NOW(), NOW()),
('CTICK8', 'Canine Tick Panel II — 8 Infections', 'pcr', 'blood', '2ml EDTA Blood; Urine for Leptospira', '8 Hrs', '["Canine Tick Panel 8","Tick Panel II"]', NOW(), NOW()),
('CTICK6', 'Canine Tick Panel III — 6 Infections', 'pcr', 'blood', '2ml EDTA Blood; Urine for Leptospira', '8 Hrs', '["Canine Tick Panel 6","Tick Panel III"]', NOW(), NOW()),
('BAB-EC', 'Babesia & E. canis Panel', 'pcr', 'blood', '2ml EDTA Blood', '8 Hrs', '["Babesia E canis","Babesia Panel"]', NOW(), NOW()),
('CPV-PCR', 'Canine Parvovirus PCR', 'pcr', 'blood', 'Fecal swab / 2ml Blood in EDTA vial', '8 Hrs', '["Parvo PCR","CPV PCR","Canine Parvo"]', NOW(), NOW()),
('CDV-PCR', 'Canine Distemper PCR', 'pcr', 'blood', 'Conjunctival swabs / Blood / Urine', '10 Hrs', '["Distemper PCR","CDV PCR"]', NOW(), NOW()),
('LEPT-PCR', 'Leptospira PCR', 'pcr', 'blood', 'Urine / 2ml Blood in EDTA vial', '8 Hrs', '["Lepto PCR","Leptospira PCR"]', NOW(), NOW()),

-- ========================
-- PCR — FELINE
-- ========================
('FPCR-I', 'Feline PCR Advance Panel I', 'pcr', 'blood', '2ml Blood in EDTA vial', '8 Hrs', '["Feline PCR Panel I","Feline Advance Panel"]', NOW(), NOW()),
('FPCR-II', 'Feline PCR Panel II', 'pcr', 'blood', '2ml Blood in EDTA vial', '10 Hrs', '["Feline PCR Panel II"]', NOW(), NOW()),
('FIP-PCR', 'Feline Infectious Peritonitis (FIP) PCR', 'pcr', 'blood', 'Peritoneal fluid (wet) / 2ml Blood in EDTA (dry)', '10 Hrs', '["FIP PCR","FIP","Feline Peritonitis"]', NOW(), NOW()),
('FIV-PCR', 'Feline Immunodeficiency Virus (FIV) PCR', 'pcr', 'blood', '2ml Blood in EDTA vial', '10 Hrs', '["FIV PCR","FIV"]', NOW(), NOW()),
('FLV-PCR', 'Feline Leukemia Virus (FeLV) PCR', 'pcr', 'blood', '2ml Blood in EDTA vial', '10 Hrs', '["FeLV PCR","FLV PCR","Feline Leukemia"]', NOW(), NOW()),
('FPV-PCR', 'Feline Panleukopenia (FPV) PCR', 'pcr', 'blood', 'Anal/Rectal Swabs / 2ml Blood in EDTA vial', '8 Hrs', '["FPV PCR","Panleukopenia"]', NOW(), NOW()),
('FCV-PCR', 'Cat Flu / Feline Calicivirus (FCV) PCR', 'pcr', 'blood', 'Oropharynx or Conjunctiva Swab / 2ml Blood in EDTA vial', '10 Hrs', '["FCV PCR","Cat Flu","Calicivirus"]', NOW(), NOW()),
('FHV-PCR', 'Feline Viral Rhinotracheitis (FHV-1) PCR', 'pcr', 'blood', 'Oropharynx or Conjunctiva Swab / 2ml Blood in EDTA vial', '8 Hrs', '["FHV PCR","Rhinotracheitis","Feline Herpes"]', NOW(), NOW()),
('MH-PCR', 'Mycoplasma Haemofelis PCR', 'pcr', 'blood', '2ml Blood in EDTA vial', '8 Hrs', '["Mycoplasma Haemofelis","M. haemofelis"]', NOW(), NOW()),
('CMH-PCR', 'Candidatus Mycoplasma haemominutum PCR', 'pcr', 'blood', '2ml Blood in EDTA vial', '8 Hrs', '["Candidatus Mycoplasma","haemominutum"]', NOW(), NOW()),
('FBAB-PCR', 'Feline Babesiosis PCR', 'pcr', 'blood', '2ml Blood in EDTA vial', '8 Hrs', '["Feline Babesia","Babesiosis Feline"]', NOW(), NOW()),
('CYTZ-PCR', 'Cytauxzoon felis PCR', 'pcr', 'blood', '2ml Blood in EDTA vial', '8 Hrs', '["Cytauxzoon","Cytauxzoon felis"]', NOW(), NOW()),

-- ========================
-- PCR — OTHER
-- ========================
('CJ-PCR', 'Campylobacter jejunii PCR', 'pcr', 'feces', 'Stool / 2ml Blood in EDTA vial', '8 Hrs', '["Campylobacter jejunii","C. jejunii"]', NOW(), NOW()),
('CU-PCR', 'Campylobacteriosis upsaliensis PCR', 'pcr', 'feces', 'Stool / 2ml Blood in EDTA vial', '8 Hrs', '["Campylobacteriosis upsaliensis"]', NOW(), NOW()),
('TOXO-PCR', 'Toxoplasmosis PCR', 'pcr', 'blood', 'Faecal swab / 2ml Blood in EDTA vial', '8 Hrs', '["Toxoplasmosis","Toxoplasma"]', NOW(), NOW()),
('CARC-PCR', 'Carcinoma PCR', 'pcr', 'blood', 'Tissue/Blood', '7 days', '["Carcinoma PCR"]', NOW(), NOW()),
('FECB', 'FecB Genotyping', 'pcr', 'blood', '2ml EDTA Blood', '5 days', '["FecB","FecB Genotyping"]', NOW(), NOW()),
('A1A2', 'A1A2 Genotyping', 'pcr', 'blood', '2ml EDTA Blood', '3 days', '["A1A2","A1A2 Genotyping"]', NOW(), NOW()),
('SEXBIRD', 'Sex Determination in Birds', 'pcr', 'other', '4-6 Feathers from Breast Region', '1 day', '["Sex Determination","Bird Sexing"]', NOW(), NOW()),
('PCR-DNA', 'PCR: DNA (Single Infection)', 'pcr', 'blood', '2ml EDTA Blood', '8 Hrs', '["PCR DNA","DNA PCR"]', NOW(), NOW()),
('PCR-RNA', 'PCR: RNA (Single Infection)', 'pcr', 'blood', '2ml EDTA Blood', '10 Hrs', '["PCR RNA","RNA PCR"]', NOW(), NOW()),

-- ========================
-- RAPID / POINT OF CARE
-- ========================
('CPV-RAP', 'Canine Parvovirus Rapid Antigen Test', 'rapid_test', 'feces', 'Fecal swab', '15 min', '["Parvo Rapid","CPV Rapid","Parvo Snap"]', NOW(), NOW()),
('CDV-RAP', 'Canine Distemper Rapid Test', 'rapid_test', 'blood', 'Conjunctival swab / Blood', '15 min', '["Distemper Rapid","CDV Snap"]', NOW(), NOW()),
('EHRL-RAP', 'Ehrlichia canis Rapid Ab Test', 'rapid_test', 'blood', '2ml EDTA Blood', '15 min', '["Ehrlichia Rapid","Tick Fever Rapid"]', NOW(), NOW()),
('FELV-RAP', 'FeLV/FIV Rapid Combo Test', 'rapid_test', 'blood', '2ml EDTA Blood', '15 min', '["FeLV FIV Combo","SNAP FIV/FeLV","Feline Combo"]', NOW(), NOW()),

-- ========================
-- IMAGING (non-lab but part of diagnostics)
-- ========================
('XRAY', 'X-Ray / Radiograph', 'imaging', 'other', NULL, '30 min', '["X-Ray","Radiograph","Xray"]', NOW(), NOW()),
('USG', 'Ultrasound / Ultrasonography', 'imaging', 'other', NULL, '30 min', '["Ultrasound","USG","Sonography","Abdominal USG"]', NOW(), NOW()),
('ECHO', 'Echocardiography', 'imaging', 'other', NULL, '1 Hr', '["Echo","Echocardiography","Cardiac Ultrasound"]', NOW(), NOW()),
('ECG', 'Electrocardiogram (ECG)', 'imaging', 'other', NULL, '15 min', '["ECG","EKG","Electrocardiogram"]', NOW(), NOW()),
('CT', 'CT Scan', 'imaging', 'other', NULL, '1-2 Hrs', '["CT Scan","CT","Computed Tomography"]', NOW(), NOW()),
('MRI', 'MRI', 'imaging', 'other', NULL, '1-2 Hrs', '["MRI","Magnetic Resonance Imaging"]', NOW(), NOW()),
('ENDOSC', 'Endoscopy', 'imaging', 'other', NULL, '1-2 Hrs', '["Endoscopy"]', NOW(), NOW());
