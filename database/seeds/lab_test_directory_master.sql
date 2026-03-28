-- ============================================================
-- MASTER LAB TEST DIRECTORY — Single source of truth
-- All in-house labs + external labs pick from this list
-- ============================================================

TRUNCATE TABLE lab_test_directory;

-- ========================
-- HEMATOLOGY
-- ========================
INSERT INTO lab_test_directory (code, name, category, sample_type, aliases, created_at, updated_at) VALUES
('CBC', 'Complete Blood Count (CBC)', 'hematology', 'blood', '["CBC","Complete Blood Count","Hemogram","Full Blood Count","FBC"]', NOW(), NOW()),
('PLT-MAN', 'Manual Platelet Count', 'hematology', 'blood', '["Manual Platelet Count","Platelet Count"]', NOW(), NOW()),
('BSE', 'Blood Smear Examination', 'hematology', 'blood', '["Blood Smear","Peripheral Smear","PBS","Parasite","Hemoprotozoan"]', NOW(), NOW()),
('XMATCH', 'Blood Cross Matching', 'hematology', 'blood', '["Cross Match","Blood Cross Matching","Crossmatch"]', NOW(), NOW()),
('BTYP', 'Blood Typing', 'hematology', 'blood', '["Blood Typing","Blood Group","Blood Type"]', NOW(), NOW()),
('COOMBS', 'Coombs Test', 'hematology', 'blood', '["Coombs","Direct Coombs","Antiglobulin Test"]', NOW(), NOW()),

-- ========================
-- COAGULATION
-- ========================
('PT', 'Prothrombin Time (PT)', 'coagulation', 'blood', '["PT","Prothrombin","Prothrombin Time"]', NOW(), NOW()),
('APTT', 'Activated Partial Thromboplastin Time (APTT)', 'coagulation', 'blood', '["APTT","aPTT","Partial Thromboplastin"]', NOW(), NOW()),
('FIB', 'Fibrinogen', 'coagulation', 'blood', '["Fibrinogen"]', NOW(), NOW()),
('DDIM', 'D-Dimer', 'coagulation', 'blood', '["D-Dimer","D Dimer"]', NOW(), NOW()),
('DDIM2', 'Two Step D-Dimer', 'coagulation', 'blood', '["Two Step D-Dimer"]', NOW(), NOW()),

-- ========================
-- BIOCHEMISTRY — LIVER
-- ========================
('ALT', 'Alanine Aminotransferase (ALT/SGPT)', 'biochemistry', 'blood', '["ALT","SGPT","Alanine Aminotransferase"]', NOW(), NOW()),
('AST', 'Aspartate Aminotransferase (AST/SGOT)', 'biochemistry', 'blood', '["AST","SGOT","Aspartate Aminotransferase"]', NOW(), NOW()),
('ALP', 'Alkaline Phosphatase (ALP)', 'biochemistry', 'blood', '["ALP","Alkaline Phosphatase"]', NOW(), NOW()),
('GGT', 'Gamma-Glutamyltransferase (GGT)', 'biochemistry', 'blood', '["GGT","Gamma GT","g-GT"]', NOW(), NOW()),
('ALB', 'Albumin', 'biochemistry', 'blood', '["Albumin","Alb"]', NOW(), NOW()),
('GLOB', 'Globulin', 'biochemistry', 'blood', '["Globulin","Glob"]', NOW(), NOW()),
('TP', 'Total Protein', 'biochemistry', 'blood', '["Total Protein","TP","Serum Protein"]', NOW(), NOW()),
('TBIL', 'Total Bilirubin', 'biochemistry', 'blood', '["Total Bilirubin","TBIL","Bilirubin Total"]', NOW(), NOW()),
('DBIL', 'Direct Bilirubin', 'biochemistry', 'blood', '["Direct Bilirubin","DBIL","Conjugated Bilirubin"]', NOW(), NOW()),
('IBIL', 'Indirect Bilirubin', 'biochemistry', 'blood', '["Indirect Bilirubin","IBIL","Unconjugated Bilirubin"]', NOW(), NOW()),
('BIL3', 'Bilirubin: Total, Direct, Indirect', 'biochemistry', 'blood', '["Bilirubin Panel","Bilirubin Total Direct Indirect"]', NOW(), NOW()),

-- ========================
-- BIOCHEMISTRY — KIDNEY
-- ========================
('BUN', 'Blood Urea Nitrogen (BUN)', 'biochemistry', 'blood', '["BUN","Blood Urea Nitrogen","Blood Urea"]', NOW(), NOW()),
('CREA', 'Creatinine', 'biochemistry', 'blood', '["Creatinine","CR","CRT","Serum Creatinine"]', NOW(), NOW()),
('UREA', 'Urea', 'biochemistry', 'blood', '["Urea","Serum Urea"]', NOW(), NOW()),
('UA', 'Uric Acid', 'biochemistry', 'blood', '["Uric Acid","UA","Serum Uric Acid"]', NOW(), NOW()),
('PHOS', 'Phosphorous', 'biochemistry', 'blood', '["Phosphorous","Phosphorus","P","Serum Phosphorus"]', NOW(), NOW()),

-- ========================
-- BIOCHEMISTRY — ELECTROLYTES & MINERALS
-- ========================
('NA', 'Sodium (Na)', 'biochemistry', 'blood', '["Sodium","Na","Serum Sodium"]', NOW(), NOW()),
('K', 'Potassium (K)', 'biochemistry', 'blood', '["Potassium","K","Serum Potassium"]', NOW(), NOW()),
('CL', 'Chloride (Cl)', 'biochemistry', 'blood', '["Chloride","Cl","Serum Chloride"]', NOW(), NOW()),
('CA', 'Calcium Total', 'biochemistry', 'blood', '["Calcium","Ca","Total Calcium","Serum Calcium"]', NOW(), NOW()),
('CA-ION', 'Calcium Ionised', 'biochemistry', 'blood', '["Ionized Calcium","Ionised Calcium","Ca Ionized"]', NOW(), NOW()),
('MG', 'Magnesium', 'biochemistry', 'blood', '["Magnesium","Mg","Serum Magnesium"]', NOW(), NOW()),
('IRON', 'Iron', 'biochemistry', 'blood', '["Iron","Serum Iron","Fe"]', NOW(), NOW()),
('BICARB', 'Bicarbonate', 'biochemistry', 'blood', '["Bicarbonate","HCO3"]', NOW(), NOW()),
('FERR', 'Ferritin', 'biochemistry', 'blood', '["Ferritin","Serum Ferritin"]', NOW(), NOW()),

-- ========================
-- BIOCHEMISTRY — METABOLIC
-- ========================
('GLU-R', 'Glucose Random', 'biochemistry', 'blood', '["Glucose Random","Blood Sugar Random","BSR"]', NOW(), NOW()),
('GLU-F', 'Glucose Fasting (Pre-prandial)', 'biochemistry', 'blood', '["Glucose Fasting","FBS","Fasting Blood Sugar","Pre-prandial"]', NOW(), NOW()),
('GLU-PP', 'Glucose Postprandial', 'biochemistry', 'blood', '["Glucose Postprandial","PPBS","Post-prandial Blood Sugar"]', NOW(), NOW()),
('HBA1C', 'Glycosylated Hemoglobin (HbA1c)', 'biochemistry', 'blood', '["HbA1c","HBA1C","Glycosylated Hemoglobin"]', NOW(), NOW()),
('FRUC', 'Fructosamine', 'biochemistry', 'blood', '["Fructosamine"]', NOW(), NOW()),
('AMY', 'Amylase', 'biochemistry', 'blood', '["Amylase","AMY","Serum Amylase"]', NOW(), NOW()),
('LIP', 'Lipase', 'biochemistry', 'blood', '["Lipase","Serum Lipase"]', NOW(), NOW()),
('CPLI', 'Lipase Canine Specific (cPLI/Spec cPL)', 'biochemistry', 'blood', '["cPLI","Spec cPL","Canine Pancreatic Lipase","Lipase Canine Specific"]', NOW(), NOW()),
('FPLI', 'Lipase Feline Specific (fPLI)', 'biochemistry', 'blood', '["fPLI","Feline Pancreatic Lipase","Lipase Feline Specific"]', NOW(), NOW()),
('CK', 'Creatine Kinase (CK)', 'biochemistry', 'blood', '["CK","Creatine Kinase","CPK"]', NOW(), NOW()),
('LDH', 'Lactate Dehydrogenase (LDH)', 'biochemistry', 'blood', '["LDH","Lactate Dehydrogenase"]', NOW(), NOW()),
('CHOL', 'Total Cholesterol', 'biochemistry', 'blood', '["Total Cholesterol","TC","Cholesterol"]', NOW(), NOW()),
('TG', 'Triglycerides', 'biochemistry', 'blood', '["Triglycerides","TG"]', NOW(), NOW()),
('HDL', 'HDL Cholesterol', 'biochemistry', 'blood', '["HDL","HDL-C","High Density Lipoprotein"]', NOW(), NOW()),
('LDL', 'LDL Cholesterol', 'biochemistry', 'blood', '["LDL","LDL-C","Low Density Lipoprotein"]', NOW(), NOW()),
('IGE', 'Immunoglobulin E (IgE)', 'biochemistry', 'blood', '["IgE","Immunoglobulin E"]', NOW(), NOW()),
('PHENO', 'Phenobarbitol', 'biochemistry', 'blood', '["Phenobarbitol","Phenobarbital"]', NOW(), NOW()),

-- ========================
-- THYROID
-- ========================
('T3', 'Triiodothyronine (T3)', 'endocrinology', 'blood', '["T3","Triiodothyronine"]', NOW(), NOW()),
('T4', 'Thyroxine (T4)', 'endocrinology', 'blood', '["T4","Thyroxine","Total T4"]', NOW(), NOW()),
('TSH', 'Thyroid Stimulating Hormone (TSH)', 'endocrinology', 'blood', '["TSH","Thyroid Stimulating Hormone"]', NOW(), NOW()),
('FT3', 'Free T3 (fT3)', 'endocrinology', 'blood', '["fT3","Free T3"]', NOW(), NOW()),
('FT4', 'Free T4 (fT4)', 'endocrinology', 'blood', '["fT4","Free T4"]', NOW(), NOW()),
('THYR-P', 'Thyroid Profile (T3, T4, TSH)', 'endocrinology', 'blood', '["Thyroid Profile","Thyroid Panel","T3 T4 TSH"]', NOW(), NOW()),
('THYR-F', 'Thyroid Profile Free (fT3, fT4)', 'endocrinology', 'blood', '["Thyroid Profile Free","Free Thyroid Panel"]', NOW(), NOW()),
('CT4', 'Canine Specific T4', 'endocrinology', 'blood', '["Canine T4","Canine Specific T4"]', NOW(), NOW()),
('CTSH', 'Canine Specific TSH', 'endocrinology', 'blood', '["Canine TSH","Canine Specific TSH"]', NOW(), NOW()),
('CFT4', 'Canine Specific Free T4', 'endocrinology', 'blood', '["Canine Free T4","Canine Specific Ft4"]', NOW(), NOW()),
('CTHYR', 'Canine Specific Thyroid Profile (TSH & T4)', 'endocrinology', 'blood', '["Canine Thyroid Profile","Canine Thyroid Panel"]', NOW(), NOW()),

-- ========================
-- FERTILITY / HORMONES
-- ========================
('LH', 'Luteinizing Hormone (LH)', 'endocrinology', 'blood', '["LH","Luteinizing Hormone"]', NOW(), NOW()),
('AMH', 'Anti-Mullerian Hormone (AMH)', 'endocrinology', 'blood', '["AMH","Anti-Mullerian Hormone"]', NOW(), NOW()),
('BHCG', 'Beta-HCG', 'endocrinology', 'blood', '["Beta-HCG","b-HCG","Human Chorionic Gonadotropin"]', NOW(), NOW()),
('E2', 'Estradiol (E2)', 'endocrinology', 'blood', '["Estradiol","E2"]', NOW(), NOW()),
('FSH', 'Follicle Stimulating Hormone (FSH)', 'endocrinology', 'blood', '["FSH","Follicle Stimulating Hormone"]', NOW(), NOW()),
('PROG', 'Progesterone', 'endocrinology', 'blood', '["Progesterone"]', NOW(), NOW()),
('CPROG', 'Canine Specific Progesterone', 'endocrinology', 'blood', '["Canine Progesterone","Canine Specific Progesterone"]', NOW(), NOW()),
('PRL', 'Prolactin', 'endocrinology', 'blood', '["Prolactin","PRL"]', NOW(), NOW()),
('TESTO', 'Testosterone', 'endocrinology', 'blood', '["Testosterone"]', NOW(), NOW()),
('RELAX', 'Canine Relaxin Test (Pregnancy)', 'endocrinology', 'blood', '["Relaxin","Canine Relaxin","Pregnancy Test"]', NOW(), NOW()),
('CORT', 'Cortisol', 'endocrinology', 'blood', '["Cortisol","Serum Cortisol"]', NOW(), NOW()),
('ACTH', 'ACTH', 'endocrinology', 'blood', '["ACTH","Adrenocorticotropic Hormone"]', NOW(), NOW()),
('PTH', 'Parathyroid Hormone', 'endocrinology', 'blood', '["PTH","Parathyroid Hormone","Parathyroid"]', NOW(), NOW()),

-- ========================
-- RENAL INJURY MARKERS
-- ========================
('MAU', 'Micro-albumin (MAU)', 'biochemistry', 'blood', '["Micro-albumin","MAU","Microalbumin"]', NOW(), NOW()),
('SDMA-C', 'SDMA Canine', 'biochemistry', 'blood', '["SDMA Canine","SDMA-Canine","Symmetric Dimethylarginine Canine"]', NOW(), NOW()),
('SDMA-F', 'SDMA Feline', 'biochemistry', 'blood', '["SDMA Feline","SDMA-Feline","Symmetric Dimethylarginine Feline"]', NOW(), NOW()),

-- ========================
-- TUMOR MARKERS
-- ========================
('AFP', 'Alpha Fetoprotein (AFP)', 'tumor_markers', 'blood', '["AFP","Alpha Fetoprotein"]', NOW(), NOW()),
('PSA', 'Prostate-Specific Antigen (PSA)', 'tumor_markers', 'blood', '["PSA","Prostate Specific Antigen"]', NOW(), NOW()),
('CEA', 'Carcino-Embryonic Antigen (CEA)', 'tumor_markers', 'blood', '["CEA","Carcino-Embryonic Antigen"]', NOW(), NOW()),
('FPSA', 'Free Prostate Specific Antigen (fPSA)', 'tumor_markers', 'blood', '["fPSA","Free PSA"]', NOW(), NOW()),

-- ========================
-- CARDIAC MARKERS
-- ========================
('NTPRO', 'NT-proBNP', 'cardiac', 'blood', '["NT-proBNP","BNP","Brain Natriuretic Peptide"]', NOW(), NOW()),
('CTNI', 'Cardiac Troponin I (cTnI)', 'cardiac', 'blood', '["cTnI","Troponin","Cardiac Troponin"]', NOW(), NOW()),
('CKMB', 'Creatine Kinase-MB (CK-MB)', 'cardiac', 'blood', '["CK-MB","CKMB","Creatine Kinase MB"]', NOW(), NOW()),
('MYO', 'Myoglobin', 'cardiac', 'blood', '["Myoglobin","MYO"]', NOW(), NOW()),
('HFABP', 'Heart-type Fatty Acid-Binding Protein (H-FABP)', 'cardiac', 'blood', '["H-FABP","HFABP","Heart FABP"]', NOW(), NOW()),
('CARD3', 'Cardiac 3 in 1 (cTnI/Myo/CK-MB)', 'cardiac', 'blood', '["Cardiac 3 in 1","Triple Cardiac"]', NOW(), NOW()),
('CARD2', 'Cardiac 2 in 1 (cTnI/NT-proBNP)', 'cardiac', 'blood', '["Cardiac 2 in 1","Dual Cardiac"]', NOW(), NOW()),

-- ========================
-- INFLAMMATION
-- ========================
('PCT', 'Pro-calcitonin (PCT)', 'inflammation', 'blood', '["PCT","Pro-calcitonin","Procalcitonin"]', NOW(), NOW()),
('CRP', 'C-Reactive Protein (CRP)', 'inflammation', 'blood', '["CRP","C-Reactive Protein"]', NOW(), NOW()),
('HSCRP', 'High-sensitivity CRP (hsCRP)', 'inflammation', 'blood', '["hsCRP","High Sensitivity CRP"]', NOW(), NOW()),
('IL6', 'Interleukin-6 (IL-6)', 'inflammation', 'blood', '["IL-6","Interleukin 6"]', NOW(), NOW()),
('CRPPCT', 'CRP + PCT Combo', 'inflammation', 'blood', '["CRP PCT","CRP+PCT"]', NOW(), NOW()),

-- ========================
-- VITAMINS
-- ========================
('VITD', 'Vitamin D', 'biochemistry', 'blood', '["Vitamin D","Vit D","25-OH Vitamin D"]', NOW(), NOW()),
('VITB12', 'Vitamin B12', 'biochemistry', 'blood', '["Vitamin B12","Vit B12","Cobalamin"]', NOW(), NOW()),
('FOLAT', 'Folate', 'biochemistry', 'blood', '["Folate","Folic Acid"]', NOW(), NOW()),

-- ========================
-- URINE ANALYSIS
-- ========================
('URINE', 'Routine Urine Analysis (Physical & Microscopic)', 'urinalysis', 'urine', '["Urinalysis","Urine Routine","Urine Analysis","UA Complete"]', NOW(), NOW()),
('USTRIP', 'Urine Examination (Strip Test)', 'urinalysis', 'urine', '["Urine Strip","Dipstick","Urine Examination"]', NOW(), NOW()),
('UCCR', 'Urine Cortisol Creatinine Ratio', 'urinalysis', 'urine', '["UCCR","Urine Cortisol","Cortisol Creatinine Ratio"]', NOW(), NOW()),
('UPCR', 'Urine Protein Creatinine Ratio', 'urinalysis', 'urine', '["UPCR","Urine Protein","Protein Creatinine Ratio"]', NOW(), NOW()),
('UMPRO', 'Urine Microprotein', 'urinalysis', 'urine', '["Urine Microprotein"]', NOW(), NOW()),

-- ========================
-- MICROBIOLOGY
-- ========================
('CS-BAC', 'Culture & Sensitivity — Bacteria (Pus/Urine/Swab)', 'microbiology', 'swab', '["Culture Sensitivity","C&S","Bacterial Culture","ABST Bacteria"]', NOW(), NOW()),
('CS-FUN', 'Culture & Sensitivity — Fungus', 'microbiology', 'swab', '["Fungal Culture","ABST Fungus","Fungal C&S"]', NOW(), NOW()),
('CS-STL', 'Culture & Sensitivity — Stool', 'microbiology', 'feces', '["Stool Culture","Stool C&S"]', NOW(), NOW()),
('CS-BLD', 'Culture & Sensitivity — Blood', 'microbiology', 'blood', '["Blood Culture","Blood C&S"]', NOW(), NOW()),

-- ========================
-- HISTOPATHOLOGY & CYTOLOGY
-- ========================
('HISTO-S', 'Histopathology — Small Tissue', 'histopathology', 'tissue', '["Histopathology Small","Biopsy Small"]', NOW(), NOW()),
('HISTO-L', 'Histopathology — Large Tissue', 'histopathology', 'tissue', '["Histopathology Large","Biopsy Large"]', NOW(), NOW()),
('FNAC', 'Cytology / FNAC', 'cytology', 'tissue', '["FNAC","Fine Needle Aspiration","Cytology"]', NOW(), NOW()),

-- ========================
-- OTHERS / SPECIALTY
-- ========================
('ALLERGY', 'Food Allergy Testing (180 Allergens)', 'serology', 'blood', '["Food Allergy","Allergy Panel","Allergen Test"]', NOW(), NOW()),
('EPO', 'Erythropoietin (EPO)', 'biochemistry', 'blood', '["EPO","Erythropoietin"]', NOW(), NOW()),
('PLFLUID', 'Pleural Fluid Analysis', 'other', 'fluid', '["Pleural Fluid","Pleural Analysis"]', NOW(), NOW()),
('CPELAST', 'Canine Faecal Pancreatic Elastase', 'biochemistry', 'feces', '["Faecal Elastase","Pancreatic Elastase","cPE"]', NOW(), NOW()),
('STOOL', 'Stool Examination', 'other', 'feces', '["Stool Exam","Fecal Examination","Stool Test"]', NOW(), NOW()),
('RFIT', 'Rabies Antibody Titer (RFFIT)', 'serology', 'blood', '["Rabies Titer","RFFIT","Rabies Antibody"]', NOW(), NOW()),
('STONE', 'Stone Analysis', 'other', 'other', '["Stone Analysis","Urinary Stone","Calculus Analysis"]', NOW(), NOW()),

-- ========================
-- PANELS (Combo Tests)
-- ========================
('LFT', 'Liver Function Test (LFT) Panel', 'panel', 'blood', '["LFT","Liver Function Test","Liver Panel","Hepatic Panel"]', NOW(), NOW()),
('KFT', 'Kidney Function Test (KFT) Panel', 'panel', 'blood', '["KFT","Kidney Function Test","Renal Panel","RFT"]', NOW(), NOW()),
('LKFCBC', 'LFT + KFT + CBC Panel', 'panel', 'blood', '["LFT KFT CBC","Complete Panel"]', NOW(), NOW()),
('ELEC', 'Serum Electrolyte Panel (Na, K, Cl)', 'panel', 'blood', '["Electrolytes","Electrolyte Panel","Na K Cl"]', NOW(), NOW()),
('PANC-P', 'Pancreatic Profile', 'panel', 'blood', '["Pancreatic Profile","Amylase Lipase"]', NOW(), NOW()),
('MIN-P', 'Mineral Profile (Ca, Mg, P, Na, K, Cl)', 'panel', 'blood', '["Mineral Profile","Mineral Panel"]', NOW(), NOW()),
('ANEM-P', 'Anemia Panel (CBC, Iron, Ferritin)', 'panel', 'blood', '["Anemia Panel","Anemia Profile"]', NOW(), NOW()),
('LIPID-P', 'Lipid Profile (TC, HDL-C, LDL-C, TG)', 'panel', 'blood', '["Lipid Profile","Lipid Panel","Cholesterol Panel"]', NOW(), NOW()),

-- ========================
-- PCR — CANINE
-- ========================
('CTICK10', 'Canine Tick Panel I — 10 Infections', 'pcr', 'blood', '["Canine Tick Panel 10","Tick Panel I"]', NOW(), NOW()),
('CTICK8', 'Canine Tick Panel II — 8 Infections', 'pcr', 'blood', '["Canine Tick Panel 8","Tick Panel II"]', NOW(), NOW()),
('CTICK6', 'Canine Tick Panel III — 6 Infections', 'pcr', 'blood', '["Canine Tick Panel 6","Tick Panel III"]', NOW(), NOW()),
('BAB-EC', 'Babesia & E. canis Panel', 'pcr', 'blood', '["Babesia E canis","Babesia Panel"]', NOW(), NOW()),
('CPV-PCR', 'Canine Parvovirus PCR', 'pcr', 'blood', '["Parvo PCR","CPV PCR","Canine Parvo"]', NOW(), NOW()),
('CDV-PCR', 'Canine Distemper PCR', 'pcr', 'blood', '["Distemper PCR","CDV PCR"]', NOW(), NOW()),
('LEPT-PCR', 'Leptospira PCR', 'pcr', 'blood', '["Lepto PCR","Leptospira PCR"]', NOW(), NOW()),

-- ========================
-- PCR — FELINE
-- ========================
('FPCR-I', 'Feline PCR Advance Panel I', 'pcr', 'blood', '["Feline PCR Panel I","Feline Advance Panel"]', NOW(), NOW()),
('FPCR-II', 'Feline PCR Panel II', 'pcr', 'blood', '["Feline PCR Panel II"]', NOW(), NOW()),
('FIP-PCR', 'Feline Infectious Peritonitis (FIP) PCR', 'pcr', 'blood', '["FIP PCR","FIP","Feline Peritonitis"]', NOW(), NOW()),
('FIV-PCR', 'Feline Immunodeficiency Virus (FIV) PCR', 'pcr', 'blood', '["FIV PCR","FIV"]', NOW(), NOW()),
('FLV-PCR', 'Feline Leukemia Virus (FeLV) PCR', 'pcr', 'blood', '["FeLV PCR","FLV PCR","Feline Leukemia"]', NOW(), NOW()),
('FPV-PCR', 'Feline Panleukopenia (FPV) PCR', 'pcr', 'blood', '["FPV PCR","Panleukopenia"]', NOW(), NOW()),
('FCV-PCR', 'Cat Flu / Feline Calicivirus (FCV) PCR', 'pcr', 'blood', '["FCV PCR","Cat Flu","Calicivirus"]', NOW(), NOW()),
('FHV-PCR', 'Feline Viral Rhinotracheitis (FHV-1) PCR', 'pcr', 'blood', '["FHV PCR","Rhinotracheitis","Feline Herpes"]', NOW(), NOW()),
('MH-PCR', 'Mycoplasma Haemofelis PCR', 'pcr', 'blood', '["Mycoplasma Haemofelis","M. haemofelis"]', NOW(), NOW()),
('CMH-PCR', 'Candidatus Mycoplasma haemominutum PCR', 'pcr', 'blood', '["Candidatus Mycoplasma","haemominutum"]', NOW(), NOW()),
('FBAB-PCR', 'Feline Babesiosis PCR', 'pcr', 'blood', '["Feline Babesia","Babesiosis Feline"]', NOW(), NOW()),
('CYTZ-PCR', 'Cytauxzoon felis PCR', 'pcr', 'blood', '["Cytauxzoon","Cytauxzoon felis"]', NOW(), NOW()),

-- ========================
-- PCR — OTHER
-- ========================
('CJ-PCR', 'Campylobacter jejunii PCR', 'pcr', 'feces', '["Campylobacter jejunii","C. jejunii"]', NOW(), NOW()),
('CU-PCR', 'Campylobacteriosis upsaliensis PCR', 'pcr', 'feces', '["Campylobacteriosis upsaliensis"]', NOW(), NOW()),
('TOXO-PCR', 'Toxoplasmosis PCR', 'pcr', 'blood', '["Toxoplasmosis","Toxoplasma"]', NOW(), NOW()),
('CARC-PCR', 'Carcinoma PCR', 'pcr', 'blood', '["Carcinoma PCR"]', NOW(), NOW()),
('FECB', 'FecB Genotyping', 'pcr', 'blood', '["FecB","FecB Genotyping"]', NOW(), NOW()),
('A1A2', 'A1A2 Genotyping', 'pcr', 'blood', '["A1A2","A1A2 Genotyping"]', NOW(), NOW()),
('SEXBIRD', 'Sex Determination in Birds', 'pcr', 'other', '["Sex Determination","Bird Sexing"]', NOW(), NOW()),
('PCR-DNA', 'PCR: DNA (Single Infection)', 'pcr', 'blood', '["PCR DNA","DNA PCR"]', NOW(), NOW()),
('PCR-RNA', 'PCR: RNA (Single Infection)', 'pcr', 'blood', '["PCR RNA","RNA PCR"]', NOW(), NOW()),

-- ========================
-- RAPID / POINT OF CARE
-- ========================
('CPV-RAP', 'Canine Parvovirus Rapid Antigen Test', 'rapid_test', 'feces', '["Parvo Rapid","CPV Rapid","Parvo Snap"]', NOW(), NOW()),
('CDV-RAP', 'Canine Distemper Rapid Test', 'rapid_test', 'blood', '["Distemper Rapid","CDV Snap"]', NOW(), NOW()),
('EHRL-RAP', 'Ehrlichia canis Rapid Ab Test', 'rapid_test', 'blood', '["Ehrlichia Rapid","Tick Fever Rapid"]', NOW(), NOW()),
('FELV-RAP', 'FeLV/FIV Rapid Combo Test', 'rapid_test', 'blood', '["FeLV FIV Combo","SNAP FIV/FeLV","Feline Combo"]', NOW(), NOW()),

-- ========================
-- IMAGING (non-lab but part of diagnostics)
-- ========================
('XRAY', 'X-Ray / Radiograph', 'imaging', 'other', '["X-Ray","Radiograph","Xray"]', NOW(), NOW()),
('USG', 'Ultrasound / Ultrasonography', 'imaging', 'other', '["Ultrasound","USG","Sonography","Abdominal USG"]', NOW(), NOW()),
('ECHO', 'Echocardiography', 'imaging', 'other', '["Echo","Echocardiography","Cardiac Ultrasound"]', NOW(), NOW()),
('ECG', 'Electrocardiogram (ECG)', 'imaging', 'other', '["ECG","EKG","Electrocardiogram"]', NOW(), NOW()),
('CT', 'CT Scan', 'imaging', 'other', '["CT Scan","CT","Computed Tomography"]', NOW(), NOW()),
('MRI', 'MRI', 'imaging', 'other', '["MRI","Magnetic Resonance Imaging"]', NOW(), NOW()),
('ENDOSC', 'Endoscopy', 'imaging', 'other', '["Endoscopy"]', NOW(), NOW());
