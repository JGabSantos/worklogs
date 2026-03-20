<flux:card class="min-w-0 overflow-hidden">
    <div class="flex flex-col items-start justify-between gap-3 sm:flex-row sm:items-center">
        <div>
            <flux:heading size="md">{{ __('Hours by activity') }}</flux:heading>
            <flux:subheading class="text-sm">{{ __('Share of total time by task type') }}</flux:subheading>
        </div>

        <flux:select wire:model.live="period" class="w-full shrink-0 text-sm sm:w-36">
            <flux:select.option value="today">{{ __('Today') }}</flux:select.option>
            <flux:select.option value="7d">{{ __('7 days') }}</flux:select.option>
            <flux:select.option value="30d">{{ __('30 days') }}</flux:select.option>
            <flux:select.option value="90d">{{ __('90 days') }}</flux:select.option>
            <flux:select.option value="180d">{{ __('180 days') }}</flux:select.option>
            <flux:select.option value="365d">{{ __('365 days') }}</flux:select.option>
            <flux:select.option value="all">{{ __('All time') }}</flux:select.option>
        </flux:select>
    </div>

    <div class="mt-2 flex min-h-80 min-w-0 items-center justify-center">
        @if (empty($chart['series']))
            <div class="flex h-64 flex-col items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-zinc-300 dark:text-zinc-600" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <p class="text-sm text-zinc-400">{{ __('No data for this period') }}</p>
            </div>
        @else
            <div id="hours-by-activity-type-chart-{{ $this->getId() }}" class="w-full min-w-0" wire:ignore></div>
        @endif
    </div>

    @script
        <script>
            const CHART_ELEMENT_ID = 'hours-by-activity-type-chart-{{ $this->getId() }}'
            const CHART_CLEANUP_KEY = 'hoursByActivityTypeChartCleanup_{{ $this->getId() }}'
            const CHART_EVENT = 'hours-by-activity-type-chart-updated'

            const PALETTE_BASE = [{
                    h: 210,
                    s: 75,
                    l: 52
                }, // azul
                {
                    h: 155,
                    s: 65,
                    l: 42
                }, // verde-esmeralda
                {
                    h: 262,
                    s: 60,
                    l: 58
                }, // violeta
                {
                    h: 35,
                    s: 85,
                    l: 52
                }, // âmbar
                {
                    h: 185,
                    s: 70,
                    l: 45
                }, // ciano
                {
                    h: 330,
                    s: 65,
                    l: 55
                }, // rosa
                {
                    h: 90,
                    s: 55,
                    l: 44
                }, // verde-lima
                {
                    h: 20,
                    s: 80,
                    l: 52
                }, // laranja
            ]

            const generatePalette = (count, theme = "light") => {
                if (count <= 0) return []

                const isDark = theme === "dark"
                const lShift = isDark ? +12 : 0 // mais claro no dark mode
                const sShift = isDark ? -10 : 0 // ligeiramente menos saturado

                // Se count <= base, usa as cores base diretamente
                if (count <= PALETTE_BASE.length) {
                    return PALETTE_BASE.slice(0, count).map(({
                            h,
                            s,
                            l
                        }) =>
                        `hsl(${h}, ${s + sShift}%, ${l + lShift}%)`
                    )
                }

                // Para mais cores, gera extras com golden angle (137.5°)
                const palette = PALETTE_BASE.map(({
                        h,
                        s,
                        l
                    }) =>
                    `hsl(${h}, ${s + sShift}%, ${l + lShift}%)`
                )

                const GOLDEN_ANGLE = 137.508
                for (let i = PALETTE_BASE.length; i < count; i++) {
                    const h = Math.round((i * GOLDEN_ANGLE) % 360)
                    const s = isDark ? 55 : 65
                    const l = isDark ? 62 : 48
                    palette.push(`hsl(${h}, ${s}%, ${l}%)`)
                }

                return palette
            }

            const CHART_THEME = {
                light: {
                    mode: 'light',
                    foreColor: '#3f3f46',
                    background: '#ffffff',
                    axisLabel: '#3f3f46',
                    gridBorder: '#e4e4e7',
                    stroke: '#ffffff',
                    tooltip: 'light'
                },
                dark: {
                    mode: 'dark',
                    foreColor: '#d4d4d8',
                    background: '#3c3c3c',
                    axisLabel: '#d4d4d8',
                    gridBorder: '#525252',
                    stroke: '#3c3c3c',
                    tooltip: 'dark'
                },
            }

            const isDarkMode = () => window.Flux?.dark === true || document.documentElement.classList.contains('dark')
            const resolveTheme = () => isDarkMode() ? CHART_THEME.dark : CHART_THEME.light

            const fmtMinutes = (m) => {
                const h = Math.floor(m / 60),
                    min = m % 60
                if (h === 0) return `${min}min`
                if (min === 0) return `${h}h`
                return `${h}h ${min}min`
            }

            const buildOptions = (series, categories) => {
                const t = resolveTheme()
                return {
                    chart: {
                        type: 'donut',
                        height: 320,
                        width: '100%',
                        fontFamily: 'inherit',
                        foreColor: t.foreColor,
                        background: t.background,
                        redrawOnWindowResize: true,
                        redrawOnParentResize: true,
                        toolbar: {
                            show: false
                        },
                        animations: {
                            enabled: true,
                            speed: 400
                        },
                    },
                    theme: {
                        mode: t.mode
                    },
                    series,
                    labels: categories,
                    colors: generatePalette(series.length, t.mode),
                    stroke: {
                        width: 2,
                        colors: [t.stroke]
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: (v) => `${v.toFixed(1)}%`,
                        style: {
                            fontSize: '11px',
                            fontWeight: 600
                        }
                    },
                    legend: {
                        show: true,
                        position: 'bottom',
                        horizontalAlign: 'center',
                        markers: {
                            strokeWidth: 0,
                        },
                    },
                    states: {
                        hover: {
                            filter: {
                                type: 'lighten',
                                value: 0.08
                            }
                        }
                    },
                    tooltip: {
                        theme: t.tooltip,
                        y: {
                            formatter: (v) => fmtMinutes(v)
                        }
                    },
                }
            }

            const buildThemeOptions = () => {
                const t = resolveTheme()
                return {
                    chart: {
                        foreColor: t.foreColor,
                        background: t.background
                    },
                    theme: {
                        mode: t.mode
                    },
                    stroke: {
                        colors: [t.stroke]
                    },
                    tooltip: {
                        theme: t.tooltip
                    },
                    legend: {
                        labels: {
                            colors: t.foreColor
                        }
                    },
                }
            }

            let chart = null

            const initChart = (series, categories) => {
                const el = document.getElementById(CHART_ELEMENT_ID)
                if (!el) return
                chart?.destroy();
                chart = null
                chart = new window.ApexCharts(el, buildOptions(series, categories))
                chart.render()
            }

            const syncTheme = () => {
                if (!chart) return
                chart.updateOptions(buildThemeOptions(), false, false)
            }

            const observeThemeChanges = () => {
                const observer = new MutationObserver(() => syncTheme())
                observer.observe(document.documentElement, {
                    attributes: true,
                    attributeFilter: ['class']
                })
                return () => observer.disconnect()
            }

            if (typeof window[CHART_CLEANUP_KEY] === 'function') window[CHART_CLEANUP_KEY]()
            window[CHART_CLEANUP_KEY] = observeThemeChanges()

            @if (!empty($chart['series']))
                initChart(@js($chart['series']), @js($chart['categories']))
            @endif

            this.$on(CHART_EVENT, ({
                series,
                categories
            }) => {
                if (!series?.length) {
                    chart?.destroy();
                    chart = null;
                    return
                }
                initChart(series, categories)
            })
        </script>
    @endscript
</flux:card>
