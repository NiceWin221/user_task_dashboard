import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    root: "public/frontend",
    build: {
        outDir: "../../dist", // output ke folder 'dist' di project root
        emptyOutDir: true,
    },
    server: {
        open: true, // buka browser otomatis saat dev server berjalan
        proxy: {
            "/api": "http://localhost:8000", // Jika kamu menggunakan backend di Laravel
        },
    },
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
        // tailwindcss(),
    ],
});
