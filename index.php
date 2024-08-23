<?php
$servername = "localhost"; // Cambia si tu servidor no es local
$username = "root"; // Tu usuario de MySQL
$password = ""; // Tu contraseña de MySQL
$dbname = "biblioteca"; // Nombre de tu base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Manejar adiciones de libros
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_libro'])) {
    $titulo = $_POST['titulo'];
    $autor = $_POST['autor'];
    $año_publicacion = $_POST['año_publicacion'];
    $genero = $_POST['genero'];

    $sql = "INSERT INTO libros (titulo, autor, año_publicacion, genero) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssis", $titulo, $autor, $año_publicacion, $genero);

    if ($stmt->execute()) {
        echo "Libro añadido exitosamente.<br>";
    } else {
        echo "Error añadiendo libro: " . $stmt->error . "<br>";
    }
    $stmt->close();
}

// Manejar eliminaciones de libros
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_libro'])) {
    $id = $_POST['id_libro'];

    $sql = "DELETE FROM libros WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "Libro eliminado exitosamente.<br>";
    } else {
        echo "Error eliminando libro: " . $stmt->error . "<br>";
    }
    $stmt->close();
}

// Manejar modificaciones de libros
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_libro'])) {
    $id = $_POST['id_libro'];
    $titulo = $_POST['titulo'];
    $autor = $_POST['autor'];
    $año_publicacion = $_POST['año_publicacion'];
    $genero = $_POST['genero'];

    $sql = "UPDATE libros SET titulo = ?, autor = ?, año_publicacion = ?, genero = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssisi", $titulo, $autor, $año_publicacion, $genero, $id);

    if ($stmt->execute()) {
        echo "Libro modificado exitosamente.<br>";
    } else {
        echo "Error modificando libro: " . $stmt->error . "<br>";
    }
    $stmt->close();
}

// Manejar búsqueda de libros
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_libro'])) {
    $titulo = $_POST['titulo'];

    $sql = "SELECT * FROM libros WHERE titulo LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchParam = "%" . $titulo . "%";
    $stmt->bind_param("s", $searchParam);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<h3>Resultados de búsqueda de libros:</h3>";
        echo "<table border='1'><tr><th>ID</th><th>Título</th><th>Autor</th><th>Año de Publicación</th><th>Género</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row['id'] . "</td><td>" . $row['titulo'] . "</td><td>" . $row['autor'] . "</td><td>" . $row['año_publicacion'] . "</td><td>" . $row['genero'] . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "No se encontraron libros con ese título.<br>";
    }
    $stmt->close();
}

// Manejar adiciones de autores
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_autor'])) {
    $nombre = $_POST['nombre'];

    $sql = "INSERT INTO autores (nombre) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $nombre);

    if ($stmt->execute()) {
        echo "Autor añadido exitosamente.<br>";
    } else {
        echo "Error añadiendo autor: " . $stmt->error . "<br>";
    }
    $stmt->close();
}

// Manejar eliminaciones de autores
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_autor'])) {
    $id = $_POST['id_autor'];

    $sql = "DELETE FROM autores WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "Autor eliminado exitosamente.<br>";
    } else {
        echo "Error eliminando autor: " . $stmt->error . "<br>";
    }
    $stmt->close();
}

// Manejar modificaciones de autores
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_autor'])) {
    $id = $_POST['id_autor'];
    $nombre = $_POST['nombre'];

    $sql = "UPDATE autores SET nombre = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $nombre, $id);

    if ($stmt->execute()) {
        echo "Autor modificado exitosamente.<br>";
    } else {
        echo "Error modificando autor: " . $stmt->error . "<br>";
    }
    $stmt->close();
}

// Manejar búsqueda de autores
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_autor'])) {
    $nombre = $_POST['nombre'];

    $sql = "SELECT * FROM autores WHERE nombre LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchParam = "%" . $nombre . "%";
    $stmt->bind_param("s", $searchParam);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<h3>Resultados de búsqueda de autores:</h3>";
        echo "<table border='1'><tr><th>ID</th><th>Nombre</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row['id'] . "</td><td>" . $row['nombre'] . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "No se encontraron autores con ese nombre.<br>";
    }
    $stmt->close();
}
// Función para mostrar tablas
function mostrarTabla($conn, $tabla) {
    $sql = "SELECT * FROM $tabla";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<h2>Tabla: $tabla</h2>";
        echo "<table border='1'><tr>";

        // Obtener nombres de las columnas
        while ($field = $result->fetch_field()) {
            echo "<th>" . htmlspecialchars($field->name) . "</th>";
        }
        echo "</tr>";

     // Obtener filas de resultados
     while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "0 resultados en la tabla $tabla";
}
}

// Mostrar tabla libros
mostrarTabla($conn, "libros");

?>

<h3>Añadir Libro</h3>
<form method="post">
    Título: <input type="text" name="titulo" required>
    Autor: <input type="text" name="autor" required>
    Año de Publicación: <input type="number" name="año_publicacion" required>
    Género: <input type="text" name="genero" required>
    <input type="submit" name="add_libro" value="Añadir Libro">
</form>

<h3>Eliminar Libro</h3>
<form method="post">
    ID del Libro: <input type="number" name="id_libro" required>
    <input type="submit" name="delete_libro" value="Eliminar Libro">
</form>

<h3>Modificar Libro</h3>
<form method="post">
    ID del Libro: <input type="number" name="id_libro" required>
    Nuevo Título: <input type="text" name="titulo" required>
    Nuevo Autor: <input type="text" name="autor" required>
    Nuevo Año de Publicación: <input type="number" name="año_publicacion" required>
    Nuevo Género: <input type="text" name="genero" required>
    <input type="submit" name="update_libro" value="Modificar Libro">
</form>

<h3>Buscar Libro</h3>
<form method="post">
    Título: <input type="text" name="titulo" required>
    <input type="submit" name="search_libro" value="Buscar Libro">
</form>

<?php
// Mostrar tabla autores
mostrarTabla($conn, "autores");
?>

<h3>Añadir Autor</h3>
<form method="post">
    Nombre: <input type="text" name="nombre" required>
    <input type="submit" name="add_autor" value="Añadir Autor">
</form>

<h3>Eliminar Autor</h3>
<form method="post">
    ID del Autor: <input type="number" name="id_autor" required>
    <input type="submit" name="delete_autor" value="Eliminar Autor">
</form>

<h3>Modificar Autor</h3>
<form method="post">
    ID del Autor: <input type="number" name="id_autor" required>
    Nuevo Nombre: <input type="text" name="nombre" required>
    <input type="submit" name="update_autor" value="Modificar Autor">
</form>

<h3>Buscar Autor</h3>
<form method="post">
    Nombre: <input type="text" name="nombre" required>
    <input type="submit" name="search_autor" value="Buscar Autor">
</form>

<?php
// Cerrar conexión
$conn->close();
?>
