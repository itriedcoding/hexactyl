import React, { useEffect, useState } from 'react';
import tw from 'twin.macro';
import ContentContainer from '@/components/elements/ContentContainer';

type Stats = {
    totalServers: number;
    runningServers: number;
    stoppedServers: number;
    totalUsers: number;
    totalNodes: number;
    onlineNodes: number;
};

export default () => {
    const [stats, setStats] = useState<Stats | null>(null);

    useEffect(() => {
        // Dashboard stats would be fetched from API
        setStats({
            totalServers: 0,
            runningServers: 0,
            stoppedServers: 0,
            totalUsers: 0,
            totalNodes: 0,
            onlineNodes: 0,
        });
    }, []);

    const statCards = stats
        ? [
            { label: 'Total Servers', value: stats.totalServers, color: 'from-purple-500 to-purple-700' },
            { label: 'Running', value: stats.runningServers, color: 'from-green-500 to-green-700' },
            { label: 'Stopped', value: stats.stoppedServers, color: 'from-red-500 to-red-700' },
            { label: 'Users', value: stats.totalUsers, color: 'from-blue-500 to-blue-700' },
            { label: 'Nodes', value: stats.totalNodes, color: 'from-orange-500 to-orange-700' },
            { label: 'Online Nodes', value: stats.onlineNodes, color: 'from-cyan-500 to-cyan-700' },
        ]
        : [];

    return (
        <ContentContainer css={tw`mt-4`}>
            <h2 css={tw`text-2xl font-bold text-neutral-100 mb-4`}>Dashboard Overview</h2>
            <div css={tw`grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4`}>
                {statCards.map((card) => (
                    <div
                        key={card.label}
                        css={tw`rounded-xl p-4 bg-gradient-to-br text-white shadow-lg`}
                        className={card.color}
                    >
                        <p css={tw`text-sm opacity-80`}>{card.label}</p>
                        <p css={tw`text-3xl font-bold mt-1`}>{card.value}</p>
                    </div>
                ))}
            </div>
        </ContentContainer>
    );
};
