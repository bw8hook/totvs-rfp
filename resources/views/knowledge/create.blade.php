<x-app-layout>
    <div class="flex flex-col">
        <div class="profile-bar">
            <x-profile-bar></x-profile-bar>
        </div>

        <script>
            const userId = @json($userId);
            document.addEventListener('DOMContentLoaded', () => {
                console.log(userId);
            });
        </script>
        <div class="w-full max-w-md mx-auto justify-center items-center flex flex-column" style="width: 85vw; min-height: 73vh; max-width: 90%;">

                <x-title-component :showButton="false"> Enviar RFP Preparada </x-title-component>

                <div style="padding: 2px;"></div>
                <x-upload/>
                
        </div>
    </div>
</x-app-layout>