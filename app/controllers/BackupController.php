<?php

class BackupController {

    public function descargar() {
        // 1. Inicialización y control de seguridad de la sesión
        if (session_status() == PHP_SESSION_NONE) session_start();
        
        // Candado estricto: Solo la dueña (ADMIN) puede descargar el respaldo de la empresa
        if (!isset($_SESSION['user_rol']) || strtoupper($_SESSION['user_rol']) !== 'ADMIN') {
            header("Location: index.php?route=dashboard&error=unauthorized");
            exit();
        }

        // 2. RUTA EXACTA REAL DETECTADA EN TU COMPUTADORA
        // Retrocedemos dos niveles desde app/controllers/ para llegar a la raíz del Lubricentro
        //$archivo_base_datos = realpath(__DIR__ . '/../../db_lubricentro.sqlite');

        // 3. Rescate por si XAMPP interpreta las barras inclinadas de forma absoluta en Windows
        if (!$archivo_base_datos || !file_exists($archivo_base_datos)) {
            $archivo_base_datos = 'C:/xampp/htdocs/Lubricentro/db_lubricentro.sqlite';
        }

        // 4. Verificación de seguridad por si el archivo es movido o renombrado
        if (!file_exists($archivo_base_datos)) {
            die("<div style='font-family:sans-serif; padding:40px; color:#991B1B; background:#FEE2E2; border-radius:10px;'>
                    <h2>❌ Error del Sistema de Copias</h2>
                    <p>No se encontró físicamente el archivo de datos <code>ldb_lubricentro.sqlite</code> en la raíz del proyecto.</p>
                    <p>Ruta buscada en tu disco duro: <code>".htmlspecialchars($archivo_base_datos)."</code></p>
                    <a href='index.php?route=dashboard' style='color:#2563EB; font-weight:bold; text-decoration:none;'><i class='bi bi-arrow-left'></i> Volver al Panel</a>
                 </div>");
        }

        // 5. Nombre de descarga limpio, elegante y fechado al segundo para el resguardo de la dueña
        $nombre_descarga = "Backup_IsmarERP_" . date('d-m-Y_H-i-s') . ".sqlite";

        // 6. Configuración de cabeceras HTTP nativas para transferencia de archivos binarios en Brave
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $nombre_descarga . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($archivo_base_datos));
        
        // Limpiamos los búferes de salida de Apache para evitar corrupción de datos o archivos vacíos
        if (ob_get_length()) ob_end_clean();
        
        // 7. Leemos el archivo físico de SQLite y lo empujamos directo a las descargas de Brave
        readfile($archivo_base_datos);
        exit();

        if (!file_exists(__DIR__ . '/../../data/db_lubricentro.sqlite')) {
            // Estamos en producción (Render/Supabase)
            // Redirigir o mostrar mensaje amigable
            $_SESSION['mensaje'] = "Backup solo disponible en entorno local";
            header("Location: /dashboard");
            exit;
        }

    }
}