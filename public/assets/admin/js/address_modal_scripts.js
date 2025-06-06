$(document).ready(function () {
    let map = null; // Змінна для зберігання об'єкта карти

    // Обробка кліку на посилання адреси
    $('.address-link').on('click', function () {
        const zipcodeId = $(this).data('zipcode-id');

        // Очищення попередніх даних
        $('#addressDetails').html('<p><strong>{{ labels('admin_labels.loading', 'Loading...') }}</strong></p>');
        $('#addressMap').empty();

        // AJAX-запит для отримання даних адреси
        $.ajax({
            url: '{{ route("address.details") }}',
            method: 'GET',
            data: { zipcode_id: zipcodeId },
            success: function (response) {
                if (response.error) {
                    $('#addressDetails').html('<p class="text-danger">{{ labels('admin_labels.error', 'Error') }}: ' + response.message + '</p>');
                    return;
                }

                // Відображення текстової інформації
                const data = response.data;
                let addressHtml = `
    < p > <strong>{{ labels('admin_labels.country', 'Country') }}:</strong> ${data.country || 'N/A'}</ >
                    <p><strong>{{ labels('admin_labels.region', 'Region') }}:</strong> ${data.region || 'N/A'}</p>
                    <p><strong>{{ labels('admin_labels.city', 'City') }}:</strong> ${data.city || 'N/A'}</p>
                    <p><strong>{{ labels('admin_labels.zipcode', 'Zipcode') }}:</strong> ${data.zipcode || 'N/A'}</p>
                    <p><strong>{{ labels('admin_labels.street', 'Street') }}:</strong> ${data.street || 'N/A'}</p>
                    <p><strong>{{ labels('admin_labels.latitude', 'Latitude') }}:</strong> ${data.latitude || 'N/A'}</p>
                    <p><strong>{{ labels('admin_labels.longitude', 'Longitude') }}:</strong> ${data.longitude || 'N/A'}</p>
`;
                $('#addressDetails').html(addressHtml);

                // Ініціалізація карти OpenStreetMap
                if (data.latitude && data.longitude) {
                    if (map) {
                        map.remove(); // Видаляємо попередню карту, якщо вона існує
                    }
                    map = L.map('addressMap').setView([data.latitude, data.longitude], 13);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(map);
                    L.marker([data.latitude, data.longitude]).addTo(map)
                        .bindPopup(`${data.city || 'Address'} <br>${data.street || ''}`)
                        .openPopup();
                } else {
                    $('#addressMap').html('<p class="text-muted">{{ labels('admin_labels.no_coordinates', 'No coordinates available') }}</p>');
                }
            },
            error: function (xhr) {
                $('#addressDetails').html('<p class="text-danger">{{ labels('admin_labels.error', 'Error') }}: Failed to load address details.</p>');
            }
        });
    });

    // Очищення карти при закритті модального вікна
    $('#addressModal').on('hidden.bs.modal', function () {
        if (map) {
            map.remove();
            map = null;
        }
        $('#addressMap').empty();
    });
});