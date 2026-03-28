-- ============================================================
-- Indian Veterinary Vaccines — Dogs & Cats (Accurate 2025 data)
-- Knowledge Base (drug_generics, drug_brands, drug_dosages)
-- ============================================================

SET @base_id = 200;

-- ──────────────────────────────────────────
-- DRUG GENERICS (Vaccine types)
-- ──────────────────────────────────────────
INSERT INTO `drug_generics` (`id`, `name`, `drug_class`, `default_dose_unit`, `created_at`, `updated_at`) VALUES
-- Dog core
(@base_id + 1, 'Canine DHPPi (7-in-1)', 'Combination MLV', 'dose', NOW(), NOW()),
(@base_id + 2, 'Canine DHPPi + L (7-in-1 with Lepto)', 'Combination MLV + Inactivated', 'dose', NOW(), NOW()),
(@base_id + 3, 'Canine DHPPi + L4 (9-in-1)', 'Combination MLV + Inactivated', 'dose', NOW(), NOW()),
(@base_id + 4, 'Canine Puppy DP', 'Modified Live Vaccine', 'dose', NOW(), NOW()),
(@base_id + 5, 'Rabies Vaccine (Dog)', 'Inactivated Vaccine', 'dose', NOW(), NOW()),
(@base_id + 6, 'Canine Leptospirosis (L4 standalone)', 'Inactivated Vaccine', 'dose', NOW(), NOW()),
(@base_id + 7, 'Canine Coronavirus Vaccine', 'Inactivated Vaccine', 'dose', NOW(), NOW()),
(@base_id + 8, 'Kennel Cough (Bordetella + CPi)', 'Live Intranasal/Injectable', 'dose', NOW(), NOW()),
(@base_id + 9, 'Canine DHPPi+L4+CV (11-in-1)', 'Combination Vaccine', 'dose', NOW(), NOW()),

-- Cat core
(@base_id + 10, 'Feline CRP / FVRCP (3-in-1)', 'Combination MLV', 'dose', NOW(), NOW()),
(@base_id + 11, 'Rabies Vaccine (Cat)', 'Inactivated Vaccine', 'dose', NOW(), NOW()),
(@base_id + 12, 'Feline Leukemia (FeLV)', 'Inactivated/Recombinant', 'dose', NOW(), NOW()),

-- Multi-species
(@base_id + 13, 'Anti-Rabies Vaccine (ARV)', 'Inactivated Cell Culture', 'dose', NOW(), NOW());


-- ──────────────────────────────────────────
-- DRUG BRANDS — DOGS
-- ──────────────────────────────────────────
INSERT INTO `drug_brands` (`id`, `generic_id`, `brand_name`, `manufacturer`, `form`, `strength_value`, `strength_unit`, `pack_size`, `pack_unit`, `created_at`, `updated_at`) VALUES

-- === PUPPY DP (first vaccine at 6 weeks) ===
(@base_id + 1, @base_id + 4, 'Nobivac Puppy DP', 'MSD Animal Health', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),

-- === DHPPi (5-in-1 / 7-in-1 without Lepto) ===
(@base_id + 2, @base_id + 1, 'Nobivac DHPPi', 'MSD Animal Health', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),
(@base_id + 3, @base_id + 1, 'Recombitek C4', 'Boehringer Ingelheim', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),
(@base_id + 4, @base_id + 1, 'Canishot DHPPL', 'Intas Animal Health', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),

-- === DHPPi + L (7-in-1 with Lepto L2) ===
(@base_id + 5, @base_id + 2, 'Nobivac DHPPi + Lepto', 'MSD Animal Health', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),
(@base_id + 6, @base_id + 2, 'Canigen DHPPi/L', 'Virbac', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),
(@base_id + 7, @base_id + 2, 'Megavac-7', 'Indian Immunologicals', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),
(@base_id + 8, @base_id + 2, 'Biocan DHPPi+L', 'Bioveta', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),
(@base_id + 9, @base_id + 2, 'Recombitek C6', 'Boehringer Ingelheim', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),
(@base_id + 10, @base_id + 2, 'Canvac 8', 'Glochem Industries', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),

-- === DHPPi + L4 (9-in-1 — covers 4 Lepto serovars) ===
(@base_id + 11, @base_id + 3, 'Vanguard Plus 5/L4', 'Zoetis', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),
(@base_id + 12, @base_id + 3, 'Nobivac DHPPi + L4', 'MSD Animal Health', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),
(@base_id + 13, @base_id + 3, 'Eurican DHPPi2-L', 'Boehringer Ingelheim', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),
(@base_id + 14, @base_id + 3, 'Vencomax 8', 'Venkys (Venkateshwara Hatcheries)', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),

-- === 11-in-1 (DHPPi + L4 + Coronavirus) ===
(@base_id + 15, @base_id + 9, 'Vanguard Plus 5/L4 CV', 'Zoetis', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),
(@base_id + 16, @base_id + 9, 'Vencomax 11', 'Venkys (Venkateshwara Hatcheries)', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),

-- === RABIES — Dogs ===
(@base_id + 17, @base_id + 5, 'Nobivac Rabies', 'MSD Animal Health', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),
(@base_id + 18, @base_id + 5, 'Defensor 3', 'Zoetis', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),
(@base_id + 19, @base_id + 5, 'Rabisin', 'Boehringer Ingelheim', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),
(@base_id + 20, @base_id + 5, 'Raksharab', 'Indian Immunologicals', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),
(@base_id + 21, @base_id + 5, 'Rabigen Mono', 'Virbac', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),
(@base_id + 22, @base_id + 5, 'Nobivac RL (Rabies+Lepto)', 'MSD Animal Health', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),
(@base_id + 23, @base_id + 5, 'Canvac R', 'Glochem Industries', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),

-- === LEPTOSPIROSIS standalone ===
(@base_id + 24, @base_id + 6, 'Nobivac L4', 'MSD Animal Health', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),
(@base_id + 25, @base_id + 6, 'Nobivac Lepto', 'MSD Animal Health', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),
(@base_id + 26, @base_id + 6, 'Recombitek L4', 'Boehringer Ingelheim', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),
(@base_id + 27, @base_id + 6, 'Vanguard L4', 'Zoetis', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),

-- === KENNEL COUGH ===
(@base_id + 28, @base_id + 8, 'Nobivac KC', 'MSD Animal Health', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),
(@base_id + 29, @base_id + 8, 'Bronchi-Shield III', 'Boehringer Ingelheim', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),
(@base_id + 30, @base_id + 8, 'Canishot K5', 'Intas Animal Health', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),

-- === CORONAVIRUS standalone ===
(@base_id + 31, @base_id + 7, 'Vanguard CV', 'Zoetis', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),
(@base_id + 32, @base_id + 7, 'Vencosix', 'Venkys (Venkateshwara Hatcheries)', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),


-- ──────────────────────────────────────────
-- DRUG BRANDS — CATS
-- ──────────────────────────────────────────

-- === FVRCP / CRP (3-in-1) ===
(@base_id + 33, @base_id + 10, 'Nobivac Tricat Trio', 'MSD Animal Health', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),
(@base_id + 34, @base_id + 10, 'Feligen CRP', 'Virbac', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),
(@base_id + 35, @base_id + 10, 'Felocell 3', 'Zoetis', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),
(@base_id + 36, @base_id + 10, 'Ronvac CRP', 'Panav Biotech', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),
(@base_id + 37, @base_id + 10, 'Purevax RCPCh', 'Boehringer Ingelheim', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),
(@base_id + 38, @base_id + 10, 'Biofel PCH', 'Bioveta', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),

-- === FELINE RABIES ===
(@base_id + 39, @base_id + 11, 'Nobivac Rabies (Feline)', 'MSD Animal Health', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),
(@base_id + 40, @base_id + 11, 'Purevax Rabies', 'Boehringer Ingelheim', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),
(@base_id + 41, @base_id + 11, 'Rabigen Mono (Feline)', 'Virbac', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),

-- === FELINE LEUKEMIA ===
(@base_id + 42, @base_id + 12, 'Purevax FeLV', 'Boehringer Ingelheim', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),
(@base_id + 43, @base_id + 12, 'Nobivac FeLV', 'MSD Animal Health', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),
(@base_id + 44, @base_id + 12, 'Leucogen', 'Virbac', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),

-- === MULTI-SPECIES RABIES ===
(@base_id + 45, @base_id + 13, 'Raksharab (ARV)', 'Indian Immunologicals', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW()),
(@base_id + 46, @base_id + 13, 'Nobivac Rabies (ARV)', 'MSD Animal Health', 'injection', 1.00, 'dose', 1.00, 'vial', NOW(), NOW());


-- ──────────────────────────────────────────
-- DRUG DOSAGES (Vaccination protocols)
-- ──────────────────────────────────────────
INSERT INTO `drug_dosages` (`generic_id`, `species`, `dose_min`, `dose_max`, `dose_unit`, `routes`, `frequencies`, `created_at`, `updated_at`) VALUES

-- Puppy DP
(@base_id + 4, 'canine', 1.00, 1.00, 'dose', '["SC"]', '["Single dose at 6 weeks"]', NOW(), NOW()),

-- DHPPi (7-in-1 without Lepto)
(@base_id + 1, 'canine', 1.00, 1.00, 'dose', '["SC"]', '["8 wks, 12 wks, 16 wks (puppy)","Annually (booster)"]', NOW(), NOW()),

-- DHPPi+L (7-in-1 with Lepto)
(@base_id + 2, 'canine', 1.00, 1.00, 'dose', '["SC"]', '["8 wks, 12 wks, 16 wks (puppy)","Annually (booster)"]', NOW(), NOW()),

-- DHPPi+L4 (9-in-1)
(@base_id + 3, 'canine', 1.00, 1.00, 'dose', '["SC"]', '["8 wks, 12 wks, 16 wks (puppy)","Annually (booster)"]', NOW(), NOW()),

-- 11-in-1
(@base_id + 9, 'canine', 1.00, 1.00, 'dose', '["SC"]', '["8 wks, 12 wks, 16 wks (puppy)","Annually (booster)"]', NOW(), NOW()),

-- Rabies — Dog
(@base_id + 5, 'canine', 1.00, 1.00, 'dose', '["SC","IM"]', '["Single dose at 12-16 wks","Annually (mandatory in India)"]', NOW(), NOW()),

-- Lepto standalone
(@base_id + 6, 'canine', 1.00, 1.00, 'dose', '["SC"]', '["2 doses 3-4 wks apart","Every 6-12 months in endemic areas"]', NOW(), NOW()),

-- Coronavirus
(@base_id + 7, 'canine', 1.00, 1.00, 'dose', '["SC"]', '["2 doses 3 wks apart"]', NOW(), NOW()),

-- Kennel Cough
(@base_id + 8, 'canine', 1.00, 1.00, 'dose', '["IN","SC"]', '["Annually","2 wks before boarding"]', NOW(), NOW()),

-- FVRCP (Cat 3-in-1)
(@base_id + 10, 'feline', 1.00, 1.00, 'dose', '["SC"]', '["8 wks, 12 wks, 16 wks (kitten)","Annually (booster)"]', NOW(), NOW()),

-- Rabies — Cat
(@base_id + 11, 'feline', 1.00, 1.00, 'dose', '["SC"]', '["Single dose at 12-16 wks","Annually"]', NOW(), NOW()),

-- FeLV
(@base_id + 12, 'feline', 1.00, 1.00, 'dose', '["SC"]', '["2 doses 3-4 wks apart","Annually"]', NOW(), NOW()),

-- ARV multi-species
(@base_id + 13, 'canine', 1.00, 1.00, 'dose', '["SC","IM"]', '["Annually (mandatory)"]', NOW(), NOW()),
(@base_id + 13, 'feline', 1.00, 1.00, 'dose', '["SC","IM"]', '["Annually"]', NOW(), NOW());
