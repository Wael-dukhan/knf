@extends('layouts.reports')

@section('title', __('messages.student_grades_report_in_materials'))

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
    <h3 class="mb-4 fw-bold text-primary">{{ __('messages.student_grades_report_in_materials') }}</h3>

    {{-- فلاتر البحث الرئيسية --}}
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
                    <option value="{{ $classSection->id }}">{{ $classSection->grade->school->name }} - {{ $classSection->grade->name }} - {{ $classSection->name }} [  {{ $classSection->grade->academicYear->name }} ]</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label fw-semibold">{{ __('messages.material') }}</label>
            <select id="material_id" class="form-select select2">
                <option value="">{{ __('messages.select_material') }}</option>
                @foreach($materials as $material)
                    <option value="{{ $material->id }}">{{ $material->grade->school->name }} - {{ $material->grade->name }} - {{ $material->name }} - {{ $material->grade->academicYear->name }}</option>
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

    {{-- جدول عرض النتائج --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-bordered table-striped table-hover align-middle" id="marksTable" style="min-width:1300px;">
                <thead class="table-primary text-center align-middle">
                    <tr>
                        <th>#</th>
                        <th>{{ __('messages.student') }}</th>
                        <th>{{ __('messages.school') }}</th>
                        <th>{{ __('messages.grade') }}</th>
                        <th>{{ __('messages.class_section') }}</th>
                        <th>{{ __('messages.subject') }}</th>
                        <th>{{ __('messages.academic_year') }}</th>
                        <th>{{ __('messages.term') }}</th>
                        <th>{{ __('messages.oral_mark') }}</th>
                        <th>{{ __('messages.homework_mark') }}</th>
                        <th>{{ __('messages.first_study_mark') }}</th>
                        <th>{{ __('messages.second_study_mark') }}</th>
                        <th>{{ __('messages.work_total') }}</th>
                        <th>{{ __('messages.oral_exam_mark') }}</th>
                        <th>{{ __('messages.written_exam_mark') }}</th>
                        <th>{{ __('messages.term_total') }}</th>
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

        var table = $('#marksTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("reports.student.marks_report_data") }}',
                data: function(d) {
                    d.school_id = $('#school_id').val();
                    d.academic_year_id = $('#academic_year_id').val();
                    d.term_id = $('#term_id').val();
                    d.grade_id = $('#grade_id').val();
                    d.class_section_id = $('#class_section_id').val();
                    d.material_id = $('#material_id').val();
                    d.student_id = $('#student_id').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'student_name', name: 'student_name' },
                { data: 'school_name', name: 'school_name' },
                { data: 'grade_name', name: 'grade_name' },
                { data: 'class_section_name', name: 'class_section_name' },
                { data: 'material_name', name: 'material_name' },
                { data: 'academic_year_name', name: 'academic_year_name' },
                { data: 'term_name', name: 'term_name' },
                { data: 'oral_mark', name: 'oral_mark' },
                { data: 'homework_mark', name: 'homework_mark' },
                { data: 'first_study_mark', name: 'first_study_mark' },
                { data: 'second_study_mark', name: 'second_study_mark' },
                { data: 'work_total', name: 'work_total' },
                { data: 'oral_exam_mark', name: 'oral_exam_mark' },
                { data: 'written_exam_mark', name: 'written_exam_mark' },
                { data: 'term_total', name: 'term_total' }
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
            responsive: true,
             dom: 'Bfltip',  // مكان ظهور الأزرار: Buttons + filter + length + table + info + pagination
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '{{ __("messages.export_excel") }}',
                    className: 'btn btn-success btn-sm',
                    exportOptions: {
                        columns: ':visible:not(:first-child)' // استثناء عمود الأرقام التسلسلية لو تريد
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '{{ __("messages.export_pdf") }}',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        //columns: ':visible:not(:last-child)'
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

                        // $(win.document.body).find('table th:last-child, table td:last-child').css('display', 'none');
                    }
                }
            ]
        });
        console.log(window.innerWidth);
        $('#school_id, #academic_year_id, #term_id, #grade_id, #class_section_id, #material_id, #student_id').change(function() {
            table.ajax.reload();
        });
        $(document).ready(function() {
    $('.select2').select2({ width: '100%' });

    // عند تغيير المدرسة
    $('#school_id').change(function() {
        let schoolId = $(this).val();
        $('#academic_year_id').empty().append('<option value="">اختر السنة الدراسية</option>');
        $('#grade_id').empty().append('<option value="">اختر الصف</option>');
        $('#class_section_id').empty().append('<option value="">اختر الشعبة</option>');
        $('#material_id').empty().append('<option value="">اختر المادة</option>');
        $('#student_id').empty().append('<option value="">اختر الطالب</option>');

        if (schoolId) {
            $.get("{{ route('filters.academic-years', ':id') }}".replace(':id', schoolId), function(data) {
                data.forEach(function(item) {
                    $('#academic_year_id').append(`<option value="${item.id}">${item.name}</option>`);
                });
            });
        }
    });

    // عند تغيير السنة الدراسية
    $('#academic_year_id').change(function() {
        let academicYearId = $(this).val();
        let schoolId = $('#school_id').val();

        $('#term_id').empty().append('<option value="">اختر الفصل</option>');
        $('#grade_id').empty().append('<option value="">اختر الصف</option>');
        $('#class_section_id').empty().append('<option value="">اختر الشعبة</option>');
        $('#material_id').empty().append('<option value="">اختر المادة</option>');
        $('#student_id').empty().append('<option value="">اختر الطالب</option>');

        if (academicYearId) {
            $.get("{{ route('filters.terms', ':id') }}".replace(':id', academicYearId), function(data) {
                data.forEach(function(item) {
                    $('#term_id').append(`<option value="${item.id}">${item.name}</option>`);
                });
            });

            $.get("{{ route('filters.grades', [':school', ':year']) }}"
                .replace(':school', schoolId)
                .replace(':year', academicYearId), function(data) {
                data.forEach(function(item) {
                    $('#grade_id').append(`<option value="${item.id}">${item.name}</option>`);
                });
            });
        }
    });

    // عند تغيير الصف
    $('#grade_id').change(function() {
        let gradeId = $(this).val();

        $('#class_section_id').empty().append('<option value="">اختر الشعبة</option>');
        $('#material_id').empty().append('<option value="">اختر المادة</option>');
        $('#student_id').empty().append('<option value="">اختر الطالب</option>');

        if (gradeId) {
            $.get("{{ route('filters.class-sections', ':id') }}".replace(':id', gradeId), function(data) {
                data.forEach(function(item) {
                    $('#class_section_id').append(`<option value="${item.id}">${item.name}</option>`);
                });
            });

            $.get("{{ route('filters.materials', ':id') }}".replace(':id', gradeId), function(data) {
                data.forEach(function(item) {
                    $('#material_id').append(`<option value="${item.id}">${item.name}</option>`);
                });
            });
        }
    });

    // عند تغيير الشعبة
    $('#class_section_id').change(function() {
        let sectionId = $(this).val();
        $('#student_id').empty().append('<option value="">اختر الطالب</option>');

        if (sectionId) {
            $.get("{{ route('filters.students', ':id') }}".replace(':id', sectionId), function(data) {
                data.forEach(function(item) {
                    $('#student_id').append(`<option value="${item.id}">${item.name}</option>`);
                });
            });
        }
    });
});

    });
</script>
@endpush
