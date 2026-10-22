import './bootstrap';

import * as bootstrap from 'bootstrap';
import Chart from 'chart.js/auto';

import { initCore } from './eventlynk/core';
import { initLanding } from './eventlynk/landing';
import { initSearchableSelects } from './eventlynk/searchable-select';
import { initBrowse } from './eventlynk/browse';
import { initBooking } from './eventlynk/booking';
import { initAuth } from './eventlynk/auth';
import { initDashboard } from './eventlynk/dashboard';

// Expose to inline scripts in Blade views (dropdowns/modals, charts & reports).
window.bootstrap = bootstrap;
window.Chart = Chart;

initCore();
initLanding();
initSearchableSelects();
initBrowse();
initBooking();
initAuth();
initDashboard();
