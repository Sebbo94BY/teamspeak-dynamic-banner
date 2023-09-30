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
                @else
                <div class="col-lg-12">
                    <table class="table" id="availableVariables">
                        <thead>
                        <tr>
                            <th scope="col">{{ __('views/modals/modal-variables.table_variable_name') }}</th>
                            <th scope="col">{{ __('views/modals/modal-variables.table_variable_value') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($instanceVariableModal->variables()['variables_and_values'] as $key => $value)
                            <tr>
                                <td><code>%{{ $key }}%</code></td>
                                <td>{{ $value }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('views/modals/modal-variables.dismiss_button') }}</button>
            </div>
        </div>
    </div>
</div>
