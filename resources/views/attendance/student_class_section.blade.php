    @extends('layouts.table-layout2')

    @section('title', __('messages.attendance_for_section', ['section' => $classSection->name]))

    @section('content')
    <div class="">
        <div class="">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    {{ __('messages.class_section_details') }}: <span class="text-primary">{{ $classSection->grade->school->name }} - {{ $classSection->grade->name }} - {{ $classSection->name }} [ {{ $classSection->grade->academicYear->name }} ]</span>
                </h2>
                <div class="d-flex">
                    <button type="button" class="btn btn-outline-secondary" onclick="adjustDate(-1)">
                        {{ __('messages.previous_day') }}
                    </button>

                    <input type="date" id="date" name="date" class="form-control text-center" 
                        style="max-width: 200px;" value="{{ request('date', date('Y-m-d')) }}">

                    <button type="button" class="btn btn-outline-secondary" onclick="adjustDate(1)">
                        {{ __('messages.next_day') }}
                    </button>
                </div>

                <script>
                    function adjustDate(offset) {
                        const dateInput = document.getElementById('date');
                        const date = new Date(dateInput.value);
                        date.setDate(date.getDate() + offset);
                        const newDateStr = date.toISOString().split('T')[0];
                        dateInput.value = newDateStr;
                        dateInput.dispatchEvent(new Event('change')); // Trigger the reload
                    }

                    document.getElementById('date').addEventListener('change', function () {
                        const selectedDate = this.value;
                        const params = new URLSearchParams(window.location.search);
                        params.set('date', selectedDate);
                        window.location.search = params.toString(); // reload with date
                    });
                </script>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form>
                @csrf
                <div class="row">
                    <div class="col-md-6" style="margin: auto">
                        <h2>
                            {{ __('messages.attendance_log') }} [ {{ $dayName }} ]
                        </h2>
                    </div>
                    <div class="mb-4 col-md-3">
                        <label class="form-label">{{ __('messages.term') }}</label>
                        
                        {{-- إدخال مخفي يحتوي على ID الخاص بالفصل الدراسي --}}
                        <input type="hidden" id="term_id" name="term_id" value="{{ $currentTerm->id }}">

                        {{-- عرض اسم الفصل الدراسي --}}
                        <div class="form-control bg-light">
                            {{ $currentTerm->name }}
                        </div>
                    </div>



                </div>
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
                                @foreach($students as $index => $student)
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
                            <a href="{{ route('admin.grades.show', $classSection->id) }}" class="btn btn-secondary mt-3">
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
            ],
            columnDefs: [
                {
                    targets: 3, // عمود الملاحظات
                    render: function (data, type, row, meta) {
                        if (type === 'filter') {
                            const inputElement = $('<div>').html(data).find('input').val();
                            return inputElement || '';
                        }
                        return data;
                    }
                }
            ],
        });

        // تحديث data-status مبدئيًا
        $('#attendanceTable tbody tr').each(function () {
            const selectedStatus = $(this).find('.attendance-status:checked').val();
            $(this).attr('data-status', selectedStatus);
        });

        // دالة عامة لحفظ البيانات
        function saveAttendance($row) {
            const studentId = $row.data('student-id');
            const selectedStatus = $row.find('.attendance-status:checked').val();
            const notes = $row.find('input[name^="attendance"][name$="[notes]"]').val();
            const date = $('#date').val();
            const classSectionId = {{ $classSection->id }};
            const termId = $('#term_id').val();

            $.ajax({
                url: '{{ route("attendance.ajaxUpdate") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    student_id: studentId,
                    status: selectedStatus,
                    date: date,
                    class_section_id: classSectionId,
                    term_id: termId,
                    notes: notes
                },
                success: function (response) {
                    console.log('تم الحفظ بنجاح:', response.message);
                },
                error: function (xhr) {
                    console.error('خطأ في الحفظ:', xhr.responseText);
                    alert('حدث خطأ أثناء حفظ الحضور');
                }
            });
        }

        // Debounce Function (تأخير التنفيذ حتى انتهاء الكتابة)
        function debounce(fn, delay) {
            let timer = null;
            return function () {
                clearTimeout(timer);
                const context = this;
                const args = arguments;
                timer = setTimeout(function () {
                    fn.apply(context, args);
                }, delay);
            };
        }

        // عند تغيير الحالة
        $('#attendanceTable').on('change', '.attendance-status', function () {
            const $row = $(this).closest('tr');
            const selectedStatus = $row.find('.attendance-status:checked').val();
            $row.attr('data-status', selectedStatus);
            table.draw();
            saveAttendance($row);
        });

        // عند الكتابة في الملاحظات (مع تأخير 500ms)
        const debouncedSave = debounce(function () {
            const $row = $(this).closest('tr');
            saveAttendance($row);
        }, 500);

        $('#attendanceTable').on('input', 'input[name^="attendance"][name$="[notes]"]', debouncedSave);

        // الفلاتر
        $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            const selectedStatus = $('#statusSearch').val();
            if (selectedStatus === 'all') return true;
            const rowNode = table.row(dataIndex).node();
            const rowStatus = $(rowNode).attr('data-status');
            return rowStatus === selectedStatus;
        });

        $('#statusSearch').on('change', function () {
            table.draw();
        });

        $('#nameSearch').on('keyup', function () {
            table.column(1).search(this.value).draw();
        });

        $('#noteSearch').on('keyup', function () {
            table.column(3).search(this.value).draw();
        });
    });
    </script>
    @endpush
