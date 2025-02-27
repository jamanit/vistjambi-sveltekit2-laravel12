<script lang="ts">
	const appName = import.meta.env.VITE_APP_NAME;
</script>

<nav class="flex items-center bg-purple-800 px-4 py-4 text-white md:px-24">
	<h1 class="text-xl font-bold">{appName}</h1>
	<div class="ml-auto">
		<a href="/" class="mr-4">Home</a>
		<a href="/about" class="mr-4" data-sveltekit-prefetch>About</a>
	</div>
</nav>
