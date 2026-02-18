<?php

/** @var yii\web\View $this */
/** @var array $estadisticas */
/** @var array $chartsData */

use yii\bootstrap5\Html;

$this->title = 'Estadísticas y Gráficas';
$this->params['breadcrumbs'][] = ['label' => 'Reportes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Register Chart.js from CDN
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js', ['position' => \yii\web\View::POS_HEAD]);
?>

<div class="report-estadisticas">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a('<i class="bi bi-arrow-left"></i> Volver', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>
    
    <p class="lead">Análisis visual de indicadores clave del sistema de gestión de tesis.</p>
    
    <!-- Key Indicators -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center border-primary">
                <div class="card-body">
                    <h3 class="text-primary"><?= $estadisticas['total_stt'] ?></h3>
                    <p class="text-muted small mb-0">Total Solicitudes</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-success">
                <div class="card-body">
                    <h3 class="text-success"><?= $estadisticas['tasa_aceptacion'] ?>%</h3>
                    <p class="text-muted small mb-0">Tasa de Aceptación</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-info">
                <div class="card-body">
                    <h3 class="text-info"><?= $estadisticas['promedio_tiempo_resolucion'] ?></h3>
                    <p class="text-muted small mb-0">Días Prom. Resolución</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-warning">
                <div class="card-body">
                    <h3 class="text-warning"><?= $estadisticas['promedio_revisores'] ?></h3>
                    <p class="text-muted small mb-0">Prom. Revisores/Tesis</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Chart 1: Bar Chart - Modality Distribution -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Distribución de Modalidades en Estados</h5>
        </div>
        <div class="card-body">
            <p class="text-muted">¿Cuál es la distribución de las modalidades de tesis (TT, Papers, Pasantías) en diferentes estados?</p>
            <canvas id="modalidadesChart" style="max-height: 400px;"></canvas>
        </div>
    </div>
    
    <!-- Chart 2: Pie Chart - Category Distribution -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-pie-chart"></i> Categorías Principales de Tesis</h5>
        </div>
        <div class="card-body">
            <p class="text-muted">¿Cuáles son las áreas temáticas principales donde se concentran las tesis?</p>
            <div style="max-width: 600px; margin: 0 auto;">
                <canvas id="categoriasChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Chart 3: Line Chart - Monthly Evolution -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="bi bi-graph-up"></i> Evolución Mensual de STT</h5>
        </div>
        <div class="card-body">
            <p class="text-muted">¿Cómo ha evolucionado la cantidad de solicitudes de tema de tesis en los últimos 12 meses?</p>
            <canvas id="evolucionChart" style="max-height: 400px;"></canvas>
        </div>
    </div>
    
    <!-- Chart 4: Stacked Bar Chart - Modality by State -->
    <div class="card mb-4">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="bi bi-bar-chart-steps"></i> Tesis Agrupadas por Modalidad y Estado</h5>
        </div>
        <div class="card-body">
            <p class="text-muted">¿Cómo se distribuyen las tesis según modalidad y su estado actual?</p>
            <canvas id="modalidadEstadoChart" style="max-height: 400px;"></canvas>
        </div>
    </div>
    
    <!-- Chart 5: Grouped Bar Chart - Resolution Times -->
    <div class="card mb-4">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0"><i class="bi bi-clock-history"></i> Tiempos de Resolución por Rol (Guía vs Revisor)</h5>
        </div>
        <div class="card-body">
            <p class="text-muted">¿Cuál es el tiempo promedio de resolución de STT según el rol del profesor (guía o revisor)?</p>
            <canvas id="tiemposResolucionChart" style="max-height: 400px;"></canvas>
        </div>
    </div>
    
    <!-- Summary Section -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-file-earmark-text"></i> Resumen de Indicadores</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>Estado de Solicitudes</h6>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($estadisticas['stt_por_estado'] as $estado => $count): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?= Html::encode($estado) ?>
                                <span class="badge bg-primary rounded-pill"><?= $count ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6>Tesis por Modalidad</h6>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($estadisticas['tesis_por_modalidad'] as $modalidad => $count): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?= Html::encode($modalidad) ?>
                                <span class="badge bg-success rounded-pill"><?= $count ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$modalidadesData = json_encode($chartsData['modalidades']);
$categoriasData = json_encode($chartsData['categorias']);
$evolucionData = json_encode($chartsData['evolucionMensual']);
$modalidadEstadoData = json_encode($chartsData['modalidadEstado']);
$tiemposData = json_encode($chartsData['tiemposResolucion']);

$this->registerJs(<<<JS
// Constants
const NO_DATA_MESSAGE = '<div class="alert alert-info text-center"><i class="bi bi-info-circle"></i> No hay datos disponibles para mostrar este gráfico.</div>';
const NO_DATA_MESSAGE_PREFIX = '<div class="alert alert-info text-center"><i class="bi bi-info-circle"></i> No hay datos disponibles para mostrar este gráfico. ';

// Chart 1: Bar Chart - Modalities Distribution
const modalidadesData = $modalidadesData;
if (modalidadesData && modalidadesData.labels && modalidadesData.labels.length > 0) {
    const modalidadesCtx = document.getElementById('modalidadesChart').getContext('2d');
    new Chart(modalidadesCtx, {
        type: 'bar',
        data: {
            labels: modalidadesData.labels,
            datasets: [{
                label: 'Cantidad de Solicitudes',
                data: modalidadesData.values,
                backgroundColor: [
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                title: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
} else {
    const container = document.getElementById('modalidadesChart').parentElement;
    container.innerHTML = NO_DATA_MESSAGE;
}

// Chart 2: Pie Chart - Categories
const categoriasData = $categoriasData;
if (categoriasData && categoriasData.labels && categoriasData.labels.length > 0) {
    const categoriasCtx = document.getElementById('categoriasChart').getContext('2d');
    
    // Generate dynamic colors for all categories
    const generateColor = (index, total) => {
        const hue = (index * 360 / total) % 360;
        return `hsla(\${hue}, 70%, 60%, 0.8)`;
    };
    
    const colors = categoriasData.labels.map((_, index) => 
        generateColor(index, categoriasData.labels.length)
    );
    
    const borderColors = categoriasData.labels.map((_, index) => 
        generateColor(index, categoriasData.labels.length).replace('0.8', '1')
    );
    
    new Chart(categoriasCtx, {
        type: 'pie',
        data: {
            labels: categoriasData.labels,
            datasets: [{
                data: categoriasData.values,
                backgroundColor: colors,
                borderColor: borderColors,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { 
                    position: 'right',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return label + ': ' + value + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
} else {
    // Show "no data available" message
    const container = document.getElementById('categoriasChart').parentElement;
    container.innerHTML = NO_DATA_MESSAGE_PREFIX + 'Asegúrese de que existen categorías activas configuradas en el sistema.</div>';
}

// Chart 3: Line Chart - Monthly Evolution
const evolucionData = $evolucionData;
if (evolucionData && evolucionData.labels && evolucionData.labels.length > 0) {
    const evolucionCtx = document.getElementById('evolucionChart').getContext('2d');
    new Chart(evolucionCtx, {
        type: 'line',
        data: {
            labels: evolucionData.labels,
            datasets: [{
                label: 'Solicitudes por Mes',
                data: evolucionData.values,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: true }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
} else {
    const container = document.getElementById('evolucionChart').parentElement;
    container.innerHTML = NO_DATA_MESSAGE;
}

// Chart 4: Stacked Bar Chart - Modality by State
const modalidadEstadoData = $modalidadEstadoData;
if (modalidadEstadoData && modalidadEstadoData.labels && modalidadEstadoData.labels.length > 0 && modalidadEstadoData.datasets && modalidadEstadoData.datasets.length > 0) {
    const colors = [
        'rgba(54, 162, 235, 0.7)',
        'rgba(255, 206, 86, 0.7)',
        'rgba(75, 192, 192, 0.7)',
        'rgba(153, 102, 255, 0.7)',
        'rgba(255, 99, 132, 0.7)',
    ];
    const datasets = modalidadEstadoData.datasets.map((dataset, index) => ({
        label: dataset.label,
        data: dataset.values,
        backgroundColor: colors[index % colors.length]
    }));

    const modalidadEstadoCtx = document.getElementById('modalidadEstadoChart').getContext('2d');
    new Chart(modalidadEstadoCtx, {
        type: 'bar',
        data: {
            labels: modalidadEstadoData.labels,
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                x: { stacked: true },
                y: { 
                    stacked: true,
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
} else {
    const container = document.getElementById('modalidadEstadoChart').parentElement;
    container.innerHTML = NO_DATA_MESSAGE;
}

// Chart 5: Grouped Bar Chart - Resolution Times
const tiemposData = $tiemposData;
if (tiemposData && tiemposData.labels && tiemposData.labels.length > 0) {
    const tiemposCtx = document.getElementById('tiemposResolucionChart').getContext('2d');
    new Chart(tiemposCtx, {
        type: 'bar',
        data: {
            labels: tiemposData.labels,
            datasets: [
                {
                    label: 'Como Guía (días)',
                    data: tiemposData.guia,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Como Revisor (días)',
                    data: tiemposData.revisor,
                    backgroundColor: 'rgba(255, 99, 132, 0.7)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Días Promedio'
                    }
                }
            }
        }
    });
} else {
    // Show "no data" message
    const container = document.getElementById('tiemposResolucionChart').parentElement;
    container.innerHTML = '<div class="alert alert-info"><i class="bi bi-info-circle"></i> No hay datos suficientes para mostrar este gráfico. Se requiere al menos un profesor con resoluciones de STT.</div>';
}
JS
, \yii\web\View::POS_READY);
?>
