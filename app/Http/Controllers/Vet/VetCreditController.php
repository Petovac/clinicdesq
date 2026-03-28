<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use App\Models\VetAiCredit;
use App\Models\VetAiTransaction;
use Illuminate\Http\Request;

class VetCreditController extends Controller
{
    public function index()
    {
        $vet = auth('vet')->user();
        $credit = VetAiCredit::getOrCreate($vet->id);
        $packs = VetAiTransaction::$packs;
        $creditCosts = VetAiCredit::$creditCosts;

        $transactions = VetAiTransaction::where('vet_id', $vet->id)
            ->orderByDesc('created_at')
            ->paginate(20);

        // Token usage stats
        $usageStats = VetAiTransaction::where('vet_id', $vet->id)
            ->where('type', 'deduction')
            ->selectRaw('
                SUM(input_tokens) as total_input,
                SUM(output_tokens) as total_output,
                SUM(cost_usd) as total_cost_usd,
                COUNT(*) as total_requests
            ')
            ->first();

        // Per-feature breakdown
        $featureBreakdown = VetAiTransaction::where('vet_id', $vet->id)
            ->where('type', 'deduction')
            ->whereNotNull('ai_feature')
            ->selectRaw('
                ai_feature,
                COUNT(*) as uses,
                AVG(input_tokens) as avg_input,
                AVG(output_tokens) as avg_output,
                AVG(cost_usd) as avg_cost
            ')
            ->groupBy('ai_feature')
            ->get();

        return view('vet.credits.index', compact('credit', 'packs', 'creditCosts', 'transactions', 'usageStats', 'featureBreakdown'));
    }

    public function purchase(Request $request)
    {
        $request->validate([
            'pack' => 'required|string|in:starter,standard,pro',
        ]);

        $vet = auth('vet')->user();
        $pack = VetAiTransaction::$packs[$request->pack];
        $credit = VetAiCredit::getOrCreate($vet->id);

        $credit->addCredits(
            $pack['credits'],
            "Purchased {$pack['name']} ({$pack['credits']} credits)",
            'purchase',
            $request->pack
        );

        return redirect()
            ->route('vet.credits.index')
            ->with('success', "Purchased {$pack['name']} — {$pack['credits']} credits added to your account.");
    }

    public function balance()
    {
        $vet = auth('vet')->user();
        $credit = VetAiCredit::getOrCreate($vet->id);

        return response()->json([
            'balance' => $credit->balance,
            'total_used' => $credit->total_used,
        ]);
    }
}
