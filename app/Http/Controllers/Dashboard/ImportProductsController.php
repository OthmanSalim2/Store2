<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Jobs\ImportProducts;
use Illuminate\Http\Request;

class ImportProductsController extends Controller
{

    public function create()
    {
        return view('dashboard.products.import');
    }

    public function store(Request $request)
    {
        $jobs = new ImportProducts($request->post('count'));
        // $jobs->onQueue('import')->onConnection('database')->delay(now()->addSeconds(5));
        $jobs->onQueue('import')->delay(now()->addSeconds(5));
        $this->dispatch($jobs);

        return redirect()
            ->route('dashboard.products.index')
            ->with('success', 'Import is running...');
    }
}
