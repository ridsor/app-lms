import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: ["public/assets/scss/style.scss", "resources/js/app.js", "resources/css/app.css"],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
