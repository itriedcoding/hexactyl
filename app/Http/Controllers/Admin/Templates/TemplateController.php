<?php

namespace Hexactyl\Http\Controllers\Admin\Templates;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Prologue\Alerts\AlertsMessageBag;
use Hexactyl\Http\Controllers\Controller;
use Hexactyl\Models\ServerTemplate;
use Hexactyl\Services\Templates\TemplateCreationService;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    /**
     * TemplateController constructor.
     */
    public function __construct(
        protected AlertsMessageBag $alert,
        protected TemplateCreationService $templateCreationService,
    ) {
    }

    /**
     * Display a listing of server templates.
     */
    public function index(): View
    {
        $templates = ServerTemplate::with(['egg', 'nest', 'location'])
            ->latest()
            ->paginate(20);

        return view('admin.templates.index', [
            'templates' => $templates,
        ]);
    }

    /**
     * Store a newly created server template.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $template = $this->templateCreationService->create($request->all());

        $this->alert->success('Server template has been created successfully.')->flash();

        return redirect()->route('admin.templates.show', $template->id);
    }

    /**
     * Display the specified server template.
     */
    public function show(int $id): View
    {
        $template = ServerTemplate::with(['egg', 'nest', 'location', 'servers'])
            ->findOrFail($id);

        return view('admin.templates.show', [
            'template' => $template,
        ]);
    }

    /**
     * Update the specified server template.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $template = ServerTemplate::findOrFail($id);
        $validated = $this->templateCreationService->validate($request->all());

        $template->update($validated);

        $this->alert->success('Server template has been updated successfully.')->flash();

        return redirect()->route('admin.templates.show', $template->id);
    }

    /**
     * Remove the specified server template.
     */
    public function destroy(int $id): RedirectResponse
    {
        $template = ServerTemplate::findOrFail($id);

        if ($template->servers()->count() > 0) {
            $this->alert->danger('Cannot delete template that has servers using it.')->flash();
            return redirect()->route('admin.templates.show', $id);
        }

        $template->delete();

        $this->alert->success('Server template has been deleted successfully.')->flash();

        return redirect()->route('admin.templates.index');
    }

    /**
     * Duplicate the specified server template.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function duplicate(int $id): RedirectResponse
    {
        $template = $this->templateCreationService->duplicate($id);

        $this->alert->success('Server template has been duplicated successfully.')->flash();

        return redirect()->route('admin.templates.show', $template->id);
    }
}