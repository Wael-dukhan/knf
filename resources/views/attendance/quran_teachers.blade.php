@extends('layouts.table-layout2')

@section('title', __('messages.attendance_for_quran_teachers'))

@section('content')
<style>
    .custom-info {
        display: grid;
        grid-template-columns: 50% 50%;
    }
</style>

<div>
    <div>
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="mb-4 custom-info">
                    <h5 class="card-title text-primary mb-3">
                        <i class="fas fa-school"></i> {{ __('messages.basic_information') }}
                    </h5>
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <button type="button" class="btn btn-outline-secondary me-2" onclick="adjustDate(-1)">
                            <i class="fas fa-arrow-left"></i> {{ __('messages.previous_day') }}
                        </button>

                        <input type="date" id="date" name="date" class="form-control text-center me-2"
                            style="max-width: 200px;" value="{{ request('date', date('Y-m-d')) }}">

                        <button type="button" class="btn btn-outline-secondary" onclick="adjustDate(1)">
                            {{ __('messages.next_day') }} <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6 mb-2">
                        <div class="border rounded p-3 bg-light">
                            <h6 class="text-muted">{{ __('messages.school') }}</h6>
                            <h4 class="mb-0 text-dark">{{ $attendanceRecords->first()?->school?->name ?? '-' }}</h4>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="border rounded p-3 bg-light">
                            <h6 class="text-muted">{{ __('messages.teacher_attendance_log') }}</h6>
                            <h4 class="mb-0 text-primary">{{ $dayName }} - {{ $dateString }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function adjustDate(offset) {
                const dateInput = document.getElementById('date');
                const date = new Date(dateInput.value);
                date.setDate(date.getDate() + offset);
                const newDateStr = date.toISOString().split('T')[0];
                dateInput.value = newDateStr;
                dateInput.dispatchEvent(new Event('change'));
            }

            document.getElementById('date').addEventListener('change', function () {
                const selectedDate = this.value;
                const params = new URLSearchParams(window.location.search);
                params.set('date', selectedDate);
                window.location.search = params.toString();
            });
        </script>

        <form>
            @csrf
            <input type="hidden" id="term_id" value="{{ $currentTerm->id }}">
            <input type="hidden" id="school_id" value="{{ $schoolId }}">

            <div class="card card-table">
                <div class="card-body">
                    <table class="table table-bordered table-hover table-center mb-0" id="attendanceTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('messages.teacher_name') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.notes') }}</th>
                            </tr>
                            <tr>
                                <th></th>
                                <th>
                                    <input type="text" id="nameSearch" class="form-control form-control-sm"
                                        placeholder="{{ __('messages.search') }}">
                                </th>
                                <th>
                                    <select id="statusSearch" class="form-select form-select-sm">
                                        <option value="all">{{ __('messages.choose') }}</option>
                                        <option value="present">{{ __('messages.present') }}</option>
                                        <option value="absent">{{ __('messages.absent') }}</option>
                                        <option value="late">{{ __('messages.late') }}</option>
                                        <option value="excused">{{ __('messages.excused') }}</option>
                                    </select>
                                </th>
                                <th>
                                    <input type="text" id="noteSearch" class="form-control form-control-sm"
                                        placeholder="{{ __('messages.search') }}">
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($teachers as $index => $teacher)
                                @php
                                    $statuses = ['present', 'absent', 'late', 'excused'];
                                    $record = $attendanceRecords[$teacher->id] ?? null;
                                    $selectedStatus = $record->status ?? 'present';
                                    $notes = $record->notes ?? '';
                                @endphp
                                <tr data-status="{{ $selectedStatus }}" data-teacher-id="{{ $teacher->id }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $teacher->name }}</td>
                                    <td>
                                        @foreach ($statuses as $status)
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input attendance-status" type="radio"
                                                    name="attendance[{{ $teacher->id }}][status]"
                                                    id="{{ $status }}_{{ $teacher->id }}"
                                                    value="{{ $status }}"
                                                    {{ $selectedStatus === $status ? 'checked' : '' }}>
                                                <label class="form-check-label"
                                                    for="{{ $status }}_{{ $teacher->id }}">{{ __('messages.' . $status) }}</label>
                                            </div>
                                        @endforeach
                                    </td>
                                    <td>
                                        <input type="text" name="attendance[{{ $teacher->id }}][notes]"
                                            class="form-control"
                                            placeholder="{{ __('messages.optional_notes') }}"
                                            value="{{ $notes }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4">
                        <a href="{{ route('admin.schools.index') }}" class="btn btn-secondary mt-3">
                            <i class="feather-arrow-left"></i> {{ __('messages.back') }}
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        const table = $('#attendanceTable').DataTable({
            dom: 'Bfrtip',
            order: [[0, 'asc']],
            orderCellsTop: true,
            pageLength: 20,
            fixedHeader: true,
            language: {
                search: "{{ __('messages.search') }}",
                lengthMenu: "{{ __('messages.show') }} _MENU_",
                info: "{{ __('messages.showing') }} _START_ {{ __('messages.to') }} _END_ {{ __('messages.of') }} _TOTAL_",
                paginate: {
                    previous: "{{ __('messages.previous') }}",
                    next: "{{ __('messages.next') }}"
                }
            },
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '{{ __("messages.export_excel") }}',
                    exportOptions: { columns: ':visible' }
                },
                {
                    extend: 'print',
                    text: '{{ __("messages.print") }}',
                    customize: function (win) {
                        $(win.document.body)
                            .css('direction', '{{ app()->getLocale() == "ar" ? "rtl" : "ltr" }}')
                            .css('text-align', '{{ app()->getLocale() == "ar" ? "right" : "left" }}')
                            .find('table')
                            .addClass('table table-bordered');
                    }
                }
            ]
        });

        // تحديث data-status مبدئياً
        $('#attendanceTable tbody tr').each(function () {
            const selectedStatus = $(this).find('.attendance-status:checked').val();
            $(this).attr('data-status', selectedStatus);
        });

        function saveAttendance($row) {
            const teacherId = $row.data('teacher-id');
            const status = $row.find('.attendance-status:checked').val();
            const notes = $row.find('input[name^="attendance"][name$="[notes]"]').val();
            const schoolId = $('#school_id').val();
            const date = $('#date').val();
            const termId = $('#term_id').val();

            $.ajax({
                url: "{{ route('quran_teacher_attendance.ajaxUpdate') }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    teacher_id: teacherId,
                    status: status,
                    notes: notes,
                    school_id: schoolId,
                    date: date,
                    term_id: termId,
                },
                success: function () {
                    $row.attr('data-status', status);
                },
                error: function () {
                    alert('{{ __("messages.error_saving") }}');
                }
            });
        }

        // عند تغيير حالة الحضور (راديو) يحدث التحديث فوراً
        $('#attendanceTable').on('change', '.attendance-status', function () {
            const $row = $(this).closest('tr');
            saveAttendance($row);
        });

        // عند كتابة ملاحظات نستخدم debounce لتقليل عدد طلبات ajax
        let typingTimer;
        const doneTypingInterval = 700; // 700 مللي ثانية تأخير بعد التوقف عن الكتابة

        $('#attendanceTable').on('input', 'input[name$="[notes]"]', function () {
            const $row = $(this).closest('tr');
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => {
                saveAttendance($row);
            }, doneTypingInterval);
        });

        // فلترة الجدول
        $('#nameSearch').on('keyup', function () {
            table.column(1).search(this.value).draw();
        });
        $('#statusSearch').on('change', function () {
            const val = this.value;
            if (val === 'all') {
                table.column(2).search('').draw();
            } else {
                table.column(2).search(val).draw();
            }
        });
        $('#noteSearch').on('keyup', function () {
            table.column(3).search(this.value).draw();
        });
    });
</script>
@endpush
