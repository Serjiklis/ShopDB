<form id="export-selected-form" method="POST" action="{{ route('supplies.exportSelected') }}" style="display: none;">
    @csrf
    <input type="hidden" id="selected-ids" name="selected">
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const exportButton = document.querySelector('[data-bulk-action="exportSelected"]');

        if (exportButton) {
            exportButton.addEventListener('click', function () {
                const selectedIds = @json(session('selected_ids', []));
                if (selectedIds.length === 0) {
                    alert('Нет выбранных записей для экспорта.');
                    return;
                }

                // Set selected IDs to the hidden input
                const hiddenInput = document.getElementById('selected-ids');
                hiddenInput.value = JSON.stringify(selectedIds);

                // Submit the hidden form
                document.getElementById('export-selected-form').submit();
            });
        }
    });
</script>
