<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Lubricentro Ismar ERP</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #F5F7FA; min-height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; }
        .login-wrapper { background-color: #FFFFFF; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05); max-width: 1050px; width: 100%; min-height: 600px; }
        .brand-side { background: linear-gradient(135deg, rgba(30, 41, 59, 0.95), rgba(15, 23, 42, 0.98)), url('https://images.unsplash.com/photo-1486006920555-c77dce18193b?q=80&w=1000&auto=format&fit=crop') no-repeat center center; background-size: cover; display: flex; flex-direction: column; justify-content: space-between; padding: 50px; color: #FFFFFF; }
        .brand-main h1 { font-weight: 700; font-size: 36px; margin-bottom: 10px; }
        .brand-main p { color: #94A3B8; font-size: 15px; font-weight: 300; }
        .form-side { padding: 60px; display: flex; flex-direction: column; justify-content: space-between; }
        .form-header h2 { color: #0F172A; font-weight: 600; font-size: 26px; margin-bottom: 8px; }
        .form-header p { color: #64748B; font-size: 14px; }
        .form-label { color: #0F172A; font-weight: 500; font-size: 13px; }
        .input-group-text { background-color: #F8FAFC; border-color: #E2E8F0; color: #64748B; border-radius: 10px 0 0 10px; }
        .form-control { border-color: #E2E8F0; color: #0F172A; font-size: 14px; height: 46px; }
        .form-control:focus { border-color: #2563EB; box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1); }
        .btn-login { background-color: #2563EB; border-color: #2563EB; color: #FFFFFF; font-weight: 600; height: 46px; border-radius: 10px; font-size: 15px; transition: all 0.2s; }
        .btn-login:hover { background-color: #1D4ED8; transform: translateY(-1px); }
        .footer-text { color: #94A3B8; font-size: 12px; }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center py-5">
    <div class="row login-wrapper g-0">
        
        <div class="col-12 col-md-5 brand-side d-none d-md-flex">
            <div class="brand-logo d-flex align-items-center gap-2">
                <div class="bg-white d-flex align-items-center justify-content-center rounded-3" style="width: 45px; height: 45px;">
                    <i class="bi bi-gear-fill text-primary fs-5"></i>
                </div>
                <span class="fw-bold tracking-wider fs-5" style="color: #FFFFFF;">ISMAR ERP</span>
            </div>
            
            <div class="brand-main">
                <h1>Lubricentro Ismar</h1>
                <p>Sistema Integral de Gestión Empresarial. Control de inventarios, facturación SUNAT, flujo de caja y fidelización por tiempo.</p>
            </div>
            
            <div class="small text-white-50">
                <i class="bi bi-shield-check me-1 text-primary"></i> Conexión Segura Local
            </div>
        </div>

        <div class="col-12 col-md-7 form-side bg-white">
            <div class="form-header mb-4">
                <h2>¡Bienvenido al Sistema!</h2>
                <p>Ingresa tus credenciales para acceder al panel de control claro.</p>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger border-0 d-flex align-items-center gap-2 small py-2 mb-3" style="background-color: #FEE2E2; color: #991B1B; border-radius: 10px;">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div>Usuario o contraseña incorrectos. Intente de nuevo.</div>
                </div>
            <?php endif; ?>

            <form action="index.php?route=procesar_login" method="POST">
                
                <div class="mb-3">
                    <label for="usuario" class="form-label">Nombre de Usuario / Correo</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-circle"></i></span>
                        <input type="text" class="form-control" style="border-radius: 0 10px 10px 0;" id="usuario" name="usuario" placeholder="Ej: admin o correo" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Contraseña de Acceso</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" style="border-radius: 0;" required>
                        <button class="btn btn-outline-secondary" type="button" id="btnToggle" style="border-color: #E2E8F0; border-radius: 0 10px 10px 0; background-color: #F8FAFC;">
                            <i class="bi bi-eye-slash" id="iconoOjo"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-login w-100 shadow-sm mb-3">
                    <i class="bi bi-box-arrow-in-right me-2"></i> Iniciar Sesión Gerencial
                </button>
            </form>

            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top" style="border-color: #F1F5F9 !important;">
                <span class="footer-text">© <?php echo date('Y'); ?> Lubricentro Ismar.</span>
                <span class="badge bg-light text-secondary border fw-medium px-2 py-1" style="font-size: 11px;">v2.4.0</span>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const btnToggle = document.getElementById("btnToggle");
    const passwordInput = document.getElementById("password");
    const iconoOjo = document.getElementById("iconoOjo");

    if(btnToggle && passwordInput && iconoOjo) {
        btnToggle.addEventListener("click", function() {
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                iconoOjo.classList.remove("bi-eye-slash");
                iconoOjo.classList.add("bi-eye");
            } else {
                passwordInput.type = "password";
                iconoOjo.classList.remove("bi-eye");
                iconoOjo.classList.add("bi-eye-slash");
            }
        });
    }
});
</script>

</body>
</html>