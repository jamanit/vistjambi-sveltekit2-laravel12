<script lang="ts">
	// Mengimpor fungsi onMount dari Svelte untuk menjalankan kode setelah komponen dipasang (mounted)
	import { onMount } from 'svelte';
	// Mengimpor komponen Breadcrumb untuk digunakan dalam template
	import Breadcrumb from '$lib/components/Breadcrumb.svelte';
	import { goto } from '$app/navigation';

	// Variabel untuk menyimpan breadcrumb yang akan ditampilkan di halaman
	let breadcrumbItems = [
		{ name: 'Home', url: '/' },
		{ name: 'Users', url: '/users' },
		{ name: 'Create', url: '', isActive: true }
	];

	// Interface untuk tipe data user (name, email, password, password_confirmation)
	interface User {
		name: string;
		email: string;
		password: string;
		password_confirmation: string;
	}

	// Inisialisasi variabel untuk user baru
	let newUser: User = {
		name: '',
		email: '',
		password: '',
		password_confirmation: ''
	};

	// Variabel untuk menyimpan error dari API
	let errors: { [key: string]: string[] } = {};
	let message = '';
	let status = false;

	// Mendapatkan URL dasar API dari environment variables
	const apiBaseURL = import.meta.env.VITE_API_BASE_URL;

	// Fungsi untuk handle form submit dan create user
	async function createUser() {
		const token = localStorage.getItem('token');

		if (!token) {
			message = 'You are not authenticated. Please log in.';
			return;
		}

		try {
			const res = await fetch(apiBaseURL + `/api/users`, {
				method: 'POST',
				headers: {
					Authorization: `Bearer ${token}`,
					'Content-Type': 'application/json'
				},
				body: JSON.stringify(newUser)
			});

			const responseBody = await res.json();

			if (!res.ok) {
				// Jika API merespons gagal (HTTP 422 atau 500), tampilkan error
				if (res.status === 422) {
					// Menangani error validasi
					errors = responseBody.errors || {};
					message = responseBody.message || 'Validation failed.';
				} else {
					// Menangani error selain validasi
					message = responseBody.message || 'Something went wrong.';
				}
				throw new Error(message);
			}

			// Jika berhasil, tampilkan pesan sukses dan redirect ke halaman users
			status = responseBody.status || true;
			message = responseBody.message || 'User created successfully.';
			setTimeout(() => {
				goto('/users');
			}, 2000); // Redirect setelah 2 detik
		} catch (err) {
			console.error(err);
		}
	}
</script>

<section>
	<Breadcrumb {breadcrumbItems} />

	<div class="mx-auto max-w-lg pb-2 pt-6">
		{#if message}
			<div class="mb-4 rounded-lg {status ? 'bg-green-500' : 'bg-red-500'} p-4 text-white">
				{message}
			</div>
		{/if}

		<form on:submit|preventDefault={createUser} class="space-y-4">
			<div class="mb-3">
				<label class="form-label" for="name">Name</label>
				<input
					type="text"
					id="name"
					name="name"
					bind:value={newUser.name}
					placeholder="Enter name"
					class="form-input w-full rounded-md border border-gray-300 px-4 py-2"
				/>
				{#if errors.name}
					<p class="text-sm text-red-500">{errors.name[0]}</p>
				{/if}
			</div>

			<div class="mb-3">
				<label class="form-label" for="email">Email</label>
				<input
					type="email"
					id="email"
					name="email"
					bind:value={newUser.email}
					placeholder="Enter email"
					class="form-input w-full rounded-md border border-gray-300 px-4 py-2"
				/>
				{#if errors.email}
					<p class="text-sm text-red-500">{errors.email[0]}</p>
				{/if}
			</div>

			<div class="mb-3">
				<label class="form-label" for="password">Password</label>
				<input
					type="password"
					id="password"
					name="password"
					bind:value={newUser.password}
					placeholder="Enter password"
					class="form-input w-full rounded-md border border-gray-300 px-4 py-2"
				/>
				{#if errors.password}
					<p class="text-sm text-red-500">{errors.password[0]}</p>
				{/if}
			</div>

			<div class="mb-3">
				<label class="form-label" for="password_confirmation">Confirm Password</label>
				<input
					type="password"
					id="password_confirmation"
					name="password_confirmation"
					bind:value={newUser.password_confirmation}
					placeholder="Confirm password"
					class="form-input w-full rounded-md border border-gray-300 px-4 py-2"
				/>
				{#if errors.password_confirmation}
					<p class="text-sm text-red-500">{errors.password_confirmation[0]}</p>
				{/if}
			</div>

			<button
				type="submit"
				class="mt-4 cursor-pointer rounded-lg bg-blue-500 px-4 py-2 text-white hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
			>
				Save
			</button>
		</form>
	</div>
</section>
