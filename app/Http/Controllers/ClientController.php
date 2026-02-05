<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function getUsers()
    {
        $users = DB::table('user')->get();
        return response()->json([
            'success' => true,
            'data' => $users  
        ]);
    }

    public function getBeneficiaries($userId)
    {
        $beneficiaries = DB::table('beneficiaire')
            ->where('userId', $userId)
            ->get();
        return response()->json($beneficiaries);
    }

    public function getUserAccounts($userId)
    {
        try {
            $accounts = \App\Models\Compte::where('userId', $userId)->get();

            if ($accounts->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No saved accounts found for this user.',
                    'data' => []
                ], 200);
            }

            return response()->json([
                'success' => true,
                'data' => $accounts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
