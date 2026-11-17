<?php
// index.php - Formulario (izquierda) + Registros almacenados en MySQL (derecha)
require_once 'conexion.php';

$usuarios = $pdo->query(
    "SELECT id, nombre, email, password, fecha_nacimiento, rol
     FROM usuarios ORDER BY id ASC"
)->fetchAll();
$total = count($usuarios);

$mensajes = [
    'ok'        => ['ok',  '¡Usuario registrado correctamente en MySQL!'],
    'vacio'     => ['err', 'Todos los campos requeridos deben ser completados.'],
    'email'     => ['err', 'El correo electrónico no es válido.'],
    'duplicado' => ['err', 'El correo electrónico ya está registrado.'],
    'error'     => ['err', 'Ocurrió un error en la base de datos.'],
];
$msg = $mensajes[$_GET['msg'] ?? ''] ?? null;

function hash_corto(string $h): string {
    return strlen($h) > 20 ? substr($h, 0, 13) . ' ... ' . substr($h, -3) : $h;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuarios</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
<main class="pagina">
    <header class="encabezado">
        <span class="etiqueta">Ejemplo 1: Autenticación</span>
        <span class="bd">BD: sistema_usuarios_db</span>
        <h1>Registro de Usuarios</h1>
        <p>Formulario de registro con encriptación segura de contraseñas usando password_hash().</p>
    </header>

    <?php if ($msg): ?>
        <div class="aviso <?= $msg[0] ?>"><?= htmlspecialchars($msg[1]) ?></div>
    <?php endif; ?>

    <div class="columnas">
        <!-- IZQUIERDA: formulario -->
        <section class="tarjeta">
            <form action="insertar.php" method="post">
                <label for="nombre">Nombre Completo *</label>
                <input type="text" id="nombre" name="nombre" placeholder="Ej. Sofía Martínez" required>

                <label for="email">Correo Electrónico *</label>
                <input type="email" id="email" name="email" placeholder="sofia@ejemplo.com" required>

                <label for="password">Contraseña *</label>
                <input type="password" id="password" name="password" placeholder="••••••••" minlength="6" required>

                <div class="fila">
                    <div>
                        <label for="fecha_nac">F. Nacimiento</label>
                        <input type="date" id="fecha_nac" name="fecha_nac" required>
                    </div>
                    <div>
                        <label for="rol">Rol</label>
                        <select id="rol" name="rol">
                            <option value="Usuario">Usuario</option>
                            <option value="Administrador">Administrador</option>
                        </select>
                    </div>
                </div>

                <button type="submit">Registrar Usuario en MySQL</button>
            </form>
        </section>

        <!-- DERECHA: registros almacenados -->
        <section class="tarjeta">
            <div class="titulo-tabla">
                <h2>Registros almacenados en MySQL</h2>
                <span class="contador"><?= $total ?> registro(s)</span>
            </div>

            <div class="tabla-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Contraseña (Hash Bcrypt)</th>
                            <th>F. Nacimiento</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($total === 0): ?>
                        <tr><td colspan="5" class="vacio">Aún no hay usuarios registrados.</td></tr>
                    <?php else: foreach ($usuarios as $u): ?>
                        <tr>
                            <td class="id"><?= (int)$u['id'] ?></td>
                            <td><?= htmlspecialchars($u['nombre']) ?></td>
                            <td class="mono"><?= htmlspecialchars($u['email']) ?></td>
                            <td class="mono"><?= htmlspecialchars(hash_corto($u['password'])) ?></td>
                            <td class="mono"><?= htmlspecialchars($u['fecha_nacimiento']) ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>

            <p class="nota">Los datos enviados desde el formulario de la izquierda se procesan e insertan dinámicamente aquí.</p>
        </section>
    </div>
</main>
</body>
</html>
