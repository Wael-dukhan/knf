@extends('layouts.table-layout2')

@section('title', __('messages.grades_list'))

@section('content')
    <div class="card-body" style="background-color: white">
        <div class="d-flex justify-content-between">
            <h2>{{ __('messages.grades_list') }}</h2>
            <a href="{{ route('admin.grades.create') }}"
            class="btn btn-primary mb-3">{{ __('messages.create_new_grade') }}</a>

        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif


        <div id="pdfContent">
            <table id="gradesTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('messages.name') }}</th>
                        <th>{{ __('messages.description') }}</th>
                        <th>{{ __('messages.academic_year') }}</th>
                        <th>{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($grades as $index => $grade)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $grade->name }}</td>
                            <td>{{ $grade->description ?? '-' }}</td>
                            <td>{{ $grade->academicYear->name ?? '-' }}</td>
                            <td>
                                <a href="{{ route('admin.grades.edit', $grade->id) }}"
                                    class="btn btn-warning btn-sm">{{ __('messages.edit') }}</a>
                                <form action="{{ route('admin.grades.destroy', $grade->id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('{{ __('messages.are_you_sure') }}')">{{ __('messages.delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#gradesTable').DataTable({
                dom: 'Bfrtip',
                order: [[0, 'asc']],
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
        });
    </script>
@endpush