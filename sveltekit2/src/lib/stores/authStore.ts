import { writable } from 'svelte/store'; // Mengimpor fungsi untuk membuat store (penyimpan data) di Svelte

let storedToken: string | null = null; // Menyimpan token, bisa berisi string atau null (kosong)

// Mengecek apakah kode ini dijalankan di browser (bukan server)
if (typeof window !== 'undefined') {
    storedToken = localStorage.getItem('token'); // Mengambil token yang disimpan di browser (localStorage)
}

export const isLoggedIn = writable(!!storedToken); // Membuat store `isLoggedIn`, berisi true jika ada token, false jika tidak ada

// Fungsi untuk login
export function login(token: string) {
    // Cek apakah kode dijalankan di browser
    if (typeof window !== 'undefined') {
        localStorage.setItem('token', token); // Menyimpan token di localStorage
    }
    isLoggedIn.set(true); // Mengubah status login menjadi true
}

// Fungsi untuk logout
export function logout() {
    // Cek apakah kode dijalankan di browser
    if (typeof window !== 'undefined') {
        localStorage.removeItem('token'); // Menghapus token dari localStorage
    }
    isLoggedIn.set(false); // Mengubah status login menjadi false
}
