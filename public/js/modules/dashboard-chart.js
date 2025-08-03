/**
 * Dashboard Chart Module
 * Handles Chart.js initialization and data visualization for the dashboard
 */

class DashboardChart {
    constructor() {
        this.chart = null;
        this.chartData = null;
        this.init();
    }

    init() {
        // Get chart data from window object (set by Blade template)
        this.chartData = window.chartData;
        
        if (this.chartData) {
            this.loadChartJS().then(() => {
                this.initializeChart();
            }).catch(error => {
                console.error('Failed to load Chart.js:', error);
            });
        }
    }

    loadChartJS() {
        return new Promise((resolve, reject) => {
            // Check if Chart.js is already loaded
            if (typeof Chart !== 'undefined') {
                resolve();
                return;
            }

            // Try multiple CDNs
            const cdnUrls = [
                'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js',
                'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.js',
                'https://unpkg.com/chart.js@4.4.0/dist/chart.umd.js'
            ];
            
            let currentIndex = 0;
            
            const tryLoadCDN = () => {
                if (currentIndex >= cdnUrls.length) {
                    reject(new Error('All CDNs failed'));
                    return;
                }
                
                const script = document.createElement('script');
                script.src = cdnUrls[currentIndex];
                script.onload = () => {
                    console.log('Chart.js loaded from:', cdnUrls[currentIndex]);
                    resolve();
                };
                script.onerror = () => {
                    console.warn('Failed to load from:', cdnUrls[currentIndex]);
                    currentIndex++;
                    tryLoadCDN();
                };
                document.head.appendChild(script);
            };
            
            tryLoadCDN();
        });
    }

    initializeChart() {
        const ctx = document.getElementById('myChart');
        if (!ctx) {
            console.error('Canvas element not found');
            return;
        }
        
        if (!this.chartData) {
            console.error('Chart data not available');
            return;
        }
        
        const { labels, moodData, productivityData } = this.chartData;
        
        // Validate data
        if (!Array.isArray(labels) || !Array.isArray(moodData) || !Array.isArray(productivityData)) {
            console.error('Invalid chart data format:', { labels, moodData, productivityData });
            return;
        }
        
        // Debug: Log the actual data
        console.log('Chart data received:', {
            labels: labels,
            moodData: moodData,
            productivityData: productivityData,
            labelsLength: labels.length,
            moodDataLength: moodData.length,
            productivityDataLength: productivityData.length
        });
        
        try {
            ctx.height = 250;
            
            this.chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Mood',
                            data: moodData,
                            borderColor: '#48a6a6',
                            backgroundColor: 'rgba(72, 166, 166, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#48a6a6',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 6,
                            pointHoverRadius: 8
                        },
                        {
                            label: 'Productivity',
                            data: productivityData,
                            borderColor: '#ffcd2d',
                            backgroundColor: 'rgba(255, 205, 45, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#ffcd2d',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 6,
                            pointHoverRadius: 8
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: {
                                    family: 'FigtreeReg, Figtree, Arial, sans-serif',
                                    size: 12
                                },
                                color: '#333'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: '#48a6a6',
                            borderWidth: 1,
                            cornerRadius: 8,
                            displayColors: true,
                            callbacks: {
                                label: function(context) {
                                    const label = context.dataset.label || '';
                                    const value = context.parsed.y;
                                    return `${label}: ${value}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#666',
                                font: {
                                    family: 'FigtreeReg, Figtree, Arial, sans-serif',
                                    size: 11
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            max: 10,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)',
                                drawBorder: false
                            },
                            ticks: {
                                stepSize: 2,
                                color: '#666',
                                font: {
                                    family: 'FigtreeReg, Figtree, Arial, sans-serif',
                                    size: 11
                                }
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    elements: {
                        point: {
                            hoverBackgroundColor: '#fff',
                            hoverBorderColor: '#48a6a6',
                            hoverBorderWidth: 3
                        }
                    }
                }
            });
            
            console.log('Chart initialized successfully');
        } catch (error) {
            console.error('Error initializing chart:', error);
        }
    }

    // Method to update chart data
    updateChartData(newData) {
        if (this.chart && newData) {
            this.chart.data.labels = newData.labels || [];
            this.chart.data.datasets[0].data = newData.moodData || [];
            this.chart.data.datasets[1].data = newData.productivityData || [];
            this.chart.update();
        }
    }

    // Method to destroy chart
    destroy() {
        if (this.chart) {
            this.chart.destroy();
            this.chart = null;
        }
    }
}

// Auto-initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on the dashboard page with chart data
    if (window.chartData && document.getElementById('myChart')) {
        new DashboardChart();
    }
});

// Make available globally for debugging
window.DashboardChart = DashboardChart; 