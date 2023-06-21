<?php

namespace App\Http\Controllers;

use App\Models\system_logs;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SystemLogsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::user()->user_type == 2)
            return response([
                'message' => "O tipo de usuário não tem permissão para executar esta funcionalidade"
            ], 401);

        $logs = SystemLog::all();
        return response($logs);
    }
}
