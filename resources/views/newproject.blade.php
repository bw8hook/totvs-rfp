<x-app-layout>
    <div class="flex items-center justify-center h-screen flex-col">
        <h1>
            <script>
                const userId = @json($userId);
                document.addEventListener('DOMContentLoaded', () => {
                    console.log(userId);
                });
            </script>
            <x-upload></x-upload>
        </h1>
    </div>
</x-app-layout>