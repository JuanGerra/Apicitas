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

    // Obtener datos del formulario y validar que no estén vacíos
    $Nombres = isset($_POST['Nombres']) ? $_POST['Nombres'] : '';
    $Apellidos = isset($_POST['Apellido']) ? $_POST['Apellido'] : '';
    $Telefono = isset($_POST['Telefono']) ? $_POST['Telefono'] : '';
    $Correo = isset($_POST['Correo']) ? $_POST['Correo'] : '';
    $Contraseña = isset($_POST['Contraseña']) ? $_POST['Contraseña'] : '';
    $Nombre_U = isset($_POST['Nombre_U']) ? $_POST['Nombre_U'] : '';

    // Verificar si el correo ya existe
    $sql = "SELECT * FROM usuarios WHERE Correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $Correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(["correo en uso.<a href='index.html'><button>Volver</button></a>"]);
    } else {
        // Insertar nuevo usuario
        $sql = "INSERT INTO usuarios (Nombres, Apellidos, Telefono, Correo, Contraseña, Nombre_U) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $Nombres, $Apellidos, $Telefono, $Correo, $Contraseña, $Nombre_U);

        if ($stmt->execute()) {
            echo json_encode(["Registro exitoso.<a href='index.html'><button>Iniciar sesion</button></a>"]);
        } else {
            echo json_encode(["Error al registrarse.<a href='index.html'><button>Volver</button></a>"]);
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
        <input type="text" name="Nombre" placeholder="Nombre" required>
        <input type="text" name="Apellido" placeholder="Apellido" required>
        <input type="tel" name="Telefono" placeholder="Telefono" required>
        <input type="email" name="Correo" placeholder="Correo" required>
        <input type="password" name="Contraseña" placeholder="Contraseña" required>
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