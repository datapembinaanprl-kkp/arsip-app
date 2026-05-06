// ecosystem.config.js

module.exports = {
  apps: [
    {
      name: 'cbt-app',
      script: './src/app.js',

      // Jumlah instance — 'max' untuk pakai semua core CPU
      // Untuk server 2 vCPU: instances: 2
      instances: 2,
      exec_mode: 'cluster',    // Load balancing otomatis antar instances

      // Environment variables per mode
      env: {
        NODE_ENV: 'development',
        PORT: 3000,
      },
      env_production: {
        NODE_ENV: 'production',
        PORT: 3000,
      },

      // Restart policy
      max_memory_restart: '500M',   // Restart kalau pakai RAM > 500MB
      restart_delay: 4000,          // Tunggu 4 detik sebelum restart
      max_restarts: 10,             // Maksimal 10 restart — setelah itu stop

      // Log configuration
      error_file: '/var/log/cbt/error.log',
      out_file: '/var/log/cbt/out.log',
      log_date_format: 'YYYY-MM-DD HH:mm:ss Z',
      merge_logs: true,             // Gabungkan log dari semua instances

      // Watch file changes (untuk development, matikan di production)
      watch: false,
    }
  ]
};
