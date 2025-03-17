<script lang="ts">
	import { onMount } from 'svelte';
	import Breadcrumb from '$lib/components/Breadcrumb.svelte';
	import Swal from 'sweetalert2';
	import 'sweetalert2/dist/sweetalert2.min.css';

	let breadcrumbItems = [
		{ name: 'Home', url: '/' },
		{ name: 'Me', url: '', isActive: true }
	];

	const apiBaseURL = import.meta.env.VITE_API_BASE_URL;
	let user: User | null = null;
	let message = '';

	interface User {
		name: string;
		email: string;
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

	onMount(async () => {
		const token = localStorage.getItem('token');

		if (!token) {
			message = 'You are not authenticated. Please log in.';
			showToast(message, 'error');
			return;
		}

		try {
			const response = await fetch(`${apiBaseURL}/api/me`, {
				method: 'GET',
				headers: {
					Authorization: `Bearer ${token}`,
					'Content-Type': 'application/json'
				}
			});

			const data = await response.json();

			if (!response.ok) {
				message = 'Failed to fetch user data.';
				showToast(message, 'error');
				return;
			}

			user = data;
		} catch (error) {
			message = 'Something went wrong, please try again later.';
			showToast(message, 'error');
		}
	});
</script>

<section>
	<Breadcrumb {breadcrumbItems} />

	<div class="pb-2 pt-6">
		{#if user}
			<p><strong>Name:</strong> {user.name}</p>
			<p><strong>Email:</strong> {user.email}</p>
		{:else}
			<p class="text-center">Loading...</p>
		{/if}
	</div>
</section>
