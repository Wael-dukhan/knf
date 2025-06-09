@extends('layouts.table-layout2')

@section('title', __('messages.attendance_for_quran_students'))

@section('content')
<style>
    .custom-info {
        display: grid;
        grid-template-columns: 50% 50%;
    }
</style>

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
                        <h6 class="text-muted">{{ __('messages.quran_class') }}</h6>
                        <h4 class="mb-0 text-dark">{{ $quranClass->name }}</h4>
                    </div>
                </div>
                <div class="col-md-6 mb-2">
                    <div class="border rounded p-3 bg-light">
                        <h6 class="text-muted">{{ __('messages.student_attendance_log') }}</h6>
                        <h4 class="mb-0 text-primary">{{ $dayName }} - {{ $dateString }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form>
        @csrf
        <input type="hidden" id="term_id" value="{{ $currentTerm->id }}">
        <input type="hidden" id="school_id" value="{{ $quranClass->quranLevel->school_id }}">
        <input type="hidden" id="quran_class_id" value="{{ $quranClass->id }}">

        <div class="card card-table">
            <div class="card-body">
                <table class="table table-bordered table-hover table-center mb-0" id="attendanceTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('messages.student_name') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th>{{ __('messages.notes') }}</th>
                        </tr>
                        <tr>
                            <th></th>
                            <th><input type="text" id="nameSearch" class="form-control form-control-sm" placeholder="{{ __('messages.search') }}"></th>
                            <th>
                                <select id="statusSearch" class="form-select form-select-sm">
                                    <option value="all">{{ __('messages.choose') }}</option>
                                    <option value="present">{{ __('messages.present') }}</option>
                                    <option value="absent">{{ __('messages.absent') }}</option>
                                    <option value="late">{{ __('messages.late') }}</option>
                                    <option value="excused">{{ __('messages.excused') }}</option>
                                </select>
                            </th>
                            <th><input type="text" id="noteSearch" class="form-control form-control-sm" placeholder="{{ __('messages.search') }}"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($students as $index => $student)
                            @php
                                $statuses = ['present', 'absent', 'late', 'excused'];
                                $record = $attendanceRecords[$student->id] ?? null;
                                $selectedStatus = $record->status ?? 'present';
                                $notes = $record->notes ?? '';
                            @endphp
                            <tr data-status="{{ $selectedStatus }}" data-student-id="{{ $student->id }}">
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $student->name }}</td>
                                <td>
                                    @foreach ($statuses as $status)
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input attendance-status" type="radio"
                                                name="attendance[{{ $student->id }}][status]"
                                                id="{{ $status }}_{{ $student->id }}"
                                                value="{{ $status }}"
                                                {{ $selectedStatus === $status ? 'checked' : '' }}>
                                            <label class="form-check-label" for="{{ $status }}_{{ $student->id }}">{{ __('messages.' . $status) }}</label>
                                        </div>
                                    @endforeach
                                </td>
                                <td>
                                    <input type="text" name="attendance[{{ $student->id }}][notes]"
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

        function saveAttendance($row) {
            const studentId = $row.data('student-id');
            const status = $row.find('.attendance-status:checked').val();
            const notes = $row.find('input[name^="attendance"][name$="[notes]"]').val();
            const schoolId = $('#school_id').val();
            const date = $('#date').val();
            const termId = $('#term_id').val();
            const quranClassId = $('#quran_class_id').val();

            $.ajax({
                url: "{{ route('quran_student_attendance.ajaxUpdate') }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    student_id: studentId,
                    quran_class_id: quranClassId,
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

        $('#attendanceTable').on('change', '.attendance-status', function () {
            const $row = $(this).closest('tr');
            saveAttendance($row);
        });

        let typingTimer;
        const doneTypingInterval = 700;

        $('#attendanceTable').on('input', 'input[name$="[notes]"]', function () {
            const $row = $(this).closest('tr');
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => {
                saveAttendance($row);
            }, doneTypingInterval);
        });

        $('#nameSearch').on('keyup', function () {
            table.column(1).search(this.value).draw();
        });
        // فلترة مخصصة للبحث في حالة الحضور بناءً على الراديو المختار
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            const selectedStatus = $('#statusSearch').val();
            if (selectedStatus === 'all' || !selectedStatus) {
                return true; // عرض كل الصفوف
            }
            // احصل على الصف الحالي في الجدول
            const rowNode = table.row(dataIndex).node();
            // ابحث عن الراديو المختار داخل الصف
            const status = $(rowNode).find('input.attendance-status:checked').val();
            return status === selectedStatus;
        });

        $('#statusSearch').on('change', function () {
            table.draw();
        });

        $('#noteSearch').on('keyup', function () {
            table.column(3).search(this.value).draw();
        });
    });
</script>
@endpush
