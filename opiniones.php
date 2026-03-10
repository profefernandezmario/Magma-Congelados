<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

$archivo = 'opiniones.json';

// Función para leer opiniones
function leerOpiniones() {
    global $archivo;
    if (!file_exists($archivo)) {
        // Opiniones por defecto (las tres de ejemplo)
        $opinionesEjemplo = [
            ["nombre" => "Lucía M.", "producto" => "Chipacitos", "texto" => "Los chipacitos congelados son prácticos, quedan como recién hechos.", "fecha" => "10/2/2025"],
            ["nombre" => "Roberto S.", "producto" => "Empanadas de Carne", "texto" => "Las empanadas de carne son riquísimas, horneadas quedan perfectas.", "fecha" => "18/2/2025"],
            ["nombre" => "Camila F.", "producto" => "Mini Churros", "texto" => "Los churritos con salsa son un vicio. Compré congelados y los freí en casa.", "fecha" => "22/2/2025"]
        ];
        file_put_contents($archivo, json_encode($opinionesEjemplo, JSON_PRETTY_PRINT));
        return $opinionesEjemplo;
    }
    $contenido = file_get_contents($archivo);
    return json_decode($contenido, true) ?? [];
}

// Si es GET, devolver todas las opiniones
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode(leerOpiniones());
    exit;
}

// Si es POST, guardar nueva opinión
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = json_decode(file_get_contents('php://input'), true);
    
    // Validar campos requeridos
    if (empty($datos['nombre']) || empty($datos['texto'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Nombre y texto son obligatorios']);
        exit;
    }

    $opiniones = leerOpiniones();
    
    // Agregar nueva opinión
    $nueva = [
        'nombre' => htmlspecialchars($datos['nombre']),
        'producto' => htmlspecialchars($datos['producto'] ?? ''),
        'texto' => htmlspecialchars($datos['texto']),
        'fecha' => date('d/m/Y')
    ];
    
    array_push($opiniones, $nueva);
    
    // Guardar en el archivo
    file_put_contents($archivo, json_encode($opiniones, JSON_PRETTY_PRINT));
    
    echo json_encode($nueva);
    exit;
}

// Método no permitido
http_response_code(405);
echo json_encode(['error' => 'Método no permitido']);