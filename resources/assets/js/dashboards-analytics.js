/**
 * Dashboard Analytics
 */

'use strict';

document.addEventListener('DOMContentLoaded', function (e) {
  let cardColor, legendColor, labelColor, borderColor, fontFamily;
  cardColor = config.colors.cardColor;
  legendColor = config.colors.bodyColor;
  labelColor = config.colors.textMuted;
  borderColor = config.colors.borderColor;
  fontFamily = config.fontFamily;

  // Ventas por mes - Bar Chart
  // --------------------------------------------------------------------
  const totalRevenueChartEl = document.querySelector('#totalRevenueChart'),
    totalRevenueChartOptions = {
      series: [
        {
          name: 'Ventas ' + (window.dashboardYear ?? new Date().getFullYear()),
          data: window.dashboardVentasAnuales ?? new Array(12).fill(0)
        }
      ],
      chart: {
        height: 300,
        type: 'bar',
        toolbar: { show: false }
      },
      plotOptions: {
        bar: {
          horizontal: false,
          columnWidth: '40%',
          borderRadius: 8,
          startingShape: 'rounded',
          endingShape: 'rounded',
          borderRadiusApplication: 'around'
        }
      },
      colors: [config.colors.primary],
      dataLabels: {
        enabled: false
      },
      stroke: {
        curve: 'smooth',
        width: 6,
        lineCap: 'round',
        colors: [cardColor]
      },
      legend: {
        show: true,
        horizontalAlign: 'left',
        position: 'top',
        markers: {
          size: 4,
          radius: 12,
          shape: 'circle',
          strokeWidth: 0
        },
        fontSize: '13px',
        fontFamily: fontFamily,
        fontWeight: 400,
        labels: {
          colors: legendColor,
          useSeriesColors: false
        },
        itemMargin: {
          horizontal: 10
        }
      },
      grid: {
        strokeDashArray: 7,
        borderColor: borderColor,
        padding: {
          top: 0,
          bottom: -8,
          left: 20,
          right: 20
        }
      },
      xaxis: {
        categories: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        labels: {
          style: {
            fontSize: '13px',
            fontFamily: fontFamily,
            colors: labelColor
          }
        },
        axisTicks: {
          show: false
        },
        axisBorder: {
          show: false
        }
      },
      yaxis: {
        labels: {
          style: {
            fontSize: '13px',
            fontFamily: fontFamily,
            colors: labelColor
          },
          formatter: function (val) {
            return 'S/ ' + val;
          }
        }
      },
      states: {
        hover: {
          filter: {
            type: 'none'
          }
        },
        active: {
          filter: {
            type: 'none'
          }
        }
      }
    };
  if (totalRevenueChartEl !== null) {
    const totalRevenueChart = new ApexCharts(totalRevenueChartEl, totalRevenueChartOptions);
    totalRevenueChart.render();
  }
});
