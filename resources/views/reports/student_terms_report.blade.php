@extends('layouts.reports')

@section('title', __('messages.student_terms_report'))

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
        @media screen and (max-width: 1600px){
            html[dir="rtl"] .page-wrapper {
                margin-right: 0%!important;
            }
        }
    </style>

    <h3 class="mb-4 fw-bold text-primary">{{ __('messages.student_terms_report') }}</h3>

    {{-- فلاتر البحث --}}
    <div class="row g-3 align-items-end mb-4">
        <div class="col-md-2">
            <label class="form-label fw-semibold">{{ __('messages.school') }}</label>
            <select id="school_id" class="form-select select2">
                <option value="">{{ __('messages.select_school') }}</option>
                @foreach($schools as $school)
                    <option value="{{ $school->id }}">{{ $school->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label fw-semibold">{{ __('messages.academic_year') }}</label>
            <select id="academic_year_id" class="form-select select2">
                <option value="">{{ __('messages.select_academic_year') }}</option>
                @foreach($academicYears as $year)
                    <option value="{{ $year->id }}">{{ $year->school->name }} - {{ $year->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label fw-semibold">{{ __('messages.term') }}</label>
            <select id="term_id" class="form-select select2">
                <option value="">{{ __('messages.select_term') }}</option>
                @foreach($terms as $term)
                    <option value="{{ $term->id }}">{{ $term->school->name }} - {{ $term->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label fw-semibold">{{ __('messages.grade') }}</label>
            <select id="grade_id" class="form-select select2">
                <option value="">{{ __('messages.select_grade') }}</option>
                @foreach($grades as $grade)
                    <option value="{{ $grade->id }}">{{ $grade->school->name }} - {{ $grade->name }} - {{ $grade->academicYear->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label fw-semibold">{{ __('messages.class_section') }}</label>
            <select id="class_section_id" class="form-select select2">
                <option value="">{{ __('messages.select_class_section') }}</option>
                @foreach($classSections as $classSection)
                    <option value="{{ $classSection->id }}">{{ $classSection->grade->school->name }} - {{ $classSection->grade->name }} - {{ $classSection->name }} [{{ $classSection->grade->academicYear->name }}]</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label fw-semibold">{{ __('messages.student') }}</label>
            <select id="student_id" class="form-select select2">
                <option value="">{{ __('messages.select_student') }}</option>
                @foreach($students as $student)
                    <option value="{{ $student->id }}">{{ $student->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- جدول عرض التقرير --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-bordered table-striped table-hover align-middle" id="termsTable" style="min-width:1000px;">
                <thead class="table-primary text-center align-middle">
                    <tr>
                        <th>#</th>
                        <th>{{ __('messages.student') }}</th>
                        <th>{{ __('messages.school') }}</th>
                        <th>{{ __('messages.grade') }}</th>
                        <th>{{ __('messages.class_section') }}</th>
                        <th>{{ __('messages.academic_year') }}</th>
                        <th>{{ __('messages.term') }}</th>
                        <th>{{ __('messages.average_score') }}</th>
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

        var table = $('#termsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("reports.student.terms_report_data") }}',
                data: function(d) {
                    d.school_id = $('#school_id').val();
                    d.academic_year_id = $('#academic_year_id').val();
                    d.term_id = $('#term_id').val();
                    d.grade_id = $('#grade_id').val();
                    d.class_section_id = $('#class_section_id').val();
                    d.student_id = $('#student_id').val();
                    console.log(d.academic_year_id);
                },
                dataSrc: function(json) {
                    console.log('Ajax Response:', json); // طباعة استجابة السيرفر في الكونسول
                    return json.data; // مهم أن تعيد الصفوف لـ DataTables
                },
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'student_name', name: 'student_name' },
                { data: 'school_name', name: 'school_name' },
                { data: 'grade_name', name: 'grade_name' },
                { data: 'class_section_name', name: 'class_section_name' },
                { data: 'academic_year_name', name: 'academic_year_name' },
                { data: 'term_name', name: 'term_name' },
                { data: 'average_score', name: 'average_score' }
            ],
            order: [[1, 'asc']],
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
            dom: 'Bfltip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '{{ __("messages.export_excel") }}',
                    className: 'btn btn-success btn-sm',
                    exportOptions: {
                        columns: ':visible:not(:first-child)'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '{{ __("messages.export_pdf") }}',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: ':visible'
                    },
                    customize: function (doc) {
                        doc.defaultStyle.alignment = 'right';
                        doc.styles.tableHeader.alignment = 'center';
                    }
                },
                {
                    extend: 'print',
                    text: '{{ __("messages.print") }}',
                    customize: function (win) {
                        $(win.document.body)
                            .css('direction', 'rtl')
                            .css('text-align', 'right')
                            .find('table')
                            .addClass('table table-bordered');
                    }
                }
            ]
        });

        $('#school_id, #academic_year_id, #term_id, #grade_id, #class_section_id, #student_id').change(function() {
            console.log('Academic year changed:', $(this).val());
            table.ajax.reload();
        });
    });
</script>
@endpush
