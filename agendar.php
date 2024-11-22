<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "hospital";

    // Crear conexión
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar conexión
    if ($conn->connect_error) {
        echo json_encode(["status" => "error", "message" => "Conexión fallida."]);
        exit;
    }

    // Capturar datos del formulario
    $Nombres = isset($_POST['Nombres']) ? $_POST['Nombres'] : '';
    $Apellidos = isset($_POST['Apellidos']) ? $_POST['Apellidos'] : '';
    $Telefono = isset($_POST['Telefono']) ? $_POST['Telefono'] : '';
    $Correo = isset($_POST['Correo']) ? $_POST['Correo'] : '';
    $Fecha_de_nacimiento = isset($_POST['Nacimiento']) ? $_POST['Nacimiento'] : '';
    $Direccion_r = isset($_POST['Direccion']) ? $_POST['Direccion'] : '';
    $Numero_D = isset($_POST['Numero_D']) ? $_POST['Numero_D'] : '';

    // Verificar si el correo ya existe
    $sql = "SELECT * FROM clientes WHERE Correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $Correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "El correo ya está en uso."]);
    } else {
        // Insertar nuevo usuario
        $sql = "INSERT INTO clientes (Nombres, Apellidos, Telefono, Correo, Fecha_de_nacimiento, Direccion_r, Numero_D) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $Nombres, $Apellidos, $Telefono, $Correo, $Fecha_de_nacimiento, $Direccion_r, $Numero_D);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Registro exitoso."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al registrarse."]);
        }
    }

    $stmt->close();
    $conn->close();
    exit; // Finaliza la ejecución del script PHP para no devolver el HTML en la respuesta Ajax.
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div id="mensaje" class="alert"></div>
    <form id="formulario" method="POST">
        <input type="text" name="Nombres" placeholder="Nombres" required>
        <input type="text" name="Apellidos" placeholder="Apellidos" required>
        <input type="tel" name="Telefono" placeholder="Teléfono" required>
        <input type="email" name="Correo" placeholder="Correo electrónico" required>
        <input type="date" name="Nacimiento" placeholder="Nacimiento" required>
        <input type="text" name="Direccion" placeholder="Direccion" required>
        <input type="number" name="Numero_D" placeholder="Número de documento" required>
        <button type="submit">Registrar</button>
    </form>

    <script>
        $(document).ready(function () {
            $('#formulario').on('submit', function (event) {
                event.preventDefault();

                $.ajax({
                    url: '', // Deja vacío para que apunte al mismo archivo PHP
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        let result = JSON.parse(response);
                        if (result.status === "success") {
                            $('#mensaje')
                                .removeClass('alert-danger')
                                .addClass('alert-success')
                                .text(result.message)
                                .show();
                            $('#formulario')[0].reset(); // Reiniciar formulario
                        } else {
                            $('#mensaje')
                                .removeClass('alert-success')
                                .addClass('alert-danger')
                                .text(result.message)
                                .show();
                        }
                    },
                    error: function () {
                        $('#mensaje')
                            .removeClass('alert-success')
                            .addClass('alert-danger')
                            .text("Error en la solicitud.")
                            .show();
                    }
                });
            });
        });
    </script>
</body>
</html>