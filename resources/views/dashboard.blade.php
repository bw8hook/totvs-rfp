<x-app-layout>
    <div class="flex min-w-full flex-col">
        <div style="display: flex; padding-top: 10px; align-self: end;">
            <x-profile-bar>
            </x-profile-bar>
        </div>
    </div>
    <div class="flex items-center justify-center min-h-full min-w-full min-h-screen flex-col">
        <script>
            const userId = @json($userId);
            document.addEventListener('DOMContentLoaded', () => {
                console.log(userId);
            });
        </script>
        <div class="w-full max-w-md mx-auto justify-center items-center flex flex-column"
            style="width: 55vw; height: 73vh;">
            <a href="/newproject">
                Criar novo projeto
            </a>
            <a href="/newproject">
                Criar novo projeto
            </a>
        </div>
    </div>
</x-app-layout>