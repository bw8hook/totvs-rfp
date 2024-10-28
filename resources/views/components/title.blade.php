<div class="text-lg font-bold flex items-center justify-between w-full px-4 space-x-2 relative" style="background: #F9F9F9; height: 9.1vh; border-radius: 15px;">
    <div class="flex items-center space-x-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        <span>{{$slot}}</span>
    </div>

    <div class="relative flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600 cursor-pointer" fill="none" viewBox="0 0 24 24" stroke="currentColor" onclick="toggleMenu()">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6h.01M12 12h.01M12 18h.01" />
        </svg>

        <div id="dropdownMenu" class="hidden absolute right-0 mt-8 w-32 bg-white border border-gray-200 rounded shadow-lg">
            <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Option 1</a>
            <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Option 2</a>
            <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Option 3</a>
        </div>
    </div>
</div>
<script>
    function toggleMenu() {
        const dropdownMenu = document.getElementById("dropdownMenu");
        dropdownMenu.classList.toggle("hidden");
    }

    document.addEventListener("click", function (event) {
        const dropdownMenu = document.getElementById("dropdownMenu");
        const isClickInside = event.target.closest('.relative');
        if (!isClickInside && !dropdownMenu.classList.contains("hidden")) {
            dropdownMenu.classList.add("hidden");
        }
    });
</script>