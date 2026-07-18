<?php

namespace Hexactyl\Http\Controllers\Admin\Settings;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Prologue\Alerts\AlertsMessageBag;
use Hexactyl\Http\Controllers\Controller;
use Hexactyl\Contracts\Repository\SettingsRepositoryInterface;

class ThemeController extends Controller
{
    /**
     * ThemeController constructor.
     */
    public function __construct(
        private AlertsMessageBag $alert,
        private SettingsRepositoryInterface $settings,
    ) {
    }

    /**
     * Display theme settings page.
     */
    public function index(): View
    {
        $currentTheme = $this->settings->get('settings::theme', 'hexactyl');

        return view('admin.settings.theme', [
            'currentTheme' => $currentTheme,
            'themes' => [
                'hexactyl' => 'Hexactyl (Default)',
                'dark' => 'Dark Mode',
                'light' => 'Light Mode',
            ],
        ]);
    }

    /**
     * Update panel theme setting.
     *
     * @throws \Hexactyl\Exceptions\Model\DataValidationException
     * @throws \Hexactyl\Exceptions\Repository\RecordNotFoundException
     */
    public function update(\Illuminate\Http\Request $request): RedirectResponse
    {
        $request->validate([
            'theme' => 'required|string|in:hexactyl,dark,light',
        ]);

        $theme = $request->input('theme');
        $this->settings->set('settings::theme', $theme);

        $this->alert->success('Panel theme has been updated successfully.')->flash();

        return redirect()->route('admin.settings.theme');
    }
}