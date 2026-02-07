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

    public function getBeneficiaries($userId)
    {
        $beneficiaries = DB::table('beneficiaire')
            ->join('user', 'beneficiaire.benId', '=', 'user.userID')
            ->where('beneficiaire.userId', $userId)
            ->select('user.userID', 'user.nprenom', 'user.phone', 'user.cpt')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $beneficiaries
        ]);
    }
    
    public function getFilteredBeneficiaries(Request $request)
    {
        $type = $request->query('type'); 
        $authUserId = $request->query('userId');

        $beneficiaries = DB::table('beneficiaire')
            ->join('user', 'beneficiaire.benId', '=', 'user.userID')
            ->where('beneficiaire.userId', $authUserId)
            ->when($type === 'Depot Mobile', function ($query) {
                return $query->whereNotNull('user.phone')->where('user.phone', '!=', '');
            })
            ->when($type === 'Depot Banquaire', function ($query) {
                return $query->whereNotNull('user.cpt');
            })
            ->select('user.userID', 'user.nprenom', 'user.phone', 'user.cpt')
            ->get();

        return response()->json([
            'success' => true,
            'selected_type' => $type,
            'data' => $beneficiaries
        ]);
    }

    public function getTransactionSummary()
    {
        //
    }
}
