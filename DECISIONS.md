# Architectural Decision Records (ADR)

Dokumen ini mencatat keputusan-keputusan krusial, latar belakang masalah, opsi alternatif, serta konsekuensi teknis yang diambil selama pengembangan sistem manajemen pesanan.

## ADR-001: Penerapan Pessimistic Locking untuk Penanganan Stok Konkuren

*   **Status:** Disetujui (Approved)
*   **Konteks:** Sistem diakses oleh banyak kasir secara bersamaan (*high concurrency*). Penggunaan *Optimistic Locking* berisiko tinggi menghasilkan banyak kegagalan transaksi (*abort/retry*) pada produk populer (*hot products*).
*   **Keputusan:** Kami memilih menggunakan **Pessimistic Row Locking (`lockForUpdate()`)** yang dibungkus di dalam blok transaksi `DB::transaction()` pada `StockService.php` / `OrderService.php`. ID produk wajib diurutkan sebelum dikunci untuk mengeliminasi risiko *deadlock*.
*   **Konsekuensi:** 
    *   *Positif:* Konsistensi data stok terjamin 100% akurat langsung di tingkat database; tidak akan pernah terjadi stok bernilai negatif.
    *   *Negatif:* Menimbulkan antrean latensi kecil di database PostgreSQL jika ribuan pengguna membeli item yang sama secara bersamaan.

---

## ADR-002: Transisi Arsitektur Autentikasi untuk Lingkungan Produksi

*   **Status:** Disetujui (Approved)
*   **Konteks:** Struktur awal menggunakan autentikasi *Laravel Sanctum Bearer Token* yang disimpan di *Local Storage* atau memori frontend Nuxt. Mekanisme ini rentan terhadap eksploitasi keamanan Cross-Site Scripting (XSS) di lingkungan produksi.
*   **Keputusan:** Mengubah mekanisme autentikasi pada deployment produksi berskala besar menggunakan arsitektur **HttpOnly, Secure, dan SameSite=Lax Cookie Sessions** melalui fitur bawaan *Sanctum SPA Authentication*.
*   **Konsekuensi:**
    *   *Positif:* Token dan sesi tidak dapat dibaca atau dicuri oleh skrip JavaScript pihak ketiga yang berbahaya (Aman dari serangan XSS).
    *   *Negatif:* Memerlukan konfigurasi *Stateful Domains* (`SANCTUM_STATEFUL_DOMAINS`) dan manajemen CORS yang sangat ketat antara domain asal frontend Nuxt dan backend Laravel API.

---

## ADR-003: Isolasi Ekstensi Runtime Redis pada Docker Container

*   **Status:** Disetujui (Approved)
*   **Konteks:** Terjadi *bug startup* nyata pada lingkungan container di mana worker antrean asinkron gagal diinisialisasi karena runtime PHP tidak mendeteksi ekstensi library Redis (`Warning: PHP Startup: Unable to load dynamic library 'redis'`).
*   **Keputusan:** Mewajibkan proses kompilasi dan aktivasi ekstensi biner Redis secara eksplisit pada berkas konfigurasi `Dockerfile` backend sebelum aplikasi Laravel di-deploy.
*   **Konsekuensi:**
    *   *Positif:* Menjamin ketersediaan driver Redis untuk menangani `GenerateInvoiceJob` dan sistem *cache* aplikasi secara andal sejak container pertama kali dinyalakan.
    *   *Negatif:* Sedikit meningkatkan waktu kompilasi awal citra kontainer (*container image build time*) karena harus mengunduh `$PHPIZE_DEPS` dan dependensi lingkungan Alpine.

---

## ADR-004: Mekanisme Kompensasi dan Kebijakan Toleransi Kegagalan Kerja Asinkron

*   **Status:** Disetujui (Approved)
*   **Konteks:** Kegagalan infrastruktur eksternal (seperti server SMTP email mati atau kehabisan memori saat cetak HTML) tidak boleh membatalkan atau mengubah status transaksi pesanan yang sudah berhasil dikomit di database utama.
*   **Keputusan:** Mengisolasi proses pencetakan faktur dan notifikasi ke dalam kelas Job asinkron (`GenerateInvoiceJob.php`) dengan menerapkan kebijakan *retry otomatis* (`$tries = 3`) menggunakan *Exponential Backoff*. Jika gagal total setelah batas percobaan, data akan dialihkan ke tabel `failed_jobs` untuk diaudit secara manual oleh tim IT tanpa memicu kegagalan sistem pada sisi pengguna.
*   **Konsekuensi:**
    *   *Positif:* Siklus transaksi utama pesanan kasir tetap berjalan instan dan responsif.
    *   *Negatif:* Logika fungsi di dalam `InvoiceService.php` wajib ditulis secara *idempotent* (aman dieksekusi berulang kali dengan parameter data yang sama tanpa menghasilkan penggandaan dokumen finansial).
