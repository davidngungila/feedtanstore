<div id="confirm-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4 shadow-xl">
        <div class="flex items-center justify-between mb-4">
            <h3 id="confirm-modal-title" class="text-lg font-bold text-primary-900"></h3>
            <button type="button" id="confirm-modal-close-x" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <p id="confirm-modal-message" class="text-sm text-gray-600"></p>
        <div class="mt-6 flex justify-end gap-2">
            <button type="button" id="confirm-modal-cancel" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50">Keep Batch</button>
            <button type="button" id="confirm-modal-ok" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium">Yes, Cancel Batch</button>
        </div>
    </div>
</div>

<script>
(function () {
    const modal = document.getElementById('confirm-modal');
    const titleEl = document.getElementById('confirm-modal-title');
    const messageEl = document.getElementById('confirm-modal-message');
    let onSubmit = null;

    function close() {
        modal.classList.add('hidden');
        onSubmit = null;
    }

    window.openConfirmModal = function (title, message, callback) {
        titleEl.textContent = title;
        messageEl.textContent = message;
        onSubmit = callback;
        modal.classList.remove('hidden');
    };

    document.getElementById('confirm-modal-ok').addEventListener('click', function () {
        if (typeof onSubmit === 'function') onSubmit();
    });
    document.getElementById('confirm-modal-cancel').addEventListener('click', close);
    document.getElementById('confirm-modal-close-x').addEventListener('click', close);
    modal.addEventListener('click', function (e) {
        if (e.target === modal) close();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') close();
    });
})();
</script>
