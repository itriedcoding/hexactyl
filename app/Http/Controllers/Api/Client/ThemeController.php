<?php

namespace Hexactyl\Http\Controllers\Api\Client;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Hexactyl\Models\Setting;
use Hexactyl\Services\Themes\ThemeEngine;

class ThemeController extends ClientApiController
{
    /**
     * Returns all 5 available themes.
     */
    public function getThemes(): array
    {
        $themes = [];
        $currentTheme = $this->getCurrentThemeSlug();

        foreach (ThemeEngine::getThemes() as $key => $theme) {
            $themes[$key] = [
                'name' => $theme['name'],
                'description' => $theme['description'],
                'primary_color' => $theme['primary_color'],
                'bg_color' => $theme['bg_color'],
                'accent_color' => $theme['accent_color'],
                'active' => $currentTheme === $key,
            ];
        }

        return [
            'themes' => $themes,
            'current' => $currentTheme,
        ];
    }

    /**
     * Returns the currently active theme.
     */
    public function getCurrentTheme(): array
    {
        return [
            'theme' => $this->getCurrentThemeSlug(),
        ];
    }

    /**
     * Sets the active theme for the panel (5 options: hexactyl, midnight, forest, sunset, light).
     */
    public function setTheme(Request $request): JsonResponse
    {
        $validThemes = array_keys(ThemeEngine::getThemes());

        $request->validate([
            'theme' => 'required|string|in:' . implode(',', $validThemes),
        ]);

        $theme = $request->input('theme');

        Setting::updateOrCreate(
            ['key' => 'settings::active_theme'],
            ['value' => $theme]
        );

        $themeName = ThemeEngine::getTheme($theme)['name'] ?? $theme;

        return new JsonResponse([
            'theme' => $theme,
            'message' => "Theme changed to {$themeName}.",
        ], Response::HTTP_OK);
    }

    /**
     * Gets the current theme slug from settings.
     */
    private function getCurrentThemeSlug(): string
    {
        $setting = Setting::where('key', 'settings::active_theme')->first();

        return $setting->value ?? config('hexactyl.active_theme', 'hexactyl');
    }
}
