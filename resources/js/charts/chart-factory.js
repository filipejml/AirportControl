import Chart from 'chart.js/auto';
import ChartDataLabels from 'chartjs-plugin-datalabels';

Chart.register(ChartDataLabels);

const palette = Object.freeze({
    primary: '#0d6efd',
    secondary: '#6c757d',
    success: '#198754',
    danger: '#dc3545',
    warning: '#ffc107',
    info: '#0dcaf0',
    purple: '#6f42c1',
    orange: '#fd7e14',
});

Chart.defaults.responsive = true;
Chart.defaults.font.family = 'system-ui, -apple-system, "Segoe UI", sans-serif';
Chart.defaults.color = '#495057';
Chart.defaults.plugins.legend.labels.usePointStyle = true;
Chart.defaults.plugins.datalabels = {
    display: false,
    color: '#333',
    anchor: 'end',
    align: 'top',
    offset: 4,
    font: { weight: 'bold', size: 11 },
    formatter(value) {
        if (value === 0) return '';
        if (value >= 1000) return `${(value / 1000).toFixed(1)}k`;
        return String(value);
    },
};

const instances = new Map();

function resolveCanvas(target) {
    if (typeof target === 'string') return document.getElementById(target);
    if (target instanceof CanvasRenderingContext2D) return target.canvas;
    return target;
}

function destroyChart(target) {
    const canvas = resolveCanvas(target);
    if (!canvas) return;

    const chart = instances.get(canvas) ?? Chart.getChart(canvas);
    chart?.destroy();
    instances.delete(canvas);
}

function createChart(target, config) {
    const canvas = resolveCanvas(target);
    if (!canvas) return null;

    destroyChart(canvas);
    const chart = new Chart(canvas, config);
    instances.set(canvas, chart);
    return chart;
}

const withType = (type) => (target, data, options = {}, plugins = []) =>
    createChart(target, { type, data, options, plugins });

const AirportCharts = Object.freeze({
    Chart,
    ChartDataLabels,
    palette,
    create: createChart,
    destroy: destroyChart,
    bar: withType('bar'),
    line: withType('line'),
    pie: withType('pie'),
    doughnut: withType('doughnut'),
});

window.Chart = Chart;
window.ChartDataLabels = ChartDataLabels;
window.AirportCharts = AirportCharts;

export { Chart, ChartDataLabels, palette, createChart, destroyChart };
export default AirportCharts;
