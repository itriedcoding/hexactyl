<?php

namespace Hexactyl\Services\Themes;

class ThemeEngine
{
    const THEMES = [
        'hexactyl' => [
            'name' => 'Hexactyl (Default)',
            'description' => 'The signature Hexactyl dark theme with purple accents',
            'primary_color' => '#7c3aed',
            'bg_color' => '#0f172a',
            'sidebar_color' => '#1e293b',
            'text_color' => '#e2e8f0',
            'accent_color' => '#a78bfa',
            'success_color' => '#22c55e',
            'warning_color' => '#f59e0b',
            'danger_color' => '#ef4444',
            'border_color' => '#334155',
            'card_bg' => '#1e293b',
            'font_family' => 'Inter, system-ui, sans-serif',
        ],
        'midnight' => [
            'name' => 'Midnight Blue',
            'description' => 'Deep blue dark theme for night owls',
            'primary_color' => '#3b82f6',
            'bg_color' => '#020617',
            'sidebar_color' => '#0f172a',
            'text_color' => '#cbd5e1',
            'accent_color' => '#60a5fa',
            'success_color' => '#10b981',
            'warning_color' => '#fbbf24',
            'danger_color' => '#f87171',
            'border_color' => '#1e293b',
            'card_bg' => '#0f172a',
            'font_family' => 'Inter, system-ui, sans-serif',
        ],
        'forest' => [
            'name' => 'Forest Green',
            'description' => 'Nature-inspired green theme',
            'primary_color' => '#16a34a',
            'bg_color' => '#052e16',
            'sidebar_color' => '#14532d',
            'text_color' => '#dcfce7',
            'accent_color' => '#4ade80',
            'success_color' => '#22c55e',
            'warning_color' => '#facc15',
            'danger_color' => '#f87171',
            'border_color' => '#166534',
            'card_bg' => '#14532d',
            'font_family' => 'Inter, system-ui, sans-serif',
        ],
        'sunset' => [
            'name' => 'Sunset Orange',
            'description' => 'Warm orange and red tones',
            'primary_color' => '#ea580c',
            'bg_color' => '#1c0a00',
            'sidebar_color' => '#431407',
            'text_color' => '#fed7aa',
            'accent_color' => '#fb923c',
            'success_color' => '#4ade80',
            'warning_color' => '#facc15',
            'danger_color' => '#ef4444',
            'border_color' => '#7c2d12',
            'card_bg' => '#431407',
            'font_family' => 'Inter, system-ui, sans-serif',
        ],
        'light' => [
            'name' => 'Clean Light',
            'description' => 'Bright light theme for daytime use',
            'primary_color' => '#6d28d9',
            'bg_color' => '#f8fafc',
            'sidebar_color' => '#ffffff',
            'text_color' => '#1e293b',
            'accent_color' => '#8b5cf6',
            'success_color' => '#16a34a',
            'warning_color' => '#d97706',
            'danger_color' => '#dc2626',
            'border_color' => '#e2e8f0',
            'card_bg' => '#ffffff',
            'font_family' => 'Inter, system-ui, sans-serif',
        ],
    ];

    public static function getThemes(): array
    {
        return self::THEMES;
    }

    public static function getTheme(string $key): ?array
    {
        return self::THEMES[$key] ?? null;
    }

    public static function getThemeNames(): array
    {
        return array_map(fn($theme) => $theme['name'], self::THEMES);
    }

    public static function getThemeKeys(): array
    {
        return array_keys(self::THEMES);
    }

    public static function generateCssVariables(string $themeKey): string
    {
        $theme = self::getTheme($themeKey);
        if (!$theme) {
            $theme = self::getTheme('hexactyl');
        }

        $variables = [];
        foreach ($theme as $key => $value) {
            if ($key !== 'name' && $key !== 'description') {
                $cssKey = str_replace('_', '-', $key);
                $variables[] = "--hx-{$cssKey}: {$value}";
            }
        }

        return ':root {' . implode('; ', $variables) . '}';
    }

    public static function generateFullCss(string $themeKey): string
    {
        $theme = self::getTheme($themeKey);
        if (!$theme) {
            $theme = self::getTheme('hexactyl');
        }

        $css = self::generateCssVariables($themeKey);

        $css .= <<<'CSS'

/* Hexactyl Theme Engine - Auto-generated styles */
body {
    background-color: var(--hx-bg-color);
    color: var(--hx-text-color);
    font-family: var(--hx-font-family);
}

.hx-sidebar {
    background-color: var(--hx-sidebar-color);
    border-right: 1px solid var(--hx-border-color);
}

.hx-card {
    background-color: var(--hx-card-bg);
    border: 1px solid var(--hx-border-color);
    border-radius: 0.5rem;
}

.hx-btn-primary {
    background-color: var(--hx-primary-color);
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    cursor: pointer;
    transition: opacity 0.2s;
}

.hx-btn-primary:hover {
    opacity: 0.9;
}

.hx-btn-success {
    background-color: var(--hx-success-color);
    color: white;
}

.hx-btn-warning {
    background-color: var(--hx-warning-color);
    color: white;
}

.hx-btn-danger {
    background-color: var(--hx-danger-color);
    color: white;
}

.hx-text-accent {
    color: var(--hx-accent-color);
}

.hx-border {
    border-color: var(--hx-border-color);
}

.hx-input {
    background-color: var(--hx-sidebar-color);
    border: 1px solid var(--hx-border-color);
    color: var(--hx-text-color);
    padding: 0.5rem 0.75rem;
    border-radius: 0.375rem;
}

.hx-input:focus {
    outline: none;
    border-color: var(--hx-primary-color);
    box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.3);
}

.hx-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.125rem 0.5rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
}

.hx-badge-primary {
    background-color: var(--hx-primary-color);
    color: white;
}

.hx-badge-success {
    background-color: var(--hx-success-color);
    color: white;
}

.hx-badge-danger {
    background-color: var(--hx-danger-color);
    color: white;
}

.hx-badge-warning {
    background-color: var(--hx-warning-color);
    color: white;
}
CSS;

        return $css;
    }

    public function getCurrentTheme(): string
    {
        return config('hexactyl.active_theme', 'hexactyl');
    }

    public function setCurrentTheme(string $themeKey): bool
    {
        if (!isset(self::THEMES[$themeKey])) {
            return false;
        }

        config(['hexactyl.active_theme' => $themeKey]);
        return true;
    }
}