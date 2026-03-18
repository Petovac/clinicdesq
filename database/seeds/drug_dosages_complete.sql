-- ============================================================
-- COMPLETE DRUG DOSAGES — fills in all missing generics
-- Run AFTER veterinary_drugs_india.sql
-- ============================================================

SET NAMES utf8mb4;

INSERT INTO `drug_dosages` (`generic_id`, `species`, `dose_min`, `dose_max`, `dose_unit`, `created_at`, `updated_at`, `routes`, `frequencies`) VALUES

-- Ampicillin (103)
(103, 'dog', 10.00, 20.00, 'mg/kg', NOW(), NOW(), '["IV","IM","SC"]', '["TID","QID"]'),
(103, 'cat', 10.00, 20.00, 'mg/kg', NOW(), NOW(), '["IV","IM","SC"]', '["TID","QID"]'),

-- Marbofloxacin (108)
(108, 'dog', 2.00, 5.50, 'mg/kg', NOW(), NOW(), '["Oral","IV"]', '["SID"]'),
(108, 'cat', 2.00, 5.50, 'mg/kg', NOW(), NOW(), '["Oral"]', '["SID"]'),

-- Clindamycin (112)
(112, 'dog', 5.50, 11.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["BID"]'),
(112, 'cat', 5.50, 11.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["BID"]'),

-- Gentamicin (113)
(113, 'dog', 6.00, 8.00, 'mg/kg', NOW(), NOW(), '["IV","IM","SC"]', '["SID"]'),
(113, 'cat', 5.00, 8.00, 'mg/kg', NOW(), NOW(), '["IV","IM","SC"]', '["SID"]'),

-- TMP-SMX (114)
(114, 'dog', 15.00, 30.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["BID"]'),
(114, 'cat', 15.00, 30.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["BID"]'),

-- Lincomycin (115)
(115, 'dog', 15.00, 25.00, 'mg/kg', NOW(), NOW(), '["Oral","IM"]', '["BID","TID"]'),
(115, 'cat', 15.00, 25.00, 'mg/kg', NOW(), NOW(), '["Oral","IM"]', '["BID"]'),

-- Tolfenamic Acid (122)
(122, 'dog', 4.00, 4.00, 'mg/kg', NOW(), NOW(), '["Oral","SC"]', '["SID x 3d"]'),
(122, 'cat', 4.00, 4.00, 'mg/kg', NOW(), NOW(), '["Oral","SC"]', '["SID x 3d"]'),

-- Firocoxib (123)
(123, 'dog', 5.00, 5.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["SID"]'),

-- Butorphanol (125)
(125, 'dog', 0.20, 0.40, 'mg/kg', NOW(), NOW(), '["IV","IM","SC"]', '["PRN","QID"]'),
(125, 'cat', 0.20, 0.40, 'mg/kg', NOW(), NOW(), '["IV","IM","SC"]', '["PRN","QID"]'),

-- Paracetamol Dogs only (126)
(126, 'dog', 10.00, 15.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["BID","TID"]'),

-- Metamizole (127)
(127, 'dog', 20.00, 25.00, 'mg/kg', NOW(), NOW(), '["IV","IM","Oral"]', '["BID","TID"]'),
(127, 'cat', 20.00, 25.00, 'mg/kg', NOW(), NOW(), '["IV","IM"]', '["BID"]'),

-- Methylprednisolone (132)
(132, 'dog', 0.50, 1.00, 'mg/kg', NOW(), NOW(), '["IV","IM","Oral"]', '["SID"]'),
(132, 'cat', 0.50, 1.00, 'mg/kg', NOW(), NOW(), '["IV","IM","Oral"]', '["SID"]'),

-- Triamcinolone (133)
(133, 'dog', 0.10, 0.20, 'mg/kg', NOW(), NOW(), '["IM"]', '["Once"]'),
(133, 'cat', 0.10, 0.20, 'mg/kg', NOW(), NOW(), '["IM"]', '["Once"]'),

-- Metoclopramide (141)
(141, 'dog', 0.20, 0.50, 'mg/kg', NOW(), NOW(), '["Oral","IV","IM","SC"]', '["TID","QID"]'),
(141, 'cat', 0.20, 0.50, 'mg/kg', NOW(), NOW(), '["Oral","SC"]', '["TID"]'),

-- Domperidone (143)
(143, 'dog', 0.10, 0.30, 'mg/kg', NOW(), NOW(), '["Oral"]', '["TID"]'),
(143, 'cat', 0.10, 0.30, 'mg/kg', NOW(), NOW(), '["Oral"]', '["TID"]'),

-- Ranitidine (150)
(150, 'dog', 1.00, 2.00, 'mg/kg', NOW(), NOW(), '["Oral","IV","IM"]', '["BID","TID"]'),
(150, 'cat', 1.00, 2.00, 'mg/kg', NOW(), NOW(), '["Oral","IV"]', '["BID"]'),

-- Pantoprazole (152)
(152, 'dog', 0.70, 1.00, 'mg/kg', NOW(), NOW(), '["IV","Oral"]', '["SID"]'),
(152, 'cat', 0.70, 1.00, 'mg/kg', NOW(), NOW(), '["IV","Oral"]', '["SID"]'),

-- Sucralfate (153)
(153, 'dog', 0.50, 1.00, 'g/dog', NOW(), NOW(), '["Oral"]', '["TID","QID"]'),
(153, 'cat', 0.25, 0.50, 'g/cat', NOW(), NOW(), '["Oral"]', '["TID"]'),

-- Loperamide (154)
(154, 'dog', 0.10, 0.20, 'mg/kg', NOW(), NOW(), '["Oral"]', '["BID","TID"]'),

-- Fenbendazole (161)
(161, 'dog', 50.00, 50.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["SID x 3-5d"]'),
(161, 'cat', 50.00, 50.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["SID x 3-5d"]'),

-- Praziquantel (162)
(162, 'dog', 5.00, 7.50, 'mg/kg', NOW(), NOW(), '["Oral","SC","IM"]', '["Once"]'),
(162, 'cat', 5.00, 7.50, 'mg/kg', NOW(), NOW(), '["Oral","SC","IM"]', '["Once"]'),

-- Pyrantel (163)
(163, 'dog', 5.00, 10.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["Once, repeat 2-3 wks"]'),
(163, 'cat', 5.00, 10.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["Once, repeat 2-3 wks"]'),

-- Albendazole (164)
(164, 'dog', 25.00, 50.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["BID x 3d"]'),

-- Benazepril (181)
(181, 'dog', 0.25, 0.50, 'mg/kg', NOW(), NOW(), '["Oral"]', '["SID"]'),
(181, 'cat', 0.25, 0.50, 'mg/kg', NOW(), NOW(), '["Oral"]', '["SID"]'),

-- Spironolactone (184)
(184, 'dog', 1.00, 2.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["BID"]'),
(184, 'cat', 1.00, 2.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["SID","BID"]'),

-- Amlodipine (185)
(185, 'dog', 0.05, 0.10, 'mg/kg', NOW(), NOW(), '["Oral"]', '["SID"]'),
(185, 'cat', 0.625, 1.25, 'mg/cat', NOW(), NOW(), '["Oral"]', '["SID"]'),

-- Atenolol (186)
(186, 'dog', 0.25, 1.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["BID"]'),
(186, 'cat', 6.25, 12.50, 'mg/cat', NOW(), NOW(), '["Oral"]', '["BID"]'),

-- Digoxin (187)
(187, 'dog', 0.005, 0.010, 'mg/kg', NOW(), NOW(), '["Oral"]', '["BID"]'),

-- Diltiazem (188)
(188, 'dog', 0.50, 1.50, 'mg/kg', NOW(), NOW(), '["Oral"]', '["TID"]'),
(188, 'cat', 1.50, 2.50, 'mg/kg', NOW(), NOW(), '["Oral"]', '["TID"]'),

-- Cetirizine (191)
(191, 'dog', 1.00, 2.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["SID","BID"]'),
(191, 'cat', 5.00, 5.00, 'mg/cat', NOW(), NOW(), '["Oral"]', '["SID"]'),

-- Diphenhydramine (192)
(192, 'dog', 2.00, 4.00, 'mg/kg', NOW(), NOW(), '["Oral","IM"]', '["BID","TID"]'),
(192, 'cat', 2.00, 4.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["BID"]'),

-- Hydroxyzine (193)
(193, 'dog', 1.00, 2.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["BID","TID"]'),

-- Cyproheptadine (194)
(194, 'cat', 1.00, 2.00, 'mg/cat', NOW(), NOW(), '["Oral"]', '["BID","TID"]'),
(194, 'dog', 0.30, 2.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["BID"]'),

-- Ketoconazole (200)
(200, 'dog', 5.00, 10.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["SID","BID"]'),
(200, 'cat', 5.00, 10.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["SID"]'),

-- Itraconazole (201)
(201, 'dog', 5.00, 10.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["SID"]'),
(201, 'cat', 5.00, 10.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["SID, alternate weeks"]'),

-- Fluconazole (202)
(202, 'dog', 5.00, 10.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["SID","BID"]'),
(202, 'cat', 5.00, 10.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["SID","BID"]'),

-- Griseofulvin (203)
(203, 'dog', 25.00, 50.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["SID","BID"]'),
(203, 'cat', 25.00, 50.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["SID"]'),

-- Terbinafine (204)
(204, 'dog', 30.00, 40.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["SID"]'),
(204, 'cat', 30.00, 40.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["SID, alternate weeks"]'),

-- Medetomidine (211)
(211, 'dog', 10.00, 40.00, 'mcg/kg', NOW(), NOW(), '["IV","IM"]', '["PRN"]'),
(211, 'cat', 10.00, 40.00, 'mcg/kg', NOW(), NOW(), '["IV","IM"]', '["PRN"]'),

-- Dexmedetomidine (212)
(212, 'dog', 5.00, 20.00, 'mcg/kg', NOW(), NOW(), '["IV","IM"]', '["PRN"]'),
(212, 'cat', 5.00, 20.00, 'mcg/kg', NOW(), NOW(), '["IV","IM"]', '["PRN"]'),

-- Atipamezole (213)
(213, 'dog', 0.10, 0.20, 'mg/kg', NOW(), NOW(), '["IM"]', '["PRN"]'),
(213, 'cat', 0.10, 0.20, 'mg/kg', NOW(), NOW(), '["IM"]', '["PRN"]'),

-- Midazolam (215)
(215, 'dog', 0.10, 0.30, 'mg/kg', NOW(), NOW(), '["IV","IM"]', '["PRN"]'),
(215, 'cat', 0.10, 0.30, 'mg/kg', NOW(), NOW(), '["IV","IM"]', '["PRN"]'),

-- Acepromazine (216)
(216, 'dog', 0.01, 0.05, 'mg/kg', NOW(), NOW(), '["IV","IM","SC"]', '["PRN"]'),
(216, 'cat', 0.01, 0.05, 'mg/kg', NOW(), NOW(), '["IV","IM","SC"]', '["PRN"]'),

-- Propofol (218)
(218, 'dog', 4.00, 6.00, 'mg/kg', NOW(), NOW(), '["IV"]', '["To effect"]'),
(218, 'cat', 4.00, 8.00, 'mg/kg', NOW(), NOW(), '["IV"]', '["To effect"]'),

-- Glycopyrrolate (221)
(221, 'dog', 0.005, 0.010, 'mg/kg', NOW(), NOW(), '["IV","IM","SC"]', '["PRN"]'),
(221, 'cat', 0.005, 0.010, 'mg/kg', NOW(), NOW(), '["IV","IM","SC"]', '["PRN"]'),

-- Potassium Bromide (231)
(231, 'dog', 20.00, 40.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["SID"]'),

-- Levetiracetam (232)
(232, 'dog', 20.00, 60.00, 'mg/kg', NOW(), NOW(), '["Oral","IV"]', '["TID"]'),
(232, 'cat', 20.00, 60.00, 'mg/kg', NOW(), NOW(), '["Oral","IV"]', '["TID"]'),

-- Zonisamide (233)
(233, 'dog', 5.00, 10.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["BID"]'),

-- Methimazole (240)
(240, 'cat', 1.25, 2.50, 'mg/cat', NOW(), NOW(), '["Oral"]', '["BID"]'),

-- Levothyroxine (241)
(241, 'dog', 10.00, 22.00, 'mcg/kg', NOW(), NOW(), '["Oral"]', '["BID"]'),

-- Trilostane (242)
(242, 'dog', 1.00, 3.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["SID","BID"]'),

-- Ursodeoxycholic Acid (250)
(250, 'dog', 10.00, 15.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["SID","BID"]'),
(250, 'cat', 10.00, 15.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["SID"]'),

-- SAMe (251)
(251, 'dog', 17.00, 20.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["SID"]'),
(251, 'cat', 17.00, 20.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["SID"]'),

-- Silymarin (252)
(252, 'dog', 20.00, 50.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["SID"]'),
(252, 'cat', 20.00, 50.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["SID"]'),

-- Azathioprine (261)
(261, 'dog', 1.00, 2.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["SID then EOD"]'),

-- Mycophenolate (262)
(262, 'dog', 10.00, 20.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["BID"]'),

-- Dopamine (271)
(271, 'dog', 2.00, 10.00, 'mcg/kg/min', NOW(), NOW(), '["IV CRI"]', '["CRI"]'),
(271, 'cat', 2.00, 10.00, 'mcg/kg/min', NOW(), NOW(), '["IV CRI"]', '["CRI"]'),

-- Dobutamine (272)
(272, 'dog', 2.00, 15.00, 'mcg/kg/min', NOW(), NOW(), '["IV CRI"]', '["CRI"]'),
(272, 'cat', 2.00, 15.00, 'mcg/kg/min', NOW(), NOW(), '["IV CRI"]', '["CRI"]'),

-- Mannitol (273)
(273, 'dog', 0.50, 1.00, 'g/kg', NOW(), NOW(), '["IV over 15-20 min"]', '["PRN, Q6-8H"]'),
(273, 'cat', 0.25, 0.50, 'g/kg', NOW(), NOW(), '["IV over 15-20 min"]', '["PRN"]'),

-- Calcium Gluconate (274)
(274, 'dog', 0.50, 1.50, 'ml/kg', NOW(), NOW(), '["IV slow"]', '["PRN"]'),
(274, 'cat', 0.50, 1.00, 'ml/kg', NOW(), NOW(), '["IV slow"]', '["PRN"]'),

-- Naloxone (277)
(277, 'dog', 0.01, 0.04, 'mg/kg', NOW(), NOW(), '["IV","IM"]', '["PRN"]'),
(277, 'cat', 0.01, 0.04, 'mg/kg', NOW(), NOW(), '["IV","IM"]', '["PRN"]'),

-- Glucosamine + Chondroitin (296)
(296, 'dog', 25.00, 50.00, 'mg/kg', NOW(), NOW(), '["Oral"]', '["SID"]');
