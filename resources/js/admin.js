function setupAdminSidebar() {
    const shell = document.querySelector('[data-admin-shell]');
    const sidebar = document.querySelector('[data-sidebar]');
    const overlay = document.querySelector('[data-sidebar-overlay]');
    const toggle = document.querySelector('[data-sidebar-toggle]');

    if (!(shell instanceof HTMLElement) || !(sidebar instanceof HTMLElement) || !(overlay instanceof HTMLElement) || !(toggle instanceof HTMLElement)) {
        return;
    }

    const mobileMedia = window.matchMedia('(max-width: 767px)');

    function setSidebarOpen(isOpen) {
        shell.dataset.sidebarOpen = isOpen ? 'true' : 'false';
        sidebar.classList.toggle('translate-x-0', isOpen);
        sidebar.classList.toggle('-translate-x-full', !isOpen);
        overlay.classList.toggle('hidden', !isOpen);
        document.body.classList.toggle('overflow-hidden', isOpen && mobileMedia.matches);
    }

    toggle.addEventListener('click', () => {
        setSidebarOpen(shell.dataset.sidebarOpen !== 'true');
    });

    overlay.addEventListener('click', () => {
        setSidebarOpen(false);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            setSidebarOpen(false);
        }
    });

    mobileMedia.addEventListener('change', (event) => {
        if (!event.matches) {
            setSidebarOpen(false);
        }
    });

    setSidebarOpen(false);
}

function parseDashboardData() {
    const payload = document.getElementById('dashboard-chart-data');

    if (!(payload instanceof HTMLTemplateElement)) {
        return null;
    }

    try {
        return JSON.parse(payload.innerHTML.trim());
    } catch {
        return null;
    }
}

function createHiDpiCanvas(canvas) {
    const context = canvas.getContext('2d');

    if (!context) {
        return null;
    }

    const parent = canvas.parentElement;
    const width = parent?.clientWidth ?? canvas.clientWidth;
    const height = parent?.clientHeight ?? 320;
    const ratio = window.devicePixelRatio || 1;

    canvas.width = Math.max(Math.floor(width * ratio), 1);
    canvas.height = Math.max(Math.floor(height * ratio), 1);
    canvas.style.width = `${width}px`;
    canvas.style.height = `${height}px`;

    context.setTransform(ratio, 0, 0, ratio, 0, 0);
    context.clearRect(0, 0, width, height);

    return { context, width, height };
}

function drawAxes(context, labels, width, height, bounds, colors) {
    const steps = 4;

    context.save();
    context.strokeStyle = colors.grid;
    context.fillStyle = colors.textMuted;
    context.lineWidth = 1;
    context.font = '12px system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif';

    for (let step = 0; step <= steps; step += 1) {
        const value = bounds.max * (step / steps);
        const y = bounds.top + bounds.chartHeight - (bounds.chartHeight * step) / steps;

        context.beginPath();
        context.moveTo(bounds.left, y);
        context.lineTo(width - bounds.right, y);
        context.stroke();

        context.textAlign = 'right';
        context.textBaseline = 'middle';
        context.fillText(formatAxisValue(value), bounds.left - 10, y);
    }

    labels.forEach((label, index) => {
        const x = bounds.left + (bounds.chartWidth * index) / Math.max(labels.length - 1, 1);

        context.textAlign = index === 0 ? 'left' : index === labels.length - 1 ? 'right' : 'center';
        context.textBaseline = 'top';
        context.fillText(label, x, height - bounds.bottom + 10);
    });

    context.restore();
}

function drawLineChart(canvas, labels, values) {
    if (!labels.length || !values.length) {
        return;
    }

    const surface = createHiDpiCanvas(canvas);

    if (!surface) {
        return;
    }

    const { context, width, height } = surface;
    const bounds = {
        top: 18,
        right: 18,
        bottom: 38,
        left: 40,
        chartWidth: width - 58,
        chartHeight: height - 56,
        max: Math.max(...values, 1),
    };
    const colors = {
        line: '#f97316',
        fill: 'rgba(249, 115, 22, 0.16)',
        grid: 'rgba(148, 163, 184, 0.18)',
        textMuted: '#94a3b8',
    };

    drawAxes(context, labels, width, height, bounds, colors);

    context.save();
    context.strokeStyle = colors.line;
    context.fillStyle = colors.fill;
    context.lineWidth = 2;
    context.lineJoin = 'round';
    context.lineCap = 'round';

    values.forEach((value, index) => {
        const x = bounds.left + (bounds.chartWidth * index) / Math.max(values.length - 1, 1);
        const y = bounds.top + bounds.chartHeight - (value / bounds.max) * bounds.chartHeight;

        if (index === 0) {
            context.beginPath();
            context.moveTo(x, y);
        } else {
            context.lineTo(x, y);
        }
    });

    context.stroke();

    context.lineTo(bounds.left + bounds.chartWidth, bounds.top + bounds.chartHeight);
    context.lineTo(bounds.left, bounds.top + bounds.chartHeight);
    context.closePath();
    context.fill();

    values.forEach((value, index) => {
        const x = bounds.left + (bounds.chartWidth * index) / Math.max(values.length - 1, 1);
        const y = bounds.top + bounds.chartHeight - (value / bounds.max) * bounds.chartHeight;

        context.beginPath();
        context.fillStyle = '#f97316';
        context.arc(x, y, 3.5, 0, Math.PI * 2);
        context.fill();
    });

    context.restore();
}

function drawBarChart(canvas, labels, values) {
    if (!labels.length || !values.length) {
        return;
    }

    const surface = createHiDpiCanvas(canvas);

    if (!surface) {
        return;
    }

    const { context, width, height } = surface;
    const bounds = {
        top: 18,
        right: 18,
        bottom: 38,
        left: 40,
        chartWidth: width - 58,
        chartHeight: height - 56,
        max: Math.max(...values, 1),
    };
    const colors = {
        bar: '#fb923c',
        grid: 'rgba(148, 163, 184, 0.18)',
        textMuted: '#94a3b8',
    };

    drawAxes(context, labels, width, height, bounds, colors);

    const slotWidth = bounds.chartWidth / values.length;
    const barWidth = Math.min(Math.max(slotWidth * 0.52, 18), 42);

    context.save();
    context.fillStyle = colors.bar;

    values.forEach((value, index) => {
        const barHeight = (value / bounds.max) * bounds.chartHeight;
        const x = bounds.left + slotWidth * index + (slotWidth - barWidth) / 2;
        const y = bounds.top + bounds.chartHeight - barHeight;

        context.beginPath();
        context.roundRect(x, y, barWidth, Math.max(barHeight, 4), 6);
        context.fill();
    });

    context.restore();
}

function setupDashboardCharts() {
    const chartData = parseDashboardData();
    const earningsCanvas = document.getElementById('earningsChart');
    const ordersCanvas = document.getElementById('ordersChart');

    if (!chartData || !(earningsCanvas instanceof HTMLCanvasElement) || !(ordersCanvas instanceof HTMLCanvasElement)) {
        return;
    }

    const labels = Array.isArray(chartData.labels) ? chartData.labels : [];
    const earnings = Array.isArray(chartData.earnings) ? chartData.earnings.map(Number) : [];
    const orders = Array.isArray(chartData.units ?? chartData.orders) ? (chartData.units ?? chartData.orders).map(Number) : [];

    const render = () => {
        drawLineChart(earningsCanvas, labels, earnings);
        drawBarChart(ordersCanvas, labels, orders);
    };

    const debouncedRender = debounce(render, 120);

    render();
    window.addEventListener('resize', debouncedRender);
}

function formatAxisValue(value) {
    if (value >= 1000000) {
        return `${(value / 1000000).toFixed(1)}M`;
    }

    if (value >= 1000) {
        return `${(value / 1000).toFixed(1)}K`;
    }

    return Math.round(value).toString();
}

function debounce(callback, wait) {
    let timeoutId;

    return (...args) => {
        window.clearTimeout(timeoutId);
        timeoutId = window.setTimeout(() => callback(...args), wait);
    };
}

document.addEventListener('DOMContentLoaded', () => {
    setupAdminSidebar();
    setupDashboardCharts();
});
