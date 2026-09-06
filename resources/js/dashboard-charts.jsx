import React from 'react';
import { createRoot } from 'react-dom/client';
import {
    Area,
    AreaChart,
    Bar,
    BarChart,
    CartesianGrid,
    Cell,
    Pie,
    PieChart,
    ResponsiveContainer,
    Tooltip,
    XAxis,
    YAxis,
} from 'recharts';

const chartColors = ['#2f8cff', '#20f7a5', '#7cffcf', '#ffe08a', '#ff9fba'];

const readChartData = (id) => {
    const node = document.getElementById(id);

    if (!node) {
        return null;
    }

    try {
        return JSON.parse(node.textContent || '{}');
    } catch {
        return null;
    }
};

const moneyTick = (value) => {
    const number = Number(value) || 0;

    if (number >= 1000) {
        return `PHP ${Math.round(number / 1000)}k`;
    }

    return `PHP ${number}`;
};

const tooltipStyle = {
    background: 'rgba(6, 16, 30, .96)',
    border: '1px solid rgba(124, 242, 255, .22)',
    borderRadius: 8,
    color: '#d7f7ff',
};

function EmptyChart({ label }) {
    return <div className="chart-empty">{label}</div>;
}

function AdminCharts({ data }) {
    const revenue = data.monthlyRevenue || [];
    const services = data.serviceRevenue || [];
    const status = data.statusMix || [];

    return (
        <div className="dashboard-chart-grid">
            <div className="chart-panel chart-panel-wide">
                <div className="chart-heading">
                    <h2>Revenue Pulse</h2>
                    <span>Completed jobs by month</span>
                </div>
                {revenue.length ? (
                    <ResponsiveContainer width="100%" height={260}>
                        <AreaChart data={revenue} margin={{ top: 12, right: 16, left: 0, bottom: 0 }}>
                            <defs>
                                <linearGradient id="adminRevenueGlow" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="5%" stopColor="#20f7a5" stopOpacity={0.78} />
                                    <stop offset="95%" stopColor="#2f8cff" stopOpacity={0.08} />
                                </linearGradient>
                            </defs>
                            <CartesianGrid stroke="rgba(124, 242, 255, .1)" vertical={false} />
                            <XAxis dataKey="label" tick={{ fill: '#8ca8bb', fontSize: 12 }} axisLine={false} tickLine={false} />
                            <YAxis tickFormatter={moneyTick} tick={{ fill: '#8ca8bb', fontSize: 12 }} axisLine={false} tickLine={false} width={72} />
                            <Tooltip contentStyle={tooltipStyle} formatter={(value) => [`PHP ${Number(value).toLocaleString()}`, 'Revenue']} />
                            <Area type="monotone" dataKey="value" stroke="#20f7a5" strokeWidth={3} fill="url(#adminRevenueGlow)" />
                        </AreaChart>
                    </ResponsiveContainer>
                ) : (
                    <EmptyChart label="No completed revenue yet." />
                )}
            </div>

            <div className="chart-panel">
                <div className="chart-heading">
                    <h2>Service Mix</h2>
                    <span>Revenue by service</span>
                </div>
                {services.length ? (
                    <ResponsiveContainer width="100%" height={260}>
                        <BarChart data={services} margin={{ top: 12, right: 6, left: 0, bottom: 0 }}>
                            <CartesianGrid stroke="rgba(124, 242, 255, .1)" vertical={false} />
                            <XAxis dataKey="label" tick={{ fill: '#8ca8bb', fontSize: 12 }} axisLine={false} tickLine={false} />
                            <YAxis tickFormatter={moneyTick} tick={{ fill: '#8ca8bb', fontSize: 12 }} axisLine={false} tickLine={false} width={68} />
                            <Tooltip contentStyle={tooltipStyle} formatter={(value) => [`PHP ${Number(value).toLocaleString()}`, 'Revenue']} />
                            <Bar dataKey="value" radius={[8, 8, 0, 0]}>
                                {services.map((entry, index) => (
                                    <Cell key={entry.label} fill={chartColors[index % chartColors.length]} />
                                ))}
                            </Bar>
                        </BarChart>
                    </ResponsiveContainer>
                ) : (
                    <EmptyChart label="No service revenue yet." />
                )}
            </div>

            <div className="chart-panel">
                <div className="chart-heading">
                    <h2>Job Status</h2>
                    <span>Current workflow split</span>
                </div>
                {status.length ? (
                    <ResponsiveContainer width="100%" height={260}>
                        <PieChart>
                            <Pie data={status} dataKey="value" nameKey="label" innerRadius={56} outerRadius={92} paddingAngle={4}>
                                {status.map((entry, index) => (
                                    <Cell key={entry.label} fill={chartColors[index % chartColors.length]} />
                                ))}
                            </Pie>
                            <Tooltip contentStyle={tooltipStyle} />
                        </PieChart>
                    </ResponsiveContainer>
                ) : (
                    <EmptyChart label="No jobs to chart yet." />
                )}
            </div>
        </div>
    );
}

function MechanicCharts({ data }) {
    const status = data.statusMix || [];
    const performance = data.performance || [];

    return (
        <div className="dashboard-chart-grid">
            <div className="chart-panel chart-panel-wide">
                <div className="chart-heading">
                    <h2>Repair Flow</h2>
                    <span>Assigned work by status</span>
                </div>
                {status.length ? (
                    <ResponsiveContainer width="100%" height={260}>
                        <BarChart data={status} margin={{ top: 12, right: 16, left: 0, bottom: 0 }}>
                            <CartesianGrid stroke="rgba(124, 242, 255, .1)" vertical={false} />
                            <XAxis dataKey="label" tick={{ fill: '#8ca8bb', fontSize: 12 }} axisLine={false} tickLine={false} />
                            <YAxis allowDecimals={false} tick={{ fill: '#8ca8bb', fontSize: 12 }} axisLine={false} tickLine={false} width={32} />
                            <Tooltip contentStyle={tooltipStyle} />
                            <Bar dataKey="value" radius={[8, 8, 0, 0]}>
                                {status.map((entry, index) => (
                                    <Cell key={entry.label} fill={chartColors[index % chartColors.length]} />
                                ))}
                            </Bar>
                        </BarChart>
                    </ResponsiveContainer>
                ) : (
                    <EmptyChart label="No assigned repairs yet." />
                )}
            </div>

            <div className="chart-panel">
                <div className="chart-heading">
                    <h2>Output Mix</h2>
                    <span>Completion and active load</span>
                </div>
                {performance.length ? (
                    <ResponsiveContainer width="100%" height={260}>
                        <PieChart>
                            <Pie data={performance} dataKey="value" nameKey="label" innerRadius={56} outerRadius={92} paddingAngle={4}>
                                {performance.map((entry, index) => (
                                    <Cell key={entry.label} fill={chartColors[index % chartColors.length]} />
                                ))}
                            </Pie>
                            <Tooltip contentStyle={tooltipStyle} />
                        </PieChart>
                    </ResponsiveContainer>
                ) : (
                    <EmptyChart label="No performance data yet." />
                )}
            </div>
        </div>
    );
}

const adminMount = document.getElementById('admin-overview-charts');
const mechanicMount = document.getElementById('mechanic-overview-charts');

if (adminMount) {
    createRoot(adminMount).render(<AdminCharts data={readChartData('admin-chart-data') || {}} />);
}

if (mechanicMount) {
    createRoot(mechanicMount).render(<MechanicCharts data={readChartData('mechanic-chart-data') || {}} />);
}
