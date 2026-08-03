<script setup>
import { onBeforeUnmount, onMounted, ref, watch } from "vue";
import {
    Chart,
    LineController, LineElement, PointElement,
    BarController, BarElement,
    DoughnutController, ArcElement,
    CategoryScale, LinearScale,
    Tooltip, Legend, Filler,
} from "chart.js";

const datalabelsPlugin = {
    id: 'datalabels',
    afterDatasetsDraw(chart, args, opts) {
        const { ctx } = chart;
        const options = opts || {};
        if (!options.display) return;

        chart.data.datasets.forEach((dataset, datasetIndex) => {
            const meta = chart.getDatasetMeta(datasetIndex);
            if (meta.hidden) return;

            dataset.data.forEach((value, dataIndex) => {
                const element = meta.data[dataIndex];
                if (!element) return;

                const label = typeof options.formatter === 'function'
                    ? options.formatter(value, { dataset, dataIndex, datasetIndex })
                    : value;

                if (label === null || label === undefined || label === '') return;

                const center = element.getCenterPoint();
                const font = options.font || {};

                ctx.save();
                ctx.font = `${font.weight || 'bold'} ${font.size || 12}px ${Chart.defaults.font.family}`;
                ctx.fillStyle = options.color || '#ffffff';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(String(label), center.x, center.y);
                ctx.restore();
            });
        });
    },
};

Chart.register(
    LineController, LineElement, PointElement,
    BarController, BarElement,
    DoughnutController, ArcElement,
    CategoryScale, LinearScale,
    Tooltip, Legend, Filler,
    datalabelsPlugin,
);
Chart.defaults.font.family = 'Inter, sans-serif';



const props = defineProps({
    type: { type: String, required: true },
    data: { type: Object, required: true },
    options: { type: Object, default: () => ({}) },
});

const canvasRef = ref(null);
let chart = null;

const renderChart = () => {
    chart?.destroy();
    chart = new Chart(canvasRef.value, {
        type: props.type,
        data: props.data,
        options: props.options,
    })
};

onMounted(renderChart);
watch(() => [props.data, props.options], renderChart, { deep: true });
onBeforeUnmount(() => chart?.destroy());
</script>
<template>
    <canvas ref="canvasRef"></canvas>
</template>