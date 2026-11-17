# Dokumentasi Hasil Analisa & Perbaikan Sistem Darut Tafsir

Dokumen ini merangkum hasil analisa menyeluruh dan perbaikan terhadap kode sumber (*codebase*) sistem **Pondok Pesantren Modern Darut Tafsir** berdasarkan laporan kendala/bug serta permintaan dari klien (**Bang Fauzan**) pada tanggal **28 Juli 2026** dan **5 Agustus 2026**.

---

## Ringkasan Status Perbaikan

| No | Tanggal Laporan | Deskripsi Laporan / Permintaan Klien | Status di Sistem | Keterangan Singkat |
| :-: | :--- | :--- | :---: | :--- |
| **1** | 28 Juli 2026 | Pilihan Surat tidak sesuai dengan Juz yang dipilih (misal pilih Juz 29 muncul surat Juz 30) | **SUDAH DIPERBAIKI** | Menggunakan pemetaan multi-juz (`VARCHAR`) dan query `FIND_IN_SET(?, juz)`. |
| **2** | 28 Juli 2026 | Tambah Pengguna baru (misal Role Orang Tua) otomatis terdaftar sebagai Role Guru | **SUDAH DIPERBAIKI** | Pembuatan data `Teacher` dibatasi hanya jika `role_id === 2` pada model `User::booted()`. |
| **3** | 28 Juli 2026 | Pengguna non-guru tetap muncul di menu *Asatidz*, dan jika dihapus dari *Asatidz* akun pengguna ikut terhapus | **SUDAH DIPERBAIKI** | Auto-sync hapus/buat record `Teacher` saat `role_id` berubah & aksi hapus pada `TeacherResource` dinonaktifkan (`canDelete: false`). |
| **4** | 28 Juli 2026 | Fitur kamera (Ambil Foto) tidak aktif saat ditekan oleh sebagian Asatidz | **SUDAH DITANGANI** | Menggunakan komponen `TakePicture` dengan pemilih kamera & pemaksaan skema `HTTPS` (syarat wajib WebRTC/kamera di browser modern). |
| **5** | 28 Juli 2026 | Website tidak bisa dibuka/tampil baik di perangkat iOS (Safari) | **SUDAH DIPERBAIKI** | Implementasi `URL::forceScheme('https')` di `AppServiceProvider` dan penggantian hero background CSS menjadi tag `<img>` standar WebKit dengan fallback. |
| **6** | 28 Juli 2026 | *(Tambahan)* Kuota guru untuk mentoring santri bisa diatur lebih dari 3 | **SUDAH DIPERBAIKI** | Kolom `student_quota` ditambahkan pada tabel `teachers` dan dapat diubah melalui modal **Edit Kuota** di menu *Asatidz*. |
| **7** | 28 Juli 2026 | *(Tambahan)* Link Google Drive/Form pendaftaran di Landing Page dibuat dinamis | **SUDAH DIPERBAIKI** | Field `registration_link` ditambahkan pada `ProfileResource` dan terhubung langsung ke tombol *Daftar Sekarang* di halaman utama. |
| **8** | 5 Agustus 2026 | Laman Perizinan terlihat di semua akun Wali Santri (data perizinan antar wali tercampur) | **SUDAH DIPERBAIKI** | Query `PermissionResource` pada panel Parent telah difilter dengan `->where('id_parent', auth()->id())`. |
| **9** | 5 Agustus 2026 | Pop-up `500 - Server Error` saat Admin ingin menambah Mentor Santri | **SUDAH DIPERBAIKI** | Query `havingRaw` yang memicu error SQL diganti menggunakan Eloquent collection `withCount('binaan')` dan `filter()` di `MentorStudentResource`. |
| **10** | 5 Agustus 2026 | Pop-up `FORBIDDEN` (403) saat Asatidz login ke portal, namun hilang setelah di-refresh | **SUDAH DIPERBAIKI** | Implementasi `CustomLoginResponse`, pembersihan `url.intended` lintas panel, type-casting `(int) $user->role_id`, dan auto-redirect sesuai role pada middleware panel. |
| **11** | 5 Agustus 2026 | Opsi *"Create & Kirim WA"* pada menu Kegiatan | **SESUAI KETENTUAN** | Pengiriman notifikasi WhatsApp kegiatan difokuskan khusus untuk **Wali Santri (`role_id = 3`)** yang memiliki nomor telepon terdaftar melalui `SendWaBroadcastJob`. |

---

## Detail Analisa Teknis per Poin

### Bagian A: Laporan 28 Juli 2026 & Fitur Tambahan

#### 1. Ketidaksesuaian Daftar Surah Berdasarkan Pilihan Juz
* **Masalah Awal:** Ketika memilih Juz (misal Juz 29), daftar surah yang muncul justru surah dari Juz 30 atau tidak akurat untuk surah yang melintasi lebih dari satu juz.
* **Implementasi Perbaikan:**
  * Migrasi `database/migrations/2026_07_28_223000_update_juz_in_surah_table.php` dan seeder `database/seeders/SurahJuzSeeder.php` mengubah tipe kolom `juz` pada tabel `surah` menjadi `VARCHAR(255)` yang menyimpan daftar juz terpisah koma (contoh: Al-Baqarah `1,2,3`, Juz 29 berisi surah ke-67 s.d. 77, dan Juz 30 berisi surah ke-78 s.d. 114).
  * Pada `app/Filament/Teacher/Resources/MemorizeResource.php` (baris 245–260), komponen `Select::make('juz')` bersifat `reactive()`, mereset `id_surah` ketika diubah, dan memfilter surah dengan query:
    ```php
    Surah::whereRaw('FIND_IN_SET(?, juz)', [$juz])
        ->orderBy('id')
        ->pluck('surah_name', 'id');
    ```
* **Status:** **Sudah diperbaiki dan berfungsi normal.**

#### 2 & 3. Bug Role Pengguna Baru Menjadi Guru & Sinkronisasi Data Asatidz
* **Masalah Awal:** Setiap membuat pengguna baru (meskipun memilih role Orang Tua), pengguna tersebut terdaftar sebagai Guru, masuk ke menu *Manajemen Operasional > Asatidz*, tetap bertahan di daftar Asatidz walau role diubah, serta menghapus akun utama jika dihapus dari menu Asatidz.
* **Implementasi Perbaikan:**
  * Pada `app/Filament/Resources/UserResource/Pages/CreateUser.php`, logika pembuatan manual record `Teacher` telah dibersihkan dan dipindahkan ke model event `User::booted()` di `app/Models/User.php`:
    ```php
    static::saved(function ($user) {
        if ((int) $user->role_id === 2) {
            if (!$user->teacher()->exists()) {
                Teacher::create([
                    'id_users' => $user->id,
                    'student_quota' => 3,
                ]);
            }
        } else {
            if ($user->teacher()->exists()) {
                $user->teacher()->delete();
            }
        }
    });
    ```
  * Pada `app/Filament/Resources/TeacherResource.php` (baris 42–50), fungsi hapus telah dikunci (`canDelete()` dan `canDeleteAny()` mengembalikan `false`), sehingga Admin tidak lagi bisa menghapus data dari tabel Asatidz secara tidak sengaja.
  * Ditambahkan pula tab filter per Role pada `app/Filament/Resources/UserResource/Pages/ListUsers.php` untuk memudahkan pemantauan pengguna berdasarkan role.
* **Status:** **Sudah diperbaiki secara menyeluruh.**

#### 4 & 5. Kendala Kamera Asatidz & Kompatibilitas iOS (Safari)
* **Masalah Awal:** Kamera tidak aktif saat tombol foto ditekan, dan beberapa pengguna iOS tidak dapat membuka website.
* **Implementasi Perbaikan:**
  * **Paksaan Skema HTTPS:** Browser modern (terutama Safari di iOS dan Chrome Mobile) memblokir akses `navigator.mediaDevices.getUserMedia` (kamera & mikrofon) jika halaman diakses melalui protokol HTTP tidak aman. Pada `app/Providers/AppServiceProvider.php`, telah ditambahkan `URL::forceScheme('https')` untuk domain produksi (`santriqu.id`, `herd.network`, `production`, dan header `x-forwarded-proto`).
  * **Kompatibilitas Hero Landing Page di iOS WebKit:** Pada `resources/views/index.blade.php` (baris 85–100), pemuatan gambar banner telah diubah menggunakan elemen `<img loading="eager" onerror="...">` standar dengan fallback otomatis ke `/assets/admin/banner.jpg`.
* **Status:** **Sudah ditangani dengan baik.**

#### 6. Penambahan Fitur Kuota Bimbingan Guru (> 3 Santri)
* **Implementasi Perbaikan:**
  * Pada `app/Filament/Resources/TeacherResource.php`, tersedia form input `student_quota` (*Batas Santri Binaan*, default `3`, minimal `1`) yang dapat diedit langsung melalui tombol modal **Edit Kuota** pada tabel Asatidz.
* **Status:** **Sudah berjalan sesuai spesifikasi.**

#### 7. Link Pendaftaran Dinamis di Landing Page
* **Implementasi Perbaikan:**
  * Migrasi penambahan kolom `registration_link` telah diterapkan pada tabel `profiles`.
  * Field `TextInput::make('registration_link')` telah tersedia di `app/Filament/Resources/ProfileResource.php` dan ditampilkan pada tombol **Daftar Sekarang** di `resources/views/index.blade.php`.
* **Status:** **Sudah berjalan sesuai spesifikasi.**

---

### Bagian B: Laporan 5 Agustus 2026

#### 8. Laman Perizinan Terlihat di Semua Akun Wali Santri
* **Masalah Awal:** Wali santri dapat melihat daftar perizinan milik wali santri lainnya.
* **Implementasi Perbaikan:**
  * Pada `app/Filament/Parent/Resources/PermissionResource.php`, method `getEloquentQuery()` telah ditambahkan filter kepemilikan:
    ```php
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('id_parent', auth()->id())
            ->with(['student', 'parent']);
    }
    ```
  * Pilihan dropdown santri saat membuat izin baru hanya menampilkan santri yang berelasi dengan akun wali yang sedang login (`Student::where('parent', $user->id)`), dan `CreatePermission.php` telah disesuaikan agar tidak menimpa pilihan santri jika wali memiliki lebih dari satu anak di pondok.
* **Status:** **Sudah diperbaiki dengan sempurna.**

#### 9. Error `500 - Server Error` Saat Admin Menambah Mentor Santri
* **Masalah Awal:** Ketika Admin membuka form atau memilih pembimbing di menu *Mentor Santri*, muncul pop-up `500 - Server Error`.
* **Implementasi Perbaikan:**
  * Sebelumnya, `Select::make('id_teacher')` menggunakan `->relationship(...)` dengan klausa SQL `->havingRaw('binaan_count < teachers.student_quota')` yang memicu `QueryException` (SQL 500 error).
  * Pada `app/Filament/Resources/MentorStudentResource.php`, logika tersebut telah diganti menggunakan closure `options()` berbasis koleksi Eloquent yang aman:
    ```php
    Select::make('id_teacher')
        ->label('Nama Pembimbing')
        ->options(function () {
            return Teacher::with('user')
                ->whereHas('user', fn($q) => $q->where('role_id', 2))
                ->withCount('binaan')
                ->get()
                ->filter(fn($teacher) => $teacher->binaan_count < $teacher->student_quota)
                ->pluck('user.name', 'id');
        })
    ```
* **Status:** **Sudah diperbaiki dan bebas dari error 500.**

#### 10. Pop-Up `"FORBIDDEN"` Saat Asatidz Login ke Portal
* **Masalah Awal:** Beberapa user Asatidz ketika login ke portal mendapati pop-up `"FORBIDDEN"`, namun setelah di-refresh halaman masuk secara normal.
* **Akar Masalah:**
  1. Saat pengguna belum login atau sesi habis di URL panel lain (misalnya `/admin`), Laravel menyimpan URL tersebut di session `url.intended`. Ketika Asatidz (`role_id = 2`) login melalui `/auth/login`, mekanisme bawaan Filament memanggil `redirect()->intended()` yang mengarahkan request Livewire ke `/admin` dan langsung ditolak dengan `abort(403, 'Unauthorized')` oleh middleware `OnlyAdmin`.
  2. Pengecekan pada middleware `OnlyTeacher`, `OnlyParent`, dan `OnlyKeamanan` sebelumnya menggunakan perbandingan ketat (`!== 2`) tanpa casting `(int)`, yang rentan gagal jika nilai `role_id` dari database terbaca sebagai tipe string.
* **Implementasi Perbaikan:**
  * Dibuat class `app/Http/Responses/CustomLoginResponse.php` dan didaftarkan sebagai singleton untuk kontrak `Filament\Http\Responses\Auth\Contracts\LoginResponse` di `app/Providers/AppServiceProvider.php`. Saat login berhasil, session `url.intended` langsung dibersihkan dan pengguna diarahkan secara eksplisit ke dashboard panel sesuai `role_id`-nya.
  * Pada `app/Http/Responses/CustomLogoutResponse.php`, session `url.intended` juga dibersihkan saat pengguna logout.
  * Pada `app/Models/User.php`, method `canAccessPanel(Panel $panel)` dilengkapi untuk seluruh panel (`admin`, `teacher`, `parent`, `keamanan`, `auth`) dengan casting `(int) $this->role_id`.
  * Pada middleware `OnlyAdmin`, `OnlyTeacher`, `OnlyParent`, `OnlyKeamanan`, dan `RedirectByRole`, seluruh pengecekan `role_id` telah distandarisasi menggunakan `(int)` dan mengarahkan pengguna ke dashboard panel yang sesuai dengan rolenya alih-alih melempar error `403 Forbidden`.
* **Status:** **Sudah diperbaiki secara tuntas.**

#### 11. Opsi *"Create & Kirim WA"* pada Menu Kegiatan
* **Ketentuan Sistem:**
  * Fitur **Create & Kirim WA** pada `app/Filament/Resources/ActivityResource/Pages/CreateActivity.php` dan background job `app/Jobs/SendWaBroadcastJob.php` dirancang khusus untuk mengirimkan pengumuman kegiatan pondok kepada seluruh akun **Wali Santri (`role_id = 3`)** yang telah memiliki nomor WhatsApp terdaftar.
  * Pengiriman dilakukan secara asynchronous di latar belakang (*queue job*) dilengkapi jeda waktu acak (*random delay* 10–25 detik) dan kode unik (*anti-spam suffix*) melalui `FonnteService`.
* **Status:** **Berjalan sesuai ketentuan.**
