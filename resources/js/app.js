import './bootstrap';

import * as bootstrap from 'bootstrap';
import Chart from 'chart.js/auto';

// Expose to inline scripts in Blade views (dropdowns/modals, charts & reports).
window.bootstrap = bootstrap;
window.Chart = Chart;
