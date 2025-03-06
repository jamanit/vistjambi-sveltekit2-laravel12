<script lang="ts">
	// Mengimpor fungsi onMount dari Svelte untuk menjalankan kode setelah komponen dipasang (mounted)
	import { onMount } from 'svelte';

	// Mengimpor komponen Breadcrumb untuk digunakan dalam template
	import Breadcrumb from '$lib/components/Breadcrumb.svelte';

	// Interface untuk tipe data user (name dan email)
	interface User {
		name: string;
		email: string;
	}

	// Variabel untuk menyimpan breadcrumb yang akan ditampilkan di halaman
	let breadcrumbItems = [
		{ name: 'Home', url: '/' },
		{ name: 'Me', url: '', isActive: true }
	];

	// Mendapatkan URL dasar API dari environment variables
	const apiBaseURL = import.meta.env.VITE_API_BASE_URL;

	// Variabel untuk menyimpan data user dan pesan error
	let user: User | null = null;
	let errorMessage = '';

	// Fungsi yang dijalankan setelah komponen dipasang (mounted)
	onMount(async () => {
		// Mengambil token dari localStorage untuk autentikasi
		const token = localStorage.getItem('token');

		// Jika tidak ada token, tampilkan pesan error bahwa user belum login
		if (!token) {
			errorMessage = 'You are not authenticated. Please log in.';
			return;
		}

		// Jika token ada, coba fetch data user dari API
		try {
			const response = await fetch(`${apiBaseURL}/api/me`, {
				method: 'GET',
				headers: {
					Authorization: `Bearer ${token}`, // Mengirim token untuk autentikasi
					'Content-Type': 'application/json' // Menyatakan bahwa data yang dikirimkan dalam format JSON
				}
			});

			// Mengambil data response dan mengubahnya menjadi JSON
			const data = await response.json();

			// Jika response tidak berhasil (misalnya token invalid), tampilkan pesan error
			if (!response.ok) {
				errorMessage = 'Failed to fetch user data.';
				return;
			}

			// Jika berhasil, simpan data user ke dalam variabel user
			user = data;
		} catch (error) {
			// Jika terjadi error lainnya (misalnya masalah jaringan), tampilkan pesan error umum
			errorMessage = 'Something went wrong, please try again later.';
		}
	});
</script>

<section>
	<Breadcrumb {breadcrumbItems} />

	<div class="pb-2 pt-6">
		{#if errorMessage}
			<p class="text-center text-red-500">{errorMessage}</p>
		{/if}

		{#if user}
			<p><strong>Name:</strong> {user.name}</p>
			<p><strong>Email:</strong> {user.email}</p>
		{:else}
			<p class="text-center">Loading...</p>
		{/if}
	</div>
</section>
