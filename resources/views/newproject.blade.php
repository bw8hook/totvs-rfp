<x-app-layout>
    <div class="flex items-center justify-center min-h-full min-w-full min-h-screen flex-col">
        <script>
            const userId = @json($userId);
            document.addEventListener('DOMContentLoaded', () => {
                console.log(userId);
            });
        </script>
        <div class="w-full max-w-md mx-auto justify-center items-center flex flex-column" style="width: 55vw; height: 73vh;">
            <x-title>
                Novo projeto
            </x-title>
            <div style="padding: 2px;">
            </div>
            <x-upload/>
        </div>
    </div>
</x-app-layout>