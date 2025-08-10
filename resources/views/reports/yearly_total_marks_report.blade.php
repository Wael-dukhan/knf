@extends('layouts.reports')

@section('content')
<div class="">
    <h2 class="mb-4">{{ __('messages.yearly_report_title') }}</h2>
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
    <div class="row mb-3">
        <div class="col-md-3">
            <label for="school_id">{{ __('messages.school') }}</label>
            <select id="school_id" class="form-control select2">
                <option value="">{{ __('messages.select_school') }}</option>
                @foreach($schools as $school)
                    <option value="{{ $school->id }}" {{ (isset($schoolId) && $schoolId == $school->id) ? 'selected' : '' }}>{{ $school->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label for="academic_year_id">{{ __('messages.academic_year') }}</label>
            <select id="academic_year_id" class="form-control select2">
                <option value="">{{ __('messages.select_academic_year') }}</option>
                @foreach($academicYears as $year)
                    <option value="{{ $year->id }}" {{ (isset($academicYearId) && $academicYearId == $year->id) ? 'selected' : '' }}>{{ $year->school->name }} - {{ $year->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label for="grade_id">{{ __('messages.grade') }}</label>
            <select id="grade_id" class="form-control select2">
                <option value="">{{ __('messages.select_grade') }}</option>
                @foreach($grades as $grade)
                    <option value="{{ $grade->id }}" {{ (isset($gradeId) && $gradeId == $grade->id) ? 'selected' : '' }}>{{ $grade->school->name }} - {{ $grade->name }} - {{ $grade->academicYear->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label for="class_section_id">{{ __('messages.class_section') }}</label>
            <select id="class_section_id" class="form-control select2">
                <option value="">{{ __('messages.select_class_section') }}</option>
                @foreach($classSections as $section)
                    <option value="{{ $section->id }}" {{ (isset($classSectionId) && $classSectionId == $section->id) ? 'selected' : '' }}>{{ $section->grade->school->name }} - {{ $section->grade->name }} - {{ $section->name }} [{{ $section->grade->academicYear->name }}]</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <label for="student_id">{{ __('messages.student') }}</label>
            <select id="student_id" class="form-control select2">
                <option value="">{{ __('messages.select_student') }}</option>
                @foreach($students as $student)
                    <option value="{{ $student->id }}" {{ (isset($studentId) && $studentId == $student->id) ? 'selected' : '' }}>{{ $student->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table id="yearlyReportTable" class="table table-bordered table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('messages.student_name') }}</th>
                        <th>{{ __('messages.school_name') }}</th>
                        <th>{{ __('messages.grade_name') }}</th>
                        <th>{{ __('messages.class_section_name') }}</th>
                        <th>{{ __('messages.academic_year_name') }}</th>
                        <th>{{ __('messages.yearly_average_score') }}</th>
                        <th>{{ __('messages.material_count') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.select2').select2({ width: '100%' });

    var table = $('#yearlyReportTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("reports.yearly_total_marks_data") }}',
            data: function(d) {
                d.school_id = $('#school_id').val();
                d.academic_year_id = $('#academic_year_id').val();
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
            { data: 'average_score', name: 'average_score' },
            { data: 'material_count', name: 'material_count' }
        ],
        order: [[1, 'asc']],
        lengthMenu: [[10, 25, 50], [10, 25, 50]],
        language: {
            emptyTable: "{{ __('messages.empty_table') }}",
            processing: "{{ __('messages.loading') }}",
            search: "{{ __('messages.search') }}",
            lengthMenu: "{{ __('messages.show') }} _MENU_",
            info: "{{ __('messages.showing') }} _START_ {{ __('messages.to') }} _END_ {{ __('messages.of') }} _TOTAL_",
            paginate: {
                previous: "{{ __('messages.previous') }}",
                next: "{{ __('messages.next') }}"
            }
        },
        dom: 'Bfltip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '{{ __("messages.export_excel") }}',
                className: 'btn btn-success btn-sm',
                exportOptions: { columns: ':visible:not(:first-child)' }
            },
            {
                extend: 'pdfHtml5',
                text: '{{ __("messages.export_pdf") }}',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: { columns: ':visible' },
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

    $('#school_id, #academic_year_id, #grade_id, #class_section_id, #student_id').on('change', function() {
        table.ajax.reload();
    });
});
</script>
@endpush
