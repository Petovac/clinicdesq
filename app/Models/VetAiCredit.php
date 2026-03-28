<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VetAiCredit extends Model
{
    protected $fillable = ['vet_id', 'balance', 'total_purchased', 'total_used'];

    public function vet()
    {
        return $this->belongsTo(Vet::class);
    }

    public function transactions()
    {
        return $this->hasMany(VetAiTransaction::class, 'vet_id', 'vet_id');
    }

    public static function getOrCreate($vetId): self
    {
        return self::firstOrCreate(
            ['vet_id' => $vetId],
            ['balance' => 0, 'total_purchased' => 0, 'total_used' => 0]
        );
    }

    public function addCredits(int $credits, string $description, string $type = 'purchase', ?string $reference = null): VetAiTransaction
    {
        $this->increment('balance', $credits);
        $this->increment('total_purchased', $credits);

        return VetAiTransaction::create([
            'vet_id' => $this->vet_id,
            'type' => $type,
            'credits' => $credits,
            'balance_after' => $this->fresh()->balance,
            'description' => $description,
            'reference' => $reference,
        ]);
    }

    public function deductCredits(int $credits, string $description, ?string $aiFeature = null, ?int $appointmentId = null, array $tokenUsage = []): VetAiTransaction
    {
        $this->decrement('balance', $credits);
        $this->increment('total_used', $credits);

        return VetAiTransaction::create([
            'vet_id' => $this->vet_id,
            'type' => 'deduction',
            'credits' => $credits,
            'balance_after' => $this->fresh()->balance,
            'description' => $description,
            'ai_feature' => $aiFeature,
            'appointment_id' => $appointmentId,
            'input_tokens' => $tokenUsage['input_tokens'] ?? null,
            'output_tokens' => $tokenUsage['output_tokens'] ?? null,
            'cost_usd' => $tokenUsage['cost_usd'] ?? null,
        ]);
    }

    public function hasCredits(int $required = 1): bool
    {
        return $this->balance >= $required;
    }

    public static $creditCosts = [
        'refine' => 1,
        'clinical_insights' => 1,
        'senior_support' => 2,
        'prescription_support' => 2,
    ];

    public static function costFor(string $feature): int
    {
        return self::$creditCosts[$feature] ?? 1;
    }
}
