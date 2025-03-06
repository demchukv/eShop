import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import react from "@vitejs/plugin-react";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.jsx",
                "frontend/elegant/css/plugins.css",
                "frontend/elegant/css/vendor/photoswipe.min.css",
                "frontend/elegant/css/bootstrap-table.min.css",
                "frontend/elegant/css/style.css",
                "frontend/elegant/css/theme.min.css",
                "frontend/elegant/css/star-rating.css",
                "frontend/elegant/css/star-rating.min.css",
                "frontend/elegant/css/intlTelInput.css",
                "frontend/elegant/css/select2.min.css",
                "frontend/elegant/css/iziToast.css",
                "frontend/elegant/css/daterangepicker.css",
                "frontend/elegant/css/responsive.css",
                "frontend/elegant/css/shareon.min.css",
                "frontend/elegant/css/app.css",
                "frontend/elegant/js/firebase-app.js",
                "frontend/elegant/js/firebase-auth.js",
                "frontend/elegant/js/firebase-firestore.js",
                "frontend/elegant/js/bootstrap-table.min.js",
                "frontend/elegant/js/bootstrap-table-export.min.js",
                "frontend/elegant/js/main.js",
                "frontend/elegant/js/daterangepicker.js",
                "frontend/elegant/js/ionicons.js",
                "frontend/elegant/js/star-rating.js",
                "frontend/elegant/js/intlTelInput.js",
                "frontend/elegant/js/iziToast.min.js",
                "frontend/elegant/js/star-rating.min.js",
                "frontend/elegant/js/select2.min.js",
                "frontend/elegant/js/checkout.js",
                "frontend/elegant/js/wallet.js",
            ],
            refresh: true,
        }),
        react(),
    ],
    optimizeDeps: {
        exclude: ["js-big-decimal"],
    },
    server: {
        hmr: {
            host: "localhost",
        },
    },
});
