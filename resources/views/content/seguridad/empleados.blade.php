@extends('layouts/contentNavbarLayout')

@section('title', 'Empleados - Seguridad')

@section('page-script')
@if ($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modalId = 'modalCrearEmpleado';
        @if (old('eliminar_empleado_id'))
            modalId = 'modalEliminarEmpleado{{ old('eliminar_empleado_id') }}';
        @elseif (old('empleado_id'))
            modalId = 'modalEditarEmpleado{{ old('empleado_id') }}';
        @endif
        var modalEl = document.getElementById(modalId);
        if (modalEl) {
            new bootstrap.Modal(modalEl).show();
        }
    });
</script>
@endif
@endsection

@section('content')
@if (session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0">Empleados</h5>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrearEmpleado">
            <i class="icon-base bx bx-plus me-1"></i> Nuevo empleado
        </button>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Usuario</th>
                    <th>Email</th>
                    <th>Creado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($empleados as $empleado)
                <tr>
                    <td>{{ $empleado->name }}</td>
                    <td>{{ $empleado->username ?? '-' }}</td>
                    <td>{{ $empleado->email }}</td>
                    <td>{{ $empleado->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="icon-base bx bx-dots-vertical-rounded"></i></button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalVerEmpleado{{ $empleado->id }}"><i class="icon-base bx bx-show me-1"></i> Ver</a>
                                <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalEditarEmpleado{{ $empleado->id }}"><i class="icon-base bx bx-edit-alt me-1"></i> Editar</a>
                                <a class="dropdown-item text-danger" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalEliminarEmpleado{{ $empleado->id }}"><i class="icon-base bx bx-trash me-1"></i> Eliminar</a>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">No hay empleados registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Crear Empleado -->
<div class="modal fade" id="modalCrearEmpleado" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form class="modal-content" action="{{ route('seguridad-empleados.store') }}" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Nuevo empleado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <label class="form-label" for="emp-name">Nombre</label>
                    <input type="text" id="emp-name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" />
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-4">
                    <label class="form-label" for="emp-username">Usuario</label>
                    <input type="text" id="emp-username" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" />
                    @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-4">
                    <label class="form-label" for="emp-email">Email</label>
                    <input type="email" id="emp-email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" />
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-4">
                    <label class="form-label" for="emp-password">Contraseña</label>
                    <input type="password" id="emp-password" name="password" class="form-control @error('password') is-invalid @enderror" />
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-4">
                    <label class="form-label" for="emp-password-confirmation">Confirmar contraseña</label>
                    <input type="password" id="emp-password-confirmation" name="password_confirmation" class="form-control" />
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

@foreach ($empleados as $empleado)
<!-- Modal Ver Empleado -->
<div class="modal fade" id="modalVerEmpleado{{ $empleado->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle del empleado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2"><strong>Nombre:</strong> {{ $empleado->name }}</p>
                <p class="mb-2"><strong>Usuario:</strong> {{ $empleado->username ?? '-' }}</p>
                <p class="mb-2"><strong>Email:</strong> {{ $empleado->email }}</p>
                <p class="mb-0"><strong>Creado:</strong> {{ $empleado->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Empleado -->
<div class="modal fade" id="modalEditarEmpleado{{ $empleado->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form class="modal-content" action="{{ route('seguridad-empleados.update', $empleado) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="empleado_id" value="{{ $empleado->id }}" />
            <div class="modal-header">
                <h5 class="modal-title">Editar empleado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @php
                    $isThisRow = (string) old('empleado_id') === (string) $empleado->id;
                @endphp
                <div class="mb-4">
                    <label class="form-label" for="edit-name-{{ $empleado->id }}">Nombre</label>
                    <input type="text" id="edit-name-{{ $empleado->id }}" name="edit_name" class="form-control {{ $isThisRow && $errors->has('edit_name') ? 'is-invalid' : '' }}" value="{{ $isThisRow ? old('edit_name') : $empleado->name }}" />
                    @if ($isThisRow && $errors->has('edit_name'))
                        <div class="invalid-feedback d-block">{{ $errors->first('edit_name') }}</div>
                    @endif
                </div>
                <div class="mb-4">
                    <label class="form-label" for="edit-username-{{ $empleado->id }}">Usuario</label>
                    <input type="text" id="edit-username-{{ $empleado->id }}" name="edit_username" class="form-control {{ $isThisRow && $errors->has('edit_username') ? 'is-invalid' : '' }}" value="{{ $isThisRow ? old('edit_username') : $empleado->username }}" />
                    @if ($isThisRow && $errors->has('edit_username'))
                        <div class="invalid-feedback d-block">{{ $errors->first('edit_username') }}</div>
                    @endif
                </div>
                <div class="mb-4">
                    <label class="form-label" for="edit-email-{{ $empleado->id }}">Email</label>
                    <input type="email" id="edit-email-{{ $empleado->id }}" name="edit_email" class="form-control {{ $isThisRow && $errors->has('edit_email') ? 'is-invalid' : '' }}" value="{{ $isThisRow ? old('edit_email') : $empleado->email }}" />
                    @if ($isThisRow && $errors->has('edit_email'))
                        <div class="invalid-feedback d-block">{{ $errors->first('edit_email') }}</div>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Eliminar Empleado -->
<div class="modal fade" id="modalEliminarEmpleado{{ $empleado->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form class="modal-content" action="{{ route('seguridad-empleados.destroy', $empleado) }}" method="POST">
            @csrf
            @method('DELETE')
            <input type="hidden" name="eliminar_empleado_id" value="{{ $empleado->id }}" />
            <div class="modal-header">
                <h5 class="modal-title">Eliminar empleado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Seguro que deseas eliminar a <strong>{{ $empleado->name }}</strong>? Esta acción no se puede deshacer.</p>
                @php
                    $isThisDelete = (string) old('eliminar_empleado_id') === (string) $empleado->id;
                @endphp
                <div class="mb-2">
                    <label class="form-label" for="confirm-password-{{ $empleado->id }}">Ingresa tu contraseña para confirmar</label>
                    <input type="password" id="confirm-password-{{ $empleado->id }}" name="confirm_password" class="form-control {{ $isThisDelete && $errors->has('confirm_password') ? 'is-invalid' : '' }}" />
                    @if ($isThisDelete && $errors->has('confirm_password'))
                        <div class="invalid-feedback d-block">{{ $errors->first('confirm_password') }}</div>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-danger">Eliminar</button>
            </div>
        </form>
    </div>
</div>
@endforeach
@endsection
