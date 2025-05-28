<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario de Registro</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }
        .form-container {
            background-color: #fff;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        input[type="text"],
        input[type="email"],
        input[type="tel"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: rgb(175, 76, 167);
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        input[type="submit"]:hover {
            background-color: rgb(160, 69, 160);
        }
        .success-message {
            margin-top: 20px;
            padding: 15px;
            background-color: #e8f5e9;
            border-left: 4px solid #4caf50;
        }
        .error-message {
            margin-top: 20px;
            padding: 15px;
            background-color: #ffebee;
            border-left: 4px solid #f44336;
        }
        .db-status {
            margin-top: 20px;
            padding: 15px;
            background-color: #e3f2fd;
            border-left: 4px solid #2196f3;
        }
        .db-status.success {
            background-color: #e8f5e9;
            border-left-color: #4caf50;
        }
        .db-status.error {
            background-color: #ffebee;
            border-left-color: #f44336;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .registros-title {
            margin-top: 40px;
            color: #333;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Registro de Usuario</h1>
        <form method="post" action="">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            
            <div class="form-group">
                <label for="primer_apellido">Primer Apellido:</label>
                <input type="text" id="primer_apellido" name="primer_apellido" required>
            </div>
            
            <div class="form-group">
                <label for="segundo_apellido">Segundo Apellido:</label>
                <input type="text" id="segundo_apellido" name="segundo_apellido">
            </div>
            
            <div class="form-group">
                <label for="correo">Correo Electrónico:</label>
                <input type="email" id="correo" name="correo" required>
            </div>
            
            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="tel" id="telefono" name="telefono" required>
            </div>
            
            <input type="submit" name="enviar" value="Enviar">
        </form>

        <?php
        // Verificar si el formulario ha sido enviado
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['enviar'])) {
            // Recoger y sanitizar los datos del formulario
            $nombre = trim($_POST['nombre']);
            $primer_apellido = trim($_POST['primer_apellido']);
            $segundo_apellido = trim($_POST['segundo_apellido'] ?? '');
            $correo = trim($_POST['correo']);
            $telefono = trim($_POST['telefono']);

            // Validaciones básicas
            $errores = [];
            if (empty($nombre)) $errores[] = "El nombre es obligatorio";
            if (empty($primer_apellido)) $errores[] = "El primer apellido es obligatorio";
            if (empty($correo)) {
                $errores[] = "El correo electrónico es obligatorio";
            } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $errores[] = "El formato del correo electrónico no es válido";
            }
            if (empty($telefono)) $errores[] = "El teléfono es obligatorio";

            // Si no hay errores, procedemos con la conexión a la base de datos
            if (empty($errores)) {
                try {
                    // Configuración de la conexión PDO para Azure SQL
                    $serverName = "bdserversql.database.windows.net";
                    $database = "bdsql01";
                    $username = "adminsql";
                    $password = "Servid0r1";
                    
                    $conn = new PDO("sqlsrv:server=$serverName;Database=$database", $username, $password);
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    
                    // Preparar la consulta SQL para insertar
                    $sql = "INSERT INTO usuarios (nombre, primer_apellido, segundo_apellido, correo, telefono, fecha_registro) 
                            VALUES (:nombre, :primer_apellido, :segundo_apellido, :correo, :telefono, GETDATE())";
                    
                    $stmt = $conn->prepare($sql);
                    
                    // Bind parameters
                    $stmt->bindParam(':nombre', $nombre);
                    $stmt->bindParam(':primer_apellido', $primer_apellido);
                    $stmt->bindParam(':segundo_apellido', $segundo_apellido);
                    $stmt->bindParam(':correo', $correo);
                    $stmt->bindParam(':telefono', $telefono);
                    
                    // Ejecutar la consulta
                    $stmt->execute();
                    
                    // Mensaje de éxito
                    echo '<div class="success-message">Registro completado con éxito!</div>';
                    echo '<div class="db-status success">Conexión a la base de datos establecida correctamente.</div>';
                    
                } catch (PDOException $e) {
                    // Manejo de errores
                    echo '<div class="error-message">Error al registrar los datos: ' . $e->getMessage() . '</div>';
                    echo '<div class="db-status error">Error al conectar con la base de datos: ' . $e->getMessage() . '</div>';
                } finally {
                    // Cerrar conexión
                    $conn = null;
                }
            } else {
                // Mostrar errores de validación
                echo '<div class="error-message"><ul>';
                foreach ($errores as $error) {
                    echo '<li>' . htmlspecialchars($error) . '</li>';
                }
                echo '</ul></div>';
            }
        }

        // Mostrar los registros existentes
        try {
            $serverName = "bdserversql.database.windows.net";
            $database = "bdsql01";
            $username = "adminsql";
            $password = "Servid0r1";
            
            $conn = new PDO("sqlsrv:server=$serverName;Database=$database", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Consulta para obtener todos los registros
            $sql = "SELECT id, nombre, primer_apellido, segundo_apellido, correo, telefono, 
                    CONVERT(VARCHAR, fecha_registro, 120) as fecha_registro 
                    FROM usuarios ORDER BY fecha_registro DESC";
            
            $stmt = $conn->query($sql);
            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($registros) > 0) {
                echo '<h2 class="registros-title">Registros Existentes</h2>';
                echo '<table>';
                echo '<tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Primer Apellido</th>
                        <th>Segundo Apellido</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Fecha Registro</th>
                      </tr>';
                
                foreach ($registros as $registro) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($registro['id']) . '</td>';
                    echo '<td>' . htmlspecialchars($registro['nombre']) . '</td>';
                    echo '<td>' . htmlspecialchars($registro['primer_apellido']) . '</td>';
                    echo '<td>' . htmlspecialchars($registro['segundo_apellido']) . '</td>';
                    echo '<td>' . htmlspecialchars($registro['correo']) . '</td>';
                    echo '<td>' . htmlspecialchars($registro['telefono']) . '</td>';
                    echo '<td>' . htmlspecialchars($registro['fecha_registro']) . '</td>';
                    echo '</tr>';
                }
                
                echo '</table>';
            } else {
                echo '<div class="db-status">No hay registros existentes en la base de datos.</div>';
            }
            
        } catch (PDOException $e) {
            echo '<div class="db-status error">Error al obtener registros: ' . $e->getMessage() . '</div>';
        }
        ?>
        
    </div>
</body>
</html>