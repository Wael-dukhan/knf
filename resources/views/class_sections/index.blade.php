@extends('layouts.table-layout2')

@section('title', __('Class Sections List'))

@section('content')
    <style>
        #classSectionsTable_wrapper {
            background: #fff;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
        }

        #classSectionsTable thead {
            background-color: #f1f3f5;
            color: #212529;
        }

        #classSectionsTable tbody td {
            padding: 0.85rem;
            font-size: 0.9rem;
            vertical-align: middle;
        }

        #classSectionsTable tbody tr:hover {
            background-color: #f8f9fa;
            transition: background-color 0.2s ease-in-out;
        }

        .dt-buttons .btn {
            margin: 0 0.3rem 1rem 0;
            border-radius: 0.375rem;
            font-size: 0.875rem;
        }
    </style>

    <h2>{{ __('Class Sections List') }}</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('admin.class_sections.create') }}" class="btn btn-primary mb-3">{{ __('Create New Class Section') }}</a>

    <table id="classSectionsTable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('Section Name') }}</th>
                <th>{{ __('Grade') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sections as $index => $section)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $section->name }}</td>
                    <td>{{ $section->grade->name ?? '-' }}</td>
                    <td>
                        <a href="{{ route('admin.class_sections.edit', $section->id) }}" class="btn btn-warning btn-sm">{{ __('Edit') }}</a>
                        <form action="{{ route('admin.class_sections.destroy', $section->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('{{ __('Are you sure?') }}')">{{ __('Delete') }}</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#classSectionsTable').DataTable({
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
                    search: "{{ __('Search') }}",
                    lengthMenu: "{{ __('Show') }} _MENU_",
                    info: "{{ __('Showing') }} _START_ {{ __('to') }} _END_ {{ __('of') }} _TOTAL_",
                    paginate: {
                        previous: "{{ __('Previous') }}",
                        next: "{{ __('Next') }}"
                    }
                }
            });
        });
    </script>
@endpush
