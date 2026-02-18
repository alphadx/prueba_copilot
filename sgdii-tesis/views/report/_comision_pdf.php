<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Comisión</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 8pt; }
        h1 { font-size: 14pt; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 7pt; }
        th, td { border: 1px solid #ddd; padding: 4px; text-align: left; }
        th { background-color: #4a5568; color: white; }
        .info-box { background-color: #f9f9f9; padding: 8px; margin-bottom: 10px; border-left: 4px solid #48bb78; }
    </style>
</head>
<body>
    <h1>Reporte de Comisión Evaluadora</h1>
    
    <div class="info-box">
        <p><strong>Fecha de Reporte:</strong> <?= date('d/m/Y H:i') ?></p>
        <p><strong>Total de Solicitudes:</strong> <?= count($solicitudes) ?></p>
    </div>
    
    <?php if (empty($solicitudes)): ?>
        <p>No se encontraron solicitudes.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Correlativo</th>
                    <th>Título</th>
                    <th>Modalidad</th>
                    <th>Estado</th>
                    <th>Alumnos</th>
                    <th>Profesor Guía</th>
                    <th>Fecha Creación</th>
                    <th>Fecha Resolución</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($solicitudes as $stt): ?>
                    <tr>
                        <td><?= htmlspecialchars($stt->correlativo) ?></td>
                        <td><?= htmlspecialchars(substr($stt->titulo, 0, 80)) ?></td>
                        <td><?= htmlspecialchars($stt->modalidad->nombre) ?></td>
                        <td><?= htmlspecialchars($stt->estado) ?></td>
                        <td>
                            <?php 
                            $alumnos = array_map(function($a) { return htmlspecialchars($a->nombre); }, $stt->alumnos);
                            echo implode(', ', $alumnos);
                            ?>
                        </td>
                        <td><?= $stt->profesorGuiaPropuesto ? htmlspecialchars($stt->profesorGuiaPropuesto->nombre) : 'N/A' ?></td>
                        <td><?= date('d/m/Y', strtotime($stt->fecha_creacion)) ?></td>
                        <td><?= $stt->fecha_resolucion ? date('d/m/Y', strtotime($stt->fecha_resolucion)) : 'Pendiente' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
