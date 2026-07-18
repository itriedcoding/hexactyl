<?php

namespace Hexactyl\\Http\Controllers\Admin;

use Illuminate\View\View;
use Hexactyl\\Http\Controllers\Controller;
use Hexactyl\\Services\Helpers\SoftwareVersionService;

class BaseController extends Controller
{
    /**
     * BaseController constructor.
     */
    public function __construct(private SoftwareVersionService $version)
    {
    }

    /**
     * Return the admin index view.
     */
    public function index(): View
    {
        return view('admin.index', ['version' => $this->version]);
    }
}
