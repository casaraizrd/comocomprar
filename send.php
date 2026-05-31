<?php
// Landing Casa Raiz RD - Captura de leads para guía gratuita
// Sube este archivo junto a index.html, gracias.html y el PDF en el mismo directorio.

// ========================
// CONFIGURACIÓN BÁSICA
// ========================
$admin_email = "ivanalberto@casaraizrd.com"; // Cambia si deseas recibir los leads en otro correo.
$from_email  = "ivanalberto@casaraizrd.com"; // Debe ser un correo del dominio para mejor entrega en Hostinger.
$redirect_ok = "gracias.html";
$leads_file  = __DIR__ . "/leads-guia.csv";

// Opcional: si luego quieres conectar directo a tu CRM por API, coloca aquí el endpoint.
// Por ahora está vacío para evitar errores.
$crm_endpoint = "";

// ========================
// SOLO ACEPTAR POST
// ========================
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.html");
    exit;
}

// ========================
// FUNCIÓN DE LIMPIEZA
// ========================
function clean_input($value) {
    $value = trim($value ?? "");
    $value = strip_tags($value);
    $value = str_replace(["\r", "\n"], " ", $value);
    return $value;
}

$nombre      = clean_input($_POST["nombre"] ?? "");
$whatsapp    = clean_input($_POST["whatsapp"] ?? "");
$correo      = filter_var(trim($_POST["correo"] ?? ""), FILTER_SANITIZE_EMAIL);
$presupuesto = clean_input($_POST["presupuesto"] ?? "");
$zona        = clean_input($_POST["zona"] ?? "");
$origen      = clean_input($_POST["origen"] ?? "Landing Guía Financiamiento");
$fecha       = date("Y-m-d H:i:s");
$ip          = $_SERVER["REMOTE_ADDR"] ?? "";

// ========================
// VALIDACIÓN
// ========================
if ($nombre === "" || $whatsapp === "" || !filter_var($correo, FILTER_VALIDATE_EMAIL) || $presupuesto === "") {
    echo "<h2>Faltan datos obligatorios.</h2><p>Por favor vuelve atrás y completa el formulario correctamente.</p>";
    exit;
}

// ========================
// GUARDAR EN CSV
// ========================
$is_new_file = !file_exists($leads_file);

$fp = fopen($leads_file, "a");
if ($fp) {
    if ($is_new_file) {
        fputcsv($fp, ["fecha", "nombre", "whatsapp", "correo", "presupuesto", "zona", "origen", "ip"]);
    }

    fputcsv($fp, [$fecha, $nombre, $whatsapp, $correo, $presupuesto, $zona, $origen, $ip]);
    fclose($fp);
}

// ========================
// ENVIAR CORREO AL ADMIN
// ========================
$subject_admin = "Nuevo lead - Guía Comprar Apartamento RD";
$message_admin = "
Nuevo lead desde la landing de la guía:

Nombre: $nombre
WhatsApp: $whatsapp
Correo: $correo
Presupuesto: $presupuesto
Zona de interés: $zona
Origen: $origen
Fecha: $fecha
IP: $ip
";

$headers_admin  = "From: Casa Raiz RD <$from_email>\r\n";
$headers_admin .= "Reply-To: $correo\r\n";
$headers_admin .= "Content-Type: text/plain; charset=UTF-8\r\n";

@mail($admin_email, $subject_admin, $message_admin, $headers_admin);

// ========================
// ENVIAR CORREO AL LEAD
// ========================
$subject_user = "Tu guía gratis para comprar apartamento en RD";
$message_user = "Hola $nombre,

Gracias por solicitar la guía gratuita de Casa Raiz RD.

Puedes descargarla desde este enlace:
https://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["REQUEST_URI"]) . "/guia-comprar-apartamento-rd-2026.pdf

Si deseas asesoría personalizada, escríbenos por WhatsApp:
https://wa.me/18292786677

Casa Raiz RD
";

$headers_user  = "From: Casa Raiz RD <$from_email>\r\n";
$headers_user .= "Reply-To: $admin_email\r\n";
$headers_user .= "Content-Type: text/plain; charset=UTF-8\r\n";

@mail($correo, $subject_user, $message_user, $headers_user);

// ========================
// OPCIONAL: ENVIAR A CRM POR API
// ========================
if (!empty($crm_endpoint)) {
    $payload = [
        "nombre" => $nombre,
        "whatsapp" => $whatsapp,
        "correo" => $correo,
        "presupuesto" => $presupuesto,
        "zona" => $zona,
        "origen" => $origen,
        "categoria" => "Lead Magnet Financiamiento",
        "fecha" => $fecha
    ];

    $ch = curl_init($crm_endpoint);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 8);
    curl_exec($ch);
    curl_close($ch);
}

// ========================
// REDIRIGIR A DESCARGA
// ========================
header("Location: $redirect_ok");
exit;
?>
