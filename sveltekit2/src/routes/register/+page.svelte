<script lang="ts">
	// Mengimpor fungsi `goto` untuk navigasi antar halaman dan `login` untuk menyimpan token setelah registrasi
	import { goto } from '$app/navigation';
	import { login } from '$lib/stores/authStore';

	// Mengambil URL dasar API dari environment variables
	const apiBaseURL = import.meta.env.VITE_API_BASE_URL;

	// Mendeklarasikan variabel untuk menyimpan input user dan pesan error
	let name = '';
	let email = '';
	let password = '';
	let password_confirmation = '';
	let errorMessage = '';
	let errors = { name: '', email: '', password: '', password_confirmation: '' };
	let loading = false;

	// Fungsi untuk menangani registrasi user
	async function registerUser(event: Event) {
		event.preventDefault(); // Mencegah halaman untuk reload saat form disubmit
		loading = true; // Menandakan bahwa proses registrasi sedang berjalan
		errorMessage = ''; // Menghapus pesan error sebelumnya
		errors = { name: '', email: '', password: '', password_confirmation: '' }; // Menghapus error sebelumnya

		// Membuat payload untuk dikirim ke server
		const payload = { name, email, password, password_confirmation };

		try {
			// Mengirim request registrasi ke API
			const response = await fetch(`${apiBaseURL}/api/register`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json' // Mengirim data dalam format JSON
				},
				body: JSON.stringify(payload) // Mengirimkan data registrasi dalam body request
			});

			// Mengambil data dari response API
			const data = await response.json();

			// Mengecek apakah response berhasil atau tidak
			if (!response.ok) {
				// Jika ada error validasi dari server, tampilkan pesan error pada masing-masing field
				if (data.errors) {
					errors.name = data.errors.name ? data.errors.name[0] : ''; // Menampilkan error name
					errors.email = data.errors.email ? data.errors.email[0] : ''; // Menampilkan error email
					errors.password = data.errors.password ? data.errors.password[0] : ''; // Menampilkan error password
					errors.password_confirmation = data.errors.password_confirmation
						? data.errors.password_confirmation[0]
						: ''; // Menampilkan error password_confirmation
				} else {
					// Jika tidak ada error spesifik, tampilkan error umum
					errorMessage = data.message || 'Registration failed, please try again.';
				}
				return; // Keluar dari fungsi jika ada error
			}

			// Jika registrasi berhasil, simpan token akses dan arahkan user ke halaman dashboard
			login(data.access_token);
			goto('/dashboard'); // Navigasi ke halaman dashboard
		} catch (error) {
			// Jika terjadi error lain (misalnya masalah jaringan), tampilkan pesan error
			errorMessage = 'Something went wrong, please try again later.';
		} finally {
			// Menandakan bahwa proses registrasi telah selesai
			loading = false;
		}
	}
</script>

<section>
	<div class="mx-auto max-w-lg pb-2 pt-6">
		<h3 class="mb-4 text-center text-xl font-bold text-gray-800">Register</h3>

		{#if errorMessage}
			<p class="mb-4 text-center text-red-500">{errorMessage}</p>
		{/if}

		<form on:submit={registerUser}>
			<div class="mb-3 flex flex-col gap-2">
				<label for="name">Name</label>
				<input
					type="text"
					name="name"
					id="name"
					bind:value={name}
					class="rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-sky-500"
				/>
				{#if errors.name}
					<p class="text-sm text-red-500">{errors.name}</p>
				{/if}
			</div>
			<div class="mb-3 flex flex-col gap-2">
				<label for="email">Email</label>
				<input
					type="email"
					name="email"
					id="email"
					bind:value={email}
					class="rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-sky-500"
				/>
				{#if errors.email}
					<p class="text-sm text-red-500">{errors.email}</p>
				{/if}
			</div>
			<div class="mb-3 flex flex-col gap-2">
				<label for="password">Password</label>
				<input
					type="password"
					name="password"
					id="password"
					bind:value={password}
					class="rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-sky-500"
				/>
				{#if errors.password}
					<p class="text-sm text-red-500">{errors.password}</p>
				{/if}
			</div>
			<div class="mb-3 flex flex-col gap-2">
				<label for="password_confirmation">Confirm Password</label>
				<input
					type="password"
					name="password_confirmation"
					id="password_confirmation"
					bind:value={password_confirmation}
					class="rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-sky-500"
				/>
				{#if errors.password_confirmation}
					<p class="text-sm text-red-500">{errors.password_confirmation}</p>
				{/if}
			</div>
			<div class="mt-6">
				<button
					type="submit"
					disabled={loading}
					class="cursor-pointer rounded-lg bg-blue-500 px-4 py-2 text-white hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
				>
					{#if loading}
						Loading...
					{:else}
						Register
					{/if}
				</button>
			</div>
		</form>
	</div>
</section>
