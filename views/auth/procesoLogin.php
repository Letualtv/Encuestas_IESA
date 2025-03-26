<?php
session_start();
include __DIR__ . '/../../config/db.php';
include __DIR__ . '/../../controller/PreguntasController.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $clave = trim(strtolower($_POST['clave']));

    // Validar la clave
    if (empty($clave) || !ctype_alnum($clave)) {
        echo json_encode(["success" => false, "message" => "La clave es inválida."]);
        exit;
    }

    try {
        // Comprobar si la clave existe en la base de datos
        $query = "SELECT IDGrupo FROM claves WHERE clave = :clave";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':clave', $clave, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $claveId = $result['IDGrupo'];

            // Verificar si ya existe un registro en la tabla muestra para esta clave
            $checkQuery = "SELECT reg_m, n_login FROM muestra WHERE clave = :clave";
            $checkStmt = $pdo->prepare($checkQuery);
            $checkStmt->bindParam(':clave', $clave, PDO::PARAM_STR);
            $checkStmt->execute();

            if ($checkStmt->rowCount() === 0) {
                // Insertar un nuevo registro en la tabla muestra
                $browser = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';
                $lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'Desconocido';
                $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
                $date_login = date('Y-m-d H:i:s');

                $insertQuery = "INSERT INTO muestra (clave, browser, lang, ip, n_login, date_login) 
                                VALUES (:clave, :browser, :lang, :ip, 1, :date_login)";
                $insertStmt = $pdo->prepare($insertQuery);
                $insertStmt->bindParam(':clave', $clave, PDO::PARAM_STR);
                $insertStmt->bindParam(':browser', $browser, PDO::PARAM_STR);
                $insertStmt->bindParam(':lang', $lang, PDO::PARAM_STR);
                $insertStmt->bindParam(':ip', $ip, PDO::PARAM_STR);
                $insertStmt->bindParam(':date_login', $date_login, PDO::PARAM_STR);
                $insertStmt->execute();

                $reg_m = $pdo->lastInsertId();
            } else {
                // Obtener el registro existente y actualizar n_login
                $existingRecord = $checkStmt->fetch(PDO::FETCH_ASSOC);
                $reg_m = $existingRecord['reg_m'];
                $currentLoginCount = $existingRecord['n_login'];

                // Incrementar n_login en la tabla muestra
                $updateQuery = "UPDATE muestra SET n_login = n_login + 1 WHERE reg_m = :reg_m";
                $updateStmt = $pdo->prepare($updateQuery);
                $updateStmt->bindParam(':reg_m', $reg_m, PDO::PARAM_INT);
                $updateStmt->execute();

                // Verificar si el incremento fue exitoso
                if ($updateStmt->rowCount() === 0) {
                    echo json_encode(["success" => false, "message" => "Error al actualizar el contador de inicios de sesión."]);
                    exit;
                }
            }

            // Guardar datos en la sesión
            $_SESSION['clave'] = $clave;
            $_SESSION['clave_id'] = $claveId;
            $_SESSION['reg_m'] = $reg_m;

            // Calcular la última página completada
            $respuestasQuery = "SELECT * FROM cuestionario WHERE reg_m = :reg_m";
            $respuestasStmt = $pdo->prepare($respuestasQuery);
            $respuestasStmt->bindParam(':reg_m', $reg_m, PDO::PARAM_INT);
            $respuestasStmt->execute();
            $respuestasResult = $respuestasStmt->fetch(PDO::FETCH_ASSOC);

            $lastPage = 1;
            if ($respuestasResult) {
                $preguntas = json_decode(file_get_contents(__DIR__ . '/../../models/Preguntas.json'), true);
                $paginas = array_unique(array_column($preguntas, 'n_pag'));
                sort($paginas);

                foreach (array_reverse($paginas) as $pagina) {
                    $preguntasEnPagina = array_filter($preguntas, fn($p) => $p['n_pag'] === $pagina);
                    $completada = true;

                    foreach ($preguntasEnPagina as $pregunta) {
                        if (!isset($respuestasResult["r{$pregunta['id']}"])) {
                            $completada = false;
                            break;
                        }
                    }

                    if ($completada) {
                        $lastPage = $pagina;
                        break;
                    }
                }
            }

           // Redirige al controlador de preguntas con la última página completada
           // Redirige al controlador de preguntas con la última página completada
           header("Location: cuestionario?n_pag=$lastPage");
           exit;
       } else {
           $errorMessage = "Clave incorrecta. Por favor, intenta nuevamente.";
           error_log($errorMessage);
       }
   } catch (PDOException $e) {
       $errorMessage = "Error de conexión con la base de datos: " . $e->getMessage();
       error_log($errorMessage);
   }
}
?>