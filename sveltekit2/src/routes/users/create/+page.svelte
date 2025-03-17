<script lang="ts">
	import Breadcrumb from '$lib/components/Breadcrumb.svelte';
	import { goto } from '$app/navigation';
	import Swal from 'sweetalert2';
	import 'sweetalert2/dist/sweetalert2.min.css';

	let breadcrumbItems = [
		{ name: 'Home', url: '/' },
		{ name: 'Users', url: '/users' },
		{ name: 'Create', url: '', isActive: true }
	];

	let newUser: User = {
		name: '',
		email: '',
		password: '',
		password_confirmation: ''
	};

	const apiBaseURL = import.meta.env.VITE_API_BASE_URL;
	let errors: { [key: string]: string[] } = {};
	let message = '';
	let status = false;
	let loading = false;
	interface User {
		name: string;
		email: string;
		password: string;
		password_confirmation: string;
	}

	function showToast(message: string, icon: 'success' | 'error') {
		Swal.fire({
			toast: true,
			position: 'top-end',
			icon: icon,
			title: message,
			showConfirmButton: false,
			showCloseButton: true,
			timer: 3000,
			timerProgressBar: true
		});
	}

	async function createUser() {
		const token = localStorage.getItem('token');

		if (!token) {
			message = 'You are not authenticated. Please log in.';
			showToast(message, 'error');
			return;
		}

		loading = true;
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
				if (res.status === 422) {
					errors = responseBody.errors || {};
					message = responseBody.message || 'Validation failed.';
				} else {
					message = responseBody.message || 'Something went wrong.';
				}
				showToast(message, 'error');
				throw new Error(message);
			}

			status = responseBody.status || true;
			message = responseBody.message || 'User created successfully.';
			showToast(message, 'success');
			setTimeout(() => {
				goto('/users');
			}, 1000);
		} catch (err) {
			console.error(err);
			message = 'Error fetching user data.';
			showToast(message, 'error');
		} finally {
			loading = false;
		}
	}
</script>

<section>
	<Breadcrumb {breadcrumbItems} />

	<div class="mx-auto max-w-lg pb-2 pt-6">
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
				disabled={loading}
			>
				{#if loading}
					Loading...
				{:else}
					Save
				{/if}
			</button>
		</form>
	</div>
</section>
