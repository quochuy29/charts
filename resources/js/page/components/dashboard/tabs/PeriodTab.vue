<template>
    <div class="relative w-full h-full">
        <LineChart 
            v-if="chartData && chartData.datasets.length" 
            :data="chartData" 
            :options="chartOptions" 
            class="w-full h-full"
        />
    </div>
</template>

<script setup>
import { computed } from 'vue';
// 1. Import Chart để lấy hàm generateLabels mặc định
import { Chart } from 'chart.js';
import LineChart from '../../charts/type/Line.vue';

const props = defineProps({
    chartData: Object,
    axisSettings: Object,
    unit: String,
    periodType: String
});

// 2. Khởi tạo hình ảnh
const currentImg = new Image(20, 20);
currentImg.src = '/images/current.png'; 

const compareImg = new Image(20, 20);
compareImg.src = '/images/compare.png';

const targetImg = new Image(20, 20);
targetImg.src = '/images/target.png';

// 3. Cấu hình Options (Logic chính nằm ở đây)
const chartOptions = computed(() => {
    return {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        
        // Cấu hình điểm trên biểu đồ (giữ nguyên là hình tròn)
        elements: {
            point: {
                radius: 3,         // Kích thước điểm trên đường kẻ
                hoverRadius: 6,    // Kích thước khi hover
                pointStyle: 'circle' // Mặc định là hình tròn
            }
        },

        plugins: {
            legend: { 
                position: 'bottom',
                labels: { 
                    usePointStyle: true, // Bắt buộc true
                    boxWidth: 20,       // Chiều rộng icon trong legend (phải đủ lớn để chứa ảnh)
                    padding: 20,
                    font: { size: 12 },

                    // --- LOGIC CUSTOM LEGEND ---
                    generateLabels: (chart) => {
                        // Lấy danh sách label mặc định do Chart.js tạo ra
                        const defaults = Chart.defaults.plugins.legend.labels.generateLabels(chart);

                        // Duyệt qua từng item để thay đổi icon
                        return defaults.map(item => {
                            // Lấy dataset gốc để kiểm tra tên
                            const dataset = chart.data.datasets[item.datasetIndex];
                            const label = dataset.label;

                            // Logic gán ảnh dựa trên Label
                            if (label === '実績') {
                                item.pointStyle = currentImg;
                            } else if (label === '目標') {
                                item.pointStyle = targetImg;
                            } else {
                                // Mặc định cho các trường hợp khác (Compare, v.v.)
                                item.pointStyle = compareImg;
                            }

                            return item;
                        });
                    }
                    // ---------------------------
                }
            },
            tooltip: {
                backgroundColor: 'rgba(255, 255, 255, 0.95)',
                titleColor: '#1e293b', bodyColor: '#475569', borderColor: '#e2e8f0',
                borderWidth: 1, padding: 10,
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        if (label) label += ': ';
                        if (context.parsed.y !== null) label += Math.round(context.parsed.y).toLocaleString();
                        return label;
                    }
                }
            }
        },
        scales: {
            x: { 
                border: {
                    display: false // Ẩn đường kẻ đậm ở trục hoành (tùy chọn)
                },
                grid: { 
                    display: true,       // BẮT BUỘC: Hiện lưới dọc
                    color: '#cbd5e1',    // Màu xám (đậm hơn chút để dễ nhìn)
                    tickBorderDash: [5, 5],  // [Độ dài gạch, Độ dài khoảng trống]
                    tickBorderDashOffset: 2,
                    drawTicks: true,     
                },
                ticks: { 
                    font: { size: 11 },
                    autoSkip: false,
                    maxRotation: 0,
                    callback: function(val, index) {
                        if (props.periodType === 'week') {
                            return index % 24 === 0 ? this.getLabelForValue(val) : '';
                        }
                        return this.getLabelForValue(val);
                    }
                }
            },
            y: {
                beginAtZero: true,
                title: { display: true, text: props.unit, font: { weight: 'bold' } },
                min: props.axisSettings?.yLeftMin ? Number(props.axisSettings.yLeftMin) : undefined,
                max: props.axisSettings?.yLeftMax ? Number(props.axisSettings.yLeftMax) : undefined,
                ticks: { stepSize: props.axisSettings?.yLeftStepSize ? Number(props.axisSettings.yLeftStepSize) : undefined },
                
                border: {
                    display: false // Ẩn đường kẻ đậm ở trục tung (tùy chọn)
                },
                grid: { 
                    display: true,       // BẮT BUỘC: Hiện lưới ngang
                    color: '#cbd5e1',    // Màu xám giống trục X để tạo hiệu ứng caro đều
                    tickBorderDash: [5, 5],  // [Độ dài gạch, Độ dài khoảng trống]
                    tickBorderDashOffset: 2,
                    drawTicks: true
                }
            }
        }
    };
});
</script>