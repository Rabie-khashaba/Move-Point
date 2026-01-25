// Fix for interviewModal (Start Work Modal) - Handle representative ID for each row
document.addEventListener('DOMContentLoaded', function () {
    const interviewModal = document.getElementById('interviewModal');

    if (interviewModal) {
        interviewModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const repId = button.getAttribute('data-id');

            console.log('Opening interview (start work) modal for representative ID:', repId);

            // Set representative ID in hidden input
            const hiddenInput = interviewModal.querySelector('input[name="representative_id"]');
            if (hiddenInput) {
                hiddenInput.value = repId;
            }

            // Update form action
            const form = interviewModal.querySelector('form');
            if (form) {
                // Replace the ID in the action URL
                const baseUrl = form.action.replace(/\/\d+$/, '');
                form.action = baseUrl + '/' + repId;
            }
        });

        // Handle governorate change
        const govSelect = document.getElementById('interview_government_id');
        const locSelect = document.getElementById('interview_location_id');
        const msgSelect = document.getElementById('interview_message_id');
        const preview = document.getElementById('messagePreview');

        if (govSelect) {
            govSelect.addEventListener('change', function () {
                const governorateId = this.value;

                if (!governorateId) {
                    if (locSelect) locSelect.innerHTML = '<option value="">اختر المنطقة (اختياري)</option>';
                    if (msgSelect) msgSelect.innerHTML = '<option value="">اختر الرسالة</option>';
                    if (preview) preview.innerHTML = '<small class="text-muted">اختر المحافظة لعرض الرسائل المتاحة</small>';
                    return;
                }

                // Load locations
                fetch(`/getlocations/${governorateId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (locSelect) {
                            locSelect.innerHTML = '<option value="">اختر المنطقة (اختياري)</option>';
                            data.forEach(loc => {
                                locSelect.innerHTML += `<option value="${loc.id}">${loc.name}</option>`;
                            });
                        }
                        // Load messages
                        loadInterviewMessages(governorateId, null);
                    })
                    .catch(err => {
                        console.error('Error loading locations:', err);
                        if (locSelect) locSelect.innerHTML = '<option value="">خطأ في تحميل البيانات</option>';
                    });
            });
        }

        if (locSelect) {
            locSelect.addEventListener('change', function () {
                const locationId = this.value;
                const governorateId = govSelect ? govSelect.value : null;

                if (!governorateId) return;
                loadInterviewMessages(governorateId, locationId || null);
            });
        }

        function loadInterviewMessages(governorateId, locationId = null) {
            if (!msgSelect) return;

            let url = `/getmessagesStartWork?government_id=${governorateId}`;
            if (locationId) {
                url += `&location_id=${locationId}`;
            }

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    msgSelect.innerHTML = '<option value="">اختر الرسالة</option>';
                    data.forEach(msg => {
                        msgSelect.innerHTML += `<option value="${msg.id}">${msg.description}</option>`;
                    });
                    if (preview) {
                        preview.innerHTML = '<small class="text-muted">اختر الرسالة لعرض المعاينة</small>';
                    }
                })
                .catch(err => {
                    console.error('Error loading messages:', err);
                    msgSelect.innerHTML = '<option value="">خطأ في تحميل الرسائل</option>';
                    if (preview) {
                        preview.innerHTML = '<small class="text-danger">خطأ في تحميل الرسائل</small>';
                    }
                });
        }

        if (msgSelect) {
            msgSelect.addEventListener('change', function () {
                const messageId = this.value;

                if (!messageId) {
                    if (preview) {
                        preview.innerHTML = '<small class="text-muted">اختر الرسالة لعرض المعاينة</small>';
                    }
                    return;
                }

                fetch(`/getmessageStartWork/${messageId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (preview) {
                            preview.innerHTML = `
                                <div class="mb-2"><strong>الوصف:</strong> ${data.description}</div>
                                ${data.google_map_url ? `<div><strong>رابط الخريطة:</strong> <a href="${data.google_map_url}" target="_blank">${data.google_map_url}</a></div>` : ''}
                            `;
                        }
                    })
                    .catch(err => {
                        console.error('Error loading message preview:', err);
                        if (preview) {
                            preview.innerHTML = '<small class="text-danger">خطأ في تحميل الرسالة</small>';
                        }
                    });
            });
        }
    }
});
