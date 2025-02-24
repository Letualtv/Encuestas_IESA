<?php
// guardarTextos.php

header('Content-Type: application/json');


try {
    // Leer los datos enviados por POST
    $data = json_decode(file_get_contents('php://input'), true);

    $section = $data['section'];
    $index = $data['index'];
    $question = $data['question'];
    $answer = $data['answer'];

    // Leer el archivo JSON existente
    $jsonFile = '../../models/textos.json';
    $textos = json_decode(file_get_contents($jsonFile), true);

    // Actualizar los datos
    $textos[$section][$index]['question'] = $question;
    $textos[$section][$index]['answer'] = $answer;

    // Guardar los cambios en el archivo JSON
    file_put_contents($jsonFile, json_encode($textos, JSON_PRETTY_PRINT));

    // Responder al cliente
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

?>