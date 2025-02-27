<script lang="ts">
	import Breadcrumb from '$lib/components/Breadcrumb.svelte';
	let breadcrumbItems = [{ name: 'Home', url: '/', isActive: true }];

	type Category = {
		name: string;
	};

	type Destination = {
		id: number;
		name: string;
		description: string;
		image: string;
		created_at: string;
		category: Category;
	};

	let destinations: Destination[] = [];
	let apiError = '';
	const apiBaseURL = import.meta.env.VITE_API_BASE_URL;
	let limit = 6;

	async function getDestinations() {
		try {
			const res = await fetch(apiBaseURL + `/api/destinations?limit=${limit}`, {
				method: 'GET'
			});
			if (!res.ok) {
				throw new Error(`Failed to fetch: ${res.status}`);
			}
			const result = await res.json();
			destinations = result.data;
		} catch (err) {
			apiError = 'Error fetching destinations.';
			console.error(err);
		}
	}

	getDestinations();
</script>

<section>
	<Breadcrumb {breadcrumbItems} />

	{#if apiError}
		<p class="text-red-500">{apiError}</p>
	{:else if destinations.length === 0}
		<p class="text-center text-gray-500">Loading...</p>
	{:else}
		<div class="flex items-center justify-center">
			<div class="grid w-full grid-cols-1 gap-4 md:grid-cols-3">
				{#each destinations as destination}
					<a href={`/destinations/${destination.id}`} class="text-sm text-gray-600">
						<div class="flex h-full flex-col rounded-lg bg-gray-100 p-4 text-center shadow-lg">
							<img
								src={apiBaseURL + '/storage/' + destination.image}
								alt={destination.name}
								class="mb-2 h-54 w-full rounded-md object-cover"
							/>
							<h3 class="text-lg font-semibold">{destination.name}</h3>
							<div class="flex items-center justify-center gap-6 text-sm">
								<p class="text-sm text-gray-600">
									{new Date(destination.created_at).toLocaleDateString()}
								</p>
								<p class="text-sm text-gray-600">{destination.category?.name}</p>
							</div>
							<p class="line-clamp-3 text-sm text-gray-600">{@html destination.description}</p>
						</div>
					</a>
				{/each}
			</div>
		</div>

		<div class="mt-4 flex items-center justify-center">
			<a
				href="/destinations"
				class="rounded-lg bg-sky-600 px-4 py-2 text-base text-white hover:bg-sky-700"
				>View all destinations</a
			>
		</div>
	{/if}
</section>
