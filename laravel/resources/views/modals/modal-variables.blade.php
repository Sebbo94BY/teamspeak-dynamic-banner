<div class="modal fade" id="modalAvailableVariables-{{$instanceVariableModal->id}}" tabindex="-1" aria-labelledby="modalAvailableVariables-{{$instanceVariableModal->id}}-Label" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-bold" id="modalAvailableVariables-{{$instanceVariableModal->id}}-Label">{{ __('views/modals/modal-variables.available_variables') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if (! is_null($instanceVariableModal->variables()['redis_connection_error']))
                    <div class="alert alert-warning" role="alert">
                        {{$instanceVariableModal->variables()['redis_connection_error']}}
                    </div>
                @endif
                <div class="col-lg-12 table-responsive">
                    <table class="table table-striped" id="availableVariables-{{ $instanceVariableModal->id }}">
                        <thead>
                        <tr>
                            <th scope="col">{{ __('views/modals/modal-variables.table_variable_name') }}</th>
                            <th scope="col">{{ __('views/modals/modal-variables.table_variable_value') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if (isset($twitch_streamer_variables))
                            @foreach ($twitch_streamer_variables as $key => $value)
                                <tr>
                                    <td><code>%{{ $key }}%</code></td>
                                    <td>{{ $value }}</td>
                                </tr>
                            @endforeach
                        @endif

                        @foreach($instanceVariableModal->variables()['variables_and_values'] as $key => $value)
                            <tr>
                                <td><code>%{{ $key }}%</code></td>
                                <td>{{ $value }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('views/modals/modal-variables.dismiss_button') }}</button>
            </div>
        </div>
    </div>
</div>

<script type="module">
    window.addEventListener('load', function () {
        const variablesModal = document.getElementById('modalAvailableVariables-{{ $instanceVariableModal->id }}');
        const variablesTable = $('#availableVariables-{{ $instanceVariableModal->id }}');
        let variablesDataTable;

        variablesModal.addEventListener('shown.bs.modal', function () {
            if (!variablesDataTable) {
                variablesDataTable = variablesTable.DataTable({
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, {{ Illuminate\Support\Js::from(__('views/modals/modal-variables.datatable_all')) }}]],
                    language: {
                        emptyTable: {{ Illuminate\Support\Js::from(__('views/modals/modal-variables.datatable_empty')) }},
                        info: {{ Illuminate\Support\Js::from(__('views/modals/modal-variables.datatable_info')) }},
                        infoEmpty: {{ Illuminate\Support\Js::from(__('views/modals/modal-variables.datatable_info_empty')) }},
                        lengthMenu: {{ Illuminate\Support\Js::from(__('views/modals/modal-variables.datatable_length_menu')) }},
                        search: {{ Illuminate\Support\Js::from(__('views/modals/modal-variables.datatable_search')) }},
                        zeroRecords: {{ Illuminate\Support\Js::from(__('views/modals/modal-variables.datatable_zero_records')) }},
                        paginate: {
                            previous: {{ Illuminate\Support\Js::from(__('views/modals/modal-variables.datatable_previous')) }},
                            next: {{ Illuminate\Support\Js::from(__('views/modals/modal-variables.datatable_next')) }},
                        },
                    },
                });
            } else {
                variablesDataTable.columns.adjust();
            }
        });
    });
</script>
