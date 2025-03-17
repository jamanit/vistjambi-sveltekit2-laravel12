<script lang="ts">
	import { onMount } from 'svelte';
	import { goto } from '$app/navigation';
	import Breadcrumb from '$lib/components/Breadcrumb.svelte';

	let breadcrumbItems = [
		{ name: 'Home', url: '/' },
		{ name: 'Users', url: '', isActive: true }
	];

	interface User {
		name: string;
		email: string;
	}

	const apiBaseURL = import.meta.env.VITE_API_BASE_URL;
	let status = false;
	let message = '';
	let dataUsers: User[] = [];

	async function getUsers() {
		const token = localStorage.getItem('token');
		if (!token) {
			message = 'You are not authenticated. Please log in.';
			return;
		}

		try {
			const response = await fetch(apiBaseURL + `/api/users`, {
				method: 'GET',
				headers: {
					Authorization: `Bearer ${token}`,
					'Content-Type': 'application/json'
				}
			});
			if (!response.ok) {
				throw new Error(`Failed to fetch users: ${response.status}`);
			}
			const result = await response.json();
			dataUsers = result.data;
		} catch (err) {
			message = 'Error fetching users.';
			console.error(err);
		}
	}

	async function deleteUser(userId: string) {
		const token = localStorage.getItem('token');

		if (!token) {
			message = 'You are not authenticated. Please log in.';
			return;
		}

		const confirmed = window.confirm('Are you sure you want to delete this user?');
		if (!confirmed) return;

		try {
			const response = await fetch(apiBaseURL + `/api/users/${userId}`, {
				method: 'DELETE',
				headers: {
					Authorization: `Bearer ${token}`,
					'Content-Type': 'application/json'
				}
			});

			if (!response.ok) {
				throw new Error(`Failed to delete user: ${response.status}`);
			}

			dataUsers = dataUsers.filter((user) => user.id !== userId);

			const data = await response.json();
			status = data.status || true;
			message = data.message || 'User has been successfully deleted.';
		} catch (err) {
			message = 'Error deleting user.';
			console.error(err);
			window.alert('Failed to delete the user. Please try again.');
		}
	}

	function addUser() {
		goto('/users/create');
	}

	function editUser(userId: strin) {
		goto(`/users/edit/${userId}`);
	}

	onMount(() => {
		getUsers();
	});
</script>

<section>
	<Breadcrumb {breadcrumbItems} />

	<div class="pb-2 pt-6">
		<div class="mb-4 flex justify-end">
			<button
				on:click={() => addUser()}
				class="cursor-pointer rounded-lg bg-blue-500 px-4 py-2 text-white hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
				>Add User</button
			>
		</div>

		{#if message}
			<div class="mb-4 rounded-lg {status ? 'bg-green-500' : 'bg-red-500'} p-4 text-white">
				{message}
			</div>
		{/if}

		<table class="mt-4 min-w-full table-auto border-collapse">
			<thead>
				<tr class="border-b">
					<th class="px-4 py-2 text-left">Name</th>
					<th class="px-4 py-2 text-left">Email</th>
					<th class="px-4 py-2 text-left">Actions</th>
				</tr>
			</thead>
			<tbody>
				{#each dataUsers as user}
					<tr class="border-b">
						<td class="px-4 py-2">{user.name}</td>
						<td class="px-4 py-2">{user.email}</td>
						<td class="px-4 py-2">
							<button
								on:click={() => deleteUser(user.id)}
								class="cursor-pointer rounded-lg bg-red-500 px-4 py-2 text-white hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500"
								>Delete</button
							>
							<button
								on:click={() => editUser(user.id)}
								class="cursor-pointer rounded-lg bg-yellow-500 px-4 py-2 text-white hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500"
								>Edit</button
							>
						</td>
					</tr>
				{/each}
			</tbody>
		</table>
	</div>
</section>
