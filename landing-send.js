import { firebaseConfig } from "./firebase-config.js";
import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.5/firebase-app.js";
import { getFirestore, collection, addDoc, serverTimestamp } from "https://www.gstatic.com/firebasejs/10.12.5/firebase-firestore.js";

const app = initializeApp(firebaseConfig);
const db = getFirestore(app);

const form = document.getElementById("leadMagnetForm");

if (form) {
  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const button = form.querySelector("button[type='submit']");
    const originalText = button.textContent;

    try {
      button.disabled = true;
      button.textContent = "Enviando...";

      const data = new FormData(form);

      const lead = {
        nombre: String(data.get("nombre") || "").trim(),
        whatsapp: String(data.get("whatsapp") || "").trim(),
        correo: String(data.get("correo") || "").trim(),
        presupuesto: String(data.get("presupuesto") || "").trim(),
        zona: String(data.get("zona") || "").trim(),
        estado: "Nuevo",
        origen: "Landing Guía Financiamiento",
        notas: "",
        createdAt: serverTimestamp(),
        updatedAt: serverTimestamp()
      };

      if (!lead.nombre || !lead.whatsapp || !lead.correo || !lead.presupuesto) {
        alert("Por favor completa los campos obligatorios.");
        return;
      }

      await addDoc(collection(db, "leads"), lead);

      window.location.href = "gracias.html";

    } catch (error) {
      console.error(error);
      alert("No se pudo enviar la información. Revisa Firestore y las reglas de Firebase.");
    } finally {
      button.disabled = false;
      button.textContent = originalText;
    }
  });
}
