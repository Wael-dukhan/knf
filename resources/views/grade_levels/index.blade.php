@extends('layouts.table-layout2')

@section('title', __('messages.grade_levels') . ' - ' . $school->name)

@section('content')
<div class="card">
    <div class="col-auto d-flex justify-content-between ms-auto" style="width: 96%;">
        <h3 class="card-title">{{ __('messages.grade_levels') }} - {{ $school->name }}</h3>
    </div>
    <div class="card-body">
        @if(empty($gradeLevels))
            <div class="alert alert-info">{{ __('messages.no_grade_levels_available') }}</div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="gradeLevelsTable">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('messages.grade_level_name') }}</th>
                            <th class="">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($gradeLevels as $index => $level)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $level }}</td>
                                <td class="text-end">
                                    <div class="actions">
                                        <a href="{{ route('grade_levels.show', [$school->id , $index ]) }}" class="btn btn-sm bg-info-light me-2">
                                            <i class="feather-eye"></i> {{ __('messages.view_grades') }}
                                        </a>
                                        {{-- <form action="{{ route('admin.schools.destroy', $school->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm bg-danger-light" onclick="return confirm('{{ __('messages.confirm_delete') }}')">
                                                <i class="feather-trash-2"></i> {{ __('messages.delete') }}
                                            </button>
                                        </form> --}}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <a href="{{ route('admin.schools.show', $school->id) }}" class="btn btn-secondary mt-3">
            <i class="feather-arrow-left"></i> {{ __('messages.back') }}
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#gradeLevelsTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '{{ __("messages.export_excel") }}',
                    exportOptions: { columns: ':visible' }
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
