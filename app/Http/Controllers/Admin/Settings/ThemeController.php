<?php

namespace Hexactyl\Http\Controllers\Admin\Settings;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Prologue\Alerts\AlertsMessageBag;
use Hexactyl\Http\Controllers\Controller;
use Hexactyl\Contracts\Repository\SettingsRepositoryInterface;
use Hexactyl\Services\Themes\ThemeEngine;

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
     * Display theme settings page with all 5 themes.
     */
    public function index(): View
    {
        $currentTheme = $this->settings->get('settings::active_theme', 'hexactyl');
        $customCssEnabled = $this->settings->get('settings::custom_css_enabled', false);

        return view('admin.settings.theme', [
            'currentTheme' => $currentTheme,
            'themes' => ThemeEngine::getThemes(),
            'customCssEnabled' => $customCssEnabled,
        ]);
    }

    /**
     * Update panel theme setting.
     *
     * @throws \Hexactyl\Exceptions\Model\DataValidationException
     * @throws \Hexactyl\Exceptions\Repository\RecordNotFoundException
     */
    public function update(Request $request): RedirectResponse
    {
        $themeKeys = array_keys(ThemeEngine::getThemes());

        $request->validate([
            'theme' => 'required|string|in:' . implode(',', $themeKeys),
        ]);

        $theme = $request->input('theme');
        $this->settings->set('settings::active_theme', $theme);

        $themeName = ThemeEngine::getTheme($theme)['name'] ?? $theme;
        $this->alert->success("Panel theme has been changed to \"$themeName\".")->flash();

        return redirect()->route('admin.settings.theme');
    }

    /**
     * Preview a theme without applying it.
     */
    public function preview(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate(['theme' => 'required|string']);

        $theme = ThemeEngine::getTheme($request->input('theme'));
        if (!$theme) {
            return response()->json(['error' => 'Theme not found'], 404);
        }

        return response()->json([
            'theme' => $theme,
            'css_variables' => ThemeEngine::generateCssVariables($request->input('theme')),
        ]);
    }

    /**
     * Get theme CSS for a specific theme.
     */
    public function css(string $theme): \Illuminate\Http\Response
    {
        $css = ThemeEngine::generateFullCss($theme);

        return response($css, 200, [
            'Content-Type' => 'text/css',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * Toggle custom CSS support.
     */
    public function toggleCustomCss(Request $request): RedirectResponse
    {
        $enabled = $request->boolean('enabled', false);
        $this->settings->set('settings::custom_css_enabled', $enabled);

        $status = $enabled ? 'enabled' : 'disabled';
        $this->alert->success("Custom CSS has been $status.")->flash();

        return redirect()->route('admin.settings.theme');
    }
}
