<script lang="ts">
	// Mengimpor fungsi `goto` dari SvelteKit untuk navigasi antar halaman
	import { goto } from '$app/navigation';

	// Mengimpor store `isLoggedIn` dan fungsi `logout` dari file `authStore`
	import { isLoggedIn, logout } from '$lib/stores/authStore';

	// Mendapatkan nama aplikasi dari environment variables
	const appName = import.meta.env.VITE_APP_NAME;

	// Fungsi yang menangani proses logout
	const handleLogout = () => {
		logout(); // Memanggil fungsi logout yang menghapus token dan mengubah status login
		goto('/login'); // Setelah logout, arahkan user ke halaman login
	};
</script>

<nav class="flex items-center bg-purple-800 px-4 py-4 text-white md:px-24">
	<h1 class="text-xl font-bold">{appName}</h1>
	<div class="ml-auto">
		<a href="/" class="mr-4">Home</a>
		<a href="/about" class="mr-4" data-sveltekit-prefetch>About</a>

		{#if $isLoggedIn}
			<a href="/dashboard" class="mr-4" data-sveltekit-prefetch>Dashboard</a>
			<a href="/me" class="mr-4" data-sveltekit-prefetch>Me</a>
			<a href="/users" class="mr-4" data-sveltekit-prefetch>Users</a>
			<button
				class="cursor-pointer rounded-lg bg-red-500 px-4 py-2 text-white hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500"
				on:click={handleLogout}
			>
				Logout
			</button>
		{:else}
			<a href="/login" class="mr-4" data-sveltekit-prefetch>Login</a>
			<a href="/register" class="mr-4" data-sveltekit-prefetch>Register</a>
		{/if}
	</div>
</nav>
