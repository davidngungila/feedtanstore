<div id="processingOverlay" class="hidden fixed inset-0 z-[70] flex items-center justify-center bg-black bg-opacity-60">
    <div class="bg-white rounded-2xl shadow-xl px-10 py-8 flex flex-col items-center gap-4">
        <svg class="animate-spin h-10 w-10 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        <p class="text-gray-700 font-medium">Processing&hellip; please wait</p>
    </div>
</div>
<script>
(function () {
    var overlay = document.getElementById('processingOverlay');
    if (!overlay) return;
    document.querySelectorAll('form').forEach(function (form) {
        form.addEventListener('submit', function () {
            overlay.classList.remove('hidden');
        });
    });
})();
</script>
