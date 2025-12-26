<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     title="Bank System API",
 *     version="1.0.0",
 *     description="API documentation for Bank Management System with AI-powered fraud detection. All endpoints require authentication using Laravel Sanctum tokens.",
 *     @OA\Contact(
 *         email="support@banksystem.com"
 *     )
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8000/api/v1",
 *     description="Local Development Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="oauth2",
 *     @OA\Flow(
 *         flow="password",
 *         tokenUrl="/api/v1/login",
 *         refreshUrl="/api/v1/refresh",
 *         scopes={"api": "Access API endpoints"}
 *     )
 * )
 */
class ApiController extends Controller
{
    /**
     * @OA\Post(
     *     path="/login",
     *     summary="Login and get API token (OAuth2 Password Grant)",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"username","password"},
     *                 @OA\Property(property="username", type="string", description="User email", example="admin@gmail.com"),
     *                 @OA\Property(property="password", type="string", format="password", example="password"),
     *                 @OA\Property(property="grant_type", type="string", example="password", description="OAuth2 grant type")
     *             )
     *         ),
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"username","password"},
     *                 @OA\Property(property="username", type="string", description="User email", example="admin@gmail.com"),
     *                 @OA\Property(property="password", type="string", format="password", example="password")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful login",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string", example="1|abcdef123456789"),
     *             @OA\Property(property="token_type", type="string", example="Bearer"),
     *             @OA\Property(property="user", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials"
     *     )
     * )
     */
    public function login(Request $request)
    {
        // OAuth2 password flow uses 'username' field, but we accept both
        $email = $request->input('username') ?? $request->input('email');
        $password = $request->input('password');

        if (!$email || !$password) {
            return response()->json([
                'error' => 'invalid_request',
                'error_description' => 'Username and password are required'
            ], 400);
        }

        if (!\Auth::attempt(['email' => $email, 'password' => $password])) {
            return response()->json([
                'error' => 'invalid_grant',
                'error_description' => 'Invalid username or password'
            ], 401);
        }

        $user = User::where('email', $email)->first();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user->load('roles')
        ]);
    }

    /**
     * @OA\Get(
     *     path="/accounts",
     *     summary="Get bank accounts",
     *     tags={"Accounts"},
     *     security={{"sanctum": {}}},
     *     description="Returns all accounts for admin, or only user's own accounts for other roles",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Savings Account"),
     *                 @OA\Property(property="number", type="string", example="1234567890"),
     *                 @OA\Property(property="available_balance", type="number", format="float", example=5000.00),
     *                 @OA\Property(property="ledger_balance", type="number", format="float", example=5000.00)
     *             )
     *         )
     *     )
     * )
     */
    public function getAccounts()
    {
        $query = BankAccount::with('bank', 'bank_location', 'user');

        // If not admin, only show user's own accounts
        if (!auth()->user()->hasRole('System-Admin')) {
            $query->where('user_id', auth()->id());
        }

        $accounts = $query->orderBy('id', 'DESC')->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $accounts
        ]);
    }

    /**
     * @OA\Get(
     *     path="/accounts/{id}",
     *     summary="Get account by ID",
     *     tags={"Accounts"},
     *     security={{"sanctum": {}}},
     *     description="Get specific account. Non-admin users can only view their own accounts.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Account ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Access denied - not your account"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Account not found"
     *     )
     * )
     */
    public function getAccount($id)
    {
        $account = BankAccount::with('bank', 'bank_location', 'user')->find($id);

        if (!$account) {
            return response()->json([
                'status' => 'error',
                'message' => 'Account not found'
            ], 404);
        }

        // If not admin, check if account belongs to user
        if (!auth()->user()->hasRole('System-Admin') && $account->user_id !== auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Access denied. You can only view your own accounts.'
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'data' => $account
        ]);
    }

    /**
     * @OA\Get(
     *     path="/transactions",
     *     summary="Get transactions",
     *     tags={"Transactions"},
     *     security={{"sanctum": {}}},
     *     description="Returns all transactions for admin, or only user's own transactions for other roles",
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="transaction_code", type="string", example="TRX123456"),
     *                 @OA\Property(property="amount", type="number", format="float", example=100.00),
     *                 @OA\Property(property="type", type="string", example="credit"),
     *                 @OA\Property(property="risk_level", type="string", example="safe"),
     *                 @OA\Property(property="is_flagged", type="boolean", example=false)
     *             )
     *         )
     *     )
     * )
     */
    public function getTransactions()
    {
        $query = BankTransaction::with('bank_account', 'user');

        // If not admin, only show user's own transactions
        if (!auth()->user()->hasRole('System-Admin')) {
            $query->where('user_id', auth()->id());
        }

        $transactions = $query->orderBy('id', 'DESC')->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $transactions
        ]);
    }

    /**
     * @OA\Get(
     *     path="/transactions/flagged",
     *     summary="Get flagged/suspicious transactions (Admin only)",
     *     tags={"Transactions"},
     *     security={{"sanctum": {}}},
     *     description="Returns all transactions flagged as suspicious by AI fraud detection system. Admin access required.",
     *     @OA\Parameter(
     *         name="risk_level",
     *         in="query",
     *         description="Filter by risk level",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"low", "medium", "high"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="total_flagged", type="integer", example=15),
     *                 @OA\Property(
     *                     property="statistics",
     *                     type="object",
     *                     @OA\Property(property="high", type="integer", example=3),
     *                     @OA\Property(property="medium", type="integer", example=7),
     *                     @OA\Property(property="low", type="integer", example=5)
     *                 ),
     *                 @OA\Property(
     *                     property="transactions",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="transaction_code", type="string"),
     *                         @OA\Property(property="amount", type="number", format="float"),
     *                         @OA\Property(property="risk_level", type="string"),
     *                         @OA\Property(property="analysis_result", type="string"),
     *                         @OA\Property(property="is_flagged", type="boolean")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Admin access required"
     *     )
     * )
     */
    public function getFlaggedTransactions(Request $request)
    {
        // Check if user is admin
        if (!auth()->user()->hasRole('System-Admin')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Access denied. Admin privileges required.'
            ], 403);
        }

        $query = BankTransaction::with('bank_account', 'user')
            ->where('is_flagged', true);

        if ($request->has('risk_level')) {
            $query->where('risk_level', $request->risk_level);
        }

        $transactions = $query->orderBy('risk_level', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'high' => BankTransaction::where('risk_level', 'high')->count(),
            'medium' => BankTransaction::where('risk_level', 'medium')->count(),
            'low' => BankTransaction::where('risk_level', 'low')->count(),
        ];

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_flagged' => BankTransaction::where('is_flagged', true)->count(),
                'statistics' => $stats,
                'transactions' => $transactions
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/users",
     *     summary="Get all users (Admin only)",
     *     tags={"Users"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Admin access required"
     *     )
     * )
     */
    public function getUsers()
    {
        // Check if user is admin
        if (!auth()->user()->hasRole('System-Admin')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Access denied. Admin privileges required.'
            ], 403);
        }

        $users = User::with('roles')
            ->orderBy('id', 'DESC')
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $users
        ]);
    }

    /**
     * @OA\Get(
     *     path="/statistics",
     *     summary="Get system statistics (Admin only)",
     *     tags={"Statistics"},
     *     security={{"sanctum": {}}},
     *     description="Returns overall system statistics including fraud detection metrics. Admin access required.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="total_accounts", type="integer"),
     *                 @OA\Property(property="total_transactions", type="integer"),
     *                 @OA\Property(property="total_users", type="integer"),
     *                 @OA\Property(property="flagged_transactions", type="integer"),
     *                 @OA\Property(
     *                     property="risk_distribution",
     *                     type="object",
     *                     @OA\Property(property="safe", type="integer"),
     *                     @OA\Property(property="low", type="integer"),
     *                     @OA\Property(property="medium", type="integer"),
     *                     @OA\Property(property="high", type="integer")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Admin access required"
     *     )
     * )
     */
    public function getStatistics()
    {
        // Check if user is admin
        if (!auth()->user()->hasRole('System-Admin')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Access denied. Admin privileges required.'
            ], 403);
        }

        $stats = [
            'total_accounts' => BankAccount::count(),
            'total_transactions' => BankTransaction::count(),
            'total_users' => User::count(),
            'flagged_transactions' => BankTransaction::where('is_flagged', true)->count(),
            'risk_distribution' => [
                'safe' => BankTransaction::where('risk_level', 'safe')->count(),
                'low' => BankTransaction::where('risk_level', 'low')->count(),
                'medium' => BankTransaction::where('risk_level', 'medium')->count(),
                'high' => BankTransaction::where('risk_level', 'high')->count(),
            ]
        ];

        return response()->json([
            'status' => 'success',
            'data' => $stats
        ]);
    }
}
