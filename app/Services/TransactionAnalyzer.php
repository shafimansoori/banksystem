<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TransactionAnalyzer
{
    private $openaiApiKey;

    public function __construct()
    {
        $this->openaiApiKey = env('OPENAI_API_KEY');
    }

    /**
     * Analyze transaction description for suspicious activity
     */
    public function analyzeTransaction(string $description, float $amount, string $type): array
    {
        // Simple keyword-based analysis (local, fast, no API cost)
        $localAnalysis = $this->performLocalAnalysis($description, $amount, $type);

        // If local analysis flags it as potentially risky, use AI for deeper analysis
        if ($localAnalysis['risk_level'] !== 'safe') {
            return $this->performAIAnalysis($description, $amount, $type, $localAnalysis);
        }

        return $localAnalysis;
    }

    /**
     * Perform local keyword-based analysis (Text Mining)
     */
    private function performLocalAnalysis(string $description, float $amount, string $type): array
    {
        $description = strtolower($description);
        $riskLevel = 'safe';
        $reasons = [];

        // Suspicious keywords
        $suspiciousKeywords = [
            'high' => ['urgent', 'lottery', 'prize', 'winner', 'casino', 'gambling', 'betting'],
            'medium' => ['transfer', 'foreign', 'offshore', 'gift', 'donation', 'charity'],
            'low' => ['cash', 'atm', 'withdrawal']
        ];

        // Check for suspicious keywords
        foreach ($suspiciousKeywords['high'] as $keyword) {
            if (str_contains($description, $keyword)) {
                $riskLevel = 'high';
                $reasons[] = "Contains high-risk keyword: '{$keyword}'";
            }
        }

        if ($riskLevel !== 'high') {
            foreach ($suspiciousKeywords['medium'] as $keyword) {
                if (str_contains($description, $keyword)) {
                    $riskLevel = 'medium';
                    $reasons[] = "Contains medium-risk keyword: '{$keyword}'";
                }
            }
        }

        if ($riskLevel === 'safe') {
            foreach ($suspiciousKeywords['low'] as $keyword) {
                if (str_contains($description, $keyword)) {
                    $riskLevel = 'low';
                    $reasons[] = "Contains low-risk keyword: '{$keyword}'";
                }
            }
        }

        // Large amount analysis
        if ($amount > 10000) {
            if ($riskLevel === 'safe') {
                $riskLevel = 'low';
            } elseif ($riskLevel === 'low') {
                $riskLevel = 'medium';
            }
            $reasons[] = "Large transaction amount: $" . number_format($amount, 2);
        }

        // Very short or unclear descriptions
        if (strlen(trim($description)) < 5) {
            if ($riskLevel === 'safe') {
                $riskLevel = 'low';
            }
            $reasons[] = "Description is too short or unclear";
        }

        return [
            'risk_level' => $riskLevel,
            'analysis' => empty($reasons) ? 'Transaction appears normal' : implode('. ', $reasons),
            'is_flagged' => in_array($riskLevel, ['medium', 'high'])
        ];
    }

    /**
     * Perform AI-based deep analysis using OpenAI
     */
    private function performAIAnalysis(string $description, float $amount, string $type, array $localAnalysis): array
    {
        if (empty($this->openaiApiKey)) {
            // If no API key, return local analysis
            return $localAnalysis;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->openaiApiKey,
                'Content-Type' => 'application/json',
            ])->timeout(10)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a fraud detection expert analyzing bank transactions. Analyze the transaction and respond with a JSON object containing: risk_level (safe/low/medium/high), reason (short explanation), and suspicious (boolean).'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Analyze this transaction:\nDescription: {$description}\nAmount: \${$amount}\nType: {$type}\n\nLocal analysis flagged it as: {$localAnalysis['risk_level']}"
                    ]
                ],
                'temperature' => 0.3,
                'max_tokens' => 150
            ]);

            if ($response->successful()) {
                $content = $response->json()['choices'][0]['message']['content'] ?? '';

                // Try to parse JSON response
                $aiResult = json_decode($content, true);

                if ($aiResult && isset($aiResult['risk_level'])) {
                    return [
                        'risk_level' => $aiResult['risk_level'],
                        'analysis' => $aiResult['reason'] ?? $aiResult['analysis'] ?? 'AI analysis completed',
                        'is_flagged' => $aiResult['suspicious'] ?? in_array($aiResult['risk_level'], ['medium', 'high'])
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::error('AI Transaction Analysis Failed: ' . $e->getMessage());
        }

        // Fallback to local analysis if AI fails
        return $localAnalysis;
    }

    /**
     * Get statistics about flagged transactions
     */
    public function getStatistics(): array
    {
        $totalFlagged = \DB::table('bank_transactions')
            ->where('is_flagged', true)
            ->count();

        $byRiskLevel = \DB::table('bank_transactions')
            ->select('risk_level', \DB::raw('count(*) as count'))
            ->groupBy('risk_level')
            ->get()
            ->pluck('count', 'risk_level')
            ->toArray();

        return [
            'total_flagged' => $totalFlagged,
            'by_risk_level' => $byRiskLevel
        ];
    }
}
