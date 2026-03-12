<script>
    $(document).ready(function() {
        // Configure toastr options
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-bottom-right",
            "preventDuplicates": true,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        // Handle Laravel session-based toast messages
        @if(session('toast'))
            @php
                $toast = session('toast');
                $type = isset($toast['type']) ? $toast['type'] : 'info';
                $message = isset($toast['message']) ? $toast['message'] : '';
            @endphp
            toastr.{{ $type }}('{{ $message }}');
        @endif

        // Handle standard Laravel session messages
        @if(session('success'))
            toastr.success('{{ session('success') }}');
        @endif

        @if(session('error'))
            toastr.error('{{ session('error') }}');
        @endif

        @if(session('warning'))
            toastr.warning('{{ session('warning') }}');
        @endif

        @if(session('info'))
            toastr.info('{{ session('info') }}');
        @endif

        // Handle toast messages stored in sessionStorage (for redirects after AJAX actions)
        const storedToast = sessionStorage.getItem('toast');
        if (storedToast) {
            try {
                const toast = JSON.parse(storedToast);
                const type = toast.type || 'info';
                const message = toast.message || '';

                if (message && typeof toastr[type] === 'function') {
                    toastr[type](message);
                }
            } catch (error) {
                // Ignore invalid storage payload
            }

            sessionStorage.removeItem('toast');
        }
    });

    // Global toast utility functions
    window.Toast = {
        success: function(message) {
            toastr.success(message);
        },
        error: function(message) {
            toastr.error(message);
        },
        warning: function(message) {
            toastr.warning(message);
        },
        info: function(message) {
            toastr.info(message);
        }
    };
</script>