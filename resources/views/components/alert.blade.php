@if (session('alert'))
    @php
        $alert = session('alert');

        $types = [
            'success' => [
                'theme' => 'success',
                'icon' => 'bi-check-circle-fill',
            ],
            'error' => [
                'theme' => 'danger',
                'icon' => 'bi-x-circle-fill',
            ],
            'warning' => [
                'theme' => 'warning',
                'icon' => 'bi-exclamation-triangle-fill',
            ],
            'info' => [
                'theme' => 'info',
                'icon' => 'bi-info-circle-fill',
            ],
        ];

        $style = $types[$alert['type'] ?? 'info']
            ?? $types['info'];
    @endphp

    <div class="alert custom-alert custom-alert-{{ $style['theme'] }}"
         role="alert"
         data-auto-dismiss="5000">

        <div class="custom-alert-icon">
            <i class="bi {{ $style['icon'] }}"></i>
        </div>

        <div class="custom-alert-content">
            <h6>{{ $alert['title'] ?? 'Notification' }}</h6>
            <p>{{ $alert['message'] ?? '' }}</p>
        </div>

        <button type="button"
                class="custom-alert-close"
                aria-label="Close notification" data-bs-dismiss="alert" aria-label="Close">
            <i class="bi bi-x-lg"></i>
        </button>

        <div class="custom-alert-progress">
            <div class="custom-alert-progress-bar"></div>
        </div>
    </div>
@endif


@if ($errors->any())
    <div class="alert custom-alert custom-alert-danger"
         role="alert">

        <div class="custom-alert-icon">
            <i class="bi bi-exclamation-circle-fill"></i>
        </div>

        <div class="custom-alert-content">
            <h6>Validation Failed!</h6>

            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>

        <button type="button" class="custom-alert-close" data-bs-dismiss="alert" aria-label="Close">
            <i class="bi bi-x-lg"></i>
        </button>

    </div>
@endif


@section('javascript')
<script>

    document.addEventListener('DOMContentLoaded', function () {
        const alerts = document.querySelectorAll('.custom-alert');
        alerts.forEach(function (alert) {
            const closeButton = alert.querySelector(
                '.custom-alert-close'
            );
            const duration = Number(
                alert.dataset.autoDismiss || 5000
            );
            let dismissTimer;
            // Dismiss alert with animation
            function dismissAlert() {
                if (alert.classList.contains('is-hiding')) {
                    return;
                }
                clearTimeout(dismissTimer);
                alert.classList.add('is-hiding');
                setTimeout(function () {
                    alert.remove();
                }, 300);
            }
            // Manual close button
            closeButton.addEventListener('click', function () {
                dismissAlert();
            });
            // Automatic dismissal
            dismissTimer = setTimeout(function () {
                dismissAlert();
            }, duration);
        });
    });
</script>
@endsection
