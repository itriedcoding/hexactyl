@extends('templates/wrapper', [
    'css' => ['body' => 'bg-neutral-800'],
])

@section('container')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-white mb-2">Theme Settings</h1>
    <p class="text-neutral-400 mb-8">Choose from 5 beautiful themes to customize your Hexactyl panel.</p>

    <!-- Theme Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @foreach($themes as $key => $theme)
        <div class="theme-card rounded-xl overflow-hidden border-2 transition-all duration-200 {{ $currentTheme === $key ? 'border-purple-500 ring-2 ring-purple-500/50' : 'border-neutral-600 hover:border-neutral-400' }}"
             style="background: linear-gradient(135deg, {{ $theme['primary_color'] }} 0%, {{ $theme['bg_color'] }} 100%);"
             onclick="selectTheme('{{ $key }}')">

            @if($currentTheme === $key)
            <div class="absolute top-3 right-3 w-6 h-6 rounded-full bg-white flex items-center justify-center">
                <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </div>
            @endif

            <div class="p-6">
                <div class="flex items-center space-x-3 mb-3">
                    <div class="w-8 h-8 rounded-full border-2 border-white/30" style="background-color: {{ $theme['primary_color'] }}"></div>
                    <div class="w-6 h-6 rounded-full border-2 border-white/30" style="background-color: {{ $theme['bg_color'] }}"></div>
                    <div class="w-4 h-4 rounded-full border-2 border-white/30" style="background-color: {{ $theme['accent_color'] }}"></div>
                </div>
                <h3 class="text-xl font-bold text-white drop-shadow-lg">{{ $theme['name'] }}</h3>
                <p class="text-white/70 text-sm mt-1 drop-shadow">{{ $theme['description'] }}</p>
                <div class="mt-4 flex items-center justify-between">
                    <span class="text-xs text-white/50">{{ ucfirst(str_replace('_', ' ', $key)) }}</span>
                    @if($currentTheme === $key)
                    <span class="text-xs bg-white/20 text-white px-2 py-1 rounded-full">Active</span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Theme Preview -->
    <div id="theme-preview" class="hidden mb-8 p-6 rounded-xl border border-neutral-600 bg-neutral-900">
        <h3 class="text-lg font-semibold text-white mb-4">Theme Preview</h3>
        <div id="preview-content" class="space-y-4"></div>
    </div>

    <!-- Apply Button -->
    <form id="theme-form" method="POST" action="{{ route('admin.settings.theme.update') }}">
        @csrf
        @method('PUT')
        <input type="hidden" name="theme" id="selected-theme" value="{{ $currentTheme }}">
        <div class="flex items-center space-x-4">
            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-medium py-3 px-6 rounded-lg transition-colors">
                Apply Theme
            </button>
            <span id="current-theme-label" class="text-neutral-400">Current: {{ $themes[$currentTheme]['name'] ?? 'Unknown' }}</span>
        </div>
    </form>

    <!-- Custom CSS Toggle -->
    <div class="mt-8 p-6 rounded-xl border border-neutral-600 bg-neutral-900">
        <h3 class="text-lg font-semibold text-white mb-2">Custom CSS</h3>
        <p class="text-neutral-400 text-sm mb-4">Allow custom CSS overrides for advanced theming.</p>
        <form method="POST" action="{{ route('admin.settings.theme.custom-css') }}">
            @csrf
            <label class="flex items-center space-x-3 cursor-pointer">
                <input type="checkbox" name="enabled" value="1" {{ $customCssEnabled ? 'checked' : '' }}
                       class="w-5 h-5 rounded border-neutral-600 text-purple-600 focus:ring-purple-500">
                <span class="text-neutral-300">Enable Custom CSS</span>
            </label>
        </form>
    </div>
</div>

<script>
function selectTheme(themeKey) {
    document.getElementById('selected-theme').value = themeKey;

    // Update UI
    document.querySelectorAll('.theme-card').forEach(card => {
        card.classList.remove('border-purple-500', 'ring-2', 'ring-purple-500/50');
        card.classList.add('border-neutral-600');
    });

    event.currentTarget.classList.remove('border-neutral-600');
    event.currentTarget.classList.add('border-purple-500', 'ring-2', 'ring-purple-500/50');

    // Show preview
    fetch('/admin/settings/theme/css/' + themeKey)
        .then(r => r.text())
        .then(css => {
            const preview = document.getElementById('theme-preview');
            preview.classList.remove('hidden');
            document.getElementById('current-theme-label').textContent = 'Selected: ' + themeKey.charAt(0).toUpperCase() + themeKey.slice(1);
        });
}
</script>
@endsection
