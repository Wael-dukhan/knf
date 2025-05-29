@extends('layouts.table-layout2')

@section('title', __('messages.school_details'))

@section('content')
<div class="">
    <div class="">
        <!-- Page Header -->
        <div class="">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">{{ __('messages.school_details') }}: {{ $school->name }}</h3>
                </div>
                
                <div class="col-auto">
                    <a href="{{ route('admin.grades.create', ['school_id' => $school->id]) }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ __('messages.create_grade') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- School Info -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title">{{ __('messages.basic_information') }}</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <strong>{{ __('messages.location') }}:</strong> {{ $school->location ?? '-' }}
                    </li>
                    <li class="list-group-item">
                        <strong>{{ __('messages.number_of_class_sections') }}:</strong>
                        {{ $school->grades->pluck('classSections')->flatten()->count() }}
                    </li>
                </ul>
            </div>
        </div>

        <!-- Grades & Students Table -->
        <div class="card card-table">
            <div class="card-body">
                <h5 class="card-title">{{ __('messages.grades_list') }}</h5>

                @if($school->grades->isEmpty())
                    <div class="alert alert-info">{{ __('messages.no_grades_available') }}</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-center mb-0" id="schoolsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('messages.grade_name') }} <input type="text" id="gradeSearch" class="form-control form-control-sm" placeholder="{{ __('messages.search') }}"></th>
                                    <th>{{ __('messages.number_of_class_sections') }} <input type="text" id="sectionsSearch" class="form-control form-control-sm" placeholder="{{ __('messages.search') }}"></th>
                                    <th>{{ __('messages.number_of_students') }} <input type="text" id="studentsSearch" class="form-control form-control-sm" placeholder="{{ __('messages.search') }}"></th>
                                    <th class="text-end">{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($school->grades as $index => $grade)
                                    @php
                                        $sectionIds = $grade->classSections->pluck('id');
                                        $studentCount = \App\Models\User::whereHas('roles', fn($q) => $q->where('name', 'student'))
                                            ->whereHas('classSections', fn($q) => $q->whereIn('class_sections.id', $sectionIds))
                                            ->count();
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $grade->name }}</td>
                                        <td>{{ $grade->classSections->count() }}</td>
                                        <td>{{ $studentCount }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.grades.show', $grade->id) }}" class="btn btn-sm bg-info-light me-2">
                                                <i class="feather-eye"></i> {{ __('messages.show') }}
                                            </a>
                                            <a href="{{ route('admin.grades.edit', $grade->id) }}" class="btn btn-warning btn-sm">{{ __('messages.edit') }}</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
                <div class="mt-4">
                    <a href="{{ route('admin.schools.index') }}" class="btn btn-secondary mt-3">
                        <i class="feather-arrow-left"></i> {{ __('messages.back') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            let table = $('#schoolsTable').DataTable({
                dom: 'Bfrtip',
                order: [[0, 'asc']],
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '{{ __("messages.export_excel") }}',
                        exportOptions: {
                            columns: ':visible:not(:last-child)'
                        },
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '{{ __("messages.export_pdf") }}',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        exportOptions: {
                            columns: ':visible:not(:last-child)'
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

                            $(win.document.body).find('table th:last-child, table td:last-child').css('display', 'none');
                        }
                    }
                ],
                language: {
                    search: "{{ __('messages.search') }}",
                    lengthMenu: "{{ __('messages.show') }} _MENU_",
                    info: "{{ __('messages.showing') }} _START_ {{ __('messages.to') }} _END_ {{ __('messages.of') }} _TOTAL_",
                    paginate: {
                        previous: "{{ __('messages.previous') }}",
                        next: "{{ __('messages.next') }}"
                    }
                }
            });

            // فلترة الأعمدة
            $('#gradeSearch').on('keyup', function () {
                table.column(1).search(this.value).draw();
            });

            $('#sectionsSearch').on('keyup', function () {
                table.column(2).search(this.value).draw();
            });

            $('#studentsSearch').on('keyup', function () {
                table.column(3).search(this.value).draw();
            });
        });
    </script>
@endpush
