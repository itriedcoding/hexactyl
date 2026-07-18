import React, { useState } from 'react';
import styled from 'styled-components/macro';
import tw from 'twin.macro';
import { Dialog } from '@/components/elements/dialog';
import Button from '@/components/elements/Button';
import useFlash from '@/plugins/useFlash';

interface Theme {
    key: string;
    name: string;
    description: string;
    primary_color: string;
    bg_color: string;
    preview_gradient: string;
}

const themes: Theme[] = [
    {
        key: 'hexactyl',
        name: 'Hexactyl',
        description: 'Signature dark theme with purple accents',
        primary_color: '#7c3aed',
        bg_color: '#0f172a',
        preview_gradient: 'linear-gradient(135deg, #7c3aed 0%, #0f172a 100%)',
    },
    {
        key: 'midnight',
        name: 'Midnight Blue',
        description: 'Deep blue theme for night owls',
        primary_color: '#3b82f6',
        bg_color: '#020617',
        preview_gradient: 'linear-gradient(135deg, #3b82f6 0%, #020617 100%)',
    },
    {
        key: 'forest',
        name: 'Forest Green',
        description: 'Nature-inspired green theme',
        primary_color: '#16a34a',
        bg_color: '#052e16',
        preview_gradient: 'linear-gradient(135deg, #16a34a 0%, #052e16 100%)',
    },
    {
        key: 'sunset',
        name: 'Sunset Orange',
        description: 'Warm orange and red tones',
        primary_color: '#ea580c',
        bg_color: '#1c0a00',
        preview_gradient: 'linear-gradient(135deg, #ea580c 0%, #1c0a00 100%)',
    },
    {
        key: 'light',
        name: 'Clean Light',
        description: 'Bright light theme for daytime',
        primary_color: '#6d28d9',
        bg_color: '#f8fafc',
        preview_gradient: 'linear-gradient(135deg, #6d28d9 0%, #f8fafc 100%)',
    },
];

const ThemeCard = styled.div<{ $gradient: string; $active: boolean }>`
    ${tw`relative rounded-xl overflow-hidden cursor-pointer transition-all duration-200`};
    ${tw`border-2 hover:scale-105`};
    ${(props) => props.$active ? tw`border-white ring-2 ring-white` : tw`border-transparent hover:border-gray-400`};
    background: ${(props) => props.$gradient};
    min-height: 120px;
`;

const CheckBadge = styled.div`
    ${tw`absolute top-2 right-2 w-6 h-6 rounded-full bg-white flex items-center justify-center`};
`;

type Props = {
    visible: boolean;
    onDismissed: () => void;
    currentTheme: string;
    onThemeChange: (theme: string) => void;
};

export default ({ visible, onDismissed, currentTheme, onThemeChange }: Props) => {
    const [selected, setSelected] = useState(currentTheme);
    const { clearFlashes, addFlash } = useFlash();

    const handleSave = () => {
        clearFlashes('theme');
        fetch('/api/client/account/theme', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            body: JSON.stringify({ theme: selected }),
        })
            .then((response) => {
                if (response.ok) {
                    addFlash({
                        type: 'success',
                        key: 'theme',
                        message: 'Theme updated successfully!',
                    });
                    onThemeChange(selected);
                    // Apply theme CSS
                    const link = document.getElementById('theme-css') as HTMLLinkElement;
                    if (link) {
                        link.href = `/themes/hexactyl/css/theme-${selected}.css`;
                    }
                    onDismissed();
                }
            })
            .catch(() => {
                addFlash({
                    type: 'danger',
                    key: 'theme',
                    message: 'Failed to update theme.',
                });
            });
    };

    return (
        <Dialog open={visible} onClose={onDismissed}>
            <Dialog.Title>
                <span css={tw`text-xl font-bold`}>Choose Your Theme</span>
            </Dialog.Title>
            <Dialog.Description>
                <span css={tw`text-sm text-neutral-400`}>
                    Select a theme to personalize your Hexactyl experience. Choose from 5 beautiful themes.
                </span>
            </Dialog.Description>
            <div css={tw`grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-4`}>
                {themes.map((theme) => (
                    <ThemeCard
                        key={theme.key}
                        $gradient={theme.preview_gradient}
                        $active={selected === theme.key}
                        onClick={() => setSelected(theme.key)}
                    >
                        {selected === theme.key && (
                            <CheckBadge>
                                <svg css={tw`w-4 h-4 text-purple-600`} fill="currentColor" viewBox="0 0 20 20">
                                    <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd" />
                                </svg>
                            </CheckBadge>
                        )}
                        <div css={tw`p-4`}>
                            <h3 css={tw`text-white font-bold text-lg drop-shadow-lg`}>{theme.name}</h3>
                            <p css={tw`text-white/80 text-sm mt-1 drop-shadow`}>{theme.description}</p>
                            <div css={tw`flex items-center mt-3 space-x-2`}>
                                <div css={tw`w-4 h-4 rounded-full border border-white/30`} style={{ backgroundColor: theme.primary_color }} />
                                <div css={tw`w-4 h-4 rounded-full border border-white/30`} style={{ backgroundColor: theme.bg_color }} />
                            </div>
                        </div>
                    </ThemeCard>
                ))}
            </div>
            <Dialog.Footer>
                <Button onClick={onDismissed} variant={'secondary'}>Cancel</Button>
                <Button onClick={handleSave} css={tw`ml-2`}>Apply Theme</Button>
            </Dialog.Footer>
        </Dialog>
    );
};
