<?php
/**
 * Expand the form enum in drug_brands to support more pharmaceutical forms.
 * Run: php artisan tinker database/seeds/expand_form_enum.php
 */

use Illuminate\Support\Facades\DB;

DB::statement("ALTER TABLE drug_brands MODIFY COLUMN form ENUM(
    'injection', 'vial', 'tablet', 'syrup', 'ointment', 'shampoo', 'drops',
    'capsule', 'suspension', 'chewable', 'cream', 'eye drops', 'ear drops',
    'spot-on', 'solution', 'gel', 'sachet', 'powder', 'spray', 'paste',
    'nebulizer', 'suppository', 'topical'
) NOT NULL DEFAULT 'tablet'");

echo "Form enum expanded successfully!\n";
echo "Allowed values: injection, vial, tablet, syrup, ointment, shampoo, drops, capsule, suspension, chewable, cream, eye drops, ear drops, spot-on, solution, gel, sachet, powder, spray, paste, nebulizer, suppository, topical\n";
