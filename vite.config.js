import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
  server: {
    host: '0.0.0.0',  // Permite acesso de qualquer rede
    port: 3000,        // Porta do Vite
    strictPort: true,  // Garante que o Vite vai usar a porta 3000 exatamente
    hmr: {
      host: '192.168.0.105', // IP da sua máquina
    },
  },
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
      refresh: true,
    }),
  ],
});
