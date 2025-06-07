<!-- Address Details Modal -->
<div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addressModalLabel">
                    {{ labels('admin_labels.address_details', 'Address Details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Left Column: Text Information -->
                    <div class="col-md-6">
                        <div id="addressDetails">
                            <p><strong>{{ labels('admin_labels.loading', 'Loading...') }}</strong></p>
                        </div>
                    </div>
                    <!-- Right Column: OpenStreetMap -->
                    <div class="col-md-6">
                        <div id="addressMap" style="height: 400px;"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">{{ labels('admin_labels.close', 'Close') }}</button>
            </div>
        </div>
    </div>
</div>

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        #addressMap {
            width: 100%;
            height: 400px;
            border-radius: 8px;
        }

        #addressDetails p {
            margin-bottom: 10px;
        }

        #addressDetails strong {
            color: #333;
        }

        .modal-body .row {
            display: flex;
            align-items: stretch;
        }

        .modal-body .col-md-6 {
            padding: 15px;
        }

        .address-link {
            color: #007bff;
            text-decoration: none;
        }

        .address-link:hover {
            text-decoration: underline;
        }
    </style>
@endsection


@section('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        $(document).ready(function() {
            let map = null; // Змінна для зберігання об'єкта карти

            // Обробка кліку на посилання адреси
            $('.address-link').on('click', function() {
                const zipcodeId = $(this).data('zipcode-id');
                const addressId = $(this).data('address-id');

                // Очищення попередніх даних
                $('#addressDetails').html(
                    '<p><strong>{{ labels('admin_labels.loading', 'Loading...') }}</strong></p>');
                $('#addressMap').empty();

                // AJAX-запит для отримання даних адреси
                $.ajax({
                    url: '{{ route('address.details') }}',
                    method: 'GET',
                    data: {
                        zipcode_id: zipcodeId,
                        address_id: addressId
                    },
                    success: function(response) {
                        if (response.error) {
                            $('#addressDetails').html(
                                '<p class="text-danger">{{ labels('admin_labels.error', 'Error') }}: ' +
                                response.message + ' </p>');
                            return;
                        }

                        // Відображення текстової інформації
                        const data = response.data;
                        const addressData = response.addressData;

                        let addressHtml = `
                        <h5>Client entered address</h5>
                        <p>${addressData.address}</p>
                        <h5>Address by zipcode</h5>
                        <p><strong>{{ labels('admin_labels.country', 'Country') }}:</strong> ${data.country || 'N/A'}<br>
                        <strong>{{ labels('admin_labels.region', 'Region') }}:</strong> ${data.region || 'N/A'}<br>
                        <strong>{{ labels('admin_labels.city', 'City') }}:</strong> ${data.city || 'N/A'}<br>
                        <strong>{{ labels('admin_labels.zipcode', 'Zipcode') }}:</strong> ${data.zipcode || 'N/A'}</p>
                        <p><strong>{{ labels('admin_labels.street', 'Formatted') }}:</strong> ${addressData.address || 'N/A'}, ${data.street || 'N/A'}</p>
                        <p><strong>{{ labels('admin_labels.latitude', 'Latitude') }}:</strong> ${data.latitude || 'N/A'}<br>
                        <strong>{{ labels('admin_labels.longitude', 'Longitude') }}:</strong> ${data.longitude || 'N/A'}</p>
                        `;
                        $('#addressDetails').html(addressHtml);

                        // Ініціалізація карти OpenStreetMap
                        if (data.latitude && data.longitude) {
                            if (map) {
                                map.remove(); // Видаляємо попередню карту, якщо вона існує
                            }
                            map = L.map('addressMap').setView([data.latitude, data
                                .longitude
                            ], 13);
                            L.tileLayer(
                                'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                                }).addTo(map);
                            L.marker([data.latitude, data.longitude]).addTo(map)
                                .bindPopup(
                                    `${data.city || 'Address'}<br>${data.street || ''}`)
                                .openPopup();
                        } else {
                            $('#addressMap').html(
                                '<p class="text-muted">{{ labels('admin_labels.no_coordinates', 'No coordinates available') }} </p> '
                            );
                        }
                    },
                    error: function(xhr) {
                        $('#addressDetails').html(
                            '<p class="text-danger">{{ labels('admin_labels.error', 'Error') }}: Failed to load address details. </p>'
                        );
                    }
                });
            });

            // Очищення карти при закритті модального вікна
            $('#addressModal').on('hidden.bs.modal', function() {
                if (map) {
                    map.remove();
                    map = null;
                }
                $('#addressMap').empty();
            });
        });
    </script>
@endsection
