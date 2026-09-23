document.addEventListener('DOMContentLoaded', function () {
    const rows = document.querySelectorAll('tbody tr');

        rows.forEach(function (row) {
            const inputs = row.querySelectorAll('input[type="number"]');
            const totalInput = row.querySelector('input[name$="[total]"]');

            inputs.forEach(function (input) {
                if (!input.name.endsWith('[total]')) {
                    // Prevent negative values
                    input.addEventListener('input', function () {
                        if (parseFloat(this.value) < 0) {
                            this.value = 0; // Reset to zero if negative
                        }

                        let total = 0;
                        inputs.forEach(function (field) {
                            if (!field.name.endsWith('[total]') && !isNaN(parseFloat(field.value))) {
                                total += parseFloat(field.value);
                            }
                        });
                        totalInput.value = total.toFixed(2);
                    });
                }
            });
        });
});
