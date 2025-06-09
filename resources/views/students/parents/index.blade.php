@extends('layouts.table-layout2')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>{{ __('messages.students_and_parents') }}</h2>
        <div>
            <a href="{{ route('students.parents.create') }}" class="btn btn-primary">
                <i class="fas fa-users"></i> {{ __('messages.assign_parents_for_student') }}
            </a>
        </div>
    </div>

    <table class="table table-striped table-bordered" id="studentsTable">
        <thead>
            <tr>
                <th>{{ __('messages.student_name') }}</th>
                <th>{{ __('messages.assigned_parents') }}</th>
                <th>{{ __('messages.school') }}</th>
                <th>{{ __('messages.actions') }}</th>
            </tr>
            <tr>
                <th><input type="text" id="studentSearch" placeholder="{{ __('messages.search_student') }}" class="form-control form-control-sm" /></th>
                <th><input type="text" id="parentSearch" placeholder="{{ __('messages.search_parent') }}" class="form-control form-control-sm" /></th>
                <th><input type="text" id="schoolSearch" placeholder="{{ __('messages.search_school') }}" class="form-control form-control-sm" /></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $student)
                <tr>
                    <td>{{ $student->name }}</td>
                    <td>
                        @if($student->parents->count() > 0)
                            @foreach($student->parents as $parent)
                                <span>{{ $parent->name }}</span><br>
                            @endforeach
                        @else
                            <span class="text-muted">{{ __('messages.no_parents_assigned') }}</span>
                        @endif
                    </td>
                    <td>{{ $student->school->name ?? __('messages.no_school') }}</td>
                    <td>
                        @foreach($student->parents as $parent)
                            <a href="{{ route('students.parents.edit', ['student' => $student->id, 'parent' => $parent->id]) }}" class="btn btn-warning btn-sm mb-1">
                                <i class="fas fa-edit"></i> {{ __('messages.edit') }}
                            </a>
                            <form action="{{ route('students.parents.destroy', ['student' => $student->id, 'parent' => $parent->id]) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm mb-1" onclick="return confirm('{{ __('messages.confirm_delete') }}')">
                                    <i class="fas fa-trash"></i> {{ __('messages.remove') }}
                                </button>
                            </form>
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        let table = $('#studentsTable').DataTable({
            dom: 'Bfrtip',
            orderCellsTop: true,
            fixedHeader: true,
            order: [[0, 'asc']],
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '{{ __("messages.export_excel") }}',
                    exportOptions: { columns: ':visible:not(:last-child)' }
                },
                {
                    extend: 'print',
                    text: '{{ __("messages.print") }}',
                    customize: function (win) {
                        $(win.document.body).css('direction', 'rtl').css('text-align', 'right')
                            .find('table').addClass('table table-bordered');
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

        // البحث الجزئي لاسم الطالب
        $('#studentSearch').on('keyup change', function () {
            table.column(0).search(this.value, true, false).draw();
        });
        $('#schoolSearch').on('keyup change', function () {
            table.column(2).search(this.value, true, false).draw();
        });
        // البحث الجزئي لاسم ولي الأمر
        $('#parentSearch').on('keyup change', function () {
            table.column(1).search(this.value, true, false).draw();
        });
    });
</script>

@endpush
