<?php
$servername = "localhost";
$username = "root"; // Cambia si tienes otra configuración
$password = "";
$dbname = "hospital";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener datos del formulario
$Nombre_U = $_POST['Nombre'];
$Contraseña = $_POST['Contraseña'];  // Contraseña proporcionada por el usuario

// Consulta para verificar solo el nombre
$sql = "SELECT * FROM usuarios WHERE Nombre_U = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $Nombre_U);  // Solo se pasa un parámetro: Nombre
$stmt->execute();
$result = $stmt->get_result();

// Verificar si el usuario existe con ese nombre
if ($result->num_rows == 0) {
    echo "No se encontraron datos para el usuario ingresado. <br>";
    echo "<a href='registrar.html'><button>Registrar</button></a>"; // Botón que redirige a registrar.html
    echo "<br><a href='index.html'><button>Volver</button></a>"; // Botón para regresar al inicio
} else {
    // Obtener los datos del usuario de la base de datos
    $row = $result->fetch_assoc();
    
    // Comparar la contraseña ingresada con la almacenada en la base de datos
    if ($Contraseña === $row['Contraseña']) {
        // Si la contraseña es correcta, redirigir
        echo "Inicio de sesión exitoso. Redirigiendo...";
        header("Location: agendar.html"); // Cambia a la página deseada
        exit(); // Es importante salir después de redirigir
    } else {
        // Si la contraseña es incorrecta, mostrar error
        echo "La contraseña es incorrecta. <br>";
        echo "<a href='index.html'><button>Volver</button></a>"; // Botón para regresar al inicio
    }
}

$stmt->close();
$conn->close();
?>