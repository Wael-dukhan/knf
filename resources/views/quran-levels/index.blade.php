@extends('layouts.table-layout2')

@section('title', __('quran_levels.list_title'))

@section('content')
<div class="card-body" style="background-color: white">
    <div class="d-flex justify-content-between">
        <h2>{{ __('quran_levels.list_title') }}</h2>
        <a href="{{ route('quran-levels.create') }}" class="btn btn-primary mb-3">
            {{ __('quran_levels.create_new') }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div id="pdfContent">
        <table id="quranLevelsTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('quran_levels.name') }}</th>
                    <th>{{ __('quran_levels.description') }}</th>
                    <th>{{ __('quran_levels.academic_year') }}</th>
                    <th>{{ __('quran_levels.school') }}</th>
                    <th>{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quranLevels as $index => $level)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $level->name }}</td>
                        <td>{{ $level->description ?? '-' }}</td>
                        <td>{{ $level->academicYear->name ?? '-' }}</td>
                        <td>{{ $level->school->name ?? '-' }}</td>
                        <td>
                            <a href="{{ route('quran-levels.show', $level->id) }}" class="btn btn-sm btn-info">
                                {{ __('messages.show') }}
                            </a>
                            <a href="{{ route('quran-levels.edit', $level->id) }}" class="btn btn-warning btn-sm">
                                {{ __('messages.edit') }}
                            </a>
                            <form action="{{ route('quran-levels.destroy', $level->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" 
                                    onclick="return confirm('{{ __('messages.are_you_sure') }}')">
                                    {{ __('messages.delete') }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="col-auto text-end float-end ms-auto">
    <a href="{{ route('admin.schools.index') }}" class="btn btn-secondary">
        <i class="feather-arrow-left"></i> {{ __('messages.back') }}
    </a>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#quranLevelsTable').DataTable({
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
                            .css('direction', '{{ app()->getLocale() === "ar" ? "rtl" : "ltr" }}')
                            .css('text-align', '{{ app()->getLocale() === "ar" ? "right" : "left" }}')
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
