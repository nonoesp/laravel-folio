<?php

namespace Nonoesp\Folio\Controllers;

use Illuminate\Http\Request;

class DebugController extends Controller
{
	/**
     * An informal test of some dependencies and features.
     */
    public function helloFolio(Request $request, $domain) {
	    return view('folio::debug.test')->with(['amount' => 3]);
	}

    /**
     * An informal test of some dependencies and features.
     */
    public function loadTime(Request $request, $domain) {
	    return view('folio::debug.load-time');
    }

    /**
     * An informal test of some dependencies and features.
     */
    public function time(Request $request, $domain) {
	    return view('folio::debug.time');
    }
    
    /**
     * An informal test of some dependencies and features.
     */    
    public function templateStats(Request $request, $domain) {

        return view('folio::debug.template-stats', [
            'template_groups' => \Folio::templates()
          ]);
          
    }
	
}
