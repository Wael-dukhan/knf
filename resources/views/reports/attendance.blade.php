@extends('layouts.reports')

@section('title', __('messages.student_attendance_report'))

@section('content')
<div class="mt-4">
    <style>
        .row.g-3.align-items-end.mb-4 {
            justify-content: center;
            column-gap: 80px;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.previous { float: right; }
        .dataTables_wrapper .dataTables_paginate .paginate_button.next { float: left; }
        .dataTables_wrapper .dataTables_paginate a.paginate_button {
            margin: 0 4px;
            padding: 6px 10px;
            border-radius: 4px;
            border: 1px solid transparent;
        }
        .dataTables_wrapper .dataTables_paginate a.paginate_button.current {
            background-color: #0d6efd;
            color: white !important;
            border-color: #0d6efd;
        }
        .dataTables_wrapper .dataTables_paginate a.paginate_button:hover {
            background-color: #e9ecef;
            color: #0d6efd !important;
        }
        div.dataTables_wrapper div.dataTables_info {
            padding-bottom: 25px;
        }
        @media screen and (min-width:1200px) and (max-width:1600px){
            table#attendanceTable {
                min-width: auto !important;
                width: auto !important;
            }
        }
    </style>

    <h3 class="mb-4 fw-bold text-primary">{{ __('messages.student_attendance_report') }}</h3>

    {{-- فلاتر البحث --}}
    <div class="row">
        <div class="col-md-2">
            <select id="school_id" class="form-control select2">
                <option value="">{{ __('messages.select_school') }}</option>
                @foreach($schools as $school)
                    <option value="{{ $school->id }}">{{ $school->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <select id="academic_year_id" class="form-control select2">
                <option value="">{{ __('messages.select_academic_year') }}</option>
            </select>
        </div>

        <div class="col-md-2">
            <select id="grade_id" class="form-control select2">
                <option value="">{{ __('messages.select_grade') }}</option>
            </select>
        </div>

        <div class="col-md-2">
            <select id="class_section_id" class="form-control select2">
                <option value="">{{ __('messages.select_class_section') }}</option>
            </select>
        </div>

        <div class="col-md-2">
            <select id="student_id" class="form-control select2">
                <option value="">{{ __('messages.select_student') }}</option>
            </select>
        </div>
    </div>

    {{-- جدول عرض الحضور --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-bordered table-striped table-hover align-middle" id="attendanceTable" style="min-width:1200px;">
                <thead class="table-primary text-center align-middle">
                    <tr>
                        <th>#</th>
                        <th>{{ __('messages.student') }}</th>
                        <th>{{ __('messages.school') }}</th>
                        <th>{{ __('messages.grade') }}</th>
                        <th>{{ __('messages.class_section') }}</th>
                        <th>{{ __('messages.academic_year') }}</th>
                        <th>{{ __('messages.term') }}</th>
                        <th>{{ __('messages.date') }}</th>
                        <th>{{ __('messages.status') }}</th>
                        <th>{{ __('messages.notes') }}</th>
                        <th>{{ __('messages.recorded_by') }}</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({ width: '100%' });

        var table = $('#attendanceTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("reports.student.attendance_report_data") }}',
                data: function(d) {
                    d.school_id = $('#school_id').val();
                    d.academic_year_id = $('#academic_year_id').val();
                    d.term_id = $('#term_id').val();
                    d.grade_id = $('#grade_id').val();
                    d.class_section_id = $('#class_section_id').val();
                    d.student_id = $('#student_id').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'student_name', name: 'student_name' },
                { data: 'school_name', name: 'school_name' },
                { data: 'grade_name', name: 'grade_name' },
                { data: 'class_section_name', name: 'class_section_name' },
                { data: 'academic_year_name', name: 'academic_year_name' },
                { data: 'term_name', name: 'term_name' },
                { data: 'date', name: 'date' },
                { data: 'status', name: 'status' },
                { data: 'notes', name: 'notes' },
                { data: 'recorded_by_name', name: 'recorded_by_name' }
            ],
            order: [[7, 'desc']], // ترتيب حسب التاريخ
            lengthMenu: [[10, 25, 50], [10, 25, 50]],
            language: {
                emptyTable: "{{ __('messages.empty_table') }}",
                processing: "{{ __('messages.loading') }}...",
                search: "{{ __('messages.search') }}:",
                lengthMenu: "{{ __('messages.show') }} _MENU_",
                info: "{{ __('messages.showing') }} _START_ {{ __('messages.to') }} _END_ {{ __('messages.of') }} _TOTAL_",
                paginate: {
                    previous: "<i class='fa fa-chevron-right'></i>",
                    next: "<i class='fa fa-chevron-left'></i>"
                }
            },
            responsive: true,
            dom: 'Bfltip',
            buttons: [
                { extend: 'excelHtml5', text: '{{ __("messages.export_excel") }}', className: 'btn btn-success btn-sm' },
                { extend: 'pdfHtml5', text: '{{ __("messages.export_pdf") }}', orientation: 'landscape', pageSize: 'A4' },
                { extend: 'print', text: '{{ __("messages.print") }}' }
            ]
        });

        $('#school_id, #academic_year_id, #term_id, #grade_id, #class_section_id, #student_id').change(function() {
            table.ajax.reload();
        });
        // dependent filters
        $('#school_id').change(function() {
            let schoolId = $(this).val();
            $('#academic_year_id').html('<option>{{ __("messages.loading") }}</option>').val('');
            $('#grade_id, #class_section_id, #student_id').html('<option value="">--</option>');

            if(schoolId) {
                $.get("{{ url('filters/academic-years') }}/" + schoolId, function(data) {
                    $('#academic_year_id').html('<option value="">{{ __("messages.select_academic_year") }}</option>');
                    $.each(data, function(i, item){
                        $('#academic_year_id').append('<option value="'+item.id+'">'+item.name+'</option>');
                    });
                });
            }
        });

        $('#academic_year_id').change(function() {
            let schoolId = $('#school_id').val();
            let yearId = $(this).val();
            $('#grade_id').html('<option>{{ __("messages.loading") }}</option>').val('');
            $('#class_section_id, #student_id').html('<option value="">--</option>');

            if(schoolId && yearId) {
                $.get("{{ url('/filters/grades/') }}/" + schoolId + '/' + yearId, function(data) {
                    $('#grade_id').html('<option value="">{{ __("messages.select_grade") }}</option>');
                    $.each(data, function(i, item){
                        $('#grade_id').append('<option value="'+item.id+'">'+item.name+'</option>');
                    });
                });
            }
        });

        $('#grade_id').change(function() {
            let gradeId = $(this).val();
            $('#class_section_id').html('<option>{{ __("messages.loading") }}</option>').val('');
            $('#student_id').html('<option value="">--</option>');

            if(gradeId) {
                $.get("{{ url('/filters/class-sections/') }}/" + gradeId, function(data) {
                    $('#class_section_id').html('<option value="">{{ __("messages.select_class_section") }}</option>');
                    $.each(data, function(i, item){
                        $('#class_section_id').append('<option value="'+item.id+'">'+item.name+'</option>');
                    });
                });
            }
        });

        $('#class_section_id').change(function() {
            let sectionId = $(this).val();
            $('#student_id').html('<option>{{ __("messages.loading") }}</option>').val('');

            if(sectionId) {
                $.get("{{ url('/filters/students/') }}/" + sectionId, function(data) {
                    $('#student_id').html('<option value="">{{ __("messages.select_student") }}</option>');
                    $.each(data, function(i, item){
                        $('#student_id').append('<option value="'+item.id+'">'+item.name+'</option>');
                    });
                });
            }
        });
    });
</script>
@endpush
