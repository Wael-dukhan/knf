@extends('layouts.table-layout2')

@section('title', __('messages.class_section_details'))

@section('content')
    <div class="container mt-5">
        <style>
            table.table-bordered.dataTable th, table.table-bordered.dataTable td {
                text-align: center;
            }
        </style>

        <div class="card shadow-sm p-4 mb-4" style="background-color: #fff;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    {{ __('messages.class_section_details') }}: <span class="text-primary">{{ $grade->school->name }} - {{ $grade->name }} - {{ $class_section->name }} [ {{ $grade->academicYear->name }} ]</span>
                </h2>
                <a href="{{ route('student.assign.create', ['gradeId' => $grade->id]) }}" class="btn btn-outline-primary">
                    <i class="feather-list"></i> {{ __('messages.assign_student_to_class_section') }}
                </a>
            </div>

            @if($students->isEmpty())
                <div class="alert alert-info text-center">
                    {{ __('messages.no_students_in_class') }}
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle" id="studentsTable">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>{{ __('messages.student_name') }}</th>
                                <th>{{ __('messages.student_email') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.actions') }}</th>
                            </tr>
                            <tr>
                                <th></th>
                                <th>
                                    <input type="text" class="column_search" data-column="1" placeholder="{{ __('messages.search') }}">
                                </th>
                                <th>
                                    <input type="text" class="column_search" data-column="2" placeholder="{{ __('messages.search') }}">
                                </th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $student->student_name }}</td>
                                    <td>{{ $student->email }}</td>
                                    <td>
                                        <select class="status-select" data-student-id="{{ $student->student_id }}" data-class-section-id="{{ $class_section->id }}">
                                            <option value="active" {{ $student->status == 'active' ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                                            <option value="dropout" {{ $student->status == 'dropout' ? 'selected' : '' }}>{{ __('messages.dropout') }}</option>
                                            <option value="expelled" {{ $student->status == 'expelled' ? 'selected' : '' }}>{{ __('messages.expelled') }}</option>
                                            <option value="transferred_to_another_school" {{ $student->status == 'transferred_to_another_school' ? 'selected' : '' }}>{{ __('messages.transferred_to_another_school') }}</option>
                                            <option value="temporarily_suspended" {{ $student->status == 'temporarily_suspended' ? 'selected' : '' }}>{{ __('messages.temporarily_suspended') }}</option>
                                        </select>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.users.show', $student->id) }}" class="btn btn-warning btn-sm">{{ __('messages.view_student') }}</a>
                                        <form action="{{ route('student.class_sections.delete',[$class_section->id , $student->id]) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('{{ __('messages.are_you_sure') }}')">{{ __('messages.delete') }}</button>
                                        </form>
                                        <a href="{{ route('student.class_sections.edit', $student->student_id) }}" class="btn btn-secondary btn-sm">{{ __('messages.move') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="mt-4">
                <a href="{{ route('admin.grades.show', $class_section->id) }}" class="btn btn-secondary mt-3">
                    <i class="feather-arrow-left"></i> {{ __('messages.back') }}
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            var table = $('#studentsTable').DataTable({
                dom: 'Bfrtip',
                order: [[0, 'asc']],
                orderCellsTop: true,
                fixedHeader: true,
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '{{ __("messages.export_excel") }}',
                        exportOptions: {
                            columns: ':visible:not(:last-child)'
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

            // Custom search for each column
            $('input.column_search').on('keyup', function () {
                var columnIndex = $(this).data('column');
                table.column(columnIndex).search(this.value).draw();
            });

            $('.status-select').change(function() {
                var studentId = $(this).data('student-id');
                var classSectionId = $(this).data('class-section-id');
                var newStatus = $(this).val();
                // console.log(newStatus);
                $.ajax({
                url: '/knf/public/students/' + studentId + '/update-status',
                method: 'POST',
                data: {
                    status: newStatus,
                    class_section_id: classSectionId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    // alert('Status updated successfully!');  
                },
                error: function(xhr) {
                    // alert('Failed to update status.');
                }
                });
            });
        });
    </script>
@endpush
