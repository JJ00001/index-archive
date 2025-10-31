import './bootstrap'
import '../css/app.css'

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { ZiggyVue } from '../../vendor/tightenco/ziggy'
import 'primeicons/primeicons.css'
import { createI18n } from 'vue-i18n'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const i18n = createI18n({
            locale: 'en',
            numberFormats: {
                en: {
                    percent: {
                        style: 'percent',
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2,
                    },
                    percentFine: {
                        style: 'percent',
                        minimumFractionDigits: 3,
                        maximumFractionDigits: 3,
                    },
                    currencyUSDCompact: {
                        style: 'currency',
                        currency: 'USD',
                        notation: 'compact',
                    },
                    currencyUSD: {
                        style: 'currency',
                        currency: 'USD',
                    }
                }
            },
            datetimeFormats: {
                en: {
                    short: {
                        year: 'numeric',
                        month: 'short',
                    }
                }
            },
            messages: {
                en: {
                    company: {
                        name: 'Company | Companies',
                        count: 'Companies (Count)',
                    },
                    sector: {
                        name: 'Sector | Sectors',
                        count: 'Sectors (Count)',
                    },
                    country: {
                        name: 'Country | Countries',
                        count: 'Countries (Count)',
                    },
                    index: {
                        name: 'Index | Indices',
                        count: 'Indices (Count)',
                    },
                  indexHolding: {
                    name: 'Constituent | Constituents',
                  },
                    indexProvider: {
                        name: 'Index Provider | Index Providers',
                    },
                    activity: {
                        action: 'Action',
                        dataDate: 'Date',
                        noRecentActivity: 'No recent activity',
                        company_added_to_index: 'Added',
                        company_removed_from_index: 'Removed',
                    },
                    weight: 'Weight',
                    marketCap: 'Capitalization',
                    rank: 'Rank',
                    exchange: 'Exchange',
                },
            }
        });

        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(i18n)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
