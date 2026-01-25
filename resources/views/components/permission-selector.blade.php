@props(['permissions', 'selectedPermissions' => [], 'name' => 'permission_ids[]'])

@php
    use App\Services\ModuleNameService;

    // Automatically group permissions by module using the service
    $permissionGroups = ModuleNameService::groupPermissionsByModule($permissions);
@endphp

<div class="mb-4">
    <label class="form-label fw-bold">الأذونات</label>


    <div class="accordion" id="permissionsAccordion">
        @foreach($permissionGroups as $groupName => $groupPermissions)
            <div class="accordion-item mb-2 border-0 shadow-sm">
                <h2 class="accordion-header" id="heading_{{ $loop->index }}">
                    <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapse_{{ $loop->index }}" aria-expanded="false">
                        <i class="fas fa-shield-alt me-2"></i>
                        {{ $groupName }}
                        <span class="badge bg-primary ms-auto" id="count_{{ $loop->index }}">0</span>
                    </button>
                </h2>
                <div id="collapse_{{ $loop->index }}" class="accordion-collapse collapse"
                     aria-labelledby="heading_{{ $loop->index }}" data-bs-parent="#permissionsAccordion">
                    <div class="accordion-body bg-white">
                        <div class="row g-3">
                            @foreach($groupPermissions as $permission)
                                <div class="col-md-6 col-lg-4">
                                    <div class="form-check permission-item">
                                        <input type="checkbox" name="{{ $name }}" value="{{ $permission->id }}"
                                               class="form-check-input permission-checkbox"
                                               id="perm_{{ $permission->id }}"
                                               data-group="{{ $loop->parent->index }}"
                                            {{ in_array($permission->id, $selectedPermissions) ? 'checked' : '' }}>
                                        <label class="form-check-label d-flex align-items-center"
                                               for="perm_{{ $permission->id }}">
                                            <span class="permission-icon me-2">
                                                @if(str_contains($permission->name, 'view'))
                                                    <i class="fas fa-eye text-info"></i>
                                                @elseif(str_contains($permission->name, 'create'))
                                                    <i class="fas fa-plus text-success"></i>
                                                @elseif(str_contains($permission->name, 'edit'))
                                                    <i class="fas fa-edit text-warning"></i>
                                                @elseif(str_contains($permission->name, 'delete'))
                                                    <i class="fas fa-trash text-danger"></i>
                                                @elseif(str_contains($permission->name, 'manage'))
                                                    <i class="fas fa-cogs text-primary"></i>
                                                @elseif(str_contains($permission->name, 'add_followup'))
                                                    <i class="fas fa-plus-circle text-info"></i>
                                                @elseif(str_contains($permission->name, 'export'))
                                                    <i class="fas fa-download text-success"></i>
                                                @elseif(str_contains($permission->name, 'generate'))
                                                    <i class="fas fa-chart-bar text-warning"></i>
                                                @else
                                                    <i class="fas fa-key text-secondary"></i>
                                                @endif
                                            </span>
                                            <div>
                                                <div
                                                    class="fw-medium">{{ $permission->display_name ?? $permission->name }}</div>
                                                @if($permission->description)
                                                    <small class="text-muted">{{ $permission->description }}</small>
                                                @endif
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

@push('scripts')
    <script>
        (function () {
            'use strict';

            // Store the field name for use in JavaScript
            var fieldName = @json($name);

            // Wait for DOM to be ready
            function initPermissionControls() {
                try {
                    console.log('Initializing permission controls...');

                    // Get elements
                    const selectAllBtn = document.getElementById('selectAllBtn');
                    const deselectAllBtn = document.getElementById('deselectAllBtn');
                    const permissionCheckboxes = document.querySelectorAll('input[name="' + fieldName + '"]');
                    const totalSelectedElement = document.getElementById('totalSelected');

                    console.log('Found elements:', {
                        selectAllBtn: !!selectAllBtn,
                        deselectAllBtn: !!deselectAllBtn,
                        permissionCheckboxes: permissionCheckboxes.length,
                        totalSelectedElement: !!totalSelectedElement,
                        fieldName: fieldName
                    });

                    // Select All functionality
                    if (selectAllBtn) {
                        selectAllBtn.addEventListener('click', function (e) {
                            e.preventDefault();
                            e.stopPropagation();
                            console.log('Select All clicked');

                            permissionCheckboxes.forEach(function (checkbox) {
                                checkbox.checked = true;
                            });
                            updatePermissionCounts();
                        });
                    }

                    // Deselect All functionality
                    if (deselectAllBtn) {
                        deselectAllBtn.addEventListener('click', function (e) {
                            e.preventDefault();
                            e.stopPropagation();
                            console.log('Deselect All clicked');

                            permissionCheckboxes.forEach(function (checkbox) {
                                checkbox.checked = false;
                            });
                            updatePermissionCounts();
                        });
                    }

                    // Update counts when checkboxes change
                    permissionCheckboxes.forEach(function (checkbox) {
                        checkbox.addEventListener('change', updatePermissionCounts);
                    });

                    function updatePermissionCounts() {
                        try {
                            console.log('Updating permission counts...');

                            const groups = {};
                            const totalCheckboxes = permissionCheckboxes.length;
                            const checkedCheckboxes = document.querySelectorAll('input[name="' + fieldName + '"]:checked').length;

                            console.log('Total checkboxes:', totalCheckboxes, 'Checked:', checkedCheckboxes);

                            // Count checked permissions per group
                            document.querySelectorAll('input[name="' + fieldName + '"]:checked').forEach(function (checkbox) {
                                const group = checkbox.getAttribute('data-group');
                                console.log('Checkbox group:', group, 'for checkbox:', checkbox.id);
                                if (group !== null && group !== '') {
                                    groups[group] = (groups[group] || 0) + 1;
                                }
                            });

                            console.log('Groups:', groups);

                            // Update badge counts for all groups
                            const allCountElements = document.querySelectorAll('[id^="count_"]');
                            allCountElements.forEach(function (countElement) {
                                const groupIndex = countElement.id.replace('count_', '');
                                const count = groups[groupIndex] || 0;
                                countElement.textContent = count;
                                console.log('Updated count for group ' + groupIndex + ':', count);
                            });

                            // Update total selected count
                            if (totalSelectedElement) {
                                totalSelectedElement.textContent = 'تم تحديد ' + checkedCheckboxes + ' من أصل ' + totalCheckboxes + ' صلاحية';
                                console.log('Updated total selected text');
                            }
                        } catch (error) {
                            console.error('Error in updatePermissionCounts:', error);
                        }
                    }

                    // Add hover effects for permission items
                    document.querySelectorAll('.permission-item').forEach(function (item) {
                        item.addEventListener('mouseenter', function () {
                            this.style.backgroundColor = '#f8f9fa';
                            this.style.borderRadius = '8px';
                            this.style.padding = '8px';
                            this.style.margin = '-8px';
                        });

                        item.addEventListener('mouseleave', function () {
                            this.style.backgroundColor = '';
                            this.style.borderRadius = '';
                            this.style.padding = '';
                            this.style.margin = '';
                        });
                    });

                    // Initial count update
                    updatePermissionCounts();
                    console.log('Permission controls initialization complete');

                } catch (error) {
                    console.error('Error initializing permission controls:', error);
                }
            }

            // Try multiple ways to initialize
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initPermissionControls);
            } else {
                initPermissionControls();
            }

            // Also try on window load as backup
            window.addEventListener('load', function () {
                setTimeout(initPermissionControls, 100);
            });
        })();
    </script>
@endpush
