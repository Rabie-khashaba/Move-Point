// Fix for SendMessageTrainingModal - Handle representative ID for each row
document.addEventListener('DOMContentLoaded', function () {
    const trainingModal = document.getElementById('SendMessageTrainingModal');

    if (trainingModal) {
        trainingModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const repId = button.getAttribute('data-id');
            const repName = button.getAttribute('data-name');

            console.log('Opening training modal for:', repName, 'ID:', repId);

            // Set representative ID in hidden input
            const hiddenInput = document.getElementById('trainingRepId');
            if (hiddenInput) {
                hiddenInput.value = repId;
            }

            // Update form action
            const form = trainingModal.querySelector('#trainingForm');
            if (form) {
                form.action = form.action.replace(/\/\d+$/, '/' + repId);
            }
        });

        // Handle governorate change for training modal
        const governmentT_id = document.getElementById('governmentT_id');
        const locationT_id = document.getElementById('locationT_id');
        const messageT_id = document.getElementById('messageT_id');
        const messagePreviewT = document.getElementById('messagePreviewT');

        if (governmentT_id) {
            governmentT_id.addEventListener('change', function () {
                const governorateId = this.value;

                if (!governorateId) {
                    if (locationT_id) locationT_id.innerHTML = '<option value="">اختر المنطقة (اختياري)</option>';
                    if (messageT_id) messageT_id.innerHTML = '<option value="">اختر الرسالة</option>';
                    if (messagePreviewT) messagePreviewT.innerHTML = '<small class="text-muted">اختر المحافظة لعرض الرسائل المتاحة</small>';
                    return;
                }

                // Load locations
                fetch(`/getlocations/${governorateId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (locationT_id) {
                            locationT_id.innerHTML = '<option value="">اختر المنطقة (اختياري)</option>';
                            data.forEach(loc => {
                                locationT_id.innerHTML += `<option value="${loc.id}">${loc.name}</option>`;
                            });
                        }
                        // Load messages for governorate
                        loadTrainingMessages(governorateId, null);
                    })
                    .catch(err => {
                        console.error('Error loading locations:', err);
                        if (locationT_id) locationT_id.innerHTML = '<option value="">خطأ في تحميل البيانات</option>';
                    });
            });
        }

        if (locationT_id) {
            locationT_id.addEventListener('change', function () {
                const locationId = this.value;
                const governorateId = governmentT_id ? governmentT_id.value : null;

                if (!governorateId) return;
                loadTrainingMessages(governorateId, locationId || null);
            });
        }

        function loadTrainingMessages(governorateId, locationId = null) {
            if (!messageT_id) return;

            let url = `/getmessages?government_id=${governorateId}`;
            if (locationId) {
                url += `&location_id=${locationId}`;
            }

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    messageT_id.innerHTML = '<option value="">اختر الرسالة</option>';
                    data.forEach(msg => {
                        messageT_id.innerHTML += `<option value="${msg.id}">${msg.description}</option>`;
                    });
                    if (messagePreviewT) {
                        messagePreviewT.innerHTML = '<small class="text-muted">اختر الرسالة لعرض المعاينة</small>';
                    }
                })
                .catch(err => {
                    console.error('Error loading messages:', err);
                    messageT_id.innerHTML = '<option value="">خطأ في تحميل الرسائل</option>';
                    if (messagePreviewT) {
                        messagePreviewT.innerHTML = '<small class="text-danger">خطأ في تحميل الرسائل</small>';
                    }
                });
        }

        if (messageT_id) {
            messageT_id.addEventListener('change', function () {
                const messageId = this.value;

                if (!messageId) {
                    if (messagePreviewT) {
                        messagePreviewT.innerHTML = '<small class="text-muted">اختر الرسالة لعرض المعاينة</small>';
                    }
                    return;
                }

                fetch(`/getmessage/${messageId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (messagePreviewT) {
                            messagePreviewT.innerHTML = `
                                <div class="mb-2"><strong>الوصف:</strong> ${data.description}</div>
                                ${data.google_map_url ? `<div><strong>رابط الخريطة:</strong> <a href="${data.google_map_url}" target="_blank">${data.google_map_url}</a></div>` : ''}
                            `;
                        }
                    })
                    .catch(err => {
                        console.error('Error loading message preview:', err);
                        if (messagePreviewT) {
                            messagePreviewT.innerHTML = '<small class="text-danger">خطأ في تحميل الرسالة</small>';
                        }
                    });
            });
        }
    }
});
