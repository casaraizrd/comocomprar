import { firebaseConfig } from "./firebase-config.js";
import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.5/firebase-app.js";
import { getFirestore, collection, addDoc, serverTimestamp } from "https://www.gstatic.com/firebasejs/10.12.5/firebase-firestore.js";

const app = initializeApp(firebaseConfig);
const db = getFirestore(app);

const form = document.getElementById("leadMagnetForm");

if (form) {
form.addEventListener("submit", async (e) => {
e.preventDefault();

```
const button = form.querySelector("button[type='submit']") || form.querySelector("button");
const originalText = button ? button.textContent : "";

try {
  if (button) {
    button.disabled = true;
    button.textContent = "Preparando acceso...";
  }

  const data = new FormData(form);

  const lead = {
    nombre: String(data.get("nombre") || "").trim(),
    whatsapp: String(data.get("whatsapp") || "").trim(),
    correo: String(data.get("correo") || "").trim(),
    estado: "Nuevo",
    origen: "Landing Curso Interactivo",
    recurso: "Curso interactivo HTML",
    notas: "",
    createdAt: serverTimestamp(),
    updatedAt: serverTimestamp()
  };

  if (!lead.nombre || !lead.whatsapp || !lead.correo) {
    alert("Por favor completa los campos obligatorios.");
    return;
  }

  await addDoc(collection(db, "leads"), lead);

  window.location.href = "curso_apartamento_rd.html";

} catch (error) {
  console.error(error);
  alert("No se pudo enviar la información. Revisa Firebase o intenta nuevamente.");
} finally {
  if (button) {
    button.disabled = false;
    button.textContent = originalText;
  }
}
```

});
}
