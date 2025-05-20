import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'

export default defineConfig({
  plugins: [vue()],
  build: {
    outDir: '../public/',
    emptyOutDir: true,
    rollupOptions: {
      input: './index.html'
    }
  },
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
    }
  },
  server: {
    proxy: {
      '/album/api': 'http://localhost:8080'  // Point to Laminas dev server if needed
    }
  }
})
