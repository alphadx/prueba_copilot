<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Profesor</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10pt; }
        h1 { font-size: 16pt; margin-bottom: 10px; }
        h2 { font-size: 14pt; margin-top: 20px; margin-bottom: 10px; background-color: #f0f0f0; padding: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #4a5568; color: white; }
        .info-box { background-color: #f9f9f9; padding: 10px; margin-bottom: 15px; border-left: 4px solid #4299e1; }
    </style>
</head>
<body>
    <h1>Reporte de Tesis - Profesor</h1>
    
    <div class="info-box">
        <p><strong>Profesor:</strong> <?= htmlspecialchars($profesor->nombre) ?></p>
        <p><strong>RUT:</strong> <?= htmlspecialchars($profesor->rut) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($profesor->correo) ?></p>
        <p><strong>Fecha de Reporte:</strong> <?= date('d/m/Y H:i') ?></p>
    </div>
    
    <h2>Tesis como Profesor Guía (<?= count($tesisComoGuia) ?>)</h2>
    
    <?php if (empty($tesisComoGuia)): ?>
        <p>No hay tesis asignadas como profesor guía.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Correlativo</th>
                    <th>Título</th>
                    <th>Modalidad</th>
                    <th>Alumnos</th>
                    <th>Estado</th>
                    <th>Etapa</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tesisComoGuia as $tesis): ?>
                    <tr>
                        <td><?= htmlspecialchars($tesis->stt->correlativo) ?></td>
                        <td><?= htmlspecialchars($tesis->stt->titulo) ?></td>
                        <td><?= htmlspecialchars($tesis->stt->modalidad->nombre) ?></td>
                        <td>
                            <?php 
                            $alumnos = array_map(function($a) { return htmlspecialchars($a->nombre); }, $tesis->stt->alumnos);
                            echo implode(', ', $alumnos);
                            ?>
                        </td>
                        <td><?= htmlspecialchars($tesis->estado) ?></td>
                        <td><?= htmlspecialchars($tesis->getEtapaLabel()) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    
    <h2>Tesis como Profesor Revisor (<?= count($tesisComoRevisor) ?>)</h2>
    
    <?php if (empty($tesisComoRevisor)): ?>
        <p>No hay tesis asignadas como profesor revisor.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Correlativo</th>
                    <th>Título</th>
                    <th>Modalidad</th>
                    <th>Alumnos</th>
                    <th>Estado</th>
                    <th>Etapa</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tesisComoRevisor as $tesis): ?>
                    <tr>
                        <td><?= htmlspecialchars($tesis->stt->correlativo) ?></td>
                        <td><?= htmlspecialchars($tesis->stt->titulo) ?></td>
                        <td><?= htmlspecialchars($tesis->stt->modalidad->nombre) ?></td>
                        <td>
                            <?php 
                            $alumnos = array_map(function($a) { return htmlspecialchars($a->nombre); }, $tesis->stt->alumnos);
                            echo implode(', ', $alumnos);
                            ?>
                        </td>
                        <td><?= htmlspecialchars($tesis->estado) ?></td>
                        <td><?= htmlspecialchars($tesis->getEtapaLabel()) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
