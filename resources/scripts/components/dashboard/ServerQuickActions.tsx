import React, { useState } from 'react';
import tw from 'twin.macro';
import styled from 'styled-components/macro';
import { Server } from '@/api/server/getServer';

const ActionCard = styled.div`
    ${tw`p-4 rounded-lg border border-neutral-600 hover:border-neutral-400 cursor-pointer transition-all duration-200`};
    ${tw`hover:bg-neutral-700/50`};
`;

type Props = {
    server: Server;
    onAction: (action: string) => void;
};

export default ({ server, onAction }: Props) => {
    const [showConfirm, setShowConfirm] = useState<string | null>(null);

    const actions = [
        { key: 'restart', label: 'Restart', icon: '🔄', color: 'text-yellow-400' },
        { key: 'stop', label: 'Stop', icon: '⏹️', color: 'text-red-400' },
        { key: 'start', label: 'Start', icon: '▶️', color: 'text-green-400' },
        { key: 'kill', label: 'Kill', icon: '💀', color: 'text-red-500' },
        { key: 'clone', label: 'Clone', icon: '📋', color: 'text-blue-400' },
        { key: 'reinstall', label: 'Reinstall', icon: '🔧', color: 'text-orange-400' },
    ];

    return (
        <div css={tw`grid grid-cols-3 sm:grid-cols-6 gap-2`}>
            {actions.map((action) => (
                <ActionCard
                    key={action.key}
                    onClick={() => onAction(action.key)}
                >
                    <div css={tw`text-center`}>
                        <span css={tw`text-2xl`}>{action.icon}</span>
                        <p css={tw`text-xs text-neutral-300 mt-1 font-medium`}>{action.label}</p>
                    </div>
                </ActionCard>
            ))}
        </div>
    );
};
