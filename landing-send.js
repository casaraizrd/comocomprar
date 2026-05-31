import { firebaseConfig } from "./firebase-config.js";
import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.5/firebase-app.js";
import { getFirestore, collection, addDoc, serverTimestamp } from "https://www.gstatic.com/firebasejs/10.12.5/firebase-firestore.js";
const app = initializeApp(firebaseConfig);
const db = getFirestore(app);
const form = document.getElementById("leadMagnetForm");
form.addEventListener("submit", async (e)=>{
e.preventDefault();
const data = new FormData(form);
await addDoc(collection(db,"leads"),{
nombre:data.get("nombre"),
whatsapp:data.get("whatsapp"),
correo:data.get("correo"),
presupuesto:data.get("presupuesto"),
estado:"Nuevo",
createdAt:serverTimestamp()
});
window.location.href="gracias.html";
});