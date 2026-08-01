<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function index()
    {
        return view('about', [
            'stats' => config('content_sources.stats'),
            'sources' => config('content_sources.sources'),
            'accuracy' => config('content_sources.accuracy'),
            'honestyQuote' => config('content_sources.honesty_quote'),
            'auditTimestamp' => config('content_sources.audit_timestamp'),
        ]);
    }
}
