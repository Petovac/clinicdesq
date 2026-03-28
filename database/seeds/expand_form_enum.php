<?php
/**
 * Expand the form enum in drug_brands to support more pharmaceutical forms.
 * Run: php artisan tinker database/seeds/expand_form_enum.php
 *
 * IMPORTANT: Preserves existing values (oral_suspension, chewable_tablet, infusion)
 * while adding new form types.
 */

use Illuminate\Support\Facades\DB;

DB::statement("ALTER TABLE drug_brands MODIFY COLUMN form ENUM(
    'injection', 'vial', 'tablet', 'syrup', 'ointment', 'shampoo', 'drops',
    'capsule', 'suspension', 'oral_suspension', 'chewable', 'chewable_tablet',
    'cream', 'eye_drops', 'ear_drops', 'spot_on', 'solution', 'gel', 'sachet',
    'powder', 'spray', 'paste', 'nebulizer', 'suppository', 'topical', 'infusion'
) NOT NULL DEFAULT 'tablet'");

echo "Form enum expanded successfully!\n";
echo "Total values: 26\n";
echo "Preserved existing: oral_suspension, chewable_tablet, infusion\n";
echo "Added new: suspension, chewable, cream, eye_drops, ear_drops, spot_on, solution, gel, sachet, powder, spray, paste, nebulizer, suppository, topical\n";
